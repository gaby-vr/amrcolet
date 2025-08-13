<?php

namespace App\Http\Controllers\Admin;

use App\Courier\CourierGateway;
use App\Models\Curier;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        $users = User::query();
        if($request->input()) {
            if($request->has('name') && $request->input('name') != "")
            {
                $users->where('name', 'like', '%'.$request->input('name').'%');
            }
            if($request->has('email') && $request->input('email') != "")
            {
                $users->where('email', 'like', '%'.$request->input('email').'%');
            }
        }
        return view('admin.users.show', [
            'users' => $users->orderBy('created_at')->paginate(15)->appends($request->query()),
            'condtitions' => $request->input(),
        ]);
    }

    public function impersonate(User $user)
    {
        Auth::loginUsingId($user->id);
        return redirect()->route('dashboard.show');
    }

    public function get() {
        $search = request()->input('search');
        return json_encode(User::select('id','name')->where('name', 'LIKE', $search.'%')->orderBy('name')->take(5)->get());
    }

    public function create()
    {
        return view('admin.users.create', [
            'curieri' => Curier::all(),
            'prices' => old('prices'),
            'frequency_dates' => old('frequency_dates'),
            'twoship_locations' => $this->fetch2shipLocations(),
        ]);
    }

    public function store()
    {
        $input = Validator::make(request()->input(), $this->rules(request()))->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'],
        ]);

        $this->addUserMetas($user, $input, false);

        return redirect()->route('admin.users.edit', $user->id)->with([
            'success' => __('User-ul a fost creat cu succes.')
        ]);
    }

    public function edit(User $user)
    {
        $prices = [];
        foreach(Curier::all() as $curier) {
            $prices[$curier->id] = $this->unpluck($user->pricesMetaWithKey('kg','price',$curier->id));
        }
        $user = $user->withBalance()->withMetas('special_')->withMetas('frequency_', keep:true)->withMetaKeys(['sheet_frequency']);
        return view('admin.users.create', [
            'user' => $user,
            'prices' => old('prices', $prices),
            'curieri' => Curier::all(),
            'curieri_speciali' => $user->couriers->pluck('id')->toArray() ?? [],
            'frequency_dates' => old('frequency_dates', json_decode($user->frequency_dates, true)),
            'twoship_locations' => $this->fetch2shipLocations(),
        ]);
    }

    public function unpluck($columns)
    {
        $result = [];
        foreach ($columns as $key => $column) {
            foreach ($column as $row => $value) {
                $result[$row][$key] = $value;
            }
        }
        return $result;
    }

    public function update(User $user)
    {
        $input = Validator::make(request()->input(), $this->rules(request(), $user->id))->validate();

        $user->update([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => $input['role'],
        ]);

        if($input['password']) {
            User::where('id', $user->id)->update([
                'password' => Hash::make($input['password']),
            ]);
        }

        $this->addUserMetas($user, $input);

        return redirect()->route('admin.users.edit', $user->id)->with([
            'success' => __('User-ul a fost modificat cu succes.')
        ]);
    }

    public function addUserMetas(User $user, $input, $edit = true)
    {
        if(!empty($input['curieri'])) {
            $user->couriers()->sync($input['curieri']);
        } else {
            $user->couriers()->sync([]);
        }
        $edit ? $user->unsetMetas('special_') : null;
        foreach($input['special'] ?? [] as $name => $value) {
            if($value != null && $value >= 0) {
                $user->setMeta('special_'.$name, $value);
            }
        }

        $edit ? $user->unsetMetas('frequency_') : null;
        foreach($input['frequency'] ?? [] as $name => $value) {
            if($value != null && $value >= 0) {
                $user->setMeta('frequency_'.$name, is_array($value) 
                    ? json_encode(call_user_func(function(array $a){sort($a);return $a;}, $value)) 
                    : $value
                );
            }
        }

        $edit ? $user->unsetMetas('%_kg_') : null;
        if (isset($input['prices'])) {
            foreach($input['prices'] as $curier_id => $value) {
                $newPrices = [];
                foreach($value['kg'] as $index => $values) {
                    $newPrices[$values] = $value['price'][$index];
                }

                if(count($newPrices) > 0) {
                    $user->setMetas($newPrices, null, '_kg_'.$curier_id);
                }
            }
        }

        $input['sheet_frequency'] 
            ? $user->setMeta('sheet_frequency', $input['sheet_frequency'])
            : ($edit ? $user->unsetMeta('sheet_frequency') : null);

        $input['account_balance'] 
            ? $user->setMeta('account_balance', $input['account_balance'])
            : ($edit ? $user->unsetMeta('account_balance') : null);

        if(isset($input['bonus_credits'])) {
            $bonus_credits = $edit ? $user->meta('bonus_credits') : 0;
            $bonus_credits = $bonus_credits == '' ? 0 : $bonus_credits;
            $user->setMeta('bonus_credits', $bonus_credits + $input['bonus_credits']);
            $user->setMeta('account_balance', ($input['account_balance'] ?? 0) + $input['bonus_credits']);
        }

        if($user->role == '2') {
            $user->setMeta('days_of_negative_balance', $input['days_of_negative_balance']);
            if(isset($input['expiration_date'])) {
                $user->setMeta('expiration_date', $input['expiration_date']);
            }
        } elseif($edit) {
            $result = $user->unsetMetaKeys(['days_of_negative_balance','expiration_date']);
            // dd($user->getMetas());
        }

        if(!$edit) {
            $user->setMetas([
                'invoice_active' => 1,
                'invoice_email' => $input['email'],
                'alerts_active' => 1,
                'alerts_email' => $input['email'],
                'ramburs_active' => 1,
                'ramburs_email' => $input['email'],
            ], 'notifications_');
        }
    }

    public function destroy(User $user)
    {
        UserMeta::where('user_id', $user->id)->delete();
        User::where('id', $user->id)->delete();

        session()->flash('success', 'User-ul a fost sters cu succes.');

        return redirect()->route('admin.view');
    }

    public function invoice(User $user)
    {
        return view('admin.users.invoice-info', [
            'user' => $user,
            'client' => $user->invoiceInfo(),
        ]);
    }

    public function rules($request, $id = null)
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'],
            'role' => ['required', 'integer', 'in:1,2'],
            'curieri' => ['nullable', 'array', 'min:0'],
            'curieri.*' => ['nullable', 'integer', Rule::exists(Curier::class, 'id')],
            'special' => ['nullable', 'array', 'min:0'],
            'special.*' => ['nullable', 'numeric', 'min:0'],
            'sheet_frequency' => ['nullable', 'integer', 'in:1,2'],
            'frequency' => ['nullable', 'array', 'min:0'],
            'frequency.type' => ['nullable', 'integer', 'in:1,2'],
            'frequency.recurrence' => ['nullable', 'required_if:frequency.type,1', 'exclude_unless:frequency.type,1', 'integer', 'min:1', 'max:366'],
            'frequency.time' => ['nullable', 'required_if:frequency.type,1', 'exclude_unless:frequency.type,1', 'string', 'max:255', 'in:days,months,years'],
            'frequency.dates' => ['nullable', 'required_if:frequency.type,2', 'exclude_unless:frequency.type,2', 'array'],
            'frequency.dates.*' => ['nullable', 'required_if:frequency.type,2', 'exclude_unless:frequency.type,2', 'integer', 'min:1', 'max:31'],
            'prices' => ['nullable', 'array', 'min:1'],
            'prices.*.kg' => ['nullable', 'array', 'min:1'],
            'prices.*.price' => ['nullable', 'array', 'min:1', function ($attribute, $value, $fail) use ($request){
                if (count($value) != count($request->input(str_replace('.price', '.kg', $attribute)))) {
                    $fail(__('The :attribute is invalid.'));
                }
            },],
            // 'special_percent_price' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'account_balance' => ['required', 'numeric', ($request->input('role') == '1' ? 'min:0' : '')],
            'bonus_credits' => ['nullable', 'integer', 'min:0'],
            'days_of_negative_balance' => ['nullable', 'required_if:role,2', 'exclude_unless:role,2', 'integer', 'min:1', 'max:255'],
            'expiration_date' => ['nullable', 'date', 'exclude_unless:role,2'],
        ];
    }

    private function fetch2shipLocations()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'User-WS-Key ' . env('2SHIP_API_KEY'),
                'Content-Type' => 'application/json',
            ])->get(env('2SHIP_API_URL') . '/GetAllLocations');

            if ($response->successful()) {
                return $response->json()['Locations'];
            }
        } catch (\Exception $e) {
            \Log::error('2Ship API Error: ' . $e->getMessage());
        }

        return [];
    }
}
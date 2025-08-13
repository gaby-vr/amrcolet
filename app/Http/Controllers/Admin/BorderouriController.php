<?php

namespace App\Http\Controllers\Admin;

use App\Billing\PaymentGateway;
use App\Exports\ExportBorderouri;
use App\Models\Borderou;
use App\Models\BorderouAwb;
use App\Models\BorderouApiRequest;
use App\Models\Livrare;
use App\Models\Setting;
use App\Models\User;
use App\Traits\BorderouCreationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Validator;

class BorderouriController extends Controller
{
    use BorderouCreationTrait;

    public function index(Request $request)
    {
        $paymentGateway = app(PaymentGateway::class, ['type' => 2]);
        return view('admin.borderouri.show', [
            'items' => $this->search($request),
            'condtitions' => $request->input(),
            'cert_date' => date('d.m.Y', $paymentGateway->getSSLCertificateValidToTime() ?? null),
            'cert_thumbprint' => $paymentGateway->getSSLCertificateDataThumbprint(),
        ]);
    }

    public function search(Request $request)
    {
        $table = Borderou::getTableName();
        $items = Borderou::with(['user']);
        if($request->input()) {
            if($request->input('from') != "") {
                $items->whereDate($table.'.start_date', '>=', $request->input('from'));
            }
            if($request->input('to') != "") {
                $items->whereDate($table.'.end_date', '<=', $request->input('to'));
            }
            if($request->input('status') != "") {
                $func = $request->input('status') == '1' ? 'whereNotNull' : 'whereNull';
                $items->{$func}($table.'.payed_at');
            }
            if($request->has('user_name') && $request->input('user_name') != "") {
                $items->where(function($query) use($request) {
                    $query->whereHas('user', function($subquery) use($request) {
                        $subquery->where('name', 'like', $request->input('user_name').'%');
                    });
                });
            }
            if($request->has('user_email') && $request->input('user_email') != "") {
                $items->where(function($query) use($request) {
                    $query->whereHas('user', function($subquery) use($request) {
                        $subquery->where('email', 'like', $request->input('user_email').'%');
                    });
                });
            }
        }
        return $items->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());
    }

    public function get(Request $request, User $user = null)
    {
        $search = request()->input('search');
        return json_encode($user 
            ? $user->orders()->select('api_shipment_awb as text')->where('api_shipment_awb', 'LIKE', $search.'%')
                ->whereNotIn('api_shipment_awb', $user->borderouAwbs()->select('awb')
                    ->whereDate(BorderouAwb::getTableName().'.created_at', '>=', now()->subMonths(2))
                )->orderBy('api_shipment_awb')->take(5)->get()
            : []
        );
    }

    public function create()
    {
        return view('admin.borderouri.create', [
            'user' => User::find(old('user_id')),
            'livrari' => old('livrari'),
        ]);
    }

    public function edit(Borderou $borderou)
    {
        $user = User::find(old('user_id', $borderou->user_id));
        return view('admin.borderouri.create', [
            'item' => $borderou,
            'user' => $user,
            'livrari' => old('livrari', !empty($borderou->borderouAwbs->toArray()) ? $borderou->borderouAwbs->toArray() : ['']),
            'livrari_valabile' => $user->orders()->with(['sender','receiver'])->where('ramburs', '>', '1')->where('status', '1')
                ->whereNotIn('api_shipment_awb', $user->borderouAwbs()->select('awb')
                    ->whereDate(BorderouAwb::getTableName().'.created_at', '>=', now()->subMonths(2))
                )->whereDate('created_at', '>=', now()->subMonths(2))->orderBy('api_shipment_awb')->take(25)->get(),
        ]);
    }

    public function save(Request $request, Borderou $borderou = null)
    {
        $input = $request->merge([
            'livrari' => flip_array_keys($request->input('livrari') ?? ['livrari' => null])
        ])->validate($this->rules($borderou->id ?? null), [], $this->names());

        $new = false;
        if($borderou === null) {
            $new = true;
            $borderou = new Borderou;
        }

        $input['total'] = collect($input['livrari'] ?? [])->sum('payment') ?? 0;
        if($borderou && $borderou->payed_at === null && $input['payed_at'] !== null) {
            $payed = true;
        }
        $borderou->fill($input)->save();
        $borderou->borderouAwbs()->delete();
        if(isset($input['livrari'])) {
            $borderou->borderouAwbs()->createMany($input['livrari']);
        }
        if(isset($payed)) {
            $borderou->repayments()->update([
                'status' => 1,
                'type' => 2,
                'payed_on' => $input['payed_at'],
            ]);
            if($borderou->user && $borderou->user->meta('notifications_ramburs_active')) {
                try {
                    $email = $borderou->user->meta('notifications_ramburs_email') ?? $borderou->user->email;
                    Mail::to($email)->send(new \App\Mail\SendBorderouPayedNotification(['borderou' => $borderou]));
                } catch(\Exception $e) { \Log::info($e); }
            }
        }

        return redirect()->route('admin.borderouri.edit', $borderou)->with([
            'success' => $new 
                ? __('Borderoul a fost creat cu succes.')
                : __('Borderoul a fost modificata cu succes.'), 
        ]);
    }

    public function updateBorderouManual(Borderou $borderou)
    {
        return $this->updateBorderou($borderou, null, false);
    }

    public function sendApiRequestsBorderouManual(Borderou $borderou)
    {
        $this->sendApiRequestsBorderou($borderou);
        return back()->with([
            'success' => __('Plata borderourilor a fost trimisa catre Libra Bank.') 
        ]);
    }

    public function export(Request $request, Borderou $borderou)
    {
        try {
            return Excel::download(new ExportBorderouri($borderou->borderouAwbs->toArray()), config('app.name').'_borderou_'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage() ?? __('A avut loc o eroare, va rog incercati mai tarziu.')]);
        }
    }

    public function destroy(Request $request, Borderou $borderou)
    {
        $borderou->delete();
        return back()->with([
            'success' => __('Borderoul a fost sters cu succes.') 
        ]);
    }

    public function rules($id = null)
    {
        $rulesAwbs = BorderouAwb::rules(false, $id);
        foreach($rulesAwbs as $column => $rules) {
            $rulesAwbs[$column][0] = 'sometimes';
        }
        return Borderou::rules() + \Arr::prependKeysWith($rulesAwbs, 'livrari.*.')/* + [
            'livrari.*' => ['sometimes', 'required_array_keys:'.implode(',', array_keys($rulesAwbs))]
        ]*/;
    }

    public function names()
    {
        return Borderou::names() + BorderouAwb::names() + \Arr::prependKeysWith(BorderouAwb::names(), 'livrari.*.') + [
            'livrari.*' => __('Livrare')
        ];
    }
}
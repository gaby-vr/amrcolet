<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Address;
use App\Traits\OrderValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    use OrderValidationTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'addresses',
            'subsection' => null,
            'title' => __('Adrese')
        ]);
    }

    public function get(Request $request, Address $address)
    {
        if($address->user_id == auth()->id()) {
            return response()->json(['items' => $address->toArray(), 'status' => 200]);
        } else {
            return response()->json(['status' => 422]);
        }
    }

    public function update(Request $request, Address $address = null)
    {
        if($address != null && $address->user_id != auth()->id()) {
            return redirect()->back();
        }

        if($address != null) {
            session()->flash('edit', $address->id);
        }
        session()->flash('validated', true);

        $request = $this->trimPhoneNumberSpaces($request);
        $rules = $this->replaceFullPhoneNumberRule($this->addressRules('sender', false));
        $attributes = $request->validate([
            'favorite' => ['nullable', 'string', Rule::in(['1'])],
            'address_name' => ['required', 'string', 'min:2', 'max:255'],
        ] + $rules, [], [
            'favorite' => __('favorit'),
            'address_name' => __('nume adresa'),
        ] + $this->addressNames());

        session()->pull('edit');
        session()->pull('validated');

        $attributes['favorite'] = $attributes['favorite'] ?? 0;

        if($address == null) {
            $attributes['user_id'] = auth()->id();
            Address::create($attributes);
        } else {
            Address::where('id', $address->id)->update($attributes);
        }

        session()->flash('success', __('Informatiile au fost salvate'));

        return redirect()->back();
    }

    public function delete(Request $request, Address $address)
    {
        Address::where('id', $address->id)->delete();
        session()->flash('success', __('Adresa a fost stearsa'));
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Traits\OrderValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    use OrderValidationTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'invoice',
            'subsection' => null,
            'title' => __('Date de facturare')
        ]);
    }

    public function update(Request $request)
    {
        $request = $this->trimPhoneNumberSpaces($request);
        $rules = $this->replaceFullPhoneNumberRule($this->addressRules('invoice', false));
        $attributes = $request->validate($rules, [], $this->addressNames());

        $user = auth()->user();

        // Add user info to invoice
        $metas = [];
        foreach($attributes as $key => $value) {
            $value != null ? ($metas[$key] = $value) : null;
        }

        // add user invoice info
        $user->unsetMetas('invoice_');
        $metas = [
            'type' => isset($attributes['is_company']) ? 2 : 1,
        ] + $metas;

        $user->setMetas($metas, 'invoice_');

        return redirect()->back()->with([
            'success' => __('Informatiile au fost salvate')
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Traits\OrderValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Mail;


class HomeController extends Controller
{
    use OrderValidationTrait;

    public function index(Request $request)
    {
        return view('page', ['page' => Page::firstWhere('slug', 'home')]);
    }

    public function page(Request $request, Page $page)
    {
        if($page) {
            return view('page', ['page' => $page]);
        }
        return abort(404);
    }

    public function getCountriesIso(Request $request)
    {
        return Country::pluck('iso');
    }

    public function showPackagingPolicy(Request $request)
    {
        return view('packaging');
    }

    public function showCookiesPolicy(Request $request)
    {
        return view('cookies');
    }

    public function showPostalPolicy(Request $request)
    {
        return view('postal');
    }

    public function showContact(Request $request)
    {
        return view('contact', [
            'settings' => Setting::select('name','value')->where('name', 'like', 'PROVIDER_%')->get()->mapWithKeys(function ($item) {
                return [$item['name'] => $item['value']];
            })->toArray(),
        ]);
    }

    public function sendMail(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'message' => ['required', 'string'],
        ])->validate();

        Mail::send('mail.email', [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'user_message' => $request->get('message')
        ], function($message) {
           $message->from(request()->get('email'), request()->get('name'));
           $message->to('office@amrcolet.ro', 'Admin')->subject('AMR Colet Contacteaza-ne');
        });
        return back()->with('success', 'Emailul a fost trimis.<br>Multumim pentru feedback!');
    }

    public function sendContractMail(Request $request)
    {
        $request = $this->trimPhoneNumberSpaces($request);
        $rules = $this->addressRules('sender', false);

        $attr = Validator::make($request->input(), [
            'name' => $rules['name'],
            'phone' => $rules['phone_full'],
            'email' => $rules['email'],
            'company_address' => ['required', 'string', 'max:1024'],
            'nr_colete' => ['required', 'integer','min:1','max:999999'],
            'terms' => ['required', 'accepted'],
        ] + [
        ],[],$this->addressNames() + [
            'company_address' => __('adresa companie'),
            'nr_colete' => __('expedieri lunare'),
            'terms' => __('termeni'),
        ])->validate();

        Mail::send('mail.email', [
            'name' => $attr['name'],
            'email' => $attr['email'],
            'phone' => $attr['phone'],
            'company_address' => $attr['company_address'],
            'nr_colete' => $attr['nr_colete'],
        ], function($message) use($attr) {
           $message->from($attr['email'], $attr['name']);
           $message->to('office@amrcolet.ro', 'Admin')->subject('Oferta expediere colete');
        });
        return back()->with('success', 'Emailul a fost trimis.<br>Multumim pentru interesul acordat!');
    }
}

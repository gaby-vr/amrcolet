<?php

namespace App\Http\Controllers\Dashboard;

use App\Traits\OrderValidationTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class SettingsController extends Controller
{
    use OrderValidationTrait;

    public function index(Request $request)
    {
        $sections = explode(".", Route::currentRouteName());
        $section = $sections[1];
        $subsection = $sections[count($sections) - 2];
        switch ($subsection) {
            case 'repayment':
                $title = __('Setari').' <small>></small> '.__('Rambursare');
                break;
            case 'security':
                $title = __('Setari').' <small>></small> '.__('Securitate');
                break;
            case 'notifications':
                $title = __('Setari').' <small>></small> '.__('Notificari');
                break;
            case 'print':
                $title = __('Setari').' <small>></small> '.__('Printare');
                break;
            case 'schedule':
                $title = __('Setari').' <small>></small> '.__('Program');
                break;
        }

        return view('profile.dashboard', [
            'section' => $sections[1],
            'subsection' => $subsection,
            'title' => $title
        ]);
    }

    public function updateRepaymentsSettings(Request $request)
    {
        $attributes = $request->validate([
            'iban' => ['required', 'string', 'min:24', 'max:24', 
                function ($attribute, $value, $fail) {
                    if (!preg_match('/RO[0-9]{2}('.implode('|', $this->bankCodes()).')[A-Z0-9]{16}/', $value)) {
                        if(auth()->id() == 1) {
                            dd($value);
                        }
                        $fail(__('Este necesar un cont al unei banci romanesti.'));
                    }
                },
            ],
            'card_owner_name' => ['required', 'string', 'min:3', 'max:32'],
            // 'time' => ['required', 'integer', 'min:1', 'max:5'],
            // 'sum' => ['nullable', 'required_unless:time,1', 'integer', Rule::in(['200','500','1000','2000','5000','10000','-1'])],
            // 'one_day' => ['nullable', 'integer', 'min:1', 'max:1'],
        ],[],[
            'iban' => __('IBAN'),
            'card_owner_name' => __('nume titular cont bancar'),
            'time' => __('interval de timp'),
            'sum' => __('suma minima'),
        ]);

        $this->setUsingMetas($attributes, 'repayment_', true);

        return redirect()->back()->with([
            'success' => __('Informatiile au fost salvate')
        ]);
    }

    public function updateSecuritySettings(Request $request)
    {
        $attributes = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', new Password, 'confirmed'],
        ],[],[
            'current_password' => __('parola curenta'),
            'password' => __('parola noua'),
        ]);

        auth()->user()->forceFill([
            'password' => Hash::make($attributes['password']),
        ])->save();

        return redirect()->back()->with([
            'success' => __('Parola a fost schimbata')
        ]);
    }

    public function updateNotificationsSettings(Request $request)
    {
        $rules = $names = [];
        foreach ([
            'invoice' => 'notificare factura', 
            'awb' => 'fisier awb livrare',
            'alerts' => 'alerta livrare',
            'ramburs' => 'notificare ramburs'
        ] as $value => $name) {
            $key = $value.'_active';

            $rules = $rules + [
                $key => ['nullable', 'integer', 'min:1', 'max:1'],
            ] + ($value != 'awb' ? [
                $value.'_email' => ['nullable','required_if:'.$key.',1', 'exclude_unless:'.$key.',1', 'email', 'min:4', 'max:255']
            ] : []);

            $names = $names + [
                $key => __($name),
                $value.'_email' => __('email '.$name)
            ];
        }

        $attributes = $request->validate($rules,[],$names);

        $this->setUsingMetas($attributes, 'notifications_', true);

        return redirect()->back()->with([
            'success' => __('Informatiile au fost salvate')
        ]);
    }

    public function updatePrintSettings(Request $request)
    {
        $attributes = $request->validate([
            'paper_size' => ['required', Rule::in(['A4','A6'])],
        ],[],[
            'paper_size' => __('marime pagina'),
        ]);

        $this->setUsingMetas($attributes, 'print_', true);

        return redirect()->back()->with([
            'success' => __('Informatiile au fost salvate')
        ]);
    }

    public function updateScheduleSettings(Request $request)
    {
        $attributes = $request->validate([
            'start_pickup_hour' => ['required', 'integer', 'min:8', 'max:15'],
            'end_pickup_hour' => ['required', 'integer', 'min:9', 'max:18'],
        ],[],[
            'start_pickup_hour' => __('ora de inceput'),
            'end_pickup_hour' => __('ora de sfarsit'),
        ]);

        foreach ($attributes as $key => $value) {
            $metas[$key] = $value;
        }
        // add user settings
        auth()->user()->setMetas($metas, 'schedule_');

        return redirect()->back()->with([
            'success' => __('Informatiile au fost salvate')
        ]);
    }

    protected function setUsingMetas($attributes, $prefix = '', $unset = false)
    {
        $user = auth()->user();

        if($unset) {
            // unset old meta settings
            $user->unsetMetas($prefix);
        }
        // set new meta settings
        $metas = [];
        foreach ($attributes as $key => $value) {
            $metas[$key] = $value;
        }
        if($metas) {
            $user->setMetas($metas, $prefix);
        }
    }

    // protected function bank_codes()
    // {
    //     return[
    //         'ABNA', 'ARBL', 
    //         'BCUN', 'BCYP', 'BITR', 'BLOM', 'BPOS', 'BRDE', 'BREL', 'BRMA', 'BSEA', 'BTRL', 'BUCU', 'BCRL', 'BACX',
    //         'CAIX', 'CARP', 'CECE', 'CITI', 'CRCO', 'CRDZ', 
    //         'DABA', 'DAFB', 'DARO', 'DPFA', 
    //         'EGNA', 'ETHN', 'EXIM', 
    //         'FNNB', 'FRBU', 'FTSB', 
    //         'HVBL', 
    //         'INGB', 
    //         'MILB', 'MIND', 'MIRO', 
    //         'NBOR', 
    //         'OTPV', 
    //         'PIRB', 'PORL', 
    //         'RNCB', 'ROIN', 'RZBL', 'RZBR', 
    //         'TRFD', 
    //         'UGBI', 
    //         'VBBU', 
    //         'WBAN', 
    //         'TREZ'
    //     ];
    // }
}

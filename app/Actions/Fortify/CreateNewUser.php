<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:strict,dns', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'g-recaptcha-response' => ['required', 'recaptchav3:register,0.6'],
        ],[
            'g-recaptcha-response.*' => __('Validarea captcha nu a reusit. Incercati mai tarziu.')
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->setMeta('notifications_invoice_active', 1);
        $user->setMeta('notifications_invoice_email', $input['email']);
        $user->setMeta('notifications_alerts_active', 1);
        $user->setMeta('notifications_alerts_email', $input['email']);
        $user->setMeta('notifications_ramburs_active', 1);
        $user->setMeta('notifications_ramburs_email', $input['email']);

        return $user;
    }
}

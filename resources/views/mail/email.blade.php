{{ __('Ai primit un email de la') }}: {{ $email }}
<p>
Email: {{ $email }}
</p>
<p>
Nume: {{ $name }}
</p>
@isset($user_message)
<p>
Message:<br>
{{ $user_message }}
</p>
@endisset
@isset($phone)
<p>
Numar telefon:<br>
{{ $phone }}
</p>
@endisset
@isset($company_address)
<p>
Adresa companie:<br>
{{ $company_address }}
</p>
@endisset
@isset($nr_colete)
<p>
Numar colete trimise lunar:<br>
{{ $nr_colete }}
</p>
@endisset
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('js/vendors/mat/select2/select2-tailwind.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/admin/plugins/flatpickr/flatpickr.min.css') }}"> --}}
<link rel="stylesheet" type="text/css" href="{{ asset('css/admin/invoices.css') }}">
@endpush

@push("scripts")
<script src="{{ asset('js/admin/plugins/jquery/jquery.min.js') }}"></script>
{{-- <script src="{{ asset('js/vendors/mat/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.min.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.ro.js') }}"></script>
<script src="{{ asset('js/admin/plugins/jquery.ui.autocomplete/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/admin/invoices.js') }}"></script> --}}
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Istoric plati') }}</x-slot>
        <x-slot name="href">{{ route('admin.invoices.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza factura') }}</x-slot>
        <x-jet-authentication-card class="px-2 form-admin" classForm="md:max-w-xl lg:max-w-4xl">
            <x-slot name="logo"></x-slot>

            @if(session()->has('success'))
                <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3" role="alert">
                    <p>{{ session()->get('success') }}</p>
                </div>
            @endif
            <x-jet-validation-errors class="mb-4" />
                
                <h4 class="text-2xl">{{ __('Date de facturare') }}</h4>
                <div class="grid grid-cols-3 gap-2 my-4">
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="first_name">{{ __('Prenume') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="first_name" class="block mt-1 w-full" type="text" value="{{ old('first_name', $client['first_name'] ?? '') }}" name="first_name" disabled />
                    </div>
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="last_name">{{ __('Nume') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="last_name" class="block mt-1 w-full" type="text" value="{{ old('last_name', $client['last_name'] ?? '') }}" name="last_name" disabled />
                    </div>
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="email">{{ __('Email') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="email" class="block mt-1 w-full" type="text" value="{{ old('email', $client['email'] ?? $user['email']) }}" name="email" disabled />
                    </div>
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="phone">{{ __('Numar de telefon') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="phone" class="block mt-1 w-full" type="text" value="{{ old('phone', $client['phone'] ?? '') }}" name="phone" disabled />
                    </div>
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="phone">{{ __('Numar de telefon 2') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="phone" class="block mt-1 w-full" type="text" value="{{ old('phone', $client['phone_2'] ?? '') }}" name="phone" disabled />
                    </div>
                    <div class="mt-4 col-span-3">
                        <x-jet-label for="is_company">
                            <div class="flex items-center">
                                <input id="is_company" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="checkbox" {{ old('is_company') == '1' || isset($client['is_company']) && $client['is_company'] ? 'checked' : '' }} value="1" name="is_company" disabled>
                                <div class="ml-2 text-lg">{{ __('Persoana juridica') }}</div>
                            </div>
                        </x-jet-label>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-2 mt-4 show-juridic">
                    <h5 class="text-lg col-span-12">{{ __('Date companie') }}</h5>
                    <div class="col-span-12 md:col-span-6 lg:col-span-2">
                        <x-jet-label for="company_type">{{ __('Tip') }} <span class="text-red-500">*</span></x-jet-label>
                        <select name="company_type" id="company_type"  class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full">
                            <option value="1" {{ old('company_type', $client['company_type'] ?? '') == "1" ? 'selected' : '' }}>{{ __('CUI') }}</option>
                            <option value="2" {{ old('company_type', $client['company_type'] ?? '') == "2" ? 'selected' : '' }}>{{ __('NIF') }}</option>
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <x-jet-label for="cui_nif"><span class="show-cui">{{ __('CUI') }}</span><span class="show-nif">{{ __('NIF') }}</span> <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="cui_nif" class="block mt-1 w-full" type="text" value="{{ old('cui_nif', $client['cui_nif'] ?? '') }}" name="cui_nif" disabled />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <x-jet-label for="nr_reg_com">{{ __('Nr. reg. com.') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="nr_reg_com" class="block mt-1 w-full" type="text" value="{{ old('nr_reg_com', $client['nr_reg'] ?? '') }}" name="nr_reg_com" disabled />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4">
                        <x-jet-label for="company_name">{{ __('Nume firma') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="company_name" class="block mt-1 w-full" type="text" value="{{ old('company_name', $client['company_name'] ?? '') }}" name="company_name" disabled />
                    </div>
                </div>
                <h5 class="text-lg mb-2 mt-4">{{ __('Adresa de facturare') }}</h5>
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12">
                        <x-jet-label for="country">{{ __('Tara') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="country" class="block mt-1 w-full" type="text" value="{{ old('country', $client['country'] ?? '') }}" name="country" disabled />
                        <input id="country_code" type="hidden" value="{{ old('country_code', $client['country_code'] ?? '') }}" name="country_code">
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-2">
                        <x-jet-label for="postcode">{{ __('Cod postal') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="postcode" class="block mt-1 w-full" type="text" value="{{ old('postcode', $client['postcode'] ?? '') }}" name="postcode" data-postcode="1" disabled />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4 ui-front">
                        <x-jet-label for="locality">{{ __('Localitate') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="locality" class="block mt-1 w-full" type="text" value="{{ old('locality', $client['locality'] ?? '') }}" name="locality" data-url="{{ route('dashboard.get.county') }}" disabled />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-2">
                        <x-jet-label for="county">{{ __('Judet/Sector') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="county" class="block mt-1 w-full" type="text" value="{{ old('county', $client['county'] ?? '') }}" readonly name="county" disabled />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4 ui-front">
                        <x-jet-label for="street">{{ __('Strada') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="street" class="block mt-1 w-full" type="text" value="{{ old('street', $client['street'] ?? '') }}" name="street" data-url="{{ route('dashboard.get.street') }}" disabled />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="street_nr">{{ __('Nr.') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="street_nr" class="block mt-1 w-full" type="text" value="{{ old('street_nr', $client['street_nr'] ?? '') }}" name="street_nr" disabled />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="bl_code">{{ __('Bl.') }}</x-jet-label>
                        <x-jet-input id="bl_code" class="block mt-1 w-full" type="text" value="{{ old('bl_code', $client['bl_code'] ?? '') }}" name="bl_code" disabled />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="bl_letter">{{ __('Sc.') }}</x-jet-label>
                        <x-jet-input id="bl_letter" class="block mt-1 w-full" type="text" value="{{ old('bl_letter', $client['bl_letter'] ?? '') }}" name="bl_letter" disabled />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="intercom">{{ __('Interfon') }}</x-jet-label>
                        <x-jet-input id="intercom" class="block mt-1 w-full" type="text" value="{{ old('intercom', $client['intercom'] ?? '') }}" name="intercom" disabled />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="floor">{{ __('Et.') }}</x-jet-label>
                        <x-jet-input id="floor" class="block mt-1 w-full" type="text" value="{{ old('floor', $client['floor'] ?? '') }}" name="floor" disabled />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="apartment">{{ __('Ap.') }}</x-jet-label>
                        <x-jet-input id="apartment" class="block mt-1 w-full" type="text" value="{{ old('apartment', $client['apartment'] ?? '') }}" name="apartment" disabled />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4">
                        <x-jet-label for="landmark">{{ __('Reper') }}</x-jet-label>
                        <x-jet-input id="landmark" class="block mt-1 w-full" type="text" value="{{ old('landmark', $client['landmark'] ?? '') }}" name="landmark" disabled />
                    </div>
                    <div class="col-span-12">
                        <x-jet-label for="more_information">{{ __('Alte informatii') }}</x-jet-label>
                        <x-jet-input id="more_information" class="block mt-1 w-full" type="text" value="{{ old('more_information', $client['more_information'] ?? '') }}" name="more_information" disabled />
                    </div>
                </div>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
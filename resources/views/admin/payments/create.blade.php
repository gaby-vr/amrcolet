@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/vendors/mat/select2/select2-tailwind.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/admin/plugins/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/admin/invoices.css') }}">
@endpush

@push("scripts")
<script src="{{ asset('js/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.min.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.ro.js') }}"></script>
<script src="{{ asset('js/admin/plugins/jquery.ui.autocomplete/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/admin/pages/invoices.js') }}?v=25082023"></script>
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

            <form method="POST" action="{{ isset($invoice) ? route('admin.invoices.update', $invoice->id) : route('admin.invoices.store') }}" enctype="multipart/form-data">
                @csrf

                <h4 class="text-2xl">{{ __('Date factura') }}</h4>
                <div class="grid grid-cols-6 gap-2 my-4">
                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                        <x-jet-label for="status">{{ __('Status') }} <span class="text-red-500">*</span></x-jet-label>
                        <select name="status" id="status"  class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full">
                            <option value="1" {{ old('status', $invoice->status ?? '') == 1 ? 'selected' : '' }}>{{ $status_list[1] }}</option>
                            <option value="2" {{ old('status', $invoice->status ?? '') == 2 ? 'selected' : '' }}>{{ $status_list[2] }}</option>
                            <option value="3" {{ old('status', $invoice->status ?? '') == 3 ? 'selected' : '' }}>{{ $status_list[3] }}</option>
                        </select>
                    </div>
                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                        <x-jet-label for="payed_on">{{ __('Data emiterii') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="payed_on" class="block mt-1 w-full datepicker" data-default="{{ old('payed_on', $invoice->payed_on ?? now()->format('Y-m-d')) }}" type="text" value="{{ old('payed_on', $invoice->payed_on ?? now()->format('Y-m-d')) }}" name="payed_on" />
                    </div>
                    @if(!isset($invoice) || $invoice->meta('created_by_admin') == '1')
                    <div class="col-span-6 lg:col-span-2 select2-container">
                        <x-jet-label for="user_id">{{ __('Utilizator') }} <span class="text-red-500">*</span></x-jet-label>
                        <select name="user_id" id="user_id"  class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full select2-ajax" data-url="{{ route('admin.users.get') }}" data-search-url="{{ route('admin.invoices.get.invoice') }}" data-default="{{ $old_user ? $old_user->id : ($invoice->user_id ?? '') }}">
                            @if(isset($old_user) && $old_user->id)
                                <option value="{{ $old_user->id }}">{{ $old_user->name }}</option>
                            @elseif(isset($invoice) && $invoice->user_id)
                                <option value="{{ $invoice->user_id }}">{{ $invoice->user->name }}</option>
                            @endif
                        </select>
                    </div>
                    @endif
                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                        <x-jet-label for="provider_tva">{{ __('TVA') }} <span class="text-red-500">*</span></x-jet-label>
                        <div class="relative flex w-full flex-wrap items-stretch">
                            <x-jet-input id="provider_tva" class="block mt-1 w-full pr-9" type="number" step="0.1" placeholder="19" value="{{ old('provider_tva', $provider['tva'] ?? '') }}" name="provider_tva" />
                            <span class="z-5 h-full leading-snug font-normal absolute text-center text-gray-500 absolute bg-transparent rounded text-base items-center justify-center w-8 right-0 pr-3 py-4">
                                %
                            </span>
                        </div>
                    </div>
                </div>
                
                <h4 class="text-2xl">{{ __('Date de contact') }}</h4>
                <div class="grid grid-cols-3 gap-2 my-4 contact-info">
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="first_name">{{ __('Prenume') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="first_name" class="block mt-1 w-full" type="text" value="{{ old('first_name', $client['first_name'] ?? '') }}" name="first_name" />
                    </div>
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="last_name">{{ __('Nume') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="last_name" class="block mt-1 w-full" type="text" value="{{ old('last_name', $client['last_name'] ?? '') }}" name="last_name" />
                    </div>
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="email">{{ __('Email') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="email" class="block mt-1 w-full" type="text" value="{{ old('email', $client['email'] ?? '') }}" name="email" />
                    </div>
                    <div class="col-span-3 lg:col-span-1">
                        <x-jet-label for="phone">{{ __('Numar de telefon') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="phone" class="block mt-1 w-full" type="text" value="{{ old('phone', $client['phone'] ?? '') }}" name="phone" />
                    </div>
                    <div class="mt-4 col-span-3">
                        <x-jet-label for="is_company">
                            <div class="flex items-center">
                                <input id="is_company" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="checkbox" {{ old('is_company') == '1' || isset($client['type']) && $client['type'] == '2' ? 'checked' : '' }} value="1" name="is_company">
                                <div class="ml-2 text-lg">{{ __('Persoana juridica') }}</div>
                            </div>
                        </x-jet-label>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-2 mt-4 show-juridic company-info">
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
                        <x-jet-input id="cui_nif" class="block mt-1 w-full" type="text" value="{{ old('cui_nif', $client['cui_nif'] ?? '') }}" name="cui_nif" />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-3">
                        <x-jet-label for="nr_reg_com">{{ __('Nr. reg. com.') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="nr_reg_com" class="block mt-1 w-full" type="text" value="{{ old('nr_reg_com', $client['nr_reg_com'] ?? $client['nr_reg'] ?? '') }}" name="nr_reg_com" />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4">
                        <x-jet-label for="company_name">{{ __('Nume firma') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="company_name" class="block mt-1 w-full" type="text" value="{{ old('company_name', $client['nume_firma'] ?? '') }}" name="company_name" />
                    </div>
                </div>
                <h5 class="text-lg mb-2 mt-4">{{ __('Adresa de facturare') }}</h5>
                <div class="grid grid-cols-12 gap-2 address-info">
                    <div class="col-span-12">
                        <x-jet-label for="country">{{ __('Tara') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="country" class="block mt-1 w-full" type="text" value="{{ old('country', $client['country'] ?? '') }}" name="country" />
                        <input id="country_code" type="hidden" value="{{ old('country_code', $client['country_code'] ?? '') }}" name="country_code">
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-2">
                        <x-jet-label for="postcode">{{ __('Cod postal') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="postcode" class="block mt-1 w-full" type="text" value="{{ old('postcode', $client['postcode'] ?? '') }}" name="postcode" data-postcode="1" />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4 ui-front">
                        <x-jet-label for="locality">{{ __('Localitate') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="locality" class="block mt-1 w-full" type="text" value="{{ old('locality', $client['locality'] ?? '') }}" name="locality" data-url="{{ route('dashboard.get.county') }}" />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-2">
                        <x-jet-label for="county">{{ __('Judet/Sector') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="county" class="block mt-1 w-full" type="text" value="{{ old('county', $client['county'] ?? '') }}" readonly name="county" />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4 ui-front">
                        <x-jet-label for="street">{{ __('Strada') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="street" class="block mt-1 w-full" type="text" value="{{ old('street', $client['street'] ?? '') }}" name="street" data-url="{{ route('dashboard.get.street') }}" />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="street_nr">{{ __('Nr.') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="street_nr" class="block mt-1 w-full" type="text" value="{{ old('street_nr', $client['street_nr'] ?? '') }}" name="street_nr" />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="bl_code">{{ __('Bl.') }}</x-jet-label>
                        <x-jet-input id="bl_code" class="block mt-1 w-full" type="text" value="{{ old('bl_code', $client['bl_code'] ?? '') }}" name="bl_code" />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="bl_letter">{{ __('Sc.') }}</x-jet-label>
                        <x-jet-input id="bl_letter" class="block mt-1 w-full" type="text" value="{{ old('bl_letter', $client['bl_letter'] ?? '') }}" name="bl_letter" />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="intercom">{{ __('Interfon') }}</x-jet-label>
                        <x-jet-input id="intercom" class="block mt-1 w-full" type="text" value="{{ old('intercom', $client['intercom'] ?? '') }}" name="intercom" />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="floor">{{ __('Et.') }}</x-jet-label>
                        <x-jet-input id="floor" class="block mt-1 w-full" type="text" value="{{ old('floor', $client['floor'] ?? '') }}" name="floor" />
                    </div>
                    <div class="col-span-4 md:col-span-3 lg:col-span-1">
                        <x-jet-label for="apartment">{{ __('Ap.') }}</x-jet-label>
                        <x-jet-input id="apartment" class="block mt-1 w-full" type="text" value="{{ old('apartment', $client['apartment'] ?? '') }}" name="apartment" />
                    </div>
                    <div class="col-span-12 md:col-span-6 lg:col-span-4">
                        <x-jet-label for="landmark">{{ __('Reper') }}</x-jet-label>
                        <x-jet-input id="landmark" class="block mt-1 w-full" type="text" value="{{ old('landmark', $client['landmark'] ?? '') }}" name="landmark" />
                    </div>
                    <div class="col-span-12">
                        <x-jet-label for="more_information">{{ __('Alte informatii') }}</x-jet-label>
                        <x-jet-input id="more_information" class="block mt-1 w-full" type="text" value="{{ old('more_information', $client['more_information'] ?? '') }}" name="more_information" />
                    </div>
                </div>

                <div class="mt-4">
                    <h4 class="text-2xl">{{ __('Produse') }}</h4>
                    <div class="grid gap-3 grid-cols-12 text-normal hidden lg:grid">
                    	<p class="block mt-1 col-span-5">{{ __('Nume') }}</p>
                        <p class="block mt-1 col-span-2">{{ __('Cantitate') }}</p>
                        <p class="block mt-1 col-span-2">{{ __('Pret unitar') }}</p>
                        <p class="block mt-1 col-span-2">{{ __('Valoare') }}</p>
                    	<p class="block mt-1 col-span-1"></p>
                    </div>
                    <div id="products" class="w-full">
                    	@if(old('product') != null || isset($products) && (count($products) > 0) && isset($products['nr_products']))
                    		@php $nrProducts = $nrProducts > 0 ? $nrProducts : ($products['nr_products'] ?? 0); @endphp
	                    	@for($i = 0 ; $i < $nrProducts ; $i++)
			                    <div class="grid gap-2 grid-cols-12 products-group pb-2">
                                    <div class="col-span-12 lg:col-span-5">
                                        <x-jet-label class="block lg:hidden">{{ __('Nume') }}</x-jet-label>
                                        <x-jet-input class="block mt-1 w-full" type="text" value="{{ old('product.'.$i) ?? $products['name_'.$i] ?? '' }}" name="product[]" />
                                    </div>
                                    <div class="col-span-4 lg:col-span-2">
                                        <x-jet-label class="block lg:hidden">{{ __('Cantitate') }}</x-jet-label>
                                        <x-jet-input class="block mt-1 w-full" type="number" value="{{ old('qty.'.$i) ?? $products['qty_'.$i] ?? '1' }}" step="0.01" min="0" name="qty[]" />
                                    </div>
                                    <div class="col-span-4 lg:col-span-2">
                                        <x-jet-label class="block lg:hidden">{{ __('Pret unitar') }}</x-jet-label>
                                        <x-jet-input class="block mt-1 w-full" type="number" value="{{ old('price.'.$i) ?? $products['price_'.$i] ?? '' }}" step="0.01" name="price[]" />
                                    </div>
                                    <div class="col-span-4 lg:col-span-2">
                                        <x-jet-label class="block lg:hidden">{{ __('Valoare') }}</x-jet-label>
                                        <x-jet-input class="block mt-1 w-full opacity-50 outline-none focus:border-gray-300 focus:ring-0 shadow-none" type="number" value="{{ round(old('qty.'.$i, $products['qty_'.$i] ?? 0) * old('price.'.$i, $products['price_'.$i] ?? '0'), 2) }}" step="0.01" readonly />
                                    </div>
                                    <div class="col-span-12 lg:col-span-8">
                                        <x-jet-label class="block lg:hidden">{{ __('Descriere') }}</x-jet-label>
                                        <textarea class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" rows="1" placeholder="{{ __('Ex: Colet 1 - greutate kg, lungime x latime x inaltime (Optional)') }}"  name="description[]" >{{ old('description.'.$i) ?? $products['description_'.$i] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-span-4 lg:col-span-1 w-full mx-auto">
                                        <x-jet-button type="button" class="mt-1 w-full remove-product">
                                            <i class="fa fa-minus w-full" style="line-height: 24px;"></i>
                                        </x-jet-button>
                                    </div>
			                    </div>
		                    @endfor
		                @else
	                    	<div class="grid gap-2 grid-cols-12 products-group pb-2">
                                <div class="col-span-12 lg:col-span-5">
                                    <x-jet-label class="block lg:hidden">{{ __('Nume') }}</x-jet-label>
                                    <x-jet-input class="block mt-1 w-full" type="text" value="" name="product[]" />
                                </div>
                                <div class="col-span-4 lg:col-span-2">
                                    <x-jet-label class="block lg:hidden">{{ __('Cantitate') }}</x-jet-label>
                                    <x-jet-input class="block mt-1 w-full" type="number" value="" step="0.01" min="0" value="1" name="qty[]" />
                                </div>
                                <div class="col-span-4 lg:col-span-2">
                                    <x-jet-label class="block lg:hidden">{{ __('Pret unitar') }}</x-jet-label>
                                    <x-jet-input class="block mt-1 w-full" type="number" value="" step="0.01" name="price[]" />
                                </div>
                                <div class="col-span-4 lg:col-span-2">
                                    <x-jet-label class="block lg:hidden">{{ __('Valoare') }}</x-jet-label>
                                    <x-jet-input class="block mt-1 w-full opacity-50 outline-none focus:border-gray-300 focus:ring-0 shadow-none" type="number" value="" step="0.01" name="total[]" readonly />
                                </div>
                                <div class="col-span-12 lg:col-span-8">
                                    <x-jet-label class="block lg:hidden">{{ __('Descriere') }}</x-jet-label>
                                    <textarea class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" rows="1" value="" placeholder="{{ __('Ex: Colet 1 - greutate kg, lungime x latime x inaltime (Optional)') }}"  name="description[]" ></textarea>
                                </div>
                                <div class="col-span-4 lg:col-span-1 w-full mx-auto">
                                    <x-jet-button type="button" class="mt-1 w-full remove-product">
                                        <i class="fa fa-minus w-full" style="line-height: 24px;"></i>
                                    </x-jet-button>
                                </div>
		                    </div>
                    	@endif
	                </div>
                    <div class="w-full">
                    	<x-jet-button type="button" class="add-product">
                            <i class="fa fa-plus"></i>
                            <span>{{ __('Adauga produs') }}</span>
                        </x-jet-button>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-jet-button class="ml-4" id="submit">
                        {{ isset($invoice) ?  __('Edit') : __('Create') }}
                    </x-jet-button>
                </div>
            </form>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
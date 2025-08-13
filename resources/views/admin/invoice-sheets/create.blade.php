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
<script src="{{ asset('js/admin/pages/borderouri.js') }}?v=19032024"></script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Fise facturi') }}</x-slot>
        <x-slot name="href">{{ route('admin.invoice-sheets.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza fisa facturi') }}</x-slot>
        <x-jet-authentication-card class="px-2 form-admin" classForm="md:max-w-xl lg:max-w-4xl">
            <x-slot name="logo"></x-slot>

            <x-jet-validation-messages class="mb-4" />
            <x-jet-validation-errors class="mb-4" />

            <form method="POST" action="{{ isset($item) ? route('admin.invoice-sheets.update', $item->id) : route('admin.invoice-sheets.store') }}" enctype="multipart/form-data">
                @csrf

                <h4 class="text-2xl">{{ __('Date fisa facturi') }}</h4>
                <div class="grid grid-cols-6 gap-2 my-4">

                    
                    <div class="col-span-6 lg:col-span-2 select2-container">
                        <x-jet-label for="user_id">{{ __('Utilizator') }} <span class="text-red-500">*</span></x-jet-label>
                        <select name="user_id" id="user_id" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full select2-ajax" 
                            data-url="{{ route('admin.users.get') }}" 
                            data-default="{{ $user ? $user->id : ($item->user_id ?? '') }}" required>
                            @if(isset($user) && $user->id)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @elseif(isset($item) && $item->user_id)
                                <option value="{{ $item->user_id }}">{{ $item->user->name }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                        <x-jet-label for="start_date">{{ __('Data de inceput') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="start_date" class="block mt-1 w-full datepicker" type="text" name="start_date"
                            data-default="{{ old('start_date', $item->start_date ?? now()->format('Y-m-d')) }}" 
                            value="{{ old('start_date', $item->start_date ?? now()->format('Y-m-d')) }}" required />
                    </div>
                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                        <x-jet-label for="end_date">{{ __('Data de sfarsit') }} <span class="text-red-500">*</span></x-jet-label>
                        <x-jet-input id="end_date" class="block mt-1 w-full datepicker" type="text" name="end_date"
                            data-default="{{ old('end_date', $item->end_date ?? now()->format('Y-m-d')) }}" 
                            value="{{ old('end_date', $item->end_date ?? now()->format('Y-m-d')) }}" required />
                    </div>
                    <div class="col-span-6 md:col-span-3 lg:col-span-2">
                        <x-jet-label for="payed_at">{{ __('Data emiteri') }}</x-jet-label>
                        <x-jet-input id="payed_at" class="block mt-1 w-full datepicker" type="text" name="payed_at"
                            data-default="{{ old('payed_at', $item->payed_at ?? '') }}" 
                            value="{{ old('payed_at', $item->payed_at ?? '') }}" />
                    </div>
                    @isset($item)
                        <div class="col-span-6 md:col-span-3 lg:col-span-2 opacity-60">
                            <x-jet-label for="total">{{ __('Total') }} ({{ __('doar vizualizare') }})</x-jet-label>
                            <x-jet-input id="total" class="block mt-1 w-full" type="number" step="0.01" min="0" value="{{ old('total', $item->total ?? '') }}" disabled readonly />
                        </div>
                    @endisset
                </div>

                <div class="mt-4">
                    <h4 class="text-2xl">
                        <span class="float-left mr-2">{{ __('Livrari fisa facturi') }}</span>
                        <x-jet-button class="float-left sm:float-right bg-red-500 my-1" type="button" onclick="document.getElementById('items').innerHTML=''">{{ __('Sterge awb-urile') }}</x-jet-button>
                        @isset($item)
                        <x-ad::modal class="float-left sm:float-right inline-block my-1 available-awb-list" 
                            id="available-awb" :title="__('Adauga livrari in fisa de facturi')" :button_text="__('Livrari valabile')">
                            @foreach($livrari_valabile ?? [] as $i => $livrare)
                                <x-ad::invoice-sheets.awb-row :livrare="$livrare" name="valabil" :i="$i" :disabled="true" :all_checked="false" />
                            @endforeach
                            <x-slot name="buttons">
                                <x-jet-button class="bg-green-500 add-awb-list" type="button" x-on:click="expanded = false"><i class="fas fa-plus mr-1"></i>{{ __('Adauga') }}</x-jet-button>
                            </x-slot>
                        </x-ad::modal>
                        @endisset
                        <div class="clear-both"></div>
                    </h4>
                    <div id="items" class="w-full">
                    	@for($i = 0 ; $i < count($livrari ?? ['']) ; $i++)
                            <x-ad::invoice-sheets.awb-row :livrare="($livrari[$i] ?? '')" :i="$i" />
	                    @endfor
	                </div>
                    <div class="w-full">
                    	<x-jet-button type="button" class="add-item">
                            <i class="fa fa-plus"></i>
                            <span>{{ __('Adauga awb') }}</span>
                        </x-jet-button>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-jet-button class="ml-4" id="submit">
                        {{ isset($item) ?  __('Edit') : __('Create') }}
                    </x-jet-button>
                </div>
            </form>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
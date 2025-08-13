<x-jet-form-section-simple submit="{{ route('dashboard.settings.repayment.update') }}">
    <x-slot name="form">
        <h5 class="col-span-12">{{ __('Cont bancar (pentru ramburs in cont)') }}</h5>
        <p class="col-span-12"><i>{{ __('Cont bancar al unei banci romanesti in care se vor vira rambursurile colectate pt Dvs de la destinatarii coletelor') }}.</i></p>
        @csrf
        <div class="row col-span-12 m-0">
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="card_owner_name" >{{ __('Nume titular cont bancar') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="card_owner_name" type="text" class="block w-full" name="card_owner_name" value="{{ old('card_owner_name') ?? $this->state['card_owner_name'] ?? '' }}" autocomplete="card_owner_name" data-length="32" required />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="card_owner_name" class="mt-2 errorTxt1" />
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="iban" >{{ __('Cont bancar (IBAN)') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="iban" type="text" class="block w-full" name="iban" value="{{ old('iban') ?? $this->state['iban'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="iban" class="mt-2 errorTxt1" />
            </div>
        </div>
        {{-- <h5 class="col-span-12">{{ __('Optiuni rambursuri') }}</h5>
        <p class="col-span-12"><i>{{ __('Selectati momentul in care sa se vireze rambursurile colectate de la clientii Dvs. Aveti optiunea de a alege ca platile catre contul Dvs bancar sa se faca la anumite intervale de timp sau la atingerea unei sume colectate minime.') }}</i></p>
        <div class="row col-span-12 m-0 mt-2">
            <h6 class="m-1"><i>{{ __('Interval de timp') }}</i> {{ __('pentru trimitere rambursuri') }}:</h6>
            <div class="input-field col s12">
                <p>
                    <label>
                        <input class="with-gap" id="repayment_time_1" name="time" type="radio" value="1" {{ old('time') == '1' ? 'checked' : ( isset($this->state['time']) && $this->state['time'] == '1' ? 'checked' : '') }}>
                        <span class="text-capitalize">{{ __('Zilnic') }}</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_time_2" name="time" type="radio" value="2" {{ old('time') == '2' ? 'checked' : ( isset($this->state['time']) && $this->state['time'] == '2' ? 'checked' : '') }}>
                        <span class="text-capitalize">{{ __(' De 2 ori pe saptamana ') }}</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_time_3" name="time" type="radio" value="3" {{ old('time') == '3' ? 'checked' : ( isset($this->state['time']) && $this->state['time'] == '3' ? 'checked' : '') }}>
                        <span class="text-capitalize">{{ __('Saptmanal') }}</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_time_4" name="time" type="radio" value="4" {{ old('time') == '4' ? 'checked' : ( isset($this->state['time']) && $this->state['time'] == '4' ? 'checked' : '') }}>
                        <span class="text-capitalize">{{ __('La 2 saptamani') }}</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_time_5" name="time" type="radio" value="5" {{ old('time') == '5' ? 'checked' : ( isset($this->state['time']) && $this->state['time'] == '5' ? 'checked' : '') }}>
                        <span class="text-capitalize">{{ __('La sfarsitul lunii') }}</span>
                    </label>&nbsp;&nbsp;
                </p>
                <x-jet-input-error for="time" class="mt-2 errorTxt1" />
            </div>
        </div>
        <div class="row col-span-12 m-0 mt-2 show-sum">
            <h6 class="m-1"><i>{{ __('Sau') }} {{ __('suma minima') }}</i> {{ __('care declanseaza virarea rambursurilor') }}:</h6>
            <div class="input-field col s12">
                <p>
                    <label>
                        <input class="with-gap" id="repayment_sum_1" name="sum" type="radio" value="200" {{ old('sum') == '200' ? 'checked' : ( isset($this->state['sum']) && $this->state['sum'] == '200' ? 'checked' : '') }}>
                        <span class="text-capitalize">200</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_sum_2" name="sum" type="radio" value="500" {{ old('sum') == '500' ? 'checked' : ( isset($this->state['sum']) && $this->state['sum'] == '500' ? 'checked' : '') }}>
                        <span class="text-capitalize">500</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_sum_3" name="sum" type="radio" value="1000" {{ old('sum') == '1000' ? 'checked' : ( isset($this->state['sum']) && $this->state['sum'] == '1000' ? 'checked' : '') }}>
                        <span class="text-capitalize">1000</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_sum_4" name="sum" type="radio" value="2000" {{ old('sum') == '2000' ? 'checked' : ( isset($this->state['sum']) && $this->state['sum'] == '2000' ? 'checked' : '') }}>
                        <span class="text-capitalize">2000</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_sum_5" name="sum" type="radio" value="5000" {{ old('sum') == '5000' ? 'checked' : ( isset($this->state['sum']) && $this->state['sum'] == '5000' ? 'checked' : '') }}>
                        <span class="text-capitalize">5000</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_sum_6" name="sum" type="radio" value="10000" {{ old('sum') == '10000' ? 'checked' : ( isset($this->state['sum']) && $this->state['sum'] == '10000' ? 'checked' : '') }}>
                        <span class="text-capitalize">10000</span>
                    </label>&nbsp;&nbsp;
                    <label>
                        <input class="with-gap" id="repayment_sum_7" name="sum" type="radio" value="-1" {{ old('sum') == '-1' ? 'checked' : ( isset($this->state['sum']) && $this->state['sum'] == '-1' ? 'checked' : '') }}>
                        <span class="text-capitalize">{{ __('Fara limita') }}</span>
                    </label>&nbsp;&nbsp;
                </p>
                <x-jet-input-error for="sum" class="mt-2 errorTxt1" />
            </div>
        </div>
        <div class="input-field col-span-12">
            <div class="switch -ml-3">
                <label>
                    <input id="one_day" value="1" name="one_day" {{ old('one_day') ? 'checked' : ( $this->state['one_day'] ?? '' ? 'checked' : '') }} type="checkbox">
                    <span class="lever"></span>
                    <span style="font-size: 1rem;">{{ __('Asteapta inca 1 zi daca sunt rambursuri in procesare') }}</span>
                </label>
                <x-jet-input-error for="one_day" class="mt-2 errorTxt1" />
            </div>
        </div>
        <p class="col-span-12"><i>{{ __('In cazul in care se detecteaza ca in ziua urmatoare vor fi incasate alte rambursuri, daca setarea este activa se vor astepta si acestea. Se va astepta maxim 1 zi.') }}</i></p> --}}
        
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section-simple>

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-facturare.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('js/pages/form-facturare.js') }}"></script>
@if(session()->has('success'))
    <script type="text/javascript">
        M.toast({html: '{{ session()->get('success') }}.', classes: 'green accent-4', displayLength: 5000});
        $('#toast-container').css({
            top: '19%',
            right: '6%',
        });
    </script>
@endif
@endpush
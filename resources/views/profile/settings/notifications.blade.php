<x-jet-form-section-simple submit="{{ route('dashboard.settings.notifications.update') }}">
    <x-slot name="form">
        @csrf
        <div class="col-span-12">
            <h5>{{ __('Mail facturi') }}</h5>
            <p>{{ __('Aceasta optiune activeaza sau dezactiveaza trimiterea facturilor pe mail. Acestea pot fi descarcate in continuare din platforma') }}.</p>
            {{-- <p><i>{{ __('Pot fi adaugate pana la 3 adrese la care sa se trimita facturile') }}.</i></p> --}}
        </div>
        <div class="row col-span-12 m-0" style="margin-left: -0.25rem;">
            <div class="input-field col s12 my-3">
                <div class="switch -ml-6">
                    <label>
                        <input id="invoice_active" value="1" name="invoice_active" {{ old('invoice_active') ? 'checked' : ( $this->state['invoice_active'] ?? '' ? 'checked' : '') }} type="checkbox">
                        <span class="lever"></span>
                        <span style="font-size: 1rem;">{{ __('Trimite facturile pe mail') }}</span>
                    </label>
                    <x-jet-input-error for="invoice_active" class="mt-2 errorTxt1" />
                </div>
            </div>
            <div class="input-field col l4 m6 s12 my-3 show-invoice-email">
                <x-jet-label for="invoice_email" >{{ __('Email') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="invoice_email" type="text" class="block w-full" name="invoice_email" value="{{ old('invoice_email') ?? $this->state['invoice_email'] ?? $this->user->email }}" autocomplete="invoice_email" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="invoice_email" class="mt-2 errorTxt1" />
            </div>
        </div>

        <div class="col-span-12">
            <h5>{{ __('Fisier AWB la creare comanda') }}</h5>
            <p>{{ __('Aceasta optiune activeaza sau dezactiveaza trimiterea awb-ului pe mail. Acestea pot fi descarcate in continuare din platforma') }}.</p>
            {{-- <p><i>{{ __('Pot fi adaugate pana la 3 adrese la care sa se trimita facturile') }}.</i></p> --}}
        </div>
        <div class="row col-span-12 m-0" style="margin-left: -0.25rem;">
            <div class="input-field col s12 my-3">
                <div class="switch -ml-6">
                    <label>
                        <input id="awb_active" value="1" name="awb_active" {{ old('awb_active') ? 'checked' : ( $this->state['awb_active'] ?? '' ? 'checked' : '') }} type="checkbox">
                        <span class="lever"></span>
                        <span style="font-size: 1rem;">{{ __('Trimite AWB pe mail') }}</span>
                    </label>
                    <x-jet-input-error for="awb_active" class="mt-2 errorTxt1" />
                </div>
            </div>
            {{-- <div class="input-field col l4 m6 s12 my-3 show-awb-email">
                <x-jet-label for="awb_email" >{{ __('Email') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="awb_email" type="text" class="block w-full" name="awb_email" value="{{ old('awb_email') ?? $this->state['awb_email'] ?? $this->user->email }}" autocomplete="awb_email" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="invoice_email" class="mt-2 errorTxt1" />
            </div> --}}
        </div>


        <div class="col-span-12">
            <h5>{{ __('Mail alerte livrare') }}</h5>
            <p>{{ __('Aceasta optiune activeaza sau dezactiveaza trimiterea de alerte in cazul in care sunt probleme la livrarea coletelor. Cu ajutorul acestor alerte puteti detecta problemele aparute la livrare inainte ca firma de curierat sa faca returul trimiterii') }}.</p>
            {{-- <p><i>{{ __('Pot fi adaugate pana la 3 adrese la care sa se trimita alertele') }}.</i></p> --}}
        </div>
        <div class="row col-span-12 m-0" style="margin-left: -0.25rem;">
            <div class="input-field col s12 my-3">
                <div class="switch -ml-6">
                    <label>
                        <input id="alerts_active" value="1" name="alerts_active" {{ old('alerts_active') ? 'checked' : ( $this->state['alerts_active'] ?? '' ? 'checked' : '') }} type="checkbox">
                        <span class="lever"></span>
                        <span style="font-size: 1rem;">{{ __('Trimite alerte cu probleme de livrare') }}</span>
                    </label>
                    <x-jet-input-error for="alerts_active" class="mt-2 errorTxt1" />
                </div>
            </div>
            <div class="input-field col l4 m6 s12 my-3 show-alerts-email">
                <x-jet-label for="alerts_email" >{{ __('Email') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="alerts_email" type="text" class="block w-full" name="alerts_email" value="{{ old('alerts_email') ?? $this->state['alerts_email'] ?? $this->user->email }}" autocomplete="alerts_email" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="alerts_email" class="mt-2 errorTxt1" />
            </div>
        </div>
        <div class="col-span-12">
            <h5>{{ __('Mail borderou rambursuri') }}</h5>
            <p>{{ __('Aceasta optiune activeaza sau dezactiveaza trimiterea borderourilor de ramburs in momentul in care se face o plata. Acestea pot fi descarcate in continuare din platforma') }}.</p>
            {{-- <p><i>{{ __('Pot fi adaugate pana la 3 adrese la care sa se trimita borderourile') }}.</i></p> --}}
        </div>
        <div class="row col-span-12 m-0" style="margin-left: -0.25rem;">
            <div class="input-field col s12 my-3">
                <div class="switch -ml-6">
                    <label>
                        <input id="ramburs_active" value="1" name="ramburs_active" {{ old('ramburs_active') ? 'checked' : ( $this->state['ramburs_active'] ?? '' ? 'checked' : '') }} type="checkbox">
                        <span class="lever"></span>
                        <span style="font-size: 1rem;">{{ __('Trimite borderourile de ramburs pe mail') }}</span>
                    </label>
                    <x-jet-input-error for="ramburs_active" class="mt-2 errorTxt1" />
                </div>
            </div>
            <div class="input-field col l4 m6 s12 my-3 show-ramburs-email">
                <x-jet-label for="ramburs_email" >{{ __('Email') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="ramburs_email" type="text" class="block w-full" name="ramburs_email" value="{{ old('ramburs_email') ?? $this->state['ramburs_email'] ?? $this->user->email }}" autocomplete="ramburs_email" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="ramburs_email" class="mt-2 errorTxt1" />
            </div>
        </div>
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
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-facturare.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('/js/pages/form-facturare.js') }}"></script>
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
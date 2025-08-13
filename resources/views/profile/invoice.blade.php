<x-jet-form-section-simple submit="{{ route('dashboard.invoice.update') }}">
    <x-slot name="form">
        <h5 class="col-span-12 mt-0"><i>{{ __('Date necesare pentru emiterea facturii fiscale') }}</i></h5>
        {{-- <x-jet-validation-errors /> --}}
        @csrf
        <div class="row col-span-12 m-0">
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="first_name" >{{ __('First Name') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="first_name" type="text" class="block w-full" name="first_name" value="{{ old('first_name') ?? $this->state['first_name'] ?? '' }}" autocomplete="first_name" required />
                <small class="errorTxt1 float-right red-text">@error('first_name') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="last_name" >{{ __('Last Name') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="last_name" type="text" class="block w-full" name="last_name" value="{{ old('last_name') ?? $this->state['last_name'] ?? '' }}" autocomplete="last_name"  />
                <small class="errorTxt1 float-right red-text">@error('last_name') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="phone">{{ __('Numar de telefon') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="phone" type="text" placeholder="" class="block w-full" name="phone" value="{{ old('phone') ?? $this->state['phone'] ?? '' }}" style="box-sizing: border-box;" autocomplete="phone"  />
                <small class="errorTxt1 float-right red-text">@error('phone') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col s12">
                <div class="switch -ml-3">
                    <label>
                        <input id="juridic" value="1" name="is_company" {{ old('is_company') ? 'checked' : (isset($this->state['is_company']) && $this->state['is_company'] ? 'checked' : '') }} type="checkbox">
                        <span class="lever"></span>
                        <span style="font-size: 1rem;">Persoana juridica</span>
                    </label>
                </div>
            </div>
            <div class="input-field col s12 m-0 show-nif">
                <div class="card-alert card orange lighten-5 m-0">
                    <div class="card-content orange-text">
                        <p><i class="material-icons">warning</i> <b>Atentie!</b> Numarul de identificare fiscala (NIF) este atribuit de ANAF entitatilor juridice care sunt din alte tari, dar platesc taxe si in Romania. Va rugam sa folositi NIF ca optiune doar in cazul sunteti sigur ca aceasta optiune vi se potriveste.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row col-span-12 m-0 show-juridic">
            <h6 class="ml-1">Date companie</h6>
            <div class="input-field col l1 m6 s12">
                <select name="company_type" id="company_type" class="">
                    <option value="1" {{ old('company_type') == "1" || (!old('company_type') && isset($this->state['company_type']) && $this->state['company_type'] == "1") ? 'selected' : '' }}>CUI</option>
                    <option value="2" {{ old('company_type') == "2" || (!old('company_type') && isset($this->state['company_type']) && $this->state['company_type'] == "2") ? 'selected' : '' }}>NIF</option>
                </select>
                <small class="errorTxt1 float-right red-text">@error('company_type') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l3 m6 s12">
                <x-jet-label for="cui_nif" ><span class="show-cui">{{ __('CUI') }}</span><span class="show-nif">{{ __('NIF') }}</span> <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="cui_nif" type="text" class="block w-full" name="cui_nif" value="{{ old('cui_nif') ?? $this->state['cui_nif'] ?? '' }}" autocomplete="cui_nif"  />
                <small class="errorTxt1 float-right red-text">@error('cui_nif') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="nr_reg_com" >{{ __('Nr. Reg. Com.') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="nr_reg_com" type="text" class="block w-full" name="nr_reg_com" value="{{ old('nr_reg_com') ?? $this->state['nr_reg_com'] ?? $this->state['nr_reg'] ?? '' }}" autocomplete="nr_reg_com"  />
                <small class="errorTxt1 float-right red-text">@error('nr_reg_com') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="company_name" >{{ __('Nume firma') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="company_name" type="text" class="block w-full" name="company_name" value="{{ old('company_name') ?? $this->state['company_name'] ?? '' }}" autocomplete="company_name"  />
                <small class="errorTxt1 float-right red-text">@error('company_name') {{ $message }} @enderror</small>
            </div>
        </div>
        <div class="row col-span-12 mt-2">
            <h6 class="m-1">Adresa de facturare</h6>
            <div class="input-field col s12">
                <x-jet-label for="country" >{{ __('Tara') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="country" type="text" placeholder="" class="block w-full" name="country" value="{{ old('country') ?? $this->state['country'] ?? '' }}"  style="box-sizing: border-box;" />
                <small class="errorTxt1 float-right red-text">@error('country') {{ $message }} @enderror</small>
                <x-jet-input id="country_code" type="hidden" name="country_code" value="{{ old('country_code') ?? $this->state['country_code'] ?? 'ro' }}" />
            </div>
            <div class="input-field col l2 m6 s12">
                <x-jet-label for="postcode" >{{ __('Cod postal') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="postcode" type="text" class="block w-full" name="postcode" value="{{ old('postcode') ?? $this->state['postcode'] ?? '' }}" data-postcode="1" />
                <small class="errorTxt1 float-right red-text">@error('postcode') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="locality" >{{ __('Localitate') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="locality" type="text" class="block w-full" name="locality" value="{{ old('locality') ?? $this->state['locality'] ?? '' }}" data-url="{{ route('dashboard.get.county') }}" />
                <small class="errorTxt1 float-right red-text">@error('locality') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l2 m6 s12">
                <x-jet-label for="county" >{{ __('Judet') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="county" type="text" class="block w-full" name="county" value="{{ old('county') ?? $this->state['county'] ?? '' }}" readonly />
                <small class="errorTxt1 float-right red-text">@error('county') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="street" >{{ __('Strada') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="street" type="text" class="block w-full" name="street" value="{{ old('street') ?? $this->state['street'] ?? '' }}" data-url="{{ route('dashboard.get.street') }}" />
                <small class="errorTxt1 float-right red-text">@error('street') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l1 m3 s12">
                <x-jet-label for="street_nr" >{{ __('Nr.') }} <span class="red-text">*</span></x-jet-label>
                <x-jet-input id="street_nr" type="text" class="block w-full" name="street_nr" value="{{ old('street_nr') ?? $this->state['street_nr'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('street_nr') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l1 m3 s12">
                <x-jet-label for="bl_code" >{{ __('Bl.') }} </x-jet-label>
                <x-jet-input id="bl_code" type="text" class="block w-full" name="bl_code" value="{{ old('bl_code') ?? $this->state['bl_code'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('bl_code') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l1 m3 s12">
                <x-jet-label for="bl_letter" >{{ __('Sc.') }} </x-jet-label>
                <x-jet-input id="bl_letter" type="text" class="block w-full" name="bl_letter" value="{{ old('bl_letter') ?? $this->state['bl_letter'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('bl_letter') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l1 m3 s12">
                <x-jet-label for="intercom" >{{ __('Interfon') }} </x-jet-label>
                <x-jet-input id="intercom" type="text" class="block w-full" name="intercom" value="{{ old('intercom') ?? $this->state['intercom'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('intercom') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l1 m3 s12">
                <x-jet-label for="floor" >{{ __('Et.') }} </x-jet-label>
                <x-jet-input id="floor" type="text" class="block w-full" name="floor" value="{{ old('floor') ?? $this->state['floor'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('floor') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l1 m3 s12">
                <x-jet-label for="apartment" >{{ __('Ap.') }} </x-jet-label>
                <x-jet-input id="apartment" type="text" class="block w-full" name="apartment" value="{{ old('apartment') ?? $this->state['apartment'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('apartment') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="landmark" >{{ __('Reper') }} </x-jet-label>
                <x-jet-input id="landmark" type="text" class="block w-full" name="landmark" value="{{ old('landmark') ?? $this->state['landmark'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('landmark') {{ $message }} @enderror</small>
            </div>
            <div class="input-field col s12">
                <x-jet-label for="more_information" >{{ __('Alte informatii') }} </x-jet-label>
                <x-jet-input id="more_information" type="text" class="block w-full" name="more_information" value="{{ old('more_information') ?? $this->state['more_information'] ?? '' }}" />
                <small class="errorTxt1 float-right red-text">@error('more_information') {{ $message }} @enderror</small>
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
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-facturare.css') }}">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
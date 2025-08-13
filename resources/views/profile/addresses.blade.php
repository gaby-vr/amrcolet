<div>
    <x-jet-table>
        <h5 class="col-span-12 mt-2"><i>{{ __('Tabel adrese') }}</i></h5>
        <x-slot name="thead">
            @forelse($addresses as $address)
            <x-jet-tr location='thead'>
                <x-jet-td location='thead'>{{ __('Nume') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Tara') }}</x-jet-td>
                <x-jet-td class="text-center" location='thead'>{{ __('Favorit') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Optiuni') }}</x-jet-td>
            </x-jet-tr>
            @empty
            <x-jet-tr location='thead'>
                <x-jet-td location='thead'>{{ __('Nume') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Tara') }}</x-jet-td>
                <x-jet-td class="text-center" location='thead'>{{ __('Favorit') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Optiuni') }}</x-jet-td>
            </x-jet-tr>
            @endif
        </x-slot>

        @forelse($addresses as $address)
        <x-jet-tr>
            <x-jet-td>{{ $address->address_name }}</x-jet-td>
            <x-jet-td>{{ $address->country }}</x-jet-td>
            <x-jet-td class="sm:text-center">{!! $address->favorite == '1' ? '<i class="fas fa-star text-yellow-500"></i>' : '<i class="far fa-star text-yellow-500"></i>' !!}</x-jet-td>
            <x-jet-td>
                <a href="javascript:void(0)" class="btn-form-edit" data-get="{{ route('dashboard.addresses.get', ['address' => $address->id]) }}" data-route="{{ route('dashboard.addresses.update', ['address' => $address->id]) }}" title="{{ __('Edit') }}">
                    <i class="fas fa-pencil-alt text-blue-500"></i>
                </a>
                /
                <form method="POST" action="{{ route('dashboard.addresses.delete', ['address' => $address->id]) }}" class="inline" title="{{ __('Delete') }}">
                    @csrf
                    <a href="{{ route('dashboard.addresses.delete', ['address' => $address->id]) }}"
                             onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        <i class="fas fa-trash-alt text-red-500"></i>
                    </a>
                </form>
            </x-jet-td>
        </x-jet-tr>
        @empty
        <x-jet-tr class="border-0" style="height: 100%;">
            <x-jet-td colspan="4" class="rounded-r-md" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu ai salvat nici o adresa') }}</x-jet-td>
        </x-jet-tr>
        @endif

        <x-slot name="pagination">
            @if($addresses)
                {{ $addresses->links() }}
            @endif
        </x-slot>
    </x-jet-table>    

    <x-jet-form-section-simple class="form-section {{ session()->has('validated') ? 'show' : '' }}" submit="{{ route('dashboard.addresses.update', ['address' => session()->get('edit')]) }}" route="1" classes="shown-form hidden">

        <x-jet-validation-errors />

        <x-slot name="form">
            <h5 class="col-span-12 form-name" data-add="{{ __('Adauga adresa') }}" data-edit="{{ __('Editeaza adresa') }}"><i>{{ session()->has('edit') ? __('Editeaza adresa') : __('Adauga adresa') }}</i></h5>
            @csrf
            <div class="row col-span-12 m-0">
                <div class="input-field col s12">
                    <x-jet-label for="address_name" >{{ __('Nume adresa') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="address_name" type="text" placeholder="" class="block w-full {{ $errors->has('address_name') ? 'invalid' : '' }}" name="address_name" value="{{ old('address_name') ?? $this->state['address_name'] ?? '' }}" required />
                    <small class="errorTxt1 float-right red-text">@error('address_name') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col s12">
                    <x-jet-label for="country" >{{ __('Tara') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="country" type="text" placeholder="" class="block w-full country {{ $errors->has('country') ? 'invalid' : '' }}" name="country" value="{{ old('country') ?? $this->state['country'] ?? '' }}"  style="box-sizing: border-box;" required />
                    <small class="errorTxt1 float-right red-text">@error('country') {{ $message }} @enderror</small>
                    <x-jet-input id="country_code" type="hidden" name="country_code" value="{{ old('country_code') ?? $this->state['country_code'] ?? 'ro' }}" />
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="name" >{{ __('Name') }} <span class="red-text">*</span> </x-jet-label>
                    <x-jet-input id="name" type="text" class="block w-full {{ $errors->has('name') ? 'invalid' : '' }}" name="name" value="{{ old('name') ?? $this->state['name'] ?? '' }}" autocomplete="name" required />
                    <small class="errorTxt1 float-right red-text">@error('name') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="phone">{{ __('Numar de telefon') }} <span class="red-text">*</span> </x-jet-label>
                    <x-jet-input id="phone" type="text" placeholder="" class="block w-full phone {{ $errors->has('phone') ? 'invalid' : '' }}" name="phone" value="{{ old('phone') ?? $this->state['phone'] ?? '' }}" style="box-sizing: border-box;" autocomplete="phone" required />
                    <small class="errorTxt1 float-right red-text">@error('phone') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="phone_2" >{{ __('Numar de telefon 2') }} </x-jet-label>
                    <x-jet-input id="phone_2" type="text" placeholder="" class="block w-full phone {{ $errors->has('phone_2') ? 'invalid' : '' }}" name="phone_2" value="{{ old('phone_2') ?? $this->state['phone_2'] ?? '' }}" style="box-sizing: border-box;" autocomplete="phone_2" />
                    <small class="errorTxt1 float-right red-text">@error('phone_2') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="company" >{{ __('Company') }} </x-jet-label>
                    <x-jet-input id="company" type="text" placeholder="" class="block w-full {{ $errors->has('company') ? 'invalid' : '' }}" name="company" value="{{ old('company') ?? $this->state['company'] ?? '' }}" style="box-sizing: border-box;" autocomplete="company" />
                    <small class="errorTxt1 float-right red-text">@error('company') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="email" >{{ __('Email') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="email" type="email" placeholder="" class="block w-full {{ $errors->has('email') ? 'invalid' : '' }}" name="email" value="{{ old('email') ?? $this->state['email'] ?? '' }}" style="box-sizing: border-box;" autocomplete="email" required />
                    <small class="errorTxt1 float-right red-text">@error('email') {{ $message }} @enderror</small>
                </div>
            </div>
            <div class="row col-span-12 mt-2">
                <h6 class="m-1">Adresa</h6>
                <div class="input-field col l2 m6 s12">
                    <x-jet-label for="postcode" >{{ __('Cod postal') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="postcode" type="text" class="block w-full {{ $errors->has('postcode') ? 'invalid' : '' }}" name="postcode" value="{{ old('postcode') ?? $this->state['postcode'] ?? '' }}" data-postcode="1" required />
                    <small class="errorTxt1 float-right red-text">@error('postcode') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="locality" >{{ __('Localitate') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="locality" type="text" class="block w-full {{ $errors->has('locality') ? 'invalid' : '' }}" name="locality" value="{{ old('locality') ?? $this->state['locality'] ?? '' }}" data-url="{{ route('dashboard.get.county') }}" required />
                    <small class="errorTxt1 float-right red-text">@error('locality') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l2 m6 s12">
                    <x-jet-label for="county" >{{ __('Judet/Sector') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="county" type="text" class="block w-full {{ $errors->has('county') ? 'invalid' : '' }}" name="county" readonly value="{{ old('county') ?? $this->state['county'] ?? '' }}" required />
                    <small class="errorTxt1 float-right red-text">@error('county') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="street" >{{ __('Strada') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="street" type="text" class="block w-full {{ $errors->has('street') ? 'invalid' : '' }}" name="street" value="{{ old('street') ?? $this->state['street'] ?? '' }}" data-url="{{ route('dashboard.get.street') }}" required />
                    <small class="errorTxt1 float-right red-text">@error('street') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l1 m3 s12">
                    <x-jet-label for="street_nr" >{{ __('Nr.') }} <span class="red-text">*</span></x-jet-label>
                    <x-jet-input id="street_nr" type="text" class="block w-full {{ $errors->has('street_nr') ? 'invalid' : '' }}" name="street_nr" value="{{ old('street_nr') ?? $this->state['street_nr'] ?? '' }}" required />
                    <small class="errorTxt1 float-right red-text">@error('street_nr') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l1 m3 s12">
                    <x-jet-label for="bl_code" >{{ __('Bl.') }} </x-jet-label>
                    <x-jet-input id="bl_code" type="text" class="block w-full {{ $errors->has('bl_code') ? 'invalid' : '' }}" name="bl_code" value="{{ old('bl_code') ?? $this->state['bl_code'] ?? '' }}" />
                    <small class="errorTxt1 float-right red-text">@error('bl_code') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l1 m3 s12">
                    <x-jet-label for="bl_letter" >{{ __('Sc.') }} </x-jet-label>
                    <x-jet-input id="bl_letter" type="text" class="block w-full {{ $errors->has('bl_letter') ? 'invalid' : '' }}" name="bl_letter" value="{{ old('bl_letter') ?? $this->state['bl_letter'] ?? '' }}" />
                    <small class="errorTxt1 float-right red-text">@error('bl_letter') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l1 m3 s12">
                    <x-jet-label for="intercom" >{{ __('Interfon') }} </x-jet-label>
                    <x-jet-input id="intercom" type="text" class="block w-full {{ $errors->has('intercom') ? 'invalid' : '' }}" name="intercom" value="{{ old('intercom') ?? $this->state['intercom'] ?? '' }}" />
                    <small class="errorTxt1 float-right red-text">@error('intercom') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l1 m3 s12">
                    <x-jet-label for="floor" >{{ __('Et.') }} </x-jet-label>
                    <x-jet-input id="floor" type="text" class="block w-full {{ $errors->has('floor') ? 'invalid' : '' }}" name="floor" value="{{ old('floor') ?? $this->state['floor'] ?? '' }}" />
                    <small class="errorTxt1 float-right red-text">@error('floor') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l1 m3 s12">
                    <x-jet-label for="apartment" >{{ __('Ap.') }} </x-jet-label>
                    <x-jet-input id="apartment" type="text" class="block w-full {{ $errors->has('apartment') ? 'invalid' : '' }}" name="apartment" value="{{ old('apartment') ?? $this->state['apartment'] ?? '' }}" />
                    <small class="errorTxt1 float-right red-text">@error('apartment') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col l4 m6 s12">
                    <x-jet-label for="landmark" >{{ __('Reper') }} </x-jet-label>
                    <x-jet-input id="landmark" type="text" class="block w-full {{ $errors->has('landmark') ? 'invalid' : '' }}" name="landmark" value="{{ old('landmark') ?? $this->state['landmark'] ?? '' }}" />
                    <small class="errorTxt1 float-right red-text">@error('landmark') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col s12">
                    <x-jet-label for="more_information" >{{ __('Alte informatii') }} </x-jet-label>
                    <x-jet-input id="more_information" type="text" class="block w-full {{ $errors->has('more_information') ? 'invalid' : '' }}" name="more_information" value="{{ old('more_information') ?? $this->state['more_information'] ?? '' }}" />
                    <small class="errorTxt1 float-right red-text">@error('more_information') {{ $message }} @enderror</small>
                </div>
                <div class="input-field col s12">
                    <div class="switch -ml-3 mb-2">
                        <label>
                            <input id="favorite" value="1" class="{{ $errors->has('favorite') ? 'invalid' : '' }}" name="favorite" {{ old('favorite') ? 'checked' : (isset($this->state['favorite']) && $this->state['favorite'] ? 'checked' : '') }} type="checkbox">
                            <span class="lever"></span>
                            <span style="font-size: 1rem;">{{ __('Adresa favorita') }}</span>
                        </label>
                        <small class="errorTxt1 float-right red-text">@error('favorite') {{ $message }} @enderror</small>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Saved.') }}
            </x-jet-action-message>

            <x-jet-button class="bg-blue-500 mr-2 shown-form hidden btn-form-cancel" role="button" onclick="event.preventDefault();" data-route="{{ route('dashboard.addresses.update') }}" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-button>

            <x-jet-button class="mr-2 hidden-form btn-form-add" role="button" onclick="event.preventDefault();" data-route="{{ route('dashboard.addresses.update') }}" wire:loading.attr="disabled">
                {{ __('Add') }}
            </x-jet-button>

            <x-jet-button class="shown-form hidden" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section-simple>
</div>

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
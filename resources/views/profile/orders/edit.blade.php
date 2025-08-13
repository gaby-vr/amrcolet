<x-jet-form-section-simple class="form-section {{ session()->has('validated') ? 'show' : '' }}" submit="{{ route('dashboard.templates.update', ['template' => session()->get('edit')]) }}" route="1" classes="shown-form">

    <x-slot name="form">
        <h5 class="col-span-12 form-name" data-add="{{ __('Adauga sablon') }}" data-edit="{{ __('Editeaza sablon') }}"><i>{{ session()->has('edit') ? __('Editeaza sablon') : __('Adauga sablon') }}</i></h5>
        <x-jet-validation-errors class="errors col-span-12 mb-2 pl-1" />
        @csrf
        <div class="row col-span-12 m-0">
            <div class="input-field col s12 m-0">
                <x-jet-label for="name">Nume sablon <span class="red-text">*</span></x-jet-label>
                <x-jet-input type="text" id="name" min="1" value="{{ old('name') }}" name="name" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="name" class="mt-2 errorTxt1" />
            </div>
        </div>
        <div class="row col-span-12 m-0">
            <h6 class="pl-1">Tip pachet</h6>
            <div class="input-field col s12 mt-0">
                <p class="mt-1">
                    <label>
                        <x-jet-radio class="package-type with-gap" name="type" value="1" :checked="old('type') == '1' || !old('type') ? 'true' : '' " />
                        <span>Colet</span>
                    </label>
                    <label>
                        <x-jet-radio class="package-type with-gap" name="type" value="2" :checked="old('type') == '2' ? 'true' : '' " />
                        <span>Plic</span>
                    </label>
                    <x-jet-input-error for="type" class="mt-2 errorTxt1" />
                </p>
                <small class="errorTxt1 float-right red-text"></small>
            </div>
            <br>
        </div>
        <div class="row col-span-12 m-0">
            <h6 class="pl-1">Informatii pachet</h6>
            <div class="input-field col m2 s12 show-colet">
                <x-jet-label for="nr_colete">Numar colete <span class="red-text">*</span></x-jet-label>
                <x-jet-input type="number" id="nr_colete" class="{{ $errors->has('nr_colete') ? 'invalid' : '' }}" min="1" placeholder="" value="{{ old('nr_colete') ?? '1' }}" name="nr_colete" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="nr_colete" class="mt-2 errorTxt1" />

            </div>
            <div class="input-field col m4 s12">
                <x-jet-label for="content">Continut <span class="red-text">*</span></x-jet-label>
                <x-jet-input type="text" id="content" class="{{ $errors->has('content') ? 'invalid' : '' }}" placeholder="" value="{{ old('content') ?? __('Document') }}" name="content" data-length="50" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="content" class="mt-2 errorTxt1" />
            </div>
            <div class="input-field col s12 m-0 show-plic">
                <div class="card-alert card orange lighten-5 m-0">
                    <div class="card-content orange-text">
                        <p><i class="material-icons">warning</i> <b>Atentie!</b> Plicul poate avea maxim format A4 si greutate de maxim 1kg. In caz contrar folositi optiunea <b>Colet</b></p>
                    </div>
                </div>
            </div>
            <div class="input-field col s12 m-0 show-colet">
                <div class="card-alert card orange lighten-5 m-0 mt-1">
                    <div class="card-content orange-text">
                        <p>
                            <i class="material-icons">warning</i> Va rugam sa masurati si sa declarati exact <b>dimensiunile</b> si <b>greutatea</b>.
                            <br>
                            <b>Pretul se calculeaza si in functie de dimensiuni!</b>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row input-field col s12 pl-0 pr-0 show-colet colete" data-wrapper="1" data-items="1">
                @php $colete = old('nr_colete') ?? 1; @endphp
                @for($i = 0 ; $i < $colete ; $i++)
                    <div class="input-field col l12 m6 s12 mt-0 colet-form" data-clone="1">
                        <div class="card m-0">
                            <div class="card-content pt-0 pb-0 m-0">
                                <div class="row">
                                    <div class="input-field col l2 m12 s12 card-alert">
                                        <h5><b>Colet <span class="nr_colet" data-index="1">{{ $i + 1 }}</span></b><i class="material-icons float-right colet-remove cursor-pointer" style="font-size: 2rem;">close</i></h5>
                                    </div>
                                    <div class="input-field col l2 m12 s12">
                                        <x-jet-input id="weight" class="materialize-input{{ $errors->has('weight.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('weight.'.$i) }}" placeholder="{{ __('Weight') }}" name="weight[]" />
                                        <span class="suffix">kg</span>
                                        <small class="errorTxt1 float-right red-text"></small>
                                    </div>
                                    <div class="input-field col l2 m12 s12">
                                        <x-jet-input id="length" class="materialize-input{{ $errors->has('length.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('length.'.$i) }}" placeholder="{{ __('Length') }}" name="length[]" />
                                        <span class="suffix">cm</span>
                                        <small class="errorTxt1 float-right red-text"></small>
                                    </div>
                                    <div class="input-field col l2 m12 s12">
                                        <x-jet-input id="width" class="materialize-input{{ $errors->has('width.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('width.'.$i) }}" placeholder="{{ __('Width') }}" name="width[]" />
                                        <span class="suffix">cm</span>
                                        <small class="errorTxt1 float-right red-text"></small>
                                    </div>
                                    <div class="input-field col l2 m12 s12">
                                        <x-jet-input id="height" class="materialize-input{{ $errors->has('height.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('height.'.$i) }}" placeholder="{{ __('Height') }}" name="height[]" />
                                        <span class="suffix">cm</span>
                                        <small class="errorTxt1 float-right red-text"></small>
                                    </div>
                                    <div class="input-field col l2 m12 s12">
                                        <x-jet-input id="volume" class="materialize-input disabled" type="text" value="{{ old('volume.'.$i) }}" placeholder="Volum" name="volume[]" disabled />
                                        <span class="suffix">m<sup>3</sup></span>
                                        <small class="errorTxt1 float-right red-text"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="input-field col s12 mt-0 mb-0 show-colet">
                <div class="card m-0">
                    <div class="card-content pt-2 pb-2 m-0">
                        <p class="inline-block">
                            Total: 
                            <i class="fas fa-balance-scale"></i>
                            <span class="total-weight" data-name="total_weight">0</span> kg
                            &nbsp;
                            <i class="fas fa-cubes"></i>
                            <span class="total-volume" data-name="total_volume">0</span> m<sup>3</sup>
                        </p>
                    </div>
                </div>
            </div>
            <div class="input-field col s12">
                <div class="switch -ml-3 my-2">
                    <label>
                        <input id="favorite" value="1" class="{{ $errors->has('favorite') ? 'invalid' : '' }}" name="favorite" {{ old('favorite') ? 'checked' : (isset($this->state['favorite']) && $this->state['favorite'] ? 'checked' : '') }} type="checkbox">
                        <span class="lever"></span>
                        <span style="font-size: 1rem;">{{ __('Sablon favorit') }}</span>
                    </label>
                </div>
                <x-jet-input-error for="favorite" class="mt-2 errorTxt1" />
            </div>
            {{-- <div class="input-field col s12 card-alert">
                <i class="material-icons" style="font-size: 2rem;">info_outline</i> Vezi conditiile de impachetarea continutului.
            </div> --}}
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button class="bg-blue-500 mr-2 shown-form btn-form-cancel" role="button" onclick="event.preventDefault();" data-route="{{ route('dashboard.templates.update') }}" wire:loading.attr="disabled">
            {{ __('Cancel') }}
        </x-jet-button>

        <x-jet-button class="mr-2 hidden-form btn-form-add" role="button" onclick="event.preventDefault();" data-route="{{ route('dashboard.templates.update') }}" wire:loading.attr="disabled">
            {{ __('Add') }}
        </x-jet-button>

        <x-jet-button class="shown-form" wire:loading.attr="disabled">
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
<script src="{{ asset('js/pages/form-templates.js') }}"></script>
@if(session()->has('success'))
    <script type="text/javascript">
        M.toast({html: '{{ session()->get('success') }}.', classes: 'green accent-4', displayLength: 5000});
        $('#toast-container').css({
            top: '17%',
            right: '6%',
        });
    </script>
@endif
@endpush
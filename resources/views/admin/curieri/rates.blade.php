@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style type="text/css">
    /*.cke_reset {
        box-shadow: none!important;
    }*/
</style>
@endpush
@push("scripts")
{{-- <script src="{{ asset('ckeditor/ckeditor.js') }}"></script> --}}
{{-- <script src="{{ asset('js/admin/plugins/dropzone/min/dropzone.min.js') }}"></script> --}}
<script type="text/javascript">
    // CKEDITOR.replace('more_information', {
    //     extraPlugins: 'autogrow',
    //     autoGrow_onStartup: true,
    //     width: '100%',
    // });
    // ClassicEditor.create( document.querySelector('.editor') ).catch( error => {console.error( error );} );

    function cloneEl(wrapper, group, addClass, removeClass) {
        const wrapperEl = (typeof wrapper === "string") ? document.querySelector(wrapper) : wrapper;
        const groupContainer = wrapper.querySelector('.group-container');
        const groupEl = wrapper.querySelector(group);
        let count = wrapperEl.dataset.count;
        wrapperEl.querySelector(addClass).addEventListener("click", function() {
            let clone = groupEl.cloneNode(true);
            let inputs = clone.querySelectorAll('input');
            for(let i = 0 ; i < inputs.length ; i++) {
                inputs[i].value = ''; 
            }
            groupContainer.appendChild(clone);
        });
        wrapperEl.addEventListener("click", function(e) {
            if(e.target && (e.target.classList.contains(removeClass.substring(1)) || e.target.closest(removeClass))) {
                e.target.closest(group).remove();
                count--;
                wrapperEl.dataset.count = count;
            }
        });
        if(initialGroup = wrapperEl.querySelector('.remove-after')) {
            initialGroup.remove();
        }
    }
    // cloneEl('#discounts', '.discount-group', '.add-discount', '.remove-discount');
    const items = document.querySelectorAll('.prices');
    for (let i = 0, len = items.length; i < len; i++) {
        //work with checkboxes[i]
        cloneEl(items[i], '.prices-group', '.add-price', '.remove-price');
    }
    // items.forEach(item => {
    //     console.log(item);
    //     cloneEl(item, '.prices-group', '.add-price', '.remove-price');
    // });
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Tarife UE curier') }}</x-slot>
        <x-jet-authentication-card class="px-2 form-admin" classForm="md:max-w-5xl">

            <x-jet-validation-messages class="mb-4" />
            <x-jet-validation-errors class="mb-4" />
            <h3 class="font-bold mb-4">
                {{ __('Informatii variabile nationale') }}<br>
                <small>{{ __('Folosite pentru calcularea pretului venit de la curier pentru livrarea intr-o tara UE') }}</small>
            </h3>
            <form method="POST" 
                action="{{ route('admin.curieri.update.rates', [$curier->id, isset($user) ? $user->id : null]) }}" 
                enctype="multipart/form-data" class="grid grid-cols-12 gap-5">
                @csrf
                @foreach($countries as $country)
                    <div class="col-span-12 md:col-span-6">
                        <h4 class="font-bold">
                            {{ $country->name }}
                            <x-jet-checkbox class="ml-2" name="rates[]" value="{{ $country->id }}" 
                                onchange="let elWrapper = document.getElementById('rates-{{ $country->id }}'); 
                                    this.checked ? elWrapper.classList.remove('hidden') : elWrapper.classList.add('hidden')"
                                :checked="old('rates.'.$country->id, isset($countryPrices[$country->id]) ?? null)" 
                                />
                        </h4>
                        <div id="rates-{{ $country->id }}" class="grid grid-cols-12 gap-5 mt-4 {{ isset($countryPrices[$country->id]) ? '' : 'hidden' }}">
                            <div class="col-span-12 md:col-span-6">
                                <x-jet-label for="volum_price_{{ $country->id }}" value="{{ __('Pret per kg (volumetric sau normal) colet') }}" />
                                <x-jet-input id="volum_price_{{ $country->id }}" wrapClasses="block mt-1 w-full" p="14" step="0.01" type="number" 
                                    value="{{ old('countries.'.$country->id.'.volum_price', isset($countryPrices[$country->id]) ? $countryPrices[$country->id]->volum_price ?? '' : '') }}" 
                                    step="0.01" min="0" name="countries[{{ $country->id }}][volum_price]">
                                    RON
                                </x-jet-input>
                            </div>

                            @if($curier->external_orders == 2)
                                <div class="col-span-12 md:col-span-6">
                                    <x-jet-label for="transa_ramburs_{{ $country->id }}" value="{{ __('Suma transa ramburs (moneda in care se face rambursul)') }}" />
                                    <x-jet-input id="transa_ramburs_{{ $country->id }}" class="block mt-1 w-full" type="number" value="{{ old('countries.'.$country->id.'.transa_ramburs', isset($countryPrices[$country->id]) ? $countryPrices[$country->id]->transa_ramburs ?? '' : '') }}" min="1" step="0.01" name="countries[{{ $country->id }}][transa_ramburs]" />
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <x-jet-label for="value_ramburs_{{ $country->id }}" value="{{ __('Adaos fix la livrarea cu ramburs pana la transa') }}" />
                                    <x-jet-input id="value_ramburs_{{ $country->id }}" wrapClasses="block mt-1 w-full" type="number" value="{{ old('countries.'.$country->id.'.value_ramburs', isset($countryPrices[$country->id]) ? $countryPrices[$country->id]->value_ramburs ?? '' : '') }}" p="14" min="1" step="0.01" name="countries[{{ $country->id }}][value_ramburs]">
                                        RON
                                    </x-jet-input>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <x-jet-label for="percent_ramburs_{{ $country->id }}" value="{{ __('Adaos procentual la livrarea cu ramburs dupa transa') }}" />
                                    <x-jet-input id="percent_ramburs_{{ $country->id }}" wrapClasses="block mt-1 w-full" type="number" value="{{ old('countries.'.$country->id.'.percent_ramburs', isset($countryPrices[$country->id]) ? $countryPrices[$country->id]->percent_ramburs ?? '' : '') }}" min="1" max="100" step="0.01" name="countries[{{ $country->id }}][percent_ramburs]">
                                        <i class="fas fa-percent"></i>
                                    </x-jet-input>
                                </div>
                            @endif

                            <div class="col-span-12">
                                <x-jet-label value="{{ __('Preturi minime per kg') }}" />
                                <div class="grid gap-3 grid-cols-12 text-xs">
                                    <p id="kg" class="block mt-1 col-span-3">{{ __('Nr. kg') }}</p>
                                    <p id="price" class="block mt-1 col-span-8">{{ __('Pret minim') }}</p>
                                </div>
                                <div class="w-full prices">
                                    <div class="w-full group-container">
                                        @php 
                                            $oldPrices = old('countries.'.$country->id.'.price', $rates[$country->id]['price'] ?? null); 
                                            $oldWeight = old('countries.'.$country->id.'.kg', $rates[$country->id]['weight'] ?? null); 
                                        @endphp
                                        @if(isset($oldPrices))
                                            @for($i = 0 ; $i < count($oldPrices) ; $i++)
                                                <div class="grid gap-3 grid-cols-12 prices-group">
                                                    <x-jet-input class="block mt-1 col-span-3" type="number" value="{{ $oldWeight[$i] ?? '1' }}" 
                                                        min="1" max="100" name="countries[{{ $country->id }}][kg][]" />
                                                    <x-jet-input wrapClasses="block mt-1 col-span-7" type="number" p="14" value="{{ $oldPrices[$i] ?? '1' }}" 
                                                        step="0.01" min="1" max="100000" name="countries[{{ $country->id }}][price][]">RON
                                                    </x-jet-input>
                                                    <x-jet-button type="button" class="mt-1 col-span-2 remove-price">
                                                        <i class="fa fa-minus w-full"></i>
                                                    </x-jet-button>
                                                </div>
                                            @endfor
                                        @else
                                            <div class="grid gap-3 grid-cols-12 prices-group remove-after">
                                                <x-jet-input class="block mt-1 col-span-3" type="number" value="1" min="1" max="100" name="countries[{{ $country->id }}][kg][]" />
                                                <x-jet-input wrapClasses="block mt-1 col-span-7" type="number" p="14" value="1" step="0.01" min="1" max="100000" name="countries[{{ $country->id }}][price][]">
                                                    RON
                                                </x-jet-input>
                                                <x-jet-button type="button" class="mt-1 col-span-2 remove-price">
                                                    <i class="fa fa-minus w-full" tabindex="-1"></i>
                                                </x-jet-button>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="w-full">
                                        <x-jet-button type="button" class="my-2 add-price">
                                            <i class="fa fa-plus"></i>
                                            <span>{{ __('Adauga pret') }}</span>
                                        </x-jet-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="flex items-center justify-end col-span-12 mt-4">
                    <x-jet-button class="ml-4" id="submit">{{ __('Actualizare') }}</x-jet-button>
                </div>
            </form>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
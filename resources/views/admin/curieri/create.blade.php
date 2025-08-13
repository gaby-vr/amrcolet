@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style type="text/css">
    .cke_reset {
        box-shadow: none!important;
    }
</style>
@endpush
@push("scripts")
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/admin/plugins/dropzone/min/dropzone.min.js') }}"></script>
<script type="text/javascript">
    CKEDITOR.replace('more_information', {
        extraPlugins: 'autogrow',
        autoGrow_onStartup: true,
        width: '100%',
    });
    // ClassicEditor.create( document.querySelector('.editor') ).catch( error => {console.error( error );} );

    function cloneEl(wrapper, group, addClass, removeClass) {
        const wrapperEl = document.querySelector(wrapper);
        var groupEl = document.querySelector(group);
        var count = wrapperEl.dataset.count;
        document.querySelector(addClass).addEventListener("click", function() {
            var clone = groupEl.cloneNode(true);
            var inputs = clone.querySelectorAll('input');
            for(var i = 0 ; i < inputs.length ; i++) {
                inputs[i].value = ''; 
            }
            wrapperEl.appendChild(clone);
        });
        wrapperEl.addEventListener("click", function(e) {
            if(e.target && (e.target.classList.contains(removeClass.substring(1)) || e.target.closest(removeClass))) {
                e.target.closest(group).remove();
                count--;
                wrapperEl.dataset.count = count;
            }
        });
        if(initialGroup = wrapperEl.querySelector('.'+wrapper.substring(1)+'-remove-after')) {
            initialGroup.remove();
        }
    }
    cloneEl('#discounts', '.discount-group', '.add-discount', '.remove-discount');
    cloneEl('#prices', '.prices-group', '.add-price', '.remove-price');
</script>
<script type="text/javascript">
    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    Dropzone.autoDiscover = false;
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var logo = document.querySelector(".fallback");

    var myDropzone = new Dropzone(logo, { // Make the whole body a dropzone
        url: "{{ route('admin.curieri.store.logo', ['id' => isset($curier) ? $curier->id : null]) }}", // Set the url
        //thumbnailWidth: 80,
        //thumbnailHeight: 80,
        //parallelUploads: 20,
        paramName: "logo",
        hiddenInputContainer: "#previews",
        previewTemplate: previewTemplate,
        autoQueue: false, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        headers: {
           'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });
    myDropzone.on("addedfile", function(file) {
        // Hookup the start button
        document.querySelector("#actions").style.display = 'none';
        document.querySelector("#previews .show-action-btn").onclick = function() {
            document.querySelector("#actions").style.display = 'block';
        };
        var preview = document.querySelector("#previews");
        var rows = preview.querySelectorAll(".file-row.row");
        var numRows = preview.querySelectorAll(".file-row.row").length;
        var file = preview.getElementsByTagName("input")[0];
        var node = file.cloneNode(true);
        node.setAttribute('name', 'logo');
        rows[numRows - 1].appendChild(node);
    });
    document.querySelector("#actions .cancel").onclick = function() {
        document.querySelector("#actions").style.display = 'block';
        myDropzone.removeAllFiles(true);
    };
    document.querySelector("#submit").onsubmit = function(e) {
        e.preventDefault();
        myDropzone.processQueue();
    };
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ isset($curier) ? __('Editare curier') : __('Creare curier') }}</x-slot>
        <x-slot name="href">{{ route('admin.curieri.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza curier') }}</x-slot>
        <x-jet-authentication-card class="px-2 form-admin" classForm="md:max-w-5xl">

            <x-jet-validation-messages class="mb-4" />
            <x-jet-validation-errors class="mb-4" />

            <form method="POST" 
                action="{{ isset($curier) ? route('admin.curieri.update', $curier->id) : route('admin.curieri.store') }}" 
                enctype="multipart/form-data" class="grid grid-cols-12 gap-5">
                @csrf

                <div class="col-span-12 md:col-span-6">
                    <h3 class="font-bold">
                        {{ __('Informatii statice') }}<br>
                        <small>{{ __('Folosite pentru descrierea curierului in platforma') }}</small>
                    </h3>

                    <div class="mt-4">
                        <x-jet-label for="logo" value="{{ __('Sigla') }}" />
                        {{-- <x-jet-input id="logo" class="block mt-1 w-full" type="file" name="logo" :value="old('logo') ?? isset($curier) ? $curier->logo : ''" required /> --}}
                        <div class="col-md-6 fallback">
                            <div id="images_selector" class="@error('logo') invalid @enderror">
                                <div id="actions">
                                    <x-jet-button type="button" class="btn btn-success fileinput-button dz-clickable m-1">
                                        <i class="fa fa-plus"></i>
                                        <span>{{ __('Adauga logo') }}</span>
                                    </x-jet-button>
                                    <x-jet-button type="button" type="reset" class="btn btn-warning cancel m-1">
                                        <i class="fa fa-ban"></i>
                                        <span>{{ __('Anuleaza') }}</span>
                                    </x-jet-button>
                                </div>
                                <div class="" class="files" id="previews">

                                  <div id="template" class="grid grid-cols-12 file-row row items-center">
                                    <div class="col-span-4 text-center">
                                        <span class="preview"><img class="inline-block" style="width: 100%;height: auto;" data-dz-thumbnail /></span>
                                    </div>
                                    <div class="col-span-4 text-center">
                                        <p class="name" data-dz-name></p><br>
                                        <p class="size" data-dz-size></p>
                                        <strong class="error text-danger" data-dz-errormessage></strong>
                                    </div>
                                    <div class="col-span-4 text-center">
                                        <x-jet-button data-dz-remove class="btn btn-warning cancel show-action-btn">
                                            <i class="fa fa-ban"></i>
                                            <span>{{ __('Cancel') }}</span>
                                        </x-jet-button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @isset($curier)
                        <div class="mt-4">
                            <x-jet-label value="{{ __('Sigla actuala') }}" />
                            <img class="max-w-sm" src="{{ asset('img/curieri/'.$curier->logo) }}">
                            <input type="hidden" name="old_logo" value="1">
                        </div>
                    @endisset

                    <div class="mt-4">
                        <x-jet-label for="name" value="{{ __('Nume') }}" />
                        <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name') ?? $curier->name ?? ''" required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="api_curier" value="{{ __('API') }}" />
                        <div class="grid gap-3 grid-cols-1 md:grid-cols-3 mt-2">
                            <x-jet-label class="flex items-center">
                                <x-jet-radio name="api_curier" value="1" :checked="old('api_curier', $curier->api_curier ?? null) == '1'" required />
                                <div class="ml-2">Cargus</div>
                            </x-jet-label>
                            <x-jet-label class="flex items-center">
                                <x-jet-radio name="api_curier" value="2" :checked="old('api_curier', $curier->api_curier ?? null) == '2'" required />
                                <div class="ml-2">DPD</div>
                            </x-jet-label>
                            <x-jet-label class="flex items-center">
                                <x-jet-radio name="api_curier" value="3" :checked="old('api_curier', $curier->api_curier ?? null) == '3'" required />
                                <div class="ml-2">GLS</div>
                            </x-jet-label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="type" value="{{ __('Tip') }}" />
                        <div class="grid gap-3 grid-cols-1 md:grid-cols-3 mt-2">
                            <x-jet-label class="flex items-center">
                                <x-jet-radio name="type" value="1" :checked="old('type', $curier->type ?? null) == '1'" required />
                                <div class="ml-2">Public</div>
                            </x-jet-label>
                            <x-jet-label class="flex items-center">
                                <x-jet-radio name="type" value="2" :checked="old('type', $curier->type ?? null) == '2'" required />
                                <div class="ml-2">Privat</div>
                            </x-jet-label>
                            <x-jet-label class="flex items-center">
                                <x-jet-radio name="type" value="3" :checked="old('type', $curier->type ?? null) == '3'" required />
                                <div class="ml-2">Inactiv</div>
                            </x-jet-label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="tva" value="{{ __('TVA') }}" />
                        <x-jet-input id="tva" class="block mt-1 w-full" type="number" value="{{ old('tva') ?? $curier->tva ?? '0' }}" min="0" max="100" name="tva" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="max_package_weight" value="{{ __('Greutate maxima per colet') }}" />
                        <x-jet-input id="max_package_weight" class="block mt-1 w-full" type="number" value="{{ old('max_package_weight') ?? $curier->max_package_weight ?? '0' }}" min="0" name="max_package_weight" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="max_total_weight" value="{{ __('Greutate maxima comanda') }}" />
                        <x-jet-input id="max_total_weight" class="block mt-1 w-full" type="number" value="{{ old('max_total_weight') ?? $curier->max_total_weight ?? '0' }}" min="0" name="max_total_weight" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="performance_pickup" value="{{ __('Performanta ridicare') }}" />
                        <x-jet-input id="performance_pickup" class="block mt-1 w-full" type="number" value="{{ old('performance_pickup') ?? $curier->performance_pickup ?? '1' }}" min="1" max="5" step="0.01" name="performance_pickup" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="performance_delivery" value="{{ __('Performanta livrare') }}" />
                        <x-jet-input id="performance_delivery" class="block mt-1 w-full" type="number" value="{{ old('performance_delivery') ?? $curier->performance_delivery ?? '1' }}" min="1" max="5" step="0.01" name="performance_delivery" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="max_order_days" value="{{ __('Numarul maxim de zile pana la primirea comenzi') }}" />
                        <x-jet-input id="max_order_days" class="block mt-1 w-full" type="number" value="{{ old('max_order_days') ?? $curier->max_order_days ?? '1' }}" min="1" name="max_order_days" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label value="{{ __('Ora ultimei comenzi') }}" />
                        <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="last_order_hour" name="last_order_hour" >
                            @for($i = 8 ; $i <= 17 ; $i++)
                                <option value="{{ $i }}" {{ old('last_order_hour') == $i || (isset($curier->last_order_hour) && $curier->last_order_hour == $i) ? 'selected' : '' }}>{{ $i }}:00</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-jet-label value="{{ __('Ora ultimei ridicari') }}" />
                        <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="last_pick_up_hour" name="last_pick_up_hour" >
                            @for($i = 8 ; $i <= 18 ; $i++)
                                <option value="{{ $i }}" {{ old('last_pick_up_hour') == $i || (isset($curier->last_pick_up_hour) && $curier->last_pick_up_hour == $i) ? 'selected' : '' }}>{{ $i }}:00</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-jet-label value="{{ __('Sediu') }}" />
                        <textarea class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="office" name="office">{{ old('office') ?? $curier->office ?? '' }}</textarea>
                    </div>

                    <div class="mt-4">
                        <x-jet-label value="{{ __('Informatii') }}" />
                        <textarea class="editor border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="more_information" name="more_information">{!! old('more_information') ?? $curier->more_information ?? '' !!}</textarea>
                    </div>       
                </div>

                <div class="col-span-12 md:col-span-6 mt-4 md:mt-0">
                    <h3 class="font-bold">
                        {{ __('Informatii variabile') }}<br>
                        <small>{{ __('Folosite pentru calcularea pretului venit de la curier') }}</small>
                    </h3>
                    <div class="mt-4">
                        <x-jet-label for="volum_price" value="{{ __('Pret per kg (volumetric sau normal) colet') }}" />
                        <x-jet-input id="volum_price" wrapClasses="block mt-1 w-full" p="14" step="0.01" type="number" 
                            value="{{ old('volum_price') ?? (isset($curier) ? $curier->getAttributes()['volum_price'] : '') }}" 
                            step="0.01" min="0" name="volum_price">
                            RON
                        </x-jet-input>
                    </div>

                    <div class="mt-4">
                        <x-jet-label value="{{ __('Preturi minime per kg') }}" />
                        <div class="grid gap-3 grid-cols-12 text-xs">
                            <p id="kg" class="block mt-1 col-span-3">Nr. kg</p>
                            <p id="price" class="block mt-1 col-span-8">Pret minim</p>
                        </div>
                        <div id="prices" class="w-full">
                            @if(isset($prices['kg']) && (count($prices['kg']) > 0))
                                @for($i = 0 ; $i < count($prices['kg']) ; $i++)
                                    <div class="grid gap-3 grid-cols-12 prices-group">
                                        <x-jet-input class="block mt-1 col-span-3" type="number" value="{{ $prices['kg'][$i] ?? '1' }}" min="1" max="100" name="prices[kg][]" />
                                        <x-jet-input wrapClasses="block mt-1 col-span-7" type="number" p="14" value="{{ $prices['price'][$i] ?? '1' }}" step="0.01" min="1" max="100000" name="prices[price][]">
                                            RON
                                        </x-jet-input>
                                        <x-jet-button type="button" class="mt-1 col-span-2 remove-price">
                                            <i class="fa fa-minus w-full"></i>
                                        </x-jet-button>
                                    </div>
                                @endfor
                            @else
                                <div class="grid gap-3 grid-cols-12 prices-group prices-remove-after">
                                    <x-jet-input class="block mt-1 col-span-3" type="number" value="1" min="1" max="100" name="prices[kg][]" />
                                    <x-jet-input wrapClasses="col-span-7" type="number" p="14" value="1" step="0.01" min="1" max="100000" name="prices[price][]">
                                        RON
                                    </x-jet-input>
                                    <x-jet-button type="button" class="mt-1 col-span-2 remove-price">
                                        <i class="fa fa-minus w-full" tabindex="-1"></i>
                                    </x-jet-button>
                                </div>
                            @endif
                        </div>
                        <div class="w-full">
                            <x-jet-button type="button" class="m-1 add-price">
                                <i class="fa fa-plus"></i>
                                <span>{{ __('Adauga pret') }}</span>
                            </x-jet-button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-jet-label value="{{ __('Oferte') }}" />
                        <div class="grid gap-3 grid-cols-12 text-xs">
                            <p id="nr_colete" class="block mt-1 col-span-5">Nr. colete</p>
                            <p id="offers" class="block mt-1 col-span-7">Oferta</p>
                        </div>
                        <div id="discounts" class="w-full">
                            @if(old('nr_colete') != null || isset($discounts) && (count($discounts) > 0))
                                @php $discounts = old('nr_colete') ? old('nr_colete') : $discounts; @endphp
                                @for($i = 0 ; $i < count($discounts) ; $i++)
                                    <div class="grid gap-3 grid-cols-12 discount-group">
                                        <x-jet-input id="nr_colete" class="block mt-1 col-span-5" type="number" value="{{ old('nr_colete.'.$i) ?? $discounts[$i]['nr_colete'] ?? '1' }}" min="1" max="100" name="nr_colete[]" />
                                        <x-jet-input id="discount" class="block mt-1 col-span-5" type="number" value="{{ old('discounts.'.$i) ?? $discounts[$i]['discount'] ?? '0' }}" step="0.01" min="0" max="100" name="discounts[]" />
                                        <x-jet-button type="button" class="mt-1 col-span-2 remove-discount">
                                            <i class="fa fa-minus w-full"></i>
                                        </x-jet-button>
                                    </div>
                                @endfor
                            @else
                                <div class="grid gap-3 grid-cols-12 discount-group">
                                    <x-jet-input id="nr_colete" class="block mt-1 col-span-5" type="number" value="1" min="1" max="100" name="nr_colete[]" />
                                    <x-jet-input id="discount" class="block mt-1 col-span-5" type="number" value="0" min="0" max="100" name="discounts[]" />
                                    <x-jet-button type="button" class="mt-1 col-span-2 remove-discount">
                                        <i class="fa fa-minus w-full" tabindex="-1"></i>
                                    </x-jet-button>
                                </div>
                            @endif
                        </div>
                        <div class="w-full">
                            <x-jet-button type="button" class="m-1 add-discount">
                                <i class="fa fa-plus"></i>
                                <span>{{ __('Adauga oferta') }}</span>
                            </x-jet-button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-jet-label value="{{ __('Optiuni') }}" />
                        <x-jet-label class="col-span-12 lg:col-span-6">
                            <div class="flex items-center">
                                <x-jet-checkbox value="1" name="options[work_saturday]" :checked="old('options.work_saturday') == '1' || isset($curier) && $curier->work_saturday == '1' ? 'checked' : '' " id="work_saturday"/>

                                <div class="ml-2">
                                    {!! __('Livrare sambata') !!}
                                </div>
                            </div>
                        </x-jet-label>
                        <x-jet-label class="col-span-12 lg:col-span-6">
                            <div class="flex items-center">
                                <x-jet-checkbox value="1" name="options[require_awb]" :checked="old('options.require_awb') == '1' || isset($curier) && $curier->require_awb == '1' ? 'checked' : '' " id="require_awb"/>

                                <div class="ml-2">
                                    {!! __('Imprimarea obligatorie a etichetei de transport pe colet') !!}
                                </div>
                            </div>
                        </x-jet-label>
                        <x-jet-label class="col-span-12 lg:col-span-6">
                            <div class="flex items-center">
                                <x-jet-checkbox value="1" name="options[open_when_received]" :checked="old('options.open_when_received') == '1' || isset($curier) && $curier->open_when_received == '1' ? 'checked' : '' " id="open_when_received"/>

                                <div class="ml-2">
                                    {!! __('Deschidere la livrare') !!}
                                </div>
                            </div>
                        </x-jet-label>
                        <x-jet-label class="col-span-12 lg:col-span-6">
                            <div class="flex items-center">
                                <x-jet-checkbox value="1" name="options[retur_document]" :checked="old('options.retur_document') == '1' || isset($curier) && $curier->retur_document == '1' ? 'checked' : '' " id="retur_document"/>

                                <div class="ml-2">
                                    {!! __('Returnare document/pachet') !!}
                                </div>
                            </div>
                        </x-jet-label>
                        <x-jet-label class="col-span-12 lg:col-span-6">
                            <div class="flex items-center">
                                <x-jet-checkbox value="1" name="options[ramburs_cash]" :checked="old('options.ramburs_cash') == '1' || isset($curier) && $curier->ramburs_cash == '1' ? 'checked' : '' " id="ramburs_cash"/>

                                <div class="ml-2">
                                    {!! __('Ramburs cash') !!}
                                </div>
                            </div>
                        </x-jet-label>
                        <x-jet-label class="col-span-12 lg:col-span-6">
                            <div class="flex items-center">
                                <x-jet-checkbox value="1" name="options[ramburs_cont]" :checked="old('options.ramburs_cont') == '1' || isset($curier) && $curier->ramburs_cont == '1' ? 'checked' : '' " id="ramburs_cont"/>

                                <div class="ml-2">
                                    {!! __('Ramburs in cont') !!}
                                </div>
                            </div>
                        </x-jet-label>
                        <x-jet-label class="col-span-12 lg:col-span-6">
                            <div class="flex items-center">
                                <x-jet-checkbox value="1" name="options[assurance]" :checked="old('options.assurance') == '1' || isset($curier) && $curier->assurance == '1' ? 'checked' : '' " id="assurance"/>

                                <div class="ml-2">
                                    {!! __('Ofera asigurare') !!}
                                </div>
                            </div>
                        </x-jet-label>
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="percent_assurance" value="{{ __('Procent adaos din asigurare') }}" />
                        <x-jet-input id="percent_assurance" wrapClasses="block mt-1 w-full" type="number" value="{{ old('special.percent_assurance', isset($curier) ? $curier->getAttributes()['percent_assurance'] ?? '' : '') }}" min="1" max="100" step="0.01" name="special[percent_assurance]">
                            <i class="fas fa-percent"></i>
                        </x-jet-input>
                    </div>

                    <div class="mt-4">
                        {{-- value open when received --}}
                        <x-jet-label for="value_owr" value="{{ __('Adaos la optiunea de deschidere pachet la livrare') }}" />
                        <x-jet-input id="value_owr" wrapClasses="block mt-1 w-full" type="number" value="{{ old('special.value_owr', isset($curier) ? $curier->getAttributes()['value_owr'] ?? '' : '') }}" p="14" min="1" step="0.01" name="special[value_owr]">
                            RON
                        </x-jet-input>
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="percent_ramburs" value="{{ __('Adaos procentual la livrarea cu ramburs') }}" />
                        <x-jet-input id="percent_ramburs" wrapClasses="block mt-1 w-full" type="number" value="{{ old('special.percent_ramburs', isset($curier) ? $curier->getAttributes()['percent_ramburs'] ?? '' : '') }}" min="1" max="100" step="0.01" name="special[percent_ramburs]">
                            <i class="fas fa-percent"></i>
                        </x-jet-input>
                    </div>
                    <div class="mt-4">
                        {{-- value open when received --}}
                        <x-jet-label for="value_ramburs" value="{{ __('Adaos fix la livrarea cu ramburs') }}" />
                        <x-jet-input id="value_ramburs" wrapClasses="block mt-1 w-full" type="number" value="{{ old('special.value_ramburs', isset($curier) ? $curier->getAttributes()['value_ramburs'] ?? '' : '') }}" p="14" min="1" step="0.01" name="special[value_ramburs]">
                            RON
                        </x-jet-input>
                    </div>
                </div>
                <div class="flex items-center justify-end col-span-12 mt-4">
                    <x-jet-button class="ml-4" id="submit">
                        {{ isset($curier) ?  __('Edit') : __('Create') }}
                    </x-jet-button>
                </div>
            </form>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
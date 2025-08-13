@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('css/admin/plugins/flatpickr/flatpickr.min.css') }}"></link>
@endpush

@push("scripts")
<script src="{{ asset('js/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.ro.js') }}"></script>
<script src="{{ asset('js/admin/initAndShow.js') }}"></script>
<script type="text/javascript">
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
    @foreach($curieri as $curier)
    cloneEl('#prices-{{ $curier->id }}', '.prices-{{ $curier->id }}-group', '.add-price-{{ $curier->id }}', '.remove-price');
    @endforeach
    cloneEl('#frequency_dates', '.frequency-date-group', '.add-frequency-date', '.remove-frequency-date');
    $(document).ready( function() {
        initializeDatePicker('.datepicker');
        showElementsOnEvent('#role','.show-contractant',null,'change','==','2');
        showElementsOnEvent('#frequency_type','.borderou-frequency.frequency-1','.borderou-frequency.frequency-2','change','==','1');
    });
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ isset($user) ? __('Editare utilizatori') : __('Creare utilizatori') }}</x-slot>
        <x-slot name="href">{{ route('admin.users.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza utilizator') }}</x-slot>
        <x-jet-authentication-card class="px-2 form-admin" classForm="md:max-w-5xl">

            <x-jet-validation-messages class="mb-4" />
            <x-jet-validation-errors class="mb-4" />

            <form method="POST" class="grid grid-cols-12 gap-5" 
                action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
                @csrf

                <div class="col-span-12 md:col-span-6">
                    <h3 class="font-bold">
                        {{ __('Informatii personale') }}<br>
                        <small>{{ __('Folosite pentru a accesa platforma') }}</small>
                    </h3>
                    <div class="mt-4">
                        <x-jet-label for="name" value="{{ __('Name') }}" />
                        <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name') ?? $user->name ?? '' " required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="email" value="{{ __('Email') }}" />
                        <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email') ?? $user->email ?? '' " required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="role" value="{{ __('Tip') }}" />
                        <select id="role" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" name="role">
                            <option value="1" @selected(old('role', $user->role ?? '') == '1')>{{ __('Normal') }}</option>
                            <option value="2" @selected(old('role', $user->role ?? '') == '2')>{{ __('Contractant') }}</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="account_balance" value="{{ __('Credite') }}" />
                        <x-jet-input id="account_balance" class="block mt-1 w-full" type="number" name="account_balance" step="any" :value="old('account_balance', $user->account_balance ?? 0)" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="bonus_credits" value="{{ __('Bonus') }}" />
                        <x-jet-input id="bonus_credits" class="block mt-1 w-full" type="number" step="1" min="0" step="any" name="bonus_credits" :value="old('bonus_credits', 0)" />
                        @if(isset($user) && $user->bonus_credits != '')
                            <x-jet-label for="bonus_credits" class="mt-1" > <b>{{ __('Credite bonus adaugate:') }} {{ $user->bonus_credits }}</b> </x-jet-label>
                        @endif
                    </div>

                    <div class="mt-4 show-contractant">
                        <x-jet-label for="days_of_negative_balance" value="{{ __('Zile cu balanta negativa') }}" />
                        <x-jet-input id="days_of_negative_balance" class="block mt-1 w-full" type="number" step="1" min="1" name="days_of_negative_balance" :value="old('days_of_negative_balance', $user->days_of_negative_balance ?? 7)" />
                    </div>

                    <div class="mt-4 show-contractant">
                        <x-jet-label for="expiration_date" value="{{ __('Data la care se blocheaza contul') }}" />
                        <x-jet-input id="expiration_date" class="block mt-1 w-full datepicker" type="text" name="expiration_date" :value="old('expiration_date', $user->expiration_date ?? '')" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="password" value="{{ __('Password') }}" />
                        <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                        <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                    </div>

                    @if($curieri->where('type', 2)->isNotEmpty())
                        <h3 class="font-bold mt-4">
                            {{ __('Curieri speciali') }}<br>
                            <small>{{ __('Utilizatorul poate vedea doar curierii selectati') }}</small>
                        </h3>

                        <div class="mt-4">
                            {{-- <x-jet-label for="api_curier" value="{{ __('API') }}" /> --}}
                            <div class="grid gap-3 grid-cols-1 mt-2">
                                @foreach($curieri->where('type', 2) as $index => $curier)
                                    <x-jet-label class="flex items-center">
                                        <x-jet-checkbox name="curieri[]" value="{{ $curier->id }}" 
                                            :checked="(old('curieri.'.$index) == $curier->id) || in_array($curier->id, $curieri_speciali ?? [])" />
                                        <div class="ml-2">{{ $curier->name }}</div>
                                    </x-jet-label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <h3 class="font-bold mt-4">
                        {{ __('Frecventa facturi') }}<br>
                        <small>{{ __('Folosit pentru generarea fiselor cu livrari a facturilor') }}</small>
                    </h3>
                    <div class="mt-4">
                        <x-jet-label for="sheet_frequency" value="{{ __('Frecventa fisa') }}" />
                        <select id="sheet_frequency" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" name="sheet_frequency">
                            <option value="1" @selected(old('sheet_frequency', $user->sheet_frequency ?? '') == '1')>{{ __('Lunara') }}</option>
                            <option value="2" @selected(old('sheet_frequency', $user->sheet_frequency ?? '') == '2')>{{ __('Bilunara') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-span-12 md:col-span-6">
                    <h3 class="font-bold">
                        {{ __('Informatii variabile') }}<br>
                        <small>{{ __('Folosite pentru calcularea pretului unei comenzi') }}</small>
                    </h3>
                    @foreach($curieri as $curier)
                        <div class="mt-4">
                            <x-jet-label>
                                {{ __('Preturi minime') }} {{ $curier->name }}
                                @if($curier->external_orders)
                                    <a href="{{ route('admin.curieri.edit.rates', [$curier->id, $user->id]) }}" class="ml-2 text-blue-600"><i class="fas fa-flag"></i></a>
                                @endif
                            </x-jet-label>
                            <div class="grid gap-3 grid-cols-12 text-xs">
                                <p id="kg" class="block mt-1 col-span-3">Nr. kg</p>
                                <p id="price" class="block mt-1 col-span-8">Pret minim</p>
                            </div>
                            <div id="prices-{{ $curier->id }}" class="w-full">
                                @if(isset($prices[$curier->id]['kg']) && (count($prices[$curier->id]['kg']) > 0))
                                    @for($i = 0 ; $i < count($prices[$curier->id]['kg']) ; $i++)
                                        <div class="grid gap-3 grid-cols-12 prices-{{ $curier->id }}-group">
                                            <x-jet-input class="block mt-1 col-span-3" type="number" value="{{ $prices[$curier->id]['kg'][$i] ?? '1' }}" min="1" max="100" name="prices[{{ $curier->id }}][kg][]" />

                                            <x-jet-input wrapClasses="mt-1 col-span-7" p="14" type="number" value="{{ $prices[$curier->id]['price'][$i] ?? '1' }}" step="0.01" min="1" max="100000" name="prices[{{ $curier->id }}][price][]">
                                                RON
                                            </x-jet-input>

                                            <x-jet-button type="button" class="mt-1 col-span-2 remove-price">
                                                <i class="fa fa-minus w-full"></i>
                                            </x-jet-button>
                                        </div>
                                    @endfor
                                @else
                                    <div class="grid gap-3 grid-cols-12 prices-{{ $curier->id }}-group prices-{{ $curier->id }}-remove-after">
                                        <x-jet-input class="block mt-1 col-span-3" type="number" value="1" min="1" max="100" name="prices[{{ $curier->id }}][kg][]" />
                                        <x-jet-input wrapClasses="mt-1 col-span-7" p="14" type="number" value="1" step="0.01" min="1" max="100000" name="prices[{{ $curier->id }}][price][]">
                                            RON
                                        </x-jet-input>
                                        <x-jet-button type="button" class="mt-1 col-span-2 remove-price">
                                            <i class="fa fa-minus w-full" tabindex="-1"></i>
                                        </x-jet-button>
                                    </div>
                                @endif
                            </div>
                            <div class="w-full">
                                <x-jet-button type="button" class="m-1 add-price-{{ $curier->id }}">
                                    <i class="fa fa-plus"></i>
                                    <span>{{ __('Adauga pret') }}</span>
                                </x-jet-button>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4">
                        <x-jet-label for="special_volum_price" value="{{ __('Pret special per kg (volumetric sau normal)') }}" />
                        <x-jet-input id="special_volum_price" wrapClasses="block mt-1 w-full" p="14" step="0.01" type="number" value="{{ old('special.volum_price', $user->volum_price ?? '') }}" step="0.01" min="0" name="special[volum_price]">
                            RON
                        </x-jet-input>
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="special_value_owr" value="{{ __('Adaos special la optiunea de deschidere pachet la livrare') }}" />
                        <x-jet-input id="special_value_owr" wrapClasses="block mt-1 w-full" p="14" step="0.01" type="number" value="{{ old('special.value_owr', $user->value_owr ?? '') }}" step="0.01" min="0" name="special[value_owr]">
                            RON
                        </x-jet-input>
                    </div>
                    <div class="mt-4">
                        <x-jet-label for="special_value_ramburs" value="{{ __('Adaos fix la livrarea cu ramburs') }}" />
                        <x-jet-input id="special_value_ramburs" wrapClasses="block mt-1 w-full" p="14" step="0.01" type="number" value="{{ old('special.value_ramburs', $user->value_ramburs ?? '') }}" step="0.01" min="0" name="special[value_ramburs]">
                            RON
                        </x-jet-input>
                    </div>
                    <h3 class="font-bold mt-4">
                        {{ __('Frecventa borderou') }}<br>
                        <small>{{ __('Folosit pentru generarea borderoului') }}</small>
                    </h3>
                    <div class="mt-4">
                        <x-jet-label for="frequency_type" value="{{ __('Tip frecventa') }}" />
                        <select id="frequency_type" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" name="frequency[type]">
                            <option value="1" @selected(old('frequency_type', $user->frequency_type ?? '') == '1')>{{ __('Variabila') }}</option>
                            <option value="2" @selected(old('frequency_type', $user->frequency_type ?? '') == '2')>{{ __('Data fixa') }}</option>
                        </select>
                    </div>
                    
                    <div class="mt-4 borderou-frequency frequency-1">
                        <x-jet-label for="frequency_time" value="{{ __('Frecventa variabila') }}" />
                        <div class="grid gap-3 grid-cols-12 frequency-group-1">
                            <x-jet-input class="block mt-1 w-full col-span-3" type="number" min="1" step="1" name="frequency[recurrence]" value="{{ old('frequency_recurrence', $user->frequency_recurrence ?? '1') }}" />
                            <x-jet-select id="frequency_time" name="frequency[time]"
                                class="mt-1 col-span-9">
                                <option value="days" @selected(old('frequency_time', $user->frequency_time ?? '') == 'days')>{{ __('zile') }}</option>
                                <option value="months" @selected(old('frequency_time', $user->frequency_time ?? '') == 'months')>{{ __('luni') }}</option>
                                <option value="years" @selected(old('frequency_time', $user->frequency_time ?? '') == 'years')>{{ __('ani') }}</option>
                            </x-jet-select>
                        </div>
                    </div>

                    <div class="mt-4 borderou-frequency frequency-2">
                        <x-jet-label for="frequency_dates" value="{{ __('Zile ale lunii') }}" />
                        <div id="frequency_dates">
                            @foreach($frequency_dates ?? [''] as $date)
                                <div class="grid gap-3 grid-cols-12 frequency-date-group">
                                    <x-jet-input class="block mt-1 w-full col-span-10" type="number" min="1" max="31" step="1" name="frequency[dates][]" value="{{ $date }}" />
                                    <x-jet-button type="button" class="mt-1 col-span-2 remove-frequency-date">
                                        <i class="fa fa-minus w-full" tabindex="-1"></i>
                                    </x-jet-button>
                                </div>
                            @endforeach
                        </div>
                        <div class="w-full">
                            <x-jet-button type="button" class="my-2 add-frequency-date">
                                <i class="fa fa-plus"></i>
                                <span>{{ __('Adauga data') }}</span>
                            </x-jet-button>
                        </div>
                    </div>
                </div>


                <div class="flex items-center justify-end col-span-12">
                    <x-jet-button class="ml-4">
                        {{ isset($user) ?  __('Edit') : __('Create') }}
                    </x-jet-button>
                </div>
            </form>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
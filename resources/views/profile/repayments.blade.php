<x-jet-table>
    <div class="row col-span-12">
        <p class="col-span-12 mt-2">{{ __('Cauta facturi platite intre') }}:</p>
        <form action="{{ route('dashboard.repayments.show') }}" method="get">
            <div class="-mx-2 mt-1 col-span-12">
                <div class="input-field col s12 m3">
                    <x-jet-label value="{{ __('Data inceput') }}" />
                    <x-jet-input type="text" class="datepicker" value="{{ $condtitions['from'] ?? '' }}" placeholder="" id="from" name="from" />
                </div>
                <div class="input-field col s12 m3">
                    <x-jet-label value="{{ __('Data sfarsit') }}" />
                    <x-jet-input type="text" class="datepicker" value="{{ $condtitions['to'] ?? '' }}" placeholder="" id="to" name="to" />
                </div>
                <div class="input-field col s12 m4">
                    <select class="browser-default" id="repayment_status" name="repayment_status" >
                        <option value="" {{ !isset($condtitions['repayment_status']) ? 'selected' : '' }}>{{ __('Toate') }}</option>
                        <option value="1" {{ isset($condtitions['repayment_status']) && $condtitions['repayment_status'] == '1' ? 'selected' : '' }}>{{ __('Platite') }}</option>
                        {{-- <option value="0" {{ isset($condtitions['repayment_status']) && $condtitions['repayment_status'] == '0' ? 'selected' : '' }}>{{ __('Neplatite') }}</option> --}}
                        <option value="2" {{ isset($condtitions['repayment_status']) && $condtitions['repayment_status'] == '2' ? 'selected' : '' }}>{{ __('Nelivrate') }}</option>
                        <option value="3" {{ isset($condtitions['repayment_status']) && $condtitions['repayment_status'] == '3' ? 'selected' : '' }}>{{ __('La curier') }}</option>
                        <option value="4" {{ isset($condtitions['repayment_status']) && $condtitions['repayment_status'] == '4' ? 'selected' : '' }}>{{ __('La') }} {{ config('app.name') }}</option>
                    </select>
                    <x-jet-label for="repayment_status" class="active" style="line-height: 1.15rem" value="{{ __('Status') }}" />
                </div>
                <div class="input-field col s12 m2">
                    <div class="input-field col s12">
                        <button type="submit" class="btn blue darken-2 waves-effect waves-light">{{ __('Cauta') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row m-0 col-span-1 wallets grid gap-2 grid-cols-1 md:grid-cols-4 lg:grid-cols-3">
        <div class="w-full card m-0 flex flex-column justify-between">
            <div class="px-2">
                <h5><i>{{ __('Toate') }}</i></h5>
                <p>{{ __('Rambursuri in perioada selectata, indiferent de stare') }}</p>
            </div>
            <div class="p-2">
                <div class="flex flex-row items-center pb-4">
                    <div class="flex-shrink pr-4">
                        <div class="rounded p-3 bg-black"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                    </div>
                    <div class="flex-1 text-left">
                        <h3 class="font-bold text-3xl text-gray-600 my-0">{{ $total ?? '0' }} RON</h3>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="w-full card m-0 flex flex-column justify-between">
            <div class="px-2">
                <h5><i>{{ __('Total neplatite') }}</i></h5>
                <p>{{ __('Suma include comenzile') }} "{{ __('Nelivrate') }}", "{{ __('La curier') }}" {{ __('si') }} "{{ __('La') }} {{ config('app.name') }}"</p>
            </div>
            <div class="p-2">
                <div class="flex flex-row items-center pb-4">
                    <div class="flex-shrink pr-4">
                        <div class="rounded p-3 bg-red-600"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                    </div>
                    <div class="flex-1 text-left">
                        <h3 class="font-bold text-3xl text-gray-600 my-0">{{ Auth::user()->info('account_balance') != '' ? Auth::user()->info('account_balance') : '0' }} RON</h3>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="w-full card m-0 flex flex-column justify-between">
            <div class="px-2">
                <h5><i>{{ __('Nelivrate') }}</i></h5>
                <p>{{ __('Suma este pentru comenzile inca nelivrate') }}</p>
            </div>
            <div class="p-2">
                <div class="flex flex-row items-center pb-4">
                    <div class="flex-shrink pr-4">
                        <div class="rounded p-3 bg-red-600"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                    </div>
                    <div class="flex-1 text-left">
                        <h3 class="font-bold text-3xl text-gray-600 my-0">{{ $nelivrate ?? 0 }} RON</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full card m-0 flex flex-column justify-between">
            <div class="px-2">
                <h5><i>{{ __('La curier') }}</i></h5>
                <p>{{ __('Colete livrate, rambursuri inca la curier (indiferent de data)') }}</p>
            </div>
            <div class="p-2">
                <div class="flex flex-row items-center pb-4">
                    <div class="flex-shrink pr-4">
                        <div class="rounded p-3 bg-yellow-500"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                    </div>
                    <div class="flex-1 text-left">
                        <h3 class="font-bold text-3xl text-gray-600 my-0">{{ $laCurier ?? '0' }} RON</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full card m-0 flex flex-column justify-between">
            <div class="px-2">
                <h5><i>{{ __('La') }} {{ config('app.name') }}</i></h5>
                <p>{{ __('Rambursuri care vor fi platite la urmatoarea virare automata (indiferent de data)') }}</p>
            </div>
            <div class="p-2">
                <div class="flex flex-row items-center pb-4">
                    <div class="flex-shrink pr-4">
                        <div class="rounded p-3 bg-blue-600"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                    </div>
                    <div class="flex-1 text-left">
                        <h3 class="font-bold text-3xl text-gray-600 my-0">{{ $laAMR ?? '0' }} RON</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full card m-0 flex flex-column justify-between">
            <div class="px-2">
                <h5><i>{{ __('Platite') }}</i></h5>
                <p>{{ __('Rambursuri platite in perioada selectata') }}</p>
            </div>
            <div class="p-2">
                <div class="flex flex-row items-center pb-4">
                    <div class="flex-shrink pr-4">
                        <div class="rounded p-3 bg-green-600"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                    </div>
                    <div class="flex-1 text-left">
                        <h3 class="font-bold text-3xl text-gray-600 my-0">{{ $platite ?? '0' }} RON</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h5 class="col-span-12 mt-2"><i>{{ __('Tabel rambursuri') }}</i></h5>
    <x-slot name="thead">
        @forelse($repayments as $repayment)
        <x-jet-tr location='thead'>
            <x-jet-td location='thead'>{{ __('Data comanda') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Destinatar comanda') }}<span class="sm:hidden text-gray-800"><br>/</span></x-jet-td>
            <x-jet-td location='thead'>{{ __('AWB') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Suma') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Titular cont') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Cont') }}</x-jet-td>
            <x-jet-td location='thead' style="height: 55px; line-height: 32px;" >{{ __('Status') }}</x-jet-td>
            {{-- <x-jet-td location='thead' style="height: 55px; line-height: 32px;" >{{ __('Colet livrat') }}</x-jet-td> --}}
            <x-jet-td location='thead' style="height: 55px; line-height: 32px;" >{{ __('Ramburs trimis de curier') }}</x-jet-td>
            <x-jet-td location='thead' style="height: 55px; line-height: 32px;" >{{ __('Ramburs primit de') }} {{ config('app.name') }}</x-jet-td>
            <x-jet-td location='thead' style="height: 55px; line-height: 32px;" >{{ __('Ramburs virat clientului') }}</x-jet-td>
        </x-jet-tr>
        @empty
        <x-jet-tr location='thead'>
            <x-jet-td location='thead'>{{ __('Data comanda') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Destinatar comanda') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('AWB') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Suma') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Titular cont') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Cont') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Status') }}</x-jet-td>
            {{-- <x-jet-td location='thead'>{{ __('Colet livrat') }}</x-jet-td> --}}
            <x-jet-td location='thead'>{{ __('Ramburs trimis de curier') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Ramburs primit de') }} {{ config('app.name') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Ramburs virat clientului') }}</x-jet-td>
        </x-jet-tr>
        @endif
    </x-slot>

    @forelse($repayments as $repayment)
    <x-jet-tr>
        @php $livrare = $repayment->livrare; @endphp
        <x-jet-td>{{ Carbon\Carbon::parse($livrare ? $livrare->created_at : $repayment->created_at)->format('d/m/Y') }}</x-jet-td>
        <x-jet-td>
            @if($livrare)
                {{ $livrare->receiver->name }}<br>{{ $livrare->receiver->locality }}
            @else
                {{ __('Livrare stearsa') }}
            @endif
        </x-jet-td>
        <x-jet-td>{{ $repayment->awb }}</x-jet-td>
        <x-jet-td>{{ $repayment->total }} RON</x-jet-td>
        <x-jet-td>{{ $repayment->titular_cont }}</x-jet-td>
        <x-jet-td>{{ $repayment->iban }}</x-jet-td>
        <x-jet-td class="sm:text-center">
            @if($repayment->status == 1 && $repayment->type == 2)
                <span class="chip green lighten-5 m-0">
                    <span class="green-text">{{ __('Platita') }}</span>
                </span>
            @elseif($repayment->status == 0 && $livrare && $livrare->status == 5)
                <span class="chip red lighten-5 m-0">
                    <span class="red-text">{{ __('Anulata') }}</span>
                </span>
            @else
                <span class="chip red lighten-5 m-0">
                    <span class="red-text">{{ __('Neplatita') }}</span>
                </span>
            @endif
        </x-jet-td>
        {{-- <x-jet-td>
            @if($livrare->status == 1)
                <span class="chip green lighten-5 m-0">
                    <span class="green-text">{{ __('Da') }}</span>
                </span>
            @else
                <span class="chip red lighten-5 m-0">
                    <span class="red-text">{{ __('Nu') }}</span>
                </span>
            @endif
        </x-jet-td> --}}
        <x-jet-td>
            @if($repayment->type == 1 && $repayment->status == 1)
                <span class="chip green lighten-5 m-0">
                    <span class="green-text">{{ __('Da') }}</span>
                </span>
            @else
                <span class="chip red lighten-5 m-0">
                    <span class="red-text">{{ __('Nu') }}</span>
                </span>
            @endif
        </x-jet-td>
        <x-jet-td>
            @if($repayment->type == 3)
                <span class="chip green lighten-5 m-0">
                    <span class="green-text">{{ __('Da') }}</span>
                </span>
            @else
                <span class="chip red lighten-5 m-0">
                    <span class="red-text">{{ __('Nu') }}</span>
                </span>
            @endif
        </x-jet-td>
        <x-jet-td>
            @if($repayment->type == 2)
                <span class="chip green lighten-5 m-0">
                    <span class="green-text">{{ __('Da') }}</span>
                </span>
            @else
                <span class="chip red lighten-5 m-0">
                    <span class="red-text">{{ __('Nu') }}</span>
                </span>
            @endif
        </x-jet-td>
    </x-jet-tr>
    @empty
    <x-jet-tr class="border-0" style="height: 100%;">
        <x-jet-td colspan="11" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasit nici un ramburs') }}</x-jet-td>
    </x-jet-tr>
    @endif

    <x-slot name="pagination">
        @if($repayments)
            {{ $repayments->links() }}
        @endif
    </x-slot>
</x-jet-table>

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
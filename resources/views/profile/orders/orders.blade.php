<x-jet-table>
    <div class="row col-span-12">
        <x-jet-validation-errors class="bg-red-100 text-white text-sm px-4 py-3 mb-4 rounded-md shadow-sm" role="alert" />
        <p class="col-span-12 mt-2">{{ __('Cauta comenzile create intre datele specificate cu un anumit status') }}:</p>
        <form action="{{ route('dashboard.orders.show') }}" method="get">
            <div class="mt-1 grid grid-cols-12 gap-5 col-span-12">
                <div class="input-field col-span-12 sm:col-span-6 md:col-span-4 mb-0">
                    <x-jet-label value="{{ __('Data inceput') }}" />
                    <x-jet-input type="text" class="datepicker" value="{{ $condtitions['from'] ?? '' }}" placeholder="" id="from" name="from" />
                </div>
                <div class="input-field col-span-12 sm:col-span-6 md:col-span-4 mb-0">
                    <x-jet-label value="{{ __('Data sfarsit') }}" />
                    <x-jet-input type="text" class="datepicker" value="{{ $condtitions['to'] ?? '' }}" placeholder="" id="to" name="to" />
                </div>
                <div class="input-field col-span-12 sm:col-span-6 md:col-span-4 mb-0">
                    <select class="browser-default" id="status" name="status" >
                        <option value="" {{ !isset($condtitions['status']) ? 'selected' : '' }}>{{ __('Toate') }}</option>
                        @foreach($status_list as $status => $name)
                            <option value="{{ $status }}" @selected(isset($condtitions['status']) && $condtitions['status'] == $status)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <x-jet-label for="status" class="active" style="line-height: 1.15rem" value="{{ __('Status') }}" />
                </div>
                <div class="input-field col-span-12 sm:col-span-6 md:col-span-4 mb-0">
                    <x-jet-label value="{{ __('AWB') }}" />
                    <x-jet-input type="text" value="{{ $condtitions['awb'] ?? '' }}" placeholder="" id="awb" name="awb" />
                </div>
                <div class="input-field col-span-12 sm:col-span-6 md:col-span-4 mb-0">
                    <x-jet-label value="{{ __('Nume destinatar') }}" />
                    <x-jet-input type="text" value="{{ $condtitions['receiver_name'] ?? '' }}" placeholder="" id="receiver_name" name="receiver_name" />
                </div>
                <div class="input-field col-span-12 sm:col-span-6 md:col-span-2 mb-0">
                    <button type="submit" class="btn blue darken-2 waves-effect waves-light">{{ __('Cauta') }}</button>
                </div>
            </div>
        </form>
        <div class="my-4 block md:flex">
            <div class="flex-shrink-0 mr-1 mb-2">
                <a href="{{ route('dashboard.orders.excel', request()->query()) }}" class="inline-flex px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green-500 disabled:opacity-25 transition"><i class="far fa-lg fa-file-excel self-center"></i> &nbsp;{{ __('Export comenzi') }}</a>
            </div>
            <x-jet-alert-card class="flex-grow">
                <svg class="fill-current h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                </svg>
                <span><b class="mr-0.5">Info:</b> Daca filtrele <b>data inceput</b> si <b>data sfarsit</b> nu sunt completate atunci doar comenzile din ultimele 7 zile vor fi exportate.</span>
            </x-jet-alert-card>
        </div>
    </div>
    <h5 class="col-span-12 mt-2"><i>{{ __('Tabel comenzi') }}</i></h5>
    <x-slot name="thead">
        @forelse($orders as $address)
        <x-jet-tr location='thead'>
            <x-jet-td location='thead'>{{ __('Data') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Curier') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Cost') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('AWB') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Expeditor') }}<span class="sm:hidden text-gray-800"><br>/</span></x-jet-td>
            <x-jet-td location='thead'>{{ __('Destinatar') }}<span class="sm:hidden text-gray-800"><br>/</span></x-jet-td>
            <x-jet-td location='thead'>{{ __('Ramburs') }}</x-jet-td>
            <x-jet-td class="sm:text-center" style="height: 55px; line-height: 32px;" location='thead'>{{ __('Status') }}</x-jet-td>
            <x-jet-td class="sm:text-center" location='thead'>{{ __('Optiuni') }}</x-jet-td>
        </x-jet-tr>
        @empty
        <x-jet-tr location='thead'>
            <x-jet-td location='thead'>{{ __('Data') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Curier') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Cost') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('AWB') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Expeditor') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Destinatar') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Ramburs') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Status') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Optiuni') }}</x-jet-td>
        </x-jet-tr>
        @endif
    </x-slot>

    @forelse($orders as $order)
    <x-jet-tr>
        <x-jet-td>{{ Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</x-jet-td>
        <x-jet-td>{{ $order->curier }}</x-jet-td>
        <x-jet-td>{{ $order->value }} RON</x-jet-td>
        <x-jet-td>
            <x-tracking-links :order="$order" />
        </x-jet-td>
        @php 
            $sender = $order->sender;
            $receiver = $order->receiver;
        @endphp
        <x-jet-td class="text-capitalize"><span>{{ $sender ? $sender->name : '' }}</span><br>{{ $sender ? $sender->locality : '' }}</x-jet-td>
        <x-jet-td class="text-capitalize"><span>{{ $receiver ? $receiver->name : '' }}</span><br>{{ $receiver ? $receiver->locality : '' }}</x-jet-td>
        <x-jet-td>{{ $order->ramburs_value ?? '0' }} {{ $order->ramburs_currency ?? 'RON' }}</x-jet-td>
        <x-jet-td class="sm:text-center">
            <x-jet-order-status :order="$order" />
        </x-jet-td>
        <x-jet-td class="sm:text-center" style="height: 44px;">
            <a href="{{ route('dashboard.orders.repeat', ['livrare' => $order->id]) }}" target="_blank" title="{{ __('Repeta comanda') }} #{{ $order->id }}">
                <i class="fas fa-lg fa-redo text-blue-500"></i>
            </a>
            /
            @if($order->invoice && $order->invoice->status == '1')
                <a href="{{ route('dashboard.orders.pdf', ['livrare' => $order->id, 'invoice' => $order->invoice->id]) }}" target="Factura" title="{{ __('Factura') }} {{ $order->invoice->series }}{{ $order->invoice->number }}">
                    <i class="far fa-file-pdf fa-lg text-red-500"></i>
                </a>
                /
                @if($order->creditedInvoice)
                    <a href="{{ route('admin.orders.pdf', ['livrare' => $order->id, 'invoice' => $order->creditedInvoice->id]) }}" target="Storno" title="{{ __('Storno factura') }} {{ $order->invoice->series }}{{ $order->invoice->number }}">
                        <i class="fas fa-lg fa-file-pdf text-red-500"></i>
                    </a>
                    /
                @endif
            @endif
            <a href="{{ route('dashboard.orders.view', ['livrare' => $order->id]) }}" title="{{ __('Detalii comanda') }}">
                <i class="far fa-eye fa-lg text-blue-500"></i>
            </a>
            @if(!in_array($order->status, ['1','6','5']) && $order->api_shipment_id != null)
            /
            <a href="{{ route('dashboard.orders.awb', ['livrare' => $order->id]) }}" title="{{ __('AWB comanda') }}" target="_blank">
                <span class="fa-layers fa-lg fa-fw">
                    <i class="far fa-file text-blue-500" data-fa-transform="down-2"></i>
                    <span class="fa-layers-text fa-inverse bg-blue-500 p-2" data-fa-transform="shrink-10 down-3" style="font-weight:900">AWB</span>
                </span>
            </a>
            /
            <a class="modal-trigger" href="#modal-{{ $order->id }}">
                <i class="fas fa-times fa-lg text-gray-500"></i>
            </a>
            <div id="modal-{{ $order->id }}" class="modal">
                <div class="modal-content">
                    <h4 class="text-left mb-0">{{ __('Anulare comanda') }}</h4>
                    {{-- <p>A bunch of text</p> --}}
                </div>
                <div class="modal-footer" style="padding:1em; height:auto;">
                    <a href="javascript:void(0)"
                            class="modal-action modal-close waves-effect waves-light blue accent-2 white-text btn-small md:mt-0 mb-0 md:float-left block md:inline w-full md:w-auto">
                        <i class="fas fa-redo"></i>
                        {{ __('Inchide') }}
                    </a>
                    <form method="POST" action="{{ route('dashboard.orders.cancel', ['livrare' => $order->id]) }}" class="block md:inline" title="{{ __('Anuleaza comanda') }}">
                        @csrf
                        <input type="hidden" name="type" value="1">
                        <a href="{{ route('dashboard.orders.cancel', ['livrare' => $order->id]) }}"
                                class="modal-action modal-close waves-effect waves-light red accent-2 white-text btn-small md:mt-0 mb-0 w-full md:w-auto" 
                                onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            <i class="fas fa-times fa-lg"></i>
                            {{ __('Ramburs in credite') }}
                        </a>
                    </form>
                    @if($order->payed == '1' && $order->status == 0)
                        <form method="POST" action="{{ route('dashboard.orders.cancel', ['livrare' => $order->id]) }}" class="block md:inline" title="{{ __('Anuleaza comanda') }}">
                            @csrf
                            <input type="hidden" name="type" value="2">
                            <a href="{{ route('dashboard.orders.cancel', ['livrare' => $order->id]) }}"
                                    class="modal-action modal-close waves-effect waves-light red accent-2 white-text btn-small md:mt-0 mb-0 w-full md:w-auto" 
                                    onclick="event.preventDefault();
                                            this.closest('form').submit();">
                                <i class="fas fa-times fa-lg"></i>
                                {{ __('Ramburs in cont bancar') }}
                            </a>
                        </form>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </x-jet-td>
    </x-jet-tr>
    @empty
    <x-jet-tr class="border-0" style="height: 100%;">
        <x-jet-td colspan="9" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasita nici o comanda') }}</x-jet-td>
    </x-jet-tr>
    @endif

    <x-slot name="pagination">
        @if($orders)
            {{ $orders->links() }}
        @endif
    </x-slot>
</x-jet-table>

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-facturare.css') }}">
@endpush

@push('scripts')
{{-- <script src="{{ asset('js/vendors/mat/materialize.min.js') }}"></script> --}}
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('js/pages/form-facturare.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.modal').modal();

        //or by click on trigger
        $('.modal-trigger').modal();
    });
</script>
@if(session()->has('success'))
    <script type="text/javascript">
        M.toast({html: '{{ session()->get('success') }}.', classes: 'green accent-4', displayLength: 5000});
        $('#toast-container').css({
            top: '19%',
            right: '6%',
        });
        $(document).ready(function(){
            $('.modal').modal();

            //or by click on trigger
            $('.modal-trigger').modal();
        });
    </script>
@endif
@endpush
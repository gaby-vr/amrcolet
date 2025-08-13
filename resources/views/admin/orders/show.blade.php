@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('css/admin/plugins/flatpickr/flatpickr.min.css') }}"></link>
@endpush

@push('scripts')
<script src="{{ asset('fonts/fontawesome/js/all.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.ro.js') }}"></script>
<script type="text/javascript">
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            locale: "ro",
        });
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Comenzi') }}</x-slot>
        <x-slot name="href"></x-slot>
        <x-slot name="btnName"></x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        <div class="px-5 w-full pb-3">
                            <x-jet-validation-messages class="mb-4" />
                            <x-jet-validation-errors class="mb-4" />
                            <div class="mb-3">
                                <form action="{{ route('admin.orders.import') }}" method="post" enctype="multipart/form-data" class="sm:flex">
                                    @csrf
                                    <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full sm:mr-3" id="curier" name="curier">
                                        @foreach([
                                            '2' => 'DPD',
                                            '1' => 'Cargus',
                                        ] as $curier => $name)
                                            <option value="{{ $curier }}" @selected(old('curier') == $curier)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <x-jet-input type="file" name="file" class="block w-full text-sm border rounded-lg cursor-pointer bg-gray-50 focus:outline-none sm:mr-3 mt-2 sm:mt-0" />
                                    <label class="block font-medium text-sm text-gray-700 flex items-center sm:mr-3 sm:w-5/12 mt-2 sm:mt-0">
                                        <x-jet-checkbox name="exclude_borderou" value="1" />
                                        <span class="ml-2">{{ __('Exclude din borderouri') }}</span>
                                    </label>
                                    <button type="submit" class="inline-flex px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green-500 disabled:opacity-25 transition mt-2 items-center sm:mt-0"><i class="far fa-lg fa-file-excel self-center"></i> &nbsp;{{ __('Importa') }}</button>
                                </form>
                                <hr class="my-3">
                                <a href="{{ route('admin.orders.excel', request()->query()) }}" class="inline-flex px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green-500 disabled:opacity-25 transition"><i class="far fa-lg fa-file-excel self-center"></i> &nbsp;{{ __('Export comenzi') }}</a>
                            </div>
                            <p class="text-lg mb-2">{{ __('Cauta comenzi intre') }}:</p>
                            <form action="{{ route('admin.orders.show') }}" method="get">
                                <div class="grid grid-cols-12 gap-3 w-full">
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Data inceput') }}" />
                                        <x-jet-input type="text" class="datepicker w-full" value="{{ $condtitions['from'] ?? '' }}" placeholder="" id="from" name="from" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Data sfarsit') }}" />
                                        <x-jet-input type="text" class="datepicker w-full" value="{{ $condtitions['to'] ?? '' }}" placeholder="" id="to" name="to" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Status') }}" />
                                        <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="status" name="status" >
                                            <option value="" {{ !isset($condtitions['status']) ? 'selected' : '' }}>{{ __('Toate') }}</option>
                                            @foreach($status_list as $status => $name)
                                                <option value="{{ $status }}" @selected(isset($condtitions['status']) && $condtitions['status'] == $status)>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Email client') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['user_email'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="user_email" name="user_email" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Nume destinatar') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['receiver_name'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="receiver_name" name="receiver_name" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Awb') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['awb'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="awb" name="awb" />
                                    </div>
                                    <div class="col-span-12 md:col-span-2 self-end pb-1">
                                        <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black disabled:opacity-25 transition">{{ __('Cauta') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(session()->has('error'))
                            <div class="flex items-center bg-red-500 text-white text-sm font-bold px-4 py-3 mb-4" role="alert">
                                <p>{{ session()->get('error') }}</p>
                            </div>
                        @endif
                        {{-- @if(session()->has('success'))
                            <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3 mb-4" role="alert">
                                <p>{{ session()->get('success') }}</p>
                            </div>
                        @endif --}}
                        <x-jet-table>
                            {{ $orders->links() }}
                            <x-slot name="thead">
                                @forelse($orders as $order)
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead'>{{ __('Cont') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Data') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Curier') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Cost') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('AWB') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Expeditor') }}<span class="sm:hidden text-black"><br>/</span></x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Destinatar') }}<span class="sm:hidden text-black"><br>/</span></x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Ramburs') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center" style="{{ $order->cancelRequest != null ? 'height: 82px' : 'height: 55px' }};">
                                            {{ __('Status') }}
                                            @if($order->cancelRequest != null)
                                                <span class="sm:hidden text-black"><br>/</span>
                                            @endif
                                        </x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @empty
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead'>{{ __('Cont') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Data') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Curier') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Cost') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('AWB') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Expeditor') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Destinatar') }}</x-jet-td>
                                        <x-jet-td location='thead'>{{ __('Ramburs') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Status') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @endforelse
                            </x-slot>
                            @forelse($orders as $order)
                                <x-jet-tr>
                                    <x-jet-td x-data="{ tooltip: false }">
                                        @if($order->user_id == 0) 
                                            <i class="fas fa-user-slash text-red-500" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                                            <div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-20 -translate-y-3/4 bg-red-500 rounded-lg shadow-lg">
                                                    {{ __('Comanda fara cont') }}
                                                </div>
                                                <svg class="absolute left-16 z-10 w-6 h-6 text-red-500 transform -translate-x-16 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                </svg>
                                            </div>
                                        @else
                                            <i class="fas fa-user text-green-500" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                                            <div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-20 -translate-y-3/4 bg-green-500 rounded-lg shadow-lg">
                                                    {{ __('Comanda realizata de user-ul :name', ['name' => $order->user->name ?? '']) }}
                                                </div>
                                                <svg class="absolute left-16 z-10 w-6 h-6 text-green-500 transform -translate-x-16 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                </svg>
                                            </div>
                                        @endif
                                    </x-jet-td>
                                    <x-jet-td>{{ Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</x-jet-td>
                                    <x-jet-td>{{ $order->curier }}</x-jet-td>
                                    <x-jet-td>{{ $order->value }} RON</x-jet-td>
                                    <x-jet-td>
                                        <x-tracking-links :order="$order" />
                                    </x-jet-td>
                                    <x-jet-td class="text-capitalize truncate">{{ $order->sender->name ?? '' }}<br>{{ $order->sender->locality ?? '' }}</x-jet-td>
                                    <x-jet-td class="text-capitalize">{{ $order->receiver->name ?? '' }}<br>{{ $order->receiver->locality ?? '' }}</x-jet-td>
                                    <x-jet-td>{{ $order->ramburs_value ?? '0' }} {{ $order->ramburs_currency ?? 'RON' }}</x-jet-td>
                                    <x-jet-td class="sm:text-center" style="{{ $order->cancelRequest != null ? 'height: 82px' : 'height: 55px' }};">
                                        <x-jet-order-status :order="$order" />
                                    </x-jet-td>
                                    <x-jet-td class="sm:text-center" style="max-height: 50px;">
                                        @if($order->cancelRequest != null)
                                            <a href="{{ route('admin.orders.accept.cancel', ['cancelRequest' => $order->cancelRequest->id]) }}" title="{{ __('Accepta anularea comenzii') }}">
                                                <i class="fas fa-check fa-lg text-green-500"></i>
                                            </a>
                                            /
                                            <a href="{{ route('admin.orders.refuse.cancel', ['cancelRequest' => $order->cancelRequest->id]) }}" title="{{ __('Refuza anularea comenzii') }}">
                                                <i class="fas fa-times fa-lg text-red-500"></i>
                                            </a>
                                        @else
                                            @if($order->invoice && $order->invoice->status == 1)
                                                <a href="{{ route('admin.orders.pdf', ['livrare' => $order->id, 'invoice' => $order->invoice->id]) }}" target="order_{{ $order->id }}">
                                                    <i class="far fa-lg fa-file-pdf text-red-500"></i>
                                                </a>
                                                /
                                                @if($order->creditedInvoice)
                                                    <a href="{{ route('admin.orders.pdf', ['livrare' => $order->id, 'invoice' => $order->creditedInvoice->id]) }}" target="order_{{ $order->id }}">
                                                        <i class="fas fa-lg fa-file-pdf text-red-500"></i>
                                                    </a>
                                                /
                                                @endif
                                            @endif
                                            <a href="{{ route('admin.orders.details', ['livrare' => $order->id]) }}" title="{{ __('Detalii comanda') }}">
                                                <i class="far fa-eye fa-lg text-blue-500"></i>
                                            </a>
                                            @if(!in_array($order->status, ['1','4','5']) && $order->api_shipment_id != null)
                                            /
                                            <a href="{{ route('admin.orders.awb', ['livrare' => $order->id]) }}" title="{{ __('AWB comanda') }}" target="_blank">
                                                <span class="fa-layers fa-lg fa-fw">
                                                    <i class="far fa-file text-blue-500" data-fa-transform="down-2"></i>
                                                    <span class="fa-layers-text fa-inverse bg-blue-500 p-2" data-fa-transform="shrink-10 down-3" style="font-weight:900">AWB</span>
                                                </span>
                                            </a>
                                            @endif
                                        @endif
                                        /
                                        <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="inline">
                                            @method('delete')
                                            @csrf
                                            <a href="{{ route('admin.orders.destroy', $order->id) }}" class="text-red-500" onclick="event.preventDefault();
                                                if(confirm('Vrei sa stergi livrarea #{{ $order->id }}?')){this.closest('form').submit();}" target="order_{{ $order->id }}"><i class="fas fa-trash-alt"></i></a>
                                        </form>
                                    </x-jet-td>
                                </x-jet-tr>
                            @empty
                            <x-jet-tr class="border-0" style="height: 100%;">
                                <x-jet-td colspan="9" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasita nici o comanda') }}</x-jet-td>
                            </x-jet-tr>
                            @endforelse
                        </x-jet-table>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
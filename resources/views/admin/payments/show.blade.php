@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('css/admin/plugins/flatpickr/flatpickr.min.css') }}"></link>
@endpush

@push('scripts')
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/admin/plugins/flatpickr/flatpickr.ro.js') }}"></script>
<script type="text/javascript">
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            locale: "ro",
        });
        function toggleBoxes(source) {
            checkboxes = document.getElementsByName('invoices[]');
            for(var i=0, n=checkboxes.length;i<n;i++){
                checkboxes[i].checked = source.checked;
            }
        }
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Istoric plati') }}</x-slot>
        <x-slot name="href">{{ route('admin.invoices.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza factura') }}</x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        <div class="px-5 w-full pb-3">
                            @if(session()->has('success'))
                                <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3 mb-4" role="alert">
                                    <p>{{ session()->get('success') }}</p>
                                </div>
                            @endif
                            @if(session()->has('status'))
                                <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3 mb-4" role="alert">
                                    <p>{{ session()->get('status') }}</p>
                                </div>
                            @endif
                            <x-jet-validation-errors class="mb-4" />
                            <div class="mb-3">
                                <a href="{{ route('admin.invoices.excel', request()->query()) }}" class="inline-flex px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green-500 disabled:opacity-25 transition"><i class="far fa-lg fa-file-excel self-center"></i> &nbsp;{{ __('Export facturi') }}</a>
                                <form id="mass-download" action="{{ route('admin.invoices.pdf.multiple') }}" method="post" class="inline-block mt-1 md:mt-0 align-bottom">
                                    @csrf
                                    <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black disabled:opacity-25 transition">{{ __('Descarca facturile selectate') }}</button>
                                </form>
                            </div>
                            <p class="text-lg mb-2">{{ __('Cauta plati intre') }}:</p>
                            <form action="{{ route('admin.invoices.show') }}" method="get">
                                <div class="grid grid-cols-12 gap-3 w-full">
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Data inceput') }}" />
                                        <x-jet-input type="text" class="datepicker w-full" value="{{ $condtitions['from'] ?? '' }}" placeholder="" id="from" name="from" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Data sfarsit') }}" />
                                        <x-jet-input type="text" class="datepicker w-full" value="{{ $condtitions['to'] ?? '' }}" placeholder="" id="to" name="to" />
                                    </div>
                                    <div class="col-span-12 md:col-span-2">
                                        <x-jet-label value="{{ __('Status') }}" />
                                        <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="status" name="status" >
                                            <option value="" {{ !isset($condtitions['status']) ? 'selected' : '' }}>{{ __('Toate') }}</option>
                                            <option value="1" {{ isset($condtitions['status']) && $condtitions['status'] == '1' ? 'selected' : '' }}>{{ __('Confirmate') }}</option>
                                            <option value="0" {{ isset($condtitions['status']) && $condtitions['status'] == '0' ? 'selected' : '' }}>{{ __('In curs de procesare') }}</option>
                                            <option value="2" {{ isset($condtitions['status']) && $condtitions['status'] == '2' ? 'selected' : '' }}>{{ __('Anulate') }}</option>
                                            <option value="3" {{ isset($condtitions['status']) && $condtitions['status'] == '3' ? 'selected' : '' }}>{{ __('Stornata') }}</option>
                                            <option value="4" {{ isset($condtitions['status']) && $condtitions['status'] == '4' ? 'selected' : '' }}>{{ __('Respinsa') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Email client') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['email'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="email" name="email" />
                                    </div>
                                    <div class="col-span-12 md:col-span-1 self-end pb-1">
                                        <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black disabled:opacity-25 transition">{{ __('Cauta') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <x-jet-table>
                            {{ $invoices->links() }}
                            <x-slot name="thead">
                                @forelse($invoices as $invoice)
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">
                                            <x-jet-checkbox onclick="toggleBoxes(this)" name="invoices[]"/>
                                        </x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Numar') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Denumire') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="truncate">{{ __('Total') }} (RON)</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Platitor') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Emis in') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center" style="height: 55px; line-height: 32px;">{{ __('Status') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center">{{ __('Factura') }}</x-jet-td>
                                    </x-jet-tr>
                                @empty
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">{{ __('Numar') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Denumire') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="truncate">{{ __('Total') }} (RON)</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Platitor') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Emis in') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Status') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Factura') }}</x-jet-td>
                                    </x-jet-tr>
                                @endforelse
                            </x-slot>
                            @forelse($invoices as $invoice)
                                <x-jet-tr>
                                    <x-jet-td>
                                        @if(in_array($invoice->status, [1,2,3]))
                                            <x-jet-checkbox form="mass-download" value="{{ $invoice->id }}" name="invoices[]"/>
                                        @endif
                                    </x-jet-td>
                                    <x-jet-td>{{ $invoice->series }}{{ $invoice->number }}</x-jet-td>
                                    <x-jet-td>
                                        @if($invoice->meta('created_by_admin') == '1')
                                            {{ __('Creata manual de catre admin') }}
                                        @elseif($invoice->livrare_id == 0)
                                            {{ __('Reincarcare cont cu credite') }}
                                        @elseif($invoice->storned != null)
                                            {{ __('Stornare factura') }} {{ $invoice->storned->series }}{{ $invoice->storned->number }}
                                        @else
                                            {{ __('Comanda #').$invoice->livrare_id }}
                                        @endif
                                    </x-jet-td>
                                    <x-jet-td>{{ $invoice->total }}</x-jet-td>
                                    <x-jet-td x-data="{ tooltip: false }">{{ $invoice->user ? $invoice->user->name : $invoice->meta('client_last_name').' '.$invoice->meta('client_first_name') }} 
                                        @if($invoice->user_id == 0) 
                                            <i class="fas fa-user-slash text-red-500" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                                            <div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-20 -translate-y-3/4 bg-red-500 rounded-lg shadow-lg">
                                                    {{ __('Comanda fara cont') }}
                                                </div>
                                                <svg class="absolute left-16 z-10 w-6 h-6 text-red-500 transform -translate-x-16 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                </svg>
                                            </div>
                                        @endif
                                    </x-jet-td>
                                    <x-jet-td>{{ $invoice->created_at ? Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') : '' }}</x-jet-td>
                                    <x-jet-td class="sm:text-center" style="height: 55px; line-height: 32px;">
                                        <x-jet-invoice-pill :invoice="$invoice" type="2" />
                                    </x-jet-td>
                                    <x-jet-td class="sm:text-center group">
                                        @if(in_array($invoice->status, [1,2,3]))
                                            <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" title="{{ __('Vizualizeaza') }}" target="invoice_pdf"><i class="far fa-file-pdf text-red-500"></i></a>
                                            /
                                            @if($invoice->external_link == null)
                                                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" title="{{ __('Editeaza') }}"><i class="fas fa-edit text-blue-300"></i></a>
                                                /
                                                <a href="{{ route('admin.invoices.send.api', [$invoice->id, 0]) }}" title="{{ __('Trimite catre platforma de facturare') }}"><i class="far fa-paper-plane text-blue-300"></i></a>
                                            @else
                                                <a href="{{ route('admin.invoices.update.api', $invoice->id) }}" title="{{ __('Actualizeaza informatii prin api') }}"><i class="fas fa-wrench text-blue-300"></i></a>
                                            @endif
                                        @endif
                                        @if($invoice->status == 1 && $invoice->credited_by == null)
                                            /
                                            @if($invoice->external_link == null)
                                                <a href="{{ route('admin.invoices.storn', $invoice->id) }}" title="{{ __('Storneaza') }}"><i class="fas fa-undo text-purple-300"></i></a>
                                            @else
                                                <a href="{{ route('admin.invoices.cancel.api', $invoice->id) }}" title="{{ __('Anuleaza prin api') }}"><i class="fas fa-trash text-red-300"></i></a>
                                            @endif
                                        @elseif($invoice->status == 2)
                                            /
                                            <a href="{{ route('admin.invoices.restore.api', $invoice->id) }}" title="{{ __('Restauraza prin api') }}"><i class="fas fa-trash-restore-alt text-blue-300"></i></a>
                                        @endif
                                        @if($invoice->spv === null && $invoice->external_link !== null)
                                            /
                                            <a href="{{ route('admin.invoices.spv.api', $invoice->id) }}" title="{{ __('Trimite SPV prin api') }}" onclick="return confirm('Vrei sa trimiti factura catre SPV?');"><i class="fas fa-share-square text-blue-900"></i></a>
                                        @endif
                                        {{-- @if($invoice->status == 0)
                                            <span class="text-white group-hover:text-gray-100">/</span>
                                        @endif --}}
                                    </x-jet-td>
                                </x-jet-tr>
                            @empty
                            <x-jet-tr class="border-0" style="height: 100%;">
                                <x-jet-td colspan="7" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasita nici o factura') }}</x-jet-td>
                            </x-jet-tr>
                            @endforelse
                        </x-jet-table>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
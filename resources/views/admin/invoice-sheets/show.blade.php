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
            checkboxes = document.getElementsByName('items[]');
            for(var i=0, n=checkboxes.length;i<n;i++){
                checkboxes[i].checked = source.checked;
            }
        }
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Fise facturi') }}</x-slot>
        <x-slot name="href">{{ route('admin.invoice-sheets.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza fisa facturi') }}</x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        <div class="px-5 w-full pb-3">
                            <x-jet-validation-messages class="mb-4" />

                            <p class="text-lg mb-2">{{ __('Cauta fise intre') }}:</p>
                            <form action="{{ route('admin.invoice-sheets.show') }}" method="get">
                                <div class="grid grid-cols-12 gap-3 w-full">
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Data inceput') }}" />
                                        <x-jet-input type="text" class="datepicker w-full" value="{{ $conditions['from'] ?? '' }}" placeholder="" id="from" name="from" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Data sfarsit') }}" />
                                        <x-jet-input type="text" class="datepicker w-full" value="{{ $conditions['to'] ?? '' }}" placeholder="" id="to" name="to" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Status plata') }}" />
                                        <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="status" name="status" >
                                            <option value="">{{ __('Toate') }}</option>
                                            <option value="1" @selected(isset($conditions['status']) && $conditions['status'] == '1')>{{ __('Platite') }}</option>
                                            <option value="0" @selected(isset($conditions['status']) && $conditions['status'] == '0')>{{ __('Neplatite') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Nume client') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $conditions['user_name'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="user_name" name="user_name" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Email client') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $conditions['user_email'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="user_email" name="user_email" />
                                    </div>
                                    <div class="col-span-12 md:col-span-2 self-end pb-1">
                                        <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black disabled:opacity-25 transition">{{ __('Cauta') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <x-jet-table>
                            {{ $items->links() }}
                            <x-slot name="thead">
                                @forelse($items as $item)
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">
                                            <x-jet-checkbox onclick="toggleBoxes(this)" name="items[]"/>
                                        </x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Data inceput') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Utilizator') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="truncate">{{ __('Total') }} (RON)</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Emis in') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @empty
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">
                                            <x-jet-checkbox onclick="toggleBoxes(this)" name="items[]"/>
                                        </x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Data inceput') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Utilizator') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="truncate">{{ __('Total') }} (RON)</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Emis in') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @endforelse
                            </x-slot>
                            @forelse($items as $item)
                                <x-jet-tr>
                                    <x-jet-td>
                                        <x-jet-checkbox form="mass-download" value="{{ $item->id }}" name="items[]"/>
                                    </x-jet-td>
                                    <x-jet-td>{{ $item->transformDate('start_date') }}</x-jet-td>
                                    <x-jet-td><a href="{{ route('admin.users.edit', $item->user) }}">{{ $item->user->name }}</a></x-jet-td>
                                    <x-jet-td>{{ $item->total }}</x-jet-td>
                                    <x-jet-td>{{ $item->transformDate('payed_at') }}</x-jet-td>
                                    <x-jet-td class="sm:text-center group">
                                        @if(Route::has('admin.invoice-sheets.update.api'))
                                            <a href="{{ route('admin.invoice-sheets.update.api', $item->id) }}"><i class="fas fa-sync text-blue-300"></i></a>
                                            /
                                        @endif
                                        @if(Route::has('admin.invoice-sheets.edit'))
                                            <a href="{{ route('admin.invoice-sheets.edit', $item->id) }}"><i class="fas fa-edit text-blue-300"></i></a>
                                        @endif
                                        @if(Route::has('admin.invoice-sheets.create.invoice'))
                                            /
                                            @if($item->invoice)
                                                <a href="{{ route('admin.invoices.pdf', $item->invoice_id) }}" title="{{ __('Vizualizeaza factura') }}" target="invoice_pdf"><i class="far fa-file-pdf text-red-500"></i></a>
                                            @else
                                                <a href="{{ route('admin.invoice-sheets.create.invoice', $item->id) }}" title="{{ __('Creaza factura') }}"><i class="fas fa-file-medical text-red-300"></i></a>
                                            @endif
                                        @endif
                                        @if(Route::has('admin.invoice-sheets.export'))
                                            /
                                            <a href="{{ route('admin.invoice-sheets.export', $item->id) }}"><i class="far fa-file-excel text-green-300"></i></a>
                                        @endif
                                        @if(Route::has('admin.invoice-sheets.destroy'))
                                            /
                                            <form method="POST" action="{{ route('admin.invoice-sheets.destroy', $item->id) }}" class="inline">
                                                @method('delete')
                                                @csrf
                                                <a href="{{ route('admin.invoice-sheets.destroy', $item->id) }}" class="text-red-500" onclick="event.preventDefault();
                                                    if(confirm('Vrei sa stergi fisa de facturi #{{ $item->id }}?')){this.closest('form').submit();}" target="item_{{ $item->id }}"><i class="fas fa-trash-alt"></i></a>
                                            </form>
                                        @endif
                                    </x-jet-td>
                                </x-jet-tr>
                            @empty
                                <x-jet-tr class="border-0" style="height: 100%;">
                                    <x-jet-td colspan="7" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasit nici un borderou') }}</x-jet-td>
                                </x-jet-tr>
                            @endforelse
                        </x-jet-table>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
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
        <x-slot name="title">{{ __('Borderouri') }}</x-slot>
        <x-slot name="href">{{ route('admin.borderouri.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza borderou') }}</x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        <div class="px-5 w-full pb-3">
                            <x-jet-validation-messages class="mb-4" />
                            {{-- <div class="mb-3">
                                <a href="{{ route('admin.borderouri.excel') }}" class="inline-flex px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green-500 disabled:opacity-25 transition"><i class="far fa-lg fa-file-excel self-center"></i> &nbsp;{{ __('Export facturi') }}</a>
                                <form id="mass-download" action="{{ route('admin.invoices.pdf.multiple') }}" method="post" class="inline-block mt-1 md:mt-0 align-bottom">
                                    @csrf
                                    <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black disabled:opacity-25 transition">{{ __('Descarca facturile selectate') }}</button>
                                </form>
                            </div> --}}
                            <x-jet-alert-card type="info" class="mb-2">
                                <div class="font-medium text-blue-600">{{ __('Informatii ceritifcat SSL:') }}</div>
                                <ul class="mt-3 list-disc list-inside text-sm text-blue-600">
                                    <li>{!! __('Certificat valabil pana la date de: <b>:date</b>', ['date' => $cert_date]) !!}</li>
                                    <li>{!! __('Thumbprint: <b>:thumbprint</b> (trebuie trimis catre LibraBank la reinoire certificat)', ['thumbprint' => $cert_thumbprint]) !!}</li>
                                </ul>
                            </x-jet-alert-card>
                            <p class="text-lg mb-2">{{ __('Cauta borderouri intre') }}:</p>
                            <form action="{{ route('admin.borderouri.show') }}" method="get">
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
                                        <x-jet-label value="{{ __('Status plata') }}" />
                                        <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="status" name="status" >
                                            <option value="">{{ __('Toate') }}</option>
                                            <option value="1" @selected(isset($condtitions['status']) && $condtitions['status'] == '1')>{{ __('Platite') }}</option>
                                            <option value="0" @selected(isset($condtitions['status']) && $condtitions['status'] == '0')>{{ __('Neplatite') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Nume client') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['user_name'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="user_name" name="user_name" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Email client') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['user_email'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="user_email" name="user_email" />
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
                                        <x-jet-td location='thead' border="black">{{ __('Platit pe') }}</x-jet-td>
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
                                        <x-jet-td location='thead' border="black">{{ __('Platit pe') }}</x-jet-td>
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
                                        @if(Route::has('admin.borderouri.payment.api'))
                                            <a href="{{ route('admin.borderouri.payment.api', $item->id) }}"><i class="fas fa-money-check text-blue-300"></i></i></a>
                                            /
                                        @endif
                                        @if(Route::has('admin.borderouri.update.api'))
                                            <a href="{{ route('admin.borderouri.update.api', $item->id) }}"><i class="fas fa-sync text-blue-300"></i></a>
                                            /
                                        @endif
                                        @if(Route::has('admin.borderouri.edit'))
                                            <a href="{{ route('admin.borderouri.edit', $item->id) }}"><i class="fas fa-edit text-blue-300"></i></a>
                                        @endif
                                        @if(Route::has('admin.borderouri.export'))
                                            /
                                            <a href="{{ route('admin.borderouri.export', $item->id) }}"><i class="far fa-file-excel text-green-300"></i></a>
                                        @endif
                                        @if(Route::has('admin.borderouri.destroy'))
                                            /
                                            <form method="POST" action="{{ route('admin.borderouri.destroy', $item->id) }}" class="inline">
                                                @method('delete')
                                                @csrf
                                                <a href="{{ route('admin.borderouri.destroy', $item->id) }}" class="text-red-500" onclick="event.preventDefault();
                                                    if(confirm('Vrei sa stergi borderoul {{ $item->name }}?')){this.closest('form').submit();}" target="item_{{ $item->id }}"><i class="fas fa-trash-alt"></i></a>
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
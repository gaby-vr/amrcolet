@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('css/admin/plugins/flatpickr/flatpickr.min.css') }}"></link>
<style type="text/css">
    @media (max-width: 639px) {
        .table-admin table th, .table-admin table td {
            height: 50px; 
        }
        .table-admin table th.badge, .table-admin table td.badge {
            height: 60px; 
        }

        .table-admin table th.rows2, .table-admin table td.rows2 {
            height: 72px; 
        }
    }
</style>
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
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Rambursuri') }}</x-slot>
        <x-slot name="href"></x-slot>
        <x-slot name="btnName"></x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center table-admin">
                        <div class="px-5 w-full pb-3">
                            <p class="text-lg mb-2">{{ __('Cauta rambursuri intre') }}:</p>
                            <form action="{{ route('admin.repayments.show') }}" method="get">
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
                                            <option value="1" {{ isset($condtitions['status']) && $condtitions['status'] == '1' ? 'selected' : '' }}>{{ __('Platite') }}</option>
                                            <option value="2" {{ isset($condtitions['status']) && $condtitions['status'] == '2' ? 'selected' : '' }}>{{ __('La curier') }}</option>
                                            <option value="3" {{ isset($condtitions['status']) && $condtitions['status'] == '3' ? 'selected' : '' }}>{{ __('La') }} {{ config('app.name') }}</option>
                                            <option value="4" {{ isset($condtitions['status']) && $condtitions['status'] == '4' ? 'selected' : '' }}>{{ __('Neplatite') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-12 md:col-span-2 self-end pb-1">
                                        <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black disabled:opacity-25 transition">{{ __('Cauta') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <x-jet-table>
                            {{ $repayments->links() }}

                            <x-slot name="thead">
                                @forelse($repayments as $repayment)
                                <x-jet-tr location='thead' bg="black">
                                    <x-jet-td location='thead' border="black" class="rows2">{{ __('Data') }}<br>{{ __('comanda') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black" class="rows2">{{ __('Destinatar') }}<br>{{ __('comanda') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('AWB') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Suma') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black" class="rows2">{{ __('Titular') }}<br>{{ __('cont') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Cont') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black" class="sm:text-center badge rows2">{{ __('Status') }}<br>{{ __('plata') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black" class="sm:text-center badge rows2">{{ __('Status') }}<br>{{ __('ramburs') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black" class="sm:text-center">{{ __('Optiuni') }}</x-jet-td>
                                </x-jet-tr>
                                @empty
                                <x-jet-tr location='thead' bg="black">
                                    <x-jet-td location='thead' border="black">{{ __('Data comanda') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Destinatar comanda') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('AWB') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Suma') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Titular cont') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Cont') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Status plata') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Status ramburs') }}</x-jet-td>
                                    <x-jet-td location='thead' border="black">{{ __('Optiuni') }}</x-jet-td>
                                </x-jet-tr>
                                @endif
                            </x-slot>

                            @forelse($repayments as $repayment)
                            <x-jet-tr>
                                @php $livrare = $repayment->livrare; @endphp
                                <x-jet-td class="rows2">{{ Carbon\Carbon::parse($livrare->created_at)->format('d/m/Y') }}</x-jet-td>
                                <x-jet-td class="rows2">{{ $livrare->receiver->name }}</span><br>{{ $livrare->receiver->locality }}</x-jet-td>
                                <x-jet-td>{{ $repayment->awb }}</x-jet-td>
                                <x-jet-td>{{ $repayment->total }} RON</x-jet-td>
                                <x-jet-td class="rows2">{{ $repayment->titular_cont }}</x-jet-td>
                                <x-jet-td>{{ $repayment->iban }}</x-jet-td>
                                <x-jet-td class="sm:text-center badge rows2">
                                    @if($repayment->status == 1 && $repayment->type == 2 || $repayment->type == 3)
                                        <span class="bg-green-100 text-green-600 py-2 px-3 rounded-full text-sm inline-block">{{ __('Platita') }}</span>
                                    @elseif($repayment->status == 0 && $livrare->status == 5)
                                        <span class="bg-red-200 text-red-600 py-2 px-3 rounded-full text-sm inline-block">{{ __('Anulata') }}</span>
                                    @else
                                        <span class="bg-red-200 text-red-600 py-2 px-3 rounded-full text-sm inline-block">{{ __('Neplatita') }}</span>
                                    @endif
                                </x-jet-td>
                                <x-jet-td class="sm:text-center badge rows2">
                                    @if($repayment->status == 1)
                                        @switch($repayment->type)
                                            @case('1')
                                                <span class="bg-yellow-200 text-yellow-600 py-2 px-3 rounded-full text-sm inline-block">{{ __('Primit de curier') }}</span>
                                                @break
                                            @case('2')
                                                <span class="bg-blue-200 text-blue-600 py-2 px-3 rounded-full text-sm inline-block">{{ __('Primit de') }} {{ config('app.name') }}</span>
                                                @break
                                            @case('3')
                                                <span class="bg-green-200 text-green-600 py-2 px-3 rounded-full text-sm inline-block">{{ __('Virat clientului') }}</span>
                                                @break
                                        @endswitch
                                    @else
                                        <span class="bg-red-200 text-red-600 py-2 px-3 rounded-full text-sm inline-block">{{ __('Neplatita') }}</span>
                                    @endif
                                </x-jet-td>
                                <x-jet-td>
                                    @if($repayment->status == '1' && $repayment->type == '2')
                                        <form method="POST" action="{{ route('admin.repayments.complete', $repayment->id) }}" class="inline">
                                            @csrf
                                            <a href="{{ route('admin.repayments.complete', $repayment->id) }}" class="text-green-500" onclick="event.preventDefault();
                                                this.closest('form').submit();" target="repayment_{{ $repayment->id }}"><i class="fas fa-check"></i></a>
                                        </form>
                                    @endif
                                </x-jet-td>
                            </x-jet-tr>
                            @empty
                            <x-jet-tr class="border-0" style="height: 100%;">
                                <x-jet-td colspan="10" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasit nici un ramburs') }}</x-jet-td>
                            </x-jet-tr>
                            @endif

                            <x-slot name="pagination">
                                @if($repayments)
                                    {{ $repayments->links() }}
                                @endif
                            </x-slot>
                        </x-jet-table>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
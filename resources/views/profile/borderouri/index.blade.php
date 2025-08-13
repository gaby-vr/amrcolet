<x-jet-dashboard :title="$title ?? ''">
    <x-jet-table>
        <div class="row col-span-12">
            <x-jet-validation-errors class="bg-red-100 text-white text-sm px-4 py-3 mb-4 rounded-md shadow-sm" role="alert" />
            <p class="col-span-12 mt-2">{{ __('Cauta borderouri') }}:</p>
            <form action="{{ route('dashboard.borderouri.show') }}" method="get">
                <div class="-mx-2 mt-1 col-span-12">
                    <div class="input-field col s12 m3 l2">
                        <x-jet-label value="{{ __('Data inceput') }}" />
                        <x-jet-input type="text" class="datepicker" value="{{ $condtitions['from'] ?? '' }}" placeholder="" id="from" name="from" />
                    </div>
                    <div class="input-field col s12 m3 l2">
                        <x-jet-label value="{{ __('Data sfarsit') }}" />
                        <x-jet-input type="text" class="datepicker" value="{{ $condtitions['to'] ?? '' }}" placeholder="" id="to" name="to" />
                    </div>
                    <div class="input-field col s12 m3 l2">
                        <x-jet-label value="{{ __('Platit incepand cu') }}" />
                        <x-jet-input type="text" class="datepicker" value="{{ $condtitions['payed_from'] ?? '' }}" placeholder="" id="payed_from" name="payed_from" />
                    </div>
                    <div class="input-field col s12 m3 l2">
                        <x-jet-label value="{{ __('Platit pana la') }}" />
                        <x-jet-input type="text" class="datepicker" value="{{ $condtitions['payed_to'] ?? '' }}" placeholder="" id="payed_to" name="payed_to" />
                    </div>
                    <div class="input-field col s12 m2">
                        <div class="input-field col s12">
                            <button type="submit" class="btn blue darken-2 waves-effect waves-light">{{ __('Cauta') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <h5 class="col-span-12 mt-2"><i>{{ $title }}</i></h5>
        <x-slot name="thead">
            @forelse($items as $item)
            <x-jet-tr location='thead'>
                <x-jet-td location='thead'>{{ __('Data inceput') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Data sfarsit') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Total') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Platit pe') }}</x-jet-td>
                <x-jet-td class="sm:text-center" location='thead'>{{ __('Optiuni') }}</x-jet-td>
            </x-jet-tr>
            @empty
            <x-jet-tr location='thead'>
                <x-jet-td location='thead'>{{ __('Data inceput') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Data sfarsit') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Total') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Platit pe') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Optiuni') }}</x-jet-td>
            </x-jet-tr>
            @endif
        </x-slot>

        @forelse($items as $item)
        <x-jet-tr>
            <x-jet-td>{{ $item->transformDate('start_date') }}</x-jet-td>
            <x-jet-td>{{ $item->transformDate('end_date') }}</x-jet-td>
            <x-jet-td>{{ $item->total }} RON</x-jet-td>
            <x-jet-td>{{ $item->transformDate('payed_at') }}</x-jet-td>
            <x-jet-td class="sm:text-center">
                <a href="{{ route('dashboard.borderouri.excel', ['borderou' => $item->id]) }}" title="{{ __('Descarca borderou') }}">
                    <i class="far fa-lg fa-file-excel text-green-500"></i>
                </a>
            </x-jet-td>
        </x-jet-tr>
        @empty
        <x-jet-tr class="border-0" style="height: 100%;">
            <x-jet-td colspan="5" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasita nici o factura') }}</x-jet-td>
        </x-jet-tr>
        @endif

        <x-slot name="pagination">
            @if($items)
                {{ $items->links() }}
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
</x-jet-dashboard>


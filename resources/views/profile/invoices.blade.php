<x-jet-table>
    <div class="row col-span-12">
        <p class="col-span-12 mt-2">{{ __('Cauta facturi platite intre') }}:</p>
        <form action="{{ route('dashboard.invoices.show') }}" method="get">
            <div class="-mx-2 mt-1 col-span-12">
                <div class="input-field col s12 m3">
                    <x-jet-label value="{{ __('Data inceput') }}" />
                    <x-jet-input type="text" class="datepicker" value="{{ $condtitions['from'] ?? '' }}" placeholder="" id="from" name="from" />
                </div>
                <div class="input-field col s12 m3">
                    <x-jet-label value="{{ __('Data sfarsit') }}" />
                    <x-jet-input type="text" class="datepicker" value="{{ $condtitions['to'] ?? '' }}" placeholder="" id="to" name="to" />
                </div>
                <div class="input-field col s12 m2">
                    <div class="input-field col s12">
                        <button type="submit" class="btn blue darken-2 waves-effect waves-light">{{ __('Cauta') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <h5 class="col-span-12 mt-2"><i>{{ __('Tabel facturi') }}</i></h5>
    <x-slot name="thead">
        @forelse($invoices as $address)
        <x-jet-tr location='thead'>
            <x-jet-td location='thead'>{{ __('Numar') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Livrare') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Total') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Emis in') }}</x-jet-td>
            <x-jet-td class="sm:text-center" style="height: 55px; line-height: 32px;" location='thead'>{{ __('Status') }}</x-jet-td>
            <x-jet-td class="sm:text-center" location='thead'>{{ __('Optiuni') }}</x-jet-td>
        </x-jet-tr>
        @empty
        <x-jet-tr location='thead'>
            <x-jet-td location='thead'>{{ __('Numar') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Livrare') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Total') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Emis in') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Status') }}</x-jet-td>
            <x-jet-td location='thead'>{{ __('Optiuni') }}</x-jet-td>
        </x-jet-tr>
        @endif
    </x-slot>

    @forelse($invoices as $invoice)
    <x-jet-tr>
        <x-jet-td>{{ $invoice->series }}{{ $invoice->number }}</x-jet-td>
        <x-jet-td>
            @if($invoice->meta('created_by_admin') == '1')
                {{ __('Creata manual de catre admin') }}
            @elseif($invoice->livrare_id == 0)
                {{ __('Reincarcare cont cu credite') }}
            @elseif($invoice->storned != null)
                {{ __('Stornare factura') }} {{ $invoice->storned->series }}{{ $invoice->storned->number }}
            @else
                @foreach($invoice->livrari as $index => $livrare)
                    @if($index > 0)
                       , 
                    @endif
                    <a href="{{ route('dashboard.orders.show', ['livrare' => $livrare->id]) }}">#{{ $livrare->id }}</a>
                @endforeach
            @endif
        </x-jet-td>
        <x-jet-td>{{ $invoice->total }} RON</x-jet-td>
        <x-jet-td>{{ $invoice->transformDate('payed_on') }}</x-jet-td>
        <x-jet-td class="sm:text-center">
            <x-jet-invoice-pill :invoice="$invoice" />
        </x-jet-td>
        <x-jet-td class="sm:text-center">
            @if($invoice->status == '1' || $invoice->status == '3')
            <a href="{{ route('dashboard.invoices.pdf', ['invoice' => $invoice->id]) }}" target="Factura" title="{{ __('Factura') }} {{ $invoice->series }}{{ $invoice->number }}">
                <i class="far fa-file-pdf fa-lg text-red-500"></i>
            </a>
            @endif
        </x-jet-td>
    </x-jet-tr>
    @empty
    <x-jet-tr class="border-0" style="height: 100%;">
        <x-jet-td colspan="6" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasita nici o factura') }}</x-jet-td>
    </x-jet-tr>
    @endif

    <x-slot name="pagination">
        @if($invoices)
            {{ $invoices->links() }}
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
@if($has_invoice_info)
    <x-jet-table>
        <h5 class="col-span-12 mt-2"><i>{{ __('Istoric plati') }}</i></h5>
        @if(session()->has('error'))
            <div class="card-alert card gradient-45deg-red-pink">
                <div class="card-content white-text">
                    <p><i class="material-icons">error</i>{{ session()->get('error') }}</p>
                </div>
                <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        <x-slot name="thead">
            @forelse($invoices as $invoice)
            <x-jet-tr location='thead'>
                <x-jet-td location='thead'>{{ __('Numar') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Total') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Data') }}</x-jet-td>
                <x-jet-td class="sm:text-center" style="height: 55px; line-height: 32px;" location='thead'>{{ __('Status') }}</x-jet-td>
                <x-jet-td class="sm:text-center" location='thead'>{{ __('Optiuni') }}</x-jet-td>
            </x-jet-tr>
            @empty
            <x-jet-tr location='thead'>
                <x-jet-td location='thead'>{{ __('Numar') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Total') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Data') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Status') }}</x-jet-td>
                <x-jet-td location='thead'>{{ __('Optiuni') }}</x-jet-td>
            </x-jet-tr>
            @endif
        </x-slot>

        @forelse($invoices as $invoice)
        <x-jet-tr>
            <x-jet-td>{{ $invoice->series }}{{ $invoice->number }}</x-jet-td>
            <x-jet-td>{{ $invoice->total }} RON</x-jet-td>
            <x-jet-td>{{ $invoice->payed_on }}</x-jet-td>
            <x-jet-td class="sm:text-center">
                @if($invoice->status == 1)
                    <span class="chip green lighten-5 m-0">
                        <span class="green-text">{{ __('Confirmata') }}</span>
                    </span>
                @elseif($invoice->status == 2)
                    <span class="chip red lighten-2 m-0">
                        <span class="red-text">{{ __('Anulata') }}</span>
                    </span>
                @elseif($invoice->status == 3)
                    <span class="chip blue lighten-2 m-0">
                        <span class="blue-text">{{ __('Creditata') }}</span>
                    </span>
                @elseif($invoice->status == 4)
                    <span class="chip red lighten-2 m-0">
                        <span class="red-text">{{ __('Respinsa') }}</span>
                    </span>
                @else
                    <span class="chip orange lighten-5 m-0">
                        <span class="orange-text">{{ __('Plata in asteptare') }}</span>
                    </span>
                @endif
            </x-jet-td>
            <x-jet-td class="sm:text-center">
                @if($invoice->status == '1')
                <a href="{{ route('dashboard.purse.pdf', ['invoice' => $invoice->id]) }}" target="Factura" title="{{ __('Factura') }} {{ $invoice->series }}{{ $invoice->number }}">
                    <i class="far fa-file-pdf fa-lg text-red-500"></i>
                </a>
                @endif
            </x-jet-td>
        </x-jet-tr>
        @empty
        <x-jet-tr class="border-0" style="height: 100%;">
            <x-jet-td colspan="5" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu ai salvat nici o adresa') }}</x-jet-td>
        </x-jet-tr>
        @endif

        <x-slot name="pagination">
            @if($invoices)
                {{ $invoices->links() }}
            @endif
        </x-slot>
    </x-jet-table>
    @if(!$negative_balance)
        <x-jet-form-section-simple submit="{{ $negative_balance ? route('dashboard.purse.pay.orders') : route('dashboard.purse.buy')  }}">
            <x-slot name="form">
                <div class="row m-0 col-span-1">
                    <div class="px-2">
                        <h5><i>{{ __('Total cont') }}</i></h5>
                        <p>{{ __('Creditul valabil in cont.') }}</p>
                    </div>
                    <div class="p-2">
                        <div class="flex flex-row items-center">
                            <div class="flex-shrink pr-4">
                                <div class="rounded p-3 bg-green-600"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                            </div>
                            <div class="flex-1 text-left">
                                <h3 class="font-bold text-3xl text-gray-600 my-0">{{ $account_balance != '' ? $account_balance : '0' }} RON</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row m-0 col-span-3">
                    <x-jet-validation-errors />
                    @if($negative_balance)
                        <div class="px-2">
                            <h5><i>{{ __('Achita facturile') }}</i></h5>
                            <p>{{ __('Confirmarea plati va dura cateva minute.') }}</p>
                        </div>
                        @csrf
                        <div class="input-field col s12 m8">
                            <x-jet-button wire:loading.attr="disabled">
                                {{ __('Plateste facturile') }}
                            </x-jet-button>
                            <x-jet-input-error for="money" class="mt-2 errorTxt1" />
                        </div>
                    @else
                        <div class="px-2">
                            <h5><i>{{ __('Reincarcare cont') }}</i></h5>
                            <p>{{ __('Confirmarea plati va dura cateva minute.') }}</p>
                        </div>
                        @csrf
                        <div class="input-field col s12 m8">
                            <x-jet-label for="money" >{{ __('Suma') }} <span class="red-text">*</span></x-jet-label>
                            <x-jet-input id="money" type="text" class="block w-full {{ $errors->has('money') ? 'invalid' : '' }}" name="money" value="{{ old('money') ?? $this->state['money'] ?? '' }}" required />
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                    RON
                                </div>
                            <small class="errorTxt1 float-right red-text"></small>
                            <x-jet-input-error for="money" class="mt-2 errorTxt1" />
                        </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Saved.') }}
                </x-jet-action-message>
                @if(!$negative_balance)
                    <x-jet-button wire:loading.attr="disabled">
                        {{ __('Reincarca') }}
                    </x-jet-button>
                @endif
            </x-slot>
        </x-jet-form-section-simple>
    @else
        <div class="form-wrapper  p-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
            <div class="row m-0 col-span-1">
                <div class="px-2">
                    <h5><i>{{ __('Total cont') }}</i></h5>
                    <p>{{ __('Creditul valabil in cont.') }}</p>
                </div>
                <div class="p-2">
                    <div class="flex flex-row items-center">
                        <div class="flex-shrink pr-4">
                            <div class="rounded p-3 bg-green-600"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                        </div>
                        <div class="flex-1 text-left">
                            <h3 class="font-bold text-3xl text-gray-600 my-0">{{ $account_balance != '' ? $account_balance : '0' }} RON</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="card-alert card gradient-45deg-amber-amber mx-auto max-w-md">
        <div class="card-content white-text">
            <span class="card-title white-text darken-1"><i class="material-icons">notifications</i> {{ __('Nu ai salvate datele de facturare') }}</span>
            <p>{{ __('Iti poti salva datele de facturare completand formularul de pe pagina') }} <b>{{ __('Date facturare') }}</b>.</p>
        </div>
        {{-- <div class="card-action gradient-45deg-amber-amber darken-2">
            <a class="btn-flat waves-effect purple white-text mb-1"><i class="material-icons left">check</i> Ok</a>
            <a class="btn-flat btn waves-effect red accent-2 white-text mb-1"><i class="material-icons left">clear</i> Cancel</a>
        </div> --}}
        {{-- <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button> --}}
    </div>
@endif
@if(session()->has('form_mobilpay'))
    {!! session()->get('form_mobilpay') !!}
@endif

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-facturare.css') }}">
@if(session()->has('form_mobilpay'))
<link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mat/sweetalert/sweetalert.css') }}">
@endif
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="{{ asset('js/pages/form-facturare.js') }}"></script>
@if(session()->has('form_mobilpay'))
<script src="{{ asset('js/vendors/mat/sweetalert/sweetalert.min.js') }}"></script>
<script type="text/javascript">
    swal({
            title: "{{ __('Veti fi redirectionat in cateva momente.') }}",
            text: "",
            icon: "{{ asset('img/logo.png') }}"
        });
</script>
@endif
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
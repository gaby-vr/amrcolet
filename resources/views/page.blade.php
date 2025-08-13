<x-guest-layout :title="$page->title">
    @push('before-styles')
        @if($page->slug == 'home')
        @endif
    @endpush
    @push('styles')
        @if($page->slug == 'home')
            <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mat/sweetalert/sweetalert.css') }}">
            <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
            <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
            <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-home.css') }}">
        @endif
        @if($page->css)
            <style type="text/css">
                {!! $page->css !!}
            </style>
        @endif
    @endpush
    @push('scripts')
        @if($page->slug == 'home')
            <script src="{{ asset('js/vendors/jquery/jquery.min.js') }}"></script>
            <script src="{{ asset('js/vendors/jquery.ui.autocomplete/jquery-ui.min.js') }}"></script>
            <script src="{{ asset('js/vendors/mat/sweetalert/sweetalert.min.js') }}"></script>
            <script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
            <script src="{{ asset('js/plugins/countrySelector/js/countrySelect.min.js') }}"></script>
            {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> --}}
            <script src="{{ asset('js/pages/form-home.js') }}?v=20230426"></script>
            @if(session()->has('orderConfirmed') || isset($orderId) && isset($status))
                <script type="text/javascript">
                    var el = document.createElement('div');
                    @switch($status)
                        @case('1')
                            el.innerHTML = '{!! __('Veți primi un email de confirmare în care veți găsi atașată factura, <b>dacă plata a fost efectuată cu succes</b>.') !!}';
                            @if($livrare && auth()->check() && $livrare->api_shipment_awb)
                                el.innerHTML += '<a href="{{ route('dashboard.orders.awb', $livrare) }}" class="inline-block mt-2 px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:shadow-outline-blue-500 disabled:opacity-25 transition">{{ __('Descarcă AWB') }}</a>';
                            @endif
                            swal({
                                    title: "{{ __('Comanda a fost confirmată') }}",
                                    content: el,
                                    icon: "{{ asset('img/logo.png') }}"
                                });
                            @break

                        @case('2')
                            el.innerHTML = '{!! __('Comanda nu a putut fi efectuată. Vă rugăm reîncercați mai târziu.') !!}';
                            swal({
                                    title: "{{ __('Comanda a fost anulată') }}",
                                    content: el,
                                    icon: "{{ asset('img/logo.png') }}"
                                });
                            @break
                        @default
                            el.innerHTML = '{!! __('Comanda nu a putut fi efectuată. Vă rugăm reîncercați mai târziu.') !!}';
                            swal({
                                    title: "{{ __('Comanda a fost respinsă') }}",
                                    content: el,
                                    icon: "{{ asset('img/logo.png') }}"
                                });
                    @endswitch
                </script>
                @php session()->pull('orderConfirmed') @endphp
            @endif
            @if(session()->has('expiration_date_passed'))
                <script type="text/javascript">
                    var el = document.createElement('div');
                    el.innerHTML = '{!! __(session()->get('expiration_date_passed')) !!}'
                    swal({
                            title: "{{ __('Comanda nu a fost trimisa') }}",
                            content: el,
                            icon: "error"
                        });
                </script>
            @endif
            @isset($dataOrder)
                <script type="text/javascript">
                    swal({
                        title: "{{ __('Veți fi redirecționat în câteva momente.') }}",
                        text: "",
                        icon: "{{ asset('img/logo.png') }}"
                    });
                </script>
            @endisset
        @endif
        @if($errors->any())
            <script type="text/javascript">
                var el = document.createElement('div');
                el.innerHTML += '<ul class="mt-3 list-disc list-inside text-sm text-red-600">';
                @foreach ($errors->all() as $error)
                    el.innerHTML += '<li>{!! $error !!}</li>';
                @endforeach
                el.innerHTML += '</ul>';
                swal({
                        title: "{{ __('Whoops! Something went wrong.') }}",
                        content: el,
                        icon: "error"
                    });
            </script>
        @endif
        @if(session()->has('success'))
            <script type="text/javascript">
                var el = document.createElement('div');
                el.innerHTML = '{!! __(session()->get('success')) !!}'
                swal({
                        title: "{{ __('Success') }}",
                        content: el,
                        icon: "success"
                    });
            </script>
        @endif
    @endpush

    <x-jet-banner />
    @livewire('navigation-menu')
    <div class="pt-16">
        {!! replace_csrf_placeholder($page->html) !!}
    </div>
    @isset($dataOrder)
        {!! $dataOrder['form'] !!}
    @endisset
    @livewire('footer')
</x-guest-layout>

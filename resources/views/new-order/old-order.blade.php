@push('styles')
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/materialize.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/style.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mat/materialize-stepper/materialize-stepper.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('js/vendors/mat/select2/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('js/vendors/mat/select2/select2-materialize.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/intlTelInput/css/intlTelInput.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/form-wizard.css') }}">
<style>
    .gradient {
        background: linear-gradient(90deg, #0038cc 0%, #0038cc 100%);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('fonts/fontawesome/js/all.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/vendors.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/plugins.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/materialize-stepper/materialize-stepper.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/formatter/formatter.js') }}"></script>
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.min.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{ asset('js/pages/form-wizard.js') }}"></script>
@if((session()->has('to_send_email') || session()->has('sender')) && !isset($repeat))
<script src="{{ asset('js/vendors/mat/sweetalert/sweetalert.min.js') }}"></script>
<script type="text/javascript">
    swal({
        title: "{{ __('Resetare comandă') }}",
        text: "{{ __('Comanda are date completate. Doriți să o resetați?.') }}",
        icon: "{{ asset('img/logo.png') }}",
        buttons: {
            cancel: "{{ __('Nu, continui comanda') }}",
            delete: "{{ __('Da, resetează datele') }}",
        }
    }).then((result) => {
        if (result) {
            window.location.href = '{{ route('order.free.session') }}';
        }
    });
</script>
@endif
@endpush

<x-guest-layout>
    <x-jet-banner />
    @livewire('navigation-menu')
    <div id="page-order" class="pt-28 pb-4 bg-gray-100">
        <div class="row max-w-7xl mx-auto">
        	<div class="col s12">
                <div class="card">
                    <div class="card-content pb-0">
                        <div id="form-title" class="card-header mb-2">
                            <h4 class="card-title">{{ __('Comanda noua') }}</h4>
                        </div>

                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                @foreach ($errors->all() as $error)
                                    <span class="block sm:inline">{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if (auth()->check() && auth()->user()->role == '2' && auth()->user()->info('expiration_date') != '' && auth()->user()->info('expiration_date') < date('Y-m-d'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                    <span class="block sm:inline">{{ __('Perioada de comanda fara plata sa terminat, nu vei putea face comenzi pana nu vei achita comenzile neplatite.') }}</span>
                            </div>
                        @endif

                        <form id="form-body" method="post" action="{{ route('order.submit') }}">
                            @csrf
                            <ul class="stepper horizontal @auth large @endauth" id="horizStepper" data-url="{{ route('order.get.invoice') }}">
                                @guest
                                <li class="step active">
                                    <div class="step-title waves-effect">Adresa de email</div>
                                    <div class="step-content px-2">
                                        <div class="row" style="margin: auto 0;">
                                            <div class="card red w-100 d-inline-block">
                                                <div class="card-content white-text">
                                                    <p class="card-pay-notification"> Pentru finalizarea acestei comenzi va fi necesara plata cu card bancar a comenzii. <br><b>Nu exista posibilitatea platii la curier a serviciului.</b></p>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="input-field s6">
                                                <label for="to_send_email">E-mail <span class="red-text">*</span></label>
                                                <input type="text" id="to_send_email" name="to_send_email" class="" value="{{ old('to_send_email', session()->get('to_send_email')) }}" autocomplete="off" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <div class="col l2 m12 s12 mb-3 max-w-full">
                                            <button class="waves-effect waves dark btn btn-primary blue next-step" style="padding-right: 1rem; padding-left: 1rem;" type="submit">
                                                Urmator
                                                <i class="material-icons right">arrow_forward</i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                                @endguest
                                <li class="step large">
                                    <div class="step-title waves-effect">Expeditor</div>
                                    <div class="step-content large w-h-100 px-2" data-action="{{ route('order.expeditor') }}">
                                        @auth
                                            <div class="row">
                                                <div class="input-field col s12">
                                                    <select class="favorite-address" data-person="sender" data-url="{{ route('dashboard.addresses.get') }}">
                                                        <option value="">Selecteaza o adresa</option>
                                                        @foreach(Auth::user()->favoriteAddresses as $address)
                                                            <option value="{{ $address->id }}" data-id="{{ $address->id }}">{{ $address->address_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="start-pickup-hour">Adrese favorite</label>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                            </div>
                                        @endauth
                                        <h6>Tara</h6>
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <label for="sender_country">Tara <span class="red-text">*</span></label>
                                                <input type="text" class="" placeholder="" id="sender_country" name="sender[country]" value="{{ old('sender.country', session()->get('sender.country')) }}" style="box-sizing: border-box;" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                                <input id="sender_country_code" type="hidden" name="sender[country_code]" value="{{ old('sender.country_code', session()->get('sender.country_code')) ?? 'ro' }}" required>
                                            </div>
                                        </div>
                                        <h6>Persoana de contact</h6>
                                        <div class="row">
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_name">Nume persoana <span class="red-text">*</span></label>
                                                <input type="text" class="" id="sender_name" name="sender[name]" value="{{ old('sender.name', session()->get('sender.name')) }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_phone" class="active">Telefon <span class="red-text">*</span></label>
                                                <input type="text" class="" placeholder="" id="sender_phone" name="sender[phone]" value="{{ old('sender.phone_full', session()->get('sender.phone_full')) }}" style="box-sizing: border-box;" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_phone_2">Telefon 2 </label>
                                                <input type="text" class="" placeholder="" id="sender_phone_2" name="sender[phone_2]" value="{{ old('sender.phone_2_full', session()->get('sender.phone_2_full')) }}" style="box-sizing: border-box;">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_company">Companie</label>
                                                <input type="text" class="" id="sender_company" name="sender[company]" value="{{ old('sender.company', session()->get('sender.company')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_email">E-mail <span class="red-text">*</span></label>
                                                <input type="email" class="" id="sender_email" name="sender[email]" value="{{ old('sender.email', session()->get('sender.email')) }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <h6>Adresa</h6>
                                        <div class="row">
                                            <div class="input-field col l2 m6 s12">
                                                <label for="sender_postcode">Cod postal<span class="red-text">*</span></label>
                                                <input type="text" class="" id="sender_postcode" name="sender[postcode]" value="{{ old('sender.postcode', session()->get('sender.postcode')) }}" data-person="sender" data-postcode="1" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_locality">Localitate <span class="red-text">*</span></label>
                                                <input type="text" class="" id="sender_locality" name="sender[locality]" value="{{ old('sender.locality', session()->get('sender.locality')) }}" data-person="sender" data-url="{{ route('order.get.county') }}" autocomplete="off" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l2 m6 s12">
                                                <label for="sender_county">Judet <span class="red-text">*</span></label>
                                                <input type="text" class="" id="sender_county" name="sender[county]" value="{{ old('sender.county', session()->get('sender.county')) }}" readonly data-person="sender" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_street">Strada <span class="red-text">*</span></label>
                                                <input type="text" class="" id="sender_street" name="sender[street]" value="{{ old('sender.street', session()->get('sender.street')) }}" data-person="sender" data-url="{{ route('order.get.street') }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="sender_street_nr">Nr. <span class="red-text">*</span></label>
                                                <input type="text" class="" id="sender_street_nr" name="sender[street_nr]" value="{{ old('sender.street_nr', session()->get('sender.street_nr')) }}" data-person="sender" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="sender_bl_code">Bl. </label>
                                                <input type="text" class="" id="sender_bl_code" name="sender[bl_code]" value="{{ old('sender.bl_code', session()->get('sender.bl_code')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="sender_bl_letter">Sc. </label>
                                                <input type="text" class="" id="sender_bl_letter" name="sender[bl_letter]" value="{{ old('sender.bl_letter', session()->get('sender.bl_letter')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="sender_intercom">Interfon </label>
                                                <input type="text" class="" id="sender_intercom" name="sender[intercom]" value="{{ old('sender.intercom', session()->get('sender.intercom')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="sender_floor">Et. </label>
                                                <input type="text" class="" id="sender_floor" name="sender[floor]" value="{{ old('sender.floor', session()->get('sender.floor')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="sender_apartment">Ap. </label>
                                                <input type="text" class="" id="sender_apartment" name="sender[apartment]" value="{{ old('sender.apartment', session()->get('sender.apartment')) }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="sender_landmark">Reper </label>
                                                <input type="text" class="" id="sender_landmark" name="sender[landmark]" value="{{ old('sender.landmark', session()->get('sender.landmark')) }}" data-length="60">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col s12">
                                                <label for="sender_more_information">Alte informatii </label>
                                                <input type="text" class="" id="sender_more_information" name="sender[more_information]" value="{{ old('sender.more_information', session()->get('sender.more_information')) }}" data-length="100">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <div class="step-actions" style="position:relative;">
                                            <div class="row m-0">
                                                @guest
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="btn btn-light blue previous-step" style="padding-right: 1rem; padding-left: 1rem;">
                                                        <i class="material-icons left">arrow_back</i>
                                                        Anterior
                                                    </button>
                                                </div>
                                                @endguest
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="waves-effect waves dark btn btn-primary blue next-step" style="padding-right: 1rem; padding-left: 1rem;" type="submit">
                                                        Urmator
                                                        <i class="material-icons right">arrow_forward</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="step large">
                                    <div class="step-title waves-effect">Destinatar</div>
                                    <div class="step-content large w-h-100 px-2" data-action="{{ route('order.receiver') }}">
                                        @auth
                                            <div class="row">
                                                <div class="input-field col s12">
                                                    <select class="favorite-address" data-person="receiver" data-url="{{ route('dashboard.addresses.get') }}">
                                                        <option value="" data-url="">Selecteaza o adresa</option>
                                                        @foreach(Auth::user()->favoriteAddresses as $address)
                                                            <option value="{{ $address->id }}" data-id="{{ $address->id }}">{{ $address->address_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="start-pickup-hour">Adrese favorite</label>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                            </div>
                                        @endauth
                                        <h6>Tara</h6>
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <label for="receiver_country">Tara <span class="red-text">*</span></label>
                                                <input type="text" placeholder="" class="" id="receiver_country" name="receiver[country]" value="{{ old('receiver.country', session()->get('receiver.country')) }}" style="box-sizing: border-box;" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                                <input id="receiver_country_code" type="hidden" name="receiver[country_code]" value="{{ old('receiver.country_code', session()->get('receiver.country_code')) ?? 'ro' }}" required>
                                            </div>
                                        </div>
                                        <h6>Persoana de contact</h6>
                                        <div class="row">
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_name">Nume persoana <span class="red-text">*</span></label>
                                                <input type="text" class="" id="receiver_name" name="receiver[name]" value="{{ old('receiver.name', session()->get('receiver.name')) }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_phone">Telefon <span class="red-text">*</span></label>
                                                <input type="text" placeholder="" class="" id="receiver_phone" name="receiver[phone]" value="{{ old('receiver.phone_full', session()->get('receiver.phone_full')) }}" style="box-sizing: border-box;" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_phone_2">Telefon 2 </label>
                                                <input type="text" placeholder="" class="" id="receiver_phone_2" name="receiver[phone_2]" value="{{ old('receiver.phone_2_full', session()->get('receiver.phone_2_full')) }}" style="box-sizing: border-box;">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_company">Companie</label>
                                                <input type="text" class="" id="receiver_company" name="receiver[company]" value="{{ old('receiver.company', session()->get('receiver.company')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_email">E-mail </label>
                                                <input type="email" class="" id="receiver_email" name="receiver[email]" value="{{ old('receiver.email', session()->get('receiver.email')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <h6>Adresa</h6>
                                        <div class="row">
                                            <div class="input-field col l2 m6 s12">
                                                <label for="receiver_postcode">Cod postal<span class="red-text">*</span></label>
                                                <input type="text" class="" id="receiver_postcode" name="receiver[postcode]" value="{{ old('receiver.postcode', session()->get('receiver.postcode')) }}" data-person="receiver" data-postcode="1" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_locality">Localitate <span class="red-text">*</span></label>
                                                <input type="text" class="" id="receiver_locality" name="receiver[locality]" value="{{ old('receiver.locality', session()->get('receiver.locality')) }}" data-person="receiver" data-url="{{ route('order.get.county') }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l2 m6 s12">
                                                <label for="receiver_county">Judet <span class="red-text">*</span></label>
                                                <input type="text" class="" id="receiver_county" name="receiver[county]" value="{{ old('receiver.county', session()->get('receiver.county')) }}" readonly data-person="receiver" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_street">Strada</label>
                                                <input type="text" class="" id="receiver_street" name="receiver[street]" value="{{ old('receiver.street', session()->get('receiver.street')) }}" data-person="receiver" data-url="{{ route('order.get.street') }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="receiver_street_nr">Nr. <span class="red-text">*</span></label>
                                                <input type="text" class="" id="receiver_street_nr" name="receiver[street_nr]" value="{{ old('receiver.street_nr', session()->get('receiver.street_nr')) }}" data-person="receiver" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="receiver_bl_code">Bl. </label>
                                                <input type="text" class="" id="receiver_bl_code" name="receiver[bl_code]" value="{{ old('receiver.bl_code', session()->get('receiver.bl_code')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="receiver_bl_letter">Sc. </label>
                                                <input type="text" class="" id="receiver_bl_letter" name="receiver[bl_letter]" value="{{ old('receiver.bl_letter', session()->get('receiver.bl_letter')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="receiver_intercom">Interfon </label>
                                                <input type="text" class="" id="receiver_intercom" name="receiver[intercom]" value="{{ old('receiver.intercom', session()->get('receiver.intercom')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="receiver_floor">Et. </label>
                                                <input type="text" class="" id="receiver_floor" name="receiver[floor]" value="{{ old('receiver.floor', session()->get('receiver.floor')) }}">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l1 m3 s12">
                                                <label for="receiver_apartment">Ap. </label>
                                                <input type="text" class="" id="receiver_apartment" name="receiver[apartment]" value="{{ old('receiver.apartment', session()->get('receiver.apartment')) }}" required>
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col l4 m6 s12">
                                                <label for="receiver_landmark">Reper </label>
                                                <input type="text" class="" id="receiver_landmark" name="receiver[landmark]" value="{{ old('receiver.landmark', session()->get('receiver.landmark')) }}" data-length="60">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col s12">
                                                <label for="receiver_more_information">Alte informatii </label>
                                                <input type="text" class="" id="receiver_more_information" name="receiver[more_information]" value="{{ old('receiver.more_information', session()->get('receiver.more_information')) }}" data-length="100">
                                                <small class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <div class="row m-0">
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="btn btn-light blue previous-step" style="padding-right: 1rem; padding-left: 1rem;">
                                                        <i class="material-icons left">arrow_back</i>
                                                        Anterior
                                                    </button>
                                                </div>
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="waves-effect waves dark btn btn-primary blue next-step" style="padding-right: 1rem; padding-left: 1rem;" type="submit">
                                                        Urmator
                                                        <i class="material-icons right">arrow_forward</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="step extra-large">
                                    <div class="step-title waves-effect">Informatii Suplimentare</div>
                                    <div class="step-content px-2" data-action="{{ route('order.package') }}">
                                        @auth
                                            <div class="row">
                                                <div class="input-field col s12">
                                                    <select class="favorite-template" data-url="{{ route('dashboard.templates.get') }}">
                                                        @php $bool = true; @endphp
                                                        @forelse(Auth::user()->favoriteTemplates as $template)
                                                            @if($bool)
                                                                <option value="">Selecteaza un sablon</option>
                                                                @php $bool = false; @endphp
                                                            @endif
                                                            <option value="{{ $template->id }}" data-id="{{ $template->id }}">{{ $template->name }} <i class="right">{{ $template->nr_colete }}</i></option>
                                                        @empty
                                                            <option value="">Nu ai salvat nici un sablon</option>
                                                        @endforelse
                                                    </select>
                                                    <label>Sabloane favorite</label>
                                                </div>
                                            </div>
                                        @endauth
                                        <h6>Tip pachet</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12 radio-type">
                                                <p class="mt-1">
                                                    <label>
                                                        <input class="with-gap package-type" name="type" type="radio" value="1" {{ old('type', session()->get('package.type')) == '1' || !session()->has('package.type') ? 'checked' : '' }}>
                                                        <span>Colet</span>
                                                    </label>
                                                </p>
                                                <p class="mt-1">
                                                    <label>
                                                        <input class="with-gap package-type" name="type" type="radio" value="2" {{ old('type', session()->get('package.type')) == '2' ? 'checked' : '' }}>
                                                        <span>Plic</span>
                                                    </label>
                                                </p>
                                                <small data-error="type" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <h6>Informatii pachet</h6>
                                        <div class="row">
                                            <div class="input-field col m2 s12 show-colet">
                                                <label for="nr_colete">Numar colete <span class="red-text">*</span></label>
                                                <input type="number" id="nr_colete" class="{{ $errors->has('nr_colete') ? 'invalid' : '' }}" min="1" value="{{ old('nr_colete', session()->get('package.nr_colete')) ?? '1' }}" name="nr_colete">
                                                <small data-error="nr_colete" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col m2 s12">
                                                <label for="content">Continut <span class="red-text">*</span></label>
                                                <input type="text" id="content" class="{{ $errors->has('content') ? 'invalid' : '' }}" value="{{ old('content', session()->get('package.content')) ?? '' }}" name="content" data-length="50">
                                                <small data-error="content" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col s12 m-0 show-plic">
                                                <div class="card-alert card orange lighten-5 m-0">
                                                    <div class="card-content orange-text">
                                                        <p><i class="material-icons">warning</i> <b>Atentie!</b> Plicul poate avea maxim format A4 si greutate de maxim 1kg. In caz contrar folositi optiunea <b>Colet</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-field col s12 m-0 show-colet">
                                                <div class="card-alert card orange lighten-5 m-0 mt-1">
                                                    <div class="card-content orange-text">
                                                        <p>
                                                            <i class="material-icons">warning</i> Va rugam sa masurati si sa declarati exact <b>dimensiunile</b> si <b>greutatea</b>.
                                                            <br>
                                                            <b>Pretul se calculeaza si in functie de dimensiuni!</b>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row input-field col s12 pl-0 pr-0 show-colet colete" data-wrapper="1" data-items="1">
                                                @php $colete = old('nr_colete') ?? session()->get('package.nr_colete') ?? 1; @endphp
                                                @for($i = 0 ; $i < $colete ; $i++)
                                                    <div class="input-field col l12 m6 s12 mt-0 colet-form" data-clone="1">
                                                        <div class="card m-0">
                                                            <div class="card-content pt-0 pb-0 m-0">
                                                                <div class="row">
                                                                    <div class="input-field col l2 m12 s12 card-alert">
                                                                        <h5><b>Colet <span class="nr_colet" data-index="1">{{ $i + 1 }}</span></b><i class="material-icons float-right colet-remove cursor-pointer" style="font-size: 2rem;">close</i></h5>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="weight" class="materialize-input{{ $errors->has('weight.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('weight.'.$i, session()->get('package.weight.'.$i)) }}" placeholder="{{ __('Weight') }}" name="weight[]">
                                                                        <span class="suffix">kg</span>
                                                                        <small data-error="weight[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="length" class="materialize-input{{ $errors->has('length.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('length.'.$i, session()->get('package.length.'.$i)) }}" placeholder="{{ __('Length') }}" name="length[]">
                                                                        <span class="suffix">cm</span>
                                                                        <small data-error="length[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="width" class="materialize-input{{ $errors->has('width.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('width.'.$i, session()->get('package.width.'.$i)) }}" placeholder="{{ __('Width') }}" name="width[]">
                                                                        <span class="suffix">cm</span>
                                                                        <small data-error="width[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="height" class="materialize-input{{ $errors->has('height.'.$i) ? ' invalid' : '' }}" type="text" value="{{ old('height.'.$i, session()->get('package.height.'.$i)) }}" placeholder="{{ __('Height') }}" name="height[]">
                                                                        <span class="suffix">cm</span>
                                                                        <small data-error="height[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="volume" class="materialize-input disabled" type="text" placeholder="Volum" name="volume[]" disabled>
                                                                        <span class="suffix">kg</span>
                                                                        <small data-error="volume[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor
                                                @if($colete == 0)
                                                    <div class="input-field col l12 m6 s12 mt-0 colet-form" data-clone="1">
                                                        <div class="card m-0">
                                                            <div class="card-content pt-0 pb-0 m-0">
                                                                <div class="row">
                                                                    <div class="input-field col l2 m12 s12 card-alert">
                                                                        <h5><b>Colet <span class="nr_colet" data-index="1">1</span></b><i class="material-icons float-right colet-remove cursor-pointer" style="font-size: 2rem;">close</i></h5>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="weight" class="materialize-input" type="text" value="" placeholder="{{ __('Weight') }}" name="weight[]">
                                                                        <span class="suffix">kg</span>
                                                                        <small data-error="weight[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="length" class="materialize-input" type="text" value="" placeholder="{{ __('Length') }}" name="length[]">
                                                                        <span class="suffix">cm</span>
                                                                        <small data-error="length[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="width" class="materialize-input" type="text" value="" placeholder="{{ __('Width') }}" name="width[]">
                                                                        <span class="suffix">cm</span>
                                                                        <small data-error="width[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="height" class="materialize-input" type="text" value="" placeholder="{{ __('Height') }}" name="height[]">
                                                                        <span class="suffix">cm</span>
                                                                        <small data-error="height[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                    <div class="input-field col l2 m12 s12">
                                                                        <input id="volume" class="materialize-input disabled" type="text" placeholder="Volum" name="volume[]" disabled>
                                                                        <span class="suffix">kg</span>
                                                                        <small data-error="volume[]" class="errorTxt1 float-right red-text"></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="input-field col s12 mt-0 mb-0 show-colet">
                                                <div class="card m-0">
                                                    <div class="card-content pt-2 pb-2 m-0">
                                                        <p class="inline-block">
                                                            Total: 
                                                            <i class="fas fa-balance-scale"></i>
                                                            <span class="total-weight" data-name="total_weight">0</span> kg
                                                            &nbsp;
                                                            <i class="fas fa-cubes"></i>
                                                            <span class="total-volume" data-name="total_volume">0</span> kg (volumetric)</sup>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-field col s12 card-alert">
                                                <i class="material-icons" style="font-size: 2rem;">info_outline</i> Vezi conditiile de impachetarea continutului.
                                            </div>
                                        </div>
                                        <h6>Tiparire AWB</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12 mt-1">
                                                <p class="card-alert">
                                                    <label>
                                                        <input class="with-gap" name="awb" type="radio" value="1" {{ old('awb', session()->get('package.awb')) == '1' || !old('awb', session()->get('package.awb')) ? 'checked' : '' }}>
                                                        <span>Voi printa AWB-ul si il voi lipi pe colet</span>
                                                    </label>
                                                </p>
                                                <p class="mt-1">
                                                    <label>
                                                        <input class="with-gap" name="awb" type="radio" value="2" {{ old('awb', session()->get('package.awb')) == '2' ? 'checked' : '' }}>
                                                        <span>Nu voi printa AWB-ul si acesta trebuie tiparit de curier</span>
                                                    </label>
                                                </p>
                                                <small data-error="awb" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <h6>Program ridicare</h6>
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <p>Ridicare: 
                                                    @for($i = 1 ; $i <= 3; $i++)
                                                    @php $pickup_day = date('Y-m-d', strtotime(date('Y-m-d',strtotime('-1 days')).' +'.$i.' Weekday')); @endphp
                                                    <label>
                                                        <input class="with-gap" name="pickup_day" type="radio" value="{{ $pickup_day }}" {{ old('pickup_day', session()->get('package.pickup_day')) == $pickup_day || !old('pickup_day', session()->has('package.pickup_day')) && $i == 1 ? 'checked' : '' }}>
                                                        <span class="text-capitalize">{{ date('Y-m-d', strtotime('-1 days'.' +'.$i.' weekdays')) == date('Y-m-d') ? '(Astazi)' : '' }} {{ strftime('%A %d.%m', strtotime('-1 days'.' +'.$i.' weekdays')) }}</span>
                                                    </label>&nbsp;&nbsp;
                                                    @endfor
                                                </p>
                                                <small data-error="pickup_day" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col s12 mt-0">
                                                <div class="card-alert card blue lighten-5 m-0">
                                                    <div class="card-content blue-text">
                                                        <p><i class="material-icons">info_outline</i> Disponibilitatea preluarii astazi a coletului depinde de firma de curierat aleasa si este limitata de posibilitatea cureirului din zona respectiva de a ridica coletul pana la sfarsitul programului.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-field col m4 s12">
                                                <select name="start_pickup_hour" id="start-pickup-hour">
                                                    @for($i = 8 ; $i <= 15 ; $i++)
                                                    <option value="{{$i}}" {{ old('start_pickup_hour', session()->get('package.start_pickup_hour')) == $i || (!old('start_pickup_hour', session()->get('package.start_pickup_hour')) && Auth::check() && Auth::user()->info('schedule_start_pickup_hour') != '' && Auth::user()->info('schedule_start_pickup_hour') == $i) ? 'selected' : '' }}>{{$i}}:00</option>
                                                    @endfor
                                                </select>
                                                <label for="start-pickup-hour">Colet disponibil pentru ridicare incepand cu ora</label>
                                                <small data-error="start_pickup_hour" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col m4 s12">
                                                <select name="end_pickup_hour" id="end-pickup-hour">
                                                    @for($i = 10 ; $i <= 18 ; $i++)
                                                    <option value="{{$i}}" {{ old('end_pickup_hour', session()->get('package.end_pickup_hour')) == $i || (!old('end_pickup_hour', session()->get('package.end_pickup_hour')) && Auth::check() && Auth::user()->info('schedule_end_pickup_hour') != '' && Auth::user()->info('schedule_end_pickup_hour') == $i) ? 'selected' : (!Auth::check() && $i == 18 ? 'selected' : '') }}>{{$i}}:00</option>
                                                    @endfor
                                                </select>
                                                <label for="end-pickup-hour">Ridicare pana la ora</label>
                                                <small data-error="end_pickup_hour" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                            <div class="input-field col s12 mt-0">
                                                <div class="card-alert card blue lighten-5 m-0">
                                                    <div class="card-content blue-text">
                                                        <p><i class="material-icons">info_outline</i> Ridicarea in intervalul selectat nu este garantata, acesta depinde de disponibilitatea curierului! In cazul in care intervalul nu poate fi onorat, curierul poate veni pentru ridicare oricand in programul de lucru al acestuia.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <h6>Optiuni curier</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12">
                                                <p class="mt-1">
                                                    <div class="switch">
                                                        <label>
                                                            <input name="options[work_saturday]" value="1" type="checkbox" {{ old('options.work_saturday', session()->get('package.options.work_saturday')) ? 'checked' : '' }}>
                                                            <span class="lever"></span>
                                                            <span style="font-size: 1rem;">Livrare sambata</span>
                                                        </label>
                                                    </div>
                                                </p>
                                                <p class="mt-1">
                                                    <div class="switch">
                                                        <label>
                                                            <input name="options[open_when_received]" value="1" type="checkbox" {{ old('options.open_when_received', session()->get('package.options.open_when_received')) ? 'checked' : '' }}>
                                                            <span class="lever"></span>
                                                            <span style="font-size: 1rem;">Deschidere la livrare</span>
                                                        </label>
                                                    </div>
                                                </p>
                                                <p class="mt-1">
                                                    <div class="switch">
                                                        <label>
                                                            <input name="options[retur_document]" value="1" type="checkbox" {{ old('options.retur_document', session()->get('package.options.retur_document')) ? 'checked' : '' }}>
                                                            <span class="lever"></span>
                                                            <span style="font-size: 1rem;">Retur documente</span>
                                                        </label>
                                                    </div>
                                                </p>
                                            </div>
                                        </div>
                                        {{-- <h6>Optiuni Amrcolet</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12">
                                                <p class="mt-1">
                                                    <div class="switch">
                                                        <label>
                                                            <input id="send-sms" name="send_sms" value="1" type="checkbox">
                                                            <span class="lever"></span>
                                                            <span style="font-size: 1rem;">Trimite SMS catre destinatar la ridicare</span>
                                                        </label>
                                                    </div>
                                                </p>
                                            </div>
                                        </div> --}}
                                        <h6>Ramburs</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12">
                                                <small data-error="ramburs" class="errorTxt1 float-right red-text"></small>
                                                <p class="card-alert">
                                                    <label>
                                                        <input class="with-gap ramburs-type" name="ramburs" type="radio" value="1" {{ old('ramburs', session()->get('package.ramburs')) == '1' || !old('ramburs', session()->get('package.ramburs')) ? 'checked' : '' }}>
                                                        <span>Fara ramburs</span>
                                                    </label>
                                                </p>
                                                {{-- <p class="mt-1">
                                                    <label>
                                                        <input class="with-gap ramburs-type" name="ramburs" type="radio" value="2" {{ old('ramburs', session()->get('package.ramburs')) == '2' ? 'checked' : '' }}>
                                                        <span>Ramburs cash (veti primi banii in plic, adusi de curier)</span>
                                                    </label>
                                                </p> --}}
                                                <p class="mt-1">
                                                    <label>
                                                        <input class="with-gap ramburs-type" name="ramburs" type="radio" value="3" {{ old('ramburs', session()->get('package.ramburs')) == '3' ? 'checked' : '' }}>
                                                        <span>Ramburs in cont (veti primi banii in contul bancar)</span>
                                                    </label>
                                                </p>
                                            </div>
                                            <div id="ramburs-sum" class="col s12 hidden p-0 pb-2">
                                                <div class="input-field col m6 s12">
                                                    <label for="ramburs_value">Suma </label>
                                                    <input type="text" class="" id="ramburs_value" name="ramburs_value" value="{{ old('ramburs_value', session()->get('package.ramburs_value')) }}">
                                                    <small data-error="ramburs_value" class="errorTxt1 float-right red-text"></small>
                                                </div>
                                            </div>
                                            <div id="ramburs-cont-info" class="col s12 hidden p-0 pb-2">
                                                @auth
                                                    <div class="input-field col l3 m6 s12">
                                                        <label for="titular-cont">Nume titular cont </label>
                                                        <input type="text" class="" id="titular-cont" name="titular_cont" value="{{ old('titular_cont', session()->get('package.titular_cont')) ?? Auth::user()->info('repayment_card_owner_name') }}">
                                                        <small data-error="titular_cont" class="errorTxt1 float-right red-text"></small>
                                                    </div>
                                                    <div class="input-field col l3 m6 s12">
                                                        <label for="iban">IBAN </label>
                                                        <input type="text" class="" id="iban" name="iban" value="{{ old('iban', session()->get('package.iban')) ?? Auth::user()->info('repayment_iban') }}">
                                                        <small data-error="iban" class="errorTxt1 float-right red-text"></small>
                                                    </div>
                                                @else
                                                    <div class="input-field col l3 m6 s12">
                                                        <label for="titular-cont">Nume titular cont </label>
                                                        <input type="text" class="" id="titular-cont" name="titular_cont" value="{{ old('titular_cont', session()->get('package.titular_cont')) }}">
                                                        <small data-error="titular_cont" class="errorTxt1 float-right red-text"></small>
                                                    </div>
                                                    <div class="input-field col l3 m6 s12">
                                                        <label for="iban">IBAN </label>
                                                        <input type="text" class="" id="iban" name="iban" value="{{ old('iban', session()->get('package.iban')) }}">
                                                        <small data-error="iban" class="errorTxt1 float-right red-text"></small>
                                                    </div>
                                                @endauth
                                            </div>
                                        </div>
                                        <h6>Asigurare colet (in caz de distrugere, pierdere, furt)</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12">
                                                <label for="assurance">Valoare declarata</label>
                                                <input type="number" class="" min="0" id="assurance" name="assurance" value="{{ old('assurance', session()->get('package.assurance')) }}">
                                            </div>
                                        </div>
                                        <h6>Referinta client</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12">
                                                <label for="customer-reference">Referinta client: </label>
                                                <textarea class="materialize-textarea" id="customer-reference" name="customer_reference" value="{{ old('customer_reference', session()->get('package.customer_reference')) }}" data-length="50"></textarea>
                                            </div>
                                        </div>
                                        <h6>Voucher</h6>
                                        <div class="row">
                                            <div class="input-field col m6 s12">
                                                <label for="voucher">Cod voucher </label>
                                                <input type="text" class="" id="voucher" name="voucher" value="{{ old('voucher', session()->get('package.voucher')) }}">
                                                <small data-error="voucher" class="errorTxt1 float-right red-text"></small>
                                            </div>
                                        </div>
                                        <div class="step-actions" style="position: relative;">
                                            <div class="row m-0">
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="btn btn-light blue previous-step" style="padding-right: 1rem; padding-left: 1rem;">
                                                        <i class="material-icons left">arrow_back</i>
                                                        Anterior
                                                    </button>
                                                </div>
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="waves-effect waves dark btn btn-primary blue next-step" style="padding-right: 1rem; padding-left: 1rem;" type="submit">
                                                        Urmator
                                                        <i class="material-icons right">arrow_forward</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @if($noInvoiceInfo)
                                <li class="step large">
                                    <div class="step-title waves-effect">Date de facturare</div>
                                    <div class="step-content large w-h-100 px-2" data-action="{{ route('order.invoice') }}">
                                        <h5>Date de contact</h5>
                                        <p>Se va introduce <b>datele firmei/persoanei</b> care <b>achita cu cardul</b> aceasta comanda</p>
                                        @auth
                                            <div class="row">
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_first_name">Prenume <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_first_name" name="invoice[first_name]" value="{{ old('invoice.first_name', session()->get('invoice.first_name')) ?? Auth::user()->info('invoice_first_name') }}" required>
                                                    <small data-error="invoice[first_name]" class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_last_name">Nume <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_last_name" name="invoice[last_name]" value="{{ old('invoice.last_name', session()->get('invoice.last_name')) ?? Auth::user()->info('invoice_last_name') }}" required>
                                                    <small data-error="voucher" class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_phone" class="active">Numar de telefon <span class="red-text">*</span></label>
                                                    <input type="text" class="" placeholder="" id="invoice_phone" name="invoice[phone]" value="{{ old('invoice.phone', session()->get('invoice.phone')) ?? Auth::user()->info('invoice_phone') }}" style="box-sizing: border-box;" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col m6 s12">
                                                    <div class="switch -ml-3">
                                                        <label>
                                                            <input id="juridic" value="1" name="invoice[is_company]" {{ old('invoice.is_company', session()->get('invoice.is_company')) || (!old('invoice.is_company', session()->get('invoice.is_company')) && Auth::user()->info('invoice_is_company') == '1') ? 'checked' : '' }} type="checkbox">
                                                            <span class="lever"></span>
                                                            <span style="font-size: 1rem;">Persoana juridica</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="input-field col s12 m-0 show-nif">
                                                    <div class="card-alert card orange lighten-5 m-0">
                                                        <div class="card-content orange-text">
                                                            <p><i class="material-icons">warning</i> <b>Atentie!</b> Numarul de identificare fiscala (NIF) este atribuit de ANAF entitatilor juridice care sunt din alte tari, dar platesc taxe si in Romania. Va rugam sa folositi NIF ca optiune doar in cazul sunteti sigur ca aceasta optiune vi se potriveste.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row show-juridic">
                                                <h6 class="ml-1">Date companie</h6>
                                                <div class="input-field col l1 m6 s12">
                                                    <select name="invoice[company_type]" id="company_type" class="select">
                                                        <option value="1" {{ old('invoice.company_type', session()->get('invoice.country_code')) == "1" || (!old('invoice.is_company', session()->get('invoice.country_code')) && Auth::user()->info('invoice_company_type') == "1") ? 'selected' : '' }}>CUI</option>
                                                        <option value="2" {{ old('invoice.company_type', session()->get('invoice.country_code')) == "2" || (!old('invoice.is_company', session()->get('invoice.country_code')) && Auth::user()->info('invoice_company_type') == "2") ? 'selected' : '' }}>NIF</option>
                                                    </select>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l3 m6 s12">
                                                    <label for="invoice_cui"><span class="show-cui">CUI</span><span class="show-nif">NIF</span> <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_cui" name="invoice[cui_nif]" value="{{ old('invoice.cui_nif', session()->get('invoice.cui_nif')) ?? Auth::user()->info('invoice_cui_nif')  }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_nr_reg_com">Nr. Reg. Com. <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_nr_reg_com" name="invoice[nr_reg_com]" value="{{ old('invoice.nr_reg_com', session()->get('invoice.nr_reg_com')) ?? Auth::user()->info('invoice_nr_reg')  }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_company_name">Nume firma <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_company_name" name="invoice[company_name]" value="{{ old('invoice.company_name', session()->get('invoice.company_name')) ?? Auth::user()->info('invoice_company_name')  }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                            </div>
                                            <h6>Adresa de facturare</h6>
                                            <div class="row mt-2">
                                                <div class="input-field col s12">
                                                    <label for="invoice_country">Tara <span class="red-text">*</span></label>
                                                    <input type="text" placeholder="" class="" id="invoice_country" name="invoice[country]" value="{{ old('invoice.country', session()->get('invoice.country')) ?? Auth::user()->info('invoice_country')  }}" style="box-sizing: border-box;" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                    <input id="invoice_country_code" type="hidden" name="invoice[country_code]" value="{{ old('invoice.country_code', session()->get('invoice.country_code')) ?? Auth::user()->info('invoice_country_code') ?? 'ro' }}" required>
                                                </div>
                                                <div class="input-field col l2 m6 s12">
                                                    <label for="invoice_postcode">Cod postal<span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_postcode" name="invoice[postcode]" value="{{ old('invoice.postcode', session()->get('invoice.postcode')) ?? Auth::user()->info('invoice_postcode') }}" data-person="invoice" data-postcode="1" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_locality">Localitate <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_locality" name="invoice[locality]" value="{{ old('invoice.locality', session()->get('invoice.locality')) ?? Auth::user()->info('invoice_locality') }}" data-person="invoice" data-url="{{ route('order.get.county') }}" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l2 m6 s12">
                                                    <label for="invoice_county">Judet <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_county" name="invoice[county]" value="{{ old('invoice.county', session()->get('invoice.county')) ?? Auth::user()->info('invoice_county') }}" data-person="invoice" readonly required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_street">Strada <span class="red-text">*</span></label>
                                                    <input type="text" class="" data-person="invoice" id="invoice_street" name="invoice[street]" value="{{ old('invoice.street', session()->get('invoice.street')) ?? Auth::user()->info('invoice_street') }}" data-url="{{ route('order.get.street') }}" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_street_nr">Nr. <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_street_nr" name="invoice[street_nr]" value="{{ old('invoice.street_nr', session()->get('invoice.street_nr')) ?? Auth::user()->info('invoice_street_nr') }}" data-person="invoice" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_bl_code">Bl. </label>
                                                    <input type="text" class="" id="invoice_bl_code" name="invoice[bl_code]" value="{{ old('invoice.bl_code', session()->get('invoice.bl_code')) ?? Auth::user()->info('invoice_bl_code') }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_bl_letter">Sc. </label>
                                                    <input type="text" class="" id="invoice_bl_letter" name="invoice[bl_letter]" value="{{ old('invoice.bl_letter', session()->get('invoice.bl_letter')) ?? Auth::user()->info('invoice_bl_letter') }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_intercom">Interfon </label>
                                                    <input type="text" class="" id="invoice_intercom" name="invoice[intercom]" value="{{ old('invoice.intercom', session()->get('invoice.intercom')) ?? Auth::user()->info('invoice_intercom') }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_floor">Et. </label>
                                                    <input type="text" class="" id="invoice_floor" name="invoice[floor]" value="{{ old('invoice.floor', session()->get('invoice.floor')) ?? Auth::user()->info('invoice_floor') }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_apartment">Ap. <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_apartment" name="invoice[apartment]" value="{{ old('invoice.apartment', session()->get('invoice.apartment')) ?? Auth::user()->info('invoice_apartment') }}" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_landmark">Reper </label>
                                                    <input type="text" class="" id="invoice_landmark" name="invoice[landmark]" value="{{ old('invoice.landmark', session()->get('invoice.landmark')) ?? Auth::user()->info('invoice_landmark') }}" data-length="60">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col s12">
                                                    <label for="invoice_more_information">Alte informatii </label>
                                                    <input type="text" class="" id="invoice_more_information" name="invoice[more_information]" value="{{ old('invoice.more_information', session()->get('invoice.more_information')) ?? Auth::user()->info('invoice_more_information') }}" data-length="100">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_first_name">Prenume <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_first_name" name="invoice[first_name]" value="{{ old('invoice.first_name', session()->get('invoice.first_name')) }}" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_last_name">Nume <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_last_name" name="invoice[last_name]" value="{{ old('invoice.last_name', session()->get('invoice.last_name')) }}" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_phone" class="active">Numar de telefon <span class="red-text">*</span></label>
                                                    <input type="text" class="" placeholder="" id="invoice_phone" name="invoice[phone]" value="{{ old('invoice.phone', session()->get('invoice.phone')) }}" style="box-sizing: border-box;" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col m6 s12">
                                                    <div class="switch -ml-3">
                                                        <label>
                                                            <input id="juridic" value="1" name="invoice[is_company]" {{ old('invoice.is_company', session()->get('invoice.is_company')) ? 'checked' : '' }} type="checkbox">
                                                            <span class="lever"></span>
                                                            <span style="font-size: 1rem;">Persoana juridica</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="input-field col s12 m-0 show-nif">
                                                    <div class="card-alert card orange lighten-5 m-0">
                                                        <div class="card-content orange-text">
                                                            <p><i class="material-icons">warning</i> <b>Atentie!</b> Numarul de identificare fiscala (NIF) este atribuit de ANAF entitatilor juridice care sunt din alte tari, dar platesc taxe si in Romania. Va rugam sa folositi NIF ca optiune doar in cazul sunteti sigur ca aceasta optiune vi se potriveste.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row show-juridic">
                                                <h6 class="ml-1">Date companie</h6>
                                                <div class="input-field col l1 m6 s12">
                                                    <select name="invoice[company_type]" id="company_type" class="select">
                                                        <option value="1" {{ old('invoice.company_type', session()->get('invoice.company_type')) == "1" ? 'selected' : '' }}>CUI</option>
                                                        <option value="2" {{ old('invoice.company_type', session()->get('invoice.company_type')) == "2" ? 'selected' : '' }}>NIF</option>
                                                    </select>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l3 m6 s12">
                                                    <label for="invoice_cui"><span class="show-cui">CUI</span><span class="show-nif">NIF</span> <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_cui" name="invoice[cui_nif]" value="{{ old('invoice.cui_nif', session()->get('invoice.cui_nif')) }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_nr_reg_com">Nr. Reg. Com. <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_nr_reg_com" name="invoice[nr_reg_com]" value="{{ old('invoice.nr_reg_com', session()->get('invoice.nr_reg_com')) }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_company_name">Nume firma <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_company_name" name="invoice[company_name]" value="{{ old('invoice.company_name', session()->get('invoice.company_name')) }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                            </div>
                                            <h6>Adresa de facturare</h6>
                                            <div class="row mt-2">
                                                <div class="input-field col s12">
                                                    <label for="invoice_country">Tara <span class="red-text">*</span></label>
                                                    <input type="text" placeholder="" class="" id="invoice_country" name="invoice[country]" value="{{ old('invoice.country', session()->get('invoice.country')) }}" style="box-sizing: border-box;" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                    <input id="invoice_country_code" type="hidden" name="invoice[country_code]" value="{{ old('invoice.country_code', session()->get('invoice.country_code')) ?? 'ro' }}" required>
                                                </div>
                                                <div class="input-field col l2 m6 s12">
                                                    <label for="invoice_postcode">Cod postal<span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_postcode" name="invoice[postcode]" value="{{ old('invoice.postcode', session()->get('invoice.postcode')) }}" data-person="invoice" data-postcode="1" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_locality">Localitate <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_locality" name="invoice[locality]" value="{{ old('invoice.locality', session()->get('invoice.locality')) }}" data-person="invoice" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l2 m6 s12">
                                                    <label for="invoice_county">Judet <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_county" name="invoice[county]" value="{{ old('invoice.county', session()->get('invoice.county')) }}" data-person="invoice" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_street">Strada <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_street" name="invoice[street]" value="{{ old('invoice.street', session()->get('invoice.street')) }}" data-person="invoice" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_street_nr">Nr. <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_street_nr" name="invoice[street_nr]" value="{{ old('invoice.street_nr', session()->get('invoice.street_nr')) }}" data-person="invoice" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_bl_code">Bl. </label>
                                                    <input type="text" class="" id="invoice_bl_code" name="invoice[bl_code]" value="{{ old('invoice.bl_code', session()->get('invoice.bl_code')) }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_bl_letter">Sc. </label>
                                                    <input type="text" class="" id="invoice_bl_letter" name="invoice[bl_letter]" value="{{ old('invoice.bl_letter', session()->get('invoice.bl_letter')) }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_intercom">Interfon </label>
                                                    <input type="text" class="" id="invoice_intercom" name="invoice[intercom]" value="{{ old('invoice.intercom', session()->get('invoice.intercom')) }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_floor">Et. </label>
                                                    <input type="text" class="" id="invoice_floor" name="invoice[floor]" value="{{ old('invoice.floor', session()->get('invoice.floor')) }}">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l1 m3 s12">
                                                    <label for="invoice_apartment">Ap. <span class="red-text">*</span></label>
                                                    <input type="text" class="" id="invoice_apartment" name="invoice[apartment]" value="{{ old('invoice.apartment', session()->get('invoice.apartment')) }}" required>
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col l4 m6 s12">
                                                    <label for="invoice_landmark">Reper </label>
                                                    <input type="text" class="" id="invoice_landmark" name="invoice[landmark]" value="{{ old('invoice.landmark', session()->get('invoice.landmark')) }}" data-length="60">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                                <div class="input-field col s12">
                                                    <label for="invoice_more_information">Alte informatii </label>
                                                    <input type="text" class="" id="invoice_more_information" name="invoice[more_information]" value="{{ old('invoice.more_information', session()->get('invoice.more_information')) }}" data-length="100">
                                                    <small class="errorTxt1 float-right red-text"></small>
                                                </div>
                                            </div>
                                        @endauth
                                        <div class="step-actions" style="position: relative;">
                                            <div class="row m-0">
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="btn btn-light blue previous-step" style="padding-right: 1rem; padding-left: 1rem;">
                                                        <i class="material-icons left">arrow_back</i>
                                                        Anterior
                                                    </button>
                                                </div>
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="waves-effect waves dark btn btn-primary blue next-step" style="padding-right: 1rem; padding-left: 1rem;" type="submit">
                                                        Urmator
                                                        <i class="material-icons right">arrow_forward</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                <li class="step extra-large">
                                    <div class="step-title waves-effect">Lista servicii</div>
                                    <div class="step-content" data-action="{{ route('order.service') }}">
                                        <div class="row">
                                            <div class="curieri">
                                                <div class="input-field col l12 m6 s12 mb-1 mt-0">
                                                    <div class="card m-0">
                                                        <div class="card-content pt-1 pb-1 m-0">
                                                            <div class="row mb-0">
                                                                {{ __('Nu a fost gasit nici un serviciu care sa indeplineasca cerintele dorite.') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col s12 mb-1 mt-0">
                                                <div class="card m-0">
                                                    <div class="card-content pt-1 pb-1 m-0">
                                                        <div class="row mb-0">
                                                            <div class="col s12 p-0 prose min-w-full">
                                                            	<h6><b>Sumar comanda</b></h6>
                                                            	<div class="card m-0">
				                                                    <div class="card-content pt-1 pb-1 m-0 col s12 m6 border-r">
				                                                        <div class="row mb-0">
				                                                            <div class="col m6 s12 p-0 prose min-w-full text-center">
				                                                            	<h6><b>Expeditor</b></h6>
				                                                            	<div class="col s12 p-0">
					                                                            	<div class="row m-0">
					                                                            		<div class="col s5 text-right">Nume: </div><div class="col s7 text-left"><b class="e-p-name"></b></div>
					                                                            	</div>
					                                                            	<div class="row m-0">
						                                                            	<div class="col s5 text-right">Telefon: </div><div class="col s7 text-left"><b class="e-p-phone"></b></div>
						                                                            </div>
                                                                                    <div class="row m-0">
                                                                                        <div class="col s5 text-right">Telefon 2: </div><div class="col s7 text-left"><b class="e-p-phone-2"></b></div>
                                                                                    </div>
						                                                            <div class="row m-0">	
						                                                            	<div class="col s5 text-right">Email: </div><div class="col s7 text-left"><b class="e-p-email"></b></div>
						                                                            </div>
					                                                            	<div class="row m-0">
						                                                            	<div class="col s5 text-right">Tara: </div><div class="col s7 text-left"><b class="e-p-country"></b></div>
						                                                            </div>
						                                                            <div class="row m-0">
						                                                            	<div class="col s5 text-right">Oras si Judet: </div><div class="col s7 text-left"><b class="e-p-city-county"></b></div>
						                                                            <div class="row m-0">
						                                                            </div>
						                                                            	<div class="col s5 text-right">Strada: </div><div class="col s7 text-left"><b><span class="e-p-street"></span><span class="e-p-postcode"></span><span class="e-p-street-nr"></span><span class="e-p-bl-code"></span><span class="e-p-bl-letter"></span><span class="e-p-bl-intercom"></span><span class="e-p-bl-floor"></span><span class="e-p-door-number"></span></b></div>
						                                                            </div>
					                                                            </div>
				                                                            </div>
				                                                        </div>
				                                                    </div>
				                                                </div>
				                                                <div class="card m-0">
				                                                    <div class="card-content pt-1 pb-1 m-0 col s12 m6 border-l">
				                                                        <div class="row mb-0">
				                                                            <div class="col s12 p-0 prose min-w-full text-center">
				                                                            	<h6><b>Destinatar</b></h6>
				                                                            	<div class="col p-0">
				                                                            		<div class="row m-0">
						                                                            	<div class="col s5 text-right">Nume: </div><div class="col s7 text-left"><b class="d-p-name"></b></div>
						                                                            </div>
						                                                            <div class="row m-0">
						                                                            	<div class="col s5 text-right">Telefon: </div><div class="col s7 text-left"><b class="d-p-phone"></b></div>
						                                                            </div>
						                                                            <div class="row m-0">
						                                                            	<div class="col s5 text-right">Telefon 2: </div><div class="col s7 text-left"><b class="d-p-phone-2"></b></div>
						                                                            </div>
						                                                            <div class="row m-0">	
						                                                            	<div class="col s5 text-right">Email: </div><div class="col s7 text-left"><b class="d-p-email"></b></div>
						                                                            </div>
					                                                            	<div class="row m-0">
						                                                            	<div class="col s5 text-right">Tara: </div><div class="col s7 text-left"><b class="d-p-country"></b></div>
						                                                            </div>
						                                                            <div class="row m-0">
						                                                            	<div class="col s5 text-right">Oras si Judet: </div><div class="col s7 text-left"><b class="d-p-city-county"></b></div>
						                                                            </div>
						                                                            <div class="row m-0">
						                                                            	<div class="col s5 text-right">Strada: </div><div class="col s7 text-left"><b><span class="d-p-street"></span><span class="d-p-postcode"></span><span class="d-p-street-nr"></span><span class="d-p-bl-code"></span><span class="d-p-bl-letter"></span><span class="d-p-bl-intercom"></span><span class="d-p-bl-floor"></span><span class="d-p-door-number"></span></b></div>
						                                                            </div>
					                                                            </div>
				                                                            </div>
				                                                        </div>
				                                                    </div>
				                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card m-0">
                                                    <div class="card-content pt-1 pb-1 m-0">
                                                        <div class="row mb-0">
                                                        	<h6><b>Specificatii trimitere</b></h6>
                                                            <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                            	<span>Numar pachete: <b class="p-nr-pachete">0</b></span>&nbsp;
                                                            	<span>Greutate totala: <b class="p-weight">0</b>kg</span>&nbsp;
                                                            	<span>Volum total: <b class="p-volume">0</b>kg (volumetric)</span>&nbsp;
                                                            	<span>Continut: <b class="p-content">0</b></span>&nbsp;
                                                            </div>
                                                            <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                            	<span>Deschidere colet la livrare: <b class="p-optiune-deshidere text-red-500">Nu</b></span><br>
                                                            	<span>Livrare sambata: <b class="p-optiune-livrare-sambata text-red-500">Nu</b></span><br>
                                                            	<span>Retur document: <b class="p-optiune-retur text-red-500">Nu</b></span><br>
                                                            	<span>Ramburs: <b class="p-ramburs text-red-500">Nu</b> <b class="p-ramburs-value text-red-500"></b></span><br>
                                                                <span>Voucher: <b class="p-voucher text-red-500">Nu</b></span><br>
                                                            	<span>Asigurare: <b class="p-assurance">0</b> ron</span><br>
                                                            	<div class="input-field col s12 mt-0 p-assurance-info">
					                                                <div class="card-alert card orange lighten-5 m-0 mt-1">
					                                                    <div class="card-content red-text">
					                                                        <p>
					                                                            <i class="material-icons">warning</i> <b>Atentie: Expeditie fara asigurare!</b>.
					                                                            <br>
					                                                            Raspunderea <b>maxima</b> a curierului (pentru pierderi, deteriorari, etc) este de <b>5 ori mai valoarea transportului (maxim <span class="p-assurance">91,45</span> lei)</b> conform prevederilor legale. <br>
					                                                            Daca doriti cresterea limitei de raspundere, va rugam sa asigurati expeditia.
					                                                        </p>
					                                                    </div>
					                                                </div>
					                                            </div>
                                                            	<span>Ridicarea: <span class="p-pickup-date"><b></b></span> in intervalul <b>08:30 - 18:00</b>. Atentie: Ridicarea se va face in acest interval si in functie de disponibilitatea curierilor si nu este garantata respectarea!</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card m-0">
                                                    <div class="card-content pt-1 pb-1 m-0">
                                                        <div class="row mb-0">
                                                            <div class="col s12 p-0 prose min-w-full">
                                                            	<h6><b>Date facturare</b></h6>
                                                            	<div class="col s12 p-0">
	                                                            	<div class="row m-0">
	                                                            		<div class="col s5 m3 text-right">Nume: </div><div class="col s7 m9 text-left"><b class="f-p-name"></b></div>
	                                                            	</div>
	                                                            	<div class="row m-0">
		                                                            	<div class="col s5 m3 text-right">Telefon: </div><div class="col s7 m9 text-left"><b class="f-p-phone"></b></div>
		                                                            </div>
		                                                            <div class="row m-0">	
		                                                            	<div class="col s5 m3 text-right">Email: </div><div class="col s7 m9 text-left"><b class="f-p-email">@auth {{ auth()->user()->email }} @endauth</b></div>
		                                                            </div>
                                                                    <div class="row m-0">   
                                                                        <div class="col s5 m3 text-right">Nume Companie: </div><div class="col s7 m9 text-left"><b class="f-p-company-name"></b></div>
                                                                    </div>
                                                                    <div class="row m-0">   
                                                                        <div class="col s5 m3 text-right">CUI/NIF: </div><div class="col s7 m9 text-left"><b class="f-p-cui-nif"></b></div>
                                                                    </div>
                                                                    <div class="row m-0">   
                                                                        <div class="col s5 m3 text-right">Nr. Reg. Comert.: </div><div class="col s7 m9 text-left"><b class="f-p-nr-reg-com"></b></div>
                                                                    </div>
	                                                            	<div class="row m-0">
		                                                            	<div class="col s5 m3 text-right">Tara: </div><div class="col s7 m9 text-left"><b class="f-p-country"></b></div>
		                                                            </div>
		                                                            <div class="row m-0">
		                                                            	<div class="col s5 m3 text-right">Oras si Judet: </div><div class="col s7 m9 text-left"><b class="f-p-city-county"></b></div>
                                                                    </div>
		                                                            <div class="row m-0">
		                                                            	<div class="col s5 m3 text-right">Strada: </div><div class="col s7 m9 text-left"><b><span class="f-p-street"></span><span class="f-p-postcode"></span><span class="f-p-street-nr"></span><span class="f-p-bl-code"></span><span class="f-p-bl-letter"></span><span class="f-p-bl-intercom"></span><span class="f-p-bl-floor"></span><span class="f-p-door-number"></span></b></div>
		                                                            </div>
	                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col s12 mb-1 mt-0">
                                                <div class="card m-0">
                                                    <div class="card-content pt-1 pb-1 m-0">
                                                        <div class="row mb-0">
                                                            <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                            	<span>Total plata: <b class="p-total-price">0</b> ron</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-actions" style="position: relative;">
                                            <div class="row m-0">
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="btn btn-light blue previous-step" style="padding-right: 1rem; padding-left: 1rem;">
                                                        <i class="material-icons left">arrow_back</i>
                                                        Anterior
                                                    </button>
                                                </div>
                                                <div class="col l2 m12 s12 mb-3 max-w-full">
                                                    <button class="waves-effect waves dark btn btn-primary blue" data-submit='1' style="padding-right: 1rem; padding-left: 1rem;" type="submit">
                                                        Trimite
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </form>
                        <div id="form-loader" class="text-center hidden py-4">
                            <div class="preloader-wrapper big active">
                                <div class="spinner-layer spinner-blue-only">
                                    <div class="circle-clipper left">
                                        <div class="circle"></div>
                                    </div>
                                    <div class="gap-patch">
                                        <div class="circle"></div>
                                    </div>
                                    <div class="circle-clipper right">
                                        <div class="circle"></div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="font-black">{{ __('Căutare...') }}</h3>
                            <h5>{{ __('Se caută cele mai bune oferte.') }}</h5>
                        </div>
                        <div id="finish-order" class="text-center hidden py-4">
                            <div class="preloader-wrapper big active">
                                <div class="spinner-layer spinner-blue-only">
                                    <div class="circle-clipper left">
                                        <div class="circle"></div>
                                    </div>
                                    <div class="gap-patch">
                                        <div class="circle"></div>
                                    </div>
                                    <div class="circle-clipper right">
                                        <div class="circle"></div>
                                    </div>
                                </div>
                            </div>
                            <h5>{{ __('Confirmăm detaliile comenzii. Vă rugăm așteptați.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Footer-->
    @livewire('footer')
</x-guest-layout>
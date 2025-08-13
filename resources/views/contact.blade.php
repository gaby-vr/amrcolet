@push('styles')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mat/vendors.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/fontawesome/css/all.min.css') }}">

<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/materialize.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/style.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-contact.css') }}">
<style>
    .gradient {
        background: linear-gradient(90deg, #0038cc 0%, #0038cc 100%);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/vendors/mat/vendors.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/plugins.min.js') }}"></script>
<script src="{{ asset('js/pages/page-contact.min.js') }}"></script>
@endpush

<x-guest-layout>
    <x-jet-banner />
    @livewire('navigation-menu')
    <div class="pt-16 bg-gray-100">
        <div id="contact-us" class="min-h-screen -mt-16 pt-20 flex flex-col items-center">
            <div class="app-wrapper p-2">
                <div class="contact-header">
                    <div class="row contact-us ml-0 mr-0">
                        <div class="col s12 m12 l4 sidebar-title">
                            <h3 class="my-8">{{-- <i class="material-icons contact-icon vertical-text-top" style="font-size: 3.9rem;">mail_outline</i> --}} {{ __('Contacteaza-ne') }}</h3>
                            <div class="row hide-on-large-only pb-3">
                                <!-- CIF -->
                                <div class="col s12 place mt-4 p-0">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <b x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">CIF</b>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-8 bg-blue-500 rounded-lg shadow-lg">
                                                Cod de identificare firma
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0">{{ $settings['PROVIDER_CUI'] }}</p>
                                    </div>
                                </div>
                                <!-- RC -->
                                <div class="col s12 place mt-4 p-0">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <b x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">RC</b>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-8 bg-blue-500 rounded-lg shadow-lg">
                                                Nr. Registrul Comertului
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0">{{ $settings['PROVIDER_NR_REG'] }}</p>
                                    </div>
                                </div>
                                <!-- Bank -->
                                <div class="col s12 phone mt-4 p-0">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> account_balance </i>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                Banca
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0">{{ $settings['PROVIDER_BANK'] }}</p>
                                    </div>
                                </div>
                                <!-- Bank -->
                                <div class="col s12 phone mt-4 p-0">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> account_box </i>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                IBAN
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0">{{ $settings['PROVIDER_IBAN'] }}</p>
                                    </div>
                                </div>
                                <!-- Phone -->
                                <div class="col s12 phone mt-4 p-0">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> call </i>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                Telefon
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0"><a href="tel:{{ $settings['PROVIDER_PHONE'] }}">{{ $settings['PROVIDER_PHONE'] }}</a> <br>( Luni-Vineri intre orele 09:00-18:00 )</p>
                                    </div>
                                </div>
                                <!-- Mail -->
                                <div class="col s12 mail mt-4 p-0">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> mail_outline </i>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                Adresa de email
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0"><a href="mailto:{{ $settings['PROVIDER_EMAIL'] }}">{{ $settings['PROVIDER_EMAIL'] }}</a></p>
                                    </div>
                                </div>
                                <!-- Place -->
                                <div class="col s12 place mt-4 p-0 relative">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> place </i>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                Sediu social
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0">{{ $settings['PROVIDER_ADDRESS'] }}</p>
                                    </div>
                                </div>
                                <!-- Place -->
                                <div class="col s12 place mt-4 p-0">
                                    <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                        <i class="fas fa-truck" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                                        <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                            <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-8 bg-blue-500 rounded-lg shadow-lg">
                                                Adresa de corespondenta
                                            </div>
                                            <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col s10 m10 l10">
                                        <p class="m-0">{{ $settings['PROVIDER_ADDRESS'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <p class="m-0 font-weight-500 mt-6 hide-on-med-and-up text-ellipsis"></p>
                            <span class="social-icons hide-on-med-and-down"></span>
                        </div>
                        <div class="col s12 m12 l8 form-header">
                            <h6 class="form-header-text"><i class="material-icons"> mail_outline </i> {{ __('Scrie cateva cuvinte despre experienta ta cu site-ul nostru.') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="w-full sm:max-w-7xl mt-0 p-0 overflow-hidden">
               <!--  <div id="contact-us" class="section"> -->
                    

                    <!-- Contact Sidenav -->
                    <div id="sidebar-list" class="row contact-sidenav ml-0 mr-0">
                        <div class="col s12 m12 l4 border-t-2">
                            <!-- Sidebar Area Starts -->
                            <div class="sidebar-left sidebar-fixed">
                                <div class="sidebar">
                                    <div class="sidebar-content">
                                        <div class="sidebar-menu list-group position-relative">
                                            <div class="sidebar-list-padding app-sidebar contact-app-sidebar" id="contact-sidenav">
                                               {{-- <!-- <ul class="contact-list display-grid">
                                                    <li>
                                                        <h5 class="m-0">What will be next step?</h5>
                                                    </li>
                                                    <li>
                                                        <h6 class="mt-5 line-height">You are one step closer to build your perfect product</h6>
                                                    </li>
                                                    <li>
                                                        <hr class="mt-5">
                                                    </li>
                                                </ul> --> --}}
                                                <div class="row">
                                                    <!-- CIF -->
                                                    <div class="col s12 place mt-4 p-0">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <b x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">CIF</b>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-8 bg-blue-500 rounded-lg shadow-lg">
                                                                    Cod de identificare firma
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0">{{ $settings['PROVIDER_CUI'] }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- RC -->
                                                    <div class="col s12 place mt-4 p-0">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <b x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">RC</b>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-8 bg-blue-500 rounded-lg shadow-lg">
                                                                    Nr. Registrul Comertului
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0">{{ $settings['PROVIDER_NR_REG'] }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Bank -->
                                                    <div class="col s12 phone mt-4 p-0">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> account_balance </i>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                                    Banca
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0">{{ $settings['PROVIDER_BANK'] }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Bank -->
                                                    <div class="col s12 phone mt-4 p-0">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> account_box </i>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                                    IBAN
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0">{{ $settings['PROVIDER_IBAN'] }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Phone -->
                                                    <div class="col s12 phone mt-4 p-0">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> call </i>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                                    Telefon
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0"><a href="tel:{{ $settings['PROVIDER_PHONE'] }}">{{ $settings['PROVIDER_PHONE'] }}</a> <br>( Luni-Vineri intre orele 09:00-18:00 )</p>
                                                        </div>
                                                    </div>
                                                    <!-- Mail -->
                                                    <div class="col s12 mail mt-4 p-0">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> mail_outline </i>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                                    Adresa de email
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0"><a href="mailto:{{ $settings['PROVIDER_EMAIL'] }}">{{ $settings['PROVIDER_EMAIL'] }}</a></p>
                                                        </div>
                                                    </div>
                                                    <!-- Place -->
                                                    <div class="col s12 place mt-4 p-0 relative">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <i class="material-icons" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"> place </i>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-blue-500 rounded-lg shadow-lg">
                                                                    Sediu social
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-7 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0">{{ $settings['PROVIDER_ADDRESS'] }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Place -->
                                                    <div class="col s12 place mt-4 p-0">
                                                        <div x-data="{ tooltip: false }" class="col s2 m2 l2">
                                                            <i class="fas fa-truck" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                                                            <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
                                                                <div class="absolute left-24 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-1/2 -translate-y-8 bg-blue-500 rounded-lg shadow-lg">
                                                                    Adresa de corespondenta
                                                                </div>
                                                                <svg class="absolute left-16 z-10 w-6 h-6 text-blue-500 transform -translate-x-12 -translate-y-5 fill-current stroke-current" width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="col s10 m10 l10">
                                                            <p class="m-0">{{ $settings['PROVIDER_ADDRESS'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Sidebar Area Ends -->
                        </div>
                        <div class="col s12 m12 l8 contact-form rounded-0 margin-top-contact">
                            <div class="row">
                                <form action="{{ route('contact.send') }}" method="post" class="col s12">
                                    <x-jet-validation-errors class="mb-4" />
                                    @if(session()->has('success'))
                                    <div class="mb-4">
                                        <div class="font-medium text-green-600">{!! session('success') !!}</div>
                                    </div>
                                    @endif
                                    @csrf
                                    <div class="row">
                                        <div class="input-field col m6 s12">
                                            <input id="name" name="name" type="text" class="validate" required value="{{ old('name') }}" style="box-shadow: none; box-sizing: border-box;">
                                            <label for="name" style="color: #9e9fa2;">{{ __('Nume') }}</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <input id="email" name="email" type="text" class="validate" required value="{{ old('email') }}" style="box-shadow: none; box-sizing: border-box;">
                                            <label for="email" style="color: #9e9fa2;">{{ __('Adresa de email') }}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12 width-100">
                                            <textarea id="textarea1" name="message" class="materialize-textarea" required style="box-shadow: none;">{{ old('message') }}</textarea>
                                            <label for="textarea1" style="color: #9e9fa2;">{{ __('Mesaj') }}</label>
                                            <button role="submit" class="waves-effect waves-light btn gradient">{{ __('Trimite') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

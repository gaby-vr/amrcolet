@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Comenzi') }}</x-slot>
        <x-slot name="href"></x-slot>
        <x-slot name="btnName"></x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                                <div class="p-8">
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
                                                                        @if($sender)
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Nume: </div><div class="col ellipses s7 text-left"><b class="e-p-name">{{ $sender->name }}</b></div>
                                                                            </div>
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Telefon: </div><div class="col ellipses s7 text-left"><b class="e-p-phone">{{ $sender->phone }}</b></div>
                                                                            </div>
                                                                            @if($sender->phone_2 != null)
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Telefon 2: </div><div class="col ellipses s7 text-left"><b class="e-p-phone-2">{{ $sender->phone_2 }}</b></div>
                                                                            </div>
                                                                            @endif
                                                                            <div class="row m-0">   
                                                                                <div class="col ellipses s5 text-right">Email: </div><div class="col ellipses s7 text-left"><b class="e-p-email">{{ $sender->email }}</b></div>
                                                                            </div>
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Tara: </div><div class="col ellipses s7 text-left"><b class="e-p-country">{{ $sender->country }}</b></div>
                                                                            </div>
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Oras si Judet: </div><div class="col ellipses s7 text-left"><b class="e-p-city-county">{{ $sender->locality }}, {{ $sender->county }}</b></div>
                                                                            <div class="row m-0">
                                                                            </div>
                                                                                <div class="col ellipses s5 text-right">Strada: </div>
                                                                                <div class="col ellipses s7 text-left">
                                                                                    <b>
                                                                                        <span class="e-p-street">{{ $sender->street }}</span>
                                                                                        @if($sender->street_nr != null)
                                                                                            <span class="e-p-street-nr"> Nr. {{ $sender->street_nr }},</span>
                                                                                        @endif
                                                                                        <span class="e-p-postcode"> {{ $sender->postcode }},</span>
                                                                                        @if($sender->bl_code != null)
                                                                                            <span class="e-p-bl-code"> Bl. {{ $sender->bl_code }}, </span>
                                                                                        @endif
                                                                                        @if($sender->bl_letter != null)
                                                                                            <span class="e-p-bl-letter"> Sc. {{ $sender->bl_letter }}, </span>
                                                                                        @endif
                                                                                        @if($sender->intercom != null)
                                                                                            <span class="e-p-bl-intercom"> Interfon {{ $sender->intercom }}, </span>
                                                                                        @endif
                                                                                        @if($sender->floor != null)
                                                                                            <span class="e-p-bl-floor"> Etaj {{ $sender->floor }}, </span>
                                                                                        @endif
                                                                                        @if($sender->apartment != null)
                                                                                            <span class="e-p-door-number"> Ap./Nr. {{ $sender->apartment }}, </span>
                                                                                        @endif
                                                                                    </b>
                                                                                </div>
                                                                                @if($sender->more_information != null)
                                                                                    <div class="col ellipses s5 text-right">Informatii aditionale: </div>
                                                                                    <div class="col ellipses s7 text-left"><b>{{ $sender->more_information }}</b></div>
                                                                                @endif
                                                                            </div>
                                                                        @else
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s12 text-right">A avut loc o eroare la salvarea expeditorului: </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card m-0">
                                                        <div class="card-content pt-1 pb-1 m-0 col s12 m6 border-l">
                                                            <div class="row mb-0">
                                                                <div class="col ellipses s12 p-0 prose min-w-full text-center">
                                                                    <h6><b>Destinatar</b></h6>
                                                                    <div class="col ellipses s12 p-0">
                                                                        @if($receiver)
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Nume: </div><div class="col ellipses s7 text-left"><b class="d-p-name">{{ $receiver->name }}</b></div>
                                                                            </div>
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Telefon: </div><div class="col ellipses s7 text-left"><b class="d-p-phone">{{ $receiver->phone }}</b></div>
                                                                            </div>
                                                                            @if($receiver->phone_2 != null)
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Telefon 2: </div><div class="col ellipses s7 text-left"><b class="d-p-phone-2">{{ $receiver->phone_2 }}</b></div>
                                                                            </div>
                                                                            @endif
                                                                            <div class="row m-0">   
                                                                                <div class="col ellipses s5 text-right">Email: </div><div class="col ellipses s7 text-left"><b class="d-p-email">{{ $receiver->email }}</b></div>
                                                                            </div>
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Tara: </div><div class="col ellipses s7 text-left"><b class="d-p-country">{{ $receiver->country }}</b></div>
                                                                            </div>
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s5 text-right">Oras si Judet: </div><div class="col ellipses s7 text-left"><b class="d-p-city-county">{{ $receiver->locality }}, {{ $receiver->county }}</b></div>
                                                                            <div class="row m-0">
                                                                            </div>
                                                                                <div class="col ellipses s5 text-right">Strada: </div>
                                                                                <div class="col ellipses s7 text-left">
                                                                                    <b>
                                                                                        <span class="d-p-street">{{ $receiver->street }}</span>
                                                                                        @if($receiver->street_nr != null)
                                                                                            <span class="d-p-street-nr"> Nr. {{ $receiver->street_nr }},</span>
                                                                                        @endif
                                                                                        <span class="d-p-postcode"> {{ $receiver->postcode }},</span>
                                                                                        @if($receiver->bl_code != null)
                                                                                            <span class="d-p-bl-code"> Bl. {{ $receiver->bl_code }}, </span>
                                                                                        @endif
                                                                                        @if($receiver->bl_letter != null)
                                                                                            <span class="d-p-bl-letter"> Sc. {{ $receiver->bl_letter }}, </span>
                                                                                        @endif
                                                                                        @if($receiver->intercom != null)
                                                                                            <span class="d-p-bl-intercom"> Interfon {{ $receiver->intercom }}, </span>
                                                                                        @endif
                                                                                        @if($receiver->floor != null)
                                                                                            <span class="d-p-bl-floor"> Etaj {{ $receiver->floor }}, </span>
                                                                                        @endif
                                                                                        @if($receiver->apartment != null)
                                                                                            <span class="d-p-door-number"> Ap./Nr. {{ $receiver->apartment }}, </span>
                                                                                        @endif
                                                                                    </b>
                                                                                </div>
                                                                            </div>
                                                                            @if($receiver->more_information != null)
                                                                                <div class="col ellipses s5 text-right">Informatii aditionale: </div>
                                                                                <div class="col ellipses s7 text-left"><b>{{ $receiver->more_information }}</b></div>
                                                                            @endif
                                                                        @else
                                                                            <div class="row m-0">
                                                                                <div class="col ellipses s12 text-right">A avut loc o eroare la salvarea destinatarului: </div>
                                                                            </div>
                                                                        @endif
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
                                                <h6><b class="mr-3">Status</b> <x-jet-order-status :order="$order" /></h6>
                                                <div class="col l12 m12 s12 p-0 min-w-full my-3">
                                                    <x-jet-validation-messages class="mb-4" />
                                                    <x-jet-validation-errors class="bg-red-100 text-white text-sm px-4 py-3 mb-4 shadow-md" role="alert" />
                                                    <form action="{{ route('admin.orders.update.status', $order) }}" method="post">
                                                        @csrf
                                                        <div class="grid grid-cols-12 gap-3 w-full">
                                                            <div class="col-span-12 md:col-span-3">
                                                                <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" id="status" name="status" >
                                                                    @foreach($status_list as $status => $name)
                                                                        <option value="{{ $status }}" @selected($order->status == $status)>
                                                                            {{ $name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-span-12 md:col-span-9">
                                                                <label class="block font-medium text-sm text-gray-700 flex items-center sm:mr-3 md:mt-3 sm:mt-0">
                                                                    <x-jet-checkbox name="manual_status" value="1" :checked="old('manual_status', $order->manual_status) == 1" />
                                                                    <span class="ml-2">{{ __('Opreste actualizarea automata a statusului') }}</span>
                                                                </label>
                                                            </div>
                                                            <div class="col-span-12 md:col-span-2 self-end pb-1">
                                                                <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black transition">
                                                                    <i class="far fa-edit mr-1" style="margin-top: 1px;"></i>
                                                                    {{ __('Actualizare') }}
                                                                </button>
                                                            </div>
                                                        </div>

                                                    </form>
                                                </div>
                                                <h6><b>Specificatii trimitere</b></h6>
                                                <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                    <span>Nume curier: <b class="p-content">{{ $order->curier }}</b></span><br>
                                                    <span>Continut: <b class="p-content">{{ $order->content }}</b></span>&nbsp;
                                                    @if($order->type == '1')
                                                        <span>Numar pachete: <b class="p-nr-pachete">{{ $order->nr_colete }}</b></span>&nbsp;
                                                        <span>Greutate totala: <b class="p-weight">{{ $order->total_weight }}</b>kg</span>&nbsp;
                                                        <span>Greutate volumetrica totala: <b class="p-volume">{{ $order->total_volume }}</b>kg</span><br>
                                                        <span>Pachete: <br>
                                                            
                                                            @if(count($packages) > 0)
                                                                @for($i = 1 ; $i <= $order->nr_colete ; $i++)
                                                                    <span class="pl-3 border-l inline-block">
                                                                        <b>Pachet {{ $i }}</b>&nbsp;<br>
                                                                        Dimensiuni: <b class="p-colet-dimensions"> L: {{ $packages[$i-1]->length }} x l: {{ $packages[$i-1]->width }} x H: {{ $packages[$i-1]->height }}</b></sup>&nbsp;<br>
                                                                        Greutate: <b class="p-colet-weight"> {{ $packages[$i-1]->weight }}</b>kg</sup>&nbsp;<br>
                                                                        Greutate volumetrica: <b class="p-colet-volume"> {{ $packages[$i-1]->volume }}</b>kg<br>
                                                                    </span>
                                                                @endfor
                                                            @else
                                                                <span class="pl-3 border-l inline-block">Eroare la salvarea pachetelor</span>
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                    <span>Deschidere colet la livrare: 
                                                        @if($order->open_when_received == null || $order->open_when_received == 0)
                                                            <b class="p-optiune-deshidere text-red-500">Nu</b>
                                                        @else
                                                            <b class="p-optiune-deshidere text-green-500">Da</b>
                                                        @endif
                                                    </span><br>
                                                    <span>Livrare sambata: 
                                                        @if($order->work_saturday == null || $order->work_saturday == 0)
                                                            <b class="p-optiune-livrare-sambata text-red-500">Nu</b>
                                                        @else
                                                            <b class="p-optiune-livrare-sambata text-green-500">Da</b>
                                                        @endif
                                                    </span><br>
                                                    <span>Retur document: 
                                                        @if($order->retur_document == null || $order->retur_document == 0)
                                                            <b class="p-optiune-retur text-red-500">Nu</b><br>
                                                        @else
                                                            <b class="p-optiune-retur text-green-500">Da</b>
                                                            <div class="p-swap-container pl-2">
                                                                <span>Numar colete returnate: <b class="p-swap-nr-parcels">{{ $order->swap_details['nr_parcels'] ?? 1 }}</b></span><br>
                                                                <span>Greutate totala colete returnat: <b class="p-swap-total-weight">{{ $order->swap_details['total_weight'] ?? 1 }}</b>kg</span>
                                                            </div>
                                                        @endif
                                                    </span>
                                                    {{-- <span>SMS destinatar la ridicare colet: 
                                                        @if($order->send_sms == null || $order->send_sms == 0)
                                                            <b class="p-send-sms text-red-500">Nu</b>
                                                        @else
                                                            <b class="p-send-sms text-green-500">Da</b>
                                                        @endif
                                                    </span><br> --}}
                                                    <span>Ramburs: 
                                                        @if($order->ramburs == '2')
                                                            <b class="p-ramburs text-green-500">Ramburs cash</b>
                                                        @elseif($order->ramburs == '3')
                                                            <b class="p-ramburs text-green-500">Ramburs in cont</b>
                                                        @else
                                                            <b class="p-ramburs text-red-500">Nu</b>
                                                        @endif
                                                    </span><br>
                                                    <span>Asigurare: <b class="p-assurance">{{ $order->assurance }}</b></span><br>
                                                    <hr class="mt-2 mb-2">
                                                    <span>Ridicarea: <span class="p-pickup-date"><b></b></span> in intervalul <b>08:30 - 18:00</b>. Atentie: Ridicarea se va face in acest interval si in functie de disponibilitatea curierilor si nu este garantata respectarea!</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($invoice != null)
                                    <div class="card m-0">
                                        <div class="card-content pt-1 pb-1 m-0">
                                            <div class="row mb-0">
                                                <div class="col s12 p-0 prose min-w-full">
                                                    <h6><b>Date facturare</b></h6>
                                                    <div class="col s12 p-0">
                                                        <div class="row m-0">
                                                            <div class="col ellipses s5 m3 text-right">Nume: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-name">{{ $invoice->meta('client_last_name') }} {{ $invoice->meta('client_first_name') }}</b></div>
                                                        </div>
                                                        <div class="row m-0">
                                                            <div class="col ellipses s5 m3 text-right">Telefon: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-phone">{{ $invoice->meta('client_phone') }}</b></div>
                                                        </div>
                                                        <div class="row m-0">   
                                                            <div class="col ellipses s5 m3 text-right">Email: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-email">{{ $invoice->meta('client_email') }}</b></div>
                                                        </div>
                                                        @if($invoice->meta('client_type') == '2')
                                                            <div class="row m-0">   
                                                                <div class="col ellipses s5 m3 text-right">Nume Companie: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-company-name">{{ $invoice->meta('client_nume_firma') }}</b></div>
                                                            </div>
                                                            <div class="row m-0">   
                                                                <div class="col ellipses s5 m3 text-right">CUI/NIF: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-cui-nif">{{ $invoice->meta('client_cui_nif') }}</b></div>
                                                            </div>
                                                            <div class="row m-0">   
                                                                <div class="col ellipses s5 m3 text-right">Nr. Reg. Comert.: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-nr-reg-com">{{ $invoice->meta('client_nr_reg') }}</b></div>
                                                            </div>
                                                        @endif
                                                        <div class="row m-0">
                                                            <div class="col ellipses s5 m3 text-right">Tara: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-country">{{ $invoice->meta('client_country') }}</b></div>
                                                        </div>
                                                        <div class="row m-0">
                                                            <div class="col ellipses s5 m3 text-right">Oras si Judet: </div><div class="col ellipses s7 m9 text-left"><b class="f-p-city-county">{{ $invoice->meta('client_locality') }}, {{ $invoice->meta('client_county') }}</b></div>
                                                        </div>
                                                        <div class="row m-0">
                                                            <div class="col ellipses s5 m3 text-right">Strada: </div>
                                                            <div class="col ellipses s7 m9 text-left">
                                                                <b>
                                                                    <span class="f-p-street">{{ $invoice->meta('client_address') }}, </span>
                                                                    <span class="f-p-postcode">{{ $invoice->meta('client_postcode') }}</span>
                                                                </b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col s12 mb-1 mt-0">
                                    <div class="card m-0">
                                        <div class="card-content pt-1 pb-1 m-0">
                                            <div class="row mb-0">
                                                <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                    <span>Total plata: <b class="p-total-price">{{ $invoice->total ?? $order->value }}</b> ron</span>
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
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
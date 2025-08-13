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
                                        <form method="POST" action="{{ route('admin.pending_orders.updatePending', $order) }}">
                                            @csrf
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <h6 class="font-bold text-center mb-2">Expeditor</h6>
                                                    <div class="mb-2">
                                                        <label>Nume</label>
                                                        <input type="text" name="sender_name" value="{{ old('sender_name', $sender->name) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Telefon</label>
                                                        <input type="text" name="sender_phone" value="{{ old('sender_phone', $sender->phone) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Email</label>
                                                        <input type="email" name="sender_email" value="{{ old('sender_email', $sender->email) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Țara</label>
                                                        <input type="text" name="sender_country" value="{{ old('sender_country', $sender->country) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Oraș</label>
                                                        <input type="text" name="sender_city" value="{{ old('sender_city', $sender->locality) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Județ</label>
                                                        <input type="text" name="sender_county" value="{{ old('sender_county', $sender->county) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Stradă</label>
                                                        <input type="text" name="sender_street" value="{{ old('sender_street', $sender->street) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Cod poștal</label>
                                                        <input type="text" name="sender_postcode" value="{{ old('sender_postcode', $sender->postcode) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Informații adiționale</label>
                                                        <input type="text" name="sender_more_information" value="{{ old('sender_more_information', $sender->more_information) }}" class="form-input w-full">
                                                    </div>
                                                </div>

                                                <div>
                                                    <h6 class="font-bold text-center mb-2">Destinatar</h6>
                                                    <div class="mb-2">
                                                        <label>Nume</label>
                                                        <input type="text" name="receiver_name" value="{{ old('receiver_name', $receiver->name) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Telefon</label>
                                                        <input type="text" name="receiver_phone" value="{{ old('receiver_phone', $receiver->phone) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Email</label>
                                                        <input type="email" name="receiver_email" value="{{ old('receiver_email', $receiver->email) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Țara</label>
                                                        <input type="text" name="receiver_country" value="{{ old('receiver_country', $receiver->country) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Oraș</label>
                                                        <input type="text" name="receiver_city" value="{{ old('receiver_city', $receiver->locality) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Județ</label>
                                                        <input type="text" name="receiver_county" value="{{ old('receiver_county', $receiver->county) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Stradă</label>
                                                        <input type="text" name="receiver_street" value="{{ old('receiver_street', $receiver->street) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Cod poștal</label>
                                                        <input type="text" name="receiver_postcode" value="{{ old('receiver_postcode', $receiver->postcode) }}" class="form-input w-full">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Informații adiționale</label>
                                                        <input type="text" name="receiver_more_information" value="{{ old('receiver_more_information', $receiver->more_information) }}" class="form-input w-full">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card m-0">
                                        <div class="card-content pt-1 pb-1 m-0">
                                            <div class="row mb-0">
                                                <h6><b class="mr-3">Status</b> <x-jet-order-status :order="$order" /></h6>
                                                <h6><b>Specificatii trimitere</b></h6>
                                                <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                    <span>Nume curier: <b class="p-content">{{ $order->curier }}</b></span><br>
                                                    <span>Continut: <b class="p-content">{{ $order->content }}</b></span>&nbsp;
                                                    @if($order->type == '1')
                                                        <div class="mb-2">
                                                            <label for="nr_colete">Numar colete</label>
                                                            <input type="number" name="nr_colete" id="nr_colete" value="{{ old('nr_colete', $order->nr_colete) }}" class="form-input w-full">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label for="total_weight">Greutate totală</label>
                                                            <input type="number" step="0.01" name="total_weight" id="total_weight" value="{{ old('total_weight', $order->total_weight) }}" class="form-input w-full">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label>Greutate volumetrică totală:</label>
                                                            <span id="total_volume_display" class="text-blue-600 font-bold">
                                                                {{ $order->total_volume }} kg
                                                            </span>
                                                        </div>
                                                        <br>
                                                        <div class="mt-6">
                                                            <h6 class="text-sm font-bold">Pachete:</h6>
                                                            @for($i = 0; $i < count($packages); $i++)
                                                                <div class="p-4 my-2 border-l-4 border-gray-400 bg-gray-50 rounded-md">
                                                                    <h6 class="font-semibold mb-2">Pachet {{ $i + 1 }}</h6>
                                                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                                        <div>
                                                                            <label for="length_{{ $i }}">Lungime (cm)</label>
                                                                            <input type="number" step="1" min="1" name="length[{{ $i }}]" id="length_{{ $i }}"
                                                                                value="{{ old('length.' . $i, $packages[$i]->length) }}"
                                                                                class="form-input w-full calc-volume" data-index="{{ $i }}">
                                                                        </div>
                                                                        <div>
                                                                            <label for="width_{{ $i }}">Lățime (cm)</label>
                                                                            <input type="number" step="1" min="1" name="width[{{ $i }}]" id="width_{{ $i }}"
                                                                                value="{{ old('width.' . $i, $packages[$i]->width) }}"
                                                                                class="form-input w-full calc-volume" data-index="{{ $i }}">
                                                                        </div>
                                                                        <div>
                                                                            <label for="height_{{ $i }}">Înălțime (cm)</label>
                                                                            <input type="number" step="1" min="1" name="height[{{ $i }}]" id="height_{{ $i }}"
                                                                                value="{{ old('height.' . $i, $packages[$i]->height) }}"
                                                                                class="form-input w-full calc-volume" data-index="{{ $i }}">
                                                                        </div>
                                                                        <div>
                                                                            <label for="weight_{{ $i }}">Greutate (kg)</label>
                                                                            <input type="number" step="0.01" min="0" name="weight[{{ $i }}]" id="weight_{{ $i }}"
                                                                                value="{{ old('weight.' . $i, $packages[$i]->weight) }}"
                                                                                class="form-input w-full">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <label>Greutate volumetrică:</label>
                                                                        @php
                                                                            $v = round(($packages[$i]->width * $packages[$i]->length * $packages[$i]->height) / 6000, 2);
                                                                        @endphp
                                                                        <span id="volume_{{ $i }}" data-volume="{{ $v }}" class="text-blue-600 font-bold">
                                                                            {{ $v }} kg
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col l12 m12 s12 p-0 prose min-w-full">
                                                    <div class="mb-2">
                                                        <label for="open_when_received">Deschidere colet la livrare</label>
                                                        <select name="open_when_received" id="open_when_received" class="form-select w-full">
                                                            <option value="0" {{ $order->open_when_received == 0 ? 'selected' : '' }}>Nu</option>
                                                            <option value="1" {{ $order->open_when_received == 1 ? 'selected' : '' }}>Da</option>
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <div class="mb-2">
                                                        <label for="work_saturday">Livrare sâmbăta</label>
                                                        <select name="work_saturday" id="work_saturday" class="form-select w-full">
                                                            <option value="0" {{ $order->work_saturday == 0 ? 'selected' : '' }}>Nu</option>
                                                            <option value="1" {{ $order->work_saturday == 1 ? 'selected' : '' }}>Da</option>
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <div class="mb-2">
                                                        <label for="retur_document">Retur document</label>
                                                        <select name="retur_document" id="retur_document" class="form-select w-full" onchange="toggleReturFields(this.value)">
                                                            <option value="0" {{ $order->retur_document == 0 ? 'selected' : '' }}>Nu</option>
                                                            <option value="1" {{ $order->retur_document == 1 ? 'selected' : '' }}>Da</option>
                                                        </select>
                                                    </div>

                                                    <div id="retur-details" class="{{ $order->retur_document == 1 ? '' : 'hidden' }} mt-3 p-4 border-l-4 border-green-500 bg-green-50 rounded-md">
                                                        <div class="mb-2">
                                                            <label for="swap_nr_parcels" class="font-semibold">Număr colete returnate</label>
                                                            <input type="number" name="swap_nr_parcels" id="swap_nr_parcels"
                                                                value="{{ old('swap_nr_parcels', $order->swap_details['nr_parcels'] ?? 1) }}"
                                                                class="form-input w-full">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="swap_total_weight" class="font-semibold">Greutate totală colete returnate (kg)</label>
                                                            <input type="number" step="0.01" name="swap_total_weight" id="swap_total_weight"
                                                                value="{{ old('swap_total_weight', $order->swap_details['total_weight'] ?? 1) }}"
                                                                class="form-input w-full">
                                                        </div>
                                                    </div>

                                                    {{-- <span>SMS destinatar la ridicare colet: 
                                                        @if($order->send_sms == null || $order->send_sms == 0)
                                                            <b class="p-send-sms text-red-500">Nu</b>
                                                        @else
                                                            <b class="p-send-sms text-green-500">Da</b>
                                                        @endif
                                                    </span><br> --}}
                                                    <div class="mb-2">
                                                        <label for="ramburs">Ramburs</label>
                                                        <select name="ramburs" id="ramburs" class="form-select w-full" onchange="toggleRambursFields(this.value)">
                                                            <option value="0" {{ $order->ramburs == 0 ? 'selected' : '' }}>Nu</option>
                                                            <!-- <option value="2" {{ $order->ramburs == 2 ? 'selected' : '' }}>Ramburs cash</option> -->
                                                            <option value="3" {{ $order->ramburs == 3 ? 'selected' : '' }}>Ramburs în cont</option>
                                                        </select>
                                                    </div>

                                                    <div id="ramburs-details" class="{{ in_array($order->ramburs, [2,3]) ? '' : 'hidden' }} mt-3 p-4 border-l-4 border-blue-500 bg-blue-50 rounded-md">
                                                        <div class="mb-2">
                                                            <label for="ramburs_amount" class="font-semibold">Sumă ramburs</label>
                                                            <input type="number" step="0.01" name="ramburs_amount" id="ramburs_amount"
                                                                value="{{ old('ramburs_amount', $order->ramburs_details['amount'] ?? '') }}"
                                                                class="form-input w-full">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="ramburs_account_name" class="font-semibold">Nume titular cont</label>
                                                            <input type="text" name="ramburs_account_name" id="ramburs_account_name"
                                                                value="{{ old('ramburs_account_name', $order->ramburs_details['account_name'] ?? '') }}"
                                                                class="form-input w-full">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="ramburs_iban" class="font-semibold">IBAN</label>
                                                            <input type="text" name="ramburs_iban" id="ramburs_iban"
                                                                value="{{ old('ramburs_iban', $order->ramburs_details['iban'] ?? '') }}"
                                                                class="form-input w-full">
                                                        </div>
                                                    </div>

                                                    <br>
                                                    <div class="mb-2">
                                                        <label for="assurance">Asigurare</label>
                                                        <input type="text" name="assurance" id="assurance" value="{{ old('assurance', $order->assurance) }}" class="form-input w-full">
                                                    </div>
                                                    <br>
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
                                <div class="col-span-12 md:col-span-2 self-end pb-1">
                                    <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black transition">
                                        <i class="far fa-edit mr-1" style="margin-top: 1px;"></i>
                                        {{ __('Actualizare') }}
                                    </button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
<script>
    function toggleReturFields(value) {
        const details = document.getElementById('retur-details');
        if (value == 1) {
            details.classList.remove('hidden');
        } else {
            details.classList.add('hidden');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleReturFields(document.getElementById('retur_document').value);
    });
</script>
<script>
    function toggleRambursFields(value) {
        const rambursDetails = document.getElementById('ramburs-details');
        if (value == 2 || value == 3) {
            rambursDetails.classList.remove('hidden');
        } else {
            rambursDetails.classList.add('hidden');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleRambursFields(document.getElementById('ramburs').value);
    });
</script>
<script>
    function calculateVolume(index) {
        const length = parseFloat(document.getElementById(`length_${index}`).value) || 0;
        const width = parseFloat(document.getElementById(`width_${index}`).value) || 0;
        const height = parseFloat(document.getElementById(`height_${index}`).value) || 0;
        const volume = Math.round((width * length * height) / 6000 * 100) / 100;

        document.getElementById(`volume_${index}`).textContent = volume + ' kg';
        document.getElementById(`volume_${index}`).setAttribute('data-volume', volume);

        updateTotalVolume();
    }

    function updateTotalVolume() {
        let total = 0;
        document.querySelectorAll('[id^="volume_"]').forEach(el => {
            total += parseFloat(el.getAttribute('data-volume')) || 0;
        });
        document.getElementById('total_volume_display').textContent = total.toFixed(2) + ' kg';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.calc-volume').forEach(input => {
            input.addEventListener('input', function () {
                const index = this.getAttribute('data-index');
                calculateVolume(index);
            });
        });
    });
</script>

</x-app-layout>

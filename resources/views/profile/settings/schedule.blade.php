<x-jet-form-section-simple submit="{{ route('dashboard.settings.schedule.update') }}">
    <x-slot name="form">
        <h5 class="col-span-12">{{ __('Program de lucru') }}:</h5>
        <p class="col-span-12"><i>{{ __('Configurati intervalul de timp in care curierul poate ridica coletele din locatia dvs') }}.</i></p>
        @csrf
        <div class="row col-span-12 m-0" style="min-height: 500px;">
            <div class="input-field col l4 m6 s12">
                <p>Inceput: 
                    <select name="start_pickup_hour" id="start-pickup-hour">
                        @for($i = 8 ; $i <= 15 ; $i++)
                        <option value="{{$i}}" {{ old('start_pickup_hour') == $i || (!old('start_pickup_hour') && isset($this->state['start_pickup_hour']) && $this->state['start_pickup_hour'] == $i) ? 'selected' : '' }}>{{$i}}:00</option>
                        @endfor
                    </select>
                </p>
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="print" class="mt-2 errorTxt1" />
            </div>
            <div class="input-field col l4 m6 s12">
                <p>Sfarsit: 
                    <select name="end_pickup_hour" id="end-pickup-hour">
                        @for($i = 9 ; $i <= 18 ; $i++)
                        <option value="{{$i}}" {{ old('end_pickup_hour') == $i || (!old('end_pickup_hour') && isset($this->state['end_pickup_hour']) && $this->state['end_pickup_hour'] == $i) ? 'selected' : '' }}>{{$i}}:00</option>
                        @endfor
                    </select>
                </p>
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="print" class="mt-2 errorTxt1" />
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section-simple>

@push('styles')
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
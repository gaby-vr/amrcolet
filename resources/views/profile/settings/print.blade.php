<x-jet-form-section-simple submit="{{ route('dashboard.settings.print.update') }}">
    <x-slot name="form">
        {{-- <h5 class="col-span-12"></h5> --}}
        <p class="col-span-12"><i>{{ __('In aceasta pagina puteti selecta modul in care doriti ca etichetele de transport sa va fie trimise pe mail. Din pagina "Istoric comenzi" veti avea posibilitatea sa descarcati fisierele in orice format disponibil') }}.</i></p>
        @csrf
        <div class="row col-span-12 m-0">
            <div class="input-field col l4 m6 s12">
                <select name="paper_size" id="paper_size" class="">
                    <option value="A4" {{ old('paper_size') == "A4" || (!old('paper_size') && isset($this->state['paper_size']) && $this->state['paper_size'] == "A4") ? 'selected' : '' }}>A4</option>
                    <option value="A6" {{ old('paper_size') == "A6" || (!old('paper_size') && isset($this->state['paper_size']) && $this->state['paper_size'] == "A6") ? 'selected' : '' }}>A6</option>
                </select>
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="paper_size" class="mt-2 errorTxt1" />
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
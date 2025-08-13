<x-jet-form-section-full submit="update" class="md:grid-cols-2">
    <x-slot name="title"></x-slot>
    <x-slot name="description"></x-slot>
    <x-slot name="form">
        <h5 class="col-span-6">{{ __('Plugin Wordpress') }}</h5>
        <p class="col-span-6"><i>{{ __('Pluginul folosit impreuna cu platforma ta de Wordpress.') }}.</i></p>
        @csrf
        <div class="row col-span-6 m-0">
            <div class="input-field col s12">
                <p>
                    <label>
                        <input class="with-gap" id="wordpress_active" name="wordpress_active" type="checkbox" value="1" wire:model.defer="active" @checked(old('wordpress_active', $this->active ?? '') == '1')>
                        <span class="">{{ __('Folosesc pluginul wordpress') }}</span>
                    </label>
                </p>
                <x-jet-input-error for="wordpress_active" class="mt-4 errorTxt1" />
            </div>
        </div>
        <div class="row col-span-6 m-0 show-plugin-fields {{ old('wordpress_active', $this->active ?? '') == '1' ? '' : 'hidden' }}">
            <div class="input-field col s12">
                <x-jet-label for="wordpress_domain" class="{{ isset($this->domain) ? 'active' : '' }}">{{ __('Domeniu') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="wordpress_domain" type="text" class="block w-full {{ $errors->first('wordpress_domain') ? 'invalid' : '' }}" name="wordpress_domain" wire:model.defer="domain" />
                <x-jet-input-error for="wordpress_domain" class="mt-2 errorTxt1" />
            </div>
        </div>
        <div class="row col-span-6 m-0 show-plugin-fields {{ old('wordpress_active', $this->active ?? '') == '1' ? '' : 'hidden' }}">
            <div class="input-field col s12">
                <x-jet-label for="wordpress_api_key" class="{{ isset($this->api_key) ? 'active' : '' }}">{{ __('Cheia API') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="wordpress_api_key" type="text" class="block w-full {{ $errors->first('wordpress_api_key') ? 'invalid' : '' }}" name="wordpress_api_key" wire:model.defer="api_key" />
                <x-jet-input-error for="wordpress_api_key" class="mt-2 errorTxt1" />
            </div>
        </div>
        @if(isset($this->active) && $this->active == '1')
            <div class="row col-span-6 m-0 show-plugin-fields {{ old('wordpress_active', $this->active ?? '') == '1' ? '' : 'hidden' }}">
                <div class="input-field col s12">
                    <a href="{{ route('dashboard.plugin.download') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition"><i class="material-icons small">file_download</i>&emsp;{{ __('Descarca plugin') }}</a>
                </div>
            </div>
        @endif
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section-simple-full>

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-facturare.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.js') }}"></script>
<script src="{{ asset('js/plugins/intlTelInput/js/intlTelInput.min.js') }}"></script>
<script type="text/javascript">
$(document).ready( function() {
    showElementsOnEvent('#wordpress_active','.show-plugin-fields');
});
</script>
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
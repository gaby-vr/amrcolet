<x-jet-form-section-simple submit="{{ route('dashboard.settings.security.update') }}">
    <x-slot name="form">
        <h5 class="col-span-12">{{ __('Update Password') }}</h5>
        <p class="col-span-12"><i>{{ __('Ensure your account is using a long, random password to stay secure.') }}.</i></p>
        @csrf
        <div class="row col-span-12 m-0">
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="current_password" >{{ __('Current Password') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="current_password" type="password" class="block w-full" name="current_password" required autocomplete="current_password" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="current_password" class="mt-2 errorTxt1" />
            </div>
        </div>
        <div class="row col-span-12 m-0">
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="password" >{{ __('New Password') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="password" type="password" class="block w-full" name="password" required autocomplete="new-password" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="password" class="mt-2 errorTxt1" />
            </div>
        </div>
        <div class="row col-span-12 m-0">
            <div class="input-field col l4 m6 s12">
                <x-jet-label for="password_confirmation" >{{ __('Confirm Password') }} <span class="red-text">*</span> </x-jet-label>
                <x-jet-input id="password_confirmation" type="password" class="block w-full" name="password_confirmation" required autocomplete="new-password" />
                <small class="errorTxt1 float-right red-text"></small>
                <x-jet-input-error for="password_confirmation" class="mt-2 errorTxt1" />
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
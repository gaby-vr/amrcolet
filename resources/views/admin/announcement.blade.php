@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush

@push("scripts")
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('announcement', {
        extraPlugins: 'autogrow',
        autoGrow_onStartup: true,
        width: '100%',
    });
</script>
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Editare anunt') }}</x-slot>
        <x-slot name="href"></x-slot>
        <x-slot name="btnName"></x-slot>
        <x-jet-authentication-card class="px-2 form-admin" classForm="sm:max-w-7xl">
            <x-slot name="logo"></x-slot>

            <x-jet-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('admin.announcement.update') }}">
                @csrf
                <div class="mt-4">
                    <x-jet-label for="announcement" value="{{ __('Anunt') }}" />
                    <textarea id="announcement" class="block mt-1 w-full editor" type="text" rows="10" name="announcement" required>{!! setare('ANNOUNCEMENT') !!}</textarea>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-jet-button class="ml-4">
                        {{ __('Edit') }}
                    </x-jet-button>
                </div>
            </form>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
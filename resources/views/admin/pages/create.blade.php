@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ isset($page) ? __('Editare pagina') : __('Creare pagina') }}</x-slot>
        <x-slot name="href">{{ route('admin.pages.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza pagina') }}</x-slot>
        <x-jet-authentication-card class="px-2 form-admin" classForm="sm:max-w-xl">
            <x-slot name="logo"></x-slot>

            <x-jet-validation-errors class="mb-4" />
            @if(session()->has('success'))
                <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3" role="alert">
                    <p>{{ session()->get('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ isset($page) ? route('admin.pages.update', $page) : route('admin.pages.store') }}">
                @csrf
                <div class="mt-4">
                    <x-jet-label for="title" value="{{ __('Titlu') }}*" />
                    <x-jet-input id="title" type="text" class="block mt-1 w-full" name="title" value="{{ old('title', $page->title ?? '') }}" required />
                </div>

                <div class="mt-4">
                    <x-jet-label for="slug" value="{{ __('Slug') }} ({{ __('Optional') }})" />
                    <x-jet-input id="slug" type="text" class="block mt-1 w-full" name="slug" value="{{ old('slug', $page->slug ?? '') }}" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-jet-button class="ml-4">
                        {{ isset($page) ? __('Edit') : __('Create') }}
                    </x-jet-button>
                </div>
            </form>
        </x-jet-authentication-card>
    </x-jet-admin-navigation>
</x-app-layout>
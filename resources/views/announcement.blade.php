<x-app-layout>
    <x-jet-banner />
    @livewire('navigation-menu')
    <div class="pt-16 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-7xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
                {!! setare('ANNOUNCEMENT') !!}
            </div>
        </div>
    </div>
</x-app-layout>
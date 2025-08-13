@if(session()->has('success'))
    <x-jet-alert-card type="success" {{ $attributes->merge(['class' => 'text-sm']) }}>
        <p>{{ session()->get('success') }}</p>
    </x-jet-alert-card>
@endif

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'bg-red-100 text-white text-sm px-4 py-3 shadow-md']) }} role="alert">
        <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

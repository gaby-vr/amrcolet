@props(['for'])

@error($for)
    <small {{ $attributes->merge(['class' => 'text-sm text-red-600']) }}>{{ $message }}</small>
@enderror

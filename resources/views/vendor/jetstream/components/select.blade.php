@props(['disabled' => false, 'hasError' => false])


<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full focus:ring focus:ring-opacity-50 rounded-md shadow-sm'.(
    $hasError || ($attributes['name'] && $errors->has($attributes['name'])) ? ' border-red-300 focus:border-red-300 focus:ring-red-200' : ' border-gray-300 focus:border-indigo-300 focus:ring-indigo-200'
)]) !!}>
    {{ $slot }}
</select>
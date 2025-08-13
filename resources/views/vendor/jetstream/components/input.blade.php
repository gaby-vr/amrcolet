@props(['disabled' => false, 'prefix' => false, 'p' => 8, 'wrapClasses' => '', 'hasError' => false])

@if($slot->isNotEmpty())
    <div class="affix-input-group {{ $wrapClasses }}">
        <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
            'class' => 'w-full block focus:ring focus:ring-opacity-50 rounded-md shadow-sm pr-'.$p.(
                $hasError || $errors->has($attributes['name']) ? ' border-red-300 focus:ring-red-200' : ' border-gray-300 focus:border-indigo-300 focus:ring-indigo-200'
            )
        ]) !!}>
        <span class="input-affix text-gray-500 bg-gray-100 px-2 {{ $prefix ? 'prefix rounded-l-md' : 'rounded-r-md' }}">
            {{ $slot }}
        </span>
    </div>
@else
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'focus:ring focus:ring-opacity-50 rounded-md shadow-sm'.(
        $hasError || ($attributes['name'] && $errors->has($attributes['name'])) ? ' border-red-300 focus:border-red-300 focus:ring-red-200' : ' border-gray-300 focus:border-indigo-300 focus:ring-indigo-200'
    )]) !!}>
@endif

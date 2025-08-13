@props(['checked' => ''])

<input type="checkbox" @if(is_bool($checked)) @checked($checked) @else {{ $checked }} @endif {!! $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50']) !!}>

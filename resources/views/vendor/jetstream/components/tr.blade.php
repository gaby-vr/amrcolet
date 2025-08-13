@props(['location','bg'])

@php
if(isset($bg)) {
    switch ($bg) {
        case 'black':
            $bg = 'bg-black';
            break;
        case 'white':
            $bg = 'bg-white';
            break;
        default:
            $bg = 'bg-'.$bg.'-800';
            break;
    }
} else {
    $bg = 'bg-gray-800';
}
if (isset($location)) {
    $classes = $bg.' flex flex-col flex-no wrap sm:table-row border-b-0 rounded-l-lg sm:rounded-none mb-2 sm:mb-0';
} else {
    $classes = 'flex flex-col flex-no wrap bg-white sm:table-row border-b-0 mb-2 sm:mb-0';
}
@endphp

<tr {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</tr>
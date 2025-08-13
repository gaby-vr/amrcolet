@props(['location','border'])

@php 
if(isset($border)) {
	switch ($border) {
		case 'black':
			$border = 'border-black';
			break;
		case 'white':
			$border = 'border-white';
			break;
		default:
			$border = 'border-'.$border.'-800';
			break;
	}
} else {
	$border = 'border-gray-800';
}
@endphp

@if(isset($location))
<th {{ $attributes->merge(['class' => 'p-3 border '.$border.' text-left']) }}>{{ $slot }}</th>
@else
<td {{ $attributes->merge(['class' => 'border-grey-light border hover:bg-gray-100 p-3']) }}>{{ $slot }}</td>
@endif
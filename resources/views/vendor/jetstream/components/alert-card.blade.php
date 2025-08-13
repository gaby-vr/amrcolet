@props(['type' => 'info'])

@php
switch ($type) {
	case 'success':
		$bg = 'bg-green-100';
		$text = 'text-green-900';
		break;
	case 'warning':
		$bg = 'bg-yellow-100';
		$text = 'text-yellow-900';
		break;
	case 'danger':
		$bg = 'bg-red-100';
		$text = 'text-red-900';
		break;
	case 'info':
		$bg = 'bg-blue-100';
		$text = 'text-blue-900';
		break;
	default:
		$bg = 'bg-gray-100';
		$text = 'text-gray-900';
		break;
}
@endphp
<div {{ $attributes->merge(['class' => 'rounded-b px-4 py-3 shadow-md '.$bg.' '.$text]) }} role="alert">
  	<div class="flex">
	    <div>
	      	{{ $slot }}
	    </div>
  	</div>
</div>
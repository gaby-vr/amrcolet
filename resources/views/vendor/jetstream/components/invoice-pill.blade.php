@props(['invoice', 'type' => 1])

@php 
    switch ($invoice->status) {
        case 0:
            $class = $type == 1 ? 'orange' : 'bg-yellow-200 text-yellow-600';
            break;
        case 1:
            $class = $type == 1 ? 'green' : 'bg-green-100 text-green-500';
            break;
        case 2:
            $class = $type == 1 ? 'red' : 'bg-red-200 text-red-600';
            break;
        case 3:
            $class = $type == 1 ? 'blue' : 'bg-blue-200 text-blue-600';
            break;
        case 4:
            $class = $type == 1 ? 'red' : 'bg-red-200 text-red-600';
            break;
    }
@endphp

@if($type == 1)
    <span class="chip {{ $class }} lighten-5 m-0">
        <span class="{{ $class }}-text">{{ $invoice->status_text }}</span>
    </span>
@else
    <span class="{{ $class }} py-2 px-3 rounded-full text-sm">{{ $invoice->status_text }}</span>
@endif

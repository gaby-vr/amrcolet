@props(['order', 'color' => null])

@if($color || !empty($order->status_color))
@php
if($color === null) {
	if($order->status_color === 'orange') {
		$color = 'yellow';
		$color_opacity = '500';
		$text_color_opacity = '800';
	}
}
@endphp

<span {{ $attributes->merge(['class' => 'bg-'.($color ?? $order->status_color).'-'.($color_opacity ?? '200').' text-'.($color ?? $order->status_color).'-'.($text_color_opacity ?? '600').' py-2 px-3 rounded-full text-sm inline-block']) }}>
	{{ $order->status_text ?? '' }}
	@if(isset($order->cancelRequest) && $order->cancelRequest != null)
        <br>{{ $order->cancelRequest->type == '2' ? __('cu ramburs in cont') : __('cu ramburs in credite') }}
    @endif
</span>
@endif
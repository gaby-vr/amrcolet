@props(['order'])

@if($order->api == 2)
    <a href="https://tracking.dpd.ro/?shipmentNumber={{ $order->api_shipment_awb }}&language=ro" target="_blank">{{ $order->api_shipment_awb }}</a>
@elseif($order->api == 3)
    @foreach($order->awbLabels ? $order->awbLabels->parcel_awb_list ?? [] : [] as $awb)
        <a href="https://gls-group.com/RO/en/parcel-tracking?match={{ $awb }}" target="_blank">{{ $awb }}</a><br>
    @endforeach
@else
    <a href="https://www.cargus.ro/personal/urmareste-coletul?tracking_number={{ $order->api_shipment_awb }}" target="_blank">{{ $order->api_shipment_awb }}</a>
@endif
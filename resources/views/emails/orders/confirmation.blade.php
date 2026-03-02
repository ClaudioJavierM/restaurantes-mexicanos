<x-mail::message>
# ¡Gracias por tu pedido!

Hola **{{ $order->customer_name }}**,

Tu pedido ha sido recibido y está siendo procesado.

---

## Detalles del Pedido

**Número de Pedido:** #{{ $order->order_number }}  
**Restaurante:** {{ $order->restaurant->name }}  
**Tipo de Orden:** {{ $order->order_type === 'pickup' ? 'Para Recoger' : ($order->order_type === 'delivery' ? 'Entrega a Domicilio' : 'Comer en el Local') }}

@if($order->scheduled_for)
**Programado para:** {{ $order->scheduled_for->format('d/m/Y h:i A') }}
@else
**Tiempo estimado:** Lo antes posible
@endif

---

## Tu Orden

<x-mail::table>
| Platillo | Cantidad | Precio |
|:---------|:--------:|-------:|
@foreach($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | ${{ number_format($item->total_price, 2) }} |
@endforeach
</x-mail::table>

---

**Subtotal:** ${{ number_format($order->subtotal, 2) }}  
@if($order->tax > 0)
**Impuestos:** ${{ number_format($order->tax, 2) }}  
@endif
@if($order->delivery_fee > 0)
**Envío:** ${{ number_format($order->delivery_fee, 2) }}  
@endif
@if($order->tip > 0)
**Propina:** ${{ number_format($order->tip, 2) }}  
@endif
@if($order->discount > 0)
**Descuento:** -${{ number_format($order->discount, 2) }}  
@endif

## **Total: ${{ number_format($order->total, 2) }}**

---

@if($order->order_type === 'pickup')
## Dirección para Recoger

**{{ $order->restaurant->name }}**  
{{ $order->restaurant->address }}  
{{ $order->restaurant->city }}, {{ $order->restaurant->state }} {{ $order->restaurant->zip_code }}

@if($order->restaurant->phone)
📞 {{ $order->restaurant->phone }}
@endif
@endif

@if($order->order_type === 'delivery')
## Dirección de Entrega

{{ $order->delivery_address }}  
{{ $order->delivery_city }}, {{ $order->delivery_zip }}

@if($order->delivery_instructions)
**Instrucciones:** {{ $order->delivery_instructions }}
@endif
@endif

@if($order->special_instructions)
## Instrucciones Especiales

{{ $order->special_instructions }}
@endif

---

<x-mail::button :url="route('order.confirmation', $order->order_number)">
Ver Estado del Pedido
</x-mail::button>

Si tienes alguna pregunta sobre tu pedido, contacta directamente al restaurante.

¡Gracias por usar **Restaurantes Mexicanos Famosos**!

<x-mail::subcopy>
Este es un correo automático. Por favor no respondas a este mensaje.
</x-mail::subcopy>
</x-mail::message>

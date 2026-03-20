<h1>Order Receipt - Vape Shop</h1>
<p>Hi {{ $order->customer_name }},</p>
<p>Thank you for your purchase! Your payment has been confirmed.</p>
<hr>
<p><strong>Order ID:</strong> #{{ $order->id }}</p>
<p><strong>Product:</strong> {{ $order->product_name }}</p>
<p><strong>Amount Paid:</strong> ₱{{ number_format($order->amount, 2) }}</p>
<p><strong>Date:</strong> {{ $order->updated_at }}</p>
<hr>
<p>We are now preparing your items for shipment!</p>
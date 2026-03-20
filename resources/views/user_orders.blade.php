<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        :root { --primary: #4f46e5; --dark: #1e293b; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); padding: 50px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-unpaid { background: #fef3c7; color: #92400e; }
        .status-shipping { background: #e0e7ff; color: #3730a3; }
        .tracking-box { background: #f1f5f9; padding: 10px; border-radius: 8px; font-size: 0.85rem; border-left: 4px solid var(--primary); }
        
        .btn-cancel {
            background: #fee2e2;
            color: #b91c1c;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-cancel:hover { background: #fecaca; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>My Shopping Orders</h1>
        <div>
            <a href="{{ route('user.orders') }}" style="text-decoration: none; background: #f1f5f9; padding: 8px 15px; border-radius: 8px; color: var(--dark); margin-right: 10px; font-size: 0.85rem;">🔄 Refresh Status</a>
            <a href="{{ route('user.dashboard') }}" style="text-decoration: none; color: var(--primary); font-weight: bold;">← Back to Shop</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-top: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Product</th> 
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Tracking / Info</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($myOrders as $order)
            <tr>
                {{-- Column 1: Product --}}
                <td><strong>{{ $order->product_name }}</strong></td>
                
                {{-- Column 2: Payment Status --}}
                <td>
                    @if($order->payment_status === 'Paid')
                        <span class="badge status-paid">PAID</span>
                    @else
                        <span class="badge status-unpaid">UNPAID</span>
                    @endif
                </td>

                {{-- Column 3: Order Status --}}
                <td>
                    @php $os = strtolower($order->status); @endphp
                    @if($os === 'pending')
                        <span class="badge" style="background:#f1f5f9; color:#475569;">PENDING</span>
                    @elseif($os === 'shipped')
                        <span class="badge status-shipping">SHIPPED</span>
                    @elseif($os === 'delivered')
                        <span class="badge status-paid">DELIVERED</span>
                    @else
                        <span class="badge" style="background:#f1f5f9; color:#475569;">{{ strtoupper($order->status) }}</span>
                    @endif
                </td>

                {{-- Column 4: Tracking --}}
                <td>
                    @if($order->tracking_number)
                        <div class="tracking-box"><strong>{{ $order->courier }}</strong><br>#{{ $order->tracking_number }}</div>
                    @else
                        <span style="color: #94a3b8; font-style: italic;">
                            {{ strtolower($order->payment_status) === 'paid' ? 'Preparing shipment...' : 'Awaiting Payment' }}
                        </span>
                    @endif
                </td>

                {{-- Column 5: Date --}}
                <td>{{ date('M d, Y', strtotime($order->created_at)) }}</td>

                {{-- Column 6: Action (Cancel Button) --}}
                <td>
                    @if($os === 'pending')
                        <form action="{{ route('user.orders.delete', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-cancel">Cancel Order</button>
                        </form>
                    @else
                        <span style="color: #cbd5e1; font-size: 0.75rem;">Locked</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align: center; padding: 30px;">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>
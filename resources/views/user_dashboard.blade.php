<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vape Store | Available Products</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --dark: #1e293b;
            --bg: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --white: #ffffff;
        }

        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background-color: var(--bg); 
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 10%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 600;
            margin-left: 25px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .track-btn { color: var(--primary); }
        .track-btn:hover { color: var(--primary-hover); }
        .logout-btn { color: #ef4444; }

        .container {
            padding: 40px 10%;
        }

        .alert {
            padding: 16px;
            background: #dcfce7;
            color: #166534;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid #bbf7d0;
            font-weight: 500;
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-section {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 50px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .cart-section h2 {
            margin-top: 0;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th {
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        td {
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
        }

        .total-row td {
            border-bottom: none;
            padding-top: 25px;
            font-size: 1.1rem;
        }

        .order-note-container {
            margin-bottom: 20px;
        }

        .order-note-container label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .order-note-container textarea {
            width: 100%;
            max-width: 500px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            resize: vertical;
            outline: none;
            transition: border-color 0.2s;
        }

        .order-note-container textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 0.9rem;
        }

        .checkout-btn {
            background: var(--primary);
            color: white;
        }

        .checkout-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .clear-btn {
            background: #f1f5f9;
            color: #475569;
        }

        .clear-btn:hover {
            background: #e2e8f0;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .product-card img, .no-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }

        .no-image {
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-weight: 500;
        }

        .product-content {
            padding: 20px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-content h3 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .price {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .buy-btn {
            background: var(--dark);
            color: white;
            width: 100%;
        }

        .buy-btn:hover {
            background: #000;
            transform: scale(1.02);
        }

        .update-qty-btn {
            padding: 5px 10px;
            background: var(--primary);
            color: white;
            font-size: 0.75rem;
            border-radius: 6px;
        }

        .remove-link {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .navbar { padding: 15px 5%; }
            .container { padding: 30px 5%; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 15px;">
            <h1>Vape.</h1>
            @if(isset($user) && $user->role == 'Top Customer')
                <span style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; border: 1px solid #f59e0b;">👑 TOP CUSTOMER</span>
            @endif
        </div>
        <div class="nav-links">
            <a href="{{ route('user.orders') }}" class="track-btn">📋 Track Orders</a>
            <a href="{{ route('logout') }}" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert" id="success-alert">
                <span>✅ {{ session('success') }}</span>
            </div>

            <script>
                setTimeout(function() {
                    const alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.style.transition = "opacity 0.5s ease";
                        alert.style.opacity = "0";
                        setTimeout(() => alert.remove(), 500);
                    }
                }, 4000);
            </script>
        @endif

        <div class="cart-section">
            <h2>Your Shopping Bag</h2>
            @if(session('cart') && count(session('cart')) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach(session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity']; @endphp
                            <tr>
                                <td style="font-weight: 600;">{{ $details['name'] }}</td>
                                <td>
                                    <form action="{{ route('cart.update') }}" method="POST" style="display: flex; align-items: center; gap: 8px;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}" min="1" 
                                               style="width: 50px; padding: 4px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                        <button type="submit" class="btn update-qty-btn">Update</button>
                                    </form>
                                </td>
                                <td>₱{{ number_format($details['price'], 2) }}</td>
                                <td style="font-weight: 700;">₱{{ number_format($details['price'] * $details['quantity'], 2) }}</td>
                                <td>
                                    <a href="{{ route('cart.remove', $id) }}" class="remove-link">Remove</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; color: var(--text-muted);">Total Amount:</td>
                            <td colspan="2"><strong style="color: var(--dark); font-size: 1.4rem;">₱{{ number_format($total, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                
             <form action="{{ route('checkout') }}" method="POST">
    @csrf
    @if(session('error'))
    <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
        {{ session('error') }}
    </div>
@endif
    
    <div class="order-note-container">
        <label for="message">Order Note (Optional):</label>
        <textarea name="message" id="message" rows="3" class="form-control" placeholder="e.g. Leave at front door..."></textarea>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary" style="background-color: #5a57d6;">
            Complete Purchase
        </button>
        <button type="submit" form="clear-cart-form" class="btn btn-outline-secondary">
            Clear Bag
        </button>
    </div>
</form>

<form id="clear-cart-form" action="{{ route('cart.clear') }}" method="POST" style="display:none;">
    @csrf
</form>

            @else
                <p style="color: var(--text-muted); font-style: italic;">Your shopping bag is currently empty.</p>
            @endif
        </div>

        <h2 style="margin-bottom: 30px; font-weight: 800; font-size: 1.75rem;">New Arrivals</h2>
        
        <div class="product-grid">
        @forelse($products as $product)
            <div class="product-card">
                @if($product->image)
                    <img src="{{ asset('products/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <div class="no-image">No Preview Available</div>
                @endif
                
                <div class="product-content">
                    <div>
                        <h3>{{ $product->name }}</h3>
                        <div class="price">₱{{ number_format($product->price, 2) }}</div>
                    </div>
                    
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn buy-btn">Add to Bag</button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: var(--text-muted);">
                <p>We're currently restocking. Check back soon!</p>
            </div>
        @endforelse
        </div>
    </div>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Order Note</title>
    <style>
        body { font-family: sans-serif; background: #f8fafc; padding: 50px; }
        .card { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 100px; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4f46e5; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Edit Instructions for Order #{{ $order->id }}</h2>
        <p>Product: <strong>{{ $order->product_name }}</strong></p>
        
        <form action="{{ route('user.orders.update', $order->id) }}" method="POST">
            @csrf
            <label>Update your note/instructions:</label>
            <textarea name="message">{{ $order->message }}</textarea>
            <button type="submit">Save Changes</button>
            <a href="{{ route('user.orders') }}" style="margin-left: 10px; color: #64748b;">Cancel</a>
        </form>
    </div>
</body>
</html>
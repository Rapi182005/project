<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { display: flex; margin: 0; font-family: sans-serif; background: #f4f7f6; }
        /* Sidebar Styles */
        .sidebar { width: 250px; height: 100vh; background: #2c3e50; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; color: #3498db; }
        .sidebar a { display: block; color: white; padding: 15px; text-decoration: none; margin-bottom: 5px; border-radius: 5px; }
        .sidebar a:hover { background: #34495e; }
        .sidebar a.active { background: #3498db; }
        
        /* Main Content */
        .main-content { margin-left: 270px; padding: 40px; width: 100%; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .btn { padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; border: none; cursor: pointer; }
        .btn-add { background: #27ae60; color: white; }
        .btn-edit { background: #f39c12; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="{{ route('admin.dashboard') }}" class="active">🏠 Home (Products)</a>
        <a href="{{ route('admin.orders') }}">📦 Customer Orders</a>
        <a href="{{ route('tracker.index') }}">📍 Package Tracker</a>
        <hr>
        <a href="{{ route('logout') }}" style="color: #e74c3c;">Logout</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h1>Product Management</h1>
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="number" name="price" placeholder="Price" step="0.01" required>
                <button type="submit" class="btn btn-add">Add Product</button>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td>
                            <a href="#" class="btn btn-edit">Edit</a>
                            <a href="{{ route('admin.products.delete', $product->id) }}" class="btn btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
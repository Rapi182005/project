<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Product Management</title>
    <style>
        :root { --primary: #4f46e5; --dark: #1e293b; --bg: #f8fafc; --text: #334155; }
        body { display: flex; margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); }
        
        .sidebar { width: 280px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 30px 20px; box-sizing: border-box; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 40px; color: #fff; letter-spacing: -0.5px; }
        .sidebar a { color: #94a3b8; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; border-radius: 8px; margin-bottom: 10px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background: var(--primary); color: white; }

        .content { margin-left: 280px; padding: 50px; width: 100%; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 20px; }
        
        input, select { padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; margin-right: 10px; outline: none; transition: 0.2s; }
        input:focus { border-color: var(--primary); ring: 2px var(--primary); }
        
        .btn { background: var(--primary); color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-block; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; margin-top: 10px; }
        th { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; padding: 10px 20px; text-align: left; }
        tr { background: white; transition: 0.2s; }
        td { padding: 15px 20px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
        td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        
        .prod-img { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; }

        .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 10% auto; padding: 30px; border-radius: 12px; width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .modal-content h3 { margin-top: 0; }
        .modal-content input { width: 100%; margin-bottom: 15px; display: block; }

        /* Gmail Card Specific Style */
        .gmail-card { border-left: 4px solid #ea4335; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin.</h2>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📦 Products</a>
        <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders') ? 'active' : '' }}">🛒 Orders</a>
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">👥 Users</a>
        <a href="{{ route('admin.revenue') }}" class="{{ request()->is('admin/revenue') ? 'active-link' : '' }}">📊 Revenue</a>
        
        <a href="{{ route('logout') }}" 
           onclick="return confirm('Are you sure you want to logout?')" 
           style="margin-top: 100px; color: #f87171;">Logout</a>
    </div>

    <div class="content">
        <h1 style="margin-top: 0; font-weight: 800;">Product Management</h1>
        
        @if(session('success'))
            <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <h3 style="margin-top: 0;">Add New Item</h3>
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="number" name="price" placeholder="0.00" step="0.01" required>
                <input type="file" name="image">
                <button type="submit" class="btn">Save Product</button>
            </form>
        </div>

        <div class="card gmail-card">
            <h3 style="margin-top: 0; color: #ea4335;">Gmail API Settings</h3>
            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 15px;">
                Authorize your store to send automatic order confirmations and invoices via Gmail.
            </p>
            <a href="{{ route('gmail.login') }}" class="btn" style="background: #ea4335;">Connect Store Gmail</a>
            @if(file_exists(storage_path('app/google/token.json')))
                <span style="margin-left: 15px; color: #166534; font-weight: 600; font-size: 0.875rem;">● API Connected</span>
            @else
                <span style="margin-left: 15px; color: #64748b; font-size: 0.875rem;">● Not Connected</span>
            @endif
        </div>

        <table>
            <thead>
                <tr><th>Image</th><th>Name</th><th>Price</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td><img src="{{ asset('products/'.$product->image) }}" class="prod-img"></td>
                    <td style="font-weight: 600;">{{ $product->name }}</td>
                    <td>₱{{ number_format($product->price, 2) }}</td>
                    <td>
                        <a href="javascript:void(0)" 
                           onclick="editProduct('{{ $product->id }}', '{{ $product->name }}', '{{ $product->price }}')" 
                           style="color:var(--primary); text-decoration:none; font-weight:600; margin-right: 15px;">Edit</a>
                        
                        <a href="{{ route('admin.products.delete', $product->id) }}" 
                           onclick="return confirm('Are you sure you want to delete this product?')"
                           style="color:#ef4444; text-decoration:none; font-weight:600;">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Product</h3>
            <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <label style="font-size: 0.8rem; color: #64748b;">Product Name</label>
                <input type="text" name="name" id="edit_name" required>
                
                <label style="font-size: 0.8rem; color: #64748b;">Price (₱)</label>
                <input type="number" name="price" id="edit_price" step="0.01" required>
                
                <label style="font-size: 0.8rem; color: #64748b;">Update Image (Optional)</label>
                <input type="file" name="image">
                
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" class="btn">Update Product</button>
                    <button type="button" class="btn" onclick="closeModal()" style="background: #94a3b8;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editProduct(id, name, price) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('editForm').action = "/admin/products/update/" + id;
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) closeModal();
        }
    </script>
</body>
</html>
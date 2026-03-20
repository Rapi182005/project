<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders | ShopAdmin</title>
    <style>
        :root { 
            --primary: #4f46e5; 
            --dark: #1e293b; 
            --bg: #f8fafc; 
            --text: #334155; 
            --sidebar-width: 280px;
        }

        body { 
            display: flex; 
            margin: 0; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background: var(--bg); 
            color: var(--text); 
        }
        
        /* Sidebar Navigation */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            background: var(--dark); 
            color: white; 
            position: fixed; 
            padding: 30px 20px; 
            box-sizing: border-box; 
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar h2 { 
            font-size: 1.5rem; 
            margin-bottom: 40px; 
            color: #fff; 
            letter-spacing: -0.5px; 
            font-weight: 800;
        }
        .sidebar a { 
            color: #94a3b8; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            padding: 12px 15px; 
            border-radius: 8px; 
            margin-bottom: 10px; 
            transition: 0.2s; 
            font-weight: 500;
        }
        .sidebar a:hover { 
            background: rgba(255,255,255,0.1); 
            color: white; 
        }
        .sidebar a.active-link { 
            background: var(--primary); 
            color: white; 
        }

        /* Main Content Area */
        .content { 
            margin-left: var(--sidebar-width); 
            padding: 50px; 
            width: calc(100% - var(--sidebar-width)); 
            box-sizing: border-box;
        }
        
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 { 
            margin: 0; 
            font-weight: 800; 
            font-size: 2rem;
            color: #1e293b;
        }

        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            display: block;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark);
        }
        .stat-subtext {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 5px;
            display: block;
        }

        /* Controls: Search & Filter */
        .controls-group {
            display: flex;
            gap: 15px;
        }

        .search-input, .filter-select {
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            outline: none;
            font-size: 0.9rem;
            transition: 0.3s;
            background: white;
        }
        
        .search-input { width: 300px; }
        .filter-select { cursor: pointer; min-width: 160px; }

        .search-input:focus, .filter-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Table Styling */
        .card { 
            background: white; 
            border: 1px solid #e2e8f0; 
            border-radius: 12px; 
            padding: 30px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
        }

        .alert-success { 
            padding: 15px 20px; 
            background: #dcfce7; 
            color: #166534; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            border-left: 5px solid #22c55e;
            font-weight: 500;
        }

        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0; 
        }
        th { 
            color: #64748b; 
            font-weight: 600; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            padding: 15px 20px; 
            text-align: left; 
            border-bottom: 2px solid #f1f5f9;
        }
        .order-row:hover { background: #f8fafc; }
        td { 
            padding: 20px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .badge { 
            padding: 6px 14px; 
            border-radius: 9999px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            display: inline-block;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-shipped { background: #e0e7ff; color: #3730a3; }
        .status-delivered { background: #dcfce7; color: #166534; }

        .btn-update { 
            background: var(--primary); 
            color: white; 
            border: none; 
            padding: 10px; 
            border-radius: 6px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: 0.2s; 
            width: 100%;
        }

        input[readonly] { 
            background-color: #f1f5f9; 
            border-style: dashed; 
            color: #64748b;
        }

        .action-form { 
            display: flex; 
            flex-direction: column; 
            gap: 8px; 
            width: 200px; 
        }

        /* Order Message Modal Styling */
        .btn-view-note {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            cursor: pointer;
            margin-top: 5px;
            font-weight: 600;
        }
        .btn-view-note:hover { background: #e2e8f0; }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .modal-card h3 { margin-top: 0; color: var(--dark); }
        .modal-text { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; line-height: 1.6; margin-bottom: 20px; }
        .btn-close { width: 100%; padding: 10px; background: var(--dark); color: white; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin.</h2>
    <a href="{{ route('admin.dashboard') }}">📦 Products</a>
    <a href="{{ route('admin.orders') }}" class="active-link">🛒 Orders</a>
    <a href="{{ route('admin.users') }}">👥 Users</a>
    <a href="{{ route('admin.revenue') }}" class="{{ request()->is('admin/revenue') ? 'active-link' : '' }}">📊 Revenue</a>
    
    <a href="{{ route('logout') }}" 
       onclick="return confirm('Are you sure you want to logout?')" 
       style="margin-top: auto; color: #f87171;">Logout</a>
</div>

<div class="content">
    <div class="header-flex">
        <h1>Customer Orders</h1>
        <div class="controls-group">
            <select id="statusFilter" class="filter-select" onchange="applyFilters()">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
            </select>
            <input type="text" id="orderSearch" class="search-input" placeholder="Search customer or tracking..." onkeyup="applyFilters()">
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Pending</span>
            <div class="stat-value" style="color: #92400e;">
                {{ $orders->where('status', 'Pending')->count() }}
            </div>
            <span class="stat-subtext">Awaiting fulfillment</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Completed</span>
            <div class="stat-value" style="color: #166534;">
                {{ $orders->where('status', 'Delivered')->count() }}
            </div>
            <span class="stat-subtext">Successfully delivered</span>
        </div>
    </div>

    <div class="card">
        @if(session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif

        <table id="ordersTable">
            <thead>
                <tr>
                    <th>Tracking Info</th> 
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date Placed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="order-row" data-status="{{ strtolower($order->status) }}">
                    <td class="track-num">
                        @if($order->tracking_number)
                           <div style="font-size: 0.8rem;">
                               <strong>{{ $order->courier }}:</strong><br>
                               <span class="existing-tracking" style="color: #64748b;">{{ $order->tracking_number }}</span>
                           </div>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">No info</span>
                        @endif
                    </td>
                    <td class="cust-name">
                        <strong>{{ $order->customer_name }}</strong>
                        @if(!empty($order->message))
                            <br>
                            <button class="btn-view-note" onclick="showNote('{{ $order->customer_name }}', '{{ addslashes($order->message) }}')">
                                📩 View Note
                            </button>
                        @endif
                    </td>
                    <td>{{ $order->product_name }}</td>
                    <td style="font-weight: 700; color: var(--primary);">₱{{ number_format($order->amount, 2) }}</td>
                    
                    <td>
    @if($order->payment_status === 'Paid')
        <span class="badge status-delivered">PAID</span>
        <div style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">
            Verified via Xendit
        </div>
    @else
        <span class="badge status-pending">UNPAID</span>
        
        @if($order->external_id)
            <form action="{{ route('admin.orders.check-payment', $order->id) }}" method="POST" style="margin-top: 5px;">
                @csrf
                <button type="submit" class="btn-view-note" style="color: var(--primary); border-color: var(--primary);">
                    🔄 Verify Payment
                </button>
            </form>
        @endif
    @endif
</td>

                    <td>
                        <span class="badge status-{{ strtolower($order->status) }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td style="color: #64748b; font-size: 0.85rem;">{{ date('M d, Y h:i A', strtotime($order->created_at)) }}</td>
                    <td>
                        <form class="action-form" action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                            @csrf
                            <select name="status">
                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Shipped" {{ $order->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="Delivered" {{ $order->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                            
                            <select name="courier" onchange="generateUniqueTracking(this)">
                                <option value="">Select Courier</option>
                                <option value="J&T Express" {{ $order->courier == 'J&T Express' ? 'selected' : '' }}>J&T Express</option>
                                <option value="LBC Express" {{ $order->courier == 'LBC Express' ? 'selected' : '' }}>LBC Express</option>
                            </select>

                            <input type="text" name="tracking_number" placeholder="Tracking Number" value="{{ $order->tracking_number }}" readonly>
                            
                            <button type="submit" class="btn-update">Update Order</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 60px 0;">
                        No orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="noteModal" class="modal-overlay">
    <div class="modal-card">
        <h3 id="modalTitle">Order Note</h3>
        <div id="modalBody" class="modal-text"></div>
        <button class="btn-close" onclick="closeNote()">Close Message</button>
    </div>
</div>

<script>
    function showNote(name, note) {
        document.getElementById('modalTitle').innerText = "Note from " + name;
        document.getElementById('modalBody').innerText = note;
        document.getElementById('noteModal').style.display = 'flex';
    }

    function closeNote() {
        document.getElementById('noteModal').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('noteModal');
        if (event.target == modal) closeNote();
    }

    function applyFilters() {
        const searchText = document.getElementById('orderSearch').value.toLowerCase();
        const statusLimit = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('.order-row');

        rows.forEach(row => {
            const customer = row.querySelector('.cust-name').innerText.toLowerCase();
            const tracking = row.querySelector('.track-num').innerText.toLowerCase();
            const rowStatus = row.getAttribute('data-status');

            const matchesSearch = customer.includes(searchText) || tracking.includes(searchText);
            const matchesStatus = (statusLimit === 'all' || rowStatus === statusLimit);

            row.style.display = (matchesSearch && matchesStatus) ? "" : "none";
        });
    }

    function generateUniqueTracking(selectElement) {
        const form = selectElement.closest('form');
        const trackingInput = form.querySelector('input[name="tracking_number"]');
        
        if (selectElement.value === "") {
            trackingInput.value = "";
            return;
        }

        if (trackingInput.value.trim() === '') {
            let result = '';
            const length = selectElement.value === 'J&T Express' ? 12 : 10;
            const chars = '0123456789';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const salt = Date.now().toString().slice(-3);
            trackingInput.value = result.slice(0, -3) + salt;
        }
    }
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue Report | ShopAdmin</title>
    <style>
        :root { --primary: #4f46e5; --dark: #1e293b; --bg: #f8fafc; --text: #334155; --sidebar-width: 280px; }
        body { display: flex; margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }
        
        .sidebar { width: var(--sidebar-width); height: 100vh; background: var(--dark); color: white; position: fixed; padding: 30px 20px; box-sizing: border-box; display: flex; flex-direction: column; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 40px; color: #fff; font-weight: 800; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 8px; margin-bottom: 10px; display: block; font-weight: 500; }
        .sidebar a.active-link { background: var(--primary); color: white; }
        
        .content { margin-left: var(--sidebar-width); padding: 50px; width: calc(100% - var(--sidebar-width)); box-sizing: border-box; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .stat-label { font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .stat-value { font-size: 2rem; font-weight: 800; color: var(--dark); margin-top: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #f1f5f9; color: #64748b; text-transform: uppercase; font-size: 0.75rem; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin.</h2>
    <a href="{{ route('admin.dashboard') }}">📦 Products</a>
    <a href="{{ route('admin.orders') }}">🛒 Orders</a>
    <a href="{{ route('admin.users') }}">👥 Users</a>
    <a href="{{ route('admin.revenue') }}" class="active-link">📊 Revenue</a>
    <a href="{{ route('logout') }}" style="margin-top: auto; color: #f87171;">Logout</a>
</div>

<div class="content">
    <h1>Weekly Revenue Report</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Weekly Revenue (Last 7 Days)</div>
            <div class="stat-value" style="color: #166534;">
                ₱{{ number_format($orders->filter(function($o) { 
                    return \Carbon\Carbon::parse($o->created_at)->greaterThanOrEqualTo(now()->subDays(7)); 
                })->sum('amount'), 2) }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Lifetime Revenue</div>
            <div class="stat-value" style="color: var(--primary);">
                ₱{{ number_format($orders->sum('amount'), 2) }}
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 30px;">
    <h3>🏆 Customer Leaderboard</h3>
    <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Users with the highest number of delivered purchases are automatically promoted to Top Customer.</p>
    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Current Role</th>
                <th>Delivered Orders</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaderboard as $leader)
            <tr>
                <td>
                    <strong style="color: var(--dark);">{{ $leader->name }}</strong><br>
                    <small style="color: #94a3b8;">{{ $leader->email }}</small>
                </td>
                <td>
                    @if($leader->role == 'Top Customer')
                        <span class="badge badge-top">👑 Top Customer</span>
                    @else
                        <span class="badge badge-user">Standard User</span>
                    @endif
                </td>
                <td style="font-weight: 800; color: var(--primary);">
                    {{ $leader->total_purchases }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #94a3b8; padding: 20px;">No delivered orders yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
    <div class="card">
        <h3>Day-by-Day Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders Completed</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $last7Days = collect();
                    for ($i = 0; $i < 7; $i++) { $last7Days->push(now()->subDays($i)->format('Y-m-d')); }
                @endphp
                @foreach($last7Days as $date)
                    @php
                        $dayData = $orders->filter(fn($o) => \Carbon\Carbon::parse($o->created_at)->format('Y-m-d') == $date);
                    @endphp
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y (D)') }}</strong></td>
                        <td>{{ $dayData->count() }} items sold</td>
                        <td style="font-weight: 700; color: var(--primary);">₱{{ number_format($dayData->sum('amount'), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
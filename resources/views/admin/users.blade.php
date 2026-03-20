<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | ShopAdmin</title>
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
        
        h1 { 
            margin: 0 0 30px 0; 
            font-weight: 800; 
            font-size: 2rem;
            color: #1e293b;
        }

        /* Stats Section - Matches Orders Page */
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

        /* Table Card Styling */
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
        
        .alert-error { 
            padding: 15px 20px; 
            background: #fee2e2; 
            color: #b91c1c; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            border-left: 5px solid #ef4444;
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
        tr:hover { background: #f8fafc; }
        td { 
            padding: 20px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 0.95rem;
            vertical-align: middle;
        }

        /* Badges & Buttons */
        .badge { 
            padding: 6px 12px; 
            border-radius: 9999px; 
            font-size: 0.7rem; 
            font-weight: 700; 
            text-transform: uppercase;
        }
        .badge-top { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        .badge-user { background: #f1f5f9; color: #475569; }

        .btn-promote {
            background: #e0e7ff;
            color: #3730a3;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.85rem;
            transition: 0.2s;
        }
        .btn-promote:hover { background: #c7d2fe; }

        .btn-delete { 
            background: #fee2e2; 
            color: #b91c1c; 
            border: none; 
            padding: 8px 15px; 
            border-radius: 6px; 
            font-weight: 600; 
            cursor: pointer; 
            font-size: 0.85rem;
            transition: 0.2s;
        }
        .btn-delete:hover { background: #fecaca; }

        .action-container {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin.</h2>
    <div style="padding: 0 15px; margin-bottom: 20px; color: #6366f1; font-weight: bold; font-size: 0.8rem; text-transform: uppercase;">
        Role: {{ session('user_role') }}
    </div>
    <a href="{{ route('admin.dashboard') }}">📦 Manage Products</a>
    <a href="{{ route('admin.orders') }}">🛒 View Orders</a>
    <a href="{{ route('admin.users') }}" class="active-link">👥 Manage Users</a>
    <a href="{{ route('admin.revenue') }}" class="{{ request()->is('admin/revenue') ? 'active-link' : '' }}">📊 Revenue</a>
    
    <a href="{{ route('logout') }}" 
       onclick="return confirm('Are you sure you want to logout?')" 
       style="margin-top: auto; color: #f87171;">Logout</a>
</div>

<div class="content">
    <h1>User Management</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Users</span>
            <div class="stat-value">{{ $users->count() }}</div>
            <span class="stat-subtext">Registered accounts</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Top Customers</span>
            <div class="stat-value" style="color: #92400e;">{{ $users->where('role', 'Top Customer')->count() }}</div>
            <span class="stat-subtext">Active VIP members</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Standard Users</span>
            <div class="stat-value" style="color: var(--primary);">{{ $users->where('role', '!=', 'Top Customer')->count() }}</div>
            <span class="stat-subtext">Regular customers</span>
        </div>
    </div>

    <div class="card">
        @if(session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert-error">❌ {{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>User Info</th>
                    <th>Email Address</th>
                    <th>Status / Role</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <strong style="color: var(--dark); font-size: 1rem;">{{ $user->name }}</strong>
                        <div style="font-size: 0.75rem; color: #94a3b8;">ID: #{{ $user->id }}</div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role == 'Top Customer')
                            <span class="badge badge-top">👑 Top Customer</span>
                        @else
                            <span class="badge badge-user">Standard User</span>
                        @endif
                    </td>
                    <td style="color: #64748b;">{{ date('M d, Y', strtotime($user->created_at)) }}</td>
                    <td>
                        <div class="action-container">
                            @if(session('user_role') === 'Admin')
                                <form action="{{ route('admin.users.promote', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to {{ $user->role == 'Top Customer' ? 'demote this user to Standard' : 'promote this user to Top Customer' }}?')">
                                    @csrf
                                    <button type="submit" class="btn-promote">
                                        {{ $user->role == 'Top Customer' ? 'Demote' : 'Promote' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure? This will permanently delete this account.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>     
                            @else
                                <span style="color: #94a3b8; font-style: italic; font-size: 0.8rem;">View Only</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 40px;">No registered users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
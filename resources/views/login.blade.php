<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Login</title>
    <style>
        :root { 
            --primary: #4f46e5; 
            --primary-hover: #4338ca;
            --dark: #1e293b; 
            --bg: #f8fafc; 
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background: var(--bg); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            color: var(--text-main);
        }

        .login-box { 
            background: white; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); 
            width: 100%;
            max-width: 400px; 
            text-align: center;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        h2 { 
            margin: 0 0 24px 0; 
            font-size: 1.25rem; 
            font-weight: 600;
            color: var(--text-muted);
        }

        .error { 
            background: #fef2f2;
            color: #dc2626; 
            padding: 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            border: 1px solid #fee2e2;
            font-weight: 500;
        }

        /* Success Alert Styling */
        .success-alert {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
            font-weight: 600;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--dark);
        }

        input { 
            width: 100%; 
            padding: 12px 16px; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-size: 1rem;
            transition: all 0.2s;
            outline: none;
        }

        input:focus { 
            border-color: var(--primary); 
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        button { 
            width: 100%; 
            padding: 12px; 
            background: var(--primary); 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 1rem;
            font-weight: 700; 
            transition: background 0.2s, transform 0.1s;
            margin-top: 10px;
        }

        button:hover { 
            background: var(--primary-hover); 
        }

        button:active {
            transform: scale(0.98);
        }

        .footer-text {
            margin-top: 24px;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">Login.</div>
        <h2>Welcome back</h2>
        
        @if(session('success'))
            <div class="success-alert" id="success-box">
                ✅ {{ session('success') }}
            </div>

            <script>
                setTimeout(() => {
                    const box = document.getElementById('success-box');
                    if(box) {
                        box.style.transition = "opacity 0.5s ease";
                        box.style.opacity = "0";
                        setTimeout(() => box.remove(), 500);
                    }
                }, 4000);
            </script>
        @endif

        @if(session('error')) 
            <div class="error">
                ⚠️ {{ session('error') }}
            </div> 
        @endif
        
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit">Sign In</button>
        </form>

        <p class="footer-text">
            Don't have an account? <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Sign Up</a>
            <br><br>
            Protected by ShopAdmin Security
        </p>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | ShopAdmin</title>
    <style>
        :root { --primary: #4f46e5; --primary-hover: #4338ca; --dark: #1e293b; --bg: #f8fafc; --text-main: #0f172a; --text-muted: #64748b; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .logo { font-size: 1.75rem; font-weight: 800; color: var(--dark); margin-bottom: 8px; }
        h2 { margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: var(--text-muted); }
        .form-group { text-align: left; margin-bottom: 20px; position: relative; }
        label { display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px; }
        input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; transition: 0.2s; }
        input:focus { border-color: var(--primary); outline: none; ring: 2px var(--primary); }
        
        /* Toggle Button Styling */
        .toggle-password { 
            position: absolute; 
            right: 12px; 
            top: 38px; 
            background: none; 
            border: none; 
            color: var(--primary); 
            font-size: 0.75rem; 
            font-weight: 700; 
            cursor: pointer; 
            padding: 0;
        }

        button[type="submit"] { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; margin-top: 10px; transition: 0.2s; }
        button[type="submit"]:hover { background: var(--primary-hover); }
        
        .footer-text { margin-top: 24px; font-size: 0.875rem; color: var(--text-muted); }
        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .error-list { background: #fef2f2; border: 1px solid #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: left; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">Register.</div>
        <h2>Create an account</h2>

        @if ($errors->any())
            <div class="error-list">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Choose a username" required autofocus>
            </div>

            <div class="form-group">
                <label>Gmail Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
@error('email')
    <span style="color: red;">{{ $message }}</span>
@enderror
            </div>

            <div class="form-group">
                <label>Password (Min. 6 chars)</label>
                <input type="password" name="password" id="password" placeholder="••••••••" minlength="6" required>
                <button type="button" class="toggle-password" onclick="togglePass('password', this)">SHOW</button>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" minlength="6" required>
                <button type="button" class="toggle-password" onclick="togglePass('password_confirmation', this)">SHOW</button>
            </div>

            <button type="submit">Sign Up</button>
        </form>
        <p class="footer-text">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
    </div>

    <script>
        function togglePass(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                btn.textContent = "HIDE";
            } else {
                input.type = "password";
                btn.textContent = "SHOW";
            }
        }
    </script>
</body>
</html>
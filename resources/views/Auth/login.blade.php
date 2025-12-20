<x-layouts.app title="Login | Counseling System">
    {{-- GOOGLE FONTS & ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fb 0%, #e4edff 100%);
            font-family: 'Inter', sans-serif !important;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            transition: transform 0.3s ease;
        }

        .login-header h3 {
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #4a5568;
            margin-left: 4px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-custom .material-symbols-rounded {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 20px;
            z-index: 10;
        }

        .form-control {
            height: 54px;
            border-radius: 14px;
            padding-left: 48px;
            border: 2px solid #edf2f7;
            background: #f8fafc;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            color: #2d3748;
        }

        .btn-login {
            height: 54px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1rem;
            background: #0d6efd;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(13, 110, 253, 0.3);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -3px rgba(13, 110, 253, 0.4);
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            font-size: 0.85rem;
            padding: 12px 16px;
        }
    </style>

    <div class="login-card">
        <div class="login-header text-center">
            <h3>Welcome Back</h3>
            <p>Please enter your details to sign in</p>
        </div>

        {{-- Error Alerts --}}
        @if(session('error') || $errors->any())
            <div class="alert alert-danger alert-custom mb-4">
                <div class="d-flex align-items-center">
                    <span class="material-symbols-rounded me-2" style="font-size: 18px;">error</span>
                    <div>
                        @if(session('error')) {{ session('error') }} @endif
                        @if($errors->any()) Invalid credentials provided. @endif
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group-custom">
                    <span class="material-symbols-rounded">person</span>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username"
                        value="{{ old('username') }}"
                        required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group-custom">
                    <span class="material-symbols-rounded">lock</span>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-login d-flex align-items-center justify-content-center">
                Sign In
                <span class="material-symbols-rounded ms-2" style="font-size: 20px;">login</span>
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-muted small mb-0">Counseling Management System v1.0</p>
        </div>
    </div>
</x-layouts.app>
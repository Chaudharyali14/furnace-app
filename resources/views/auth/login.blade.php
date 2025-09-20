<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .bg {
            background-image: url("{{ asset('3.svg') }}");
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            /* filter: blur(2px); */
            /* -webkit-filter: blur(2px); */
            transform: scale(1.1);
        }
        .login-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            width: 90%;
            max-width: 400px;
            padding: 20px;
            background: rgba(5, 1, 36, 0.205);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        .login-card-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-card-header h3 {
            margin: 0;
            font-weight: 600;
            color: #eee1e1;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0078d4;
        }
        .btn-primary {
            background-color: #0078d4;
            border: none;
            padding: 10px;
            width: 100%;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #005a9e;
        }
        .input-group-text {
            background-color: #f0f0f0;
            border: 1px solid #ced4da;
        }
        .alert {
            border-radius: 8px;
        }
        label{
            color:#eee1e1
        }
    
    </style>
</head>
<body>

    <div class="bg"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-card-header">
                <h3><i class="fas fa-user-shield"></i> Admin Login</h3>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required value="{{ old('username') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
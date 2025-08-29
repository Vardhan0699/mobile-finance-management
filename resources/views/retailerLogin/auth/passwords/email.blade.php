<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailer Forgot Password</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
           <!-- background: linear-gradient(to right, #667eea, #764ba2);
            font-family: 'Tahoma',Segoe UI , Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
          background-image: url('{{ asset('public/images/bg-image.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;  -->
          
          margin: 0;
            padding: 0;
            font-family: 'sans-serif', Segoe UI, Geneva, Tahoma, Verdana;
            background: #eef2f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            min-height: 100vh;
          background-image: url('{{ asset('public/images/bg-retailer.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #667eea;
            border: none;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #5a67d8;
        }

        .text-danger {
            font-size: 0.875rem;
        }

        .alert {
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card bg-white">
                    <div class="card-body">
                        <h4 class="text-center mb-4">Retailer Forgot Password</h4>

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('retailer.password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Send Password Reset Link</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('retailerLogin') }}" class="text-decoration-none">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

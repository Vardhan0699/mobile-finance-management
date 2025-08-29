<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailer Signup</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #eef2f3;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('{{ asset('public/images/bg-retailer.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .register-box {
            background: #fff;
            padding: 35px 25px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 380px;
            margin: 40px 15px;
        }

        .register-box h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .error ul {
            padding-left: 20px;
            margin: 0;
        }

        .link-back {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .link-back a {
            color: #0006ff;
            text-decoration: none !important;
        }

        .link-back a:hover {
            text-decoration: underline;
        }

        /* Responsive tweaks for smaller screens */
        @media (max-width: 400px) {
            .register-box {
                padding: 25px 20px;
                border-radius: 12px;
            }

            .register-box h2 {
                font-size: 20px;
            }

            input,
            select {
                font-size: 14px;
                padding: 10px;
            }

            button {
                font-size: 15px;
            }

            .link-back {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Retailer Signup</h2>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('retailer.register.submit') }}">
            @csrf

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>

            <button type="submit">Register</button>
        </form>

        <div class="link-back">
            Already have an account?
            <a href="{{ route('retailerLogin') }}">Login here</a>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailer Login</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #a5cbd4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
          background-image: url('{{ asset('public/images/bg-retailer.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            
        }

        .login-box {
            background: #fff;
            padding: 30px 25px;
            border-radius: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 340px;
            box-sizing: border-box;
        }

        .login-box h2 {
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

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .link-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .link-buttons a {
            font-size: 14px;
            color: #0006ff;
            text-decoration: none !important;
        }

        .link-buttons a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Responsive for very small screens */
        @media (max-width: 400px) {
            .login-box {
                padding: 25px 20px;
                border-radius: 20px;
            }

            .login-box h2 {
                font-size: 20px;
            }

            label {
                font-size: 13px;
            }

            input[type="email"],
            input[type="password"] {
                font-size: 14px;
                padding: 10px;
            }

            button {
                font-size: 15px;
            }

            .link-buttons {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .link-buttons a {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Retailer Login</h2>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('retailer.login') }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>

        <div class="link-buttons">
            <a href="{{ route('retailer.register') }}">Signup</a>
            <a href="{{ route('retailer.password.request') }}">Forgot Password?</a>
        </div>
    </div>

</body>
</html>

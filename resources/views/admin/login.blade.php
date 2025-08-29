<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #eef2f3;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-image: url('{{ asset('public/images/bg-admin.jpg') }}');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
      }

      .login-box {
        background: #fff;
        padding: 35px 30px;
        border-radius: 30px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
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
        box-sizing: border-box;
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

      .error {
        color: red;
        text-align: center;
        margin-bottom: 15px;
      }

      .forgot-password {
        height: 20px;
        width: 107px;
        display: block;
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
        color: #002fff;
        text-decoration: none !important;
      }

      .forgot-password:hover {
        text-decoration: underline;
      }

      /* Responsive Tweaks */
      @media (max-width: 480px) {
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
      }
    </style>
  </head>
  <body>
    <div class="login-box">
      <h2>Admin Login</h2>

      @if ($errors->any())
      <div class="error">
        {{ $errors->first() }}
      </div>
      @endif

      <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>

        <a class="forgot-password" href="{{ route('admin.password.request') }}">Forgot Password</a>
      </form>
    </div>
  </body>
</html>

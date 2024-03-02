<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification OTP</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0 20px 0 0;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 700px;
            width: 100%;
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
            line-height: 1.6;
        }

        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #4285f4;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 20px;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer img {
            margin-left: 10px;
            max-height: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Verification OTP</h1>
        <p>Your OTP for email verification is:</p>
        <p class="otp">{{ $otp }}</p>
        <p>If you did not request this OTP, no further action is required.</p>
        <p class="footer">
            Copyright © 2024
            <img src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('img/brenbala-logo.webp'))) }}" alt="Brenbala" />
            . All rights reserved.
        </p>
    </div>
</body>
</html>

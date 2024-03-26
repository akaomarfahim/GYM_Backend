<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM - Fitness Simplified</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ebff41;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
            text-align: center;
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: black;
        }
    </style>
</head>

<body>
    <div class="container">
        <img class="logo" src="{{ asset('img/logo.png') }}" alt="GYM Logo" height="150" width="150">
        <h1 class="heading">Welcome to GYM</h1>
        <p class="subheading">Fitness Simplified</p>
        <a class="btn btn-dark" href="#">Login</a>
        <div class="footer">
            &copy; 2024 GYM. All rights reserved. | Designed by <a href="https://yourwebsite.com"><img src="https://www.tmt-soft.com/user/assets/img/logo.png" height="80" width="150" /></a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

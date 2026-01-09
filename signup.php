<?php
session_start();



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup / Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        form {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            width: 300px;
            border-radius: 6px;
        }
        input, button {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- SIGNUP FORM -->
    <form method="POST" action="User.php">
        <h2>Sign Up</h2>

        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name = "signup">Sign Up</button>
    </form>

    <!-- LOGIN FORM -->
    <form method="POST" action="User.php">
        <h2>Login</h2>

        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name  = "login">Login</button>
    </form>

</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Register</h1>

    <form method="POST" action="<?= BASE_URL ?>register">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Daftar</button>
    </form>

    <a href="<?= BASE_URL ?>login">Login</a>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Profile</h1>

    <a href="<?= BASE_URL ?>home">Kembali</a>

    <form method="POST" action="<?= BASE_URL ?>profile/update">
        <textarea name="bio" placeholder="Tulis bio..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        <button>Update Bio</button>
    </form>

</body>

</html>
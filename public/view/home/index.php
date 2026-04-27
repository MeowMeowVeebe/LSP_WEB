<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Home</h1>

    <?php if (AuthController::check_auth()): ?>

        <span>Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>

        <a href="<?= BASE_URL ?>profile">Profile</a>
        <a href="<?= BASE_URL ?>logout">Logout</a>

    <?php else: ?>

        <a href="<?= BASE_URL ?>login">Login</a>
        <a href="<?= BASE_URL ?>register">Register</a>

    <?php endif; ?>

    <form method="GET" action="<?= BASE_URL ?>home">
        <input type="text" name="hashtag" placeholder="#php">
        <button>Cari</button>
    </form>

    <form method="POST" action="<?= BASE_URL ?>posts/store">
        <textarea name="text" maxlength="250" placeholder="Tulis postingan #php" required></textarea>
        <button>Posting</button>
    </form>

    <hr>

    <?php while ($post = mysqli_fetch_assoc($posts)): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <b><?= htmlspecialchars($post['username']) ?></b>
            <p><?= htmlspecialchars($post['text']) ?></p>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                <form method="POST" action="<?= BASE_URL ?>posts/delete">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <button>Hapus Post</button>
                </form>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>comments/store">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <input type="text" name="text" maxlength="250" placeholder="Komentar..." required>
                <button>Komentar</button>
            </form>

            <?php
            $post_id = $post['id'];

            $comments = mysqli_query($conn, "
            SELECT comments.*, users.username
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE comments.post_id = '$post_id'
            ORDER BY comments.created_at DESC
        ");
            ?>

            <?php while ($comment = mysqli_fetch_assoc($comments)): ?>
                <p>
                    <b><?= htmlspecialchars($comment['username']) ?>:</b>
                    <?= htmlspecialchars($comment['text']) ?>
                </p>
            <?php endwhile; ?>
        </div>
    <?php endwhile; ?>
</body>

</html>
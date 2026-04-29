<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $form_title ?></title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>dist/css/output.css">
</head>

<body>
    <?php include __DIR__ . "/../layouts/header/index.php"; ?>

    <div class="mx-auto min-h-screen max-w-4xl p-4">
        <section class="mx-auto flex max-w-4xl flex-col gap-6 p-4">
            <div class="bg-white p-6 shadow-sm">
                <div class="flex flex-col items-center text-center">
                    <?php if (!empty($user['photo'])) : ?>
                        <img
                            src="data:<?= $user['photo_type'] ?>;base64,<?= base64_encode($user['photo']) ?>"
                            class="mb-4 h-32 w-32 rounded-full object-cover"
                            alt="Profile picture">
                    <?php else : ?>
                        <img
                            class="mb-4 h-32 w-32 rounded-full border bg-white object-cover"
                            src="<?= ASSET_URL ?>images/default-pfp.png"
                            alt="Profile picture">
                    <?php endif; ?>

                    <p class="text-sm font-bold text-slate-500">NAMA</p>
                    <h2 class="text-2xl font-bold"><?= $user['username'] ?></h2>

                    <p class="mt-6 text-sm font-bold text-slate-500">BIO</p>
                    <p class="mt-2 max-w-xl text-center text-slate-600">
                        <?= $user['bio'] ?: 'Belum ada bio.' ?>
                    </p>
                </div>
            </div>

            <h3 class="text-center text-xl font-bold">Postingan User</h3>
            <div class="flex flex-col gap-4">
                <?php $has_user_posts = false; ?>
                <?php while ($post = mysqli_fetch_assoc($user_posts)) : ?>
                    <?php $has_user_posts = true; ?>
                    <article class="bg-white p-4 shadow-sm">
                        <div class="mb-3 flex items-center gap-3">
                            <?php if (!empty($post['photo'])): ?>
                                <img
                                    class="h-10 w-10 rounded-full border object-cover bg-white"
                                    src="data:<?= $post['photo_type'] ?>;base64,<?= base64_encode($post['photo']) ?>"
                                    alt="Profile picture">
                            <?php else: ?>
                                <img
                                    class="h-10 w-10 rounded-full border bg-white object-cover"
                                    src="<?= ASSET_URL ?>images/default-pfp.png"
                                    alt="Profile picture">
                            <?php endif; ?>
                            <div>
                                <p class="font-semibold"><?= $post['username'] ?></p>
                                <p class="text-xs text-slate-500"><?= date('d/m/Y', strtotime($post['created_at'])) ?></p>
                            </div>
                        </div>

                        <?php if (!empty($post['image'])): ?>
                            <img
                                class="mt-3 max-h-80 w-full rounded-md border object-contain bg-white"
                                src="data:<?= $post['image_type'] ?>;base64,<?= base64_encode($post['image']) ?>"
                                alt="Gambar postingan">
                        <?php endif; ?>

                        <div class="rounded-md bg-slate-50 p-3">
                            <p class="text-sm leading-6 text-slate-700"><?= $post['text'] ?></p>
                        </div>
                    </article>
                <?php endwhile; ?>

                <?php if (!$has_user_posts) : ?>
                    <div class="rounded-lg border bg-white p-6 shadow-sm">
                        <p class="text-center text-sm text-slate-600">Belum ada postingan dari akun ini.</p>
                    </div>
                <?php endif; ?>
            </div>

            <h3 class="text-center text-xl font-bold">Komentar User</h3>
            <div class="flex flex-col gap-4">
                <?php $has_user_comments = false; ?>
                <?php while ($comment = mysqli_fetch_assoc($user_comments)) : ?>
                    <?php $has_user_comments = true; ?>
                    <article class="bg-white p-4 shadow-sm">
                        <div class="mb-3 flex items-center gap-3">
                            <?php if (!empty($comment['photo'])): ?>
                                <img
                                    class="h-10 w-10 rounded-full border object-cover bg-white"
                                    src="data:<?= $comment['photo_type'] ?>;base64,<?= base64_encode($comment['photo']) ?>"
                                    alt="Profile picture">
                            <?php else: ?>
                                <img
                                    class="h-10 w-10 rounded-full border bg-white object-cover"
                                    src="<?= ASSET_URL ?>images/default-pfp.png"
                                    alt="Profile picture">
                            <?php endif; ?>
                            <div>
                                <p class="font-semibold"><?= $comment['username'] ?></p>
                                <p class="text-xs text-slate-500"><?= date('d/m/Y', strtotime($comment['created_at'])) ?></p>
                            </div>
                        </div>

                        <div class="rounded-md bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">Komentar pada post:</p>
                            <p class="mb-2 text-sm text-slate-600"><?= $comment['post_text'] ?></p>
                            <p class="text-sm leading-6 text-slate-700"><?= $comment['text'] ?></p>
                        </div>

                        <?php if (!empty($comment['image'])): ?>
                            <img
                                class="mt-3 max-h-64 w-full rounded-md border object-contain bg-white"
                                src="data:<?= $comment['image_type'] ?>;base64,<?= base64_encode($comment['image']) ?>"
                                alt="Gambar komentar">
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>

                <?php if (!$has_user_comments) : ?>
                    <div class="rounded-lg border bg-white p-6 shadow-sm">
                        <p class="text-center text-sm text-slate-600">Belum ada komentar dari user ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>

</html>

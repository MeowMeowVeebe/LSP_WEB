<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $form_title ?></title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>dist/css/output.css">
</head>

<body>
    <?php
    $is_auth = AuthController::check_auth();
    $post_error = $_SESSION['post_error'] ?? null;
    unset($_SESSION['post_error']);
    ?>

    <?php include __DIR__ . "/../layouts/header/index.php"; ?>

    <div class="mx-auto min-h-screen max-w-4xl p-4">
        <?php if ($post_error): ?>
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                <?= $post_error ?>
            </div>
        <?php endif; ?>

        <section class="mx-auto max-w-3xl  bg-white p-6">
            <div class="mb-4 flex items-start gap-3">
             <?php if (!empty($current_user['photo'])): ?>
                    <img
                        class="h-14 w-10 rounded-full border bg-white object-cover"
                        src="data:<?= $current_user['photo_type'] ?>;base64,<?= base64_encode($current_user['photo']) ?>"
                        alt="Profile picture"
                    >
                <?php else: ?>
                   
                          <img
                        class="h-10 w-10 rounded-full border bg-white object-cover"
                        src="<?= ASSET_URL ?>images/default-pfp.png"
                        alt="Profile picture"
                    >
                
                <?php endif; ?>

                <div>
                    <p class="text-xl"><?= $_SESSION['username'] ?></p>
                    <p class="text-3xl leading-tight">Apa yang kamu pikirkan?</p>
                </div>
            </div>

            <form method="POST" action="<?= $form_action ?>" enctype="multipart/form-data" class="space-y-6">
                <?php if ($form_mode === 'edit'): ?>
                    <input type="hidden" name="post_id" value="<?= (int) $post_data['id'] ?>">
                <?php endif; ?>
                <div class="w-full rounded-md border border-slate-300 bg-white p-6">
                    <textarea
                        class="min-h-56 w-full resize-none bg-transparent text-center text-1xl outline-none placeholder:text-slate-700"
                        name="text"
                        maxlength="250"
                        placeholder="Apa yang kamu pikirkan?"><?= $post_data['text'] ?? '' ?></textarea>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700" for="post-image">Gambar</label>
                        <input class="block w-full text-sm text-slate-600 file:mr-3 file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium" id="post-image" type="file" name="image" accept="image/*">
                        <?php if ($form_mode === 'edit' && !empty($post_data['image'])): ?>
                            <img
                                class="mt-3 max-h-40 w-full rounded-md border object-contain bg-white"
                                src="data:<?= $post_data['image_type'] ?>;base64,<?= base64_encode($post_data['image']) ?>"
                                alt="Current post image">
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700" for="post-file">File</label>
                        <input class="block w-full text-sm text-slate-600 file:mr-3 file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium" id="post-file" type="file" name="file">
                        <?php if ($form_mode === 'edit' && !empty($post_data['file_name'])): ?>
                            <p class="mt-3 text-sm text-slate-600">File saat ini: <?= $post_data['file_name'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button class="min-w-52 rounded-md border border-slate-300 bg-white p-6 px-8 py-3 text-3xl text-slate-900" type="submit"><?= $form_button ?></button>
                </div>
            </form>
        </section>
    </div>
</body>

</html>

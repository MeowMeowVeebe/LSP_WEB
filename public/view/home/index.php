<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>dist/css/output.css">
</head>

<body>
    <?php
    $is_auth = AuthController::check_auth();    //// Memangill function dari AuthController
    $home_error = $_SESSION['home_error'] ?? null;   ///////
    $home_success = $_SESSION['home_success'] ?? null;   //////  <----- apakah $_session[''] kalau gak menjadil null
    $editing_comment_id = (int) ($_GET['edit_comment'] ?? 0);
    unset($_SESSION['home_error'], $_SESSION['home_success']); ////// Menghapus kan memori $_session biar bisa dipake lagi
    ?>


    <?php include __DIR__ . "/../layouts/header/index.php"; ?>   <!----  referensi header dari folder layouts  !--->   
    
    <div class="mx-auto min-h-screen p-4 max-w-4xl ">
  
        <?php if ($home_error): ?>
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                <?= $home_error ?>
            </div>
        <?php endif; ?>

        <?php if ($home_success): ?>
            <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                <?= $home_success ?>
            </div>
        <?php endif; ?>

        <section class="mb-6">
            <form method="GET" action="<?= BASE_URL ?>home" class=" bg-white p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-700">Cari Postingan</h2>
                    <?php if (!empty($hashtag)): ?>
                        <a href="<?= BASE_URL ?>home" class="text-sm text-slate-600 hover:text-slate-900">Reset</a>
                    <?php endif; ?>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <input
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500"
                        type="text"
                        name="hashtag"
                        placeholder="Cari hashtag, contoh #php"
                        value="<?= $hashtag ?>">
                    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white sm:min-w-28" type="submit">Cari</button>
                </div>
            </form>
        </section>

        <section>
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-4 ">
                    <?php if ($is_auth): ?>     <!----  Apakah $is_auth itu true apa bukan kalau iya maka elemen html yang dibawah ditampilin---->
                        <a href="<?= BASE_URL ?>post" class="rounded-md bg-slate-200 px-6 py-2 text-sm font-medium text-slate-900">Buat</a>
                    <?php endif; ?>
                    
                </div>

                <div> 
                    <h2 class="text-center text-xl font-bold relative right-[220%]">Postingan Terbaru</h2>
                    </div>
            </div>

            <div class="space-y-4">
                <?php $has_posts = false; ?> <!---    !--->
                <?php while ($post = mysqli_fetch_assoc($posts)): ?> <!------ mengambil data column dari $post  !-->
                    <?php $has_posts = true; ?>   <!------ mengdeklrasikan variabel $has_posts menjadi true !---->
                    <?php
                    preg_match_all('/#([\p{L}\p{N}_]+)/u', $post['text'] ?? '', $hashtag_matches);  /// Mengecek apakah di text itu ada hashtagnya 
                    $post_hashtags = array_values(array_unique($hashtag_matches[0] ?? [])); //// mengambil daftar hashtag unik dari pencarian  hasil regex
                    ?>
                    <article class=" bg-white p-4">
                        <div class="mb-4 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <?php if (!empty($post['photo'])): ?>
                                    <a href="<?= BASE_URL ?>info_user?id=<?= $post['user_id'] ?>">
                                        <img
                                            class="h-12 w-12 rounded-full border object-cover bg-white"
                                            src="data:<?= $post['photo_type'] ?>;base64,<?= base64_encode($post['photo']) ?>"
                                            alt="Profile picture">
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>info_user?id=<?= $post['user_id'] ?>" class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-200 font-semibold">
                                        <img
                                            class="h-10 w-10 rounded-full border bg-white object-cover"
                                            src="<?= ASSET_URL ?>images/default-pfp.png"
                                            alt="Profile picture"
                                        >
                                    </a>
                                <?php endif; ?>
                                <div>
                                    <p class="font-semibold"><?= $post['username'] ?></p>
                                    <?php if (!empty($post_hashtags)): ?>
                                        <div class="mt-1 flex flex-wrap gap-2">
                                          
                                        </div>
                                    <?php endif; ?>

                                    
                                    <p class="text-xs text-slate-500"><?= date('d/m/Y', strtotime($post['created_at'])) ?></p>

                                      <?php foreach ($post_hashtags as $tag): ?>
                                                <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-600">
                                                    <?= $tag ?>
                                                </span>
                                            <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if ($is_auth && $_SESSION['user_id'] == $post['user_id']): ?>
                                <div class="flex items-center gap-3">
                                    <a class="text-sm text-slate-700 hover:text-slate-900" href="<?= BASE_URL ?>post/edit?id=<?= $post['id'] ?>"> <img class="object-fit-cover w-8 h-8" src="<?= ASSET_URL ?>images/edit.png" > </a>
                                    <form method="POST" action="<?= BASE_URL ?>posts/delete">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <button class="text-sm text-red-600 hover:text-red-700" type="submit"><img class="object-fit-cover w-10 h-10" src="<?= ASSET_URL ?>images/delete.png" ></button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="rounded-md bg-slate-50 p-3">
                            <?php if ($post['text'] !== ''): ?>
                                <p class="whitespace-pre-line text-sm leading-6 text-slate-800"><?= $post['text'] ?></p>
                            <?php endif; ?>

                            <?php if (!empty($post['image'])): ?>
                                <img
                                    class="mt-3 max-h-80 w-full object-contain bg-white"
                                    src="data:<?= $post['image_type'] ?>;base64,<?= base64_encode($post['image']) ?>"
                                    alt="Gambar postingan">
                            <?php endif; ?>

                            <?php if (!empty($post['file_name'])): ?>
                                <a
                                    class="mt-3 inline-block rounded-md border bg-white px-3 py-2 text-xs text-slate-700 hover:bg-slate-100"
                                    href="<?= BASE_URL ?>posts/file?id=<?= $post['id'] ?>">
                                    Download File: <?= $post['file_name'] ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php
                        $post_id = $post['id'];
                        $comments = mysqli_query($conn, "
                            SELECT comments.*, users.username, users.photo, users.photo_type
                            FROM comments
                            JOIN users ON comments.user_id = users.id
                            WHERE comments.post_id = '$post_id'
                            ORDER BY comments.created_at DESC
                        ");
                        ?>

                        <div class="mt-4 space-y-2">
                            <?php while ($comment = mysqli_fetch_assoc($comments)): ?>
                                <div class=" bg-slate-50 px-3 py-2"> <!--- Comments -->
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <?php if (!empty($comment['photo'])): ?>
                                                <a href="<?= BASE_URL ?>info_user?id=<?= $comment['user_id'] ?>">
                                                    <img
                                                        class="h-8 w-8 rounded-full border object-cover bg-white"
                                                        src="data:<?= $comment['photo_type'] ?>;base64,<?= base64_encode($comment['photo']) ?>"
                                                        alt="Profile picture">
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>info_user?id=<?= $comment['user_id'] ?>" class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold">
                                                    <img
                                                        class="h-10 w-10 rounded-full border bg-white object-cover"
                                                        src="<?= ASSET_URL ?>images/default-pfp.png"
                                                        alt="Profile picture"
                                                    >
                                                </a>
                                            <?php endif; ?>
                                            <span class="text-sm font-semibold"><?= $comment['username'] ?></span>
                                            <span class="text-xs text-slate-500"><?= date('d/m/Y', strtotime($comment['created_at'])) ?></span>
                                        </div>

                                        <?php if ($is_auth && $_SESSION['user_id'] == $comment['user_id']): ?>
                                            <div class="flex items-center gap-3">
                                                <a class="text-xs text-slate-700 hover:text-slate-900" href="<?= BASE_URL ?>home?edit_comment=<?= $comment['id'] ?>"><img class="object-fit-cover w-8 h-8" src="<?= ASSET_URL ?>images/edit.png" ></a>
                                                <form method="POST" action="<?= BASE_URL ?>comments/delete">
                                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                    <button class="text-xs text-red-600 hover:text-red-700" type="submit"><img class="object-fit-cover w-8 h-8" src="<?= ASSET_URL ?>images/delete.png" ></button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($editing_comment_id === (int) $comment['id'] && $is_auth && $_SESSION['user_id'] == $comment['user_id']): ?>
                                        <form method="POST" action="<?= BASE_URL ?>comments/update" enctype="multipart/form-data" class="mt-2 space-y-3">
                                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                            <input
                                                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500"
                                                type="text"
                                                name="text"
                                                maxlength="250"
                                                value="<?= $comment['text'] ?>"
                                                placeholder="Edit komentar...">
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-slate-700" for="edit-comment-image-<?= $comment['id'] ?>">Gambar</label>
                                                    <input class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-200 file:px-3 file:py-2 file:text-xs file:font-medium" id="edit-comment-image-<?= $comment['id'] ?>" type="file" name="image" accept="image/*">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-slate-700" for="edit-comment-file-<?= $comment['id'] ?>">File</label>
                                                    <input class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-200 file:px-3 file:py-2 file:text-xs file:font-medium" id="edit-comment-file-<?= $comment['id'] ?>" type="file" name="file">
                                                </div>
                                            </div>
                                            <?php if (!empty($comment['image'])): ?>
                                                <img
                                                    class="max-h-40 w-full rounded-md border object-contain bg-white"
                                                    src="data:<?= $comment['image_type'] ?>;base64,<?= base64_encode($comment['image']) ?>"
                                                    alt="Gambar komentar">
                                            <?php endif; ?>
                                            <?php if (!empty($comment['file_name'])): ?>
                                                <p class="text-xs text-slate-600">File saat ini: <?= $comment['file_name'] ?></p>
                                            <?php endif; ?>
                                            <div class="flex items-center justify-end gap-3">
                                                <a class="text-xs text-slate-600 hover:text-slate-900" href="<?= BASE_URL ?>home">Batal</a>
                                                <button class="rounded-md border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700" type="submit">Update</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <?php if ($comment['text'] !== ''): ?>
                                            <p class="text-sm text-slate-700"><?= $comment['text'] ?></p>
                                        <?php endif; ?>

                                        <?php if (!empty($comment['image'])): ?>
                                            <img
                                                class="mt-2 max-h-64 w-full rounded-md object-contain bg-white"
                                                src="data:<?= $comment['image_type'] ?>;base64,<?= base64_encode($comment['image']) ?>"
                                                alt="Gambar komentar">
                                        <?php endif; ?>

                                        <?php if (!empty($comment['file_name'])): ?>
                                            <a
                                                class="mt-2 inline-block rounded-md border bg-white px-3 py-2 text-xs text-slate-700 hover:bg-slate-100"
                                                href="<?= BASE_URL ?>comments/file?id=<?= $comment['id'] ?>">
                                                Download File: <?= $comment['file_name'] ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <?php if ($is_auth): ?>
                            <form method="POST" action="<?= BASE_URL ?>comments/store" enctype="multipart/form-data" class="mt-4 space-y-3">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input
                                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500"
                                    type="text"
                                    name="text"
                                    maxlength="250"F
                                    placeholder="Tulis komentar atau upload gambar/file...">
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-700" for="comment-image-<?= $post['id'] ?>">Gambar</label>
                                        <input class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-200 file:px-3 file:py-2 file:text-xs file:font-medium" id="comment-image-<?= $post['id'] ?>" type="file" name="image" accept="image/*">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-700" for="comment-file-<?= $post['id'] ?>">File</label>
                                        <input class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-200 file:px-3 file:py-2 file:text-xs file:font-medium" id="comment-file-<?= $post['id'] ?>" type="file" name="file">
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 sm:min-w-28" type="submit">Komentar</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="mt-4 rounded-md border border-dashed px-3 py-2 text-sm text-slate-600">
                                Ingin ikut berkomentar? <a class="font-semibold text-slate-900" href="<?= BASE_URL ?>login">Login di sini</a>.
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>

                <?php if (!$has_posts): ?> <!-----  Apakah !has_post itu false kalau iya maka akan tampilin dibawah ini!--->
                    <div class="rounded-lg border bg-white p-6 text-center">
                        <h3 class="text-lg font-semibold">Belum ada postingan</h3>
                        <p class="mt-2 text-sm text-slate-600">
                            <?= !empty($hashtag) ? 'Coba gunakan hashtag lain atau reset pencarian.' : 'Postingan baru akan tampil di sini.' ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
                </div>
        </section>
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>dist/css/output.css">
</head>

 <?php
    $is_auth = AuthController::check_auth();
    $home_error = $_SESSION['home_error'] ?? null;
    $home_success = $_SESSION['home_success'] ?? null;
    $editing_comment_id = (int) ($_GET['edit_comment'] ?? 0);
    unset($_SESSION['home_error'], $_SESSION['home_success']);
    ?>

<body>
    <?php $current_user = $user; ?>
    <?php
    $profile_error = $_SESSION['profile_error'] ?? null;
    $profile_success = $_SESSION['profile_success'] ?? null;
    unset($_SESSION['profile_error'], $_SESSION['profile_success']);
    ?>

        <?php include __DIR__ . "/../layouts/header/index.php"; ?>        


    <div class="mx-auto min-h-screen max-w-4xl p-4">


        <section class="mx-auto flex max-w-4xl flex-col gap-6 p-4">

    <?php if ($profile_error): ?>
        <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
            <?= $profile_error ?>
        </div>
    <?php endif; ?>

    <?php if ($profile_success): ?>
        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
            <?= $profile_success ?>
        </div>
    <?php endif; ?>

    <div class=" bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-center">Edit Profile</h3>
        <div class="flex flex-col items-center text-center">
        <form method="POST" class="flex flex-col items-center text-center" action="<?= BASE_URL ?>profile/update" enctype="multipart/form-data" class="space-y-4">
            <?php if (!empty($user['photo'])) : ?>
                <img 
                    src="data:<?= $user['photo_type'] ?>;base64,<?= base64_encode($user['photo']) ?>"
                    class="mb-4 h-34 w-34 rounded-full object-cover"
                >
            <?php else : ?>
              
                       <img
                        class="mb-4 h-30 w-30 rounded-full border bg-white object-cover"
                        src="<?= ASSET_URL ?>images/default-pfp.png"
                        alt="Profile picture"
                    >
   
            <?php endif; ?>

                 <input 
                type="file"
                name="photo"
                accept="image/*"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
            >


            

            <p class="text-sm font-bold text-slate-500">NAMA</p>
            <h2 class="text-2xl font-bold">
                <?= $user['username'] ?>
            </h2>

                   <input 
                type="text"
                name="username"
                value="<?= $user['username'] ?>"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500"
                placeholder="Username"
            >

            <p class="mt-6 text-sm font-bold text-slate-500">PASSWORD</p>
            <input 
                type="password"
                name="password"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500"
                placeholder="Kosongkan jika tidak ingin ganti password"
            >


            <p class="mt-6 text-sm font-bold text-slate-500">BIO</p>
            <p class="mt-2 max-w-xl text-center text-slate-600">
                <?= $user['bio'] ?: 'Belum ada bio. Tambahkan sedikit cerita tentang akun ini.' ?>
            </p>

                       <textarea
                class="min-h-32 w-full resize-none rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500"
                name="bio"
                placeholder="Tulis bio..."
            ><?= $user['bio'] ?? '' ?></textarea>
        </div>

        <hr class="my-6">

    

       
     
       
 

            <button 
                class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                type="submit"
            >
                Update Profile
            </button>
        </form>
    </div>

    <h3 class="text-center text-xl font-bold">Postingan Kamu</h3>

    <div class="flex flex-col gap-4">
        <?php $has_user_posts = false; ?>

        <?php while ($post = mysqli_fetch_assoc($user_posts)) : ?>
            <?php $has_user_posts = true; ?>

            

            <article class=" bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center gap-3">
                    <img
                        class="h-10 w-10 rounded-full border bg-white object-cover"
                        src="<?= ASSET_URL ?>images/default-pfp.png"
                        alt="Profile picture"
                    >

                    <div>
                        <p class="font-semibold">
                            <?= $post['username'] ?>
                        </p>
                        <p class="text-xs text-slate-500">
                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </p>
                    </div>
                </div>

                  <?php if (!empty($post['image'])): ?>
                                <img
                                    class="mt-3 max-h-80 w-full rounded-md border object-contain bg-white"
                                    src="data:<?= $post['image_type'] ?>;base64,<?= base64_encode($post['image']) ?>"
                                    alt="Gambar postingan">
                            <?php endif; ?>

                <div class="rounded-md bg-slate-50 p-3">
                    <p class="text-sm leading-6 text-slate-700">
                        <?= $post['text'] ?>
                    </p>
                </div>
            </article>
        <?php endwhile; ?>

        <?php if (!$has_user_posts) : ?>
            <div class="rounded-lg border bg-white p-6 shadow-sm">
                <p class="text-center text-sm text-slate-600">
                    Belum ada postingan dari akun ini.
                </p>
            </div>
        <?php endif; ?>
    </div>

</section>


    </div>
</body>

</html>

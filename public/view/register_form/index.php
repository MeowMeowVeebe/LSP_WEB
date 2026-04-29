<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>dist/css/output.css">
</head>

<body>
    <?php
    $auth = AuthController::auth('register');
    $auth_error = $auth['auth_error'];
    $auth_success = $auth['auth_success'];
    $old_username = $auth['old_username'];
    ?>


    <?php include __DIR__ . "/../layouts/header/index.php"; ?>


    <div class="mx-auto min-h-screen max-w-4xl p-4">
        <section class="flex min-h-[70vh] items-center justify-center">
            <div class="w-full max-w-md rounded-lg bg-white p-6">
                <div class="mb-6 text-center w-full flex-column justify-center">
                    <div class="flex items-center justify-center">
                        <a href="<?= BASE_URL ?>home">
                            <img 
                                src="<?= ASSET_URL ?>images/logo-lsp.png" 
                                alt="Logo LSP"
                                class="h-24 w-24 object-contain"
                            >
                        </a>
                    </div>

                    <h2 class="text-2xl font-bold">Register</h2>
                    <p class="mt-2 text-sm text-slate-600">Buat akun baru untuk mulai posting.</p>
                </div>

                <?php if ($auth_error): ?>
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                        <?= $auth_error ?>
                    </div>
                <?php endif; ?>

                <?php if ($auth_success): ?>
                    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                        <?= $auth_success ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>register" class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="username">Username</label>
                        <input class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500" id="username" type="text" name="username" placeholder="Buat username" value="<?= $old_username ?>" required>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="password">Password</label>
                        <input class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500" id="password" type="password" name="password" placeholder="Buat password" required>
                    </div>

                    <button class="mt-2 w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Daftar</button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-600">
                    Sudah punya akun? <a class="font-semibold text-slate-900" href="<?= BASE_URL ?>login">Login di sini</a>
                </p>
            </div>
        </section>
    </div>
</body>

</html>

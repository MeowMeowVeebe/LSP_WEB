<link rel="stylesheet" href="<?= ASSET_URL ?>dist/css/output.css">
    <?php
    $is_auth = AuthController::check_auth();
    $home_error = $_SESSION['home_error'] ?? null;
    $home_success = $_SESSION['home_success'] ?? null;
    $editing_comment_id = (int) ($_GET['edit_comment'] ?? 0);
    unset($_SESSION['home_error'], $_SESSION['home_success']);
    ?>

    <header class="mb-6 flex w-full items-center justify-between bg-white p-4 sticky">
        <div class="flex items-center  ml-10">
            <a href="<?= BASE_URL ?>home">
            <img 
                src="<?= ASSET_URL ?>images/logo-lsp.png" 
                alt="Logo LSP"
                class="h-20 w-20 object-contain"
            >
                
        </div>

        <?php if ($is_auth): ?>
            <div class="flex items-center gap-3">
                <?php if (!empty($current_user['photo'])): ?>
                    <img
                        class="h-10 w-10 rounded-full border bg-white object-cover"
                        src="data:<?= $current_user['photo_type'] ?>;base64,<?= base64_encode($current_user['photo']) ?>"
                        alt="Profile picture"
                    >
                <?php else: ?>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 font-semibold">
                          <img
                        class="h-10 w-10 rounded-full border bg-white object-cover"
                        src="<?= ASSET_URL ?>images/default-pfp.png"
                        alt="Profile picture"
                    >
                    </div>
                <?php endif; ?>

                <div>
                    <p class="font-semibold text-slate-900">
                        Hello, <?= $_SESSION['username'] ?>
                    </p>

                    <div class="flex gap-2 text-xs text-slate-600">
                        <a class="hover:text-slate-900" href="<?= BASE_URL ?>profile">Profile</a>
                        <span>|</span>
                        <a class="hover:text-slate-900" href="<?= BASE_URL ?>logout">Logout</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="flex gap-4 text-sm">
                <a class="text-slate-700 hover:text-slate-900" href="<?= BASE_URL ?>login">Login</a>
                <a class="text-slate-700 hover:text-slate-900" href="<?= BASE_URL ?>register">Register</a>
            </div>
        <?php endif; ?>
    </header>

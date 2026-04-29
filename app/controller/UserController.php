<?php

class UserController
{
    // Menyimpan pesan sementara ke session agar bisa ditampilkan setelah redirect.
    private static function setFlash(string $key, string $message): void
    {
        $_SESSION[$key] = $message;
    }

    // Validasi upload foto profil. Jika user tidak upload foto, return null.
    private static function getPhotoUpload(string $field_name): ?array
    {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$field_name];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload foto profil gagal diproses.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Ukuran foto profil maksimal 5MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']) ?: '';
        finfo_close($finfo);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime_type, $allowed_types, true)) {
            throw new RuntimeException('Format foto profil harus JPG, PNG, GIF, atau WEBP.');
        }

        return [
            'data' => file_get_contents($file['tmp_name']),
            'type' => $mime_type,
        ];
    }

    // Menampilkan halaman info user berdasarkan id pada query string.
    public static function info_user()
    {
        global $conn;

        // Cek apakah ada user yang sedang login untuk kebutuhan tampilan header/info tambahan.
        $is_auth = AuthController::check_auth();
        $current_user = null;

        if ($is_auth) {
            $current_user_id = (int) $_SESSION['user_id'];
            $current_user_result = mysqli_query($conn, "
                SELECT id, username, photo, photo_type
                FROM users
                WHERE id = '$current_user_id'
                LIMIT 1
            ");
            $current_user = mysqli_fetch_assoc($current_user_result);
        }

        // Ambil id user yang ingin dilihat, contoh: /info_user?id=3
        $user_id = (int) ($_GET['id'] ?? 0);

        if ($user_id <= 0) {
            header("Location: " . BASE_URL . "home");
            exit;
        }

        // Ambil data user target.
        $user_result = mysqli_query($conn, "
            SELECT *
            FROM users
            WHERE id = '$user_id'
            LIMIT 1
        ");
        $user = mysqli_fetch_assoc($user_result);

        if (!$user) {
            header("Location: " . BASE_URL . "home");
            exit;
        }

        // Ambil semua post milik user target untuk ditampilkan pada halaman info user.
        $user_posts = mysqli_query($conn, "
            SELECT posts.*, users.username, users.photo, users.photo_type
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.user_id = '$user_id'
            ORDER BY posts.created_at DESC
        ");

        // Ambil semua komentar yang pernah dibuat user target.
        $user_comments = mysqli_query($conn, "
            SELECT comments.*, posts.text AS post_text, users.username, users.photo, users.photo_type
            FROM comments
            JOIN users ON comments.user_id = users.id
            JOIN posts ON comments.post_id = posts.id
            WHERE comments.user_id = '$user_id'
            ORDER BY comments.created_at DESC
        ");

        $form_title = 'Info User';

        // Variabel seperti $user, $user_posts, dan $user_comments akan dipakai langsung di view.
        require ROOT_DIR . "/public/view/info_user/index.php";
    }

    // Menampilkan halaman profile milik user yang sedang login.
    public static function profile()
    {
        if (!AuthController::check_auth()) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $user_id = $_SESSION['user_id'];

        // Ambil data lengkap user yang sedang login.
        $result = mysqli_query($conn, "
            SELECT * FROM users WHERE id = '$user_id'
        ");

        $user = mysqli_fetch_assoc($result);

        // Ambil beberapa post terbaru milik user untuk ringkasan profile.
        $user_posts = mysqli_query($conn, "
            SELECT posts.*, users.username
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.user_id = '$user_id'
            ORDER BY posts.created_at DESC
            LIMIT 3
        ");

        require ROOT_DIR . "/public/view/profile_form/index.php";
    }

    // Memproses form update profile.
    public static function updateProfile()
    {
        if (!AuthController::check_auth()) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $user_id = $_SESSION['user_id'];
        $username = trim($_POST['username'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $password = $_POST['password'] ?? '';

        // Ambil dan validasi foto profil baru jika user mengunggah file.
        try {
            $photo_upload = self::getPhotoUpload('photo');
        } catch (RuntimeException $exception) {
            self::setFlash('profile_error', $exception->getMessage());
            header("Location: " . BASE_URL . "profile");
            exit;
        }

        // Username wajib diisi.
        if ($username === '') {
            self::setFlash('profile_error', 'Username wajib diisi.');
            header("Location: " . BASE_URL . "profile");
            exit;
        }

        // Batasi panjang bio agar tidak terlalu panjang.
        if (strlen($bio) > 1000) {
            self::setFlash('profile_error', 'Bio maksimal 1000 karakter.');
            header("Location: " . BASE_URL . "profile");
            exit;
        }

        // Cek apakah username baru sudah dipakai user lain.
        $escaped_username = mysqli_real_escape_string($conn, $username);
        $check_username = mysqli_query($conn, "
            SELECT id FROM users
            WHERE username = '$escaped_username' AND id != '$user_id'
            LIMIT 1
        ");

        if ($check_username && mysqli_num_rows($check_username) > 0) {
            self::setFlash('profile_error', 'Username sudah dipakai. Gunakan username lain.');
            header("Location: " . BASE_URL . "profile");
            exit;
        }

        // Password hanya diubah jika user benar-benar mengisi field password.
        $has_password = $password !== '';
        $hashed_password = $has_password ? password_hash($password, PASSWORD_DEFAULT) : null;

        // Gunakan query berbeda sesuai kombinasi data yang diubah:
        // foto + password, hanya foto, hanya password, atau hanya username/bio.
        if ($photo_upload !== null && $has_password) {
            $statement = mysqli_prepare($conn, "
                UPDATE users
                SET username = ?, bio = ?, password = ?, photo = ?, photo_type = ?
                WHERE id = ?
            ");

            $photo_data = $photo_upload['data'];
            $photo_type = $photo_upload['type'];
            mysqli_stmt_bind_param($statement, "sssssi", $username, $bio, $hashed_password, $photo_data, $photo_type, $user_id);
        } elseif ($photo_upload !== null) {
            $statement = mysqli_prepare($conn, "
                UPDATE users
                SET username = ?, bio = ?, photo = ?, photo_type = ?
                WHERE id = ?
            ");

            $photo_data = $photo_upload['data'];
            $photo_type = $photo_upload['type'];
            mysqli_stmt_bind_param($statement, "ssssi", $username, $bio, $photo_data, $photo_type, $user_id);
        } elseif ($has_password) {
            $statement = mysqli_prepare($conn, "
                UPDATE users
                SET username = ?, bio = ?, password = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param($statement, "sssi", $username, $bio, $hashed_password, $user_id);
        } else {
            $statement = mysqli_prepare($conn, "
                UPDATE users
                SET username = ?, bio = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param($statement, "ssi", $username, $bio, $user_id);
        }

        // Jika update database gagal, tampilkan pesan error.
        if (!mysqli_stmt_execute($statement)) {
            self::setFlash('profile_error', 'Profile gagal diperbarui.');
            header("Location: " . BASE_URL . "profile");
            exit;
        }

        // Update juga session username agar tampilan langsung mengikuti data terbaru.
        $_SESSION['username'] = $username;
        self::setFlash('profile_success', 'Profile berhasil diperbarui.');

        header("Location: " . BASE_URL . "home");
        exit;
    }
}

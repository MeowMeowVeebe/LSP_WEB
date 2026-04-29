<?php

class PostController
{
    private static function setFlash(string $key, string $message): void     
    {
        $_SESSION[$key] = $message;
    }

    private static function redirectHome(): void
    {
        header("Location: " . BASE_URL . "home");
        exit;
    }

    private static function redirectPostForm(string $path): void
    {
        header("Location: " . BASE_URL . ltrim($path, "/"));
        exit;
    }

    private static function getImageUpload(string $field_name): ?array
    {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$field_name];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload gambar gagal diproses.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Ukuran gambar maksimal 5MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']) ?: '';
        finfo_close($finfo);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime_type, $allowed_types, true)) {
            throw new RuntimeException('Format gambar harus JPG, PNG, GIF, atau WEBP.');
        }

        return [
            'data' => file_get_contents($file['tmp_name']),
            'type' => $mime_type,
        ];
    }

    private static function getFileUpload(string $field_name): ?array
    {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$field_name];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload file gagal diproses.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Ukuran file maksimal 5MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']) ?: 'application/octet-stream';
        finfo_close($finfo);

        return [
            'data' => file_get_contents($file['tmp_name']),
            'name' => basename($file['name']),
            'type' => $mime_type,
        ];
    }

    public static function index()
    {
        global $conn;

        $hashtag = $_GET['hashtag'] ?? "";
        $current_user = null;

        if (AuthController::check_auth()) {
            $current_user_id = (int) $_SESSION['user_id'];
            $current_user_result = mysqli_query($conn, "
                SELECT id, username, photo, photo_type
                FROM users
                WHERE id = '$current_user_id'
                LIMIT 1
            ");
            $current_user = mysqli_fetch_assoc($current_user_result);
        }

        $sql = "
            SELECT posts.*, users.username, users.photo, users.photo_type
            FROM posts
            JOIN users ON posts.user_id = users.id
        ";

        if ($hashtag != "") {
            $sql .= " WHERE posts.text LIKE '%$hashtag%'";
        }

        $sql .= " ORDER BY posts.created_at DESC";

        $posts = mysqli_query($conn, $sql);

        require ROOT_DIR . "/public/view/home/index.php";
    }

    public static function create()
    {
        if (!AuthController::check_auth()) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $current_user_id = (int) $_SESSION['user_id'];
        $current_user_result = mysqli_query($conn, "
            SELECT id, username, photo, photo_type
            FROM users
            WHERE id = '$current_user_id'
            LIMIT 1
        ");
        $current_user = mysqli_fetch_assoc($current_user_result);
        $form_mode = 'create';
        $form_action = BASE_URL . 'posts/store';
        $form_title = 'Buat Post';
        $form_button = 'Post';
        $post_data = [
            'id' => null,
            'text' => '',
            'image' => null,
            'image_type' => null,
            'file_name' => null,
        ];

        require ROOT_DIR . "/public/view/post_form/index.php";
    }

    public static function edit()
    {
        if (!AuthController::check_auth()) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $current_user_id = (int) $_SESSION['user_id'];
        $post_id = (int) ($_GET['id'] ?? 0);

        $current_user_result = mysqli_query($conn, "
            SELECT id, username, photo, photo_type
            FROM users
            WHERE id = '$current_user_id'
            LIMIT 1
        ");
        $current_user = mysqli_fetch_assoc($current_user_result);

        $post_result = mysqli_query($conn, "
            SELECT *
            FROM posts
            WHERE id = '$post_id' AND user_id = '$current_user_id'
            LIMIT 1
        ");
        $post_data = mysqli_fetch_assoc($post_result);

        if (!$post_data) {
            self::setFlash('home_error', 'Post tidak ditemukan.');
            self::redirectHome();
        }

        $form_mode = 'edit';
        $form_action = BASE_URL . 'posts/update';
        $form_title = 'Edit Post';
        $form_button = 'Update';

        require ROOT_DIR . "/public/view/post_form/index.php";
    }

    public static function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }


        global $conn;

        $user_id = $_SESSION['user_id'];
        $text = trim($_POST['text'] ?? '');

        if (strlen($text) > 250) {
            self::setFlash('post_error', 'Postingan maksimal 250 karakter.');
            self::redirectPostForm('/post');
        }

        try {
            $image_upload = self::getImageUpload('image');
            $file_upload = self::getFileUpload('file');
        } catch (RuntimeException $exception) {
            self::setFlash('post_error', $exception->getMessage());
            self::redirectPostForm('/post');
        }

        if ($text === '' && $image_upload === null && $file_upload === null) {
            self::setFlash('post_error', 'Isi postingan atau unggah gambar/file terlebih dahulu.');
            self::redirectPostForm('/post');
        }

        $image_data = $image_upload['data'] ?? null;
        $image_type = $image_upload['type'] ?? null;
        $file_data = $file_upload['data'] ?? null;
        $file_name = $file_upload['name'] ?? null;
        $file_type = $file_upload['type'] ?? null;

        $statement = mysqli_prepare($conn, "
            INSERT INTO posts (user_id, text, image, image_type, file, file_name, file_type)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $statement,
            "issssss",
            $user_id,
            $text,
            $image_data,
            $image_type,
            $file_data,
            $file_name,
            $file_type
        );

        if (!mysqli_stmt_execute($statement)) {
            self::setFlash('post_error', 'Postingan gagal disimpan.');
            self::redirectPostForm('/post');
        }

        self::setFlash('home_success', 'Postingan berhasil dibuat.');

        self::redirectHome();
    }

    public static function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $user_id = (int) $_SESSION['user_id'];
        $post_id = (int) ($_POST['post_id'] ?? 0);
        $text = trim($_POST['text'] ?? '');

        if ($post_id <= 0) {
            self::setFlash('home_error', 'Post tidak valid.');
            self::redirectHome();
        }

        $existing_result = mysqli_query($conn, "
            SELECT *
            FROM posts
            WHERE id = '$post_id' AND user_id = '$user_id'
            LIMIT 1
        ");
        $existing_post = mysqli_fetch_assoc($existing_result);

        if (!$existing_post) {
            self::setFlash('home_error', 'Post tidak ditemukan.');
            self::redirectHome();
        }

        if (strlen($text) > 250) {
            self::setFlash('post_error', 'Postingan maksimal 250 karakter.');
            self::redirectPostForm('/post/edit?id=' . $post_id);
        }

        try {
            $image_upload = self::getImageUpload('image');
            $file_upload = self::getFileUpload('file');
        } catch (RuntimeException $exception) {
            self::setFlash('post_error', $exception->getMessage());
            self::redirectPostForm('/post/edit?id=' . $post_id);
        }

        $image_data = $image_upload['data'] ?? $existing_post['image'];
        $image_type = $image_upload['type'] ?? $existing_post['image_type'];
        $file_data = $file_upload['data'] ?? $existing_post['file'];
        $file_name = $file_upload['name'] ?? $existing_post['file_name'];
        $file_type = $file_upload['type'] ?? $existing_post['file_type'];

        if ($text === '' && $image_data === null && $file_data === null) {
            self::setFlash('post_error', 'Isi postingan atau unggah gambar/file terlebih dahulu.');
            self::redirectPostForm('/post/edit?id=' . $post_id);
        }

        $statement = mysqli_prepare($conn, "
            UPDATE posts
            SET text = ?, image = ?, image_type = ?, file = ?, file_name = ?, file_type = ?
            WHERE id = ? AND user_id = ?
        ");

        mysqli_stmt_bind_param(
            $statement,
            "ssssssii",
            $text,
            $image_data,
            $image_type,
            $file_data,
            $file_name,
            $file_type,
            $post_id,
            $user_id
        );

        if (!mysqli_stmt_execute($statement)) {
            self::setFlash('post_error', 'Post gagal diperbarui.');
            self::redirectPostForm('/post/edit?id=' . $post_id);
        }

        self::setFlash('home_success', 'Post berhasil diperbarui.');
        self::redirectHome();
    }

    public static function delete()
    {
        
        global $conn;

        $post_id = $_POST['post_id'];
        $user_id = $_SESSION['user_id'];

        mysqli_query($conn, "
            DELETE FROM posts
            WHERE id = '$post_id' AND user_id = '$user_id'
        ");

        header("Location: " . BASE_URL . "home");
        exit;
    }

    public static function downloadFile()
    {
        global $conn;

        $post_id = (int) ($_GET['id'] ?? 0);

        if ($post_id <= 0) {
            http_response_code(404);
            exit('File tidak ditemukan.');
        }

        $statement = mysqli_prepare($conn, "
            SELECT file, file_name, file_type
            FROM posts
            WHERE id = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param($statement, "i", $post_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $post = mysqli_fetch_assoc($result);

        if (!$post || $post['file'] === null) {
            http_response_code(404);
            exit('File tidak ditemukan.');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . ($post['file_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . rawurlencode($post['file_name'] ?: 'file') . '"');
        header('Content-Length: ' . strlen($post['file']));

        echo $post['file'];
        exit;
    }
}

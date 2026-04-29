<?php

class CommentController
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

    private static function getImageUpload(string $field_name): ?array
    {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$field_name];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload gambar komentar gagal diproses.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Ukuran gambar komentar maksimal 5MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']) ?: '';
        finfo_close($finfo);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime_type, $allowed_types, true)) {
            throw new RuntimeException('Format gambar komentar harus JPG, PNG, GIF, atau WEBP.');
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
            throw new RuntimeException('Upload file komentar gagal diproses.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Ukuran file komentar maksimal 5MB.');
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

    public static function store()
    {
        if (!AuthController::check_auth()) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $post_id = $_POST['post_id'];
        $user_id = $_SESSION['user_id'];
        $text = trim($_POST['text'] ?? '');

        if (strlen($text) > 250) {
            self::setFlash('home_error', 'Komentar maksimal 250 karakter.');
            self::redirectHome();
        }

        try {
            $image_upload = self::getImageUpload('image');
            $file_upload = self::getFileUpload('file');
        } catch (RuntimeException $exception) {
            self::setFlash('home_error', $exception->getMessage());
            self::redirectHome();
        }

        if ($text === '' && $image_upload === null && $file_upload === null) {
            self::setFlash('home_error', 'Isi komentar atau unggah gambar/file terlebih dahulu.');
            self::redirectHome();
        }

        $image_data = $image_upload['data'] ?? null;
        $image_type = $image_upload['type'] ?? null;
        $file_data = $file_upload['data'] ?? null;
        $file_name = $file_upload['name'] ?? null;
        $file_type = $file_upload['type'] ?? null;

        $statement = mysqli_prepare($conn, "
            INSERT INTO comments (post_id, user_id, text, image, image_type, file, file_name, file_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $statement,
            "iissssss",
            $post_id,
            $user_id,
            $text,
            $image_data,
            $image_type,
            $file_data,
            $file_name,
            $file_type
        );

        if (!mysqli_stmt_execute($statement)) {
            self::setFlash('home_error', 'Komentar gagal disimpan.');
            self::redirectHome();
        }

        self::setFlash('home_success', 'Komentar berhasil ditambahkan.');

        self::redirectHome();
    }

    public static function delete()
    {
        if (!AuthController::check_auth()) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $comment_id = $_POST['comment_id'];
        $user_id = $_SESSION['user_id'];

        mysqli_query($conn, "
            DELETE FROM comments
            WHERE id = '$comment_id' AND user_id = '$user_id'
        ");

        header("Location: " . BASE_URL . "home");
        exit;
    }

    public static function update()
    {
        if (!AuthController::check_auth()) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        global $conn;

        $comment_id = (int) ($_POST['comment_id'] ?? 0);
        $user_id = (int) $_SESSION['user_id'];
        $text = trim($_POST['text'] ?? '');

        if ($comment_id <= 0) {
            self::setFlash('home_error', 'Komentar tidak valid.');
            self::redirectHome();
        }

        $existing_result = mysqli_query($conn, "
            SELECT *
            FROM comments
            WHERE id = '$comment_id' AND user_id = '$user_id'
            LIMIT 1
        ");
        $existing_comment = mysqli_fetch_assoc($existing_result);

        if (!$existing_comment) {
            self::setFlash('home_error', 'Komentar tidak ditemukan.');
            self::redirectHome();
        }

        if (strlen($text) > 250) {
            self::setFlash('home_error', 'Komentar maksimal 250 karakter.');
            header("Location: " . BASE_URL . "home?edit_comment=" . $comment_id);
            exit;
        }

        try {
            $image_upload = self::getImageUpload('image');
            $file_upload = self::getFileUpload('file');
        } catch (RuntimeException $exception) {
            self::setFlash('home_error', $exception->getMessage());
            header("Location: " . BASE_URL . "home?edit_comment=" . $comment_id);
            exit;
        }

        $image_data = $image_upload['data'] ?? $existing_comment['image'];
        $image_type = $image_upload['type'] ?? $existing_comment['image_type'];
        $file_data = $file_upload['data'] ?? $existing_comment['file'];
        $file_name = $file_upload['name'] ?? $existing_comment['file_name'];
        $file_type = $file_upload['type'] ?? $existing_comment['file_type'];

        if ($text === '' && $image_data === null && $file_data === null) {
            self::setFlash('home_error', 'Isi komentar atau unggah gambar/file terlebih dahulu.');
            header("Location: " . BASE_URL . "home?edit_comment=" . $comment_id);
            exit;
        }

        $statement = mysqli_prepare($conn, "
            UPDATE comments
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
            $comment_id,
            $user_id
        );

        if (!mysqli_stmt_execute($statement)) {
            self::setFlash('home_error', 'Komentar gagal diperbarui.');
            header("Location: " . BASE_URL . "home?edit_comment=" . $comment_id);
            exit;
        }

        self::setFlash('home_success', 'Komentar berhasil diperbarui.');
        self::redirectHome();
    }

    public static function downloadFile()
    {
        global $conn;

        $comment_id = (int) ($_GET['id'] ?? 0);

        if ($comment_id <= 0) {
            http_response_code(404);
            exit('File tidak ditemukan.');
        }

        $statement = mysqli_prepare($conn, "
            SELECT file, file_name, file_type
            FROM comments
            WHERE id = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param($statement, "i", $comment_id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $comment = mysqli_fetch_assoc($result);

        if (!$comment || $comment['file'] === null) {
            http_response_code(404);
            exit('File tidak ditemukan.');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . ($comment['file_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . rawurlencode($comment['file_name'] ?: 'file') . '"');
        header('Content-Length: ' . strlen($comment['file']));

        echo $comment['file'];
        exit;
    }
}

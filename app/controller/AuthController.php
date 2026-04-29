<?php

class AuthController
{
    // Menyimpan pesan sementara ke session agar bisa ditampilkan setelah redirect.
    private static function setFlash(string $key, string $message): void
    {
        $_SESSION[$key] = $message;
    }

    // Menampilkan halaman login.
    public static function login()
    {
        require ROOT_DIR . "/public/view/login_form/index.php";
    }

    // Menampilkan halaman register.
    public static function register()
    {
        require ROOT_DIR . "/public/view/register_form/index.php";
    }

    // Mengecek apakah user sedang login berdasarkan session user_id.
    public static function check_auth()
    {
        return isset($_SESSION['user_id']);
    }

    // Mengambil flash message auth dan username lama dari session
    // agar form login/register bisa menampilkan pesan dan mengisi ulang input.
    public static function auth(string $form = 'login'): array
    {
        $old_username_key = $form === 'register'
            ? 'old_register_username'
            : 'old_login_username';

        $auth_error = $_SESSION['auth_error'] ?? null;
        $auth_success = $_SESSION['auth_success'] ?? null;
        $old_username = $_SESSION[$old_username_key] ?? '';

        unset($_SESSION['auth_error'], $_SESSION['auth_success'], $_SESSION[$old_username_key]);

        return [
            'auth_error' => $auth_error,
            'auth_success' => $auth_success,
            'old_username' => $old_username,
        ];
    }

    // Memproses form register dan menyimpan user baru ke database.
    public static function processRegister()
    {
        global $conn;

        $username = trim($_POST['username'] ?? '');
        $raw_password = $_POST['password'] ?? '';

        // Simpan username lama agar bisa dimunculkan lagi jika validasi gagal.
        $_SESSION['old_register_username'] = $username;

        // Validasi input wajib.
        if ($username === '' || $raw_password === '') {
            self::setFlash('auth_error', 'Username dan password wajib diisi.');
            header("Location: " . BASE_URL . "register");
            exit;
        }

        // Cek apakah username sudah digunakan user lain.
        $escaped_username = mysqli_real_escape_string($conn, $username);
        $check_user = mysqli_query($conn, "
            SELECT id FROM users WHERE username = '$escaped_username' LIMIT 1
        ");

        if ($check_user && mysqli_num_rows($check_user) > 0) {
            self::setFlash('auth_error', 'Username sudah dipakai. Gunakan username lain.');
            header("Location: " . BASE_URL . "register");
            exit;
        }

        // Password disimpan dalam bentuk hash, bukan plaintext.
        $password = password_hash($raw_password, PASSWORD_DEFAULT);
        $escaped_password = mysqli_real_escape_string($conn, $password);

        $insert = mysqli_query($conn, "
            INSERT INTO users (username, password)
            VALUES ('$escaped_username', '$escaped_password')
        ");

        // Jika insert gagal, kembalikan user ke form register dengan pesan error.
        if (!$insert) {
            self::setFlash('auth_error', 'Registrasi gagal diproses. Coba lagi.');
            header("Location: " . BASE_URL . "register");
            exit;
        }

        // Jika berhasil, hapus input lama dan arahkan ke login.
        unset($_SESSION['old_register_username']);
        self::setFlash('auth_success', 'Registrasi berhasil. Silahkan login.');

        header("Location: " . BASE_URL . "login");
        exit;
    }

    // Memproses form login dan membuat session jika kredensial benar.
    public static function processLogin()
    {
        global $conn;

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Simpan username lama agar field tidak kosong saat login gagal.
        $_SESSION['old_login_username'] = $username;

        // Validasi input wajib.
        if ($username === '' || $password === '') {
            self::setFlash('auth_error', 'Username dan password wajib diisi.');
            header("Location: " . BASE_URL . "login");
            exit;
        }

        // Ambil user berdasarkan username yang diinput.
        $escaped_username = mysqli_real_escape_string($conn, $username);

        $result = mysqli_query($conn, "
            SELECT * FROM users WHERE username = '$escaped_username' LIMIT 1
        ");

        $user = mysqli_fetch_assoc($result);

        // Jika user ditemukan dan password cocok, buat session login.
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            unset($_SESSION['old_login_username']);

            header("Location: " . BASE_URL . "home");
            exit;
        }

        // Jika gagal login, kirim balik ke form dengan pesan error.
        self::setFlash('auth_error', 'Login gagal. Periksa username dan password.');
        header("Location: " . BASE_URL . "login");
        exit;
    }

    // Menghapus session login lalu mengarahkan user ke halaman login.
    public static function logout()
    {
        session_destroy();
        header("Location: " . BASE_URL . "login");
        exit;
    }
}

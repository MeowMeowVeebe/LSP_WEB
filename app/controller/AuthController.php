<?php

class AuthController
{
    public static function login()
    {
        require ROOT_DIR . "/public/view/login_form/index.php";
    }

    public static function register()
    {
        require ROOT_DIR . "/public/view/register_form/index.php";
    }

    public static function check_auth()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        else{
            return true;
        }

    }

    public static function processRegister()
    {
        global $conn;

        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        mysqli_query($conn, "
            INSERT INTO users (username, password)
            VALUES ('$username', '$password')
        ");

        header("Location: " . BASE_URL . "login");
        exit;
    }

    public static function processLogin()
    {
        global $conn;

        $username = $_POST['username'];
        $password = $_POST['password'];

        $result = mysqli_query($conn, "
            SELECT * FROM users WHERE username = '$username'
        ");

        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: " . BASE_URL . "home");
            exit;
        }

        echo "Login gagal";
    }

    public static function logout()
    {
        session_destroy();
        header("Location: " . BASE_URL . "login");
        exit;
    }
}
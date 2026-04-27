<?php

class UserController
{
    public static function profile()
    {
        AuthController::check_auth();
        global $conn;

        $user_id = $_SESSION['user_id'];

        $result = mysqli_query($conn, "
            SELECT * FROM users WHERE id = '$user_id'
        ");

        $user = mysqli_fetch_assoc($result);

        require ROOT_DIR . "/public/view/profile_form/index.php";
    }

    public static function updateProfile()
    {
         AuthController::check_auth();
        global $conn;

        $user_id = $_SESSION['user_id'];
        $bio = $_POST['bio'];

        mysqli_query($conn, "
            UPDATE users
            SET bio = '$bio'
            WHERE id = '$user_id'
        ");

        header("Location: " . BASE_URL . "profile");
        exit;
    }
}

<?php

class CommentController
{
    public static function store()
    {
        AuthController::check_auth();
        global $conn;

        $post_id = $_POST['post_id'];
        $user_id = $_SESSION['user_id'];
        $text = $_POST['text'];

        if (strlen($text) > 250) {
            die("Komentar maksimal 250 karakter");
        }

        mysqli_query($conn, "
            INSERT INTO comments (post_id, user_id, text)
            VALUES ('$post_id', '$user_id', '$text')
        ");

        header("Location: " . BASE_URL . "home");
        exit;
    }

    public static function delete()
    {
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
}
<?php

class PostController
{
    public static function index()
    {
        global $conn;

        $hashtag = $_GET['hashtag'] ?? "";

        $sql = "
            SELECT posts.*, users.username
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

    public static function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }


        global $conn;

        $user_id = $_SESSION['user_id'];
        $text = $_POST['text'];

        if (strlen($text) > 250) {
            die("Postingan maksimal 250 karakter");
        }

        mysqli_query($conn, "
            INSERT INTO posts (user_id, text)
            VALUES ('$user_id', '$text')
        ");

        header("Location: " . BASE_URL . "home");
        exit;
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
}
<?php
$routes = [
    "GET" => [
        "/login" => [AuthController::class, "login"],
        "/header" => [PageController::class, "header"],
        "/register" => [AuthController::class, "register"],
        "/logout" => [AuthController::class, "logout"],
        "/home" => [PostController::class, "index"],
        "/profile" => [UserController::class, "profile"],
    ],

    "POST" => [
        "/login" => [AuthController::class, "processLogin"],
        "/register" => [AuthController::class, "processRegister"],
        "/posts/store" => [PostController::class, "store"],
        "/posts/delete" => [PostController::class, "delete"],
        "/comments/store" => [CommentController::class, "store"],
        "/comments/delete" => [CommentController::class, "delete"],
        "/profile/update" => [UserController::class, "updateProfile"],
    ],
];
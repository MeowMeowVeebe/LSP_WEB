<?php
$routes = [    ///// Menentukan url mana yang untuk menjalankan class controller bersertakan method-methodnya
    "GET" => [
        "/" => [PostController::class, "index"],                             ////*  -----> 
        "/login" => [AuthController::class, "login"],
        "/header" => [PageController::class, "header"],
        "/register" => [AuthController::class, "register"],
        "/logout" => [AuthController::class, "logout"],
        "/home" => [PostController::class, "index"],
        "/post" => [PostController::class, "create"],
        "/post/edit" => [PostController::class, "edit"],
        "/posts/file" => [PostController::class, "downloadFile"],
        "/comments/file" => [CommentController::class, "downloadFile"],
        "/profile" => [UserController::class, "profile"],
        "/info_user" => [UserController::class, "info_user" ]

    ],

    "POST" => [
        "/login" => [AuthController::class, "processLogin"],
        "/register" => [AuthController::class, "processRegister"],
        "/posts/store" => [PostController::class, "store"],
        "/posts/update" => [PostController::class, "update"],
        "/posts/delete" => [PostController::class, "delete"],
        "/comments/store" => [CommentController::class, "store"],
        "/comments/update" => [CommentController::class, "update"],
        "/comments/delete" => [CommentController::class, "delete"],
        "/profile/update" => [UserController::class, "updateProfile"],
        "/"
    ],
];

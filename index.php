<?php


session_start();


define('ROOT_DIR', __DIR__);   
$script_dir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));
$script_dir = $script_dir === "/" ? "" : rtrim($script_dir, "/");
$base_url = ($script_dir === "" ? "" : $script_dir) . "/";

define("BASE_URL", $base_url);  //// 
define("ASSET_URL", $base_url . "public/");  //// ---> UNTUK VARIBEL BIAR DIPAKE

require_once ROOT_DIR . '/config/database.php';                            //***
                                                                       //
require_once ROOT_DIR . "/app/controller/AuthController.php";           //       
require_once ROOT_DIR . "/app/controller/UserController.php";
require_once ROOT_DIR . "/app/controller/PageController.php";
require_once ROOT_DIR . "/app/controller/CommentController.php";          //
require_once ROOT_DIR . "/app/controller/PostController.php";           //
require_once ROOT_DIR . "/app/controller/ErrorController.php";          //
require_once ROOT_DIR . "/routes/web.php";                            //
                                                                    // ----> UNTUK MEREFRENSI DATA YANG DI CONTROLLER SUPAYA BIAR CLASS, METHODNYA DIPANGGIL


$url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);    
if ($script_dir !== "" && strpos($url, $script_dir) === 0) { /// ---> Mengecek apakah diawali dengan folder project
    $url = substr($url, strlen($script_dir));
}
$url = $url === "" ? "/" : $url; ///// ---> Mengubah url kosong menjadi //
$requestMethod = $_SERVER["REQUEST_METHOD"]; ////--> mengdekralasikan variabel

if (isset($routes[$requestMethod][$url])) {   //////-> JIKA VARIABEL ROUTE ADA BERSERTALAM METHOD METHOD DAN CLASS FUNGSINYA
    [$controllerName, $method] = $routes[$requestMethod][$url]; ///// ---> sebenarnya itu controllername dan method nya isi dari $URL
    $controllerName::$method(); //////--> MEMANGIL FUNGSI 
} else {
    Error_Controller::error();   ////-> KALAU MUNCUL 404 ERROR NOT FOUND
}



<?php


session_start();


define('ROOT_DIR', __DIR__);   
define("BASE_URL", "/web_lsp/");  //// 
define("ASSET_URL", "/web_lsp/public/");  //// ---> UNTUK VARIBEL BIAR DIPAKE

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



$url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);    ////// --> BAGIAN ID DIBUANG 
$url = str_replace("/web_lsp", "", $url);  ////-> MENGHAPUSKAN /web_lsp SUPAYA BISA DI FUNGSI
$requestMethod = $_SERVER["REQUEST_METHOD"]; ////-->

if (isset($routes[$requestMethod][$url])) {   //////-> JIKA VARIABEL ROUTE ADA BERSERTALAM METHOD METHOD DAN CLASS FUNGSINYA
    [$controllerName, $method] = $routes[$requestMethod][$url]; ///// ---> MENGASIH VARIABEL SEPERTI $controllername = $requestMethod
    $controllerName::$method(); //////--> MEMANGIL FUNGSI 
} else {
    Error_Controller::error();   ////-> KALAU MUNCUL 404 ERROR NOT FOUND
}



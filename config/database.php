<?php 

$host = 'localhost';
$db = 'lsp_db';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host , $user , $pass , $db);

if (!$conn)
    {
        die("koneksi gagal". mysqli_connect_error());
    }


?>
<?php
define('HOST','localhost');
define('DB','projetoame');
define('USUARIO','projetoame');
define('SENHA','vp3imJizMOgWxbM');

$conn = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar');
/*
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch($e) {
    die("Connection failed: " . $e->getMessage());
}
*/

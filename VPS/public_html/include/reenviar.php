<?php
session_start();
include('conexao.php');
include('funcoes.php');
$query = "select * 
from usuarios 
WHERE usuario_id=".$_SESSION['id'];
$result = mysqli_query($conexao, $query);
if(mysqli_num_rows($result)>0) {
    $row1 = mysqli_fetch_assoc($result);
	$_SESSION['id'] = $row1["usuario_ID"];
} 


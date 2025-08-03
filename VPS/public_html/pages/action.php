<?php
  require_once 'conexao.php';

  if (isset($_POST['query'])) {
    $inpText = $_POST['query'];
    $sql = "SELECT expositor_id,empresa,NomeFantasia FROM expositores2024 WHERE empresa LIKE '%".$inpText."%'";
	$result = mysqli_query($conexao,$sql);

    if(mysqli_num_rows($result)>0) {
      while($row = mysqli_fetch_array($result)){
        echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $row['empresa'] . '</a>';
      }
     echo '<a href="novo.php" class="list-group-item list-group-item-action border-1">Adicionar novo</a></p>';
    } else {
      echo '<p class="list-group-item border-1">Nada encontrado <a href="novo.php">Adicionar novo</a></p></p>';
     echo '';
    }
  }
?>
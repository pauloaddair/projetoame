<?php
  include_once './include/conexao.php';
  include_once './include/funcoes.php';

  if (isset($_POST['query'])) {
    $inpText = $_POST['query'];
    $sql = "SELECT * FROM candidatos WHERE nome LIKE '%".$inpText."%'";
	$result = mysqli_query($conexao,$sql);

    if(mysqli_num_rows($result)>0) {
      while($row = mysqli_fetch_array($result)){
        echo '<a href="/editacandidato/'.digitos($row).' class="list-group-item list-group-item-action border-1">' . $row['nome'] . '</a>';
      }
     echo '<a href="/editacandidato" class="list-group-item list-group-item-action border-1">Adicionar novo</a></p>';
    } else {
      echo '<p class="list-group-item border-1">Nada encontrado <a href="novo.php">Adicionar novo</a></p></p>';
     echo '';
    }
  }
?>
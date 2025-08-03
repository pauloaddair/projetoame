<?php
include 'conexao.php'; // Arquivo de conexão com MySQLi
include 'funcoes.php'; // Arquivo de conexão com MySQLi

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);  // Sanitiza o valor recebido
    $sql = "SELECT mensagens.*,
imagens.url AS img_url, 
convidados.chave AS chave1,
convidados.nome,
convidados.email,
convidados.obs,
convidados.c1,
convidados.c2,
convidados.c3,
convidados.c4,
convidados.c5
FROM mensagens 
		LEFT JOIN imagens ON mensagens.imagem_id = imagens.imagem_id
		LEFT JOIN convidados ON mensagens.evento_id = convidados.evento_id
		WHERE mensagem_id = ? LIMIT 1;";
	$msg ="";
	$img = "";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
		$nome = $row['nome'];
		$obs = $row['obs'];
		$email = $row['email'];
		$chave = $row['chave'];
		$chave1 = $row['chave1'];
		$c1 = $row['c1'];
		$c2 = $row['c2'];
		$c3 = $row['c3'];
		$c4 = $row['c4'];
		$c5 = $row['c5'];
		$link = "https://fridahh.com/".$chave.$chave1;
		$links = array('{nome}','{link}',"{obs}","{email}","{c1}","{c2}","{c3}","{c4}","{c5}");
		$valores = array($nome, $link,$obs,$email,$c1,$c2,$c3,$c4,$c5);
		if ($row['tipo_msg']==0){
			$img = "<img src='/".$row['img_url']."' class='img-fluid'><br>";
		}
		$msg = $img . $row['mensagem'];			
		$textoinicial = str_replace($links,$valores,$msg);
        echo whatsAppToHtmlText($textoinicial);  // Retorna o conteúdo da mensagem
    } else {
        echo "Mensagem não encontrada.";
    }
    $stmt->close();
}
?>

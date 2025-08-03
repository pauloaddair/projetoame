<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = isset($_POST["titulo"]) ? trim($_POST["titulo"]) : "";
    $mensagem = isset($_POST["mensagem"]) ? trim($_POST["mensagem"]) : "";

    if (!empty($titulo) && !empty($mensagem)) {
        $stmt = $conexao->prepare("INSERT INTO mensagens (titulo, mensagem, criado_em) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $titulo, $mensagem);
        
        if ($stmt->execute()) {
            echo "Mensagem salva com sucesso!";
        } else {
            echo "Erro ao salvar mensagem: " . $conexao->error;
        }
        
        $stmt->close();
    } else {
        echo "Todos os campos são obrigatórios.";
    }
}

$conexao->close();
?>

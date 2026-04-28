<?php
// include_once('include/conexao.php');

header('Content-Type: application/json');

if (isset($_GET['email'])) {
    $email = mysqli_real_escape_string($conexao, $_GET['email']);
    
    $query = "SELECT candidatos.candidato_id, candidatos.nome, candidatos.RG, candidatos.CPF, candidatos.cidade, candidatos.UF, candidatos.responsavel, candidatos.CPF_RESP, imagens.url 
              FROM candidatos 
              LEFT JOIN imagens ON candidatos.imagem_id = imagens.imagem_id 
              WHERE candidatos.Email LIKE '$email' 
              LIMIT 1";
              
    $result = mysqli_query($conexao, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        // Garantir que a URL da imagem esteja correta
        if (empty($data['url'])) {
            $data['url'] = 'img/profile.png';
        }
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Candidato não encontrado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'E-mail não fornecido']);
}
?>

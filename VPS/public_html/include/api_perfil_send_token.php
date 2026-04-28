<?php
// include/api_perfil_send_token.php - Gera token e envia via Evolution API (WhatsApp)
header('Content-Type: application/json');
include_once('./conexao.php');

$data = json_decode(file_get_contents('php://input'), true);
$email = mysqli_real_escape_string($conexao, $data['email']);
$metodo = $data['method']; // 'whatsapp' ou 'email'

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

// 1. Busca dados para envio
$query = "SELECT c.candidato_id, c.usuario_id, c.nome as nome_candidato, c.Telefone, u.login 
          FROM candidatos c
          JOIN usuarios u ON c.usuario_id = u.usuario_ID
          WHERE c.Email = '$email' LIMIT 1";
$result = mysqli_query($conexao, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $usuario_id = $row['usuario_id'];
    $telefone = preg_replace('/\D/', '', $row['Telefone']); // Limpa telefone
    $nome = explode(' ', $row['nome_candidato'])[0]; // Pega primeiro nome
    
    // 2. Gera Token Único
    $token = bin2hex(random_bytes(16));
    $expira = date('Y-m-d H:i:s', strtotime('+2 hours'));
    
    $query_token = "INSERT INTO tokens_acesso (usuario_id, token, expira_em) VALUES ($usuario_id, '$token', '$expira')";
    mysqli_query($conexao, $query_token);
    
    $link = "https://projetoame.org/ativar-perfil/" . $token;

    if ($metodo === 'whatsapp' && !empty($telefone)) {
        // Envio via Evolution API
        $instancia = "ProjetoAME";
        $apikey = "3ACDA4BD8811-4B32-A4E2-8A43367E1DAB";
        $url_evo = "https://nasanet.cloud/message/sendText/" . $instancia;

        $mensagem = "Olá $nome! 💙\n\nIdentificamos sua solicitação de acesso ao Portal do Associado AME.\n\nPara definir sua senha e acessar o perfil, clique no link seguro abaixo:\n$link\n\nEste link expira em 2 horas.";

        $post_data = [
            "number" => "55" . $telefone,
            "text" => $mensagem
        ];

        $ch = curl_init($url_evo);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "apikey: $apikey"
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 201 || $http_code == 200) {
            echo json_encode(['success' => true, 'message' => 'Link enviado com sucesso para seu WhatsApp!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao enviar WhatsApp. Tente novamente por E-mail.']);
        }
    } else {
        // Lógica de E-mail (Placeholder ou PHPMailer)
        echo json_encode(['success' => false, 'message' => 'Método não suportado ou telefone não cadastrado.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Cadastro não encontrado.']);
}
?>

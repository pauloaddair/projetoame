<?php
// include_once('include/conexao.php');
mysqli_set_charset($conexao, "utf8mb4");

header('Content-Type: application/json');

// Suportar tanto GET quanto POST para facilitar testes
$method = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;

if (isset($method['email']) && !empty($method['email'])) {
    $email = mysqli_real_escape_string($conexao, trim(strtolower($method['email'])));
    
    // Busca simplificada apenas pelo e-mail (conforme solicitado para facilitar o acesso)
    $query = "SELECT candidatos.*, imagens.url as foto_url 
              FROM candidatos 
              LEFT JOIN imagens ON candidatos.imagem_id = imagens.imagem_id 
              WHERE (candidatos.Email LIKE '$email' OR candidatos.Email LIKE '%$email%')
              LIMIT 1";
              
    $result = mysqli_query($conexao, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Limpeza de campos administrativos
        unset($data['usuario_id']);
        unset($data['rodizio']);
        unset($data['inscrito']);
        
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'E-mail não localizado. Verifique se digitou corretamente ou se este é o e-mail cadastrado.'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'O campo e-mail é obrigatório para identificação.']);
}
?>

<?php
/**
 * invite_responsavel.php
 * Recebe e‑mail do responsável e ID do candidato, gera token de convite e envia via Evolution API.
 * Persistencia em tabela `convites_responsaveis` (cria se não existir).
 */

require_once __DIR__.'/../../vendor/autoload.php'; // ajuste caso Composer esteja na raiz do projeto
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$candidato_id = intval($_POST['candidato_id'] ?? 0);

if (!$email || $candidato_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'E‑mail e candidato_id são obrigatórios.']);
    exit;
}

$token = bin2hex(random_bytes(16)); // token de 32 caracteres

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $_ENV['DB_HOST'],
        $_ENV['DB_PORT'] ?? '3306',
        $_ENV['DB_DATABASE']
    );
    $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Cria a tabela se não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS convites_responsaveis (
        id INT AUTO_INCREMENT PRIMARY KEY,
        candidato_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(64) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        used_at TIMESTAMP NULL,
        UNIQUE KEY uniq_email_candidato (candidato_id, email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Insere registro de convite
    $stmt = $pdo->prepare("INSERT INTO convites_responsaveis (candidato_id, email, token) VALUES (:candidato_id, :email, :token)");
    $stmt->execute([
        ':candidato_id' => $candidato_id,
        ':email' => $email,
        ':token' => $token,
    ]);

    // Envio via Evolution API (simples curl)
    $apiUrl = $_ENV['EVOLUTION_API_URL_ProjetoAME'] ?? '';
    $apiKey = $_ENV['EVOLUTION_API_KEY_ProjetoAME'] ?? '';
    $instance = $_ENV['EVOLUTION_INSTANCE_NAME_ProjetoAME'] ?? '';
    $linkConvite = "https://{$instance}.projetoame.org.br/aceitar-convite.php?token={$token}";
    $mensagem = "Você foi convidado a acessar seu perfil como responsável. Clique no link: {$linkConvite}";

    $payload = [
        'number' => $email,
        'text' => $mensagem,
    ];

    $ch = curl_init(rtrim($apiUrl, '/') . '/message/sendText/' . $instance);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        throw new Exception('Erro ao enviar convite via Evolution: ' . $err);
    }

    echo json_encode(['success' => true, 'message' => 'Convite enviado com sucesso.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

<?php
// migrar_campos_painel.php - Criação de colunas para Saúde Avançada e Cursos Externos
$conexao_path = __DIR__ . '/public_html/database/conexao.php';
if (!file_exists($conexao_path)) {
    $conexao_path = 'F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/database/conexao.php';
}

require_once $conexao_path;

$columns_to_add = [
    'medicacao_horarios' => "ALTER TABLE candidatos ADD COLUMN medicacao_horarios TEXT DEFAULT NULL AFTER medicacao_continuada",
    'cuidados_especiais' => "ALTER TABLE candidatos ADD COLUMN cuidados_especiais TEXT DEFAULT NULL AFTER orientacoes_responsaveis",
    'cursos_externos' => "ALTER TABLE candidatos ADD COLUMN cursos_externos TEXT DEFAULT NULL AFTER cuidados_especiais",
    't21_laudo_url' => "ALTER TABLE candidatos ADD COLUMN t21_laudo_url VARCHAR(255) DEFAULT NULL AFTER cursos_externos"
];

echo "Iniciando migração de campos para o Painel do Responsável...\n";

foreach ($columns_to_add as $col_name => $sql) {
    // Verifica se a coluna já existe
    $check_q = "SHOW COLUMNS FROM candidatos LIKE '$col_name'";
    $res_check = mysqli_query($conexao, $check_q);
    
    if ($res_check && mysqli_num_rows($res_check) == 0) {
        if (mysqli_query($conexao, $sql)) {
            echo " [OK] Coluna '$col_name' adicionada com sucesso.\n";
        } else {
            echo " [ERRO] Falha ao adicionar '$col_name': " . mysqli_error($conexao) . "\n";
        }
    } else {
        echo " [INFO] Coluna '$col_name' já existe na tabela candidatos.\n";
    }
}

// Cria tabela de co-responsáveis se não existir
$sql_co_resp = "CREATE TABLE IF NOT EXISTS convites_responsaveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    usuario_origem_id INT NOT NULL,
    email_convidado VARCHAR(150) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    status ENUM('Pendente', 'Aceito', 'Expirado') DEFAULT 'Pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL
)";

if (mysqli_query($conexao, $sql_co_resp)) {
    echo " [OK] Tabela 'convites_responsaveis' verificada/criada.\n";
} else {
    echo " [ERRO] Tabela 'convites_responsaveis': " . mysqli_error($conexao) . "\n";
}

echo "Migração concluída com sucesso.\n";
?>

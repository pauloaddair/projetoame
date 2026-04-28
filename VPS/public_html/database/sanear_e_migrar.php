<?php
require_once __DIR__ . '/conexao.php';
global $conexao;

echo "Iniciando saneamento de usuários...<br>";

// 1. Identificar usuários órfãos em 'candidatos'
$queryOrfaos = "SELECT DISTINCT usuario_id FROM candidatos WHERE usuario_id IS NOT NULL AND usuario_id != 0 AND usuario_id NOT IN (SELECT usuario_id FROM usuarios)";
$result = mysqli_query($conexao, $queryOrfaos);

while ($row = mysqli_fetch_assoc($result)) {
    $uid = $row['usuario_id'];
    echo "Criando placeholder para usuario_id: $uid... ";
    $sqlInsertUser = "INSERT INTO usuarios (usuario_id, nome, email, login) VALUES ($uid, 'Responsável Legado $uid', 'legado_$uid@projetoame.org', 'legado_$uid')";
    if(mysqli_query($conexao, $sqlInsertUser)) {
        echo "OK.<br>";
    } else {
        echo "Erro: " . mysqli_error($conexao) . "<br>";
    }
}

echo "Saneamento concluído. Procedendo com a criação da tabela de relacionamento...<br>";

// 2. Criar tabela de relacionamento (agora com segurança)
$sqlCreate = "CREATE TABLE IF NOT EXISTS `candidatos_usuarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `candidato_id` int(11) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    `is_responsavel_principal` tinyint(1) NOT NULL DEFAULT 0,
    `tipo_relacao` varchar(50) DEFAULT NULL,
    `data_vinculo` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_vinculo` (`candidato_id`, `usuario_id`),
    CONSTRAINT `fk_candidato` FOREIGN KEY (`candidato_id`) REFERENCES `candidatos` (`candidato_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conexao, $sqlCreate)) {
    echo "Tabela 'candidatos_usuarios' criada com sucesso.<br>";
} else {
    echo "Erro ao criar tabela: " . mysqli_error($conexao) . "<br>";
}

// 3. Migrar dados
$sqlMigrate = "INSERT IGNORE INTO candidatos_usuarios (candidato_id, usuario_id, is_responsavel_principal)
               SELECT candidato_id, usuario_id, 1 
               FROM candidatos 
               WHERE usuario_id IS NOT NULL AND usuario_id != 0";

if (mysqli_query($conexao, $sqlMigrate)) {
    echo "Dados migrados com sucesso.<br>";
} else {
    echo "Erro ao migrar dados: " . mysqli_error($conexao) . "<br>";
}
?>
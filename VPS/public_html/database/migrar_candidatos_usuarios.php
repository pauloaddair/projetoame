<?php
require_once __DIR__ . '/conexao.php';

// $conexao é a variável definida no conexao.php
global $conexao;

try {
    // 1. Criar a nova tabela
    $sqlCreate = "CREATE TABLE IF NOT EXISTS `candidatos_usuarios` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `candidato_id` int(11) NOT NULL,
        `usuario_id` int(11) NOT NULL,
        `is_responsavel_principal` tinyint(1) NOT NULL DEFAULT 0,
        `tipo_relacao` varchar(50) DEFAULT NULL,
        `data_vinculo` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_vinculo` (`candidato_id`, `usuario_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (mysqli_query($conexao, $sqlCreate)) {
        echo "Tabela 'candidatos_usuarios' criada ou já existente.<br>";
    } else {
        echo "Erro ao criar tabela: " . mysqli_error($conexao) . "<br>";
    }

    // 2. Migrar dados existentes
    $check = mysqli_query($conexao, "SELECT COUNT(*) FROM candidatos_usuarios");
    $count = mysqli_fetch_array($check)[0];

    if ($count == 0) {
        $sqlMigrate = "INSERT INTO candidatos_usuarios (candidato_id, usuario_id, is_responsavel_principal)
                       SELECT candidato_id, usuario_id, 1 
                       FROM candidatos 
                       WHERE usuario_id IS NOT NULL AND usuario_id != 0";
        
        if (mysqli_query($conexao, $sqlMigrate)) {
            echo "Dados migrados com sucesso.<br>";
        } else {
            echo "Erro ao migrar dados: " . mysqli_error($conexao) . "<br>";
        }
    } else {
        echo "A tabela já contém dados, migração ignorada.<br>";
    }

} catch(Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
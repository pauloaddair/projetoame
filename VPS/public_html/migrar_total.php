<?php
/**
 * Script de Migração Consolidado para o Servidor - Projeto AME
 * Este script aplica todas as mudanças estruturais necessárias no banco de dados.
 */

require_once(__DIR__ . '/database/conexao.php');

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

echo "<h2>Iniciando Migração do Banco de Dados</h2>";

$migracoes = [
    "Alterando tabela 'eventos_marcados' para incluir 'tipo'" => 
        "ALTER TABLE eventos_marcados ADD COLUMN IF NOT EXISTS tipo ENUM('Trabalho', 'Curso', 'Treinamento') DEFAULT 'Trabalho' AFTER nome",

    "Adicionando expositor_id em 'eventos_marcados'" => 
        "ALTER TABLE eventos_marcados ADD COLUMN IF NOT EXISTS expositor_id INT DEFAULT NULL AFTER tipo",

    "Adicionando tipo_evento em 'eventos_marcados'" => 
        "ALTER TABLE eventos_marcados ADD COLUMN IF NOT EXISTS tipo_evento ENUM('atendimento', 'curso', 'reuniao') NOT NULL DEFAULT 'atendimento' AFTER local",

    "Adicionando status_evento em 'eventos_marcados'" => 
        "ALTER TABLE eventos_marcados ADD COLUMN IF NOT EXISTS status_evento ENUM('agendado', 'realizado', 'cancelado') NOT NULL DEFAULT 'realizado' AFTER tipo_evento",

    "Adicionando uuid em 'eventos_marcados'" => 
        "ALTER TABLE eventos_marcados ADD COLUMN IF NOT EXISTS uuid CHAR(36) NULL DEFAULT NULL AFTER status_evento",

    "Adicionando rodizio_processado em 'eventos_marcados'" => 
        "ALTER TABLE eventos_marcados ADD COLUMN IF NOT EXISTS rodizio_processado TINYINT(1) NOT NULL DEFAULT 0 AFTER status_evento",

    "Criando tabela 'imagem_candidato' (Portfólio Visual)" => 
        "CREATE TABLE IF NOT EXISTS imagem_candidato (
            id INT AUTO_INCREMENT PRIMARY KEY,
            imagem_id INT NOT NULL,
            candidato_id INT NOT NULL,
            evento_id INT DEFAULT NULL,
            horario_id INT DEFAULT NULL,
            tags_ia TEXT DEFAULT NULL,
            data_vinculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

    "Criando tabela 'candidato_notes' (Prontuário/Anotações)" => 
        "CREATE TABLE IF NOT EXISTS candidato_notas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            candidato_id INT NOT NULL,
            usuario_id INT NOT NULL,
            tipo ENUM('Campo', 'Avaliação', 'Saúde', 'Incidente', 'Elogio') DEFAULT 'Campo',
            nota TEXT NOT NULL,
            data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

    "Criando tabela 'candidatos_usuarios' (Multi-responsáveis)" => 
        "CREATE TABLE IF NOT EXISTS candidatos_usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            candidato_id INT NOT NULL,
            usuario_id INT NOT NULL,
            vinculo_tipo ENUM('Pai', 'Mãe', 'Irmão', 'Tutor', 'Outro') DEFAULT 'Tutor',
            pode_editar TINYINT(1) DEFAULT 1,
            data_vinculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY (candidato_id, usuario_id)
        )",

    "Limpando tabela antiga de avaliacoes" => 
        "DROP TABLE IF EXISTS avaliacoes",

    "Criando tabela 'avaliacoes' com o novo formato" => 
        "CREATE TABLE avaliacoes (
            avaliacao_id INT AUTO_INCREMENT PRIMARY KEY,
            evento_id INT NOT NULL,
            candidato_id INT NOT NULL,
            pontualidade INT NOT NULL,
            asseio INT NOT NULL,
            socializacao INT NOT NULL,
            simpatia INT NOT NULL,
            compreensao_instrucoes INT NOT NULL,
            facilidade_orientacoes INT NOT NULL,
            foco_atividades INT NOT NULL,
            comportamento_geral INT NOT NULL,
            observacoes TEXT NULL,
            avaliador_nome VARCHAR(128) NULL,
            avaliador_email VARCHAR(128) NULL,
            data_avaliacao DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci"
];

foreach ($migracoes as $descricao => $sql) {
    echo "Executando: $descricao... ";
    if (mysqli_query($conexao, $sql)) {
        echo "<span style='color:green;'>OK</span><br>";
    } else {
        echo "<span style='color:red;'>ERRO: " . mysqli_error($conexao) . "</span><br>";
    }
}

echo "<br><strong>Migração concluída!</strong>";
mysqli_close($conexao);
?>

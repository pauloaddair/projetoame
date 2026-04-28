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

    "Adicionando campos de saúde na tabela 'candidatos'" => 
        "ALTER TABLE candidatos
            ADD COLUMN IF NOT EXISTS restricoes_alimentares TEXT,
            ADD COLUMN IF NOT EXISTS medicacao_continuada TEXT,
            ADD COLUMN IF NOT EXISTS cuidados_especiais TEXT,
            ADD COLUMN IF NOT EXISTS orientacoes_responsaveis TEXT",

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

    "Criando tabela 'candidato_notas' (Prontuário/Anotações)" => 
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
        )"
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

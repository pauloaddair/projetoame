<?php
// Script de Migração Física de Produção Resiliente (100% Limpo)
// IMPORTANTE: delete este arquivo do servidor após a execução por segurança!

$base_path = dirname(__DIR__) . '/';
if (file_exists($base_path . 'database/conexao.php')) {
    include_once $base_path . 'database/conexao.php';
} else {
    die("Erro: Arquivo conexao.php não encontrado em database/conexao.php.\n");
}

if (!$conexao) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

echo "<pre>";
echo "=== INICIANDO MIGRAÇÃO FÍSICA RESILIENTE NO SERVIDOR ===\n";

function run_sql($conexao, $sql, $msg) {
    try {
        if (mysqli_query($conexao, $sql)) {
            echo "  [OK] $msg\n";
            return true;
        } else {
            echo "  [ERRO] $msg: " . mysqli_error($conexao) . "\n";
            return false;
        }
    } catch (mysqli_sql_exception $e) {
        echo "  [ERRO] $msg (Exceção SQL): " . $e->getMessage() . "\n";
        return false;
    }
}

function table_exists($conexao, $table) {
    try {
        $res = mysqli_query($conexao, "SHOW TABLES LIKE '$table'");
        return ($res && mysqli_num_rows($res) > 0);
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function column_exists($conexao, $table, $column) {
    if (!table_exists($conexao, $table)) return false;
    try {
        $res = mysqli_query($conexao, "SHOW COLUMNS FROM `$table` LIKE '$column'");
        return ($res && mysqli_num_rows($res) > 0);
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

// 0. Preparação da Tabela Pai (expositores2024 -> empresas)
echo "\n0. Preparando tabela principal 'empresas'...\n";

// Dropar FKs antigas se existirem para permitir a renomeação e alteração da tabela pai
// Envolvido em try-catch para rodar de forma limpa mesmo que a FK já tenha sido removida em tentativas passadas
try {
    mysqli_query($conexao, "ALTER TABLE `contatos` DROP FOREIGN KEY `contatos_ibfk_1`");
    echo "  [INFO] Chave contatos_ibfk_1 removida com sucesso ou já não existia.\n";
} catch (mysqli_sql_exception $e) {
    echo "  [INFO] Chave contatos_ibfk_1 já estava removida.\n";
}

try {
    mysqli_query($conexao, "ALTER TABLE `prospects` DROP FOREIGN KEY `prospects_ibfk_2`");
    echo "  [INFO] Chave prospects_ibfk_2 removida com sucesso ou já não existia.\n";
} catch (mysqli_sql_exception $e) {
    echo "  [INFO] Chave prospects_ibfk_2 já estava removida.\n";
}

if (table_exists($conexao, 'expositores2024') && !table_exists($conexao, 'empresas')) {
    run_sql($conexao, "RENAME TABLE `expositores2024` TO `empresas`", "Renomeando tabela 'expositores2024' para 'empresas'");
} else {
    echo "  Tabela 'expositores2024' já renomeada ou tabela 'empresas' já existe.\n";
}

if (table_exists($conexao, 'empresas')) {
    // Se a coluna expositor_id ainda existe em empresas, renomeia para empresa_id
    if (column_exists($conexao, 'empresas', 'expositor_id')) {
        run_sql($conexao, "ALTER TABLE `empresas` CHANGE `expositor_id` `empresa_id` INT(11) NOT NULL AUTO_INCREMENT", "Renomeando chave primária expositor_id para empresa_id em 'empresas'");
    }
    
    // Adicionar novas colunas booleanas de atribuição em empresas se não existirem
    $colunas_atrib = ['is_expositor', 'is_promotor', 'is_parceiro', 'is_contratante'];
    foreach ($colunas_atrib as $col) {
        if (!column_exists($conexao, 'empresas', $col)) {
            run_sql($conexao, "ALTER TABLE `empresas` ADD `$col` TINYINT(1) NOT NULL DEFAULT 0", "Adicionando coluna '$col' na tabela 'empresas'");
        }
    }
} else {
    echo "  [ERRO] Tabela 'empresas' não encontrada. Impossível prosseguir com a migração das chaves estrangeiras.\n";
    mysqli_close($conexao);
    exit;
}

// 1. Tabela: contatos
echo "\n1. Ajustando tabela 'contatos'...\n";
if (column_exists($conexao, 'contatos', 'expositor_id')) {
    run_sql($conexao, "ALTER TABLE `contatos` CHANGE `expositor_id` `empresa_id` INT(11) NULL", "Renomeando coluna expositor_id para empresa_id em 'contatos'");
}
// Recria a FK apontando para empresas(empresa_id)
run_sql($conexao, "ALTER TABLE `contatos` ADD CONSTRAINT `contatos_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`) ON DELETE SET NULL", "Recriando FK em 'contatos' apontando para 'empresas(empresa_id)'");


// 2. Tabela: prospects
echo "\n2. Ajustando tabela 'prospects'...\n";
if (column_exists($conexao, 'prospects', 'expositor_id')) {
    run_sql($conexao, "ALTER TABLE `prospects` CHANGE `expositor_id` `empresa_id` INT(11) NOT NULL", "Renomeando coluna expositor_id para empresa_id em 'prospects'");
}
// Recria a FK apontando para empresas(empresa_id)
run_sql($conexao, "ALTER TABLE `prospects` ADD CONSTRAINT `prospects_ibfk_2` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`) ON DELETE CASCADE", "Recriando FK em 'prospects' apontando para 'empresas(empresa_id)'");


// 3. Tabela: expositores_telefone -> empresas_telefones
echo "\n3. Ajustando tabela 'expositores_telefone'...\n";
if (table_exists($conexao, 'expositores_telefone')) {
    run_sql($conexao, "RENAME TABLE `expositores_telefone` TO `empresas_telefones`", "Renomeando tabela 'expositores_telefone' para 'empresas_telefones'");
}
if (column_exists($conexao, 'empresas_telefones', 'expositor_id')) {
    run_sql($conexao, "ALTER TABLE `empresas_telefones` CHANGE `expositor_id` `empresa_id` INT(11) NULL", "Renomeando coluna expositor_id para empresa_id em 'empresas_telefones'");
}


// 4. Tabela: expositor_evento -> empresa_evento
echo "\n4. Ajustando tabela 'expositor_evento'...\n";
if (table_exists($conexao, 'expositor_evento')) {
    run_sql($conexao, "RENAME TABLE `expositor_evento` TO `empresa_evento`", "Renomeando tabela 'expositor_evento' para 'empresa_evento'");
}
if (column_exists($conexao, 'empresa_evento', 'expositor_id')) {
    run_sql($conexao, "ALTER TABLE `empresa_evento` CHANGE `expositor_id` `empresa_id` INT(11) NOT NULL", "Renomeando coluna expositor_id para empresa_id em 'empresa_evento'");
}


// 5. Tabela: eventos_marcados
echo "\n5. Ajustando tabela 'eventos_marcados'...\n";
if (column_exists($conexao, 'eventos_marcados', 'expositor_id')) {
    run_sql($conexao, "ALTER TABLE `eventos_marcados` CHANGE `expositor_id` `empresa_id` INT(11) NULL", "Renomeando coluna expositor_id para empresa_id em 'eventos_marcados'");
}


// 6. Tabela: empresas - Adicionando usuario_id
echo "\n6. Vinculando tabela 'empresas' com 'usuarios'...\n";
if (!column_exists($conexao, 'empresas', 'usuario_id')) {
    run_sql($conexao, "ALTER TABLE `empresas` ADD `usuario_id` INT NULL AFTER `empresa_id`", "Adicionando coluna usuario_id na tabela 'empresas'");
    run_sql($conexao, "ALTER TABLE `empresas` ADD CONSTRAINT `fk_empresas_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_ID`) ON DELETE SET NULL", "Adicionando chave estrangeira fk_empresas_usuarios");
} else {
    echo "  Coluna usuario_id já existe na tabela 'empresas'.\n";
}

mysqli_close($conexao);
echo "\n=== MIGRAÇÃO CONCLUÍDA ===\n";
echo "</pre>";
?>

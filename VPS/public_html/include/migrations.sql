-- Migrações de Banco de Dados para o Projeto AME
-- Executadas em 24 de agosto de 2025

-- PASSO 1: Adiciona a coluna para diferenciar eventos de Atendimento, Curso ou Reunião.
-- Permite que a lógica de rodízio seja aplicada apenas a eventos de atendimento.
ALTER TABLE `eventos_marcados`
ADD `tipo_evento` ENUM('atendimento', 'curso', 'reuniao') NOT NULL DEFAULT 'atendimento'
AFTER `local`;

-- PASSO 2: Adiciona a coluna para controlar o ciclo de vida de um evento.
-- Permite que eventos cancelados sejam ocultados e o processamento do rodízio
-- seja atrelado a eventos 'realizados'.
ALTER TABLE `eventos_marcados`
ADD `status_evento` ENUM('agendado', 'realizado', 'cancelado') NOT NULL DEFAULT 'realizado'
AFTER `tipo_evento`;

-- PASSO 3: Adiciona a coluna para o identificador único e não sequencial (UUID).
-- Aumenta a segurança ao impedir que URLs de eventos sejam adivinhadas.
ALTER TABLE `eventos_marcados`
ADD `uuid` CHAR(36) NULL DEFAULT NULL,
ADD UNIQUE (`uuid`);

-- PASSO 4: Adiciona a coluna para controlar se o rodízio de um evento já foi processado.
-- Evita que o mesmo evento seja processado múltiplas vezes.
ALTER TABLE `eventos_marcados`
ADD `rodizio_processado` TINYINT(1) NOT NULL DEFAULT 0
AFTER `status_evento`;

-- NOTA IMPORTANTE PÓS-MIGRAÇÃO:
-- Após executar os 4 comandos ALTER TABLE no servidor de produção,
-- é necessário executar o script 'backfill_uuid.php' UMA VEZ para gerar
-- os UUIDs para os registros de eventos existentes.
-- Lembre-se de apagar o script 'backfill_uuid.php' do servidor de produção após o uso.

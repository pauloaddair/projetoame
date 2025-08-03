# Detalhamento da Funcionalidade de Cadastro de Eventos

Este documento descreve a implementação do sistema de cadastro e gerenciamento de eventos, horários e expositores.

## 1. Visão Geral do Fluxo

O objetivo é permitir que um administrador crie um novo evento, associe uma imagem a ele (seja uma existente ou uma nova via upload) e, em seguida, detalhe os horários de trabalho para aquele evento, associando cada turno a um expositor específico.

O fluxo de trabalho do usuário é o seguinte:

1.  Acessar `pages/novoevento.php` para iniciar o cadastro.
2.  Preencher os dados do evento, como nome, local, datas e observações.
3.  Escolher uma imagem de evento já existente na base de dados OU fazer o upload de um novo arquivo de imagem.
4.  Ao submeter, o evento é criado e o usuário é redirecionado para a página de gerenciamento.
5.  Na página `pages/gerenciar_evento.php`, o usuário adiciona os horários (turnos), especificando vagas, tipo de atividade e o expositor responsável.
6.  A busca por expositores é feita dinamicamente via AJAX para não sobrecarregar a página.

## 2. Arquivos Criados

### a) `pages/novoevento.php`

*   **Responsabilidade:** Criar o registro principal do evento na tabela `eventos_marcados`.
*   **Funcionalidades:**
    *   Formulário para inserção dos dados do evento.
    *   Lógica para upload de novas imagens: O arquivo é salvo no diretório `/img/` e o caminho é inserido na tabela `imagens`.
    *   Dropdown para selecionar uma imagem já existente.
    *   Após a criação bem-sucedida do evento, redireciona o usuário para `gerenciar_evento.php`, passando o ID do novo evento via URL.

### b) `pages/gerenciar_evento.php`

*   **Responsabilidade:** Gerenciar os detalhes de um evento específico, principalmente seus horários.
*   **Funcionalidades:**
    *   Recebe o ID do evento via GET (`?id=...`).
    *   Exibe os dados principais do evento que está sendo gerenciado.
    *   Formulário para adicionar registros na tabela `horarios`, associando-os ao evento e a um expositor.
    *   **Busca de Expositores com Autocomplete:** Utiliza JavaScript (jQuery UI Autocomplete) para fazer chamadas AJAX ao `buscar_expositores.php`.
    *   Lista os horários já cadastrados para o evento, exibindo os detalhes de cada um.

### c) `pages/buscar_expositores.php`

*   **Responsabilidade:** Servir como um endpoint AJAX para a busca de expositores.
*   **Funcionalidades:**
    *   Recebe um termo de busca via GET (`?term=...`).
    *   Consulta a tabela `expositores2024` usando `LIKE`.
    *   Retorna os resultados em formato JSON, que é consumido pelo script de autocomplete na página `gerenciar_evento.php`.

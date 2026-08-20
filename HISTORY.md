---
tags:
  - "#projeto/ame"
  - "#modulo/crm"
  - "#modulo/portal-responsavel"
  - "#modulo/web-to-print"
  - "#infra/vps1"
  - "#infra/vps2"
  - "#infra/vps3"
  - "#tipo/historico"
relacionados:
  - "[[GEMINI]]"
  - "[[REGRAS_DE_NEGOCIO]]"
  - "[[PRINTADVISOR]]"
  - "[[AUTOMACAO]]"
---

# Histórico de Trabalho - Projeto AME

- **20/08/2026 - Ativação de Rotas e Correção da Landing Page:**
    - **Registro de Rotas no Front Controller:** Mapeadas as rotas `/atestados` e `/gerar_atestado` no front-controller `index.php` para possibilitar a emissão e visualização de comprovantes em PDF.
    - **Correção de Redirecionamento da Raiz:** Ajustado o roteamento do root (`/` e `/index`) para redirecionar diretamente para o blog WordPress (`/home/`), corrigindo o erro de carregamento de constantes duplicadas do legado `conexao_vb.php`.
    - **Verificação dos Recursos do CONARH 2026:** Confirmada a presença de `conarh2026.sql` (evento cadastrado em banco) e dos arquivos `atestados.php` e `gerar_atestado.php` no diretório de páginas.

- **17/08/2026 - Expansão do Portal do Responsável (/painel), Wizard Enriquecido e Vitrine Pública (/atividades):**
    - **Portal do Responsável (/painel):** Ativado alias e roteamento `/painel` no front-controller `index.php` apontando diretamente para `pages/meuperfil.php`.
    - **Wizard de 5 Passos:** Enriquecido o fluxo do perfil com novas seções:
      - *Saúde Avançada & Cuidados:* Adicionados campos dedicados para *Medicação continuada (nomes e dosagens)*, *Horários e instruções de medicação*, *Cuidados especiais/sensoriais*, *Restrições alimentares* e *Orientações gerais*.
      - *Cursos Externos & Qualificações:* Campo estruturado para cadastrar cursos e oficinas fora do AME.
    - **Currículo Dinâmico (PDF & HTML):** Atualizado `pages/curriculo.php` para incorporar automaticamente os cursos externos preenchidos no Wizard tanto na pré-visualização HTML quanto na geração de PDF via FPDF.
    - **Álbum de Fotos Biométrica & Web-to-Print:** Adicionada galeria de fotos reconhecidas nos eventos oficiais pelo motor biométrico e documentada a visão de parceria Web-to-Print com a AlphaGraphics / PrintAdvisor no `REGRAS_DE_NEGOCIO.md`.
    - **Vitrine Pública de Atividades (/atividades):** Reformulada a página `pages/atividades.php` para servir como showcase público institucional de eventos realizados e futuros, com métricas de impacto e CTAs para contratação de equipes via rodízio transparente.

- **11/08/2026 - Arquitetura de UX & Mapeamento Impeccable do Portal do Responsável:**
            - **Integração do Cartão Virtual Contacte.me:** Adicionada a integração do cartão digital público contacte.me/projetoame/{candidato_id} no topo do Painel do Responsável, na geração de QR Code para crachás físicos de feiras e no cabeçalho do Currículo Inclusivo.
    - **Módulo GED & Documentos Obrigatórios:** Adicionada a especificação do Módulo 6 de Documentos e GED (ASO, Autorização de Imagem, Comprovantes de Pagamento/Ajuda de Custo e Laudos) com upload isolado em /docs/{candidato_id}/ e controle automatizado de validade do ASO com disparos pelo n8n.
    - **Definição da Arquitetura UX (docs/UX_PAINEL_DO_RESPONSAVEL_IMPECCABLE.md):** Mapeados os 5 módulos centrais do Painel do Responsável: Acompanhamento de Atividades (/atendentes), Histórico Retroativo & Emissão de Comprovantes/Certificados PDF, Galeria de Fotos Biométrica (Foto_analise), Gerador de Currículo Inclusivo (/curriculo/{id}) e Gestão de Multi-Dependentes/Co-Responsáveis.
    - **Padrões de Design Impeccable:** Definida a paleta da marca (Azul AME #1e3a8a, Amarelo #d97706, Off-White #f8fafc), atrito cognitivo mínimo para acesso via mobile/WhatsApp e área de toque de 48px+.
    - **Saneamento Institucional do Mandato 2024-2026:** Atas da AGO de 04/10/2024 localizadas e organizadas em F:\OneDrive\Projeto A.M.E\01_Institucional_e_Legal\ATA E ESTATUTO ATUAL\. Gerados os documentos REQUERIMENTO DE AVERBACAO - 6 RTDPJ e TERMO DE DOACAO E CESSAO DE DIREITOS AUTORAIS - LIVROS AME em Markdown e DOCX via Pandoc.

# HistÃ³rico de Trabalho - Projeto AME

- **12/07/2026 - Ativação do Currículo do Candidato (OIDC & PDF):**
    - **Mapeamento de Parâmetros:** Atualizada a página `pages/curriculo.php` para aceitar o ID do candidato via parâmetro de URL (ex: `/curriculo/024`), mapeando automaticamente para a visualização correspondente sem precisar selecionar no menu.
    - **Link de Acesso:** Adicionado o link dinâmico para visualização do currículo na barra de navegação/breadcrumbs da página `pages/editacandidato.php`.
    - **Exportação FPDF:** Validada a geração do PDF via FPDF para o perfil do atendente com base no e-mail, telefone, dados de responsáveis e qualificações.


- **12/07/2026 - Customização Avançada do Currículo (Apresentação, Idade & Ocultação de Dados Sensíveis):**
    - **Apresentação do Perfil:** Inserido um bloco dinâmico de apresentação ("Perfil Profissional") na visualização e no PDF (FPDF) para atendentes ativos (`ativo = 1`), destacando os cursos obrigatórios realizados (Curso Básico Comportamental, Prático em Evento Real, Finanças para Vida).
    - **Cálculo Dinâmico de Idade:** Implementada a conversão de `candidatos.Nascimento` para cálculo de idade automática (exibida ao lado da data de nascimento).
    - **Ocultação de Dados Privados:** Removidas as informações de chave PIX e o bloco de Observações/Mensagem interna (`candidatos.mensagem`) tanto do HTML quanto da geração do PDF para garantir privacidade.


- **12/07/2026 - Correção de Layout no PDF (Evitando Sobreposição de Imagem):**
    - Ajustado o cursor de escrita vertical do FPDF para que, se a foto de perfil do atendente existir (que ocupa de Y=10 a Y=50 no canto superior direito), a coordenada Y inicial do restante do texto seja forçada para `Y=52` (abaixo da foto). Isso impede que o parágrafo de Perfil Profissional seja desenhado sobreposto à imagem.


- **12/07/2026 - Migração da Escala de Março/2026 (EXPOPRINT) via Planilha Excel:**
    - Analisada a planilha `AME_ESCALA_MARÇO_2026.xlsx` copiada pelo usuário.
    - Utilizado script Python (`openpyxl`) para extrair as marcações de escalamento pintadas em vermelho (`FFFF0000`) nas colunas 16, 17, 18 e 19 (correspondentes aos turnos da EXPOPRINT: Horarios 83, 84, 86 e 87).
    - Executada a migração dos dados no banco de dados de produção (VPS1) e local, atualizando o status de 6 atendentes para `escalado = 1` nas respectivas tabelas de disponibilidade.


- **12/07/2026 - Migração Completa de Todas as Escalas da Planilha de Março/2026:**
    - Estendida a migração do arquivo `AME_ESCALA_MARÇO_2026.xlsx` para varrer todas as colunas de atividades (como *Make a Difference*, *Agentes da Transformação*, *Cursos Senai*, *EXPOPRINT*, *SBT*, *Curso de Automaquiagem*).
    - Executado o mapeamento completo e atualizado o status de todos os atendentes escalados (células vermelhas) para `escalado = 1` no banco de dados de produção (VPS1). 12 registros atualizados e 4 novas inserções de disponibilidade.


- **12/07/2026 - Importação e Migração em Lote de Planilhas Históricas (2024-2025):**
    - Desenvolvido script de migração em lote (`migrate_historic_sheets.py`) capaz de varrer todas as abas das planilhas `AME_ESCALA_SETEMBRO 2024.xlsx`, `AME_ESCALA_AGOSTO_2025.xlsx` e `Planilha de Controle JULHO 2025.xlsx`.
    - O script mapeia os nomes de colunas a novos registros de eventos e turnos, além de relacionar os atendentes cadastrados no banco de dados.
    - Executado o deploy e migração no banco de dados de produção (VPS1) com sucesso.
    - **Total inserido/atualizado no VPS1:** 25 eventos criados, 74 turnos cadastrados e 687 registros de disponibilidade/escalados importados.


- **12/07/2026 - Migração Histórica Legada (2012-2022):**
    - Executado script de migração em lote para importar dados de participação antigos da pasta `/planilhas_antigas`.
    - Cadastrados eventos como *50º COBEM (2012)*, *Salão Duas Rodas (2017)*, *FESPA (2021)*, *FCE-Cosmetique (2019)*, *Green Nations (2018)* e *Dr. Zan (2019)* com suas respectivas datas e horários reais informados pelo usuário.
    - **Total migrado para a base de produção (VPS1):** 63 novas participações vinculadas com escala e 114 atualizações efetuadas com sucesso.


- **12/07/2026 - Atualização e Deploy da Página de Currículo (Preview/PDF):**
    - Modificado o arquivo [curriculo.php](file:///F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/pages/curriculo.php) para exibir a nova seção de "Atuação no Projeto A.M.E. (Histórico)".
    - A seção realiza queries dinâmicas na tabela de disponibilidade filtrando pelo `candidato_id` e status `escalado = 1`, ordenando cronologicamente de forma decrescente.
    - Atualizados tanto o preview HTML quanto a geração de PDF correspondente (via FPDF).
    - Deploy concluído com sucesso na produção (VPS1).


- **12/07/2026 - Criação e Deploy da Página Institucional /nossaatuacao:**
    - Criada a página [nossaatuacao.php](file:///F:/01_Projetos/Ativos/PROJETO_AME/VPS/public_html/pages/nossaatuacao.php) servida publicamente sob a rota `/nossaatuacao`.
    - A página calcula dinamicamente indicadores de impacto institucional obtidos do banco de dados: número de eventos reais de trabalho, número de cursos/capacitações, total de horas de atendimento prestadas (soma das escalas dos atendentes), atendentes ativos, total de cadastrados e a data inicial de atuação.
    - Exibe um painel elegante com cartões coloridos em degradê, uma listagem das feiras de grande porte recentes e um resumo institucional para apresentação corporativa de vendas do AME.
    - Concluído o deploy na produção (VPS1).


- **12/07/2026 - Ajustes de Layout e CTA da Página Institucional /nossaatuacao:**
    - Corrigido o cálculo de Cursos vs Eventos na página de atuação aplicando filtros textuais inteligentes sobre o nome dos eventos (uma vez que os metadados do banco estão uniformizados).
    - Ajustado o empilhamento das seções para dispositivos móveis via classes de ordenação responsiva do Bootstrap 4 (`order-1` e `order-2`), fazendo a coluna de trajetória aparecer acima da listagem histórica de eventos.
    - Adicionado painel Call To Action (CTA) com botões em degradê para doações (*cafezinho.social*) e contatos (*contacte.me*), com o rótulo "Entre em contato".
    - Deploy atualizado concluído com sucesso em produção no VPS1.

- **28/07/2026 - Cadastro de Evento e Revisão de Schema:**
    - Verificada a estabilidade do sistema CRM/Dashboard e a correta configuração da tela de atendentes.
    - Criado e executado script SQL para cadastrar diretamente no servidor VPS1 o evento CONARH 2026.
    - Identificada divergência de schema entre o banco de desenvolvimento local (F:) e o banco de produção VPS1 nas tabelas `eventos_marcados` (coluna `empresa_id` ausente no VPS1, coluna `obs` exigida) e `horarios`.
    - Anotado no GEMINI.md como próximo passo a sincronização e atualização estrutural do banco na VPS1.

## [2026-07-30] - Implementao de Rotas Pblicas ESG e Gerador de Certificados
- Criadas rotas amigveis pblicos de eventos em index.php: /{ano}/{slug} (ex: /2026/conarh), /eventos e /eventos/{ano}.
- Desenvolvido o gerador de Certificados em PDF em formato Paisagem A4 (layout AlphaGraphics) pr-preenchido via FPDF em pages/gerarcertificados.php.
- Criada coluna link_artigo em eventos_marcados para associar o post do blog  biometria facial e galeria de fotos.
- Corrigidos mapeamentos de colunas no BD de produo VPS1 (candidato_id, horario_id, tividade_id, imagem_id).

## [2026-07-30] - Correo de Slugs e Normalizao de Acentos
- Adicionada coluna indexada slug na tabela eventos_marcados no BD de produo.
- Normalizados os 71 eventos existentes convertendo caracteres acentuados ( -> ,  -> , etc.).
- Resolvido o erro de 'Evento no encontrado' para links com acentos como niversario-henri-zylberstajn.

## [2026-07-30] - Adio de Breadcrumbs de Navegao
- Adicionado componente de Breadcrumbs em eventopublico.php e eventosgaleria.php.
- Estrutura de navegao: Incio > Eventos > Ano > Nome do Evento.

## [2026-07-30] - Condicionamento de Mensagem de Dvidas nos Eventos
- Mensagem de orientao aos pais no WhatsApp (Projeto AME Capacitao) configurada para ser exibida exclusivamente em eventos futuros/ativos (inal >= hoje).
- Ocultada a mensagem em eventos passados (ex: 2025).

## [2026-07-30] - Adio do Widget do Carto Virtual
- Adicionado o banner interativo do Carto Virtual (Contacte.me) no rodap de eventopublico.php e eventosgaleria.php.
- Link de direcionamento: https://contacte.me/projetoame (abertura em nova aba).

## [2026-07-30] - Adio da Rota Pblica de Matriz de Escalas (/escala?evento_id=X)
- Criada a rota pblica e sem senha https://projetoame.org/escala?evento_id=X em pages/escala.php.
- Exibe a Matriz de Rodzio e Disponibilidade em formato read-only (sem switches de edio), deixando transparecer o critrio de precedncia.
- Destaca em verde e com badge [ESCALADO] os atendentes quando a escala  fechada.
- Redireciona automaticamente para a pgina institucional em /{ano}/{slug} caso o evento j tenha ocorrido.

## [2026-07-30] - Atualizao de Aviso de Dvidas na Matriz de Escala
- Adicionado banner de orientao em destaque no topo da tabela e no rodap de pages/escala.php para direcionar dvidas sobre o rodzio ao grupo 'Projeto AME Capacitao'.

## [2026-07-30] - Proteo Contra Spam e Moderao de Inscries
- Desativado o candidato bot oqjffddvik (ID 242) no banco de dados de produo (tivo = 0).
- Atualizada a rotina de cadastro (inscrever.php): novas inscries entram por padro com tivo = 0 (Inativo / Pendente de aprovao) para evitar polurem a lista de atendentes ativos.
- Adicionados botes de Ao Rpida no e-mail recebido pelo administrador para 'Aprovar e Ativar' ou 'Excluir (Spam)'.
- Criada a tela pages/adminativarcandidato.php para aprovao direta.
- Ajustada a consulta de escalas pblicas para exibir apenas atendentes ativos (tivo = 1).

## [2026-07-30] - Padronizao do Esquema de Status e Rodzio
- Documentada a especificao de status: candidatos.ativo = -1 (Inativo/Spam),   (Treinamento), 1 (Ativo).
- Ajustado o cadastro (inscrever.php) para gravar tivo = -1 por padro para moderao prvia.
- Atualizado registro do bot oqjffddvik para tivo = -1.

## [2026-07-30] - Documentao Completa do Algoritmo de Fila e Rodzio
- Gravada a lgica completa do algoritmo de rodzio: convocao por menor 
odizio, preservao da posio na promoo de tivo = 0 para tivo = 1, e atualizao para 
odizio = MAX(rodizio) + 1 ao escalar.
- Atualizados REGRAS_DE_NEGOCIO.md, GUIA_PROCEDIMENTOS_OPERACIONAIS_EVENTOS.md e verso .docx via Pandoc.

## [2026-07-30] - Atualizao da Regra de Ingresso no Rodzio
- Adicionada a regra de atribuio 
odizio = MAX(rodizio) + 1 no ato da inscrio inicial do candidato.
- Atualizados REGRAS_DE_NEGOCIO.md, GUIA_PROCEDIMENTOS_OPERACIONAIS_EVENTOS.md e verso .docx via Pandoc.

- **31/07/2026 - Atualização da Ordem de Rodízio e Status via Planilha Julho/2026:**
    - Processado o arquivo de controle `AME_ESCALA_JULHO_2026.xlsx` presente na pasta apoio do projeto.
    - Mapeados todos os 53 candidatos da planilha contra os registros da base de dados (produção VPS1 e local).
    - Atualizada a sequência da coluna `rodizio` (1 a 54) conforme a ordem exata da planilha.
    - Atualizado o status `ativo`: candidatos em rodízio com `ativo = 1` e candidatos em treinamento com `ativo = 0`.
    - Inativados (`ativo = -1`) os 12 candidatos que constavam ativos no banco mas não estavam na planilha de controle, sincronizando perfeitamente a visualização da página `/admin/escala?evento_id=71`.

- **31/07/2026 - Implementação do Fluxo em 2 Momentos e Filtro de Rodízio (<1) em /escala:**
    - Atualizada a página pública `/escala?evento_id=71` (escala.php) para alternar automaticamente entre o Momento 1 (Rodízio e Disponibilidade Declarada, com aviso explicativo) e o Momento 2 (Escala Definitiva com selo de ESCALADO).
    - Aplicado o filtro `c.rodizio >= 1` impedindo que candidatos com rodízio zerado/negativo apareçam na matriz pública.
    - Atualizada a API (api_escala.php) para marcar automaticamente `escala_fechada = 1` ao salvar a escala no admin.
    - Realizado o deploy das alterações para a produção (VPS1).

- **31/07/2026 - Correção da Exibição do Selo de Disponibilidade (SIM) em /escala:**
    - Corrigido o carregamento de ícones na página pública `/escala`, adicionando suporte nativo ao FontAwesome e Bootstrap Icons CDN.
    - Atualizado o renderizador de células de disponibilidade para exibir uma pílula verde visível com ícone e texto `✔ SIM`.
    - Validado o correto funcionamento via teste HTTP em produção na VPS1.

- **31/07/2026 - Implementação de Meta Tags OpenGraph Dinâmicas para Compartilhamento em Redes/WhatsApp:**
    - Registrada diretriz no GEMINI.md para futura integração de envio direto via WhatsApp no painel admin.
    - Atualizado o front controller (index.php) e o cabeçalho (html_head.php) para gerar tags OpenGraph (og:title, og:description, og:image, og:url) e Twitter Cards dinamicamente em /escala.
    - Ao compartilhar a URL do evento em grupos de WhatsApp, o preview agora renderiza com a imagem do evento (ex: /img/conarh.jpg), título do momento e detalhes de período e local.
    - Deploy e testes HTTP concluídos com sucesso em produção na VPS1.

- **31/07/2026 - Definição da Engine de Comunicação WhatsApp (Evolution GO / NETGO1200):**
    - Registrada formalmente no REGRAS_DE_NEGOCIO.md a infraestrutura de disparo do WhatsApp.
    - O Projeto AME utilizará a engine **Evolution GO** na VPS2 (https://evogo.netmailing.com.br) conectada via instância **NETGO1200** ao celular oficial da associação.

- **01/08/2026 - Implementação da Ficha de Credenciamento para o Contratante:**
    - Criada a funcionalidade de exportação de dados para credenciamento no painel de administração da escala (/admin/escala).
    - Adicionado o botão 'Ficha de Credenciamento' que abre um modal responsivo com a tabela contendo: Nome Completo, Data de Nascimento (formatada DD/MM/AAAA), RG, CPF e Tamanho da Camiseta.
    - A lista inclui obrigatoriamente a Coordenação (Paulo Addair e Regina Justo) no topo, acompanhados de todos os atendentes escalados.
    - Implementados botões de ação instantânea: 'Copiar Texto (WhatsApp / E-mail)' e 'Copiar Tabela (para Excel)' com formatação tabulada (TSV).
    - Roteador index.php atualizado para dar suporte direto a endpoints de API na pasta /include/.
    - Deploy e testes validados com sucesso em produção na VPS1.

- **09/08/2026 - Correção na Ordenação da Escala Pública (/escala):**
    - Atualizada a consulta SQL dos candidatos em pages/escala.php para ordenar primeiro por status (c.ativo DESC, priorizando atendentes com ativo=1 sobre treinandos com ativo=0) e em seguida por rodízio (c.rodizio ASC).
    - Alinhado o comportamento da escala pública (/escala) com o painel administrativo (include/api_escala.php).


- **09/08/2026 - Proteção do Rodízio na Fase de Pré-Escala (Preservação de Fila):**
    - Eliminada a atualização prematura do campo candidatos.rodizio nas rotas de salvamento da escala (include/api_escala.php e pages/adminescala.php).
    - O salvamento da escala agora registra apenas a marcação de disponibilidade e o status do evento, mantendo a sequência exata e numeração ordinal do rodízio antes do evento.
    - Ajustados no banco de dados os valores de rodízio dos atendentes escalados recentemente para que reapareçam no topo na sua posição ordinal original (posições 1, 2, 3, 4...).
    - O deslocamento da fila (rodizio = MAX + 1) permanece restrito ao pós-evento via processar_rodizio.php, evitando qualquer ruído com as famílias e prevenindo prejuízos em caso de ausências por imprevistos ou doenças.
    - Deploy e validação HTTP concluídos com sucesso no VPS1.


- **09/08/2026 - Integração de Avaliações e Envio de Fichas ao Contratante via WhatsApp:**
    - Adicionado botão 'Avaliar Atendente' no modal de presenças (/admin/escala) permitindo ao coordenador/monitor preencher a avaliação individual de cada atendente.
    - Criada a funcionalidade 'Enviar Avaliação (Contratante)' no painel admin, gerando a mensagem formatada para o contratante com o link da página pública de avaliação (projetoame.org/avaliacao/{evento_uuid}).
    - Implementados botões de envio direto via WhatsApp, cópia rápida de mensagem e validação de telefone.
    - Deploy e testes de sintaxe concluídos com sucesso no VPS1.

---

## 🔗 Conexões & Ecossistema
- **Servidores & Infraestrutura:** [[VPS1_Hostinger]], [[VPS2_NASANET]], [[VPS3_Inferencia]].
- **Projetos Relacionados:** [[PRINTADVISOR]], [[AUTOMACAO]], [[contacteme]], [[cafezinhosocial]].
- **Documentação Local:** [[GEMINI.md]], [[REGRAS_DE_NEGOCIO.md]], [[docs/UX_PAINEL_DO_RESPONSAVEL_IMPECCABLE.md]].



---
project: PROJETO_AME
tags:
  - "#projeto/ame"
  - "#modulo/crm"
  - "#modulo/biometria"
  - "#modulo/portal-responsavel"
  - "#modulo/web-to-print"
  - "#infra/vps1"
  - "#infra/vps2"
  - "#infra/vps3"
  - "#tipo/diretrizes"
relacionados:
  - "[[PRINTADVISOR]]"
  - "[[AUTOMACAO]]"
  - "[[contacteme]]"
  - "[[cafezinhosocial]]"
  - "[[REGRAS_DE_NEGOCIO]]"
skills:
  - writing-skills
  - frontend-design
  - react-patterns
  - copywriting
  - laravel-expert
  - clean-code
  - security-review
---

## ⚡ Quick Start Card

> Leia **apenas este bloco** para iniciar qualquer tarefa.
> O restante deste arquivo é referência aprofundada — leia sob demanda.
> Para deploy/automação/IA: consulte também `F:\01_Projetos\INFRA.md`.

| Chave           | Valor                                                          |
|-----------------|----------------------------------------------------------------|
| **Stack**       | PHP 8.x / MariaDB / Bootstrap 4 / FPDF / PHPMailer            |
| **Framework**   | Vanilla PHP — Front Controller em `index.php`                  |
| **MVP Status**  | ✅ **LIVE** — `projetoame.org`                                 |
| **Job ID**      | `item_17`                                                      |
| **AGO 2026**    | 🏛️ **Adiada para 03/10/2026** (fim do mandato: **17/10/2026**) — **edital a publicar até 25/09/2026** |
| **Governança**  | Estatuto integral (38 arts.) em `ESTATUTO_VIGENTE_TEXTO_INTEGRAL.md` · reforma proposta em `ANALISE_ESTATUTO_E_REFORMA_2026.md` · **handoff/retomada em `RELATORIO_ENCERRAMENTO_GOVERNANCA_15SET2026.md`** |
| **Domínios**    | `projetoame.org`, `abiat.org.br`, `projetoame.ong.br`, `projetoame.org.br` |

### 🖥️ Ambiente Local (SOHO)
| Campo         | Valor                                                                   |
|---------------|-------------------------------------------------------------------------|
| Raiz local    | `F:\01_Projetos\Ativos\PROJETO_AME\VPS\public_html\`                    |
| URL local     | `http://localhost/projetoame` *(ou vhost configurado via vhosts_manager)*|
| DB host       | `localhost` · schema: `projetoame` · XAMPP MySQL (sem senha)            |
| Conexão DB    | `include/conexao.php`                                                   |

### 🌐 Ambiente de Produção (VPS1)
| Campo          | Valor                                                    |
|----------------|----------------------------------------------------------|
| SSH            | `ssh vps1.novaera`                                       |
| Raiz VPS       | `/home/projetoame/public_html/`                          |
| DB host        | `localhost` · schema: `projetoame`                       |
| DB user/pass   | `projetoame` / `vp3imJizMOgWxbM`                        |
| Deploy         | `ssh vps1.novaera "cd /home/projetoame/public_html && git pull"` |
| Logs Apache    | `ssh vps1.novaera "tail -50 /home/projetoame/logs/error_log"`    |

### 🔑 Integrações Ativas
| Serviço           | Endpoint / Config                                          |
|-------------------|------------------------------------------------------------|
| Evolution API     | `https://evoapi.netmailing.com.br` · inst: `ProjetoAME`   |
| LiteLLM Pool      | `hermes-local` via `llm.netmailing.com.br`                 |
| PHPMailer SMTP    | `mail.projetoame.org:587` · user: `noreply@projetoame.org` |
| PHPMailer Pass    | `PittJusto@3802`                                           |

### 📂 Arquivos-Chave
| Arquivo                         | Função                                       |
|---------------------------------|----------------------------------------------|
| `index.php`                     | Front Controller — todas as rotas            |
| `include/conexao.php`           | Credenciais e conexão com o banco            |
| `include/funcoes.php`           | Helpers globais (`formataDataEventoBR` etc.) |
| `include/funcoes-eventos.php`   | Lógica de eventos, escala e presença         |
| `pages/atendentes.php`          | Disponibilidade pública de atendentes        |
| `pages/adminescala.php`         | Painel admin de escala (41 KB)               |
| `pages/adminindex.php`          | Dashboard administrativo                     |
| `pages/meuperfil.php`           | Portal do Associado (abas + dependentes)     |
| `pages/curriculo.php`           | Currículo inclusivo + geração PDF            |

---

# Projeto AME - CRM - Painel de Controle

## 0. Regras Críticas (MANDATÓRIO)
- **NUNCA EXCLUIR NADA (ARQUIVOS OU PASTAS) SEM AUTORIZAÇÃO CLARA E EXPLÍCITA DO USUÁRIO.**
- **GED Organizado:** Novos documentos devem ser salvos em `/docs/{candidato_id}/`.
- **Segurança:** Senhas SEMPRE via Bcrypt.

## 1. Gestão de Marca (INPI)
- **Status:** Depósito da marca **Projeto AME** solicitado pela primeira vez ao INPI via MP Marcas e Patentes (Maio/2026).

## 2. Meu Papel
Atuar como desenvolvedor full-stack e arquiteto de soluções inclusivas, auxiliando na evolução do sistema para uma plataforma de gestão de carreira e autonomia para associados do Projeto AME.

### Infraestrutura de Domínios
- **Domínio Principal:** `www.projetoame.org`
- **Aliases:** `abiat.org.br`, `projetoame.ong.br`, `projetoame.org.br`

## 2. Objetivo Atual
Implementar o **Portal do Associado**, a **Automação de Eventos** (atualização de datas) e a estabilização do **CRM/Dashboard Financeiro**, reduzindo o atrito de acesso e garantindo dados auditados.

## 3. Status Atual do Projeto (22 de Agosto de 2026)

**Módulos Operacionais e Atualizações Recentes:**
- **Filtros e Gestão de Candidatos/Casting (`/casting`):** Filtros rápidos por status (Ativos/Todos/Inativos), contadores dinâmicos, estilização para participantes inativos e links diretos para chamada telefônica (`tel:`), e-mail (`mailto:`) e WhatsApp.
- **Módulo de Currículo Inclusivo (`/curriculo/{id}`):** Visualização dinâmica e geração de PDF via FPDF sem conflitos de cabeçalho, com suporte a cursos externos.
- **Gestão de Atividades & Escalas (`/admin/atividades` e `/admin/escala`):** Resolução de permissões, padronização de CDNs oficiais, listagem de eventos com badges de status, cópia de ficha de avaliação e ficha de credenciamento formatada.
- **Fluxo de Presença & Ficha de Avaliação (`/avaliacao/{uuid}` e `api_escala.php`):** Tabela `presenca` integrada; liberação automática e destaque de formulários de avaliação após confirmação de comparecimento do atendente no evento.

**Governança Institucional (Mandato 2024-2026):**
- **Organização de Atas:** Resgatada a Ata da AGO de 04/10/2024 (eleição da diretoria 2024-2026) e centralizada em F:\OneDrive\Projeto A.M.E\01_Institucional_e_Legal\ATA E ESTATUTO ATUAL\.
- **Minutas Jurídicas Geradas:** Criados o REQUERIMENTO DE AVERBACAO - 6 RTDPJ e o TERMO DE DOACAO E CESSAO DE DIREITOS AUTORAIS - LIVROS AME (em .md e .docx via Pandoc).


**Infraestrutura e Rotas:**
- **Front Controller:** `index.php` refatorado e centralizado com suporte a URLs limpas.
- **Novas Rotas:** `/admin/evento/{id}`, `/meuperfil`, `/ativar-perfil/{token}`, `/trocafoto-usuario` e a nova rota de prospecção `/admin/prospeccao` operacionais.
- **Menu Lateral Retrátil:** Componente global de sidebar administrativa (`include/admin_sidebar.php` e `include/admin_sidebar_footer.php`) integrado em todas as páginas administrativas do site (`adminindex`, `adminatividades`, `adminatividade`, `adminescala`, `adminprospeccao`, `adminrodizio`, `adminextrato`, `adminexpositores`), com persistência de estado via cookie e paleta de cores alinhada ao AME.
- **Ficha de Avaliação (/avaliacao):** Geração automática de UUID para novos eventos adicionada. Criados botões de cópia rápida de URL com feedback visual na listagem de atividades (para eventos iniciados) e card de atalho na página de detalhes do evento. Tabela de avaliações recriada no banco de dados local com estrutura correta e UUIDs dos 35 eventos existentes preenchidos via script.
- **Ordenação da Escala Pública (/escala):** Ajustada a consulta SQL dos candidatos em `pages/escala.php` para ordenar com a mesma prioridade do painel administrativo (`ORDER BY c.ativo DESC, c.rodizio ASC`), garantindo que a página exiba primeiro os Atendentes e em seguida os Treinandos, ambos ordenados por rodízio.

**Portal do Associado & Uploads Isolados:**
- **Layout de Abas:** A página `/meuperfil` (`meuperfil.php`) foi dividida em abas: "Meus Dados" e "Dependentes / Associados", com fluxo unificado de edição e salvamento assíncrono via `api_usuario_save.php` e `api_perfil_save.php`.
- **Isolamento de Fotos:** Separada a alteração da foto do usuário (`trocafoto-usuario.php`) e do candidato (`trocafoto.php`), garantindo atualizações de imagens e registros em tabelas distintas de forma 100% isolada.
- **Apoio a Múltiplos Dependentes:** Se o usuário tiver mais de um dependente associado, a aba exibe um painel de seleção intermediário com botões de retorno ao painel de listagem e permite editar o Wizard do candidato selecionado via parâmetro `cand_id`.

**Contabilidade e Extrato:**
- **Saneamento e Conciliação:** Saldo local equalizado ao saldo real de R$ 3.582,40 (combinando PagSeguro R$ 29,50 e Nubank Caixinha R$ 3.552,90) após auditoria e inserção de 49 lançamentos omitidos de 2022. Ajuste relocalizado em 31/10/2022 para exatidão do histórico acumulado.
- **Visualização de Extrato:** Página `adminextrato.php` redesenhada para suportar visão histórica geral, anual e mensal com cálculo dinâmico de saldo anterior e segurança de rotas integrada.

**Autenticação e Estabilidade (Dashboard):**
- **Dashboard (/admin):** Redesenhado em layout de 3 colunas (Financeiro, Atividades, Prospecção). Resolvido problema de gráfico em branco e títulos/links absolutos quebrados em ambiente local (subpasta) via uso de `$app_web_root`.
- **Autenticação:** Corrigido o fluxo de login em `login.php` (substituindo mysqli_num_rows por mysqli_fetch_assoc) e corrigido o fluxo de logout em `logout.php` (resolvendo session_start redundante e cookie de usuario_id com path correto).
- **Vínculo de Usuário:** Corrigido bug de perfil do administrador (ID 1) que exibia incorretamente o nome de Valéria de Lúcia Bartolomei por causa de associação indevida de candidato no banco de dados.

**CRM & Prospecção Inteligente (IA + Web Search):**
- **Saneamento de CAPTCHAs:** Instalada a biblioteca Python `duckduckgo_search` para contornar o bloqueio HTTP 202 que ocorria em buscas a partir de requisições de desenvolvimento locais no Windows. Todos os motores de busca de feiras e expositores foram migrados para esta solução.
- **Busca de Expositores:** Criado script de varredura automatizada (`find_exhibitors.py` + `api_find_exhibitors.php`) que realiza buscas sobre expositores de um evento e utiliza o LiteLLM da VPS2 (ou Gemini) para extrair os leads estruturados em JSON, gravando-os em lote em `leads_expositores`.
- **Painel de Prospecção:** Implementada a página administrativa `/admin/prospeccao` (`pages/adminprospeccao.php`) com uma interface de CRM contendo cartões de estatísticas, formulário AJAX para novos eventos, gatilhos de IA individuais e em lote, e listagem expandida de leads com registro rápido de follow-up. O card de prospecção do dashboard foi conectado a este novo painel.
- **Ambiente Isolado (venv):** PHP adaptado para detectar e priorizar a execução via `include/venv/bin/python` local do projeto no VPS1, evitando instalações globais e quebras de empacotamento no Ubuntu 24.04.
- **Fallback Automático LiteLLM:** Scripts Python configurados para bypassar a API Key local e rotear de forma transparente para o Proxy do LiteLLM no VPS2 (`https://llm.netmailing.com.br`) caso nenhuma chave `GEMINI_API_KEY` esteja presente no `.env`.

**Inteligência Artificial (Foto Análise):**
- **Arquitetura (Fase 1):** Backend concluído em Python usando matemática pura (`face_recognition`) para extração de assinaturas biométricas. Scripts `enrollment.py` e `scan_events.py` prontos. Banco de dados estruturado localmente e preparado para VPS (`migrar_foto_analise.sql`).

- **Currículos de Atendentes & Histórico de Atuação:** 
  - ✅ Ativada a rota `/curriculo/{candidato_id}` no front controller do AME. Adicionado link de acesso no painel `/editacandidato/{id}` e suporte à geração de PDFs com FPDF.
  - ✅ Omitidas informações sensíveis de PIX e Observações internas (`mensagem`).
  - ✅ Inserido Perfil Profissional dinâmico com cursos padrão do AME para atendentes ativos, com cálculo automático de idade a partir do campo Nascimento.
  - ✅ Adicionada seção de "Histórico de Atuação no Projeto A.M.E." no preview HTML e na exportação em PDF, listando eventos escalados (`escalado = 1`).
  - ✅ Correção de encoding UTF-8 para ISO-8859-1 no gerador de PDF (eliminando caracteres quebrados como MendonÃ§a).
  - ✅ Deploy de todas as atualizações concluído na produção (VPS1).

- **Acessibilidade & VLibras (20/08/2026):**
  - ✅ Integrada a barra flutuante de acessibilidade (com controle persistente de Alto Contraste e Fonte Grande via `localStorage`).
  - ✅ Adicionado o widget oficial do VLibras em todo o site por meio do cabeçalho unificado.
  - ✅ Corrigido o erro de ordenação do DataTables nas listagens de atestados (respeitando a ordenação cronológica decrescente vinda do banco).

- **Impressão de Atestados & Assinatura (20/08/2026):**
  - ✅ Resolvido bug de codificação e esvaziamento do PDF (iconv redundante removido em `gerar_atestado.php`).
  - ✅ Integrada a assinatura digital do presidente no rodapé dos atestados (Paulo Addair Daniel Filho - Presidente).
  - ✅ Deploy e ativação das rotas concluídos com sucesso na produção no VPS1.

- **Gestão de Escala & Ficha de Credenciamento (/admin/escala — 02/09/2026):**
  - ✅ Autocura e equalização da tabela `presenca` no MariaDB da VPS1 (adicionadas as colunas `candidato_id`, `presente`, `data_confirmacao`, `confirmadopor` e índice único `idx_evento_candidato`).
  - ✅ Blindagem de exceções PDO/MySQLi em `include/api_escala.php` e `pages/avaliacao.php`, garantindo retorno 100% JSON e eliminando o erro de parser JavaScript.
  - ✅ Correção do fluxo de salvamento de escala (`salvar_escala`), com persistência do container de mensagens e upsert em `disponibilidade` para atendentes atribuídos manualmente.
  - ✅ Refatoração da Ficha de Credenciamento (`#credenciamentoModal`), com atualização dinâmica de badge/título, tabela completa de equipe (coordenadores + atendentes escalados) e botões de cópia para WhatsApp, Excel (TSV) e impressão.
  - ✅ Deploy concluído via SCP para VPS1 e validação em produção.

## 4. Problemas Pendentes / Próximos Passos

> 🏛️ **[ATUALIZAÇÃO 15/09/2026 — GOVERNANÇA É A PRIORIDADE ABSOLUTA]**
> A **AGO foi adiada de 26/09 para 03/10/2026** (mandato 2024–2026 encerra em **17/10/2026**).
> **Prazos duros:** ordem do dia e minutas até **23–24/09** · advogado contratado e revisão até
> **24/09** · **edital publicado (sítio + afixação na sede) até 25/09** · prestação de contas
> out/2024–set/2026 fechada até **30/09** · **AGO 03/10** · averbação no 6º RTDPJ até **02/11**.
> **Documentos de trabalho:** [[ESTATUTO_VIGENTE_TEXTO_INTEGRAL]] · [[ANALISE_ESTATUTO_E_REFORMA_2026]] ·
> **[[RELATORIO_ENCERRAMENTO_GOVERNANCA_15SET2026]] (handoff + roteiro de retomada — começar por aqui)** ·
> [[PARECER_CONVOCACAO_ASSEMBLEIA_26SET]] · [[PLANO_FORMALIZACAO_QUADRO_SOCIAL_E_GOVERNANCA]].
> **Bloqueios:** (1) **Livro de Associados inexistente** — precisa existir antes do edital (base do quórum);
> (2) **art. 15, §1º** — alteração de 2024 não averbada, Diretoria atual tem 1 não-Família em situação
> irregular; (3) **art. 15, §2º** — vedação de parentes na Diretoria provavelmente violada;
> (4) **edital da AGO de 04/10/2024 ausente**; (5) **Conselho Fiscal vago desde out/2024**;
> (6) **Conselho Consultivo nunca constituído** (o art. 14 dava-lhe a presidência da assembleia).

1. **Migração do CRM para o iController (Aprovado):**
   - Remover arquivos locais de CRM/prospecção (rota `/admin/prospeccao`, scripts python de scraping e tabela `leads_expositores`).
   - Desenvolver o webhook `/api/webhook/novo-evento` no Projeto AME para receber e cadastrar eventos/vagas do iController (com suporte a geolocalização: lat/lng).
2. **Ficha de Inscrição de Voluntário:** Desenvolver `/inscrever-voluntario` gerando registro em `usuarios` (com senha `NULL`), `voluntarios_dados` e associando na tabela pivot `usuarios_atribuicoes`.
3. **Ficha de Inscrição de Empresas Parceiras:** Desenvolver `/inscrever-empresa` registrando na tabela unificada `empresas` com flags booleanas de papéis.
4. **Ficha de Solicitação de Contratação:** Desenvolver `/contratar` direcionando diretamente como Leads no iController CRM.
5. **Interface de Atribuições no Admin:** Adaptar as telas de edição de usuários administrativas para gerenciar atribuições múltiplas.
6. **Corresponsável:** Finalizar a interface de "Convidar Responsável" no `/meuperfil`.
6. **Portfólio Visual (Foto Análise - Fase 2):** Criar script PHP para servir dados da nova tabela `fotos_reconhecidas` ao frontend React (Lovable).
7. **CRM (Fase 4):** Conectar os componentes React com as rotas protegidas por Sanctum do backend Laravel (`crm-api`).
8. **Comunicação:** Automatizar o envio da escala final para a FCEM (Jenifer).
9. **Integração ageNET (Visão Futura):** Integrar o CTA das comunicações de prospecção (e-mail/WhatsApp) com o ageNET, realizando a triagem e o atendimento conversacional inicial por meio de um agente inteligente.
10. **Integração de Biometria e Foto Análise (Visão Futura):** Comparar a foto de perfil com as milhares de fotos do blog do AME (`projetoame.org/home`) para associar cada atendente às postagens dos eventos, gerando grade curricular dinâmica e álbuns de fotos individualizados de forma automatizada.

## 5. Papéis da IA (Gemini)
- `Developer`: Foco em análise, escrita e depuração de código (PHP/MySQL, React/TypeScript).
- `Architect`: Desenho de fluxos de baixo atrito para famílias.
---
diretrizes_ok: true
11. **Sincronização de Banco de Dados Local vs VPS1:** Revisar as atualizações de schema necessárias na VPS1 (como a coluna 'empresa_id' em 'eventos_marcados') para garantir que o banco de dados de produção esteja 100% alinhado com o ambiente de desenvolvimento local.

12. **Integração de Comunicação Direta com WhatsApp:** Implementar envio de comunicados e links direto do painel administrativo para os grupos do WhatsApp (Capacitação, Voluntários, Diretoria, etc.) através do ageNET / Evolution API.

13. **Interface de Reordenação de Rodízio no Admin:** Implementar ferramenta de ajuste rápido e reordenação do rodízio dos candidatos no painel administrativo (Drag & Drop ou input numérico) para evitar necessidade de SQL manual em correções pontuais.

14. **Efetivação de Rodízio Pós-Atendimento (Refatoração Futura):** Modificar a lógica do rodízio para que a atualização do campo rodizio = MAX(rodizio) + 1 ocorra APENAS após a confirmação presencial de atendimento no evento (check-in/presença real no dia), evitando reordenar antecipadamente candidatos que venham a faltar ou ser substituídos.

15. **Migração do Histórico Cronológico de Rodízio:** Criar script de população retroativa da tabela historico_rodizio processando as planilhas históricas de eventos e registros do banco desde o primeiro evento do Projeto AME.

16. **Portfólio Impresso & Web-to-Print (Parceria AlphaGraphics):** Desenvolver módulo no `/painel` para montagem de portfólio físico/álbum de memórias com fotos da biometria e currículo integrado, conectando à esteira de impressão sob demanda via AlphaGraphics / PrintAdvisor.

---

## 🔗 Conexões & Ecossistema
- **Servidores & Infraestrutura:** [[VPS1_Hostinger]] (Produção Web / MariaDB), [[VPS2_NASANET]] (Evolution GO NETGO1200 / LiteLLM Gateway), [[VPS3_Inferencia]] (Ollama / InsightFace).
- **Projetos Integrados:** [[PRINTADVISOR]] (Web-to-Print / AlphaGraphics), [[AUTOMACAO]] (Orquestrador e relatórios Eda), [[contacteme]] (Crachás de Identificação), [[cafezinhosocial]] (Doações PIX).
- **Documentação do Módulo:** [[REGRAS_DE_NEGOCIO.md]], [[HISTORY.md]], [[docs/UX_PAINEL_DO_RESPONSAVEL_IMPECCABLE.md]].


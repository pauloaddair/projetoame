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

- **15/09/2026 - ENCERRAMENTO DA SESSÃO DE GOVERNANÇA — relatório de handoff e roteiro de retomada:**
    - **Contexto:** Paulo encerrou o atendimento solicitando atualização documental e um relatório das orientações. Criado `RELATORIO_ENCERRAMENTO_GOVERNANCA_15SET2026.md` (raiz do projeto, whitelist no `.gitignore`) consolidando: decisão de adiar a AGO para **03/10/2026** (edital até **25/09**); achados do estatuto; **sequência obrigatória de quórum** (instalar com base atual → reforma → Regimento Interno → **só então** admitir); parecer sobre a **ratificação** da ATA de 04/10/2024 e averbação conjunta; composição do **Conselho Consultivo** (Marisa Evangelista 2018–2020 + Paulo 2020–2024); **Bloco XI — gestão profissional remunerada (CLT)** com a restrição do **CEBAS art. 30**; os **11 blocos** da reforma; **14 perguntas em aberto**; **16 riscos**; artefatos produzidos; e **roteiro de retomada em 9 tarefas datadas**.
    - **Estado do repositório:** commits `bff0e68d` (análise integral do estatuto + reforma) e `3df35ac2` (Bloco XI + Conselho Consultivo) enviados para `origin/staging`. Working tree limpo.
    - **Próxima ação bloqueante (prazo duro):** **Livro de Associados até 22/09** → **minuta do edital + advogado até 24/09** → **publicar o edital em 25/09**.
    - **Cópia do relatório em `apoio`:** `F:\01_Projetos\apoio\2026-09-15 — Encerramento Sessão Governança AME.md`.

- **15/09/2026 - Análise Integral do Estatuto + Reforma Estatutária Proposta (MUDANÇA DE RUMO: AGO adiada para início de outubro):**
    - **Mudança de rumo definida por Paulo:** a AGO **não** será em 26/09/2026. Foi **adiada para o início de outubro** (data proposta: **03/10/2026, sábado**), em razão do **fim do mandato 2024–2026 em 17/10/2026**. **Consequência imediata:** o prazo do edital (art. 14, §2º — 7 dias) **deixa de ser 19/09 e passa a ser 25/09/2026** para a data de 03/10. Os documentos `PARECER_CONVOCACAO_ASSEMBLEIA_26SET.md`, `PLANO_FORMALIZACAO_QUADRO_SOCIAL_E_GOVERNANCA.md` e `PLANO_ANALISE_COMPLIANCE_ASSEMBLEIA_26SET.md` continuam válidos no mérito, mas **as datas de 26/09 e os prazos retrocedidos estão superados**.
    - **Descoberta documental decisiva:** localizado o **estatuto completo com camada de texto OCR** em `F:\OneDrive\Projeto A.M.E\01_Institucional_e_Legal\ATA E ESTATUTO ATUAL\PROJETO AME - ESTATUTO - 20240626_181102_OCR.pdf` (6 págs., OCR Adobe Paper Capture, gerado em 15/09/2026). Isso **encerrou o bloqueio** de 09/09 (PDFs do estatuto eram scans sem texto) — extraídos **38 artigos integrais** via `pdftotext`. Achados que estavam pendentes: **art. 8º, §1º — "Votar é direito restrito aos associados família"**; **art. 15, §2º — veda membros de mesma família na Diretoria**; **art. 11, §3º — veda cargo público na Diretoria**; **arts. 10 §1º / 13 III-IV-V citam "Conselho de Administração"**, órgão que não existe no estatuto.
    - **Parecer sobre a ratificação da ATA de 04/10/2024:** **sim, a ratificação é o caminho correto e as duas atas devem ser averbadas no mesmo requerimento** — mas a ratificação **não retroage** e **não substitui o edital de 2024** (a ata de 2026 gera **título registrável novo**, com efeitos *ex nunc*, convalidando os atos de 2024). Estrutura de 6 documentos para requerimento único ao 6º RTDPJ (registro de origem nº 173.633) e cláusula de ratificação redigida para constar da ata.
    - **Achado estratégico de quórum:** como o **art. 8º, §1º restringe o voto aos Associados Família**, manter a AGO com o **quadro social ainda restrito aos fundadores** torna o quórum do art. 13, §1º **trivialmente alcançável** (5 Associados Família → 2 presentes para o mínimo de 30% → 2 votos para 50%+1), **inclusive para alteração estatutária**. Fixada a **ordem dos trabalhos obrigatória** para evitar circularidade: instalar → reforma estatutária → Regimento Interno → **só então** referendar as admissões.
    - **Extração da ATA de 04/10/2024 (texto integral do `.docx`):** 13 presentes, realizada em **2ª chamada às 19h30** (formato híbrido), presidida por Paulo Addair com Regina Justo como secretária; prestação de contas out/2022–set/2024 **aprovada por aclamação** (arrecadado R$ 77.956, gastos R$ 75.500, despesas R$ 10.500 → **saldo negativo de R$ 8.044**; campanha "UMA VAN" R$ 10.935 + rifa R$ 1.000; **saldo em caixa R$ 4.991,10**); alteração do art. 15, §1º (até 5 membros, mín. 4 Família + até 1 Honorário, vedado presidir) e eleição da Diretoria 2024–2026 — **sem qualquer menção a Conselho Fiscal ou Conselho Consultivo**. Lista nominal dos 13 presentes registrada.
    - **Reforma estatutária proposta (10 blocos, artigo por artigo):** I objeto/receita própria · II sede eletrônica e mudança de endereço · III quadro social (Fundadores, direitos, deveres, suspensão, exclusão) · IV **assembleia híbrida/eletrônica, voto por procuração, convocação por meios eletrônicos, mesa e fim do conflito Diretoria × Conselho Consultivo** · V Diretoria (composição, **atenuação da regra de parentesco**, cargos adicionais, assinaturas, conflito de interesses) · **VI artigo novo 4º-A — marca, propriedade intelectual, licenciamento, produtos (álbuns, figurinhas, mochilas, necessaires, cadernos, agendas) e uso de imagem** · VII Conselho Fiscal (parentesco, prazo de parecer, vacância) · VIII Conselho Consultivo (presidência, atribuições, até 15 membros, ex-presidentes) · IX LGPD/transparência/voluntariado/certificações · X prazos e disposições.
    - **Artefatos criados:** `ESTATUTO_VIGENTE_TEXTO_INTEGRAL.md` (38 artigos integralmente transcritos do OCR, para uso do advogado) e `ANALISE_ESTATUTO_E_REFORMA_2026.md` (parecer de ratificação, novo calendário da AGO, mapa artigo por artigo, 10 blocos de reforma com redação proposta, ordem do dia de 11 itens, 12 riscos e 10 perguntas de decisão) + cópia geral em `F:\01_Projetos\apoio\2026-09-15 — Análise Estatuto e Reforma AME.md`. Textos brutos de OCR preservados em `scratch/` (`estatuto_OCR_raw.txt`, `estatuto2022_FINAL.txt`).
    - **Composição do Conselho Consultivo definida (art. 26, §2º):** ex-presidentes identificados por Paulo — **Marisa Evangelista (2018–2020, primeira presidente)** e **Paulo Addair Daniel Filho (2020–2022 e 2022–2024)**. Novos riscos mapeados: (a) **Paulo passa automaticamente ao Conselho Consultivo em 17/10/2026** se não se reeleger — órgão **não deliberativo e sem voto** (risco de perda de comando da assembleia, mitigado pela cláusula do Bloco VIII.1); (b) falta o **nome civil completo** de Marisa (buscar nas ATAs de 2018/2020) e a lista de **entidades** (UBRAFE, WTC/Sheraton, SENAI/Theobaldo de Nigris, ABEOC).
    - **Novo Bloco XI da reforma — GESTÃO PROFISSIONAL REMUNERADA (CLT):** Paulo pediu parecer sobre contratar **diretor/gestor administrativo CLT** para prospecção e administração, remunerado pela monetização/patrocínios, **sem** que sejam membros da Diretoria ou dos Conselhos. **Veredito: viável, e a arquitetura proposta é a correta.** Fundamentos: (i) a vedação do **art. 11, §1º** alcança **dirigentes** (Diretoria e Conselhos), **não** empregados; (ii) o **art. 16, V** já autoriza *"admitir, contratar e dispensar funcionários"* — falta apenas disciplina; (iii) 🔴 **restrição crítica:** o **CEBAS (Lei 12.101/2009, art. 30) veda a remuneração de dirigentes** — logo, remunera-se **funcionário**, nunca diretor; se o gestor precisar de poderes, usar **procuração/delegação de competência**, não cargo estatutário. Minuta redigida: **art. 11 §§1º e 1º-A a 1º-D** (vedação de **acumulação** governança × emprego, delegação de poderes), **art. 16, XI + §§1º a 3º** (plano de cargos, processo seletivo simplificado, **vinculação de custeio** a monetização/patrocínio/licenciamento, limite anual de despesa com pessoal, relatório mensal/anual), **art. 13, XIII** (aprovar plano e limite de pessoal) e **art. 36, parágrafo único** (remuneração de empregado **não é** distribuição de resultados). Estrutura faseada de implantação: (1) gestor administrativo → (2) captador de recursos → (3) equipe de apoio → (4) assessoria jurídica/contábil. **Riscos anexos:** remuneração variável por captação de recurso público tem vedações legais (validar); MROSC exige **contabilidade segregada** por fonte; Lei 9.608/1998 exige termo de adesão que afaste vínculo do voluntário. **Pendência bloqueante:** levantar o **enquadramento fiscal atual da AME** (CEBAS / Utilidade Pública / OSCIP / imunidade) com o contador antes de fechar a redação.
    - **Riscos abertos:** (a) **art. 15, §1º** — a alteração de 2024 **não averbada** significa que o estatuto registrado **ainda exige 5 Associados Família**, e a Diretoria atual (Paulo, Jorge Pelarin, José Pires, Solange, Alessandra) tem **1 pessoa não-Família** em situação irregular; (b) **art. 15, §2º** — vedação de parentes na mesma Diretoria **provavelmente violada** hoje (Paulo e Regina são cônjuges); (c) conferir se o OCR (que se declara estatuto aprovado em **02/08/2018**) é o **texto consolidado vigente** ou se a ATA/Estatuto de 2022 trouxe versão posterior.

- **09/09/2026 - Plano de Formalização do Quadro Social, Conselhos e Registro Legal (desdobramento do parecer de convocação):**
    - **Gatilhos informados por Paulo:** (a) os **Associados Família nunca foram formalizados** — tecnicamente só os fundadores são reconhecidos; (b) necessidade de **formalizar o Conselho Consultivo**; (c) a **ata de eleição do mandato 2024-2026 não foi registrada**; (d) a **Dra. Marta (advogada signatária do estatuto original) se aposentou** — é preciso novo advogado.
    - **Levantamento documental da fundação (novo):** localizados o **edital de convocação da Assembleia de Constituição (14/08/2017)** e as atas de constituição de **2018** (02/08/2018 e a versão final de 10/07/2018, com cópia do edital de 16/05/2018). O estatuto de constituição (art. 6º) previa **apenas duas categorias — fundador e honorário** — nominando **5 Associados Fundadores**: Paulo Addair Daniel Filho, Jorge Antonio Villalobos Briones, Regina Aparecida Justo Daniel, Maria de Lourdes Campos Bistafa e Alessandro Martins de Oliveira; ingressos posteriores dependiam de **indicação de fundador → proposta à Diretoria → aprovação em Assembleia Geral**.
    - **Consequências mapeadas:** base de quórum irreal/frágil; **Conselho Fiscal provavelmente vago desde out/2024** (reeleito em 2022 com mandato 2022-2024 e **não renovado** na AGO de 04/10/2024); divergência sobre a presidência da assembleia (art. 14, caput, menciona **Conselho Consultivo** — leitura por OCR, a confirmar pelo advogado); averbação do mandato 2024-2026 em aberto e **edital da AGO de 2024 ausente** do acervo.
    - **Artefato criado:** `PLANO_FORMALIZACAO_QUADRO_SOCIAL_E_GOVERNANCA.md` — plano com as 4 frentes (A. quadro social/Livro de Associados; B. Conselhos Consultivo e Fiscal; C. averbação da eleição 2024-2026 com 3 vias alternativas; D. contratação de novo advogado com termo de referência), **proposta de ordem do dia** para o edital de 26/09, cronograma por data e pendências de verificação. Cópia em `F:\01_Projetos\apoio\2026-09-09 — Plano Formalização Quadro Social e Governança AME.md`.
    - **Observação técnica:** o OCR local (modelo de visão `qwen3-vl:30b` via Ollama SOHO) apresentou instabilidade em páginas densas; as regras já extraídas (arts. 12, 13 §1º, 14 §2º/§3º, 29 e 30) foram validadas em render de 300 dpi, e o caput do art. 14 ficou marcado como pendente de confirmação jurídica.

- **09/09/2026 - Parecer de Convocação da Assembleia Geral de 26/09/2026 (análise do acervo legal):**
    - **Análise realizada:** Leitura do acervo `F:\OneDrive\Projeto A.M.E\01_Institucional_e_Legal` (estatuto vigente digitalizado, atas de 2018/2019/2020/2022/2024, edital-modelo, requerimento de averbação ao 6º RTDPJ, situação cadastral RFB e minutas de estatuto). Os PDFs do estatuto não possuem camada de texto — foi necessário OCR local (modelo de visão `qwen3-vl:30b` no Ollama SOHO, com renderização das páginas via `pdftoppm`).
    - **Achado principal:** O **estatuto vigente (art. 14, §2º) exige apenas 7 (sete) dias** de antecedência, com edital **afixado na sede da Associação e no sítio da Associação**, contendo ordem do dia, data e hora. A regra de **15 dias** existe somente na **minuta proposta** (`ESTATUTO SOCIAL projeto ame sugestões Armando e Vanessa (1).docx`), não em vigor. → **Data-limite estatutária: 19/09/2026**; recomendado publicar até **11/09/2026** (margem + prova robusta).
    - **Regras de quórum extraídas (arts. 12, 13, 14, 29, 30):** instalação em 1ª convocação com maioria absoluta dos associados e qualquer número nas seguintes; deliberações por maioria simples; **itens do art. 13 (eleger/destituir conselhos, alterar estatuto, apreciar relatório anual, aprovar orçamento) exigem 50%+1 dos Associados Família presentes com presença mínima de 30% dos Associados Família**; dissolução exige 50%+1 do total.
    - **Lacunas identificadas:** (1) inexistência de **registro/cadastro formal de associados** (bloqueia apuração de quórum — só há `usuarios` 107 e `candidatos` 112, sem categorização estatutária); (2) **edital da AGO de 04/10/2024 ausente** do acervo, embora exigido como anexo da averbação; (3) **AGO de 2025 não localizada** (art. 14 exige assembleia anual — último relatório aprovado cobre até set/2024); (4) **averbação do mandato 2024-2026 em aberto** no 6º RTDPJ (requerimento sem data/protocolo); (5) divergência estatuto × prática sobre quem preside a assembleia (Conselho Consultivo vs. Diretoria); (6) inconsistência de nomenclatura (art. 13 "Conselho de Administração" × art. 15 "Diretoria").
    - **Artefato criado:** `PARECER_CONVOCACAO_ASSEMBLEIA_26SET.md` (raiz do projeto, com transcrições literais dos artigos, calendário de convocação em mermaid, tabela de quórum, checklist de execução e perguntas de decisão) + cópia em `F:\01_Projetos\apoio\2026-09-09 — Parecer Convocação Assembleia 26-09-2026.md`. O `PLANO_ANALISE_COMPLIANCE_ASSEMBLEIA_26SET.md` recebeu nota de atualização corrigindo a premissa dos 15 dias.

- **09/09/2026 - Registro do Plano de Análise Funcional & Compliance para a Assembleia de 26/09/2026:**
    - **Contexto:** Paulo solicitou registrar a necessidade de análise completa das funcionalidades do Projeto AME (melhores práticas + legislação para entidades beneficentes) visando entregar um site moderno, transparente e acessível na Assembleia de 26/09/2026.
    - **Escopo registrado (5 pilares):** A) Transparência institucional pública (estatuto/atas/balanços/prestação de contas/editais); B) Portal do Responsável seguro (informações da associação + dados do(s) filho(s)/atendente(s) por vínculo); C) Primeiro acesso sem senha (`senha = NULL` + e-mail com link de validação) e convite de novos responsáveis (M:N — 1 usuário → N candidatos e 1 candidato → N responsáveis); D) Álbuns e currículos com fotos identificadas por candidato (base para books/revistas/álbuns de figurinha futuros); E) Compliance LGPD, LBI/WCAG, Lei 12.101/2009, MROSC 13.019/2014, Lei 9.608/1998, governança e OWASP.
    - **Levantamento de estado atual:** pivot `candidatos_usuarios` já modela o M:N com `vinculo_tipo`/`pode_editar`; rotas de token/ativação e tabela `tokens` existem parcialmente; `/meuperfil` filtra por vínculo; foto-análise (VPS3) e tabelas de fotos existem; pendências mapeadas (senha NULL em produção, migração MD5→bcrypt, autorização por vínculo em todas as telas, convite/aceite, consentimento de imagem e LGPD).
    - **Artefato criado:** `PLANO_ANALISE_COMPLIANCE_ASSEMBLEIA_26SET.md` (raiz do projeto; com diagramas mermaid de fluxos e modelo M:N, roadmap por fases até 26/09 e perguntas em aberto) + cópia do relatório geral em `F:\01_Projetos\apoio\2026-09-09 — Plano Análise Compliance Projeto AME.md`.

- **09/09/2026 - Correção de CSS/UX: textos sobrepostos nos campos de `/atendentes`:**
    - **Sintoma:** No print do usuário (`apoio/Captura de tela 2026-09-09 193729.png`), os campos "nome do atendente" e "e-mail" exibiam dois textos sobrepostos dentro da mesma caixa (label escuro do MDB + placeholder cinza).
    - **Causa raiz:** Os campos usam o componente `md-form` do Material Design Bootstrap v4 (CSS carregado via CDN em `include/html_head.php`), que posiciona o `<label>` flutuante **dentro** do input. A flutuação do label para cima depende do **JS do MDB** (`mdb.min.js`), que **não é carregado** na página `/atendentes` (`include/scripts.php` está vazio e não há jQuery/MDB JS no rodapé). Resultado: label e placeholder convivem sobrepostos.
    - **Correção aplicada:** Os labels dos dois campos foram marcados como `class="sr-only"` em `pages/atendentes.php` — ocultos visualmente (o placeholder descritivo permanece visível dentro do campo) e preservados para leitores de tela (acessibilidade). Deploy via scp + `chown projetoame:projetoame` na VPS1 e validado no HTML servido.
    - **Nota adicional:** O texto "16 | SETEMBRO" visto sobre o card do MD MAKE A DIFFERENCE é **parte da imagem promocional** `img/MIRANTE-PARK-MD-2025.jpg` (arte do flyer), não é sobreposição de CSS — nenhuma alteração necessária no card.
    - **Recomendação futura:** O padrão `md-form` (label flutuante) exige o bundle jQuery+MDB JS; ao usar `md-form` em páginas que não carregam esses scripts, aplicar `sr-only` nos labels ou remover o atributo `placeholder` para evitar duplicidade visual.

- **09/09/2026 - Correção: Evento "3ª turma CURSO DJ para Eventos" (id 73) não aparecia em `/atendentes`:**
    - **Sintoma:** Evento criado na base da VPS1 (início 15/09/2026) não era listado em `https://projetoame.org/atendentes`, embora existisse em `eventos_marcados` com horário cadastrado (`horarios.horario_id = 196`, 15 vagas).
    - **Causa raiz:** A query da página pública faz INNER JOIN implícito com `imagens` (`WHERE eventos_marcados.imagem_id = imagens.imagem_id AND final >= CURDATE()`). O evento foi inserido com `imagem_id = 0` (inserção direta na base, fora das telas oficiais `adminnovaatividade.php`/`novoevento.php`, que exigem `imagem_id > 0`), sendo silenciosamente descartado do JOIN por não existir imagem com id 0.
    - **Correção aplicada (produção VPS1):**
        1. Upload do pôster local `public_html/img/poster_AMEDJs.jpg` para `/home/projetoame/public_html/img/` (via scp) com `chown projetoame:projetoame` + `chmod 644`.
        2. `INSERT INTO imagens (url) VALUES ('img/poster_AMEDJs.jpg');` → nova `imagem_id = 102`.
        3. `UPDATE eventos_marcados SET imagem_id = 102 WHERE id = 73;`
    - **Validação:** Query da página retorna o evento 73 com a imagem; página pública `https://projetoame.org/atendentes` exibe o card "3ª turma CURSO DJ para Eventos" (15/Set a 06/Out, 15 vagas).
    - **Lições / Recomendação:** Ao criar eventos fora da interface oficial (SQL direto), garantir sempre um `imagem_id` válido existente em `imagens`; caso contrário o evento fica invisível nas páginas públicas que usam o JOIN.

- **05/09/2026 - Concepção do AME Web-to-Print Studio (AME Magazine & Álbuns de Memória) & Proposta AlphaGraphics:**
    - **Conceito & Arquitetura Editorial:** Estruturação do motor Web-to-Print para geração de publicações personalizadas sob demanda para os responsáveis dos atendentes da AME, transformando fotos reconhecidas por IA nos eventos oficiais em publicações físicas.
    - **4 Modelos Editoriais Definidos no MVP:**
        1. *Revista Celebridades (Estilo CARAS / Quem):* Edição de gala com matéria de capa, cobertura dos eventos e depoimento da família.
        2. *História em Quadrinhos (HQ AME):* Narrativa visual em estilo comic book destacando o atendente como protagonista de inclusão.
        3. *Livro de Colorir & Atividades:* Conversão automática de fotos em traço *Line Art* (P&B) para colorir com passatempos e labirintos da inclusão.
        4. *Álbum de Figurinhas AME:* Álbum A4 (grid 4x) com marca d'água/esboço a 20% de opacidade nas molduras dos cromos (estimulando foco e percepção motora) + matérias-tampão institucionais (*calhau*) + cartela de figurinhas autoadesivas A4.
    - **Acesso Seguro sem Senha:** Planejado o fluxo por Link Mágico com assinatura criptográfica HMAC-SHA256 e expiração temporal.
    - **Documentação & Planejamento:** Criado `PLANO_AME_MAGAZINE_WEB_TO_PRINT.md` com o diagrama de arquitetura e modelo de dados.
    - **Proposta Institucional AlphaGraphics (Etapa 5):** Elaborado o documento `PROPOSTA_PARCERIA_ALPHAGRAPHICS.md` e compilado em `.docx` via Pandoc (também replicado para a pasta `/apoio`), estruturando cotas de patrocínio ESG e apoio gráfico sob demanda.


- **04/09/2026 - Correção de Reconhecimento de Idioma (Galego -> pt-BR) e Supressão de Tradução Indevida:**
    - **Diagnóstico:** O Google Chrome estava exibindo o popup de tradução automática identificando o idioma da página incorretamente como "Galego". A causa raiz era a ausência da tag `<!DOCTYPE html>`, atributos `lang="pt-BR"` e meta tags de identificação de idioma nos templates legados (`include/html_head.php` e `include/html_head_table.php`).
    - **Solução Implementada:**
        - Adicionado `<!DOCTYPE html>` e `<html lang="pt-BR" class="notranslate" translate="no">` no início dos arquivos de cabeçalho global.
        - Inseridas as meta tags `<meta http-equiv="Content-Language" content="pt-BR">` e `<meta name="google" content="notranslate">`.
        - Limpeza de fechamentos prematuros (`</body></html>`) no topo de `include/html_footer_scripts.php`.
        - Sincronização via SCP com a VPS1 e validação em produção.

- **04/09/2026 - Restauração e Correção da Página de Inscrição (`/inscrever`) & Recuperação do Candidato Luiz Renato (ID 45):**
    - **Diagnóstico da Causa Raiz (/inscrever):** O acesso a `https://projetoame.org/inscrever` apresentava `Warning: Undefined variable $inscrito on line 253` e truncava a renderização logo após a tag de breadcrumb. O arquivo `pages/inscrever.php` estava incompleto (apenas 258 linhas), sem o formulário HTML de cadastro, sem os campos de entrada e sem os includes de rodapé (`footer-botton.php`), scripts (`html_footer_scripts.php`) e acessibilidade/VLibras (`end.php`).
    - **Soluções Implementadas em /inscrever:**
        - **Inicialização Segura de Variáveis:** Definidos valores padrão para `$inscrito`, `$msg`, `$status` e array `$dados`, eliminando qualquer aviso ou warning de variável indefinida.
        - **Formulário Completo e Acessível:** Implementado formulário estruturado e responsivo (Bootstrap 4 + MDBootstrap) com seções organizadas (1. Dados do Atendente, 2. Responsável & Contato, 3. Documentos & Informações Complementares) e suporte integral aos modos de Alto Contraste e Fonte Grande.
        - **Tratamento Resiliente de POST:** Inserção e atualização protegidas com escape de caracteres, cálculo dinâmico de rodízio para novos cadastros (status `ativo = -1` para moderação prévia), criação/vinculação de usuário na tabela `usuarios` e `candidatos_usuarios`, upload de anexos em `docs/` registrado na tabela `documentos` e disparo de e-mail de notificação administrativa com botões de moderação rápida.
        - **Card de Confirmação:** Exibição de tela de sucesso amigável e intuitiva com opções de retorno ao início ou consulta de escalas.
        - **Deploy & Homologação:** Arquivo sincronizado com o servidor de produção (VPS1 via `scp`) e validado ao vivo em `https://projetoame.org/inscrever`.
    - **Reposicionamento dos Botões de Acessibilidade (Navbar Topo):**
        - A barra de acessibilidade flutuante lateral (`#barra-acessibilidade` a `left: 10px; top: 120px;`) foi removida de `include/html_head.php` pois sobrepunha e bloqueava os menus laterais (ex: no painel administrativo `#admin-sidebar` e no casting).
        - Os controles de acessibilidade (Alto Contraste e Aumento de Fonte `A+`) foram integrados diretamente em linha no topo da barra de navegação principal (`include/nav.php`), posicionados em grupo estilo pílula antes do menu de usuário/login, proporcionando layout limpo, elegante e 100% livre de sobreposição em todas as páginas.
        - Deploy sincronizado com a VPS1 e validado ao vivo.

        - O candidato havia sido excluído em teste acidental via `/excluircandidato/45`.
        - Como a rotina de exclusão atingia apenas a tabela principal `candidatos`, todas as tabelas dependentes (165 registros de `disponibilidade`, 95 fotos reconhecidas em `fotos_reconhecidas`, documentos de autorização de uso de imagem em `documentos` e vínculos em `candidatos_usuarios`) permaneceram 100% íntegras no banco da VPS1.
        - O registro de `candidatos` (ID 45, Luiz Renato Justo Daniel, ativo = 1, rodizio = 23, foto 24) foi resgatado com precisão cirúrgica a partir dos backups consolidados e reinserido no MariaDB de produção da VPS1.
        - Validado acesso e visualização em `/curriculo?c=45` e `/casting`.



- **02/09/2026 - Correção de Erro de Conexão na Escala (/admin/escala) e Autocura de Tabelas de Apoio:**
    - **Diagnóstico da Causa Raiz:** O acesso a `https://projetoame.org/admin/escala?evento_id=72` exibia o erro `Erro de conexão: Unexpected token '<', "..." is not valid JSON`. O rastreamento revelou que a requisição assíncrona para `include/api_escala.php` (e na página pública de avaliação `pages/avaliacao.php`) disparava um erro fatal do PHP: `Fatal error: Uncaught mysqli_sql_exception: Unknown column 'presente' in 'field list' / 'p.presente' in 'SELECT'`. Como a tabela `presenca` no banco de dados de produção existia sem a coluna `presente`, o MySQL no PHP 8.1+ abortava a execução cuspindo HTML de erro com `<br /><b>Fatal error</b>`, quebrando o parser JSON do JavaScript.
    - **Solução & Autocura Implementadas:**
        - **Autocura de Schema (`include/funcoes.php`):** Criada a rotina `garantir_tabelas_suporte_escala($conexao)` que verifica automaticamente e cria se não existirem (ou adiciona via `ALTER TABLE` colunas ausentes) as tabelas `presenca` (`presente`, `data_confirmacao`, `confirmadopor`), `historico_rodizio` e `avaliacoes`.
        - **Proteção e Fallback na API (`include/api_escala.php`):** Chamada de autocura integrada no bootstrap do endpoint, consultas de `presenca` e `avaliacoes` encapsuladas em blocos `try...catch` com inicialização vazia segura e encapsulamento global em `try...catch (Throwable $e)` garantindo que o endpoint **sempre** retorne JSON válido (`{"success": false, "message": ...}`).
        - **Proteção da Página Pública de Avaliação (`pages/avaliacao.php`):** Integrada a autocura de tabelas e query protegida com fallback automático sem join em caso de inconsistência de schema.
        - **Resiliência no Frontend (`pages/adminescala.php`):** Tratamento do `fetch()` refatorado para ler texto bruto e sanitizar respostas não-JSON antes de lançar erro, evitando quebras genéricas de sintaxe na interface.
        - **Deploy & Homologação em Produção (VPS1):**
            - Verificado que o diretório `/home/projetoame/public_html` na VPS1 não opera como repositório Git clone.
            - Os arquivos atualizados (`funcoes.php`, `api_escala.php`, `avaliacao.php`, `adminescala.php`) foram sincronizados via `scp` diretamente para a VPS1 e alinhados com `chown projetoame:projetoame`.
            - As colunas ausentes na tabela legada `presenca` (`candidato_id`, `presente`, `data_confirmacao`, `confirmadopor`) e o índice único `idx_evento_candidato` foram criados no MariaDB de produção.
            - Validação end-to-end realizada com sucesso: a requisição JSON de `get_escala_details` para o evento 72 retornou status 200 com payload completo (`success: true`), e a rota pública de avaliação (`/avaliacao/{uuid}`) renderizou perfeitamente os atendentes e horários.
        - **Correção no Salvamento Manual da Escala (`salvar_escala`):**
            - **Bug identificado:** Ao sugerir a escala e substituir manualmente um atendente (ex: Henrique Martins por Isabele Maia Silva), o clique em "Salvar Escala" disparava `TypeError: Cannot set properties of null (setting 'innerHTML')`. A causa raiz continha dois fatores: 1) o frontend enviava `action: 'salvar_escala'`, enquanto o backend esperava apenas `action === 'save_escala'`, retornando "Ação inválida"; 2) o elemento `<div id="mensagem-escala">` ficava dentro de `#escala-container` e era apagado do DOM na montagem da tabela (`escalaContainer.innerHTML = tableHtml`), gerando o `TypeError` ao tentar exibir o feedback de erro/sucesso.
            - **Correções aplicadas:** `include/api_escala.php` adaptado para aceitar tanto `salvar_escala` quanto `save_escala`, além de garantir upsert na tabela `disponibilidade` para atendentes escalados manualmente; `pages/adminescala.php` corrigido com a fixação do `#mensagem-escala` fora da área de renderização dinâmica da tabela, com criação sob demanda como fallback resiliente e scroll suave ao topo.
            - **Deploy:** Arquivos sincronizados na VPS1 via `scp` e testados com sucesso via requisição POST real.

- **23/08/2026 - Correção de Data do Evento MD MAKE A DIFFERENCE na Tabela `horarios` (VPS1):**
    - **Bug identificado:** A página `/atendentes` exibia "16/setembro, terça-feira" para o evento MD MAKE A DIFFERENCE. A causa raiz foi identificada diretamente no banco de dados da VPS1: a tabela `eventos_marcados` continha a data correta (`inicio = 2026-09-16`, quarta-feira), mas a tabela `horarios` (que é a fonte dos checkboxes de disponibilidade exibidos na página) continha o `data_inicio = 2025-09-16` (ano errado — terça-feira em 2025).
    - **Correção aplicada:** `UPDATE horarios SET data_inicio = DATE_ADD(data_inicio, INTERVAL 1 YEAR), data_final = DATE_ADD(data_final, INTERVAL 1 YEAR) WHERE horario_id = 195;`
    - **Resultado:** `horarios.horario_id = 195` agora aponta para `2026-09-16` (quarta-feira), alinhado com `eventos_marcados.id = 72`.

- **22/08/2026 - Varredura Automática do Blog WordPress (Biometria) e Ocultação de Fotos:**
    * **Varredura e Mapeamento Histórico (`scan_blog_biometrics.py`):** Criado e executado na VPS1 o script de automação que varre a base de dados do WordPress (`projetoame_wordpress`), associa posts com eventos do CRM por proximidade de data e título, salva as URLs de posts nos eventos correspondentes, resolve os caminhos das mídias físicas no servidor (de 2016 a 2026) e realiza o reconhecimento facial dos atendentes de forma 100% automatizada (identificando 668 fotos).
    * **Ocultação de Fotos pelo Usuário:**
        - Criada a coluna `oculta` (TINYINT) na tabela `fotos_reconhecidas` locais e na VPS1.
        - Criado o endpoint API seguro `pages/api_ocultar_foto.php` que marca `oculta = 1` após validar autenticação e permissão de acesso ao associado.
        - Atualizada a consulta e renderização de fotos no Portal do Responsável (`pages/meuperfil.php`) para filtrar fotos ativas (`oculta = 0`) e renderizar um botão de remoção discreto por hover com animação de fadeOut via AJAX/jQuery.

- **22/08/2026 - Correção e Alinhamento do Banco para Portfólio de Fotos Biométricas (Fase 2):**
    - **Correção da Tabela `fotos_reconhecidas`:** Identificada e corrigida a tabela que havia sido gerada erroneamente como `otos_reconhecidas` (devido ao escape do caractere `\f` no interpretador SQL). O banco de dados local foi ajustado para conter a tabela `fotos_reconhecidas` e colunas corretas (`foto_path` e `data_registro`).
    - **Ajuste no Portal do Responsável (`/meuperfil`):** Atualizada a query e a renderização de imagens em `pages/meuperfil.php` para apontar corretamente para a tabela `fotos_reconhecidas` e colunas `foto_path` e `data_registro`, além de prepender `$GLOBALS['app_web_root']` para resolver o caminho relativo no servidor local e VPS.

- **22/08/2026 - Fluxo de Presenças e Liberação de Ficha de Avaliação de Atendentes:**
    - **Liberação de Ficha de Avaliação por Presença:** Integrada a tabela `presenca` no ecossistema de eventos. A ficha individual de avaliação e a página pública `/avaliacao/{uuid}` agora disponibilizam e destacam prioritariamente os atendentes que tiveram presença confirmada no evento.
    - **Ações Rápidas na Escala (`/admin/escala`):** Adicionada a cópia com 1 clique do link de avaliação seguro de cada participante e botão direto para avaliação.
    - **Mensagem Automatizada para o Contratante:** O modal de envio ao contratante agora formata o texto para o WhatsApp com link dinâmico da página pública de avaliação da equipe presente.
    - **Correção da Gestão de Atividades (`/admin/atividades`):** Resolvido erro 403 no endpoint `/include/lista_todos_eventos.php` com ajuste de permissões e padronização do `include/footer-database-noorder.php` para CDNs oficiais do DataTables/Bootstrap/jQuery.
    - **Ajuste de Status de Eventos:** Evento 71 (CONARH 2026) marcado como `realizado` e Evento 72 (MD MAKE A DIFFERENCE) como `agendado`.
    - **Ações Rápidas de Contato no Casting:** Implementados links diretos `tel:+55...` no ícone/número de telefone (para discagem imediata), `mailto:...` no ícone/e-mail (para abertura do cliente de mensagens) e `https://wa.me/...` no ícone do WhatsApp.
    - **Filtros Dinâmicos no Casting (`/casting`):** Adicionada barra de filtros rápidos por status: "Apenas Ativos" (padrão inicial), "Mostrar Todos" e "Inativos" com contadores automáticos do banco.
    - **Estilização Visual de Inativos:** Implementado badge vermelho discreto (`Inativo`), linha esmaecida e foto de perfil com 50% de opacidade e grayscale para participantes inativos.
    - **Roteamento Dinâmico (Front Controller):** Substituído switch manual de rotas em `index.php` por resolução dinâmica em `pages/` com sanitização regex.
    - **Redirecionamento Home:** Mantido redirecionamento automático da raiz (`/` ou `/index`) para o WordPress em `/home/`.
    - **Formatação de Datas:** Criada função `formataDataEventoBR()` em `include/funcoes.php` e aplicada em `pages/atendentes.php` corrigindo exibição em português.
    - **Casting & Candidatos:** Reformulado `pages/casting.php` com DataTables, pesquisa em tempo real e links rápidos para WhatsApp, CV e edição.
    - **Módulo de Currículo (`/curriculo/{id}`):** Refatorado `pages/curriculo.php` com suporte a `$app_web_root`, preview integrado, emissão em PDF sem conflito de cabeçalhos e inclusão do rodapé `footer-database.php`.
    - **Padronização de Assets (Bootstrap / MDB / jQuery / DataTables):** Migradas as dependências legadas inexistentes em disco para CDNs oficiais estáveis.
    - **PWA Manifest & Acessibilidade:** Criado `img/manifest.json` e widget VLibras ajustado.

- **21/08/2026 - Correção de Redirecionamento e Headers no Front Controller:**
    - **Resolução de Conflito de Headers:** Movido o redirecionamento da raiz (`/` e `/index`) para o topo de `index.php`, antes de qualquer carregamento de cabeçalho (`include/html_head.php`). Isto resolve o erro de "headers already sent" e restaura o redirecionamento automático para a landing page do WordPress (`/home/`).

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
- Documentada a especificao de status: candidatos.ativo = -1 (Inativo/Spam), 0 (Treinamento), 1 (Ativo).
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


- **18/09/2026 - Auditoria de Cobertura do Blog x Base de Atividades:**
    - **Objetivo:** medir quantas das atividades cadastradas no CRM já têm artigo publicado no blog (`projetoame.org/home`) e listar as lacunas para cobertura completa.
    - **Coleta do blog:** 72 posts públicos obtidos via REST API do WordPress (`/wp-json/wp/v2/posts`, `X-WP-Total = 72`), de 03/03/2016 a 15/09/2026. O feed `/home/rss` só expõe os 10 posts mais recentes — por isso a coleta foi feita pela REST, com paginação.
    - **Coleta da base:** tabela `eventos_marcados` do schema `projetoame` na **VPS1 (produção)** — 73 registros, de 01/09/2017 a 06/10/2026. A cópia local (XAMPP) estava defasada em 6 registros (parava em 14/04/2026) e foi descartada como fonte.
    - **Achado de integridade 1:** a tabela `eventos` de produção (981 registros) está **inteiramente preenchida com datas de 2027** (calendário de feiras prospectadas), enquanto a cópia local tem 245 registros entre 2023 e 2027. Não é a base de atividades — a base real é `eventos_marcados`. Registrado para verificação futura.
    - **Achado de integridade 2:** 16 registros legados (ids `47`-`62`) são duplicatas de importação antiga do núcleo com ficha de avaliação (ids `1`-`38`). Sugerida consolidação para não poluir relatórios.
    - **Resultado:** 15 atividades (20,5%) com artigo próprio; 4 com cobertura apenas parcial/duvidosa; **38 sem nenhum artigo** (29 do núcleo com UUID, 7 legados únicos, 1 histórica "Dr. Zan" e 1 agendada — 3ª turma do Curso de DJ).
    - **Lacuna reversa:** o blog documenta atividades ausentes da base (ALESP 20/03/2026, Maio Amarelo/Multa Moral, Dia da Mulher no MASP, barraca de café na FESPA 2023, 9º Simpósio Internacional da Síndrome de Down).
    - **Entregável:** `RELATORIO_COBERTURA_BLOG_ATIVIDADES_18SET2026.md` (raiz do projeto + cópia em `F:\01_Projetos\apoio\`), com tabelas de cobertura, lacunas priorizadas e plano editorial em 5 ondas.
- **18/09/2026 - Consolidação das Duplicatas Legadas de `eventos_marcados` (VPS1):**
    - **Diagnóstico:** 16 registros (`id` 47 a 62) eram um **lote de importação antiga** que refazia registros do núcleo vivo (mesmo evento e data, nome abreviado, sem ficha de avaliação). Ex.: `47` "FESPA" duplicava `2` "FESPA DIGITAL PRINTING 2024".
    - **Backup prévio:** `/root/backups/ame_pre_consolidacao_20260918.sql` (md5 `f35ee4dcfeedf7444dc3b38602312c37`), replicado em `scratch/backups/`. **Nenhuma linha foi destruída** — todas foram movidas para tabelas de arquivo.
    - **Arquivamento:** criada `eventos_marcados_legado_20260918` (16 linhas, com colunas `duplica_de` e `arquivado_em`).
    - **Remapeamento de filhas:** `fotos_reconhecidas` (303 linhas legadas, todas com correspondente 1:1 idêntico em `candidato_id + foto_path` no núcleo — 117/28/51/26/1/80 casados) e `horarios` (48 linhas remapeadas para os eventos núcleo).
    - **Limpeza de placeholders:** identificados via dump de backup os 48 `horario_id` do lote legado — 100% deles eram placeholder puro (`empresa_id=0`, `vagas=0`, `tipo='evento'`), sem nenhuma atribuição real. Arquivados em `horarios_legado_20260918` e removidos.
    - **Resultado:** `eventos_marcados` 73 → **57** · `horarios` 189 → **141** · `fotos_reconhecidas` 668 → **365** · **0 referências órfãs**.
    - **Validação:** rotas `/`, `/home/`, `/admin/atividades`, `/admin/escala` e `/avaliacao` respondendo HTTP 200 após a operação.
    - **Achado relevante:** a tabela já possui a coluna **`link_artigo`** (varchar 255), preenchida em apenas 12 dos 57 registros e com 3 problemas (o post do Curso de DJ está replicado em 4 atividades; `24` e `44` têm artigo mas estão sem link). O campo é a chave para a esteira automatizada de cobertura.
    - **Achado de schema:** a produção tem `slug` e `link_artigo`, e **não** tem `empresa_id` — a cópia local (XAMPP) divergia e foi descartada como fonte.

- **18/09/2026 - Plano da Esteira de Cobertura do Blog (`PLANO_ESTEIRA_COBERTURA_BLOG_AME.md`):**
    - **Diagnóstico da causa raiz dos artigos genéricos:** o motor `content_factory_critic_engine.py` tem `RSS_FEED_URL` hardcoded em `g1.globo.com/rss/g1/tecnologia/` e o pool `TENANT_CURATED_PAUTAS` não tem entrada para `projetoame` — logo o tenant cai no RSS genérico. O bloco `projetoame` em `wp_content_factory_tenants.json` não tem `strict_scope`, `editorial_anchor` nem `primary_cta_url`.
    - **Solução proposta:** modo de pauta *event-driven* (`fetch_activity_pauta()`) alimentado por `eventos_marcados`, com `pauta_source: "activities"` e `strict_scope: true` no tenant; write-back do `link_artigo` após publicação (contador de cobertura = `57 - COUNT(link_artigo <> '')`).
    - **Pipeline de fotos mapeado:** a VPS3 já tem `enrollment.py`, `scan_events.py` e `biometria_venv` (face_recognition/dlib) operacionais; `fotos_reconhecidas` já tem 365 matches de 35 atendentes. Falta ampliar o enrollment (39 de 112 candidatos têm vetor) e indexar o acervo `E:\06_Backup_Local\POCO_PADF\` (73.069 arquivos / 118,55 GB) por EXIF `DateTimeOriginal` para cruzar com as datas dos eventos.
    - **Alerta LGPD registrado:** fotos de pessoas com deficiência são dado sensível e face encoding é dado biométrico — exige base legal, registro de consentimento, respeito ao `fotos_reconhecidas.oculta` (rota `api_ocultar_foto.php`) e revisão humana obrigatória antes de publicar.

- **18/09/2026 - Esteira de Cobertura do Blog Entregue (pauta por atividade + write-back):**
    - **Causa raiz confirmada do conteúdo genérico:** `TENANT_CURATED_PAUTAS["projetoame"]` tinha **apenas 2 pautas editoriais genéricas** no motor — e foram exatamente elas os posts de 12/09 ("Trabalho e Deficiência no Brasil") e 15/09 ("Acolhimento e Equidade Corporativa"). Com o pool esgotado, o próximo artigo cairia no `RSS_FEED_URL` hardcoded (`g1.globo.com/rss/g1/tecnologia/`). A hipótese inicial de "tenant sem pauta curada" estava **errada** e foi corrigida no plano.
    - **Backfill do `link_artigo`:** o campo tinha 12 registros preenchidos, todos com defeito. Causa identificada: o script `database/add_link_artigo.php` fazia o casamento pela **primeira palavra** do nome do evento (`strtok($nome,' ')`), então "CURSO BÁSICO", "CURSO TECNOLOGIA" e "CURSO FINANÇAS" caíram todos no post do Curso de DJ. Corrigido por mapeamento explícito por ID (15 vínculos), validado slug a slug contra o blog. Contador de cobertura agora operacional: **15 com artigo / 42 de lacuna**.
    - **Endpoint `include/api_ame_pautas.php` (VPS1):** serve as atividades realizadas sem artigo, com local, endereço, empresas contratantes, atendentes reconhecidos e fotos. Autenticação fail-closed por header `X-AME-Token`; segredo em `/home/projetoame/ame_pautas.token`, **fora do web root** (o `.htaccess` só reescreve rotas e não bloqueava arquivos estáticos). Aceita `POST` para write-back e só grava URLs do próprio blog.
    - **Endurecimento do `.htaccess`:** adicionado `FilesMatch` negando acesso HTTP a `.token`, `.sql`, `.env`, `.log`, `.bak`, `.ini`. Backup do arquivo anterior em `/root/backups/htaccess_pre_20260918.bak`. Verificado: arquivos de teste `.token`/`.sql` retornam **403**, site 100% em 200.
    - **Provedor `infra/ame_pautas_provider.py` (NASANET):** consome o endpoint e monta a pauta com fatos reais, com instrução explícita de não inventar dados ausentes.
    - **Patches no motor (VPS2):** desvio `pauta_source == "activities"` antes do pool curado e do RSS; âncora factual movida para o *system prompt* (vale em todos os ciclos) com lembrete de reancoragem no ciclo ≥ 2; **trava programática** no revisor que reprova e limita a 60 pts o texto que não cita a atividade; `write_back_ame_link_artigo()` após publicação; box institucional dirigido pelo tenant.
    - **Achado durante o dry-run:** o ciclo 2 **abandonava** a atividade ("Inclusão Produtiva: Quando o Trabalho Deixa de Ser Favor e Vira Estratégia") porque o revisor avalia voz/clichê/fluidez e o prompt de refinamento não repetia a atividade. Após a correção, o ciclo 2 manteve o fato e foi **APROVADO com 95 pts** ("CONARH 2026: Atendentes Muito Especiais Mostram Que Inclusão é Trabalho Real").
    - **Efeito colateral também corrigido:** ao ganhar `primary_cta_url`, o tenant passou a receber o box **hardcoded da Trilogia do PAPONET** — o revisor flagrou no próprio dry-run. O box passou a ser dirigido por `primary_cta_title`/`primary_cta_button`/`primary_cta_body`, com o PAPONET preservado.
    - **Isolamento verificado:** os outros 14 canais não mudaram de comportamento (`pauta_source = None`); teste unitário confirmou que o PAPONET mantém a Trilogia e o AME não.
    - **Riscos registrados:** (1) o `amazon_tag` por tenant em `wp_content_factory_tenants.json` é **campo morto** — o motor usa só a constante `AMAZON_TAG` (correta desde 15/09; o post de 12/09 saiu com `novaeratec0b-20`, inválida); (2) o LiteLLM respondeu **HTTP 402** (OpenRouter sem crédito) e **404** (pool Anthropic) nos dry-runs, caindo no fallback DeepSeek-V3 direto; (3) na cadência atual do `--round-robin` (7 slots/dia entre 15 tenants), cobrir as 40 pendências levaria ~80 dias — considerar slot dedicado ao AME.
    - **Arquivos:** `VPS/public_html/include/api_ame_pautas.php`, `VPS/public_html/.htaccess`, `PLANO_ESTEIRA_COBERTURA_BLOG_AME.md`; no repo NASANET, `infra/ame_pautas_provider.py` e patches em `infra/content_factory_critic_engine.py` + `infra/wp_content_factory_tenants.json` (commit `6fec2c77`).

---

## 🔗 Conexões & Ecossistema
- **Servidores & Infraestrutura:** [[VPS1_Hostinger]], [[VPS2_NASANET]], [[VPS3_Inferencia]].
- **Projetos Relacionados:** [[PRINTADVISOR]], [[AUTOMACAO]], [[contacteme]], [[cafezinhosocial]].
- **Documentação Local:** [[GEMINI.md]], [[REGRAS_DE_NEGOCIO.md]], [[docs/UX_PAINEL_DO_RESPONSAVEL_IMPECCABLE.md]].



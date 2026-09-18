---
project: PROJETO_AME
tags:
  - "#projeto/ame"
  - "#tipo/relatorio"
  - "#modulo/biometria"
  - "#modulo/midia"
  - "#tipo/plano"
relacionados:
  - "[[PROJETO_AME]]"
  - "[[RELATORIO_COBERTURA_BLOG_ATIVIDADES]]"
  - "[[PLANO_ESTEIRA_COBERTURA_BLOG_AME]]"
---

# Projeto AME — Índice de Mídia (EXIF) × Atividades

> **Data:** 18/09/2026 · **Acervo indexado:** `E:\06_Backup_Local\POCO_PADF\` · **Ferramentas:** `tools/indexar_midia_exif.py` e `tools/cruzar_midia_atividades.py`

## 1. O que foi feito

Duas ferramentas novas, ambas somente-leitura sobre o acervo:

| Ferramenta | Função |
|---|---|
| `tools/indexar_midia_exif.py` | Varre o acervo e extrai, por arquivo: data de captura (EXIF/nome/mtime), GPS, modelo da câmera, dimensões, orientação e **categoria de origem** |
| `tools/cruzar_midia_atividades.py` | Cruza as fotos com `eventos_marcados` por janela de data e propõe quais fotos pertencem a qual atividade |

## 2. Resultado da indexação

**57.921 arquivos de mídia** processados em `E:\06_Backup_Local\POCO_PADF\`:

| Categoria | Arquivos | Utilidade para cobertura de atividades |
|---|---|---|
| `outros` (cache de apps em `device\Pictures\`) | 38.141 | ❌ ruído — só tem data de cópia |
| `thumbnail` (miniaturas de vídeo `.thumbnails`) | 14.030 | ❌ ruído |
| `social` (Facebook, WhatsApp, Instagram) | 4.851 | ❌ ruído |
| `screenshot` | 363 | ❌ ruído |
| **`camera` (`DCIM\Camera`)** | **512** | ✅ **fonte confiável** |
| `download` / `scan` | 24 | ❌ ruído |

### 2.1 Achado crítico: a data de arquivo não serve

Das 57.921 mídias, **apenas 944 (1,6%) têm EXIF real** com `DateTimeOriginal`. As outras 56.977 só têm o `mtime` — que é a **data da cópia**, não da captura.

Na prática: os arquivos de `DCIM\Camera` têm `mtime` de 06/10/2024 e 16/10/2024, mas EXIF de 13/06/2024 e 25/06/2024. Usar `mtime` produziria "eventos" inteiramente falsos — por exemplo, um agrupamento de **617 fotos em 26/02/2026** que era, na verdade, um lote de miniaturas de vídeo copiadas naquele dia.

**Consequência de projeto:** o critério de confiança do cruzamento é `fonte_data == 'exif'`, não a pasta nem o nome do arquivo.

### 2.2 Equipamentos identificados nos EXIF

| Modelo | Fotos |
|---|---|
| `21061110AG` (POCO M3 Pro 5G) | 783 |
| `Mi A3` | 125 |
| `NIKON D800` / `NIKON D5100` / `SM-M225FV` | 6 |

## 3. Cruzamento com as atividades

**944 fotos com data confiável**, distribuídas em 252 dias. Cruzando com as 56 atividades da base:

- **21 atividades** têm fotos candidatas
- **8 sem ambiguidade** — podem ir direto para a galeria do artigo
- **13 marcadas como AMBÍGUAS** — o script **não escolhe**; exige decisão humana

Exemplos de ambiguidade legítima: as turmas I e II do Curso de DJ aconteceram nos mesmos dias (11/03 a 01/04/2025) e compartilham as mesmas 80 fotos; o Curso de Fotografia SENAI se sobrepõe a seis outras atividades.

### 3.1 Lacunas de cobertura que agora têm fotos

**19 das 42 lacunas** passam a ter material fotográfico real:

| Prioridade | Atividade | Fotos | Status |
|---|---|---|---|
| ALTA | `71` CONARH 2026 | 5 | ok |
| ALTA | `21` FESPA DIGITAL PRINTING 2025 | 24 | ambíguo |
| ALTA | `19` FEBRATÊXTIL (2025) | 23 | ambíguo |
| ALTA | `35` CURSO de Automaquiagem O Boticário | 17 | ambíguo |
| ALTA | `30` ALPHAGRAPHICS AGENTES DA TRANSFORMAÇÃO 2026 | 15 | ok |
| ALTA | `9` ABF EXPO 2024 | 12 | ok |
| ALTA | `33` EXPOPRINT CONVER FLEXO 2026 | 9 | ambíguo |
| ALTA | `13` ABUP SHOW 2025 | 6 | ambíguo |
| ALTA | `36` FEBRATÊXTIL 2026 | 5 | ambíguo |
| ALTA | `34` DOWNLANDIA no SBT | 4 | ambíguo |
| MÉDIA | `25` ABF EXPO 2025 | 55 | ok |
| MÉDIA | `31` CURSO de Fotografia SENAI | 67 | ambíguo |
| BAIXA | `22`/`23` Curso de DJ Turmas I e II | 80 cada | ambíguo |
| BAIXA | `18` Aniversário Henri Zylberstajn | 9 | ambíguo |
| BAIXA | `26` MD MAKE A DIFFERENCE | 11 | ok |

Saídas: `apoio/midia_exif_poco_padf.csv` (índice completo) e `apoio/midia_por_atividade.csv` (cruzamento).

## 4. Limites e próximos passos

1. **O acervo atual é modesto para eventos.** 944 fotos com data confiável é pouco diante de 15 anos de atividades — e o download do celular atual ainda está em andamento. Reexecutar as duas ferramentas quando ele terminar deve multiplicar a base.
2. **O reconhecimento facial ainda não rodou nas atividades pendentes.** As 365 linhas de `fotos_reconhecidas` cobrem só eventos antigos (2017-2024). Consequência: a pauta por atividade hoje chega ao redator com `atendentes_presentes: 0` para as lacunas de 2025-2026.
3. **Enrollment biométrico incompleto:** apenas **39 dos 112 candidatos** têm vetor `face_encoding`. Sem ampliar isso, o reconhecimento não nomeia ninguém.
4. **Fluxo proposto por atividade:** (a) definir a pasta da atividade pelo cruzamento EXIF; (b) revisar humanamente os casos ambíguos; (c) rodar `scan_events.py <evento_id> <pasta>` na VPS3; (d) a partir daí a pauta passa a trazer contagem e fotos reais.
5. **Vídeos (`.mp4`) não entram por EXIF** — precisam de `QuickTime CreateDate` ou do nome do arquivo, em trilha própria. São 8.278 no acervo.
6. **Preservação de data na cópia:** recomenda-se `robocopy /COPY:DAT` (ou equivalente) no próximo offload do celular, para que o `mtime` deixe de ser inútil.

### ⚠️ LGPD — reforço

O cruzamento **não publica nada** e não nomeia ninguém: ele apenas sugere conjuntos de fotos por proximidade de data. Antes de qualquer uso público:

- respeitar `fotos_reconhecidas.oculta = 1` (direito de oposição já implementado em `api_ocultar_foto.php`);
- ter base legal e consentimento para uso de imagem **e** para o tratamento biométrico;
- manter **revisão humana obrigatória** — a esteira publica em `draft`.

## 5. 🔗 Conexões & Ecossistema

- [[PROJETO_AME]] · [[GEMINI]] · [[HISTORY]] · `RELATORIO_COBERTURA_BLOG_ATIVIDADES_18SET2026.md` · `PLANO_ESTEIRA_COBERTURA_BLOG_AME.md`
- Infra: `#infra/vps1` (site + `fotos_reconhecidas`), `#infra/vps3` (`scan_events.py`, `biometria_venv`)
- Módulos: `#modulo/biometria`, `#modulo/midia`, `#modulo/crm`

---
project: PROJETO_AME
tags:
  - "#projeto/ame"
  - "#tipo/relatorio"
  - "#modulo/biometria"
  - "#modulo/midia"
relacionados:
  - "[[PROJETO_AME]]"
  - "[[RELATORIO_COBERTURA_BLOG_ATIVIDADES]]"
  - "[[PLANO_ESTEIRA_COBERTURA_BLOG_AME]]"
---

# Projeto AME — Índice de Mídia (EXIF) × Atividades

> **Data:** 18/09/2026 · **Acervo:** `E:\06_Backup_Local\POCO_PADF\` · **Ferramentas:** `tools/indexar_midia_exif.py`, `tools/cruzar_midia_atividades.py`, `tools/gerar_midia_json.py`

## 1. O que foi construído

| Ferramenta | Função |
|---|---|
| `tools/indexar_midia_exif.py` | Varre o acervo e extrai data de captura, GPS, modelo da câmera, dimensões, orientação e **categoria de origem** por arquivo |
| `tools/cruzar_midia_atividades.py` | Cruza as fotos com `eventos_marcados` por janela de data, com controle de ambiguidade e **métrica de concentração** |
| `tools/gerar_midia_json.py` | Publica o cruzamento em JSON consumido pelo endpoint de pautas |

Todas são somente-leitura sobre o acervo.

## 2. Indexação: 62.963 arquivos de mídia

| Fonte da data | Arquivos | Confiança |
|---|---|---|
| `nome_data` — `IMG-20250325-WA0004.jpg` | **46.750** | média (data sem hora) |
| `exif` — `DateTimeOriginal` da câmera | **1.036** | **alta** |
| `nome_arquivo` — `IMG_20240613_164329.jpg` | 160 | alta (data e hora) |
| `pasta_ano_mes` — `MIDIA\imagens\2025\03\` | 326 | baixa (só o mês) |
| `mtime` — data da cópia | 14.691 | ❌ **descartada** |

**Total com data utilizável: 47.946 fotos em 2.134 dias.**

### 2.1 Achado 1 — a data de arquivo é inútil, o nome do arquivo é ouro

As 14.691 mídias que só têm `mtime` carregam a **data da cópia**, não da captura. O risco é concreto: um agrupamento inicial sugeria "617 fotos em 26/02/2026", que era apenas um lote de miniaturas de vídeo copiadas naquele dia. Por isso `mtime` está fora do cruzamento.

O ganho veio de outro lugar. O acervo tem **37.972 imagens de WhatsApp** em `MIDIA\imagens\<ano>\<mês>\`, com nomes como `IMG-20250325-WA0004.jpg`. O extrator inicial só reconhecia nomes com hora (`IMG_20240613_164329.jpg`) e descartava essas — depois da correção, a base utilizável saltou de **944 para 47.946 fotos (50×)**.

### 2.2 Achado 2 — a pasta não é critério de confiança

A classificação por pasta mostrou que a fonte mais rica (`MIDIA\imagens\`) não estava na categoria `camera`. E o acervo tem muito ruído estrutural:

| Categoria | Arquivos | Serve para evento? |
|---|---|---|
| `outros` (cache de apps, `device\Pictures\`) | 38.141 | ❌ |
| `thumbnail` (`.thumbnails` de vídeo) | 14.030 | ❌ |
| `social` (WhatsApp, Facebook, Instagram) | 9.893 | ⚠️ só como indício de data |
| **`camera` (`DCIM\Camera`)** | **512** | ✅ |
| `screenshot` | 363 | ❌ |

**Conclusão de projeto:** a confiança vem da **fonte da data** (`fonte_data`), nunca da pasta.

### 2.3 Equipamentos nos EXIF

`21061110AG` (POCO M3 Pro 5G, 783 fotos), `Mi A3` (125), `NIKON D800`/`D5100`/`SM-M225FV` (6).

## 3. Cruzamento com as atividades

- **36 atividades** têm fotos candidatas (de 56 na base)
- **16 sem ambiguidade** estrutural
- **20 marcadas como AMBÍGUAS** — o script **não escolhe**
- **19 com alerta de qualidade** (fotos difusas ou janela longa)

### 3.1 Ressalva importante: volume ≠ precisão

A concentração mede quanto do volume cai no dia de pico. Resultado real:

| Atividade | Fotos | Dias | Pico | Leitura |
|---|---|---|---|---|
| `31` Curso Fotografia SENAI | 3.096 | 96 | 5,7% | ❌ varre 3 meses de WhatsApp, não é o curso |
| `22`/`23` Curso de DJ (turmas I e II) | 1.785 | 24 | 7,8% | ❌ difuso e inseparável entre as turmas |
| `21` FESPA 2025 | 324 | 5 | 26,5% | ⚠️ curadoria necessária |
| `9` ABF EXPO 2024 | 285 | 6 | 28,1% | ⚠️ curadoria necessária |
| `63` IX Congresso Síndrome de Down | 236 | 3 | 35,2% | ⚠️ o mais concentrado do lote |

**Nenhuma atividade passou do limiar de 40%.** Ou seja: o índice responde bem **"houve movimento nestes dias"**, mas **não** autoriza usar o conjunto inteiro como galeria do artigo. Somente as 1.036 fotos com EXIF real carregam contexto de captura (câmera e, às vezes, GPS) e podem ir direto para curadoria.

### 3.2 Lacunas de cobertura que ganharam evidência de mídia

Com o índice publicado, **cada pauta do endpoint agora informa as fotos disponíveis**. Exemplos diretos do `api_ame_pautas.php`:

| Atividade | Fotos no acervo | Flag |
|---|---|---|
| `71` CONARH 2026 | 145 | difusas — curadoria |
| `34` DOWNLANDIA no SBT | 172 | curadoria |
| `35` Curso de Automaquiagem O Boticário | 705 | janela longa |
| `33` EXPOPRINT CONVER FLEXO 2026 | 582 | janela longa |
| `37` DOWNLANDIA no MORUMBI TOWN | 101 | curadoria |
| `36` FEBRATÊXTIL 2026 | 95 | curadoria |

## 4. Próximos passos

1. **Curadoria humana por atividade.** O índice aponta a janela e a pasta; a seleção final das fotos é decisão humana — não há como automatizar isso com segurança.
2. **Ampliar o EXIF real.** Só 1.036 fotos têm data de câmera. O acervo atual é um backup antigo (Mi A3 + POCO M3); com o download do celular atual concluído, reexecutar as duas ferramentas deve elevar muito esse número. **Recomendação: usar `robocopy /COPY:DAT`** para preservar as datas na cópia.
3. **Rodar o reconhecimento facial nas atividades pendentes.** As 365 linhas de `fotos_reconhecidas` cobrem apenas 2017-2024; por isso a pauta ainda chega com `atendentes_presentes: 0`. Fluxo: definir a pasta pelo cruzamento → revisar ambíguos → `scan_events.py <evento_id> <pasta>` na VPS3.
4. **Ampliar o enrollment biométrico** — apenas **39 dos 112 candidatos** têm vetor `face_encoding`.
5. **Vídeos (`.mp4`)** não entram por EXIF; precisam de `QuickTime CreateDate`, em trilha própria. São 8.278 no acervo.

### ⚠️ LGPD

O cruzamento **não publica e não nomeia ninguém** — apenas sugere conjuntos de fotos por proximidade de data. Antes de uso público: respeitar `fotos_reconhecidas.oculta = 1` (direito de oposição já em `api_ocultar_foto.php`), ter base legal e consentimento para imagem **e** biometria, e manter **revisão humana obrigatória** (a esteira publica em `draft`).

## 5. 🔗 Conexões & Ecossistema

- [[PROJETO_AME]] · [[GEMINI]] · [[HISTORY]] · `RELATORIO_COBERTURA_BLOG_ATIVIDADES_18SET2026.md` · `PLANO_ESTEIRA_COBERTURA_BLOG_AME.md`
- Infra: `#infra/vps1` (site, `fotos_reconhecidas`, endpoint de pautas), `#infra/vps3` (`scan_events.py`, `biometria_venv`)
- Módulos: `#modulo/biometria`, `#modulo/midia`, `#modulo/crm`, `#modulo/content-factory`

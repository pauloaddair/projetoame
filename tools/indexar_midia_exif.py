#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
indexar_midia_exif.py
Indexa o acervo de midia do Projeto AME (celular / backup) por metadados EXIF,
para cruzar as fotos com as datas das atividades cadastradas em `eventos_marcados`.

Por que existe:
    O acervo em E:\\06_Backup_Local\\POCO_PADF tem ~73 mil arquivos (118 GB) sem
    nenhuma organizacao por evento. Sem um indice de data, nao ha como saber quais
    fotos pertencem a qual atividade — e sem isso o reconhecimento facial nao pode
    ser alimentado (o `scan_events.py` da VPS3 exige uma pasta por evento).

Estrategia de data (em ordem de confianca):
    1. EXIF DateTimeOriginal  — sobrevive a renomeacao/recopia
    2. EXIF DateTimeDigitized — fallback
    3. Nome do arquivo         — padrao POCO/Xiaomi: IMG_AAAAMMDD_HHMMSS.jpg
    4. mtime do sistema de arquivos — ultimo recurso, marcado como baixa confianca

Uso:
    python indexar_midia_exif.py --root "E:\\06_Backup_Local\\POCO_PADF" \
                                 --out  "...\\apoio\\midia_exif_poco_padf.csv"
    python indexar_midia_exif.py --root ... --limit 2000      # amostra rapida
"""

import os
import re
import csv
import sys
import argparse
from datetime import datetime
from concurrent.futures import ThreadPoolExecutor, as_completed

try:
    from PIL import Image, ExifTags
    Image.MAX_IMAGE_PIXELS = None  # acervo pessoal: sem risco de decompression bomb
except Exception as e:  # pragma: no cover
    print("Pillow ausente: pip install Pillow  (%s)" % e)
    sys.exit(1)

EXT_IMAGEM = {'.jpg', '.jpeg', '.png', '.webp', '.heic', '.heif', '.dng', '.tif', '.tiff'}
EXT_VIDEO = {'.mp4', '.mov', '.avi', '.mkv', '.3gp'}

# IMG_20240613_164329.jpg  /  VID_20240613_164329.mp4  /  20240613_164329.jpg
RE_NOME = re.compile(r'(20\d{2})(\d{2})(\d{2})[_\-\s]?(\d{2})(\d{2})(\d{2})')
# IMG-20250325-WA0004.jpg  /  null-20250329-WA0053.jpg  /  20250325.jpg
# (WhatsApp e afins: data SEM hora. Achado de 18/09/2026: 37.972 arquivos em
#  MIDIA\imagens\ ficavam sem data por causa disso.)
RE_NOME_DATA = re.compile(r'(20\d{2})(\d{2})(\d{2})(?!\d)')
# share_2024-06-27_04_36_33_588.jpeg — padrao do compartilhamento do Android (DCIM\share_*)
RE_NOME_DASH = re.compile(r'(20\d{2})-(\d{2})-(\d{2})[_\s](\d{2})[_:](\d{2})[_:](\d{2})')
# Pasta MIDIA\imagens\AAAA\MM — dica mais fraca, usada apenas como ultimo recurso
RE_PASTA_ANO_MES = re.compile(r'[\\/](20\d{2})[\\/](\d{2})(?:[\\/]|$)')

GPS_IFD = 34853

# Classificacao por pasta de origem. Achado de 18/09/2026: o acervo POCO_PADF tem
# 666 miniaturas de video (.thumbnails), 294 screenshots e centenas de imagens de
# Download/Facebook — nada disso e foto de evento. Sem esta separacao, o cruzamento
# por data produzia "eventos" falsos (ex.: 617 fotos em 26/02/2026 eram thumbnails).
CATEGORIAS = [
    # ORDEM IMPORTA: o primeiro marcador que casar define a categoria. Os mais
    # especificos vem antes. Cuidado: 'DCIM\Screenshots' tambem contem '\dcim\',
    # entao a categoria 'camera' NAO pode usar o marcador generico '\dcim\'.
    ('thumbnail',  ('\\.thumbnails', '\\thumbnails', '\\.thumb')),
    ('screenshot', ('\\screenshots', '\\screenrecord', 'screenshot')),
    ('social',     ('\\facebook', '\\instagram', '\\whatsapp', 'fb_img', '\\telegram')),
    ('scan',       ('\\camscanner', '\\scanner', '\\adobe scan')),
    ('download',   ('\\download', '\\bluetooth', '\\documents')),
    ('camera',     ('\\dcim\\camera', '\\dcim\\100andro', '\\dcim\\100media')),
]


def classificar(caminho):
    """Deriva a categoria da midia a partir do caminho (para filtrar ruido)."""
    alvo = caminho.lower().replace('/', '\\')
    nome = os.path.basename(alvo)
    for cat, marcas in CATEGORIAS:
        for marca in marcas:
            if marca in alvo or marca in nome:
                return cat
    return 'outros'


def _graus(valor):
    """Converte a tupla racional de GPS do EXIF em graus decimais."""
    try:
        d, m, s = valor
        d = float(d)
        m = float(m)
        s = float(s)
        return d + m / 60.0 + s / 3600.0
    except Exception:
        return None


def extrair(caminho):
    """Le um arquivo e devolve o registro de metadados."""
    nome = os.path.basename(caminho)
    ext = os.path.splitext(nome)[1].lower()
    rec = {
        'caminho': caminho,
        'arquivo': nome,
        'categoria': classificar(caminho),
        'ext': ext,
        'bytes': None,
        'mtime': None,
        'data_captura': '',
        'fonte_data': '',
        'modelo': '',
        'largura': '',
        'altura': '',
        'orientacao': '',
        'gps_lat': '',
        'gps_lon': '',
        'erro': '',
    }

    try:
        st = os.stat(caminho)
        rec['bytes'] = st.st_size
        rec['mtime'] = datetime.fromtimestamp(st.st_mtime).strftime('%Y-%m-%d %H:%M:%S')
    except Exception as e:
        rec['erro'] = 'stat: %s' % e

    # 1 e 2) EXIF
    if ext in EXT_IMAGEM:
        try:
            with Image.open(caminho) as im:
                rec['largura'] = im.width
                rec['altura'] = im.height
                exif = im.getexif() or {}

                def _tag(codigo):
                    try:
                        return exif.get(codigo)
                    except Exception:
                        return None

                dt = _tag(36867) or _tag(306)   # DateTimeOriginal | DateTime
                if dt:
                    try:
                        rec['data_captura'] = datetime.strptime(str(dt)[:19], '%Y:%m:%d %H:%M:%S') \
                            .strftime('%Y-%m-%d %H:%M:%S')
                        rec['fonte_data'] = 'exif'
                    except Exception:
                        pass

                rec['modelo'] = str(_tag(272) or '').strip()[:40]   # Model
                rec['orientacao'] = str(_tag(274) or '')            # Orientation

                try:
                    gps = exif.get_ifd(GPS_IFD) or {}
                    if gps:
                        lat, lon = _graus(gps.get(2)), _graus(gps.get(4))
                        if lat is not None and lon is not None:
                            if str(gps.get(1, 'N')).upper().startswith('S'):
                                lat = -lat
                            if str(gps.get(3, 'E')).upper().startswith('W'):
                                lon = -lon
                            rec['gps_lat'] = round(lat, 6)
                            rec['gps_lon'] = round(lon, 6)
                except Exception:
                    pass
        except Exception as e:
            rec['erro'] = 'img: %s' % str(e)[:60]

    # 3) nome do arquivo — com hora (mais preciso)
    if not rec['data_captura']:
        m = RE_NOME.search(nome)
        if m:
            try:
                rec['data_captura'] = datetime(
                    int(m.group(1)), int(m.group(2)), int(m.group(3)),
                    int(m.group(4)), int(m.group(5)), int(m.group(6))
                ).strftime('%Y-%m-%d %H:%M:%S')
                rec['fonte_data'] = 'nome_arquivo'
            except Exception:
                pass

    # 3a) nome com separadores — share_2024-06-27_04_36_33_588.jpeg
    if not rec['data_captura']:
        m = RE_NOME_DASH.search(nome)
        if m:
            try:
                rec['data_captura'] = datetime(
                    int(m.group(1)), int(m.group(2)), int(m.group(3)),
                    int(m.group(4)), int(m.group(5)), int(m.group(6))
                ).strftime('%Y-%m-%d %H:%M:%S')
                rec['fonte_data'] = 'nome_arquivo'
            except Exception:
                pass

    # 3b) nome do arquivo — apenas data (WhatsApp: IMG-20250325-WA0004.jpg)
    if not rec['data_captura']:
        m = RE_NOME_DATA.search(nome)
        if m:
            try:
                rec['data_captura'] = datetime(
                    int(m.group(1)), int(m.group(2)), int(m.group(3))
                ).strftime('%Y-%m-%d 00:00:00')
                rec['fonte_data'] = 'nome_data'
            except Exception:
                pass

    # 3c) pasta AAAA\MM — precisao de mes, so como ultimo recurso antes do mtime
    if not rec['data_captura']:
        m = RE_PASTA_ANO_MES.search(caminho.replace('/', '\\'))
        if m:
            try:
                rec['data_captura'] = '%s-%s-01 00:00:00' % (m.group(1), m.group(2))
                rec['fonte_data'] = 'pasta_ano_mes'
            except Exception:
                pass

    # 4) mtime (data de copia — baixa confianca, o cruzamento ignora)
    if not rec['data_captura'] and rec['mtime']:
        rec['data_captura'] = rec['mtime']
        rec['fonte_data'] = 'mtime'

    return rec


def main():
    ap = argparse.ArgumentParser(description='Indexa midia por EXIF para o Projeto AME')
    ap.add_argument('--root', required=True, help='pasta raiz do acervo')
    ap.add_argument('--out', required=True, help='arquivo CSV de saida')
    ap.add_argument('--workers', type=int, default=8, help='threads (default 8)')
    ap.add_argument('--limit', type=int, default=0, help='limita N arquivos (0 = todos)')
    ap.add_argument('--incluir-video', action='store_true', help='indexa video tambem (sem EXIF)')
    args = ap.parse_args()

    if not os.path.isdir(args.root):
        print('Pasta inexistente: %s' % args.root)
        sys.exit(1)

    exts = set(EXT_IMAGEM) | (EXT_VIDEO if args.incluir_video else set())

    print('Varrendo %s ...' % args.root)
    arquivos = []
    for dirpath, _dirs, files in os.walk(args.root):
        for f in files:
            if os.path.splitext(f)[1].lower() in exts:
                arquivos.append(os.path.join(dirpath, f))
                if args.limit and len(arquivos) >= args.limit:
                    break
        if args.limit and len(arquivos) >= args.limit:
            break

    print('Arquivos de midia encontrados: %d' % len(arquivos))

    campos = ['caminho', 'arquivo', 'categoria', 'ext', 'bytes', 'mtime', 'data_captura', 'fonte_data',
              'modelo', 'largura', 'altura', 'orientacao', 'gps_lat', 'gps_lon', 'erro']

    ok = 0
    com_data = 0
    por_fonte = {}
    por_categoria = {}
    por_dia = {}

    os.makedirs(os.path.dirname(args.out), exist_ok=True)
    with open(args.out, 'w', encoding='utf-8-sig', newline='') as fh:
        cw = csv.DictWriter(fh, fieldnames=campos, delimiter=';')
        cw.writeheader()
        with ThreadPoolExecutor(max_workers=args.workers) as ex:
            futuros = {ex.submit(extrair, c): c for c in arquivos}
            for i, fut in enumerate(as_completed(futuros), 1):
                try:
                    rec = fut.result()
                except Exception as e:
                    continue
                cw.writerow(rec)
                ok += 1
                por_categoria[rec['categoria']] = por_categoria.get(rec['categoria'], 0) + 1
                if rec['data_captura']:
                    com_data += 1
                    por_fonte[rec['fonte_data']] = por_fonte.get(rec['fonte_data'], 0) + 1
                    if rec['categoria'] == 'camera':
                        dia = rec['data_captura'][:10]
                        por_dia[dia] = por_dia.get(dia, 0) + 1
                if i % 5000 == 0:
                    print('  ... %d/%d processados' % (i, len(arquivos)))

    print('\n--- RESUMO ---')
    print('indexados        : %d' % ok)
    print('com data         : %d (%.1f%%)' % (com_data, 100.0 * com_data / max(1, ok)))
    print('por fonte        : %s' % por_fonte)
    print('por categoria    : %s' % por_categoria)
    print('dias distintos (apenas pasta camera): %d' % len(por_dia))
    print('CSV              : %s' % args.out)

    top = sorted(por_dia.items(), key=lambda kv: -kv[1])[:15]
    print('\nTop 15 dias de CAMERA com mais fotos (candidatos a evento):')
    for dia, n in top:
        print('  %s  %5d fotos' % (dia, n))


if __name__ == '__main__':
    main()

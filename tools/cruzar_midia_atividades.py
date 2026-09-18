#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
cruzar_midia_atividades.py
Cruza o indice EXIF do acervo de midia com as atividades cadastradas em
`eventos_marcados`, propondo quais fotos pertencem a qual atividade.

Entrada:
    --exif    CSV gerado por tools/indexar_midia_exif.py
    --base    TSV de atividades (id; nome; inicio; final; local; ...) exportado da VPS1

Regras de casamento:
    1. Somente a categoria `camera` entra no cruzamento (screenshots, thumbnails,
       downloads e imagens de redes sociais sao ruido — ver indexar_midia_exif.py).
    2. A data da foto deve cair na janela [inicio - 1 dia, final + 1 dia], para
       acomodar montagem/desmontagem do evento.
    3. Quando mais de uma atividade cobre a mesma data, o resultado e marcado como
       AMBIGUO e vai para revisao humana — o script NUNCA escolhe sozinho.
       (Ex.: 18/01/2025 tem 4 atividades simultaneas; 14 e 15/03/2024 se sobrepoem.)

Uso:
    python cruzar_midia_atividades.py --exif apoio/midia_exif_poco_padf.csv \
                                      --base scratch/atividades_prod_pos.tsv \
                                      --out  apoio/midia_por_atividade.csv
"""

import os
import csv
import io
import re
import argparse
import collections
from datetime import datetime, timedelta

CAMPOS_SAIDA = ['evento_id', 'atividade', 'inicio', 'final', 'local', 'status_match',
                'fotos', 'primeira', 'ultima', 'pastas']


def carregar_atividades(caminho):
    """Le o TSV de atividades exportado da VPS1 (colunas separadas por TAB)."""
    ats = []
    for line in io.open(caminho, encoding='utf-8'):
        m = re.match(r'^(\d+)\t([^\t]*)\t([^\t]*)\t([^\t]*)\t([^\t]*)\t(\d{4}-\d{2}-\d{2})\t(\S*)\t([^\t]*)', line)
        if not m:
            continue
        ats.append({
            'id': int(m.group(1)),
            'nome': m.group(2).strip(),
            'tipo_evento': m.group(4),
            'status': m.group(5),
            'inicio': m.group(6),
            'fim': (m.group(7) if re.match(r'^\d{4}-\d{2}-\d{2}$', m.group(7)) else m.group(6)),
            'local': m.group(8).replace('<strong>', '').replace('</strong>', '').strip(),
        })
    return ats


def main():
    ap = argparse.ArgumentParser(description='Cruza midia (EXIF) com atividades do Projeto AME')
    ap.add_argument('--exif', required=True)
    ap.add_argument('--base', required=True)
    ap.add_argument('--out', required=True)
    ap.add_argument('--tolerancia', type=int, default=1, help='dias antes/depois da janela (default 1)')
    ap.add_argument('--min-fotos', type=int, default=3, help='minimo de fotos para sugerir match (default 3)')
    args = ap.parse_args()

    atividades = carregar_atividades(args.base)
    print('atividades carregadas: %d' % len(atividades))

    # Indexa fotos por dia. Criterio de confianca: `fonte_data == 'exif'` (data real
    # de captura, gravada pela camera). NAO usar a pasta como criterio: o acervo tem
    # 666 miniaturas, 14 mil thumbnails, 4,8 mil imagens de redes sociais e 38 mil
    # caches de app (`device\Pictures\...`) cuja unica data disponivel e o mtime da
    # copia — que geraria "eventos" falsos. Descoberto em 18/09/2026.
    por_dia = collections.defaultdict(list)
    total_exif = 0
    for r in csv.DictReader(io.open(args.exif, encoding='utf-8-sig'), delimiter=';'):
        if r.get('fonte_data') != 'exif':
            continue
        d = (r.get('data_captura') or '')[:10]
        if not d:
            continue
        total_exif += 1
        por_dia[d].append(r)
    print('fotos com EXIF real (data confiavel): %d em %d dias' % (total_exif, len(por_dia)))

    if not total_exif:
        print('\nNenhuma foto com EXIF real no indice. Confira a coluna `fonte_data` '
              'do CSV gerado por indexar_midia_exif.py.')
        return

    # cobertura de cada atividade
    linhas = []
    dias_usados = collections.Counter()
    for a in sorted(atividades, key=lambda x: x['inicio']):
        ini = datetime.strptime(a['inicio'], '%Y-%m-%d').date() - timedelta(days=args.tolerancia)
        fim = datetime.strptime(a['fim'], '%Y-%m-%d').date() + timedelta(days=args.tolerancia)

        fotos = []
        dia = ini
        while dia <= fim:
            fotos.extend(por_dia.get(dia.isoformat(), []))
            dia += timedelta(days=1)

        if len(fotos) < args.min_fotos:
            continue

        # detecta ambiguidade: alguma outra atividade tambem cobre esses dias?
        concorrentes = []
        for b in atividades:
            if b['id'] == a['id']:
                continue
            bi = datetime.strptime(b['inicio'], '%Y-%m-%d').date() - timedelta(days=args.tolerancia)
            bf = datetime.strptime(b['fim'], '%Y-%m-%d').date() + timedelta(days=args.tolerancia)
            if not (bf < ini or bi > fim):
                concorrentes.append(b['id'])

        for f in fotos:
            dias_usados[(f.get('data_captura') or '')[:10]] += 1

        datas = sorted((f.get('data_captura') or '')[:10] for f in fotos)
        pastas = sorted({os.path.dirname(f['caminho']) for f in fotos})
        linhas.append({
            'evento_id': a['id'],
            'atividade': a['nome'][:70],
            'inicio': a['inicio'],
            'final': a['fim'],
            'local': a['local'][:40],
            'status_match': 'AMBIGUO (concorre com %s)' % concorrentes if concorrentes else 'ok',
            'fotos': len(fotos),
            'primeira': datas[0],
            'ultima': datas[-1],
            'pastas': ' | '.join(p.replace('E:\\06_Backup_Local\\POCO_PADF', '') for p in pastas[:3]),
        })

    linhas.sort(key=lambda r: -r['fotos'])

    os.makedirs(os.path.dirname(args.out), exist_ok=True)
    with io.open(args.out, 'w', encoding='utf-8-sig', newline='') as fh:
        cw = csv.DictWriter(fh, fieldnames=CAMPOS_SAIDA, delimiter=';')
        cw.writeheader()
        cw.writerows(linhas)

    ambiguos = [r for r in linhas if r['status_match'] != 'ok']
    print('\n--- RESULTADO ---')
    print('atividades com fotos sugeridas : %d' % len(linhas))
    print('  sem ambiguidade              : %d' % (len(linhas) - len(ambiguos)))
    print('  ambiguas (revisao humana)    : %d' % len(ambiguos))
    print('CSV                            : %s' % args.out)

    print('\nTop 12 atividades por volume de fotos:')
    for r in linhas[:12]:
        print('  #%-3s %-42s %4d fotos  [%s]' % (r['evento_id'], r['atividade'][:42], r['fotos'], r['status_match'][:22]))

    if ambiguos:
        print('\nATENCAO — matches ambiguos (o script NAO escolhe; precisa de decisao humana):')
        for r in ambiguos[:10]:
            print('  #%-3s %-40s %4d fotos  -> %s' % (r['evento_id'], r['atividade'][:40], r['fotos'], r['status_match']))

    # dias com fotos que NAO casaram com nenhuma atividade
    casados = set()
    for r in linhas:
        d = datetime.strptime(r['inicio'], '%Y-%m-%d').date() - timedelta(days=args.tolerancia)
        f = datetime.strptime(r['final'], '%Y-%m-%d').date() + timedelta(days=args.tolerancia)
        while d <= f:
            casados.add(d.isoformat())
            d += timedelta(days=1)
    orfaos = sorted(((d, n) for d, n in dias_usados.items() if d not in casados), key=lambda kv: -kv[1])[:10]
    if orfaos:
        print('\nDias com fotos de camera que NAO casam com nenhuma atividade (candidatos a atividade nao cadastrada):')
        for d, n in orfaos:
            print('  %s  %4d fotos' % (d, n))


if __name__ == '__main__':
    main()

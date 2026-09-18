# -*- coding: utf-8 -*-
"""Gera o JSON compacto de midia por atividade consumido pelo endpoint da VPS1.

Le o cruzamento EXIF x atividades e produz um mapa {evento_id: {...}} que o
api_ame_pautas.php anexa a cada pauta, para o redator saber que existem fotos
reais da atividade no acervo.
"""
import csv
import io
import json
import os

CSV_IN = r'F:\01_Projetos\Ativos\PROJETO_AME\apoio\midia_por_atividade.csv'
JSON_OUT = r'F:\01_Projetos\Ativos\PROJETO_AME\scratch\midia_por_atividade.json'

mapa = {}
for r in csv.DictReader(io.open(CSV_IN, encoding='utf-8-sig'), delimiter=';'):
    eid = int(r['evento_id'])
    pastas = [p.strip() for p in (r.get('pastas') or '').split('|') if p.strip()]
    obs = []
    if r['status_match'] != 'ok':
        obs.append(r['status_match'])
    if r.get('alerta'):
        obs.append(r['alerta'])
    mapa[str(eid)] = {
        'fotos': int(r['fotos']),
        'confiavel': r['status_match'] == 'ok' and not r.get('alerta'),
        'observacao': ' | '.join(obs),
        'concentracao_pct': float(r.get('concentracao_pct') or 0),
        'dias_distintos': int(r.get('dias_distintos') or 0),
        'primeira': r['primeira'],
        'ultima': r['ultima'],
        'pastas': pastas[:3],
    }

os.makedirs(os.path.dirname(JSON_OUT), exist_ok=True)
with io.open(JSON_OUT, 'w', encoding='utf-8') as fh:
    json.dump({
        'gerado_em': '2026-09-18',
        'fonte': 'E:\\06_Backup_Local\\POCO_PADF (EXIF DateTimeOriginal)',
        'criterio': 'apenas arquivos com fonte_data=exif; ambiguidades marcadas',
        'atividades': mapa,
    }, fh, ensure_ascii=False, indent=1)

print('atividades com midia no mapa:', len(mapa))
print('tamanho do JSON: %d bytes' % os.path.getsize(JSON_OUT))
conf = sum(1 for v in mapa.values() if v['confiavel'])
print('confiaveis: %d | ambiguas: %d' % (conf, len(mapa) - conf))

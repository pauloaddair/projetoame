<?php
/**
 * api_ame_pautas.php — Fonte de pauta "event-driven" do Projeto AME para o Content Factory.
 *
 * Rota: https://projetoame.org/include/api_ame_pautas.php
 *
 * Substitui o RSS generico (G1 Tecnologia) por pauta baseada nas ATIVIDADES reais
 * cadastradas em `eventos_marcados`, fechando o ciclo de cobertura do blog.
 *
 * AUTENTICACAO (fail-closed):
 *   Header  X-AME-Token: <segredo>
 *   O segredo vive em `/home/projetoame/ame_pautas.token` — FORA do web root,
 *   justamente para nao ser alcancavel por HTTP. Se o arquivo nao existir,
 *   o endpoint responde 503 e nao entrega nada.
 *
 * GET  ?limit=N            -> lista de atividades realizadas SEM artigo (link_artigo vazio)
 * GET  ?id=N               -> uma atividade especifica (mesmo que ja tenha link)
 * GET  ?modo=contagem      -> omite nomes de atendentes (padrao: contagem)
 *      &modo=completo      -> inclui primeiro nome dos atendentes (exige revisao humana/LGPD)
 *
 * POST { "id": N, "url": "https://..." } -> write-back do link_artigo apos publicacao
 *
 * LGPD: fotos e nomes de pessoas com deficiencia sao dado pessoal sensivel.
 *       Este endpoint NUNCA devolve registros com `fotos_reconhecidas.oculta = 1`,
 *       e o uso de nomes exige `modo=completo` explicito.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database/conexao.php';

// ------------------------------------------------------------------ helpers
function responder($codigo, $payload)
{
    http_response_code($codigo);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ------------------------------------------------------------------ auth
// Token guardado ACIMA do web root (/home/projetoame/ame_pautas.token),
// pois o .htaccess do site nao bloqueia arquivos estaticos sensiveis.
$token_file = __DIR__ . '/../../ame_pautas.token';
if (!file_exists($token_file)) {
    responder(503, ['erro' => 'Endpoint desativado: arquivo de token ausente.']);
}
$token_esperado = trim((string) file_get_contents($token_file));
if ($token_esperado === '') {
    responder(503, ['erro' => 'Endpoint desativado: token vazio.']);
}

$token_recebido = '';
if (isset($_SERVER['HTTP_X_AME_TOKEN'])) {
    $token_recebido = trim($_SERVER['HTTP_X_AME_TOKEN']);
} elseif (function_exists('getallheaders')) {
    foreach (getallheaders() as $k => $v) {
        if (strcasecmp($k, 'X-AME-Token') === 0) {
            $token_recebido = trim($v);
            break;
        }
    }
}
if ($token_recebido === '' || !hash_equals($token_esperado, $token_recebido)) {
    responder(401, ['erro' => 'Token invalido.']);
}

$metodo = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

// ================================================================== POST: write-back
if ($metodo === 'POST') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);
    if (!is_array($body) || empty($body['id']) || empty($body['url'])) {
        responder(400, ['erro' => 'Corpo esperado: {"id": N, "url": "https://..."}']);
    }

    $id  = (int) $body['id'];
    $url = trim((string) $body['url']);

    // Aceita apenas links do proprio blog do AME — evita gravacao de URL arbitraria.
    if (strpos($url, 'https://projetoame.org/home/') !== 0) {
        responder(400, ['erro' => 'A url deve comecar com https://projetoame.org/home/']);
    }
    if (strlen($url) > 255) {
        responder(400, ['erro' => 'url excede 255 caracteres.']);
    }

    $stmt = mysqli_prepare($conexao, 'SELECT id, nome, link_artigo FROM eventos_marcados WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $ev  = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    if (!$ev) {
        responder(404, ['erro' => 'Atividade nao encontrada.', 'id' => $id]);
    }

    $anterior = $ev['link_artigo'];
    $stmt = mysqli_prepare($conexao, 'UPDATE eventos_marcados SET link_artigo = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'si', $url, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        responder(500, ['erro' => 'Falha ao gravar link_artigo.']);
    }

    responder(200, [
        'ok'              => true,
        'id'              => $id,
        'atividade'       => $ev['nome'],
        'link_anterior'   => $anterior,
        'link_artigo'     => $url,
    ]);
}

// ================================================================== GET: pautas
$modo  = (isset($_GET['modo']) && $_GET['modo'] === 'completo') ? 'completo' : 'contagem';
$limit = isset($_GET['limit']) ? max(1, min(200, (int) $_GET['limit'])) : 50;

$atividades = [];

if (isset($_GET['id']) && (int) $_GET['id'] > 0) {
    $id = (int) $_GET['id'];
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT e.id, e.nome, e.slug, e.tipo_evento, e.status_evento, e.inicio, e.final,
                e.local, e.endereco, e.maps, e.uuid, e.link_artigo, i.url AS imagem_url
           FROM eventos_marcados e
           LEFT JOIN imagens i ON e.imagem_id = i.imagem_id
          WHERE e.id = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $atividades[] = $row;
    }
    mysqli_stmt_close($stmt);
} else {
    $sql = 'SELECT e.id, e.nome, e.slug, e.tipo_evento, e.status_evento, e.inicio, e.final,
                   e.local, e.endereco, e.maps, e.uuid, e.link_artigo, i.url AS imagem_url
              FROM eventos_marcados e
              LEFT JOIN imagens i ON e.imagem_id = i.imagem_id
             WHERE e.status_evento = "realizado"
               AND (e.link_artigo IS NULL OR e.link_artigo = "")
             ORDER BY e.inicio DESC
             LIMIT ' . $limit;
    $res = mysqli_query($conexao, $sql);
    if (!$res) {
        responder(500, ['erro' => 'Falha na consulta de atividades.']);
    }
    while ($row = mysqli_fetch_assoc($res)) {
        $atividades[] = $row;
    }
}

// ------------------------------------------- contexto por atividade (empresas + fotos)
$pautas = [];
foreach ($atividades as $ev) {
    $eid = (int) $ev['id'];

    // Empresas contratantes do evento (via horarios -> empresas)
    $empresas = [];
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT DISTINCT emp.nome
           FROM horarios h
           JOIN empresas emp ON h.empresa_id = emp.id
          WHERE h.evento_id = ? AND h.empresa_id > 0 AND emp.nome IS NOT NULL AND emp.nome <> ""
          ORDER BY emp.nome'
    );
    mysqli_stmt_bind_param($stmt, 'i', $eid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $empresas[] = $r['nome'];
    }
    mysqli_stmt_close($stmt);

    // Atendentes reconhecidos nas fotos do evento — SEMPRE respeitando oculta = 0
    $stmt = mysqli_prepare(
        $conexao,
        'SELECT f.candidato_id, f.foto_path, f.distancia, c.nome
           FROM fotos_reconhecidas f
           JOIN candidatos c ON f.candidato_id = c.candidato_id
          WHERE f.evento_id = ? AND f.oculta = 0
          ORDER BY f.distancia ASC'
    );
    mysqli_stmt_bind_param($stmt, 'i', $eid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $fotos         = [];
    $atendentes    = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $fotos[] = [
            'foto_path'    => $r['foto_path'],
            'candidato_id' => (int) $r['candidato_id'],
            'distancia'    => round((float) $r['distancia'], 4),
        ];
        if ($modo === 'completo') {
            $primeiro = explode(' ', trim((string) $r['nome']));
            $primeiro = isset($primeiro[0]) ? $primeiro[0] : '';
            $atendentes[(int) $r['candidato_id']] = $primeiro;
        }
    }
    mysqli_stmt_close($stmt);

    $pauta = [
        'atividade_id'        => $eid,
        'nome'                => $ev['nome'],
        'slug'                => $ev['slug'],
        'tipo_evento'         => $ev['tipo_evento'],
        'status_evento'       => $ev['status_evento'],
        'inicio'              => $ev['inicio'],
        'final'               => $ev['final'],
        'local'               => $ev['local'],
        'endereco'            => $ev['endereco'],
        'maps'                => $ev['maps'],
        'uuid_avaliacao'      => $ev['uuid'],
        'imagem_destaque'     => $ev['imagem_url'],
        'link_artigo'         => $ev['link_artigo'],
        'empresas_contratantes' => $empresas,
        'atendentes_presentes'  => count($fotos),
        'fotos_disponiveis'     => count($fotos),
        'fotos'                 => $fotos,
    ];

    if ($modo === 'completo') {
        $pauta['atendentes_primeiro_nome'] = array_values(array_unique($atendentes));
    }

    $pautas[] = $pauta;
}

// ------------------------------------------------------------------ resposta
responder(200, [
    'tenant'            => 'projetoame',
    'gerado_em'         => date('c'),
    'modo'              => $modo,
    'total_pendentes'   => count($pautas),
    'lgpd_aviso'        => 'Fotos e nomes de pessoas com deficiencia sao dado pessoal sensivel. '
                         . 'Registros com oculta=1 sao omitidos. Exige revisao humana antes de publicar.',
    'pautas'            => $pautas,
]);

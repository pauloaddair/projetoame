<?php
$app_web_root = $GLOBALS['app_web_root'] ?? '/';

if (isset($_SESSION['id']) && $_SESSION['id'] !== "") {
    $show_mode = 'all'; // 'all', 'year', 'month'
    $ano = null;
    $mes = null;

    if (isset($parametros[2]) && is_numeric($parametros[2])) {
        $ano = intval($parametros[2]);
        if (isset($parametros[3]) && is_numeric($parametros[3])) {
            $mes = intval($parametros[3]);
            $show_mode = 'month';
        } else {
            $show_mode = 'year';
        }
    }

    $saldo_anterior = 0.00;

    // Setup queries based on mode
    if ($show_mode === 'month') {
        // Calculate Saldo Anterior (cumulative balance of transactions before this month)
        $first_day = sprintf("%04d-%02d-01", $ano, $mes);
        $query_prev = "SELECT saldo FROM contabil_movimento WHERE data_prevista < '$first_day' ORDER BY data_prevista DESC, id DESC LIMIT 1";
        $res_prev = mysqli_query($conexao, $query_prev);
        if ($res_prev && mysqli_num_rows($res_prev) > 0) {
            $row_prev = mysqli_fetch_assoc($res_prev);
            $saldo_anterior = (float)$row_prev['saldo'];
        }
        
        // Select transactions in that month
        $query = "SELECT m.id, m.data_prevista, m.descricao, m.valor_previsto, m.valor_realizado, m.saldo, p.plano_ID AS plano_id, p.plano 
                  FROM contabil_movimento m 
                  LEFT JOIN contabil_plano_itens p ON m.plano_ID = p.plano_ID
                  WHERE YEAR(m.data_prevista) = $ano AND MONTH(m.data_prevista) = $mes
                  ORDER BY m.data_prevista ASC, m.id ASC";
                  
        // Set navigation variables
        $anoantes = $ano;
        $anodepois = $ano;
        $mesantes = $mes - 1;
        $mesdepois = $mes + 1;
        if ($mesantes < 1) {
            $mesantes = 12;
            $anoantes = $ano - 1;
        }
        if ($mesdepois > 12) {
            $mesdepois = 1;
            $anodepois = $ano + 1;
        }
        
        $title_text = "Extrato de " . mes($mes - 1) . " de " . $ano;
        $breadcrumbs = '
            <li class="breadcrumb-item"><a href="' . $app_web_root . 'admin">Geral</a></li>
            <li class="breadcrumb-item"><a href="' . $app_web_root . 'admin/extrato">Todos</a></li>
            <li class="breadcrumb-item"><a href="' . $app_web_root . 'admin/extrato/' . $ano . '">' . $ano . '</a></li>
            <li class="breadcrumb-item active" aria-current="page">' . mes($mes - 1) . '</li>
        ';
        
        $nav_badges = '
            <span class="badge badge-info m-1 rounded-pill"><a href="' . $app_web_root . 'admin/extrato/' . $anoantes . '/' . $mesantes . '">' . $mesantes . '/' . $anoantes . '</a></span>
            <span class="badge badge-success m-1 rounded-pill">' . $mes . '/' . $ano . '</span>
            <span class="badge badge-info m-1 rounded-pill"><a href="' . $app_web_root . 'admin/extrato/' . $anodepois . '/' . $mesdepois . '">' . $mesdepois . '/' . $anodepois . '</a></span>
        ';
    } elseif ($show_mode === 'year') {
        // Calculate Saldo Anterior (cumulative balance of transactions before this year)
        $first_day = sprintf("%04d-01-01", $ano);
        $query_prev = "SELECT saldo FROM contabil_movimento WHERE data_prevista < '$first_day' ORDER BY data_prevista DESC, id DESC LIMIT 1";
        $res_prev = mysqli_query($conexao, $query_prev);
        if ($res_prev && mysqli_num_rows($res_prev) > 0) {
            $row_prev = mysqli_fetch_assoc($res_prev);
            $saldo_anterior = (float)$row_prev['saldo'];
        }
        
        // Select transactions in that year
        $query = "SELECT m.id, m.data_prevista, m.descricao, m.valor_previsto, m.valor_realizado, m.saldo, p.plano_ID AS plano_id, p.plano 
                  FROM contabil_movimento m 
                  LEFT JOIN contabil_plano_itens p ON m.plano_ID = p.plano_ID
                  WHERE YEAR(m.data_prevista) = $ano
                  ORDER BY m.data_prevista ASC, m.id ASC";
                  
        $title_text = "Extrato do Ano de " . $ano;
        $breadcrumbs = '
            <li class="breadcrumb-item"><a href="' . $app_web_root . 'admin">Geral</a></li>
            <li class="breadcrumb-item"><a href="' . $app_web_root . 'admin/extrato">Todos</a></li>
            <li class="breadcrumb-item active" aria-current="page">' . $ano . '</li>
        ';
        
        $nav_badges = '
            <span class="badge badge-info m-1 rounded-pill"><a href="' . $app_web_root . 'admin/extrato/' . ($ano - 1) . '">Ano Anterior (' . ($ano - 1) . ')</a></span>
            <span class="badge badge-success m-1 rounded-pill">Ano ' . $ano . '</span>
            <span class="badge badge-info m-1 rounded-pill"><a href="' . $app_web_root . 'admin/extrato/' . ($ano + 1) . '">Próximo Ano (' . ($ano + 1) . ')</a></span>
        ';
    } else {
        // Show all time transactions
        $query = "SELECT m.id, m.data_prevista, m.descricao, m.valor_previsto, m.valor_realizado, m.saldo, p.plano_ID AS plano_id, p.plano 
                  FROM contabil_movimento m 
                  LEFT JOIN contabil_plano_itens p ON m.plano_ID = p.plano_ID
                  ORDER BY m.data_prevista DESC, m.id DESC";
                  
        $title_text = "Extrato Completo (Histórico Geral)";
        $breadcrumbs = '
            <li class="breadcrumb-item"><a href="' . $app_web_root . 'admin">Geral</a></li>
            <li class="breadcrumb-item active" aria-current="page">Todos</li>
        ';
        
        // Quick links for recent years
        $current_year = intval(date("Y"));
        $nav_badges = 'Filtrar por ano: ';
        for ($y = $current_year; $y >= $current_year - 4; $y--) {
            $nav_badges .= '<span class="badge badge-info m-1 rounded-pill"><a href="' . $app_web_root . 'admin/extrato/' . $y . '">' . $y . '</a></span> ';
        }
    }

    $result = mysqli_query($conexao, $query);
    ?>
    <body>
        <?php 
        include_once('./include/nav.php'); 
        include_once('./include/admin_sidebar.php'); 
        ?>
        
        <div class="container mt-4">
            <header class="p-5 bg-light rounded shadow-sm mb-4">
                <h1 class="display-5 font-weight-bold"><?php echo $title_text; ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3" style="font-size: 0.9rem;">
                        <?php echo $breadcrumbs; ?>
                    </ol>
                </nav>
                <div class="mt-2" style="font-size: 0.9rem;">
                    <?php echo $nav_badges; ?>
                </div>
            </header>
            
            <div class="card shadow mb-5">
                <div class="card-body">
                    <table id="table" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><strong>#</strong></th>
                                <th><strong>Data</strong></th>
                                <th><strong>Descrição</strong></th>
                                <th><strong>Plano</strong></th>
                                <th class="text-right"><strong>Valor</strong></th>
                                <th class="text-right"><strong>Saldo</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $i = 1;
                        
                        // Show "Saldo Anterior" for month and year views
                        if ($show_mode !== 'all') {
                            echo "<tr>
                                <td>-</td>
                                <td>-</td>
                                <td><strong>Saldo Anterior</strong></td>
                                <td>-</td>
                                <td class='text-right'>-</td>
                                <td class='text-right'><strong>R$ " . number_format($saldo_anterior, 2, ",", ".") . "</strong></td>
                            </tr>";
                            $i++;
                        }
                        
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $data_formatted = "-";
                                if ($row['data_prevista']) {
                                    $data_formatted = date("d/m/Y", strtotime($row['data_prevista']));
                                }
                                
                                $link = "<a href='" . $app_web_root . "admin/movimento/" . digitos($row['id']) . "'>" . $i . "<i class='fa fa-arrow-right ml-1 text-info'></i></a>";
                                
                                $plano_link = "-";
                                if ($row['plano_id']) {
                                    $p_ano = $ano ? $ano : date("Y");
                                    $p_mes = $mes ? $mes : date("m");
                                    $plano_link = "<a href='" . $app_web_root . "admin/conta/" . $p_ano . "/" . $p_mes . "/" . $row['plano_id'] . "'>" . htmlspecialchars($row['plano'] ?? '') . "</a>";
                                }
                                
                                // Coalesce realized and planned values
                                $val = ($row['valor_realizado'] !== null) ? (float)$row['valor_realizado'] : (float)$row['valor_previsto'];
                                
                                echo "<tr>
                                    <td>" . $link . "</td>
                                    <td>" . $data_formatted . "</td>
                                    <td>" . htmlspecialchars($row['descricao'] ?? '') . "</td>
                                    <td>" . $plano_link . "</td>
                                    <td class='text-right " . ($val < 0 ? "text-danger" : "text-success") . "'>" . number_format($val, 2, ",", ".") . "</td>
                                    <td class='text-right font-weight-bold'>" . number_format((float)$row['saldo'], 2, ",", ".") . "</td>
                                </tr>";
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>Nenhum lançamento encontrado para este período.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <?php include_once('./include/footer-database.php'); ?>
        <?php include_once('./include/admin_sidebar_footer.php'); ?>
    </body>
    <?php 
    include_once('./include/scripts.php');
} else {
    include_once('./include/restrito.php');
}
?>

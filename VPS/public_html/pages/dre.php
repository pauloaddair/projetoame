<?php
// pages/dre.php - Demonstrativo de Resultados do Exercício (Transparência Simplificada)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$titulo = "Transparência Financeira - DRE";
include_once('./include/head.php');
?>
<style>
    #dreTabs .nav-link {
        color: rgba(255, 255, 255, 0.85) !important;
        transition: all 0.2s ease-in-out;
    }
    #dreTabs .nav-link:hover {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.15);
    }
    #dreTabs .nav-link.active {
        color: #0056b3 !important; /* Azul escuro de excelente contraste */
        background-color: #fff !important;
        border-bottom: 3px solid #0056b3 !important;
    }
</style>
<?php

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: " . $GLOBALS['app_web_root'] . "login");
    exit();
}

// Query para consolidar receitas e despesas por ano e mês (ordem ASC para cálculo do saldo acumulado)
$query = "SELECT 
            YEAR(data_prevista) as ano, 
            MONTH(data_prevista) as mes,
            SUM(CASE WHEN (COALESCE(valor_realizado, valor_previsto)) > 0 THEN (COALESCE(valor_realizado, valor_previsto)) ELSE 0 END) as receitas,
            SUM(CASE WHEN (COALESCE(valor_realizado, valor_previsto)) < 0 THEN (COALESCE(valor_realizado, valor_previsto)) ELSE 0 END) as despesas
          FROM contabil_movimento
          WHERE data_prevista IS NOT NULL
          GROUP BY YEAR(data_prevista), MONTH(data_prevista)
          ORDER BY ano ASC, mes ASC";

$result = mysqli_query($conexao, $query);

$dados_dre = [];
$saldo_acumulado = 0.0;
while ($row = mysqli_fetch_assoc($result)) {
    $ano = (int)$row['ano'];
    $mes = (int)$row['mes'];
    $receitas = (float)$row['receitas'];
    $despesas = (float)$row['despesas'];
    
    $resultado_mes = $receitas + $despesas; // despesas é negativo, então é receitas - abs(despesas)
    $saldo_acumulado += $resultado_mes;
    
    $dados_dre[$ano][$mes] = [
        'receitas' => $receitas,
        'despesas' => abs($despesas),
        'resultado' => $resultado_mes,
        'saldo_acumulado' => $saldo_acumulado
    ];
}

// Inverte a ordem dos anos para exibir o ano mais recente primeiro nas abas
krsort($dados_dre);

// Função para retornar o nome do mês por extenso
function nome_mes($num_mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return $meses[$num_mes] ?? '';
}
?>
<body class="bg-light">
    <?php include_once('./include/nav.php'); ?>

    <div class="container mt-5 pt-4">
        <header class="mb-4 text-center text-lg-left">
            <h2 class="font-weight-bold"><i class="fas fa-chart-line text-success mr-2"></i>Transparência AME: Demonstrativo Mês a Mês</h2>
            <p class="text-muted">Acompanhe a consolidação simplificada de receitas, despesas e resultados financeiros do Projeto AME.</p>
        </header>

        <?php if (empty($dados_dre)): ?>
            <div class="alert alert-info text-center p-4">
                <i class="fas fa-info-circle fa-2x mb-2 text-info"></i><br>
                Nenhum dado financeiro consolidado encontrado no sistema.
            </div>
        <?php else: ?>
            <!-- Abas para selecionar o Ano -->
            <ul class="nav nav-tabs md-tabs bg-primary bg-gradient rounded mb-4" id="dreTabs" role="tablist">
                <?php 
                $is_first = true;
                foreach (array_keys($dados_dre) as $ano_aba): 
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $is_first ? 'active font-weight-bold border-bottom' : ''; ?>" 
                           id="tab-<?php echo $ano_aba; ?>-tab" 
                           data-toggle="tab" 
                           href="#tab-<?php echo $ano_aba; ?>" 
                           role="tab" 
                           aria-controls="tab-<?php echo $ano_aba; ?>" 
                           aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>">
                            Ano <?php echo $ano_aba; ?>
                        </a>
                    </li>
                <?php 
                    $is_first = false;
                endforeach; 
                ?>
            </ul>

            <!-- Conteúdo das Abas -->
            <div class="tab-content" id="dreTabsContent">
                <?php 
                $is_first = true;
                foreach ($dados_dre as $ano_aba => $meses_dados): 
                ?>
                    <div class="tab-pane fade <?php echo $is_first ? 'show active' : ''; ?>" 
                         id="tab-<?php echo $ano_aba; ?>" 
                         role="tabpanel" 
                         aria-labelledby="tab-<?php echo $ano_aba; ?>-tab">
                        
                        <div class="card shadow border-0 mb-4">
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Mês</th>
                                                <th class="text-right text-success font-weight-bold">Receitas (+)</th>
                                                <th class="text-right text-danger font-weight-bold">Despesas (-)</th>
                                                <th class="text-right font-weight-bold">Resultado do Mês</th>
                                                <th class="text-right font-weight-bold text-primary">Saldo de Caixa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $total_receitas = 0;
                                            $total_despesas = 0;
                                            $total_resultado = 0;
                                            
                                            // Ordena os meses do menor para o maior para a exibição cronológica (Janeiro a Dezembro)
                                            ksort($meses_dados);
                                            
                                            foreach ($meses_dados as $num_mes => $valores):
                                                $total_receitas += $valores['receitas'];
                                                $total_despesas += $valores['despesas'];
                                                $total_resultado += $valores['resultado'];
                                                $resultado_classe = ($valores['resultado'] >= 0) ? 'text-success' : 'text-danger';
                                                $resultado_prefixo = ($valores['resultado'] >= 0) ? '+' : '';
                                            ?>
                                                <tr>
                                                    <td class="font-weight-bold"><?php echo nome_mes($num_mes); ?></td>
                                                    <td class="text-right text-success">R$ <?php echo number_format($valores['receitas'], 2, ',', '.'); ?></td>
                                                    <td class="text-right text-danger">R$ <?php echo number_format($valores['despesas'], 2, ',', '.'); ?></td>
                                                    <td class="text-right font-weight-bold <?php echo $resultado_classe; ?>">
                                                        R$ <?php echo $resultado_prefixo . number_format($valores['resultado'], 2, ',', '.'); ?>
                                                    </td>
                                                    <td class="text-right font-weight-bold text-primary">
                                                        R$ <?php echo number_format($valores['saldo_acumulado'], 2, ',', '.'); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <!-- Totais Anuais -->
                                        <tfoot class="bg-light font-weight-bold" style="border-top: 2px solid #ddd;">
                                            <tr>
                                                <td>TOTAL ANUAL (<?php echo $ano_aba; ?>)</td>
                                                <td class="text-right text-success">R$ <?php echo number_format($total_receitas, 2, ',', '.'); ?></td>
                                                <td class="text-right text-danger">R$ <?php echo number_format($total_despesas, 2, ',', '.'); ?></td>
                                                <td class="text-right <?php echo ($total_resultado >= 0) ? 'text-success' : 'text-danger'; ?>">
                                                    R$ <?php echo ($total_resultado >= 0 ? '+' : '') . number_format($total_resultado, 2, ',', '.'); ?>
                                                </td>
                                                <td class="text-right text-primary">
                                                    <!-- Exibe o saldo acumulado no último mês do ano exibido nesta aba -->
                                                    <?php 
                                                    $ultimo_mes_registrado = max(array_keys($meses_dados));
                                                    echo "R$ " . number_format($meses_dados[$ultimo_mes_registrado]['saldo_acumulado'], 2, ',', '.'); 
                                                    ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    $is_first = false;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once('./include/footer-database.php'); ?>
</body>
<script src="<?php echo $GLOBALS['app_web_root']; ?>js/jquery-3.4.1.min.js"></script>
<script src="<?php echo $GLOBALS['app_web_root']; ?>js/bootstrap.min.js"></script>
<?php include_once('./include/end.php'); ?>

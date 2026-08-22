<?php
include __DIR__ . '/database/conexao.php';
$res = mysqli_query($conexao, "DESCRIBE contabil_movimento");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

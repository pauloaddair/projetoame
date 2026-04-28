<?php
setlocale(LC_ALL, 'pt_BR.utf8');
//  include_once('./include/conexao.php');

// Only output HTML if it's not an AJAX request
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    include_once('./include/html_head_table.php'); // Include the new file
}
?>
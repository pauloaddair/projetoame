<$json = file_get_contents("php://input");
$dados = json_decode($json, true);

if (isset($dados['transactionCode'])) {
    $transacao = $dados['transactionCode'];
    
    // Salve os dados no banco de dados
    file_put_contents("logs_pagseguro.txt", date("Y-m-d H:i:s") . " - Nova transação: " . $transacao . "\n", FILE_APPEND);

    echo "OK";
} else {
    http_response_code(400);
}

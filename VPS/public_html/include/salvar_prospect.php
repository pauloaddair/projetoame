<?php
include 'conexao.php';

$evento = $_POST['evento'] ?? '';
$expositor = $_POST['expositor'] ?? '';
$contato = $_POST['contato'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$redes = $_POST['redes'] ?? '';
$agendamento = $_POST['agendamento'] ?? date("Y-m-d",strtotime("next Monday"));
$anotacao = $_POST['anotacao'] ?? 'salvo';
$sqlAgendamento = "";
// Verificar se o evento já existe
$sqlEvento = "SELECT evento_id FROM eventos WHERE evento = ?";
//echo "1. ". $sqlEvento ."<br>";
$stmtEvento = $conexao->prepare($sqlEvento);
$stmtEvento->bind_param("s", $evento);
$stmtEvento->execute();
$resultEvento = $stmtEvento->get_result();
if ($rowEvento = $resultEvento->fetch_assoc()) {
    $evento_id = $rowEvento['evento_id'];
} else {
    $sqlInsertEvento = "INSERT INTO eventos (Evento, Inicio, Final, local) VALUES (?, NOW(), NOW(), '')";
    $stmtInsertEvento = $conexao->prepare($sqlInsertEvento);
    $stmtInsertEvento->bind_param("s", $evento);
    $stmtInsertEvento->execute();
    $evento_id = $stmtInsertEvento->insert_id;
}
//echo "2. ". $sqlEvento ."<br>";

// Verificar se o expositor (empresa) já existe
$sqlExpositor = "SELECT empresa_id FROM empresas WHERE empresa = ?";
$stmtExpositor = $conexao->prepare($sqlExpositor);
$stmtExpositor->bind_param("s", $expositor);
$stmtExpositor->execute();
$resultExpositor = $stmtExpositor->get_result();
if ($rowExpositor = $resultExpositor->fetch_assoc()) {
    $empresa_id = $rowExpositor['empresa_id'];
} else {
    $sqlInsertExpositor = "INSERT INTO empresas (empresa) VALUES (?)";
    $stmtInsertExpositor = $conexao->prepare($sqlInsertExpositor);
    $stmtInsertExpositor->bind_param("s", $expositor);
    $stmtInsertExpositor->execute();
    $empresa_id = $stmtInsertExpositor->insert_id;
}
//echo "3. ". $sqlExpositor ."<br>";

// Relacionar empresa ao evento
$sqlRelacionamento = "INSERT IGNORE INTO empresa_evento (evento_id, empresa_id) VALUES (?, ?)";
$stmtRelacionamento = $conexao->prepare($sqlRelacionamento);
$stmtRelacionamento->bind_param("ii", $evento_id, $empresa_id);
$stmtRelacionamento->execute();

// Verificar se o contato já existe
$sqlContato = "SELECT id FROM contatos WHERE empresa_id = ? AND nome = ?";
$stmtContato = $conexao->prepare($sqlContato);
$stmtContato->bind_param("is", $empresa_id, $contato);
$stmtContato->execute();
$resultContato = $stmtContato->get_result();
if ($rowContato = $resultContato->fetch_assoc()) {
    $contato_id = $rowContato['id'];
} else {
    $sqlInsertContato = "INSERT INTO contatos (empresa_id, nome, telefone, email, redes_sociais) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertContato = $conexao->prepare($sqlInsertContato);
    $stmtInsertContato->bind_param("issss", $empresa_id, $contato, $telefone, $email, $redes);
    $stmtInsertContato->execute();
    $contato_id = $stmtInsertContato->insert_id;
}
//echo "4. ".$sqlContato ."<br>";

// Criar agendamento
if (!empty($agendamento)) {
    $sqlAgendamento = "INSERT INTO agendamentos (contato_id, data_agendada) VALUES (?, ?)";
    $stmtAgendamento = $conexao->prepare($sqlAgendamento);
    $stmtAgendamento->bind_param("is", $contato_id, $agendamento);
    $stmtAgendamento->execute();
//	echo "5. ".$sqlAgendamento ."<br>";
}

// Criar anotação
if (!empty($anotacao)) {
    $sqlAnotacao = "INSERT INTO anotacoes (contato_id, anotacao) VALUES (?, ?)";
    $stmtAnotacao = $conexao->prepare($sqlAnotacao);
    $stmtAnotacao->bind_param("is", $contato_id, $anotacao);
    $stmtAnotacao->execute();
//echo "6. ".$sqlAnotacao ."<br>";
}
echo json_encode(["success" => true, "mensagem" => "Prospect salvo com sucesso!"]);
exit;
?>

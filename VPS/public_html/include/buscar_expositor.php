<?php
include 'conexao.php';

$query = $_POST['query'];
$sql = "SELECT empresa_id AS id, empresa AS nome FROM empresas WHERE empresa LIKE ? LIMIT 5";
$stmt = $conexao->prepare($sql);
$search = "%".$query."%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<a href="#" class="list-group-item list-group-item-action expositor-item" data-id="'.$row['id'].'">'.$row['nome'].'</a>';
}
include 'scripts.php';
?>
<script>
    $(".expositor-item").click(function(){
        $("#expositor").val($(this).text());
        $("#lista-expositores").hide();
    });
</script>

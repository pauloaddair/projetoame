<?php
include 'conexao.php';

$query = $_POST['evento'] ?? "";
$sql = "SELECT evento_id AS id, Evento AS nome FROM eventos WHERE Evento LIKE ? LIMIT 5";
$stmt = $conexao->prepare($sql);
$search = "%".$query."%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<a href="#" class="list-group-item list-group-item-action evento-item" data-id="'.$row['id'].'">'.$row['nome'].'</a><br>';
}
?>
<script>
    $(".evento-item").click(function(){
        $("#evento").val($(this).text());
        $("#lista-eventos").hide();
    });
</script>

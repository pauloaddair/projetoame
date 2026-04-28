<?php
// include_once('./include/head.php');
$query = "SELECT candidato_id,nome FROM candidatos ORDER BY nome;";
$resp = mysqli_query($conexao,$query);
?>
</body>
<div class="container">
<?php 
echo iconv( "ISO-8859-1","UTF-8",Date("d/M, D",strtotime('04/15/2024'))) ."<br>";
echo gmstrftime("%a, %d de %b %H:%M", strtotime('04/15/2024 10:30'));
	print ucfirst(gmstrftime('%A'))."<br>";
?>
<label for="exampleDataList" class="form-label">Datalist example</label>
<input class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Digite para buscar...">
<datalist id="datalistOptions">
	<?php 
	while ($row = mysqli_fetch_assoc($resp)){
		?>
		<option value="<?php echo $row['nome']?>">
		<?php 
	}
	?>
</datalist>
</div>
<?php 
include_once('./include/footer.php');
include_once('./include/scripts.php');
include_once('./include/end.php');
?>

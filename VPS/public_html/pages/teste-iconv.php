<?php
$titulo = "Iconv Teste";
$string = "";
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
// include_once('./include/head.php');
static $alias = array(
        'utf8' => 'utf-8',
        'ascii' => 'us-ascii',
        'tis-620' => 'iso-8859-11',
        'cp1250' => 'windows-1250',
        'cp1251' => 'windows-1251',
        'cp1252' => 'windows-1252',
        'cp1253' => 'windows-1253',
        'cp1254' => 'windows-1254',
        'cp1255' => 'windows-1255',
        'cp1256' => 'windows-1256',
        'cp1257' => 'windows-1257',
        'cp1258' => 'windows-1258',
        'shift-jis' => 'cp932',
        'shift_jis' => 'cp932',
        'latin1' => 'iso-8859-1',
        'latin2' => 'iso-8859-2',
        'latin3' => 'iso-8859-3',
        'latin4' => 'iso-8859-4',
        'latin5' => 'iso-8859-9',
        'latin6' => 'iso-8859-10',
        'latin7' => 'iso-8859-13',
        'latin8' => 'iso-8859-14',
        'latin9' => 'iso-8859-15',
        'latin10' => 'iso-8859-16',
        'iso8859-1' => 'iso-8859-1',
        'iso8859-2' => 'iso-8859-2',
        'iso8859-3' => 'iso-8859-3',
        'iso8859-4' => 'iso-8859-4',
        'iso8859-5' => 'iso-8859-5',
        'iso8859-6' => 'iso-8859-6',
        'iso8859-7' => 'iso-8859-7',
        'iso8859-8' => 'iso-8859-8',
        'iso8859-9' => 'iso-8859-9',
        'iso8859-10' => 'iso-8859-10',
        'iso8859-11' => 'iso-8859-11',
        'iso8859-12' => 'iso-8859-12',
        'iso8859-13' => 'iso-8859-13',
        'iso8859-14' => 'iso-8859-14',
        'iso8859-15' => 'iso-8859-15',
        'iso8859-16' => 'iso-8859-16',
        'iso_8859-1' => 'iso-8859-1',
        'iso_8859-2' => 'iso-8859-2',
        'iso_8859-3' => 'iso-8859-3',
        'iso_8859-4' => 'iso-8859-4',
        'iso_8859-5' => 'iso-8859-5',
        'iso_8859-6' => 'iso-8859-6',
        'iso_8859-7' => 'iso-8859-7',
        'iso_8859-8' => 'iso-8859-8',
        'iso_8859-9' => 'iso-8859-9',
        'iso_8859-10' => 'iso-8859-10',
        'iso_8859-11' => 'iso-8859-11',
        'iso_8859-12' => 'iso-8859-12',
        'iso_8859-13' => 'iso-8859-13',
        'iso_8859-14' => 'iso-8859-14',
        'iso_8859-15' => 'iso-8859-15',
        'iso_8859-16' => 'iso-8859-16',
        'iso88591' => 'iso-8859-1',
        'iso88592' => 'iso-8859-2',
        'iso88593' => 'iso-8859-3',
        'iso88594' => 'iso-8859-4',
        'iso88595' => 'iso-8859-5',
        'iso88596' => 'iso-8859-6',
        'iso88597' => 'iso-8859-7',
        'iso88598' => 'iso-8859-8',
        'iso88599' => 'iso-8859-9',
        'iso885910' => 'iso-8859-10',
        'iso885911' => 'iso-8859-11',
        'iso885912' => 'iso-8859-12',
        'iso885913' => 'iso-8859-13',
        'iso885914' => 'iso-8859-14',
        'iso885915' => 'iso-8859-15',
        'iso885916' => 'iso-8859-16',
    )
?>
<body>
	<div class="container">
<?php
	if($_SERVER['REQUEST_METHOD']=="POST"){
		$string = $_POST['string'];
		$opcao = $_POST['opcao'];
	}
?>
	<form method="post" class="form my-2">
		<label class="form-label" for="string">Digite o texto a converter</label>
		<input type="text" class="form-input" name="string" id="string" value="<?php echo $string?>"></input>
		<select name ="opcao">
			<option value="//translit" <?php echo ($opcao=="//translit")?" selected ":""; ?>>Transliteral</option>
			<option value="//ignore" <?php echo ($opcao=="//ignore") ? " selected ":""; ?>>Ignorar</option>
			<option value="" <?php echo ($opcao=="")?" selected ":""; ?>>Converter</option>
		</select>
		<button type="submit">Enviar</button>
	</form>
	<?php 
		echo "UTF-8/ascii: ". iconv('UTF-8','US-ASCII'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-11: ". iconv('UTF-8','ISO-8859-11'.$opcao,$string)."<br>";
		echo "UTF-8/windows-1251: ". iconv('UTF-8','windows-1251'.$opcao,$string)."<br>";
		echo "windows-1251/UTF-8: ". iconv('windows-1251','UTF-8'.$opcao,$string)."<br>";
		echo "windows-1251/ascii: ". iconv('windows-1251','US-ASCII'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-11: ". iconv('windows-1251'.$opcao,'ISO-8859-11',$string)."<br>";
		echo "UTF-8/windows-1251: ". iconv('UTF-8','windows-1251'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-1: ". iconv('windows-1251','iso-8859-1'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-2: ". iconv('windows-1251','iso-8859-2'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-3: ". iconv('windows-1251','iso-8859-3'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-4: ". iconv('windows-1251','iso-8859-4'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-5: ". iconv('windows-1251','iso-8859-5'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-6: ". iconv('windows-1251','iso-8859-6'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-7: ". iconv('windows-1251','iso-8859-7'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-8: ". iconv('windows-1251','iso-8859-8'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-9: ". iconv('windows-1251','iso-8859-9'.$opcao,$string)."<br>";
		echo "windows-1251/iso-8859-10: ". iconv('windows-1251','iso-8859-10'.$opcao,$string)."<br>";
		echo "ISO-8859-11/windows-1251: ". iconv('ISO-8859-11','windows-1251'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-1: ". iconv('ISO-8859-11','iso-8859-1'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-2: ". iconv('ISO-8859-11','iso-8859-2'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-3: ". iconv('ISO-8859-11','iso-8859-3'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-4: ". iconv('ISO-8859-11','iso-8859-4'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-5: ". iconv('ISO-8859-11','iso-8859-5'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-6: ". iconv('ISO-8859-11','iso-8859-6'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-7: ". iconv('ISO-8859-11','iso-8859-7'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-8: ". iconv('ISO-8859-11','iso-8859-8'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-9: ". iconv('ISO-8859-11','iso-8859-9'.$opcao,$string)."<br>";
		echo "ISO-8859-11/iso-8859-10: ". iconv('ISO-8859-11','iso-8859-10'.$opcao,$string)."<br>";
		//echo "ISO-8859-11/iso-8859-11: ". iconv('ISO-8859-11','iso-8859-11'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-1: ". iconv('UTF-8','iso-8859-1'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-2: ". iconv('UTF-8','iso-8859-2'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-3: ". iconv('UTF-8','iso-8859-3'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-4: ". iconv('UTF-8','iso-8859-4'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-5: ". iconv('UTF-8','iso-8859-5'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-6: ". iconv('UTF-8','iso-8859-6'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-7: ". iconv('UTF-8','iso-8859-7'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-8: ". iconv('UTF-8','iso-8859-8'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-9: ". iconv('UTF-8','iso-8859-9'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-10: ". iconv('UTF-8','iso-8859-10'.$opcao,$string)."<br>";
		echo "UTF-8/iso-8859-11: ". iconv('UTF-8','iso-8859-11'.$opcao,$string)."<br>";
		echo "UTF-8/cp932: ". iconv('UTF-8','cp932'.$opcao,$string)."<br>";
		echo "<br><br>";
include_once('./include/footerbt.php');
	?>
	</div>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Documento sem título</title>
</head>

<body>
<?php
	$token = "ceaaed77-d79d-4b4f-adaa-79aaac9a9e04d1c034d84bd4a3b236103bd79a6d1dd150b6-da4b-4a99-bfce-291fac0b157aoame.org";

	$url = "https://sandbox.api.pagseguro.com/balance";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Bearer $token",
		"Content-Type: application/json"
	]);

	$response = curl_exec($ch);
	curl_close($ch);

	$saldo = json_decode($response, true);
/*
	echo "<pre>";
	print_r($saldo);
	echo "</pre>";
*/
	if (isset($saldo['available'])){
		echo "Saldo disponível: " . $saldo['available'];		
	} else {
		echo "Mensage:: " . $saldo['message'];		
	}
?>
</body>
</html>
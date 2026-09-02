<?PHP
function mes($mes){
	$label = array(
	'janeiro',
	'fevereiro',
	'marco',
	'abril',
	'maio',
	'junho',
	'julho',
	'agosto',
	'setembro',
	'outubro',
	'novembro',
	'dezembro');
	if (is_numeric($mes) && $mes <12 && $mes>=0){
	return $label[$mes];
	}
	return null;
}
function semana($dia){
	$label = array(
	'domingo',
	'segunda-feira',
	'terça-feira',
	'quarta-feira',
	'quinta-feira',
	'sexta-feira',
	'sábado');
	if (is_numeric($dia) && $dia <7 && $dia>=0){
	return $label[$dia];
	}
	return null;
}
function atividade($cn, $c=0,$a=0){
// Verifica se candidato está disponível e escalado
	$query = "SELECT * FROM disponibilidade WHERE candidato_id =".$c. " AND atividade_id =".$a;
	$resp = mysqli_query($cn,$query);
	$msg = '';
	if (mysqli_num_rows($resp)>0){
		$row = mysqli_fetch_array($resp);
		$msg = "<a href='?c=".$c."&a=".$a."'><strong>&nbsp;X&nbsp;</strong></a>";
		if ($row['escalado']==1){
			$msg = "<a href='?c=".$c."&a=".$a."'><span class='badge badge-danger text-white rounded-pill p-1'><strong>&nbsp;X&nbsp;</strong></span></a>";
		}
	}
	return $msg;
}
function mask($val, $mask) {
//	$cpf = mask($details["cpf"], '###.###.###-##');
//  $cnpj = mask($details["cnpj"], '##.###.###/####-##');
    $maskared = '';
    $k = 0;
    for($i = 0; $i<=strlen($mask)-1; $i++) {
        if($mask[$i] == '#') {
            if(isset($val[$k])) $maskared .= $val[$k++];
        } else {
            if(isset($mask[$i])) $maskared .= $mask[$i];
        }
    }
    return $maskared;
}

function valida_cnpj($cnpj) {
	// Deixa o CNPJ com apenas números
	$cnpj = preg_replace('/[^0-9]/', '', $cnpj);

	// Garante que o CNPJ é uma string
	$cnpj = (string) $cnpj;

	// O valor original
	$cnpj_original = $cnpj;

	// Captura os primeiros 12 números do CNPJ
	$primeiros_numeros_cnpj = substr($cnpj, 0, 12);

	/**
	 * Multiplicação do CNPJ
	 *
	 * @param string $cnpj Os digitos do CNPJ
	 * @param int $posicoes A posição que vai iniciar a regressão
	 * @return int O
	 *
	 */
	if (!function_exists('multiplica_cnpj')) {

		function multiplica_cnpj($cnpj, $posicao = 5) {
			// Variável para o cálculo
			$calculo = 0;

			// Laço para percorrer os item do cnpj
			for ($i = 0; $i < strlen($cnpj); $i++) {
				// Cálculo mais posição do CNPJ * a posição
				$calculo = $calculo + ( $cnpj[$i] * $posicao );

				// Decrementa a posição a cada volta do laço
				$posicao--;

				// Se a posição for menor que 2, ela se torna 9
				if ($posicao < 2) {
					$posicao = 9;
				}
			}
			// Retorna o cálculo
			return $calculo;
		}

	}

	// Faz o primeiro cálculo
	$primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);

	// Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
	// Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
	$primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 : 11 - ( $primeiro_calculo % 11 );

	// Concatena o primeiro dígito nos 12 primeiros números do CNPJ
	// Agora temos 13 números aqui
	$primeiros_numeros_cnpj .= $primeiro_digito;

	// O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
	$segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
	$segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 : 11 - ( $segundo_calculo % 11 );

	// Concatena o segundo dígito ao CNPJ
	$cnpj = $primeiros_numeros_cnpj . $segundo_digito;

	// Verifica se o CNPJ gerado é idêntico ao enviado
	if ($cnpj === $cnpj_original) {
		return true;
	}
}

function valida_cpf( $cpf = false ) {
	// Exemplo de CPF: 025.462.884-23

	/**
	 * Multiplica dígitos vezes posições 
	 *
	 * @param string $digitos Os digitos desejados
	 * @param int $posicoes A posição que vai iniciar a regressão
	 * @param int $soma_digitos A soma das multiplicações entre posições e dígitos
	 * @return int Os dígitos enviados concatenados com o último dígito
	 *
	 */
	if ( ! function_exists('calc_digitos_posicoes') ) {
		function calc_digitos_posicoes( $digitos, $posicoes = 10, $soma_digitos = 0 ) {
			// Faz a soma dos dígitos com a posição
			// Ex. para 10 posições: 
			//   0    2    5    4    6    2    8    8   4
			// x10   x9   x8   x7   x6   x5   x4   x3  x2
			//   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
			for ( $i = 0; $i < strlen( $digitos ); $i++  ) {
				$soma_digitos = $soma_digitos + ( $digitos[$i] * $posicoes );
				$posicoes--;
			}

			// Captura o resto da divisão entre $soma_digitos dividido por 11
			// Ex.: 196 % 11 = 9
			$soma_digitos = $soma_digitos % 11;

			// Verifica se $soma_digitos é menor que 2
			if ( $soma_digitos < 2 ) {
				// $soma_digitos agora será zero
				$soma_digitos = 0;
			} else {
				// Se for maior que 2, o resultado é 11 menos $soma_digitos
				// Ex.: 11 - 9 = 2
				// Nosso dígito procurado é 2
				$soma_digitos = 11 - $soma_digitos;
			}

			// Concatena mais um dígito aos primeiro nove dígitos
			// Ex.: 025462884 + 2 = 0254628842
			$cpf = $digitos . $soma_digitos;

			// Retorna
			return $cpf;
		}
	}

	// Verifica se o CPF foi enviado
	if ( ! $cpf ) {
		return false;
	}

	// Remove tudo que não é número do CPF
	// Ex.: 025.462.884-23 = 02546288423
	$cpf = preg_replace( '/[^0-9]/is', '', $cpf );

	// Verifica se o CPF tem 11 caracteres
	// Ex.: 02546288423 = 11 números
	if ( strlen( $cpf ) != 11 ) {
		return false;
	}   

	// Captura os 9 primeiros dígitos do CPF
	// Ex.: 02546288423 = 025462884
	$digitos = substr($cpf, 0, 9);

	// Faz o cálculo dos 9 primeiros dígitos do CPF para obter o primeiro dígito
	$novo_cpf = calc_digitos_posicoes( $digitos );

	// Faz o cálculo dos 10 dígitos do CPF para obter o último dígito
	$novo_cpf = calc_digitos_posicoes( $novo_cpf, 11 );

	// Verifica se o novo CPF gerado é idêntico ao CPF enviado
	if ( $novo_cpf === $cpf ) {
		// CPF válido
		return true;
	} else {
		// CPF inválido
		return false;
	}
}

function timeDiff($firstTime, $lastTime) {
    // Convert para timestamps Unix
    $firstTime = strtotime($firstTime);
    $lastTime = strtotime($lastTime);

    // Realiza a subtração para obter a diferença (em segundos) entre os tempos
    $timeDiff = $lastTime - $firstTime;

    // Retorna a diferença
    return $timeDiff;
}

function apibrasilfridahh($img, $txt, $tel) {
    $to = $tel;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cluster.apigratis.com/api/v2/whatsapp/sendFile64',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "number": "' . $to . '",
            "caption": "' . $txt . '",
            "path": "' . $img . '"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'DeviceToken: 4c5204c5-8dba-4dc2-8ab8-41f407fa5629',
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9....'
        ),
    ));

    $response = curl_exec($curl);
    $resp = json_decode($response);
    curl_close($curl);
    return $resp;
}

function apibrasilNET($img, $txt, $tel) {
    $to = $tel;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cluster.apigratis.com/api/v2/whatsapp/sendFile64',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "number": "' . $to . '",
            "caption": "' . $txt . '",
            "path": "' . $img . '"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'DeviceToken: 68b3aef2-615a-4dd5-af4a-06ed38550942',
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9....'
        ),
    ));

    $response = curl_exec($curl);
    $resp = json_decode($response);
    curl_close($curl);
    return $resp;
}

function enviaWA_msg($to='5511963573778',$msg='mensagem'){

	$params=array(
	'token' => 'coc46iyy1tveqcwb',
	'to' => $to,
	'body' => $msg
	);
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.ultramsg.com/instance59827/messages/chat",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_SSL_VERIFYHOST => 0,
	  CURLOPT_SSL_VERIFYPEER => 0,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => http_build_query($params),
	  CURLOPT_HTTPHEADER => array(
		"content-type: application/x-www-form-urlencoded"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  $resposta = "cURL Error #:" . $err;
	} else {
	  $resposta = $response;
	}
	return $resposta;
}

/*
function enviaWA_img ($to='5511963573778',$msg='enviando imagem',$url)
	{
$params=array(
'token' => 'coc46iyy1tveqcwb',
'to' => "'.$to.'",
'image' => "'.$url.'",
'caption' => ".$msg."
);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.ultramsg.com/instance59827/messages/image",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => http_build_query($params),
  CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

	if ($err) {
	  $resposta = "cURL Error #:" . $err;
	} else {
	  $resposta = $response;
	}
	return $resposta;
}
*/

function formataWA($phoneNumber) 
	{
		$ddi = substr($phoneNumber,0,1);
		$i = "+";
		if ($ddi<>"+"){
			$i = "55";
		}
		// Remover caracteres não numéricos do número de telefone
		$cleanedPhoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

		// Verificar se o número possui o DDD e acrescentá-lo, caso necessário
		if (strlen($cleanedPhoneNumber) == 8) {
			$cleanedPhoneNumber = $i . '11' . $cleanedPhoneNumber;
		} elseif (strlen($cleanedPhoneNumber) == 9) {
			$cleanedPhoneNumber = $i . '11' . $cleanedPhoneNumber;
		} elseif (strlen($cleanedPhoneNumber) == 10) {
			$cleanedPhoneNumber = $i . $cleanedPhoneNumber;
		} elseif (strlen($cleanedPhoneNumber) == 11) {
		   $cleanedPhoneNumber = $i . $cleanedPhoneNumber;
	//        $cleanedPhoneNumber = '55' . substr($cleanedPhoneNumber, 1);
		}

		// Formatar o número no padrão internacional do WhatsApp
//		$formattedPhoneNumber = '+' . $cleanedPhoneNumber;

		return $cleanedPhoneNumber;
	}

function telephone($number)
	{
		$number = preg_replace('/[^0-9]/', '', $number);
		// Verificar se o número possui o DDD e acrescentá-lo, caso necessário
		if (strlen($number) <= 9) {
			$number = '11' . $number;
	
/*
		if (strlen($number) <= 8) {
			$number = '11' . $number;
		} elseif (strlen($number) == 9) {
			$number = '11' . $number;
		} elseif (strlen($number) == 10) {
			$number = $number;
		} elseif (strlen($number) == 11) {
		   $number = $number;
*/
//		if (strlen($number)<=9){
//			$number ="11".$number;
//		}
		}
		if (strlen($number) <= 11) {
		 $number="(".substr($number,0,2).") ".substr($number,2,-4)."-".substr($number,-4);
		} else {
		 $number="(".substr($number,0,4).") " . substr($number,4,-4)."-".substr($number,-4);			
		}
		// primeiro substr pega apenas o DDD e coloca dentro do (), segundo subtr pega os números do 3º até faltar 4, insere o hifem, e o ultimo pega apenas o 4 ultimos digitos
		return $number;
	}

function slugify($text, string $divider = '-',$max=20)
	{
		// replace non letter or digits by divider
		$text = preg_replace('~[^\pL\d]+~u', $divider, $text);

		// com acentos
		$comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');

		// sem acentos
		$semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

		// substitui acentos	
		$text = str_replace($comAcentos, $semAcentos, $text);

		// transliterate
		$text = iconv('UTF-8', 'ISO-8859-1//IGNORE', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, $divider);

		// remove duplicate divider
		$text = preg_replace('~-+~', $divider, $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
		return 'n-a';
		}
	
		if(strlen($text>$max)){
			$text = substr($text,0,$max);
		}

		  return $text;
	}

function formataconfirma($text)
{
	$text = "<span class='text-warning'>talvez</span>";
	if ($text=="1"){
		$text = "<span class='text-success'>sim</span>";
	}
	if ($text=="-1"){
		$text = "<span class='text-danger'>não</span>";
	}
	return($text);
}

function digitos($numero,$digitos = 3){
	if(intval($numero)>10**$digitos){
		$digitos +=3;
	}
	$numero = str_pad($numero, $digitos, '0', STR_PAD_LEFT);
	return $numero;
}

function chave($d = 3){
	$chave = bin2hex(random_bytes($d));

	return $chave;
}

/**
 * PHP encrypt and decrypt example
 *
 * Simple method to encrypt or decrypt a plain text string initialization
 * vector(IV) has to be the same when encrypting and decrypting in PHP 5.4.9.
 *
 * @link http://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
 *
 * @param string $action Acceptable values are `encrypt` or `decrypt`.
 * @param string $string The string value to encrypt or decrypt.
 * @return string
 */
function encrypt_decrypt($action, $string)
{
  $output = false;
 
  $encrypt_method = "AES-256-CBC";
  $secret_key = '!Fridahh#';
  $secret_iv = 'novaeratec';
 
  // hash
  $key = hash('sha256', $secret_key);
 
  // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a
  // warning
  $iv = substr(hash('sha256', $secret_iv), 0, 16);
 
  if ($action == 'encrypt')
  {
    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);
  }
  else
  {
    if ($action == 'decrypt')
    {
      $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
  }
 
  return $output;
}

	function strrevpos($instr, $needle)
	{
		$rev_pos = strpos (strrev($instr), strrev($needle));
		if ($rev_pos===false) return false;
		else return strlen($instr) - $rev_pos - strlen($needle);
	};


    function after ($string, $inthat)
    {
        if (!is_bool(strpos($inthat, $string)))
        return substr($inthat, strpos($inthat,$string)+strlen($string));
    };

    function after_last ($string, $inthat)
    {
        if (!is_bool(strrevpos($inthat, $string)))
        return substr($inthat, strrevpos($inthat, $string)+strlen($string));
    };

    function before ($string, $inthat)
    {
		$pos = strpos($inthat, $string);
		if ($pos === false) {
			return $inthat; // Retorna a string completa se o delimitador não for encontrado
		}
		return substr($inthat, 0, $pos);
    };

    function before_last ($string, $inthat)
    {
        return substr($inthat, 0, strrevpos($inthat, $string));
    };

    function between ($string, $that, $inthat)
    {
        return before ($that, after($string, $inthat));
    };

    function between_last ($string, $that, $inthat)
    {
     return after_last($string, before_last($that, $inthat));
    };

    function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    function formataDataEventoBR($dataString, $mostrarHorario = false) {
        if (empty($dataString)) return '';
        $timestamp = is_numeric($dataString) ? (int)$dataString : strtotime($dataString);
        if (!$timestamp) return $dataString;
        
        $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        
        $diaSemanaNum = (int)date('w', $timestamp);
        $diaSemana = $diasSemana[$diaSemanaNum] ?? '';
        
        $dia = date('d', $timestamp);
        $mesNum = (int)date('n', $timestamp);
        $mes = $meses[$mesNum] ?? date('m', $timestamp);
        
        $res = "{$dia}/{$mes} ({$diaSemana})";
        if ($mostrarHorario) {
            $res .= ' às ' . date('H:i', $timestamp);
        }
        return $res;
    }

    /**
     * Garante que as tabelas de suporte do ecossistema de escalas (presenca, avaliacoes, historico_rodizio)
     * existam com a estrutura e colunas adequadas em qualquer ambiente (Local/VPS).
     */
    function garantir_tabelas_suporte_escala($conexao) {
        if (!$conexao) return;
        try {
            // 1. Cria tabela presenca se não existir
            @mysqli_query($conexao, "CREATE TABLE IF NOT EXISTS `presenca` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `evento_id` INT NOT NULL,
                `candidato_id` INT NOT NULL,
                `presente` TINYINT(1) NOT NULL DEFAULT 1,
                `data_confirmacao` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `confirmadopor` VARCHAR(100) DEFAULT 'sistema',
                UNIQUE KEY `idx_evento_candidato` (`evento_id`, `candidato_id`),
                KEY `idx_evento` (`evento_id`),
                KEY `idx_candidato` (`candidato_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 2. Valida e adiciona colunas faltantes na tabela presenca se ela já existia
            $res_col = @mysqli_query($conexao, "SHOW COLUMNS FROM `presenca` LIKE 'presente'");
            if ($res_col && mysqli_num_rows($res_col) === 0) {
                @mysqli_query($conexao, "ALTER TABLE `presenca` ADD `presente` TINYINT(1) NOT NULL DEFAULT 1 AFTER `candidato_id`");
            }
            $res_dt = @mysqli_query($conexao, "SHOW COLUMNS FROM `presenca` LIKE 'data_confirmacao'");
            if ($res_dt && mysqli_num_rows($res_dt) === 0) {
                @mysqli_query($conexao, "ALTER TABLE `presenca` ADD `data_confirmacao` DATETIME DEFAULT CURRENT_TIMESTAMP");
            }
            $res_cp = @mysqli_query($conexao, "SHOW COLUMNS FROM `presenca` LIKE 'confirmadopor'");
            if ($res_cp && mysqli_num_rows($res_cp) === 0) {
                @mysqli_query($conexao, "ALTER TABLE `presenca` ADD `confirmadopor` VARCHAR(100) DEFAULT 'sistema'");
            }

            // 3. Cria tabela historico_rodizio se não existir
            @mysqli_query($conexao, "CREATE TABLE IF NOT EXISTS `historico_rodizio` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `candidato_id` INT NOT NULL,
                `rodizio_anterior` INT NOT NULL,
                `rodizio_novo` INT NOT NULL,
                `evento_id` INT DEFAULT NULL,
                `tipo_movimento` VARCHAR(50) DEFAULT 'pos_evento',
                `observacao` TEXT DEFAULT NULL,
                `data_registro` DATETIME DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_candidato` (`candidato_id`),
                KEY `idx_evento` (`evento_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 4. Cria tabela avaliacoes se não existir
            @mysqli_query($conexao, "CREATE TABLE IF NOT EXISTS `avaliacoes` (
                `avaliacao_id` INT AUTO_INCREMENT PRIMARY KEY,
                `evento_id` INT NOT NULL,
                `candidato_id` INT NOT NULL,
                `pontualidade` INT NOT NULL DEFAULT 0,
                `asseio` INT NOT NULL DEFAULT 0,
                `socializacao` INT NOT NULL DEFAULT 0,
                `simpatia` INT NOT NULL DEFAULT 0,
                `compreensao_instrucoes` INT NOT NULL DEFAULT 0,
                `facilidade_orientacoes` INT NOT NULL DEFAULT 0,
                `foco_atividades` INT NOT NULL DEFAULT 0,
                `comportamento_geral` INT NOT NULL DEFAULT 0,
                `observacoes` TEXT NULL,
                `avaliador_nome` VARCHAR(128) NULL,
                `avaliador_email` VARCHAR(128) NULL,
                `data_avaliacao` DATETIME DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_ev_cand` (`evento_id`, `candidato_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (Throwable $e) {
            // Silencia para não interromper fluxo principal
        }
    }

?>
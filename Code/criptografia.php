<?php


	//$chave = addslashes($_POST['chave']);
	$chave = $_POST['chave'];
	//$metodo = addslashes($_POST['metodo']);	
	$metodo = $_POST['metodo'];
	$entrada = $_POST['entrada'];
	$crip = $_POST['crip'];
	$tipo = $_POST['tipo'];
	
	if (isset($_POST['aleat'])){
		$chave = openssl_random_pseudo_bytes(32);
	}
	
	$VI='';
	
	if ($metodo == "CBC"){ 
		$Cifra =  'AES-256-CBC';
		$VI = openssl_random_pseudo_bytes(openssl_cipher_iv_length($Cifra));
		
	} else $Cifra = 'AES-256-ECB';
	
	if($tipo=="0"){
		$atalho = 'arquivos/'.$entrada;
		$entrada = file_get_contents($atalho);
	}
	
	if($crip=="1"){
		//criptografando
		$cripto = 'criptografada';
		$TextoCriptografado = openssl_encrypt($entrada, $Cifra, $chave, OPENSSL_RAW_DATA ,$VI);
		$TextoCriptografado = base64_encode($TextoCriptografado);
		$VI = base64_encode($VI);
		$CriptoExibir = $TextoCriptografado.':'.$VI;
		$textoFinal = $CriptoExibir;
		//inverso
		$Resultado = explode(':', $CriptoExibir);
		if ($metodo == "CBC") {
			$VI = base64_decode($Resultado[1]);
		}
		$TextoCriptografado = base64_decode($Resultado[0]);
		$DescriptoExibir = openssl_decrypt($TextoCriptografado, $Cifra, $chave, OPENSSL_RAW_DATA , $VI);
	}else {
		//descriptografando
		$cripto = 'descriptografada';
		$Resultado = explode(':', $entrada);
		if ($metodo == "CBC") {
			$VI = base64_decode($Resultado[1]);
		}
		$TextoCriptografado = base64_decode($Resultado[0]);
		$DescriptoExibir = openssl_decrypt($TextoCriptografado, $Cifra, $chave, OPENSSL_RAW_DATA , $VI);		
		$textoFinal = $DescriptoExibir;
		//inverso
		$TextoCriptografado = openssl_encrypt($DescriptoExibir, $Cifra, $chave, OPENSSL_RAW_DATA ,$VI);
		$CriptoExibir = base64_encode($TextoCriptografado).':'.base64_encode($VI);
	}
		
	//retorno final
	if($tipo=="1"){
		echo '<br><center><h3>Sua palavra foi ' .$cripto.' com sucesso! <br> Chave:<b> ' .utf8_encode($chave).'</b><br><br>Palavra Criptografada: <b>' .$CriptoExibir.' </b><br><br> Palavra Inicial: <b>' .$DescriptoExibir. '</b><h3></center><br><br>';
		//print_r(openssl_get_cipher_methods());
	} else {
		$atalho =  explode('.', $atalho);
		$atalhoFinal = $atalho[0].''.$cripto.'.'.$atalho[1];	
		file_put_contents($atalhoFinal, $textoFinal);
		echo '<br><center><h3>O Arquivo foi modificado com sucesso, favor verificar em: <b>'.$atalhoFinal.'</b><h3></center><br><br>';
	
	}
?>


<?php
function encryptDecrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = "";
    $secret_iv = "";
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
function checkValidUUID($file,$uuid){
	$searchfor = $uuid;
	$fileContents = file_get_contents($file);
	$linesEncrypted = explode("\n", $fileContents);
	foreach ($linesEncrypted as $line){
		$linesDrecryped[] = encryptDecrypt("decrypt", $line);
	}
	foreach ($linesDrecryped as $line) {
		$linesCsv[] = str_getcsv($line);
	}
	foreach ($linesCsv as $index => $line){
		if ($searchfor == $line[4]){
			return(TRUE);
		} 
	}
	return(FALSE);
}
function getUserData($file,$uuid){
	$fileContents = file_get_contents($file);
	$linesEncrypted = explode("\n", $fileContents);
	foreach ($linesEncrypted as $line){
		$linesDrecryped[] = encryptDecrypt("decrypt", $line);
	}
	foreach ($linesDrecryped as $line) {
		$linesCsv[] = str_getcsv($line);
	}
	$searchfor = $uuid;
	foreach ($linesCsv as $index => $line){
		if ($searchfor == $line[4]){
			$userdata = $line;
			return($userdata);
		}
	}
}
	
?>
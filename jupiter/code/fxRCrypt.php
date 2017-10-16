<?php
$rijnKey = "\x1\x2\x3\x4\x5\x6\x7\x8\x9\x10\x11\x12\x13\x14\x15\x16";
$rijnIV = "\x1\x2\x3\x4\x5\x6\x7\x8\x9\x10\x11\x12\x13\x14\x15\x16";
function Decrypt($s){
	global $rijnKey, $rijnIV;

	if ($s == "") { return $s; }

	// Turn the cipherText into a ByteArray from Base64
	try {
		$s = str_replace("BIN00101011BIN", "+", $s);
		$s = base64_decode($s);
		$s = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $rijnKey, $s, MCRYPT_MODE_CBC, $rijnIV);
		} catch(Exception $e) {
		// There is a problem with the string, perhaps it has bad base64 padding
		// Do Nothing
	}
	return $s;
}

function Encrypt($s){
	global $rijnKey, $rijnIV;

	// Have to pad if it is too small
	$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
	$pad = $block - (strlen($s) % $block);
	$s .= str_repeat(chr($pad), $pad);

	$s = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $rijnKey, $s, MCRYPT_MODE_CBC, $rijnIV);
	$s = base64_encode($s);
	$s = str_replace("+", "BIN00101011BIN", $s);
	return $s;
}
?>
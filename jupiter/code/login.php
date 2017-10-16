<?php
	$action = 'login';
	$username = $_POST["eUser"];
	$password = $_POST["ePass"];

	include_once('fxModelo.php');
	$username = strtolower(htmlentities($username, ENT_QUOTES));
	$password = crypt($password, SALT);

	if ($action === 'login'){

/*		$db = DataBase::getInstance();

		$sql = 'SELECT `Password` FROM `tp_embajadores` WHERE `Email` = "%s" LIMIT 1;';
		$query = sprintf($sql, $username);

		$db->setQuery($query);
		$resultados = $db->loadObjectList();

		if (count($resultados)>0){
*/

		$rpta = '0';
		if ($username == '12345678'){
			if ($password !== 'demo'){
				session_unset();
				session_destroy();
				$rpta = '5'; // Bad
			} else {
				$_SESSION['userEmail'] = $username;
				$_SESSION["fchOLD"] = date("Y-n-j H:i:s"); // Now
				$_SESSION['k'] = strtoupper(md5(uniqid(rand(), true)));	//Cte random

				$rpta = '2'; // Ok
			}
		}

		if ($username == '00000000') {
			if ($password !== 'admin'){
				session_unset();
				session_destroy();
				$rpta = '3'; // Bad
			} else {
				$_SESSION['userEmail'] = $username;
				$_SESSION["fchOLD"] = date("Y-n-j H:i:s"); // Now
				$_SESSION['k'] = strtoupper(md5(uniqid(rand(), true)));	//Cte random

				$rpta = '4'; // Ok
			}
		}
		echo $rpta;
}
?>
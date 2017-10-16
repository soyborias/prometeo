<?php
	include_once('config.php');

	if (isset($_SESSION['username'])){

		if ( $_SESSION['rol'] == ROL_USER){
			// Usuario
			header ('Location: dashboard.php');
		} elseif ( $_SESSION['rol'] ==  ROL_SUPERVISOR) {
			// Supervisor
			header ('Location: dashboard-admin.php');
		} elseif ( $_SESSION['rol'] ==  ROL_JEFE) {
			// Jefe
			header ('Location: dashboard-admin.php');
		} elseif ( $_SESSION['rol'] ==  ROL_ADMIN) {
			// Admin
			header ('Location: dashboard-admin.php');
		} else {
			// Unkonw
			header ('Location: index.php');
		}

	} else {
		// User No logeado
		header ('Location: index.php');
	}
?>

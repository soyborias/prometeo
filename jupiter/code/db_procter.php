<?php 
	include_once('MysqliDb.php');
	$mysqli = new mysqli (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$db = new MysqliDb ($mysqli);
?>
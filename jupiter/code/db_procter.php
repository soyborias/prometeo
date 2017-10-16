<?php
  defined('DB_HOST') || define('DB_HOST','');
  defined('DB_USER') || define('DB_USER','');
  defined('DB_PASSWORD') || define('DB_PASSWORD','');
  defined('DB_NAME') || define('DB_NAME','');
  
  include_once('MysqliDb.php');
	$mysqli = new mysqli (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$db = new MysqliDb ($mysqli);
?>
<?php
  defined('CANONICAL') || define('CANONICAL','aprendexyz.com');
  defined('MSG_OK')    || define('MSG_OK','ok');
  defined('MSG_ERROR') || define('MSG_ERROR','error');

  defined('DB_HOST') || define('DB_HOST','');
  defined('DB_USER') || define('DB_USER','');
  defined('DB_PASSWORD') || define('DB_PASSWORD','');
  defined('DB_NAME') || define('DB_NAME','db_');

  defined('SALT') || define('SALT','t630i2h52087347sRL7xuNFQO9Q');
  defined('KEY1') || define('KEY1','8pG7a85xl8waeF6DMd05V5H62wD70gd6rs1x29eA');
  defined('KEY2') || define('KEY2','D4Vn6mXFmTxd6PhxclA9AON9pv5Z1eSh1cIPApVX');

  defined('ROL_NEW')   || define('ROL_NEW',-1);
  defined('ROL_USER')  || define('ROL_USER',1);
  defined('ROL_SUPERVISOR') || define('ROL_SUPERVISOR',2);
  defined('ROL_JEFE')  || define('ROL_JEFE',3);
  defined('ROL_ADMIN') || define('ROL_ADMIN',5);

  defined('VIDEO_CURSO') || define('VIDEO_CURSO',1);
  defined('VIDEO_TEMA')  || define('VIDEO_TEMA',5);

  defined('S3_KEY')    || define('S3_KEY','');
  defined('S3_SECRET') || define('S3_SECRET','');
  defined('S3_PATH')   || define('S3_PATH', '');
  defined('S3_BUCKET_STATIC') || define('S3_BUCKET_STATIC', '');

  defined('MANDRILL_APIKEY')  || define('MANDRILL_APIKEY', '');
  defined('IMAGES_DEFAULT')   || define('IMAGES_DEFAULT', "{'normal':'1.png','gana':'2.png','feedback':'3.png','pierde':'4.png','celebra':'5.png'}");

  $debug = '';
  @session_start();
  include_once('jupiter/code/fxCrypt.php');
?>

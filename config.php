<?php
  defined('CANONICAL') || define('CANONICAL','http://entrenatepg.com');
  defined('MSG_OK')    || define('MSG_OK','ok');
  defined('MSG_ERROR') || define('MSG_ERROR','error');

  defined('DB_HOST') || define('DB_HOST','procterdb.ckfzjwj6fges.us-east-1.rds.amazonaws.com');
  defined('DB_USER') || define('DB_USER','pg_userdata');
  defined('DB_PASSWORD') || define('DB_PASSWORD','M4st3rPr0ct3r.4132');
  defined('DB_NAME') || define('DB_NAME','db_procter');

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

  defined('S3_KEY')    || define('S3_KEY','AKIAJE5NLCPMLXGHWPUA');
  defined('S3_SECRET') || define('S3_SECRET','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');
  defined('S3_PATH')   || define('S3_PATH', 'http://staticprocter.s3.amazonaws.com/');
  defined('S3_BUCKET_STATIC') || define('S3_BUCKET_STATIC', 'staticprocter');

  defined('MANDRILL_APIKEY')  || define('MANDRILL_APIKEY', '6sL6Ba9oZCsqOae7wQLQvQ');
  defined('IMAGES_DEFAULT')   || define('IMAGES_DEFAULT', "{'normal':'57ef17c2eed30_1475286978.png','gana':'57ef17c2eed30_1475286978.png','feedback':'57ef17c2eed30_1475286978.png','pierde':'57ef17c2eed30_1475286978.png','celebra':'57ef17c2eed30_1475286978.png'}");

  $debug = '';
  @session_start();
  include_once('jupiter/code/fxCrypt.php');
?>

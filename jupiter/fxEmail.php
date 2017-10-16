<?php
$email_valid = false;
if (filter_var($email_destiny, FILTER_VALIDATE_EMAIL)) {
  $email_valid = true;
}

$email_send = false;
switch ($actionEmail) {
  case 'emailActivateUser':
    include_once('tpl-email/tpl-email-bienvenida.php');
    $template = str_replace('*|MC:NOMBRE|*', $email_nombre, $template);
    $email_send = true;
    $subject = 'Bienvenido a Entrenate P&G';
    break;

  case 'emailRecoveryPass':
    include_once('tpl-email/tpl-email-recovery.php');
    $template = str_replace('*|MC:NOMBRE|*', $email_nombre, $template);
    $template = str_replace('*|MC:LINK|*', $email_link, $template);
    $email_send = true;
    $subject = 'Recuperar Contraseña Entrenate P&G';
    break;
};

if ($email_send && $email_valid){
  $to      = $email_destiny;
  $from    = 'noreply@entrenatepg.com';

  $uri     = 'https://mandrillapp.com/api/1.0/messages/send.json';
  $api_key = MANDRILL_APIKEY;

  $postString = '{
  "key": "' . $api_key . '",
  "message": {
    "html": "' . $template . '",
    "subject": "' . $subject . '",
    "from_email": "' . $from . '",
    "from_name": "' . $from . '",
    "to": [
      { "email": "' . $to . '",  "name": "' . $to . '" }
   ],
    "track_opens": true,
    "track_clicks": true,
    "auto_text": true,
    "url_strip_qs": true,
    "preserve_recipients": true
  },
    "async": false
  }';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $uri);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
  $email_result = curl_exec($ch);
  $email_json = json_decode($email_result, true);
};
?>
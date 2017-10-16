<?php
include_once('config.php');

if(isset($_GET['logout'])){
  session_unset();
  session_destroy();
}

$title = 'Aprende XYZ';
?>
<!doctype html>
<html class="no-js" lang="es">
  <head>
    <?php include_once('code/header.php'); ?>
  </head>
  <body class="bgAzukMain">

    <main id="page" role="main" class="main full100">

    <!-- LOGIN -->
    <section id="login">

      <div class="frame">
        <h1>Aprende</h1>
        <img src="static/images/logo.svg" alt="xyz" class="logo"><br/><br/>

        <form id="frmLogin" action="" autocomplete="off">
          <div class="row collapse postfix-round">
            <div class="small-9 columns">
              <input id="txtUsuario" type="text" placeholder="Usuario" class="transparent azulSub" aria-label="Usuario" tabindex="1" required autocomplete="off" />
            </div>
            <div class="small-3 columns">
              <a href="#" class="button postfix transparent fs15"><i class="fa fa-users"></i></a>
            </div>
          </div>

          <div class="row collapse postfix-round">
            <div class="small-9 columns">
              <input id="txtPass" type="password" placeholder="Contraseña" class="transparent azulSub" aria-label="Contraseña" tabindex="2" required />
            </div>
            <div class="small-3 columns">
              <a href="#" class="button postfix transparent fs15"><i class="fa fa-key"></i></a>
            </div>
          </div>

          <button type="submit" tabindex="4" class="small round button success">INGRESAR</button>
          <span id="msgbox"></span><br/>
        </form>

        <a href="#" data-reveal-id="myRecovery" class="blanco" tabindex="5">¿Olvidaste tu contraseña?</a>

        <hr/>
        <p>&nbsp;</p>
      </div>

      <footer>
        <?php include_once('code/footer.php'); ?>
      </footer>

    </section>

    </main>

<div id="myRecovery" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">RECUPERAR CONTRASEÑA</h2>
  <form id="frmRecovery">
    <label>Escribe tu correo para enviarte los pasos para recuperar tu contraseña
      <input id="txtEmailRecovery" type="text" placeholder="Email" required autocomplete="off" />
    </label>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    <button type="submit" class="button round success">Enviar</button> <span class="msgbox pLeft5"></span>
  </form>
</div>

<?php include_once('code/script.php'); ?>
<script src="js/login.js?v=1"></script>
<script>
  $(document).on('ready', iniciar);
  var ajaxReq = 'jupiter/api.php';
  var auth = "";

  function iniciar(){
    $(document).foundation();

    $('#frmRecovery').on('submit', sendRecovery)
  };

  function sendRecovery(e){
    e.preventDefault();
    var emailRecovery = $('#txtEmailRecovery').val();
    var $msgbox = $('#frmRecovery span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');

    $.post(ajaxReq, {action:"sendRecovery", eAuth:auth, eEmail:emailRecovery, eAgent:navigator.userAgent, rand:Math.random()},
    function(data){
      $msgbox.html(' Gracias, en breve le llegará a su correo las instrucciones.');
    });
  };
</script>
</body>
</html>

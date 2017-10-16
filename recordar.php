<?php
include_once('config.php');
$a = (isset($_GET['a'])) ? $_GET['a'] : '';
$b = (isset($_GET['b'])) ? $_GET['b'] : '';
$email = base64_decode($a);

$title = 'Recuperar Contraseña P&G';
?>
<!doctype html>
<html class="no-js" lang="es">
  <head>
    <?php include_once('code/header.php'); ?>
  </head>
  <body class="bgAzukMain">

    <main id="page" role="main" class="main full100">

    <!-- REGISTRO  -->
    <section>

      <div class="row pTop1">
        <div class="small-11 medium-8 large-6 small-centered medium-centered large-centered columns">

          <img src="static/images/isotipo.png" alt="P&G">
          <h1 class="blanco">RECUPERAR CONTRASEÑA</h1>

          <?php if ( strlen($a)<3 ) { ?>
            <p>Escribe tu email para enviarte un link para restablecer tu contraseña</p>
            <div class="row collapse postfix-round">
              <div class="small-9 columns">
                <input id="txtEmail" type="email" placeholder="Correo" class="transparent azulSub" aria-label="Correo" tabindex="1" />
              </div>
              <div class="small-3 columns">
                <a href="#" class="button postfix transparent fs15"><span class="lnr lnr-envelope"></span></a>
              </div>
            </div><br/>
            <a href="#" class="small round button success" tabindex="2" data-reveal-id="myBlock">RECUPERAR</a><br/>

          <?php } else { ?>
            <p>Escribe tu nueva contraseña para <?php print($email); ?></p>
            <form id="frmChangePass">
              <div class="row collapse postfix-round">
                <div class="small-9 columns">
                  <input id="txtPass1" type="password" placeholder="Contraseña" class="transparent azulSub" aria-label="Contraseña" tabindex="1" required />
                </div>
                <div class="small-3 columns">
                  <a href="#" class="button postfix transparent fs15"><i class="fa fa-lock"></i></a>
                </div>
              </div>

              <div class="row collapse postfix-round">
                <div class="small-9 columns">
                  <input id="txtPass2" type="password" placeholder="Repetir Contraseña" class="transparent azulSub" aria-label="Contraseña" tabindex="2" required />
                </div>
                <div class="small-3 columns">
                  <a href="#" class="button postfix transparent fs15"><i class="fa fa-lock"></i></a>
                </div>
              </div><br/>

              <div class="row collapse">
                <button id="cmdGuardarPass" class="medium round button success" tabindex="3">GUARDAR</button>
                <span id="msgbox"></span><br/>
              </div>
            </form>

          <?php } ?>

        </div>
      </div>

    </section>

    </main>

<?php include_once('code/script.php'); ?>
<script>
  $(document).on('ready', iniciar);
  var ajaxReq = "jupiter/api.php";
  var auth = 0;
  var a = "<?php print($a); ?>"
  var b = "<?php print($b); ?>"

  function iniciar(){
    $(document).foundation();

    $("#frmChangePass").on("submit", function(event){
        event.preventDefault();
        sendChangePass();
    });
  };

  function sendChangePass(){
    var pass1 = $("#txtPass1").val();
    var pass2 = $("#txtPass2").val();

    if (pass1 !== pass2){
      $('#msgbox').html('<i class="fa fa-exclamation-triangle"></i> Error: Las Contraseñas no coinciden.');
      return false;
    }

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando ...');
    $.post(ajaxReq, {action:"userChangePass", eAuth:auth, eVarA:a, eVarB:b, ePass:pass1, rand:Math.random()},
    function(data){
        if ( data.status == 'ok' ){
          // OK!
          $('#msgbox').html('¡Felicitaciones! Su contraseña ha sido actualizada.');
          window.setTimeout(slowLogin, 6000);
          limpiar();
        } else {
          //$('#msgbox').html('Msg: [ ' + data.info + ' ]');
          $('#msgbox').html('Error: Error General');
        }
    });
  };

  function slowLogin(){
    window.location.href = '/';
  };

  function limpiar () {
    $('#frmRegistro input').removeClass('err');

    $('#txtPass1').val('');
    $('#txtPass2').val('');
    $('#txtPass1').focus();
  };
</script>
  </body>
</html>

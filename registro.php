<?php
include_once('config.php');

$title = 'Registro P&G';
?>
<!doctype html>
<html class="no-js" lang="es">
  <head>
    <?php include_once('code/header.php'); ?>
  </head>
  <body class="bgAzukMain">

  <main id="page" role="main" class="main full100">

    <!-- REGISTRO  -->
    <section class="">

      <div class="row pTop1">
        <div class="small-11 medium-8 large-6 small-centered medium-centered large-centered columns">

          <a href="/"><img src="static/images/isotipo.png" alt="P&G"></a>
          <h1 class="blanco">Registro P&G</h1>

          <form id="frmRegistro" class="frm" method="post" action="">
            <div class="row collapse postfix-round">
              <div class="small-9 columns">
                <input id="txtDni" type="text" placeholder="DNI" class="transparent azulSub" aria-label="DNI" tabindex="1" required autofocus />
              </div>
              <div class="small-3 columns">
                <a href="#" class="button postfix transparent fs15"><i class="fa fa-list-alt"></i></a>
              </div>
            </div>

            <div class="row collapse postfix-round">
              <div class="small-9 columns">
                <input id="txtNombre" type="text" placeholder="Nombre y Apellido" class="transparent azulSub" aria-label="Nombre" tabindex="2" required />
              </div>
              <div class="small-3 columns">
                <a href="#" class="button postfix transparent fs15"><i class="fa fa-user"></i></a>
              </div>
            </div>

            <div class="row collapse postfix-round">
              <div class="small-9 columns">
                <input id="txtEmail" type="email" placeholder="Correo" class="transparent azulSub" aria-label="Correo" tabindex="3" required />
              </div>
              <div class="small-3 columns">
                <a href="#" class="button postfix transparent fs15"><i class="fa fa-envelope-o"></i></a>
              </div>
            </div>

            <div class="row collapse postfix-round">
              <div class="small-9 columns">
                <input id="txtPass1" type="password" placeholder="Contraseña" class="transparent azulSub" aria-label="Contraseña" tabindex="4" required />
              </div>
              <div class="small-3 columns">
                <a href="#" class="button postfix transparent fs15"><i class="fa fa-lock"></i></a>
              </div>
            </div>

            <div class="row collapse postfix-round">
              <div class="small-9 columns">
                <input id="txtPass2" type="password" placeholder="Repetir Contraseña" class="transparent azulSub" aria-label="Contraseña" tabindex="5" required />
              </div>
              <div class="small-3 columns">
                <a href="#" class="button postfix transparent fs15"><i class="fa fa-lock"></i></a>
              </div>
            </div><br/>

            <div class="row collapse">
              <button type="submit" class="medium round button success" tabindex="6">REGISTRAR</button>
              <span id="msgbox"></span><br/>
            </div>
          </form>

        </div>
      </div>

    </section>

  </main>

<div id="myBlock" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">Registrar.</h2>
  <p class="lead">Registrar Nuevo Usuario</p>
  <p>El registro de nuevos usuarios se habilitara en las proximas fechas</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>
<script>
  $(document).on('ready', iniciar);
  var ajaxReq = "jupiter/api.php";
  var auth = 0;

  function iniciar(){
    $(document).foundation();

    $("#frmRegistro").on("submit", function(event){
        event.preventDefault();
        sendRegistro();
    });
  };

  function sendRegistro(){
    var dni = $("#txtDni").val();
    var nom = $("#txtNombre").val();
    var email = $("#txtEmail").val();
    var pass1 = $("#txtPass1").val();
    var pass2 = $("#txtPass2").val();

    if (pass1 !== pass2){
      $('#msgbox').html('<i class="fa fa-exclamation-triangle"></i> Error: Las Contraseñas no coinciden.');
      return false;
    }

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando ...');
    $.post(ajaxReq, {action:"newUserRegister", eAuth:auth, eDni:dni, eNom:nom, eEmail:email, ePass:pass1, rand:Math.random()},
    function(data){
        if ( data.status == 'ok' ){
          // OK!
          $('#msgbox').html('¡Gracias! por registrarte a &#171;Entrénate PG&#187;. Tus datos serán validados y en breve llegará un correo de confirmación.');
          window.setTimeout(slowLogin, 6000);
          limpiar();
        } else {
          //$('#msgbox').html('Msg: [ ' + data.info + ' ]');
          $('#msgbox').html('Ya estabas registrado con esos datos');
        }
    });
  };

  function slowLogin(){
    window.location.href = '/';
  }

  function limpiar () {
    $('#frmRegistro input').removeClass('err');

    $('#txtDni').val('');
    $('#txtNombre').val('');
    $('#txtEmail').val('');
    $('#txtPass1').val('');
    $('#txtPass2').val('');
    $('#txtDni').focus();
  };
</script>
  </body>
</html>

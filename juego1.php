<?php
include_once('config.php');
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);
$game = 'j1-'. $pid;

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  // Permisos
  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_juegos1', $pid, $db);
  $status  = ($enroll['status'] !== 'No')  ?  1  :  0;

  if (!isset($_SESSION[$game])){
    $_SESSION[$game]['current'] = 0;
    $_SESSION[$game]['history'] = array(0);
    $_SESSION[$game]['retry']   = 0;
    $_SESSION[$game]['puntos']  = 0;
    $_SESSION[$game]['n1'] = juegoStatusICO('play');
    $_SESSION[$game]['n2'] = juegoStatusICO('lock');
    $_SESSION[$game]['n3'] = juegoStatusICO('lock');
    $_SESSION[$game]['n4'] = juegoStatusICO('lock');
    $_SESSION[$game]['n5'] = juegoStatusICO('lock');
  }
  // Get Values
  $j1 = $_SESSION[$game]['n1'];
  $j2 = $_SESSION[$game]['n2'];
  $j3 = $_SESSION[$game]['n3'];
  $j4 = $_SESSION[$game]['n4'];
  $j5 = $_SESSION[$game]['n5'];

  // Last level
  $current =  $_SESSION[$game]['current'];
  $juego_puntos  = '+'. $_SESSION[$game]['puntos'];
  $puntos2 = ( isset($_SESSION['j2-'. $pid]['puntos']) ) ? $_SESSION['j2-'. $pid]['puntos'] : 0;
  $puntos3 = ( isset($_SESSION['j3-'. $pid]['puntos']) ) ? $_SESSION['j3-'. $pid]['puntos'] : 0;
  $juego2_puntos = '+'. $puntos2;
  $juego3_puntos = '+'. $puntos3;

  // Vars
  $title = 'Juego P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $linaje  = getLinajeOne(0, 0, $pid, $db);
  $bread   = genBreadCrumbs('dashboard', 4, 'Desafio 1', null, $linaje);
} else {
  // User No logeado
  header ('Location: index.php');
}
?>
<!doctype html>
<html class="no-js" lang="es">
  <head>
    <?php include_once('code/header.php'); ?>
  </head>
  <body>

    <!-- Nav -->
    <nav class="top-bar" data-topbar role="navigation">
      <?php include_once('code/top-bar-title.php'); ?>

      <section class="top-bar-section">
        <?php include_once('code/top-bar-user.php'); ?>
      </section>
    </nav>

    <!-- Menu -->
    <div class="left-sidebar show-for-large-up">
      <?php print($mnuMain); ?>
    </div>


  <main id="page" role="main" class="main mIzq45">

    <section>
      <div class="row pTop1">
        <div class="medium-12 columns">

          <div class="text-left">
            <ul class="breadcrumbs mDown0">
              <?php print $bread; ?>
            </ul>
            <h1 class="m0 azulMain">DESAFIO 1 <span class="round label"><?php print $juego_puntos; ?></span> <span class="round label"><?php print $juego2_puntos; ?></span></h1>
            <h5 class="subheader azulSub">INSTRUCCIONES: Tienes 5 niveles, supera cada nivel y pasa al siguiente desafío.</h5>
          </div>
          <hr class="mTop0" />

          <div class="row">
            <div id="panelJuegos">
              <img src="static/images/juegos/escenario1.jpg" alt="Juego">
              <a id="btnGame1" href="#" rel="1"><?php print $j1 ?></a>
              <a id="btnGame2" href="#" rel="2"><?php print $j2 ?></a>
              <a id="btnGame3" href="#" rel="3"><?php print $j3 ?></a>
              <a id="btnGame4" href="#" rel="4"><?php print $j4 ?></a>
              <a id="btnGame5" href="#" rel="5"><?php print $j5 ?></a>
            </div>
          </div>

        </div>
      </div>

    <footer>
      <?php include_once('code/footer.php'); ?>
    </footer>

    </section>

  </main>
<div id="myBlock" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">¡Bloqueado!</h2>
  <p class="lead">Aun no tienes acceso</p>
  <p>Completa el contenido anterior para poder desbloquear este contenido.</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<div id="NoPuedes" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">NO PUEDES!</h2>
  <div>
    <p class="lead">Sigue la secuencia para pasar al siguiente nivel.</p>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>
<script>
  $(document).foundation();
  var current = parseInt(<?php echo $current; ?>) + 1;
  var item    = 1;
  var estado  = parseInt(<?php print $status; ?>);
  var link  = "juego1-a.php?rel=";
  var pidE  = "<?php print $pidE ;?>";

  $('#btnGame1, #btnGame2, #btnGame3, #btnGame4, #btnGame5').on('click', clickSgte);

  function clickSgte(e){
    e.preventDefault();
    item = $(this).attr('id');
    item = parseInt(item.substring(7));

    if (estado == 0){
      $('#myBlock').foundation('reveal', 'open');
      return false;
    } else {
      if ( item <= current ){
        document.location = link + $(this).attr('rel') + '&pid=' + pidE;
        return true;
      } else {
        $('#NoPuedes').foundation('reveal', 'open');
        return false;
      }
    }
  }

</script>

  </body>
</html>
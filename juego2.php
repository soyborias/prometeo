<?php
include_once('config.php');
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);
$game = 'j2-'. $pid;


if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  // Permisos
  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_juegos2', $pid, $db);
  $status  = ($enroll['status'] !== 'No')  ?  1  :  0;

  if (!isset($_SESSION[$game])){
    $_SESSION[$game]['current'] = 1;
    $_SESSION[$game]['history'] = array(0);
    $_SESSION[$game]['retry']   = 0;
    $_SESSION[$game]['puntos']  = 0;
    $_SESSION[$game]['n1'] = juegoStatusICO('lock');
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

  // Set last level
  $ready = juegoStatusICO('win');
  $n = 0;
  $n += ( $j1 == $ready ) ? 1 : 0;
  $n += ( $j2 == $ready ) ? 1 : 0;
  $n += ( $j3 == $ready ) ? 1 : 0;
  $n += ( $j4 == $ready ) ? 1 : 0;
  $n += ( $j5 == $ready ) ? 1 : 0;

  $last = ( $n >= 4 ) ? '&last' : '';
  $juego_puntos  = '+'. $_SESSION[$game]['puntos'];
  $puntos1 = ( isset($_SESSION['j1-'. $pid]['puntos']) ) ? $_SESSION['j1-'. $pid]['puntos'] : 0;
  $puntos3 = ( isset($_SESSION['j3-'. $pid]['puntos']) ) ? $_SESSION['j3-'. $pid]['puntos'] : 0;
  $juego1_puntos = '+'. $puntos1;
  $juego3_puntos = '+'. $puntos3;

  // Vars
  $title = 'Juego P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $linaje  = getLinajeOne(0, 0, $pid, $db);
  $bread   = genBreadCrumbs('dashboard', 4, 'Desafio 2', null, $linaje);
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
            <h1 class="m0 azulMain">DESAFIO 2 <span class="round label"><?php print $juego1_puntos; ?></span> <span class="round label"><?php print $juego_puntos; ?></span></h1>
            <h5 class="subheader azulSub">INSTRUCCIONES: Completa todos los niveles para convertirte en un EXPERTO PAMPERS</h5>
          </div>
          <hr class="mTop0" />

          <div class="row">
            <div id="panelJuegos">
              <img src="static/images/juegos/escenario2.jpg" alt="Juego">
              <a id="btnGame11" href="#" rel="1"><?php print $j1 ?></a>
              <a id="btnGame12" href="#" rel="2"><?php print $j2 ?></a>
              <a id="btnGame13" href="#" rel="3"><?php print $j3 ?></a>
              <a id="btnGame14" href="#" rel="4"><?php print $j4 ?></a>
              <a id="btnGame15" href="#" rel="5"><?php print $j5 ?></a>
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

<?php include_once('code/script.php'); ?>
<script>
  $(document).on('ready', iniciar);
  var estado  = parseInt(<?php print $status; ?>);
  var link  = "juego1-c.php?rel=";
  var last  = "<?php print $last; ?>";
  var pidE  = "<?php print $pidE ;?>";

  function iniciar(){
    $(document).foundation();

    $('#btnGame11').on('click', clickSgte);
    $('#btnGame12').on('click', clickSgte);
    $('#btnGame13').on('click', clickSgte);
    $('#btnGame14').on('click', clickSgte);
    $('#btnGame15').on('click', clickSgte);
  };

  function clickSgte(e){
    e.preventDefault();
    item = $(this).attr('id');
    item = parseInt(item.substring(7));

    if (estado == 0){
      $('#myBlock').foundation('reveal', 'open');
      return false;
    } else {
      document.location = link + $(this).attr('rel') + '&pid=' + pidE + last;
      return true;
    }
  }

</script>

  </body>
</html>
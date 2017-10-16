<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once('config.php');
$pidE  = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid   = decrypt($pidE, $_SESSION['k']);
$rel   = (isset($_GET['rel'])) ? $_GET['rel'] : -1;
$retry = (isset($_GET['retry'])) ? 1 : 0;

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $game = 'j2-'. $pid;
  if ( $rel == 1 ) { $_SESSION[$game]['history'] = array(0); };
  $_SESSION[$game]['history'] = ( empty($_SESSION[$game]['history']) )   ?  array(0) :  $_SESSION[$game]['history'];
  $_SESSION[$game]['history'] = ( count($_SESSION[$game]['history'])>7 ) ?  array(0) :  $_SESSION[$game]['history'];
  $repetidos  = implode(',', $_SESSION[$game]['history']);
  $resultados = getJuegoRnd($pid, 2, 1, $repetidos, $db);
  $nextRel    = ($rel < 5) ? $rel + 1 : 5;

  // Permisos
  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_juegos2', $pid, $db);
  $status  = $enroll['status'];

  $gameID     = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_id'])          : '';
  $question   = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_pregunta'])    : '';
  $c1         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_distractor1']) : '';
  $c2         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_distractor2']) : '';
  $c3         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_respuesta'])   : '';
  $c4         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_distractor3']) : '';
  $pista      = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_pista'])       : '';
  $feedback   = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_feedback'])    : '';
  $portada    = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_portada'])     : '';
  $fondo      = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_fondo'])       : '';

  $_SESSION[$game]['puntos']  = ( isset($_SESSION[$game]['puntos']) ) ? $_SESSION[$game]['puntos'] : 0;
  $juego_puntos  = '+'. $_SESSION[$game]['puntos'];

  $portada    = ( strlen($portada)>3 )  ?  $portada  :  'portada01.jpg';
  $portada    = genPathPicture($portada, 'juegos/');

  $fondo      = ( strlen($fondo)>3 )  ?  $fondo  :  'fondo01.jpg';
  $fondo      = genPathPicture($fondo, 'juegos/');

  $perfil     = 'vendedor';

  $rptas = array( 'a' => $c1, 'b' => $c2, 'c' => $c3, 'd' => $c4 );
  $rpta  = array_rand($rptas);

  $temp1 = ''; $temp2 = '';
  if ($rpta != 'c'){
    $temp1 = $rptas['c'];
    $temp2 = $rptas[$rpta];
    $rptas['c']   = $temp2;
    $rptas[$rpta] = $temp1;
  }
  $ok = 'box-'. $rpta;

  $_SESSION[$game]['retry']  = ( isset($_SESSION[$game]['retry']) ) ? $_SESSION[$game]['retry'] : 0;
  $retry  = ( $retry === 0 ) ? 0 : $_SESSION[$game]['retry'];
  $debug  = ''; //$ok. ' :: '. $retry;

  $puntos = '<h3>...</h3><h4><i class="fa fa-spinner fa-spin"></i> Puntos</h4>';

  // Vars
  $mTbl  = 'pg_juegos2';
  $title = 'Desafio 2 P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $mnuMainMobile = crearMnuMainMobile('dashboard', null);
  $linaje  = getLinajeOne(0, 0, $pid, $db);
  $arrGame = array('url' => 'juego02.php?rel='. $rel. '&pid='. $pidE, 'link' => 'Desafio 2' );
  $bread   = genBreadCrumbs('dashboard', 5, 'Desafio 1', $arrGame, $linaje);
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

<div class="off-canvas-wrap" data-offcanvas>
  <div class="inner-wrap">

    <!-- Off Canvas Menu -->
    <aside class="left-off-canvas-menu">
      <?php print($mnuMainMobile); ?>
    </aside>

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
              <?php print($bread); ?>
            </ul>
            <h1 class="m0 azulMain">DESAFIO 2 <span class="round label"><?php print $juego_puntos; ?></span></h1>
            <h5 class="subheader azulSub"></h5>
          </div>
          <hr class="mTop0" />

          <div class="row">
            <div id="juego1" class="hide">
              <img src="<?php print($portada); ?>" alt="juego">
              <a id="btnJugar1" href="#" class="btnJugar button large round success btnShadow">Empezar Juego</a>
            </div>

            <div id="juego2" class="hide">
              <img src="<?php print($fondo); ?>" alt="juego">
              <div id="caja1" class="suelta" class="suelta btn btn-default" role="button">
                <span><?php print($question); ?></span>
              </div>
              <a href="box-a" class="box box1"><?Php print($rptas['a']); ?></a>
              <a href="box-b" class="box box2"><?Php print($rptas['b']); ?></a>
              <a href="box-c" class="box box3"><?Php print($rptas['c']); ?></a>
              <a href="box-d" class="box box4"><?Php print($rptas['d']); ?></a>
            </div>
          </div>

        </div>
      </div>

    </section>

    <footer>
      <?php print $debug; ?>
      <?php include_once('code/footer.php'); ?>
    </footer>

  </main>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="Instrucciones" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h3>¡INSTRUCCIONES!</h3>
  <div>
    <p class="lead">Clic a la respuesta correcta para pasar al siguiente nivel.</p>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoNext" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" style="background: url('/code/show-image.php?p=ok&i=celebra') top center no-repeat;">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <p>Superaste los niveles, ahora pasa al siguiente juego</p>
    <a href="tema.php?pid=<?php print($pidE);?>" class="btnMsg button large round info">Avanzar</a>
  </div>
</div>

<div id="juegoOk" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" style="background: url('/code/show-image.php?p=ok&i=gana') top center no-repeat;">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <a href="juego02.php?rel=<?php print($nextRel); ?>&pid=<?php print($pidE);?>" class="btnMsg button large round info">Avanzar</a>
  </div>
</div>

<div id="juegoPista" class="reveal-modal msgAlert" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog"  style="background: url('/code/show-image.php?p=alert&i=feedback') top center no-repeat;">
  <div class="msgDialog">
    <h3>¡SIGUE INTENTANDO!</h3><hr/>
    <p><strong>PISTA:</strong> <?php print($pista); ?></p>
    <a href="#" id="btnPista" class="btnMsg button large round info">Reintentar</a>
  </div>
</div>

<div id="juegoFeedback" class="reveal-modal msgBad" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog"  style="background: url('/code/show-image.php?p=bad&i=pierde') top center no-repeat;">
  <div class="msgDialog">
    <h3>¡VUELVE A INTENTARLO!</h3><hr/>
    <p><?php print($feedback); ?></p><hr/>
    <a href="tema.php?pid=<?php print $pidE; ?>&retry=<?php print $pid; ?>" class="btnMsg button large round info">Reiniciar</a>
  </div>
</div>

<?php include_once('code/script.php'); ?>
<script src="js/jquery.pep.min.js"></script>
<script>
  $(document).on('ready', iniciar);
  var n  = 0;
  var ajaxReq = "jupiter/api.php";
  var puntos  = 0;
  var ok    = "<?php print($ok); ?>";
  var msg   = '';
  var rel   = parseInt("<?php print $rel; ?>");
  var auth  = 0;
  var retry = <?php print $retry; ?>;
  var pid   = <?php print $pid; ?>;
  var mp3Pista = new Audio('static/sound/pista.mp3');
  var mp3Ok    = new Audio('static/sound/ok.mp3');
  var mp3Bad   = new Audio('static/sound/bad.mp3');
  var game   = 'j2-';
  var gameID = "<?php print $gameID; ?>";
  var mTbl   = "<?php print $mTbl; ?>";
  var nextTB = "pg_juegos3";
  var mID    = "<?php print $pid; ?>";
  var linaje = "<?php print $linaje[0]['linaje']; ?>";
  var estado = "<?php print $status; ?>";
  var fin    = false;

  function iniciar(){
    $(document).foundation();
    $('#btnJugar1').on('click', abrirJuego2);
    $('#btnPista').on('click', goPista);
    $('#juego2 a').on('click', jugar);

    if (rel>1){
      $( "#juego1" ).hide();
      $( "#juego2" ).show();
    } else {
      $( "#juego1" ).show();
      $( "#juego2" ).hide();
    }
  };

  function abrirJuego2(){
     $( "#juego1" ).fadeOut( "slow" );
     $( "#juego2" ).fadeIn( "slow" );
  }

  function abrirJuego3(){
     $( "#juego2" ).fadeOut( "slow" );
     $( "#juego3" ).fadeIn( "slow" );
  }

  function goPista(){
    $('#juegoPista').foundation('reveal', 'close');
    retry = 1;
    //$( "#juego2" ).fadeOut( "slow" );
    //$( "#juego1" ).fadeIn( "slow" );
    return false;
  };

  // Game
  function jugar(e){
    e.preventDefault();
    var res = $(this).attr('href');

    if (res === ok){
      grabarWin();  // Ganar
      //$.pep.toggleAll(false); // Desactivar
    } else {
      n++;
      if (retry == 1){
        grabarFeedback();
      }
      if ( n == 1 ){
        grabarPista();
      } else {
        grabarFeedback();
      }
    }
  }

  function grabarWin(){
    mp3Ok.play();
    if (rel == 5) {
      fin = true;
      grabarGame("saveGameWinFull", $('#juegoNext'));
    } else {
      grabarGame("saveGameWin", $('#juegoOk'));
      $('#juegoOk').foundation('reveal', 'open');
    }
    //$(".arrastrable").draggable('disable');
  };

  function grabarFeedback(){
    grabarGame("saveGameFail", $('#juegoFeedback'));
    mp3Bad.play();
    $('#juegoFeedback').foundation('reveal', 'open');
    //$(".arrastrable").draggable('disable');
  };

  function grabarPista(){
    grabarGame("saveGamePista", $('#juegoPista'));
    mp3Pista.play();
    $('#juegoPista').foundation('reveal', 'open');
  };

  function grabarGame(action, modal){
    $.post(ajaxReq, {action:action, eAuth:auth, ePid:pid, eRel:rel, eGame:game, eGameId:gameID, eStatus:estado, rand:Math.random()},
      function(data){
        modal.find('h3').text(data.rpta.msg);
        modal.find('h4').text(data.pje + ' Puntos');

        if (fin) {
          grabarAccion('juegoFinal2', 19);
          $('#juegoNext').foundation('reveal', 'open');
          fin = false;
        }
    });
  };

  function grabarAccion(hito, hitoID){
    $.post(ajaxReq, {action:"updateAction", eAuth:auth, eHito:hito, eGame:game, eHitoID:hitoID, eTabla:mTbl, eTblNext:nextTB, eRel:mID, eLinaje:linaje, eStatus:estado, ePid:pid, rand:Math.random()},
    function(data){
      var pje = parseInt($('#lblPuntaje span').text());
      $('#lblPuntaje span').text( pje + parseInt(data.rpta) );
    });
  };
</script>

  </body>
</html>

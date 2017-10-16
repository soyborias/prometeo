<?php
include_once('config.php');
$pidE  = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid   = decrypt($pidE, $_SESSION['k']);
$rel   = (isset($_GET['rel'])) ? $_GET['rel'] : -1;
$retry = (isset($_GET['retry'])) ? 1 : 0;

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $game = 'j1-'. $pid;
  $repetidos  = implode(',', $_SESSION[$game]['history']);
  $resultados = getJuegoRnd($pid, 1, 1, $repetidos, $db);

  // Permisos
  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_juegos1', $pid, $db);
  $status  = $enroll['status'];

  $gameID     = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_id'])    : '';
  $question   = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_pregunta'])    : '';
  $c1         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_distractor1']) : '';
  $c2         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_distractor2']) : '';
  $c3         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_respuesta'])   : '';
  $c4         = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_distractor3']) : '';
  $ganancia1  = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_ganancia1'])   : '';
  $ganancia2  = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_ganancia2'])   : '';
  $perdida1   = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_perdida1'])    : '';
  $perdida2   = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_perdida2'])    : '';
  $pista      = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_pista'])       : '';
  $feedback   = ( count($resultados) ) ? html_entity_decode($resultados[0]['juego_feedback'])    : '';

  $rptas = array( 'a' => $c1, 'b' => $c2, 'c' => $c3, 'd' => $c4 );
  $rpta  = array_rand($rptas);

  $temp1 = ''; $temp2 = '';
  if ($rpta != 'c'){
    $temp1 = $rptas['c'];
    $temp2 = $rptas[$rpta];
    $rptas['c']   = $temp2;
    $rptas[$rpta] = $temp1;
  }
  $ok = 'caja-'. $rpta;

  $retry  = ( $retry === 0 ) ? 0 : $_SESSION[$game]['retry'];
  $debug  = ''; //$ok. ' :: '. $retry;

  $puntos = '<h3>...</h3><h4><i class="fa fa-spinner fa-spin"></i> Puntos</h4>';

  // Vars
  $mTbl  = 'pg_juegos1';
  $title = 'Desafio 1 P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $linaje  = getLinajeOne(0, 0, $pid, $db);
  $arrGame = array('url' => 'juego1.php?pid='. $pidE, 'link' => 'Desafio 1' );
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
            <h1 class="m0 azulMain">DESAFIO 1</h1>
            <h5 class="subheader azulSub">INSTRUCCIONES: Arrastra la respuesta que mas se ajusta, al área punteada, para pasar al siguiente nivel.</h5>
          </div>
          <hr class="mTop0" />

          <div class="row">

            <div id="juego">
              <img src="static/images/juegos/juego01.jpg" alt="juego">

              <div id="caja1" class="suelta" class="suelta btn btn-default" role="button">
                <h6 class="orange">¡Arrastra Aqui!</h6>
                <span><?php print($question); ?></span>
              </div>

              <div id="caja-a" class="objeto arrastrable cajaColor">
                <?Php print($rptas['a']); ?>
              </div>

              <div id="caja-b" class="objeto arrastrable cajaColor">
                <?Php print($rptas['b']); ?>
              </div>

              <div id="caja-c" class="objeto arrastrable cajaColor">
                <?Php print($rptas['c']); ?>
              </div>

              <div id="caja-d" class="objeto arrastrable cajaColor">
                <?Php print($rptas['d']); ?>
              </div>
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
<div id="Instrucciones" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h3>¡INSTRUCCIONES!</h3>
  <div>
    <p class="lead">Arrastra la respuesta correcta al área punteada para pasar al siguiente nivel.</p>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoNext" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <p>Superaste los 5 niveles, ahora pasa al 2do juego</p>
    <a href="juego2.php?pid=<?php print($pidE);?>" class="btnMsg button large round info">Avanzar</a>
  </div>
</div>

<div id="juegoOk" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <p><strong>BENEFICIO AL CLIENTE:</strong> <?php print($ganancia1); ?><br/>
      <strong>BENEFICIO AL VENDEDOR:</strong> <?php print($ganancia2); ?></p>
    <a href="juego1.php?pid=<?php print($pidE);?>" class="btnMsg button large round info">Avanzar</a>
  </div>
</div>

<div id="juegoPista" class="reveal-modal msgAlert" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <h3>¡SIGUE INTENTANDO!</h3><hr/>
    <p><strong>PISTA:</strong> <?php print($pista); ?></p>
    <a href="#" id="btnPista" class="btnMsg button large round info">Reintentar</a>
  </div>
</div>

<div id="juegoFeedback" class="reveal-modal msgBad" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <h3>¡VUELVE A INTENTARLO!</h3><hr/>
    <p><?php print($feedback); ?></p><hr/>
    <p><strong>PERJUICIO AL CLIENTE:</strong> <?php print($perdida1); ?></br>
      <strong>PERJUICIO AL VENDEDOR:</strong> <?php print($perdida2); ?></p>
    <a href="juego1.php?pid=<?php print $pidE; ?>&retry=<?php print $pid; ?>" class="btnMsg button large round info">Reiniciar</a>
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
  var rel   = "<?php print $rel; ?>";
  var auth  = 0;
  var retry = <?php print $retry; ?>;
  var pid   = <?php print $pid; ?>;
  var mp3Pista = new Audio('static/sound/pista.mp3');
  var mp3Ok    = new Audio('static/sound/ok.mp3');
  var mp3Bad   = new Audio('static/sound/bad.mp3');
  var game   = 'j1-';
  var gameID = "<?php print $gameID; ?>";
  var mTbl   = "<?php print $mTbl; ?>";
  var nextTB = "pg_juegos2";
  var mID    = "<?php print $pid; ?>";
  var linaje = "<?php print $linaje[0]['linaje']; ?>";
  var estado = "<?php print $status; ?>";

  function iniciar(){
    $(document).foundation();
    $("#btnPista").on('click', goPista);
  };

  function goPista(){
    $('#juegoPista').foundation('reveal', 'close');
    retry = 1;
    return false;
  };

  // Game
  $('#juego .arrastrable').pep({
    useCSSTranslation: false,
    constrainTo: 'parent',
    droppable: '.suelta',

    initiate: function(ev, obj) {
      $(".suelta").addClass("borderRojo");
    },
    start: function(ev, obj){
      obj.noCenter = false;
    },
    drag: function(ev, obj){
      var vel = obj.velocity();
      var rot = (vel.x)/5;
      rotate(obj.$el, rot)
    },
    stop: function(ev, obj){
      $(".suelta").removeClass("borderRojo");
      rotate(obj.$el, 0)
    },
    rest: handleGame
  });

  function handleGame(ev, obj){
    if ( obj.activeDropRegions.length > 0 ) {
        var draggableId = obj.$el.attr("id");

        if (draggableId === ok){
          grabarWin();  // Ganar
          $.pep.toggleAll(false); // Desactivar
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
          obj.revert(); // Restore position
        }

    }
  };

  function rotate($obj, deg){
    $obj.css({
        "-webkit-transform": "rotate("+ deg +"deg)",
           "-moz-transform": "rotate("+ deg +"deg)",
            "-ms-transform": "rotate("+ deg +"deg)",
             "-o-transform": "rotate("+ deg +"deg)",
                "transform": "rotate("+ deg +"deg)"
      });
  };

  function grabarWin(){
    mp3Ok.play();
    if (rel == 5) {
      grabarGame("saveGameWinFull", $('#juegoNext'));
      grabarAccion('juegoFinal1', 18);
      $('#juegoNext').foundation('reveal', 'open');
    } else {
      grabarGame("saveGameWin", $('#juegoOk'));
      $('#juegoOk').foundation('reveal', 'open');
    }
    $(".arrastrable").draggable('disable');
  };

  function grabarFeedback(){
    grabarGame("saveGameFail", $('#juegoFeedback'));
    mp3Bad.play();
    $('#juegoFeedback').foundation('reveal', 'open');
    $(".arrastrable").draggable('disable');
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
    });
  };

  function grabarAccion(hito, hitoID){
    $.post(ajaxReq, {action:"updateAction", eAuth:auth, eHito:hito, eGame:game, eHitoID:hitoID, eTabla:mTbl, eTblNext:nextTB, eRel:mID, eLinaje:linaje, eStatus:estado, ePid:pid, rand:Math.random()},
    function(data){
      //var pje = parseInt($('#lblPuntaje span').text());
      //$('#lblPuntaje span').text( pje + parseInt(data.rpta) );
    });
  };
</script>

  </body>
</html>
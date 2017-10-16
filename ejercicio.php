<?php
include_once('config.php');
$try  = (isset($_GET['try'])) ? $_GET['try'] : 0;
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  // Permisos
  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_juegos3', $pid, $db);
  $status  = ($enroll['status'] !== 'No')  ?  1  :  0;
  $status2 = $enroll['status'];

  $numAcciones = 5;
  $resAcciones = getAccionesRnd($pid, $numAcciones, $db);
  $accionesJS  = genAccionesJS($resAcciones);
  $arrAcciones = json_encode($accionesJS);

  $puntosP  = getPuntajeHitoFull('ejercicioFinalPerfecto' ,$db);
  $puntosN  = getPuntajeHitoFull('ejercicioFinalNormal' ,$db);
  $puntosA  = getPuntajeHitoFull('ejercicioAccion' ,$db);

  $puntos1 = ( isset($_SESSION['j1-'. $pid]['puntos']) ) ? $_SESSION['j1-'. $pid]['puntos'] : 0;
  $puntos2 = ( isset($_SESSION['j2-'. $pid]['puntos']) ) ? $_SESSION['j2-'. $pid]['puntos'] : 0;
  $puntos3 = ( isset($_SESSION['j3-'. $pid]['puntos']) ) ? $_SESSION['j3-'. $pid]['puntos'] : 0;
  $juego1_puntos = '+'. $puntos1;
  $juego2_puntos = '+'. $puntos2;
  $juego3_puntos = '+'. $puntos3;


  // Vars
  $mTbl  = 'pg_juegos3';
  $title = 'Ejercicio P&G';
  $puntos     = '<h3>...</h3><h4><i class="fa fa-spinner fa-spin"></i> Puntos</h4>';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $linaje  = getLinajeOne(0, 0, $pid, $db);
  $linajeFull = getLinajeFull($db);
  $nexLink    = genNextLink($linaje[0]['linaje'], $linajeFull);
  $nexLinaje  = genNextLinaje($linaje[0]['linaje'], $linajeFull);
  $arrGame = array('url' => 'ejercicio.php?pid='. $pidE, 'link' => 'Desafio 3' );
  $bread   = genBreadCrumbs('dashboard', 5, 'Desafio 3', $arrGame, $linaje);
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
            <h1 class="m0 azulMain">DESAFIO 3: EJERCICIO DE CORRELACIÓN
              <span class="round label"><?php print $juego1_puntos; ?></span>
              <span class="round label"><?php print $juego2_puntos; ?></span>
              <span class="round label" id="lblPuntos3"><?php print $juego3_puntos; ?></span>
            </h1>
            <h5 class="subheader azulSub">INSTRUCCIONES: Dale click a los números para ver las acciones y arrástralas al recuadro punteado según corresponda. Concéntrate. Tienes dos vidas para intentarlo, o regresas al inicio del tema.</h5>
          </div>
          <hr class="mTop0" />

          <div class="row">
            <div id="portada">
              <img src="static/images/juegos/portada03.jpg" alt="Portada"><br/>
              <a id="btnInstrucciones" href="#" class="button large round warning btnShadow" data-reveal-id="Instrucciones">Instrucciones</a>
              <a id="btnJugar" href="#" class="button large round success btnShadow">Jugar</a>
            </div>

            <div id="cajaJuego" class="posRel hide">
              <img src="static/images/juegos/juego03.jpg" alt="Juego">

              <div id="G1" class="suelta boxMaster"><h3 class="azulSub">Beneficio al CLIENTE</h3></div>
              <div id="G2" class="suelta boxMaster"><h3 class="azulSub">Beneficio al VENDEDOR</h3></div>
              <div id="P1" class="suelta boxMaster"><h3 class="rojo">Perjuicio al CLIENTE</h3></div>
              <div id="P2" class="suelta boxMaster"><h3 class="rojo">Perjuicio al VENDEDOR</h3></div>

              <ul class="small-block-grid-3" id="balls">
                <li class="arrastrable" id="op0"><a href="">1</a></li>
                <li class="arrastrable" id="op1"><a href="">2</a></li>
                <li class="arrastrable" id="op2"><a href="">3</a></li>
                <li class="arrastrable" id="op3"><a href="">4</a></li>
                <li class="arrastrable" id="op4"><a href="">5</a></li>
              </ul>

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
<div id="Instrucciones" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2>¡INSTRUCCIONES!</h2>
  <div>
    <p class="lead">Dale click a los números para ver las acciones y arrástralas al recuadro punteado según corresponda. Concéntrate. Tienes dos vidas para intentarlo, o regresas al inicio del tema.</p>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="myAction" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2>ACCIÓN</h2>
  <p id="lblAccion">Descripción de la acción</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoOk" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <p>Junta 10 respuestas correctas para completar el ejercicio</p>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoOk2" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <p class="lead">!Felicitaciones!</p>
    <p>Completaste todos los desafios, Ahora pasa al siguiente</p>
    <a href="<?php print($nexLink); ?>" class="btnMsg large button round info">Avanzar</a>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoOk3" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <p class="lead">!Felicitaciones!</p>
    <p>Completaste todos los desafios, Ahora pasa al siguiente</p>
    <a href="<?php print($nexLink); ?>" class="btnMsg large button round info">Avanzar</a>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoOk4" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <?php print($puntos); ?><hr/>
    <p class="lead">!Felicitaciones!</p>
    <p>Concluiste el entrenamiento exitosamente ¡Sigue aprendiendo!</p>
    <a href="<?php print($nexLink); ?>" class="btnMsg large button round info">Avanzar</a>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoPista" class="reveal-modal msgAlert" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <h3>¡SIGUE INTENTANDO!</h3><hr/>
    <p>Perdiste una vida, sigue intentando.</p>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  </div>
</div>

<div id="juegoFeedback" class="reveal-modal msgBad" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <div class="msgDialog">
    <h3>¡VUELVE A INTENTARLO!</h3><hr/>
    <p>No has superado la prueba final del tema</p>
    <p>Recomendamos que vuelvas al inicio y repases bien el material</p>
    <a href="tema.php?pid=<?php print $pidE; ?>&retry=1" class="btnMsg large button round info">Reiniciar</a>
  </div>
</div>

<?php include_once('code/script.php'); ?>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
  $(document).on('ready', iniciar);
  var pid     = "<?php print $pid; ?>";
  var numAcc  = parseInt(<?php print $numAcciones; ?>);
  var limite  = numAcc - 1;
  var estado  = parseInt(<?php print $status; ?>);
  var estado2 = "<?php print $status2; ?>";
  var n       = 0;
  var rptas   = 0;
  var puntosP = parseInt(<?php print($puntosP[0]['hito_puntaje_1']); ?>);
  var puntosN = parseInt(<?php print($puntosN[0]['hito_puntaje_1']); ?>);
  var puntosA = parseInt(<?php print($puntosA[0]['hito_puntaje_1']); ?>);
  var puntos3 = 0;

  var mp3Pista = new Audio('static/sound/pista.mp3');
  var mp3Ok    = new Audio('static/sound/ok.mp3');
  var mp3Bad   = new Audio('static/sound/bad.mp3');
  var acciones = <?php print $arrAcciones; ?>;
  var mTbl   = "<?php print $mTbl; ?>";
  var mID    = "<?php print $pid; ?>";
  var linaje = "<?php print $linaje[0]['linaje']; ?>";
  var nextLinaje = "<?php print $nexLinaje; ?>";
  var next    = "<?php print strstr($nexLink, '.', true); ?>";
  var ajaxReq = "jupiter/api.php";
  var auth = 0;

  function iniciar(){
    $(document).foundation();

    $('#btnJugar').on('click', abrirJuego);
    $('#balls li').on('click', abrirAccion);
  };

  function abrirJuego(){
    if (estado == 0){
      $('#myBlock').foundation('reveal', 'open');
    } else {
      $( "#portada" ).fadeOut( "slow" );
      $( "#cajaJuego" ).show();
    }
  };

  function abrirAccion(){
    actual = $(this).attr("id");
    id = parseInt(actual.substr(2));

    $('#lblAccion').text(acciones[id].accion);
    $('#myAction').foundation('reveal','open');
    return false;
  };

  $(".arrastrable").draggable({ containment: '#cajaJuego' });
  $(".arrastrable").data("soltado", false);
  $(".suelta").data("nombre", $(".objeto").attr("id"));

  $(".suelta").droppable({
    activate: function( event, ui ) {
      $(".suelta").addClass("borderRojo");
    },
    deactivate: function( event, ui ) {
      $(".suelta").removeClass("borderRojo");
    },
    drop: function( event, ui ) {
      if (!ui.draggable.data("soltado")){
        var draggableId = ui.draggable.attr("id");
        var $this = $(this).attr("id");
        var curID = parseInt(draggableId.substr(2));
        if ( $this === acciones[curID].grupo ){
          rptas++;
          ganarPuntos(puntosA);
          if (rptas === numAcc) { ganarGame('full'); }
          if (rptas ===  limite && n === 1) { ganarGame('normal'); }
          $('#juegoOk h3').text('¡BIEN!');
          $('#juegoOk h4').text(puntosA + ' Puntos');
          $('#juegoOk').foundation('reveal', 'open');
          $("#" + draggableId).draggable('disable');
          mp3Ok.play();
        } else {
          n++
          if (rptas ===  limite && n === 1) {
            ganarGame('normal');
          } else {
            if (rptas  <   limite && n === 1) { $('#juegoPista').foundation('reveal', 'open'); mp3Pista.play(); }
            if (n >=  2) { $('#juegoFeedback').foundation('reveal', 'open'); mp3Bad.play(); $(".arrastrable").draggable('disable'); }
            $("#" + draggableId).draggable('disable');
            $('#juegoPista').foundation('reveal', 'open');
            mp3Pista.play();
          }
        }
      }
    },
    out: function( event, ui ) {
      $(".suelta").removeClass("borderRojo");
    }
  });

  function ganarGame(game){
    var PFin = 0;
    PFinP = puntosP + puntos3;
    PFinN = puntosN + puntos3;
    if (game === 'full'){
      grabarAccion('ejercicioFinalPerfecto', 10);
      if (next == 'dashboard'){
        $('#juegoOk4').foundation('reveal', 'open');
        $('#juegoOk4 h3').text('¡EXCELENTE!');
        $('#juegoOk4 h4').text(PFinP + ' Puntos');
      } else {
        $('#juegoOk2').foundation('reveal', 'open');
        $('#juegoOk2 h3').text('¡EXCELENTE!');
        $('#juegoOk2 h4').text(PFinP + ' Puntos');
      }
    } else if (game === 'normal') {
      grabarAccion('ejercicioFinalNormal', 11);
      if (next == 'dashboard'){
        $('#juegoOk4').foundation('reveal', 'open');
        $('#juegoOk4 h3').text('¡EXCELENTE!');
        $('#juegoOk4 h4').text(PFinP + ' Puntos');
      } else {
        $('#juegoOk3').foundation('reveal', 'open');
        $('#juegoOk3 h3').text('¡MUY BIEN!');
        $('#juegoOk3 h4').text(PFinN + ' Puntos');
      }
    };
    mp3Ok.play();
    $(".arrastrable").draggable('disable');
  };

  function ganarPuntos(ptos){
    puntos3 += ptos;
    $('#lblPuntos3').text( '+' + puntos3 );
  };

  function grabarAccion(hito, hitoID){
    $.post(ajaxReq, {action:"updateAction", eAuth:auth, eHito:hito, eHitoID:hitoID, eTabla:mTbl, eRel:mID, eLinaje:linaje, eStatus:estado, eStatus2:estado2, eTblNext:next, eLinaje2:nextLinaje, eRptas:rptas, ePA:puntosA, rand:Math.random()},
    function(data){
      var pje = parseInt($('#lblPuntaje span').text());
      $('#lblPuntaje span').text( pje + parseInt(data.rpta) );
    });
  };

</script>

  </body>
</html>
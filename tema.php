<?php
include_once('config.php');
$retry = (isset($_GET['retry'])) ? $_GET['retry'] : 0;
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  // Tema
  $resultados = getTemaById($pid, $db);
  $nombre    = ( count($resultados) )  ? $resultados[0]['tema_nombre']   : '';
  $objetivo  = ( count($resultados) )  ? $resultados[0]['tema_objetivo'] : '';
  $descrip   = ( count($resultados) )  ? $resultados[0]['tema_descrip']  : '';
  $video     = ( count($resultados) )  ? $resultados[0]['tema_material'] : '';
  $masterID  = ( count($resultados) )  ? $resultados[0]['curso_id']      : '';

  $objetivo   = ucfirst(strtolower($objetivo));
  $descrip    = ucfirst(strtolower($descrip));
  $masterID   = encrypt($masterID, $_SESSION['k']);

  // Permisos
  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_temas', $pid, $db);
  $status  = ($enroll['status'] !== 'No')  ?  1  :  0;

//  $resJuegos = getJuegosByUser($_SESSION['userID'], $pid, $db);
//  $divJuegos = genJuegosDIV($resJuegos);

  // Vars
  $perfil    = 'vendedor';

  $mTbl  = 'pg_temas';
  $title = 'Tema';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $mnuMainMobile = crearMnuMainMobile('dashboard', null);

  $linaje  = getLinajeOne(0, 0, $pid, $db);
  $linajeFull = getLinajeFull($db);
  $nexLink    = genNextLink($linaje[0]['linaje'], $linajeFull);
  $nexLinaje  = genNextLinaje($linaje[0]['linaje'], $linajeFull);

  $bread   = genBreadCrumbs('dashboard', 3, null, null, $linaje);
  $entrenamiento_id = encrypt($linaje[0]['entrenamiento_id'], $_SESSION['k']);
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
              <?php print $bread; ?>
            </ul>
            <h1 class="m0 azulMain">TEMA: <?php print $nombre; ?></h1>
            <h5 class="subheader azulSub">OBJETIVO: <?php print $objetivo; ?></h5>
          </div>
          <hr class="mTop0" />

          <div class="row">
            <div class="small-12 columns"><h4>MATERIAL DE APRENDIZAJE DEL TEMA</h4></div>

            <div class="small-12 medium-3 large-3 columns"><br/><br/>
              <div data-alert class="alert-box warning round">
                <h4>Aprende el Material<br/>para desbloquear<br/>los desafíos.<br/>Supéralos y Avanza.</h4>
                <a href="#" class="close">&times;</a>
              </div>
              <div class="don-pepe">
                <img src="static/images/don-pepe-saludando.png">
              </div>
            </div>

            <div class="small-12 medium-9 large-9 columns">
              <div class="vPlayer vp-initial">
                <div class="video">
                  <!-- <iframe id="player2" src="<?php print $video; ?>" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="100%" height="100%"></iframe> -->
                  <div id="video-placeholder"></div>
                </div>
                <span class="status label warning" id="msgVideo"><i class="fa fa-chevron-right"></i> Empezar</span>
                <span class="lnr lnr-checkmark-circle"></span><i class="fa fa-check"></i> Completo</span>
                <div class="title">
                  <h5><?php print $nombre; ?></h5>
                  <span><?php print $descrip; ?></span>
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>

    </section>

    <footer>
      <?php include_once('code/footer.php'); ?>
    </footer>

  </main>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="myBlock" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">¡Bloqueado!</h2>
  <p class="lead">Aun no tienes acceso</p>
  <p>Completa el contenido anterior para poder desbloquear este contenido.</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<div id="juegoNext" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" style="background: url('/code/show-image.php?p=ok&i=celebra') top center no-repeat;">
  <div class="msgDialog">
    <p>Superaste los niveles, ahora pasa al siguiente tema</p>
    <a href="entrenamiento.php?pid=<?php print($entrenamiento_id);?>" class="btnMsg button large round info">Avanzar</a>
  </div>
</div>

<?php include_once('code/script.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
<!-- <script src="//fast.wistia.net/static/iframe-api-v1.js"></script> -->
<script src="https://www.youtube.com/iframe_api"></script>
<script>
  $(document).on('ready', iniciar);
  var url   = "cuestionario.php?pid=<?php print $pidE; ?>";
  var tema  = "tema.php?pid=<?php print $pidE; ?>";
  var pid   = <?php print $pid; ?>;
  var rel   = 0;
  var game  = 0;
  var gameID = 0;
  var retry = <?php print $retry; ?>;
  var mTbl  = "<?php print $mTbl; ?>";
  var mID   = "<?php print $pid; ?>";
  var link  = ".php?rel=1&pid=<?php print $pidE; ?>";
  var linaje  = "<?php print $linaje[0]['linaje']; ?>";
  var auth    = "";
  var status  = parseInt(<?php print $status; ?>);
  var ajaxReq = "jupiter/api.php";
  var nPlay  = 0;
  var player;
  var time_update_interval;

  function iniciar(){
    $(document).foundation();

    $('.vPlayer a').on('click', goLink);
    $('#btnAvanzar').on('click', avanzar);
    //$('#btnCuestionario').attr('disabled','disabled');

/*
    var video = document.getElementById("player2").wistiaApi;
    video.bind("play", function() { iniciarVideo(); });
    video.bind("secondchange", function(t) { onPlaying(t); });
    video.bind("end", function() { terminarVideo(); });
*/
  };

function onYouTubeIframeAPIReady() {
    player = new YT.Player('video-placeholder', {
        width: '100%',
        height: '100%',
        videoId: '<?php print $video; ?>',
        playerVars: {
            color: 'white',
            playlist: '',
            rel: 0,
            showinfo: 0
        },
        events: {
            onReady: initialize,
            onStateChange: onPlayerStateChange
        },
        rel: 0,
        showinfo: 0
    });
};

function initialize(){
    // Update the controls on load
    updateTimerDisplay();

    // Clear any old interval.
    clearInterval(time_update_interval);

    // Start interval to update elapsed time display and
    // the elapsed part of the progress bar every second.
    time_update_interval = setInterval(function () {
        updateTimerDisplay();
        //updateProgressBar();
    }, 1000)

};

function onPlayerStateChange(event) {
  if (event.data == YT.PlayerState.PLAYING){
    // Inicia video
    var label = $('#msgVideo');
    label.removeClass('success').addClass('warning');
    //if (nPlay < 1) { grabarAccion('cursoIniciarVideo', 14); }
    //nPlay++;
  }
  if (event.data == YT.PlayerState.ENDED) {
    // fin video
    terminarVideo();
  }
}

// This function is called by initialize()
function updateTimerDisplay(){
    // Update current time text display.
    $('#current-time').text(formatTime( player.getCurrentTime() ));
    $('#duration').text(formatTime( player.getDuration() ));
};

function formatTime(time){
    time = Math.round(time);

    var minutes = Math.floor(time / 60),
    seconds = time - minutes * 60;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    return minutes + ":" + seconds;
};

  function goLink(e){
    e.preventDefault();
    if ( $(this).attr('class') === "alert" ){
      $('#myBlock').foundation('reveal', 'open');
    } else {
      document.location = $(this).attr('rel') + link;
    }
    return false;
  };

  function avanzar(){
    $('#juegoNext').foundation('reveal', 'close');
    $('#desafio1 a img').removeClass('lessOpacity');
    $('#desafio1 a .status').text('Iniciar');
    $('#desafio1 a .status').removeClass('alert').addClass('warning');
    $('#desafio1 a .play i').removeClass('rojo').addClass('amarillo');
    $('#desafio1 a .play i').removeClass('fa-lock').addClass('fa-play-circle-o');
    $('#desafio1 a').removeClass('alert');

    $('#desafio2 a img').removeClass('lessOpacity');
    $('#desafio2 a .status').text('Iniciar');
    $('#desafio2 a .status').removeClass('alert').addClass('warning');
    $('#desafio2 a .play i').removeClass('rojo').addClass('amarillo');
    $('#desafio2 a .play i').removeClass('fa-lock').addClass('fa-play-circle-o');
    $('#desafio2 a').removeClass('alert');

    $('#desafio3 a img').removeClass('lessOpacity');
    $('#desafio3 a .status').text('Iniciar');
    $('#desafio3 a .status').removeClass('alert').addClass('warning');
    $('#desafio3 a .play i').removeClass('rojo').addClass('amarillo');
    $('#desafio3 a .play i').removeClass('fa-lock').addClass('fa-play-circle-o');
    $('#desafio3 a').removeClass('alert');
  }

  function onPlaying(t){
    var label = $('#msgVideo');
    label.text(t + "'s mirando");
  }

  function terminarVideo(){
    var label = $('#msgVideo');
    label.text('terminado');
    label.removeClass('warning').addClass('success');

    grabarAccion('temaVerMaterial', 5);
    grabarGame("saveGameWinFull", $('#juegoNext'));
    grabarAccion('juegoFinal1', 18);
    grabarAccion('juegoFinal2', 19);
    grabarAccion('juegoFinal3', 20);
    $('#juegoNext').foundation('reveal', 'open');
  }

  function grabarGame(action, modal){
    $.post(ajaxReq, {action:action, eAuth:auth, ePid:pid, eRel:rel, eGame:game, eGameId:gameID, eStatus:estado, rand:Math.random()},
      function(data){
        //nada
    });
  };

  function grabarAccion(hito, hitoID){
    $.post(ajaxReq, {action:"updateAction", eAuth:auth, eHito:hito, eHitoID:hitoID, eTabla:mTbl, eRel:mID, eLinaje:linaje, rand:Math.random()},
    function(data){
      var pje = parseInt($('#lblPuntaje span').text());
      $('#lblPuntaje span').text( pje + parseInt(data.rpta) );
    });
  };

</script>

  </body>
</html>

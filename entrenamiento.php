<?php
include_once('config.php');
$try  = (isset($_GET['try'])) ? $_GET['try'] : 0;
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getEntrenamientoById($pid, $db);
  $entrenamiento = ( count($resultados) ) ? $resultados[0]['entrenamiento_nombre']   : '';
  $objetivo      = ( count($resultados) ) ? $resultados[0]['entrenamiento_objetivo'] : '';
  $descrip       = ( count($resultados) ) ? $resultados[0]['entrenamiento_descrip']  : '';
  $descrip       = ucfirst(strtolower($descrip));

  //$resCursos = getCursos($pid, $db);
  $resCursos = getCursosByUser($_SESSION['userID'], $pid, $db);
  $divCursos = genCursosDIV($resCursos);

  // Setup Curso 0 (intro)
  $curso_master  = $pid;
  $curso_id_0    = -1;
  $divCursoIntro = '';
  if (count($resCursos)){
    $curso_id_0    = $resCursos[0]['curso_id'];
    $resCursoIntro = getCursoById($curso_id_0, $db);
    $curso_video   = $resCursoIntro[0]['curso_video'];
    $curso_nombre  = $resCursoIntro[0]['curso_nombre'];
    $curso_descrip = $resCursoIntro[0]['curso_descrip'];
    $curso_master  = $resCursoIntro[0]['entrenamiento_id'];
  }

  // Permisos
  $firtsC  = firtsCurso($curso_master, $db);
  $firts   = ($curso_id_0 == $firtsC)  ?  1  :  0;

  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_cursos', $curso_id_0, $db);
  $status  = ($enroll['status'] !== 'No')  ?  1  :  0;

  $mTbl    = 'pg_cursos';
  $duply   = isActionDuply($_SESSION['userID'], VIDEO_CURSO, $mTbl, $curso_id_0, $db);

  $resTemas  = getTemasByUser($_SESSION['userID'], $curso_id_0, $db);
  $divTemas  = genTemasDIV($resTemas);

  // General
  $puntaje = getAvanceByEntrenamientoByUser($_SESSION['userID'], $pid, $db);
  $avance  = floatval($puntaje['avance']);

  // Vars
  $title = 'Entrenamiento';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $mnuMainMobile = crearMnuMainMobile('dashboard', null);
  $linaje  = getLinajeOne(0, $curso_id_0, 0, $db);
  $bread   = genBreadCrumbs('dashboard', 2, null, null, $linaje);
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
        <div class="large-12 columns">
          <div class="row pDown1">
            <div class="small-8 medium-9 columns">
              <div class="text-left azulMain">
                <ul class="breadcrumbs mDown0">
                  <li><a href="dashboard.php">Entrenamientos</a></li>
                  <li class="current"><?php print $entrenamiento; ?></li>
                </ul>
                <h1 class="m0 azulMain">Entrenamiento: <?php print $entrenamiento; ?><small> - <?php print $descrip; ?></small></h1>
                <h5 class="subheader azulSub">Objetivo: <?php print $objetivo; ?></h5>
              </div>
            </div>

            <div class="small-4 medium-3 columns">
              <span class="chart" data-percent="<?php print($avance); ?>">
                <span class="percent"></span>
              </span>
            </div>
          </div><hr class="mTop0" />

          <div class="row">

            <div class="small-6 columns">
              <h4>1. VIDEO DE INTRODUCCIÓN</h4>
              <h5 class="subheader azulSub">(Míralo primero para pasar a los temas del entrenamiento)</h5>
              <!-- video de intro del curso 0 -->
              <div class="row">
                <div class="small-12 columns">
                  <div class="vPlayer vp-initial">
                    <div class="video">
                      <!-- <iframe id="player2" src="<?php print $curso_video; ?>" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="100%" height="100%"></iframe> -->
                      <div id="video-placeholder"></div>
                    </div>
                    <span class="status label warning" id="msgVideo"><i class="fa fa-chevron-right"></i> Empezar <span id="current-time">0:00</span> / <span id="duration">0:00</span></span>
                    <div class="title">
                      <h5><?php print $curso_nombre; ?></h5>
                      <span><?php print $curso_descrip; ?></span>
                    </div>
                  </div>
                </div>
                <div class="small-12 columns">
                  <a href="#" class="button large round success" id="btnCuestionario" disabled="disabled">
                    <i class="fa fa-outdent"></i> Responder Cuestionario</a>
                </div>
              </div>
            </div>
            <div class="small-6 columns">
              <h4>2. TEMAS DEL ENTRENAMIENTO</h4>
              <h5 class="subheader azulSub">(Complétalos y pasa al siguiente entrenamiento)</h5>
              <!-- lista de temas -->
              <div class="row">
                <?php print $divTemas; ?>
              </div>
            </div>

          </div>

          <div class="row">
            <?php //print $divCursos; ?>
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
  <div id="myCuestionario" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h2 id="modalTitle">¡Muy Bien!</h2>
    <p>Responde correctamente el cuestionario y desbloquear los temas.</p>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  </div>

<?php include_once('code/script.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
<!-- <script src="//fast.wistia.net/static/iframe-api-v1.js"></script> -->
<script src="https://www.youtube.com/iframe_api"></script>
<script>
  $(document).on('ready', iniciar);
  var auth  = 0;
  var link  = "tema.php?pid=";
  var url   = "cuestionario.php?pid=<?php print $pidE; ?>";
  var tema  = "tema.php?pid=<?php print $pidE; ?>";
  var trial = parseInt(<?php print($try); ?>);
  var mTbl  = "<?php print $mTbl; ?>";
  var mID   = "<?php print $pid; ?>";
  var linaje   = "<?php print $linaje[0]['linaje']; ?>";
  var ajaxReq  = "jupiter/api.php";
  var firts  = parseInt(<?php print($firts); ?>);
  var status = parseInt(<?php print($status); ?>);
  var duply  = parseInt(<?php print($duply); ?>);
  var nPlay  = 0;
  var player;
  var time_update_interval;

  function iniciar(){
    $(document).foundation();

    $('.vPlayer a').on('click', goLink);
    $('#btnCuestionario').on('click', goQuestions);

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
        videoId: '<?php print $curso_video; ?>',
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
}

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

}

function onPlayerStateChange(event) {
  if (event.data == YT.PlayerState.PLAYING){
    // Inicia video
    var label = $('#msgVideo');
    label.removeClass('success').addClass('warning');
    if (nPlay < 1) { grabarAccion('cursoIniciarVideo', 14); }
    nPlay++;
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
}

function formatTime(time){
    time = Math.round(time);

    var minutes = Math.floor(time / 60),
    seconds = time - minutes * 60;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    return minutes + ":" + seconds;
}

$('#play').on('click', function () {
    player.playVideo();
});

$('#pause').on('click', function () {
    player.pauseVideo();
});

  function goLink(e){
    e.preventDefault();
    if ($(this).attr('class') == "alert"){
      $('#myBlock').foundation('reveal', 'open');
    } else {
      document.location = link + $(this).attr('rel');
    }
    return false;
  };

  function goQuestions(e){
    e.preventDefault();
    if ($(this).attr('disabled') == "disabled"){
      $('#myBlock').foundation('reveal', 'open');
    } else {
      document.location = url;
    }
    return false;
  }

  $(function() {
      $('.chart').easyPieChart({
        easing: 'easeOutElastic',
        delay: 3000,
        barColor: '#37aa6e',
        trackColor: '#d3d3d3',
        scaleColor: false,
        lineWidth: 20,
        trackWidth: 12,
        lineCap: 'butt',
        onStep: function(from, to, percent) {
          this.el.children[0].innerHTML = Math.round(percent);
        }
      });
  });

  function iniciarVideo(){
    var label = $('#msgVideo');
    label.text('mirando');
    label.removeClass('success').addClass('warning');
    if (nPlay < 1) { grabarAccion('cursoIniciarVideo', 14); }
    nPlay++;
  };

  function onPlaying(t){
    var label = $('#msgVideo');
    label.text(t + "'s mirando");
  };

  function terminarVideo(){
    var label = $('#msgVideo');
    label.text('terminado');
    label.removeClass('warning').addClass('success');

    grabarAccion('cursoVerVideo', 1);
    //goTemas();
    setTimeout(activarCuestionario, 1200);
  };

  function activarCuestionario(){
    if (firts == 1 || status == 1){
      $('#myCuestionario').foundation('reveal', 'open');
      $('#btnCuestionario').removeAttr('disabled').fadeIn( "slow" );
    } else {
      $('#myBlock').foundation('reveal', 'open');
    }
  };

  function goTemas(){
    var targetOffset = $("#listaItems").position().top;
    $("html,body").stop().animate({scrollTop: targetOffset}, 1000);
  };

  function grabarAccion(hito, hitoID){
    $.post(ajaxReq, {action:"updateAction", eAuth:auth, eHito:hito, eHitoID:hitoID, eTabla:mTbl, eRel:mID, eLinaje:linaje, eFirts:firts, eStatus:status, rand:Math.random()},
    function(data){
      var pje = parseInt($('#lblPuntaje span').text());
      $('#lblPuntaje span').text( pje + parseInt(data.rpta) );
    });
  };
</script>

  </body>
</html>

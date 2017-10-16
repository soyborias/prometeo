<?php
include_once('config.php');
$try  = (isset($_GET['try'])) ? $_GET['try'] : 0;
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  // Curso
  $resultados = getCursoById($pid, $db);
  $nombre     = ( count($resultados) ) ? $resultados[0]['curso_nombre']     : '';
  $objetivo   = ( count($resultados) ) ? $resultados[0]['curso_objetivo']   : '';
  $descrip    = ( count($resultados) ) ? $resultados[0]['curso_descrip']    : '';
  $video      = ( count($resultados) ) ? $resultados[0]['curso_video']      : '';
  $master     = ( count($resultados) ) ? $resultados[0]['entrenamiento_id'] :  0;

  $descrip    = ucfirst(strtolower($descrip));
  $masterID   = encrypt($master, $_SESSION['k']);

  // Permisos
  $firtsC  = firtsCurso($master, $db);
  $firts   = ($pid == $firtsC)  ?  1  :  0;

  $enroll  = checkEnrollStatus($_SESSION['userID'], 'pg_cursos', $pid, $db);
  $status  = ($enroll['status'] !== 'No')  ?  1  :  0;

  $mTbl    = 'pg_cursos';
  $duply   = isActionDuply($_SESSION['userID'], VIDEO_CURSO, $mTbl, $pid, $db);

  $resTemas  = getTemasByUser($_SESSION['userID'], $pid, $db);
  $divTemas  = genTemasDIV($resTemas);

  // Vars
  $title = 'Curso P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $mnuMainMobile = crearMnuMainMobile('dashboard', null);
  $linaje  = getLinajeOne(0, $pid, 0, $db);
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
            <div class="small-12 columns">
              <div class="text-left azulMain">
                <ul class="breadcrumbs mDown0">
                  <?php print $bread; ?>
                </ul>
                <h1 class="m0 azulMain">CURSO: <?php print $nombre; ?></h1>
                <h5 class="subheader azulSub">OBJETIVO: <?php print $objetivo; ?></h5>
              </div>
            </div>
          </div><hr class="mTop0" />

          <div class="row">
            <div class="small-12 columns">

              <div class="vPlayer vp-initial">
                <div class="video">
                  <iframe id="player2" src="<?php print $video; ?>" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="100%" height="100%"></iframe>
                </div>
                <span class="status label warning" id="msgVideo"><i class="fa fa-chevron-right"></i> Empezar</span>
                <div class="title">
                  <h5><?php print $nombre; ?></h5>
                  <span><?php print $descrip; ?></span>
                </div>
              </div>

            </div>
          </div>

          <div class="row">
            <div class="info-don-pepe clearfix">
              <img src="static/images/don-pepe-saludando.png" alt="Don Pepe">
              <div class="bubble">
                <p>Mira el video para responder correctamente el cuestionario y desbloquear los temas.</p>
                <p><strong>¡Cuidado!</strong> con dos respuestas incorrectas repites el video.</p>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="small-12 columns">
              <a href="#" class="button large round success" id="btnCuestionario" disabled="disabled">
                <i class="fa fa-outdent"></i> Responder Cuestionario</a>
            </div>
          </div>

          <div class="row" id="listaItems">
            <div class="small-12 columns"><h4>TEMAS DEL CURSO</h4></div>
            <?php print $divTemas; ?>
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
<script src="//fast.wistia.net/static/iframe-api-v1.js"></script>
<script>
  $(document).on('ready', iniciar);
  var auth  = "";
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

  function iniciar(){
    $(document).foundation();

    $('.vPlayer a').on('click', goLink);
    $('#btnCuestionario').on('click', goQuestions);

    var video = document.getElementById("player2").wistiaApi;
    video.bind("play", function() { iniciarVideo(); });
    video.bind("secondchange", function(t) { onPlaying(t); });
    video.bind("end", function() { terminarVideo(); });
  };

  function goLink(e){
    e.preventDefault();
    if ( $(this).attr('class') === "alert" ){
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
    goTemas();
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

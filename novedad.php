<?php
include_once('config.php');
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getNovedadById($pid, $db);
  $nombre    = ( count($resultados) )  ? $resultados[0]['novedad_nombre']   : '';
  $objetivo  = ( count($resultados) )  ? $resultados[0]['novedad_objetivo'] : '';
  $descrip   = ( count($resultados) )  ? $resultados[0]['novedad_descrip']  : '';
  $video     = ( count($resultados) )  ? $resultados[0]['novedad_material'] : '';

  $objetivo   = ucfirst(strtolower($objetivo));
  $descrip    = ucfirst(strtolower($descrip));

  // Vars
  $mTbl   = 'pg_novedades';
  $title  = 'Novedad P&G';
  $url    = 'resolver-cuestionario.php?m='. base64_encode($mTbl). '&pid='. $pidE;
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
  $mnuMainMobile = crearMnuMainMobile('dashboard', null);
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
              <li><a href="dashboard.php">Novadades</a></li>
              <li class="current"><?php print $nombre; ?></li>
            </ul>
            <h1 class="m0 azulMain">NOVEDAD: <?php print $nombre; ?></h1>
            <h5 class="subheader azulSub">OBJETIVO: <?php print $objetivo; ?></h5>
          </div><hr class="mTop0" />

          <div class="row">
            <div class="small-9 large-9 columns">
              <div class="vPlayer vp-initial">
                <div class="video">
                  <iframe id="player2" src="<?php print $video; ?>" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="100%" height="100%"></iframe>
                </div>
                <span class="status label warning" id="msgVideo"><i class="fa fa-chevron-right"></i> Empezar</span>
                <span class="lnr lnr-checkmark-circle"></span><i class="fa fa-check"></i> Completo</span>
                <div class="title">
                  <h5><?php print $nombre; ?></h5>
                  <span><?php print $descrip; ?></span>
                </div>
              </div>
            </div>
            <div class="large-3 columns">

              <div data-alert class="alert-box warning round">
                <h4>Mira el video para responder correctamente el cuestionario.</h4>
                <p><strong>¡Cuidado!</strong> con dos respuestas incorrectas repites el video.</p>
                <a href="#" class="close">&times;</a>
              </div>
              <div class="don-pepe">
                <img src="static/images/don-pepe-saludando.png">
              </div>
              <div class="row">
                <div class="small-12 columns">
                  <a href="#" class="button large round success" id="btnCuestionario" disabled="disabled">
                    <i class="fa fa-outdent"></i>
                    Responder Cuestionario</a>
                </div>
              </div>

            </div>
          </div><br/>
          <p>&nbsp;</p>

        </div>

      </div>



    </section>

  </main>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="myCuestionario" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">¡Muy Bien!</h2>
  <p>Responde correctamente el cuestionario.</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>
<script src="//fast.wistia.net/static/iframe-api-v1.js"></script>
<script>
  $(document).on('ready', iniciar);
  var url   = "<?php print $url; ?>";
  var mTbl  = "<?php print $mTbl; ?>";
  var mID   = "<?php print $pid; ?>";
  var auth  = "";
  var ajaxReq = "jupiter/api.php";

  function iniciar(){
    $(document).foundation();
    $('#btnCuestionario').attr('disabled','disabled');

    var video = document.getElementById("player2").wistiaApi;
    video.bind("play", function() { iniciarVideo(); });
    video.bind("secondchange", function(t) { onPlaying(t); });
    video.bind("end", function() { terminarVideo(); });
  };

  function onPlaying(t){
    var status = $('#msgVideo');
    status.text(t + "'s mirando");
  }

  function terminarVideo(){
    var status = $('#msgVideo');
    status.text('terminado');
    status.removeClass('warning').addClass('success');

    grabarAccion('novedadVerMaterial', 13); // ver ID
    $('#myCuestionario').foundation('reveal', 'open');
    $('#btnCuestionario').removeAttr('disabled').fadeIn( "slow" );;
    $('#btnCuestionario').attr('href', url);
  }

  function grabarAccion(hito, hitoID){
    $.post(ajaxReq, {action:"updateAction", eAuth:auth, eHito:hito, eHitoID:hitoID, eTabla:mTbl, eRel:mID, rand:Math.random()},
    function(data){
      var pje = parseInt($('#lblPuntaje span').text());
      $('#lblPuntaje span').text( pje + parseInt(data.rpta) );
    });
  };
</script>

  </body>
</html>

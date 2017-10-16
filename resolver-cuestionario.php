<?php
include_once('config.php');
$try  = (isset($_GET['try'])) ? $_GET['try'] : 0;
$pidE = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$pid  = decrypt($pidE, $_SESSION['k']);
$table  = (isset($_GET['m']))    ? $_GET['m']    : '';
$mTable = base64_decode($table);

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $msgRpta = genMensajeTry($try, true);

  $resPreguntas = getQuestionsRnd($mTable, $pid, 5, $db);
  $rpta = genQuestionsOLRnd($resPreguntas);
  $opPreguntas   = $rpta['preguntas'];
  $arrRespuestas = json_encode($rpta['answers']);

  // Vars
  $title = 'Cuestionario P&G';
  $mTbl  = 'pg_preguntas';
  $next  = 'dashboard.php';
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
        <div class="large-12 columns">

          <div class="row pDown1">
            <div class="small-12 columns">
              <div class="text-left azulMain">
                <ul class="breadcrumbs mDown0">
                  <li><a href="dashboard.php">Entrenamientos</a></li>
                  <li><a href="entrenamiento.php">Curso</a></li>
                  <li><a href="curso.php">Temas</a></li>
                  <li class="current">Cuestionario</li>
                </ul>
                <h1 class="m0 azulMain">CUESTIONARIO</h1>
                <h5 class="subheader azulSub">Responde el cuestionario con cuidado: con dos respuestas incorrectas, repites el video y el cuestionario.</h5>
              </div>
            </div>
          </div><hr class="mTop0" />

          <div class="row text-left">
            <form>

              <div class="medium-12 columns" id="frmPreguntas">
                <?php print($opPreguntas); ?>
             </div>

            </form>
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

<div id="juegoOk" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" style="background: url('/code/show-image.php?p=ok&i=gana') top center no-repeat;">>
  <div class="msgDialog">
    <h3>¡EXCELENTE!</h3>
    <h4>+10 Puntos</h4><hr/>
    <a href="<?php print $next; ?>" class="btnMsg button large round info">Avanzar</a>
  </div>
</div>

<div id="juegoOk2" class="reveal-modal msgAlert" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" style="background: url('/code/show-image.php?p=ok&i=feedback') top center no-repeat;">>
  <div class="msgDialog">
    <h3>¡Muy Bien!</h3>
    <h4>+7 Puntos</h4><hr/>
    <a href="<?php print $next; ?>" class="btnMsg button large round info">Avanzar</a>
  </div>
</div>

<div id="juegoFeedback" class="reveal-modal msgBad" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" style="background: url('/code/show-image.php?p=ok&i=pierde') top center no-repeat;">>
  <div class="msgDialog">
    <h3>¡VUELVE A INTENTARLO!</h3><hr/>
    <p>Te equivocaste en más de 2 respuestas, vuelve a repasar el video.</p>
    <a href="novedad.php?pid=<?php print $pidE; ?>&try=3" class="btnMsg button large round info">Volver a ver el video</a>
  </div>
</div>

<?php include_once('code/script.php'); ?>
<script src="js/foundation/foundation.alert.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
<script>
  $(document).foundation();
  var auth  = "";
  var ajaxReq = "jupiter/api.php";
  var mTbl  = "<?php print $mTbl; ?>";
  var mID   = "<?php print $pid; ?>";

  var warning = ' <i class="fa fa-exclamation-triangle"></i> Incorrecto';
  var success = ' <i class="fa fa-check-circle-o"></i> Bien';
  var ok   = 0;
  var bad  = 0;
  var rpta = "";

  var answers = <?php print $arrRespuestas; ?>;
  var mp3Pista = new Audio('static/sound/pista.mp3');
  var mp3Ok = new Audio('static/sound/ok.mp3');
  var mp3Bad = new Audio('static/sound/bad.mp3');

  $("#frmPreguntas input[type='radio']").on("change", function() {
    var valor = $(this).val();
    var radioName = $(this).attr('name');
    var element = $(this).parent().parent();
    var elementId = element.attr('id');
    var num = parseInt(elementId.substring(3, 2));
    var test = answers[num-1][1];

    // Add Bold
    $(this).next('label').addClass('bold');

    // Disable question
    $("#frmPreguntas input[name^=" + radioName + "]").attr('disabled', true);

    if (test === valor){
      ok++
      $("#title" + num).append(success).addClass('verde');
      if (ok === 5){
        grabarAccion('novedadCuestionarioPerfecto', 15);
        $('#juegoOk').foundation('reveal', 'open');
        mp3Ok.play();
      } else if (ok === 4 && bad === 1){
        $('#juegoOk2').foundation('reveal', 'open');
        mp3Pista.play();
      }
    } else {
      bad++
      $("#title" + num).append(warning).addClass('rojo');
      if (bad > 1){
        $("#frmPreguntas input").attr('disabled',true);
        $('#juegoFeedback').foundation('reveal', 'open');
        mp3Bad.play();
      } else if (ok === 4 && bad === 1){
        grabarAccion('novedadCuestionarioNormal', 16);
        $('#juegoOk2').foundation('reveal', 'open');
        mp3Pista.play();
      }
    }
    $(document).foundation('alert', 'reflow');
  });

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

<?php
include_once('config.php');

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resCurricula  = getCurriculaByPerfil($_SESSION['perfil'], $db);
  $perfilEntrena = isset($resCurricula[0]['curricula_entrenamientos']) ? $resCurricula[0]['curricula_entrenamientos'] : -1;
  $resEntrena    = getEntrenamientosByPerfil($_SESSION['userID'], $perfilEntrena, $db);
  $divEntrenamientos = genEntrenamientosDIV($resEntrena);

  $imgPath = getPerfilImagenes($_SESSION['perfil'], $_SESSION['nivel'], $db);
  $imgPath = str_replace("'", '"', $imgPath);
  $_SESSION['perfilImages'] = json_decode($imgPath , true);

  $perfilNovedad = isset($resCurricula[0]['curricula_novedades']) ? $resCurricula[0]['curricula_novedades'] : -1;
  $resNovedades  = getNovedadesByPerfil($_SESSION['userID'], $perfilNovedad, $db);
  $divNovedades  = genNovedadesDIV($resNovedades);

  $premios = '<p>&nbsp;</p>';

  // Vars
  $title = 'Dashboard P&G';
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

        <div class="row bgBlanco">

          <div class="small-12 medium-8 large-9 columns bgBlanco">
            <div class="row">
              <div class="small-12 columns text-left">
                <h1 class="azulMain">Entrenamientos
                </h1>
              </div>
            </div><hr class="mTop0" />
            <div class="row items">
              <?php print $divEntrenamientos; ?>
            </div><p>&nbsp;</p>
          </div>

          <div class="small-12 medium-4 large-3 columns text-center bgGrisLight">
            <h1>Novedades del mes</h1><hr class="mTop0" />
            <div class="row items">
              <?php print $divNovedades; ?>
            </div><p>&nbsp;</p>
          </div>

          <div id="section-premios" class="small-12 medium-4 large-3 columns text-center bgAzulSub">
            <h1 class="blanco">Gana y Entrénate</h1><hr class="mTop0" />
            <form action="http://www.ganayentrenateconpg.com/Ingreso.asmx/ValidarDni" id="form2" method="post" target="_blank">
              <input type="hidden" name="dni" value="<?php print($_SESSION['username']); ?>">
              <input type="submit" value="Ingresar" class="round button secondary">
            </form>
            <img src="/static/images/trofeo.png" alt="Trofeo">
            <?php print $premios; ?>
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
  <p class="lead">Aun no tienes acceso a este entrenamiento.</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<div id="myVideo" class="reveal-modal large" data-reveal aria-labelledby="videoModalTitle" aria-hidden="true" role="dialog">
  <h2 id="videoModalTitle">Manual de introdución</h2>
  <div class="flex-video widescreen vimeo">

  </div>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<div id="myManual" class="reveal-modal large" data-reveal aria-labelledby="videoModalTitle" aria-hidden="true" role="dialog">
  <h2 id="videoModalTitle">Manual de introdución</h2>
  <embed src="static/descargas/ManualEntrenatePG-Usuario.pdf" width="100%" height="500" alt="Manual PDF" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>
<script src="https://cdn.linearicons.com/free/1.0.0/svgembedder.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
<script>
  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();
    setTimeout(function(){ draw(); }, 500);
    $('div.items a[rel="No"]').on('click', clickLink);
  };

  function clickLink(e){
    e.preventDefault();

    $('#myBlock').foundation('reveal', 'open');
    return false;
  }

  function draw(){
    $('.chart').easyPieChart({
      easing: 'easeOutElastic',
      delay: 3000,
      barColor: '#37aa6e',
      trackColor: '#d3d3d3',
      scaleColor: false,
      lineWidth: 10,
      size: 150,
      trackWidth: 10,
      lineCap: 'butt',
      onStep: function(from, to, percent) {
        this.el.children[0].innerHTML = Math.round(percent);
      }
    });
  };

</script>

  </body>
</html>

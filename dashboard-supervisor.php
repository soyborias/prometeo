<?php
include_once('config.php');

if ( (isset($_SESSION['username'])) && ($_SESSION['rol']) == ROL_SUPERVISOR ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $res = getUserListBySupervisor($_SESSION['userID'], $db);
  $_SESSION['UserList'] = genListaUsuarios($res);

  // Vars
  $title = 'Dashboard Supervisor P&G';

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
        <?php include_once('code/top-bar-admin.php'); ?>
      </section>
    </nav>

    <!-- Menu -->
    <div class="left-sidebar show-for-large-up">
      <ul class="property-nav text-center">
        <li class="current">
          <a href="dashboard-supervisor.php" data-tooltip aria-haspopup="true" class="tip-right" title="Dashboard"><i class="fa fa-home"></i></a>
        </li>
        <li>
          <a href="lista-participantes.php" data-tooltip aria-haspopup="true" class="tip-right" title="Participantes"><i class="fa fa-users"></i></a>
        </li>
        <li>
          <a href="reporte-entrenamiento-sup.php" data-tooltip aria-haspopup="true" class="tip-right" title="Reportes"><i class="fa fa-indent"></i></a>
        </li>
        <li>
          <a href="index.php?logout" data-tooltip aria-haspopup="true" class="tip-right" title="Cerrar Sesión"><i class="fa fa-sign-out"></i></a>
        </li>
      </ul>
    </div>


  <main id="page" role="main" class="main mIzq45">

    <section>

      <div class="row bgGrisLight">

        <div class="small-12 columns bgBlanco">
          <div class="row">
            <div class="small-12 columns text-left">
              <h1 class="azulMain">Dashboard Supervisor
                <a class="right verde pLeft5" data-reveal-id="myManual"><i class="fa fa-leanpub"></i></a>
                <a class="right verde pLeft5" data-reveal-id="myVideo"><i class="fa fa-video-camera"></i></a>
                <a class="right verde pLeft5">Manual </a>
              </h1>
            </div>
          </div><hr class="mTop0" /><p>&nbsp;</p>

          <div class="row centered-text">
            <div class="medium-3 large-3 columns supporticons">
              <a href="lista-participantes.php">
              <i class="fa fa-users fa-4x"></i>
              <h3>Participantes</h3></a>
              <p>Gestiona los usuarios de la plataforma asignados a tu supervisión.</p>
            </div>
            <div class="medium-3 large-3 columns supporticons">
              <a href="reporte-entrenamiento-sup.php">
                <i class="fa fa-indent fa-4x"></i>
                <h3>Reportes</h3></a>
              <p>Monitorea el estado de los vendedores en los entrenamientos con multiples reportes.</p>
            </div>
          </div>

          <p>&nbsp;</p><p>&nbsp;</p>
        </div>

      </div>

    </section>

    <footer>
      <?php include_once('code/footer.php'); ?>
    </footer>

  </main>
<div id="myVideo" class="reveal-modal large" data-reveal aria-labelledby="videoModalTitle" aria-hidden="true" role="dialog">
  <h2 id="videoModalTitle">Manual de introdución</h2>
  <div class="flex-video widescreen vimeo">
    <iframe id="player2" src="//fast.wistia.net/embed/iframe/6beh0ij0oj" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="100%" height="100%"></iframe>
  </div>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<div id="myManual" class="reveal-modal large" data-reveal aria-labelledby="videoModalTitle" aria-hidden="true" role="dialog">
  <h2 id="videoModalTitle">Manual de introdución</h2>
  <embed src="static/descargas/ManualEntrenatePG-Supervisor.pdf" width="100%" height="500" alt="Manual PDF" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>
<script>
  $(document).foundation();

</script>

  </body>
</html>
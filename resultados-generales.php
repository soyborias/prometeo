<?php
include_once('config.php');

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $_SESSION['equipo_filtro'] = '';
  $_SESSION['perfil_filtro'] = '';
  if ( $_SESSION['rol'] == ROL_SUPERVISOR ){
    $resEquipos  = getEquiposBySupervisor($_SESSION['userID'], $db);
    $_SESSION['equipo_filtro'] = getUsuariosAllTeamBySupervisor($resEquipos, $db);

    $perfil_sup = getSupervisorRel($_SESSION['userID'], 'perfil_sup', $db);
    $_SESSION['perfil_filtro'] = getCurriculaByPerfil($perfil_sup, $db);
  } elseif ( $_SESSION['rol'] == ROL_JEFE ) {
    $supJefe    = getSupByJefe($_SESSION['userID'], $db);
    $resEquipos = getEquiposAllByJefe($supJefe, $db);
    $_SESSION['equipo_filtro'] = getUsuariosAllTeamBySupervisor($resEquipos, $db);

    $perfil_sup = getSupervisorRel($_SESSION['userID'], 'perfil_sup', $db);
    $_SESSION['perfil_filtro'] = getCurriculaByPerfil($perfil_sup, $db);
  };

  // Vars
  $title = 'Resultados Generales P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('reporte', null);
  $mnuMainMobile = crearMnuAdminMobile('reporte', null);
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
      <?php include_once('code/top-bar-title-admin.php'); ?>

      <section class="top-bar-section">
        <?php include_once('code/top-bar-admin.php'); ?>
      </section>
    </nav>

    <!-- Menu -->
    <div class="left-sidebar show-for-large-up">
      <?php print($mnuMain); ?>
    </div>


  <main id="page" role="main" class="main mIzq45">

    <section>

      <div class="row bgGrisLight">

        <div class="small-12 columns bgBlanco">
          <div class="row">
            <div class="small-12 columns text-left">
              <h1 class="azulMain">Resulados Generales</h1>
            </div>
          </div><hr class="mTop0" /><p>&nbsp;</p>

          <div>
            <ul class="small-block-grid-2 medium-block-grid-3 large-block-grid-4">
              <li>
                <a href="resultados-generales-distribuidora.php">
                  <i class="fa fa-indent fa-4x"></i>
                <h3>Resultados Generales</h3></a>
                <p>Resultados acumulados por distribuidora, sucursal y equipos.</p>
              </li>
              <li>
                <a href="resultados-entrenamiento.php">
                  <i class="fa fa-indent fa-4x"></i>
                <h3>Resultados por Entrenamientos</h3></a>
                <p>Resportes por entrenamientos.</p>
              </li>
              <li>
                <a href="resultados-participante.php">
                  <i class="fa fa-indent fa-4x"></i>
                <h3>Resultados por Participante</h3></a>
                <p>Resportes por participante.</p>
              </li>
            </ul>
          </div>


          <p>&nbsp;</p><p>&nbsp;</p>
        </div>

      </div>

    </section>

    <footer>
      <p class="text-center">Procter & Gamble. All Rights Reserved. Powered by Inpaktu. </p>
    </footer>

  </main>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="myVideo" class="reveal-modal large" data-reveal aria-labelledby="videoModalTitle" aria-hidden="true" role="dialog">
  <h2 id="videoModalTitle">Manual de introdución</h2>
  <div class="flex-video widescreen vimeo">
    <iframe id="player2" src="//fast.wistia.net/embed/iframe/6beh0ij0oj" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="100%" height="100%"></iframe>
  </div>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>
<script>
  $(document).foundation();

</script>

  </body>
</html>

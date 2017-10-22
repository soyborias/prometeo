<?php
include_once('config.php');

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ) ){
  include_once('jupiter/code/fxVista.php');

  // Vars
  $title = 'Dashboard Administración';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('dashboard', null);
  $mnuMainMobile = crearMnuAdminMobile('dashboard', null);
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
              <h1 class="azulMain">Dashboard <?php print(convertRol($_SESSION['rol'])) ?> </h1>
            </div>
          </div><hr class="mTop0" /><p>&nbsp;</p>

          <div>
            <ul class="small-block-grid-2 medium-block-grid-3 large-block-grid-4">
              <?php if ( ($_SESSION['rol']) == ROL_ADMIN ): ?>
              <li>
                <a href="panel-participantes.php">
                  <i class="fa fa-users fa-4x"></i>
                  <h3>Participantes</h3></a>
                <p>Gestiona los usuarios, crea, autoriza y modifica a los usuarios.</p>
              </li>
              <?php endif; ?>
              <?php if ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_JEFE ): ?>
              <li>
                <a href="panel-participantes-roles.php">
                  <i class="fa fa-shield fa-4x"></i>
                  <h3>Roles</h3></a>
                <p>Administra los roles de usuarios.</p>
              </li>
              <?php endif; ?>
              <?php if ( ($_SESSION['rol']) == ROL_ADMIN ): ?>
              <li>
                <a href="crear-perfiles.php">
                  <i class="fa fa-male fa-4x"></i>
                  <h3>Perfiles</h3></a>
                <p>Administra los perfiles de los usuarios.</p>
              </li>
              <?php endif; ?>
              <?php if ( ($_SESSION['rol']) == ROL_ADMIN ): ?>
              <li>
                <a href="crear-distribuidora.php">
                  <i class="fa fa-university fa-4x"></i>
                  <h3>Universidades</h3></a>
                <p>Configuración de Universidades.</p>
              </li>
              <?php endif; ?>
              <?php if ( ($_SESSION['rol']) == ROL_ADMIN ): ?>
              <li>
                <a href="crear-entrenamiento.php">
                  <i class="fa fa-graduation-cap fa-4x"></i>
                  <h3>Cursos</h3></a>
                <p>Gestiona, crea y modifica los cursos.</p>
              </li>
              <?php endif; ?>

              <?php if ( ($_SESSION['rol']) == ROL_ADMIN ): ?>
              <li>
                <a href="crear-curricula.php">
                  <i class="fa fa-leanpub fa-4x"></i>
                  <h3>Currícula</h3></a>
                <p>Administra la currícula por perfil.</p>
              </li>
              <?php endif; ?>
              <li>
                <a href="crear-equipo.php">
                  <i class="fa fa-joomla fa-4x"></i>
                <h3>Carreras</h3></a>
                <p>Gestiona las carreras y ciclos.</p>
              </li>
              <li>
                <a href="resultados-generales.php">
                  <i class="fa fa-indent fa-4x"></i>
                <h3>Reportes</h3></a>
                <p>Monitorea el estado de los participantes con múltiples reportes.</p>
              </li>
            </ul>
          </div>


          <p>&nbsp;</p><p>&nbsp;</p>
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

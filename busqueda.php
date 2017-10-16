<?php
include_once('config.php');
$q = (isset($_POST['txtBusqueda'])) ? $_POST['txtBusqueda'] : '';


if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getBusqueda($q, $db);
  $busquedaLI = genResultadosLI($resultados);

  // Vars
  $title = 'Busquedas P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('dashboard', null);
} else {
  // User No logeado
  header ('Location: index.php');
}
?>
<!doctype html>
<html class="no-js" lang="es">
  <head>
    <?php include_once('code/header.php'); ?>
    <link rel="stylesheet" href="static/tpl/v1/css/foundation-datepicker.min.css">
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
        <div class="large-12 columns">

          <div class="row">
            <div class="large-12 columns">
              <h4 class="azulMain left bold">BUSQUEDA</h4>
              <hr class="mTop0" />
            </div>
          </div>

          <div class="row text-left">
            <div class="small-12 large-10 large-offset-1 columns">

              <div class="row">
                <ul><?php print($busquedaLI); ?></ul>
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

<?php include_once('code/script.php'); ?>
<script>
  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();
  };

</script>

  </body>
</html>
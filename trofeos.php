<?php
include_once('config.php');

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  // GetTrofeos}
  $resTrofeos = getTrofeosByUser($_SESSION['userID'], $db);
  $tblTrofeos = genTrofeosTR($resTrofeos);

  // Vars
  $title = 'Trofeos Usuario P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('perfil', null);
  $mnuMainMobile = crearMnuMainMobile('perfil', null);
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

          <div class="row">
            <div class="large-12 columns">
              <h4 class="azulMain left bold">MIS TROFEOS</h4>
              <hr class="mTop0" />
            </div>
          </div>

          <div class="row pTop1">
            <div class="large-12 columns">
              <table class="tblAzul responsive">
               <caption>
                  <div class="row">
                    <div class="small-12 columns">
                      <h4 class="blanco">Lista de Trofeos</h4>
                    </div>
                  </div>
                </caption>
                <thead>
                  <tr>
                    <th>Trofeo</th>
                    <th>Entrenamiento</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  <?php print($tblTrofeos) ?>
                </tbody>
              </table>
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

<div id="myModal" class="reveal-modal text-center" data-reveal></div>

<?php include_once('code/script.php'); ?>
<script src="js/foundation-datepicker.min.js" ></script>
<script>
  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();

    $('a.image-modal').on('click', function() {
      var img = $(this).data('img-src');
      var revealId = $(this).data('reveal-id');
      $('#' + revealId).html('<img src="' + img + '" alt=""/>').foundation('reveal','open');
    });
  };

</script>

  </body>
</html>

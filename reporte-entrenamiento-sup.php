<?php
include_once('config.php');

if ( ( isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resRpt  = getEntrenamientoRpt($_SESSION['UserList'], $db);
  $tblRpt  = genReporteEntrenamientosTR($resRpt);

  // Vars
  $title = 'Reporte por entrenamiento P&G';

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
      <?php include_once('code/top-bar-title-admin.php'); ?>

      <section class="top-bar-section">
        <?php include_once('code/top-bar-admin.php'); ?>
      </section>
    </nav>

    <!-- Menu -->
    <div class="left-sidebar show-for-large-up">
      <ul class="property-nav text-center">
        <li>
          <a href="dashboard-supervisor.php" data-tooltip aria-haspopup="true" class="tip-right" title="Dashboard"><i class="fa fa-home"></i></a>
        </li>
        <li>
          <a href="lista-participantes.php" data-tooltip aria-haspopup="true" class="tip-right" title="Participantes"><i class="fa fa-users"></i></a>
        </li>
        <li class="current">
          <a href="reporte-entrenamiento-sup.php" data-tooltip aria-haspopup="true" class="tip-right" title="Reportes"><i class="fa fa-indent"></i></a>
        </li>
        <li>
          <a href="index.php?logout" data-tooltip aria-haspopup="true" class="tip-right" title="Cerrar Sesión"><i class="fa fa-sign-out"></i></a>
        </li>
      </ul>
    </div>


  <main id="page" role="main" class="main mIzq45">

    <section>

      <div class="row pTop1">

        <div class="large-12 columns">
          <div class="row">
            <div class="small-12 medium-12 large-6 columns text-left">
              <ul class="button-group small round mDown1">
                <li><a href="reporte-acumulado-sup.php" class="button bgVerdeL m0">Acumulado</a></li>
                <li><a href="reporte-entrenamiento-sup.php" class="button bgVerdeL m0 active">Entrenamiento</a></li>
                <li><a href="reporte-participante-sup.php" class="button bgVerdeL m0">Vendedor</a></li>
                <li><a href="reporte-personalizado-sup.php" class="button bgVerdeL m0">Personalizado</a></li>
              </ul>
            </div>
          </div>

          <div class="row text-left">
            <form id="frmLista" action="reporte-lista-usuarios.php" method="post" target="_blank">
              <input type="hidden" value="" name="pids" id="pids" />
              <div class="small-12 columns">

                <div class="row">
                  <div class="large-12 columns">
                    <table class="tblAzul responsive" id="tblShow">
                      <caption>
                        <div class="row">
                          <div class="small-12 columns text-center">
                            <h4 class="blanco">REPORTE POR ENTRENAMIENTO</h4>
                          </div>
                        </div>
                      </caption>
                      <thead>
                        <tr>
                          <th>
                            <div class="categoria">
                              Nombre<br/>Entrenamiento
                            </div>
                            <div class="iconos">
                              <a href="#"><i class="fa fa-angle-up"></i></a>
                              <a href="#"><i class="fa fa-angle-down"></i></a>
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>Inscritos
                            </div>
                            <div class="iconos">
                              <a href="#"><i class="fa fa-angle-up"></i></a>
                              <a href="#"><i class="fa fa-angle-down"></i></a>
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Entrenamientos<br/>Completados
                            </div>
                            <div class="iconos">
                              <a href="#"><i class="fa fa-angle-up"></i></a>
                              <a href="#"><i class="fa fa-angle-down"></i></a>
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % Entrenamientos<br/>Completados
                            </div>
                            <div class="iconos">
                              <a href="#"><i class="fa fa-angle-up"></i></a>
                              <a href="#"><i class="fa fa-angle-down"></i></a>
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>En Proceso
                            </div>
                            <div class="iconos">
                              <a href="#"><i class="fa fa-angle-up"></i></a>
                              <a href="#"><i class="fa fa-angle-down"></i></a>
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % Avances<br/>En Proceso
                            </div>
                            <div class="iconos">
                              <a href="#"><i class="fa fa-angle-up"></i></a>
                              <a href="#"><i class="fa fa-angle-down"></i></a>
                            </div>
                          </th>
                        </tr>
                      </thead>
                      </thead>
                      <?php print($tblRpt); ?>
                  </div>
                </div>

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

<?php include_once('code/script.php'); ?>
<script src="js/responsive-tables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.23.2/js/jquery.tablesorter.js"></script>
<script>
  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();
    $("#tblShow").tablesorter();

    $('#tblShow tbody tr td a.lista').on('click', showLista);
  };

  function showLista(e){
    e.preventDefault();
    var rel = $(this).attr("rel");
    $("#pids").val( rel );
    $("#frmLista").submit();
  }

</script>

  </body>
</html>
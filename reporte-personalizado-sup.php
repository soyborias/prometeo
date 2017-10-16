<?php
include_once('config.php');
$type  = (isset($_POST['t']))  ?   $_POST['t']  : null;

if ( ( isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  switch ($type) {
    case 'entrenamiento':
      $pk1  = (isset($_POST['pk1']))  ?   $_POST['pk1']  : array();

      $resRpt  = getEntrenamientoRpt($_SESSION['UserList'], $db);
      $tblRpt  = genRptCustomETB($resRpt, $pk1);
      break;

    case 'acumulado':
      $pk2  = (isset($_POST['pk2']))  ?   $_POST['pk2']  : array();

      if (in_array('a', $pk2)){
        $resRpt  = getEntrenamientoRptByDis($_SESSION['UserList'], $db);
        $tblRpt  = genRptCustomATB('Distribuidora', $resRpt, $pk2);
      } else {
        $resRpt  = getEntrenamientoRptByCity($_SESSION['UserList'], $db);
        $tblRpt  = genRptCustomATB('Ciudad', $resRpt, $pk2);
      }
      break;

    case 'vendedores':
      $pk3  = (isset($_POST['pk3']))  ?   $_POST['pk3']  : array();

      $resRpt  = getEntrenamientoRptByVendedor($_SESSION['UserList'], $db);
      $tblRpt  = genRptCustomVTB($resRpt, $pk3);

      break;

    default:
      # code...
      break;
  }

  // Vars
  $title = 'Reporte Personalizado P&G';

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

          <div class="row">
            <div class="large-12 columns">
              <table class="tblAzul responsive" id="tblShow">
                <caption>
                  <div class="row">
                    <div class="small-12 columns text-center">
                      <h4 class="blanco">REPORTE PERSONALIZADO</h4>
                    </div>
                  </div>
                </caption>
              </table>
            </div>
          </div>

          <?php if (isset($type)) {?>
          <div class="row repo-personalizado">
            <div class="small-12 columns">
              <?php print($tblRpt); ?>
            </div>
          </div>
          <?php } ?>

          <div class="row text-left repo-personalizado">

            <div class="small-12 medium-4 columns">
              <form id="frmEntrenamiento" action="" method="POST">
                <input type="hidden" value="entrenamiento" name="t">
                <h5>ENTRENAMIENTO</h5>
                <ul class="pLeft1 no-bullet">
                  <li><input type="checkbox" name="pk1[]" value="a" id="pk1a"><label for="pk1a">Entrenamiento</label></li>
                  <li><input type="checkbox" name="pk1[]" value="b" id="pk1b"><label for="pk1b">Inscritos</label></li>
                  <li><input type="checkbox" name="pk1[]" value="c" id="pk1c"><label for="pk1c">Aprobados</label></li>
                  <li><input type="checkbox" name="pk1[]" value="d" id="pk1d"><label for="pk1d">% Aprobados</label></li>
                  <li><input type="checkbox" name="pk1[]" value="e" id="pk1e"><label for="pk1e">En Proceso</label></li>
                  <li><input type="checkbox" name="pk1[]" value="f" id="pk1f"><label for="pk1f">% En Proceso</label></li>
                </ul>
                <button class="small round button success" type="submit">GENERAR REPORTE</button>
              </form>
            </div>

            <div class="small-12 medium-4 columns">
              <form id="frmAcumulado" action="" method="POST">
                <input type="hidden" value="acumulado" name="t">
                <h5>ACUMULADO</h5>
                <ul class="pLeft1 no-bullet">
                  <li><input type="checkbox" name="pk2[]" value="a" id="pk2a"><label for="pk2a">Distribuidora</label></li>
                  <li><input type="checkbox" name="pk2[]" value="b" id="pk2b"><label for="pk2b">Ciudad</label></li>
                  <li><input type="checkbox" name="pk2[]" value="c" id="pk2c"><label for="pk2c">Inscritos</label></li>
                  <li><input type="checkbox" name="pk2[]" value="d" id="pk2d"><label for="pk2d">Completados</label></li>
                  <li><input type="checkbox" name="pk2[]" value="e" id="pk2e"><label for="pk2e">% Completados</label></li>
                </ul>
                <button class="small round button success" type="submit">GENERAR REPORTE</button>
              </form>
            </div>

            <div class="small-12 medium-4 columns">
              <form id="frmVendedores" action="" method="POST">
                <input type="hidden" value="vendedores" name="t">
                <h5>VENDEDORES</h5>
                <ul class="pLeft1 no-bullet">
                  <li><input type="checkbox" name="pk3[]" value="a" id="pk3a"><label for="pk3a">Vendedor</label></li>
                  <li><input type="checkbox" name="pk3[]" value="b" id="pk3b"><label for="pk3b">Documento</label></li>
                  <li><input type="checkbox" name="pk3[]" value="c" id="pk3c"><label for="pk3c">Puntos Ganados</label></li>
                  <li><input type="checkbox" name="pk3[]" value="d" id="pk3d"><label for="pk3d">Entrenamientos Completados</label></li>
                  <li><input type="checkbox" name="pk3[]" value="e" id="pk3e"><label for="pk3e">Puntaje Promedio</label></li>
                  <li><input type="checkbox" name="pk3[]" value="f" id="pk3f"><label for="pk3f">Último Acceso</label></li>
                </ul>
                <button class="small round button success" type="submit">GENERAR REPORTE</button>
              </form>
            </div>

          </div>

        </div>
      </div>

    </section>

    <footer>
      <?php include_once('code/footer.php'); ?>
    </footer>

  </main>

<div id="myBlock" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">GENERAR.</h2>
  <p class="lead">Generar Reporte</p>
  <p>Esta opción genera un reporte en base a las opciones elegidas.</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

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

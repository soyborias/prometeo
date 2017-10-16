 <?php
include_once('config.php');

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resCurricula  = getCurriculaByPerfil($_SESSION['perfil'], $db);
  $perfilEntrena = isset($resCurricula[0]['curricula_entrenamientos']) ? $resCurricula[0]['curricula_entrenamientos'] : -1;

  // Resumen
  $resumen = getResumenByUser($_SESSION['userID'], $db);
  $resRpt  = getEntrenamientoByUserByPerfil($_SESSION['userID'], $perfilEntrena, $db);
  $tblRpt  = genReporteUsuarioTR($resRpt);
  $acceso  = getUltimoAcceso($_SESSION['userID'], $db);
  $total   = getTotalAccesos($_SESSION['userID'], $db);

  //$numEntrenamiento = intval(  getEntrenamientosFin($_SESSION['userID'], $db) );
  $resFin  = getEntrenamientosFinOk($_SESSION['userID'], $db);
  $resProg = getEntrenamientosProgOk($_SESSION['userID'], $db);

  $fin  = count( preg_split('/,/', $resFin, -1, PREG_SPLIT_NO_EMPTY) );
  $prog = count( preg_split('/,/', $resProg, -1, PREG_SPLIT_NO_EMPTY) );

  $full = count( preg_split('/,/', $perfilEntrena, -1, PREG_SPLIT_NO_EMPTY) );

  // Vars
  $title = 'Reporte de Usuario P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('reporte', null);
  $mnuMainMobile = crearMnuMainMobile('reporte', null);
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

          <div class="row text-left">
            <form>
              <div class="small-12 columns">

                <div class="row">
                  <div class="large-12 columns">
                    <h4 class="azulMain left bold">MI REPORTE</h4>
                    <hr class="mTop0" />
                  </div>
                </div>

                <div class="row pTop1">
                  <div class="large-12 columns">
                    <table class="tblAzul responsive">
                     <caption>
                        <div class="row">
                          <div class="small-12 columns text-left">
                            <h4 class="blanco">TOTAL</h4>
                          </div>
                        </div>
                      </caption>
                      <thead>
                        <tr>
                          <th>Puntos<br/>Ganados</th>
                          <th>Entrenamientos<br/>Activos</th>
                          <th>Entrenamientos<br/>Completados</th>
                          <th>Entrenamientos<br/>en Proceso</th>
                          <th>% promedio<br/>de Aciertos</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><div><?php print($resumen['puntos_suma']); ?></div></td>
                          <td><div><?php print $full; ?></div></td>
                          <td><div><?php print $fin; ?></div></td>
                          <td><div><?php print $prog; ?></div></td>
                          <td><div><?php print($resumen['puntos_aciertos']); ?>%</div></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="row pTop1">
                  <div class="large-12 columns" id="dvData">
                    <table class="tblAzul responsive" id="tblShow">
                      <caption>
                        <div class="row">
                          <div class="small-12 medium-3 columns text-left">
                            <h4 class="blanco">REPORTE POR ENTRENAMIENTO</h4>
                          </div>
                          <div class="small-12 medium-9 columns text-right show-for-large-up">
                            <ul class="button-group round">
                              <li></li>
                            </ul>
                          </div>
                        </div>
                      </caption>
                      <thead>
                        <tr>
                          <th>
                            <div class="categoria top">
                              Entrenamientos
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Puntos<br/>Ganados
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Puntaje<br/>Máximo
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % de<br/>Avances
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % de<br/>Aciertos
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Trofeo
                            </div>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php print($tblRpt); ?>
                      </tbody>
                    </table>
                  </div>
                  <p class="text-center"><button id="btnExport" class="button round"><i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i> Exportar Excel</button></p>
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

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="myMsg" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">Acumulado.</h2>
  <p>Esta opción acumula los datos del periodo seleccionado y los muestra</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="myMsg2" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">Reportes.</h2>
  <p>Esta opción cambia el tipo de reporte seleccionado y los muestra</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>
<script src="js/responsive-tables.js"></script>
<script src="js/jquery.table2excel.min.js"></script>
<script>
  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();

    $('#btnExport').on('click', exportXLS);
  };

  function exportXLS(e){
    $("#tblShow").table2excel({
        exclude: ".noExl",
        name: "ReporteExcelPG",
        filename: "ReporteExcelPG",
        fileext: ".xls",
        exclude_img: true,
        exclude_links: true,
        exclude_inputs: true
    });
  };

</script>

  </body>
</html>

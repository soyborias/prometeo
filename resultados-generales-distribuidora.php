<?php
error_reporting(E_ALL);
ini_set( 'display_errors','1');
include_once('config.php');

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resRpt  = getEntrenamientoRptByDis_v2($_SESSION['equipo_filtro'], $db);
  $tblRpt  = genReporteGeneralDisTR_v2($resRpt);

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

      <div class="row pTop1">

        <div class="large-12 columns">
          <div class="row">
            <div class="small-12 columns">
              <ul class="button-group round small">
                <li><a href="resultados-generales-distribuidora.php" class="button bgVerdeL active">Distribuidora</a></li>
                <li><a href="resultados-generales-sucursal.php" class="button bgVerdeL">Sucursal</a></li>
                <li><a href="resultados-generales-equipos.php" class="button bgVerdeL">Equipos</a></li>
              </ul>
            </div>
          </div>

          <div class="row text-left">
            <form id="frmLista" action="reporte-lista-usuarios.php" method="post" target="_blank">
              <input type="hidden" value="" name="pids" id="pids" />
              <div class="small-12 columns">

                <div class="row">
                  <div class="large-12 columns overflow-x">
                    <table class="tblAzul responsive" id="tblShow">
                      <caption>
                        <div class="row">
                          <div class="small-12 columns text-center">
                            <h4 class="blanco">REPORTE ACUMULADO POR DISTRIBUIDORA</h4>
                          </div>
                        </div>
                      </caption>
                      <thead>
                        <tr>
                          <th>
                            <div class="categoria">
                              Distribuidora
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>Totales
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>Inactivos
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>Activos Totales
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>Entrenados al 100%
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % Usuarios<br/>Entrenados al 100%
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>minimo 1 entrenamiento<br/>terminado
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % Usuarios<br/>minimo 1 entrenamiento<br/>terminado
                            </div>
                          </th>
                        </tr>
                      </thead>
                      <?php print($tblRpt); ?>
                  </div>
                </div>


              </div>
            </form>
            <p class="text-center"><button id="btnExport" class="button round"><i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i> Exportar Excel</button></p>
          </div>

        </div>
      </div>

    </section>

  </main>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="myCero" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">Cero Resultados</h2>
  <p id="modalDescrip" class="rojo">No hay resultados que mostrar</p>

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
    $('#tblShow tbody tr td a.lista').on('click', showLista);
  };

  function showLista(e){
    e.preventDefault();
    var rel = $(this).attr("rel");
    var text = $(this).html();

    if (text == 0 || text == "0" ){
      $('#myCero').foundation('reveal', 'open');
    } else {
      $("#pids").val( rel );
      $("#frmLista").submit();
    }
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

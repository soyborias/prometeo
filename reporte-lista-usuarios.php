<?php
include_once('config.php');
$search     = (isset($_POST['pids'])) ? $_POST['pids'] : '';

$equipo   = (isset($_POST['fEquipo']))          ? intval($_POST['fEquipo'])          : -1;
$perfil   = (isset($_POST['fPerfil']))          ? intval($_POST['fPerfil'])          : -1;
$distro   = (isset($_POST['fDistribuidora']))   ? intval($_POST['fDistribuidora'])   : -1;
$sucursal = (isset($_POST['fSucursal']))        ? intval($_POST['fSucursal'])        : -1;

$filter = array('equipo' => $equipo, 'perfil' => $perfil, 'distro' => $distro, 'sucursal' => $sucursal );

if ( ( isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN OR  $_SESSION['rol'] == ROL_SUPERVISOR OR  $_SESSION['rol'] == ROL_JEFE ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resRpt  = getListUserTotalesFilter($search, $filter, $db);
  $tblRpt  = genRptListUsersTB($resRpt);

  // Vars
  $title = 'Lista de usuarios P&G';

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

  <main id="page" role="main" class="main mIzq45">

    <section>

      <div class="row pTop1">
        <div class="large-12 columns">

          <div class="row text-left">
            <form>
              <div class="small-12 columns">

                <div class="row">
                  <div class="large-12 columns">
                    <?php print $tblRpt; ?>
                  </div>
                </div>

              </div>
            </form>
          </div>

          <p class="text-center"><button id="btnExport" class="button round"><i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i> Exportar Excel</button></p>

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

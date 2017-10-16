<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('config.php');
$q = (isset($_GET['q'])) ? $_GET['q'] : '';

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $filter = '';
  $equipo   = (isset($_POST['cboEquipo']))   ? $_POST['cboEquipo']   : -1;
  $perfil   = (isset($_POST['cboPerfil']))   ? $_POST['cboPerfil']   : -1;
  $distro   = (isset($_POST['cboDistribuidora']))   ? $_POST['cboDistribuidora']   : -1;
  $sucursal = (isset($_POST['cboSucursal'])) ? $_POST['cboSucursal'] : -1;
  if ($q === 'filter'){
    $filter = array('equipo' => $equipo, 'perfil' => $perfil, 'distro' => $distro, 'sucursal' => $sucursal );
  }

  $readonly   = '';
  if ( $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ){
    $readonly   = ' readonly="readonly" disabled="true" ';
    $sup_loc    = getLocationUser($_SESSION['userID'], $db);
    $distro     = $sup_loc['distributor_id'];
    $sucursal   = $sup_loc['subsidiary_id'];
    $perfil     = getSupervisorRel($_SESSION['userID'], 'perfil_sup', $db);
  }
  $resEquipos = getEquipos(0, $db);
  if ( $_SESSION['rol'] == ROL_SUPERVISOR ){
    $resEquipos = getEquiposBySup($_SESSION['userID'], $db);
    $equipo     = ( count($resEquipos)>0 )  ?  $resEquipos[0]['team_id']  :  $equipo;
  } elseif ( $_SESSION['rol'] == ROL_JEFE ) {
    $supJefe    = getSupByJefe($_SESSION['userID'], $db);
    $resEquipos = getEquiposByJefe($supJefe, $db);
  }
  $opEquipos  = genEquiposOP($resEquipos, $equipo, false);

  $resRpt  = getEntrenamientoRptByParticipante($_SESSION['equipo_filtro'], $filter, $db);
  $tblRpt  = genReporteEParticipanteTR($resRpt);
  $num     = count($resRpt);

  $resultados = getPerfiles(0, $db);
  $opPerfiles = genPerfilesOP($resultados, $perfil, false);

  $resDis = getDistribuidoras($db);
  $opDistribuidoras = genDistribuidorasOP($resDis, $distro, false);

  $resSuc = getSucursalesFull($db);
  $opSucursales = genSucursalesOP($resSuc, $sucursal, false);

  // Vars
  $title = 'Reporte Personalizado P&G';
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
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.12/css/dataTables.foundation.min.css">
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
            <div class="small-12 medium-12 large-6 columns text-left">
              <ul class="button-group small round mDown1">
                <li><a href="reporte-acumulado.php" class="button bgVerdeL m0">Acumulado</a></li>
                <li><a href="reporte-entrenamiento.php" class="button bgVerdeL m0">Entrenamiento</a></li>
                <li><a href="reporte-participante.php" class="button bgVerdeL m0">Participante</a></li>
                <li><a href="reporte-personalizado.php" class="button bgVerdeL m0 active">Personalizado</a></li>
              </ul>
            </div>
            <div class="small-12 medium-12 large-6 columns" style="padding-top: 0.5em;">
              <form id="frmFiltro" action="reporte-personalizado.php?q=filter" method="post">
                <div class="row collapse">
                  <div class="small-3 columns">
                    <select id="cboPerfil" name="cboPerfil" <?php print($readonly) ?>>
                      <option value="-1">PERFILES</option>
                      <?php echo $opPerfiles; ?>
                    </select>
                  </div>
                  <div class="small-3 columns">
                    <select id="cboDistribuidora" name="cboDistribuidora" <?php print($readonly) ?>>
                      <option value="-1">DISTRIBUIDORAS</option>
                      <?php echo $opDistribuidoras; ?>
                    </select>
                  </div>
                  <div class="small-3 columns">
                    <select id="cboSucursal" name="cboSucursal" <?php print($readonly) ?>>
                      <option value="-1">SUCURSALES</option>
                      <?php echo $opSucursales; ?>
                    </select>
                  </div>
                  <div class="small-2 columns">
                    <select id="cboEquipo" name="cboEquipo" <?php print($readonly2) ?>>
                      <option value="-1">EQUIPOS</option>
                      <?php echo $opEquipos; ?>
                    </select>
                  </div>
                  <div class="small-1 columns">
                    <button type="submit" class="button postfix" style="padding: 0;">Filtrar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

                <div class="row">
                  <form id="frmLista" action="reporte-lista-usuarios.php" method="post" target="_blank">
                    <div class="large-12 columns">
                      <table class="tblAzul small responsive" id="tblShow">
                        <caption>
                          <div class="row">
                            <div class="small-12 columns text-center">
                              <h4 class="blanco">REPORTE PERSONALIZADO</h4>
                            </div>
                          </div>
                        </caption>
                        <thead>
                          <tr>
                            <th>
                              <div class="categoria">Participante</div>
                            </th>
                            <th>
                              <div class="categoria">Documento</div>
                            </th>
                            <th>
                              <div class="categoria">Email</div>
                            </th>
                            <th>
                              <div class="categoria">Perfil</div>
                            </th>
                            <th>
                              <div class="categoria">Distribuidora</div>
                            </th>
                            <th>
                              <div class="categoria">Sucursal</div>
                            </th>
                            <th>
                              <div class="categoria">Supervisor</div>
                            </th>
                            <th>
                              <div class="categoria">
                                Puntos<br/>Ganados
                              </div>
                            </th>
                            <th>
                              <div class="categoria">
                                Porcentaje<br/>Aciertos
                              </div>
                            </th>
                            <th>
                              <div class="categoria">
                                Entrenamientos<br/>Completados
                              </div>
                            </th>
                            <th>
                              <div class="categoria">
                                Último<br/>Acceso
                              </div>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php print($tblRpt); ?>
                        </tbody>
                      </table>
                    </div>
                  </form>
                </div>
                <p class="text-center"><button id="btnExport" class="button round"><i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i> Exportar Excel</button></p>

              </div>
          </div>


        </div>
      </div>

    </section>

  </main>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<?php include_once('code/script.php'); ?>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/dataTables.foundation.min.js"></script>
<script src="js/responsive-tables.js"></script>
<script src="js/jquery.table2excel.min.js"></script>
<script>
  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();
    $('#tblShow').DataTable();

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

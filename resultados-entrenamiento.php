<?php
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

  $readonly  = '';
  $readonly2 = '';
  if ( $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ){
    $resEquipos = getEquiposBySup($_SESSION['userID'], $db);
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

  $resRpt  = getEntrenamientoRptFullv2($_SESSION['equipo_filtro'], $_SESSION['perfil_filtro'], $filter, $db);
  $tblRpt  = genResultadosEntrenamientosTRv2($resRpt);

  //$resultados = getEquipos(0, $db);
  //$opEquipos  = genEquiposOP($resultados, $equipo, false);

  $resultados = getPerfiles(0, $db);
  $opPerfiles = genPerfilesOP($resultados, $perfil, false);

  $resDis = getDistribuidoras($db);
  $opDistribuidoras = genDistribuidorasOP($resDis, $distro, false);

  $resSuc = getSucursalesFull($db);
  $opSucursales = genSucursalesOPFull($resSuc, $sucursal, false);

  // Vars
  $title = 'Reporte por entrenamiento P&G';
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

        <div class="large-12 columns" style="padding-top: 0.5em;">
          <form id="frmFiltro" action="resultados-entrenamiento.php?q=filter" method="post">
            <div class="row collapse">
              <div class="small-4 columns">
                <select id="cboDistribuidora" name="cboDistribuidora" <?php print($readonly) ?>>
                  <option value="-1">DISTRIBUIDORAS</option>
                  <?php print($opDistribuidoras) ?>
                </select>
              </div>
              <div class="small-4 columns">
                <select id="cboSucursal" name="cboSucursal" <?php print($readonly) ?>>
                  <option value="-1">SUCURSALES</option>
                  <?php print($opSucursales) ?>
                </select>
              </div>
              <div class="small-3 columns">
                <select id="cboEquipo" name="cboEquipo" <?php print($readonly2) ?>>
                  <option value="-1">EQUIPOS</option>
                  <?php print($opEquipos) ?>
                </select>
              </div>
              <div class="small-1 columns">
                <button type="submit" class="button postfix" style="padding:0">Filtrar</button>
              </div>
            </div>
          </form>
        </div>

        <div class="large-12 columns">
          <div class="row text-left">
            <form id="frmTemas" action="resultados-temas.php" method="post">
              <input type="hidden" value="" name="pid" id="pid" />
              <input type="hidden" value="<?php print($equipo) ?>" name="fEquipo">
              <input type="hidden" value="<?php print($sucursal) ?>" name="fSucursal">
              <input type="hidden" value="<?php print($distro) ?>" name="fDistribuidora">
              <input type="hidden" value="<?php print($perfil) ?>" name="fPerfil">
            </form>
            <form id="frmLista" action="reporte-lista-usuarios.php" method="post" target="_blank">
              <input type="hidden" value="" name="pids" id="pids" />
              <input type="hidden" value="<?php print($equipo) ?>" name="fEquipo">
              <input type="hidden" value="<?php print($sucursal) ?>" name="fSucursal">
              <input type="hidden" value="<?php print($distro) ?>" name="fDistribuidora">
              <input type="hidden" value="<?php print($perfil) ?>" name="fPerfil">
              <div class="small-12 columns">

                <div class="row">
                  <div class="large-12 columns overflow-x">
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
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>Activos Totales
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios<br/>Entrenados
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % Usuarios<br/>Entrenados
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              Usuarios con<br/>entrenamientos<br/>En Proceso
                            </div>
                          </th>
                          <th>
                            <div class="categoria">
                              % Usuarios con<br/>entrenamientos<br/>En Proceso
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
  var ajaxReq = 'jupiter/api.php';
  var auth = <?php print $_SESSION['userID']; ?>;

  var ajaxReq = "jupiter/api.php";
  var json = null;
  var jsonCity  = <?php print json_encode($resSuc); ?>;
  var city = -1;
  var jsonTeam  = <?php print json_encode($resEquipos); ?>;
  var team = -1;

  function iniciar(){
    $(document).foundation();

    $('#btnExport').on('click', exportXLS);
    $('#tblShow tbody tr td a.lista').on('click', showLista);
    $('#tblShow tbody tr td a.parent').on('click', showParent);

    $('#cboDistribuidora').on('change', changeDistribuidora);
    $('#cboSucursal').on('change', changeSurcursal);
  };

  function changeDistribuidora(){
    fillSucursalCBO( $(this).val() );
    fillTeamCBOByDistro( $(this).val() );
  };

  function changeSurcursal(){
    fillTeamCBOBySucursal( $(this).val() );
  };

  function fillSucursalCBO(distributor_id){
    var cbo =  $('#cboSucursal');
    cbo.empty();
    cbo.append('<option value="-1">SUCURSALES</option>');
    $.each(jsonCity, function(i, item) {
      if ( parseInt(item.distributor_id) === parseInt(distributor_id) ){
        cbo.append('<option value="' + item.subsidiary_id + '">' + item.subsidiary_name + '</option>');
      }
    });
  };

  function fillTeamCBOByDistro(distributor_id){
    var cbo =  $('#cboEquipo');
    cbo.empty();
    cbo.append('<option value="-1">EQUIPOS</option>');
    $.each(jsonTeam, function(i, item) {
      if ( parseInt(item.distributor_id) === parseInt(distributor_id) ){
        cbo.append('<option value="' + item.team_id + '">' + item.team_descrip + '</option>');
      }
    });
  };

  function fillTeamCBOBySucursal(subsidiary_id){
    var cbo =  $('#cboEquipo');
    cbo.empty();
    cbo.append('<option value="-1">EQUIPOS</option>');
    $.each(jsonTeam, function(i, item) {
      if ( parseInt(item.subsidiary_id) === parseInt(subsidiary_id) ){
        cbo.append('<option value="' + item.team_id + '">' + item.team_descrip + '</option>');
      }
    });
  };

  function showLista(e){
    e.preventDefault();
    var rel = $(this).attr("rel");
    $("#pids").val( rel );
    $("#frmLista").submit();
  };

  function showParent(e){
    e.preventDefault();
    var rel = $(this).attr("rel");
    $("#pid").val( rel );
    $("#frmTemas").submit();
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

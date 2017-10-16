<?php
include_once('config.php');
$q = (isset($_GET['q'])) ? $_GET['q'] : '';

switch ($q) {
  case 'search':
    $nom = (isset($_POST['txtNombre']))    ? $_POST['txtNombre']    : '';
    $doc = (isset($_POST['txtDocumento'])) ? $_POST['txtDocumento'] : '';
    $distro = (isset($_POST['cboBuscarDistro']))   ? $_POST['cboBuscarDistro']   : '';
    $ciudad = (isset($_POST['cboBuscarSucursal'])) ? $_POST['cboBuscarSucursal'] : '';
    $sexo   = (isset($_POST['opSexo'])) ? $_POST['opSexo'] : '';
    $filter = array('nom' => $nom, 'doc' => $doc, 'distro' => $distro, 'ciudad' => $ciudad, 'sexo' => $sexo );
    $view   = 'BUSQUEDA: '. $nom. ' '. $doc;
    break;

  default:
    $view    = 'PARTICIPANTES ASIGNADOS';
    break;
}

if ( (isset($_SESSION['username'])) && ($_SESSION['rol']) == ROL_SUPERVISOR ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados  = ($q !== 'search') ? getUsuariosBySupervisor($_SESSION['userID'], $db) : getUsuariosBySearchBySupervisor($filter, $_SESSION['userID'], $db);
  $tblUsuarios = genUsuariosTRSmall($resultados);

  $resSuper = getUsuariosByType(ROL_SUPERVISOR, $db);
  $opSuper  = genUsuariosOP($resSuper, '');

  $resDis = getDistribuidoras($db);
  $opDistribuidoras = genDistribuidorasOP($resDis, '');

  $resSuc = getSucursalesFull($db);
  $opSucursales = genSucursalesOP($resSuc, '');

  $opRol  = genRolOP();

  // Vars
  $title = 'Panel Usuarios P&G';

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
      <?php include_once('code/top-bar-title.php'); ?>

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
        <li class="current">
          <a href="lista-participantes.php" data-tooltip aria-haspopup="true" class="tip-right" title="Participantes"><i class="fa fa-users"></i></a>
        </li>
        <li>
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

          <div>
            <h4 class="azulMain left bold">PANEL DE PARTICIPANTES</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <div class="small-12 medium-8 large-7 columns">
              <h4 class="azulMain left"><?php print $view; ?> <span id="lblTitle" class="orange"></span></h4>
              <hr class="mTop0" />

              <table id="tblUsuarios" width="100%">
                <thead>
                  <tr>
                    <th width="30%">Nombre</th>
                    <th width="10%">Documento</th>
                    <th width="10%">Distribuidora</th>
                    <th width="10%">Sucursal</th>
                    <th width="30%">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php print($tblUsuarios); ?>
                </tbody>
              </table>
              <p id="msgbox" class="text-center"></p>
            </div>

            <div class="small-12 medium-4 large-5 columns">
              <div class="row">

                <div class="large-12 columns">
                  <h4 class="azulMain">BUSCAR PARTICIPANTES <a id="btnNewItem" class="right"><i class="fa fa-search"></i> Buscar</a></h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <form id="frmBuscar" action="lista-participantes.php?q=search" method="post">

                    <div class="row">
                      <div class="large-12 columns">
                        <label>Nombre
                          <input id="txtNombre" name="txtNombre" type="text" placeholder="Nombre del usuario" />
                        </label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="large-12 columns">
                        <label>Documento
                          <input id="txtDocumento" name="txtDocumento" type="text" placeholder="Documento de identidad" />
                        </label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="large-6 columns">
                        <label>Distribuidora
                          <select id="cboBuscarDistro" name="cboBuscarDistro">
                            <?php print($opDistribuidoras); ?>
                          </select>
                        </label>
                      </div>
                      <div class="large-6 columns">
                        <label>Sucursal
                          <select id="cboBuscarSucursal" name="cboBuscarSucursal">
                            <?php print($opSucursales); ?>
                          </select>
                        </label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="large-12 columns">
                        <label>Sexo</label>
                        <input type="radio" name="opSexo" value="Masculino" id="opMasculino"><label for="opMasculino">Masculino</label>
                        <input type="radio" name="opSexo" value="Femenino" id="opFemenino"><label for="opFemenino">Femenino</label>
                        <input type="radio" name="opSexo" value="Cualquiera" id="opCualquiera" checked><label for="opCualquiera">Cualquiera</label>
                      </div>
                    </div><hr class="mTop0" />

                    <div class="row text-center">
                      <button class="small round" id="btnBuscar" type="submit">BUSCAR</button>
                    </div>

                  </form>
                </div><p>&nbsp;</p>

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

<div id="myCambiar" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">CAMBIAR CONTRASEÑA</h2>

  <label>
    <input type="text" value="" id="txtContrasena" placeholder="Contraseña" />
  </label>
  <label>
    <input type="text" value="" id="txtRepcontrasena" placeholder="Repetir Contraseña" />
  </label>
  <label>
    <input type="text" value="" id="txtContrasenaSuper" placeholder="Contraseña del Autorizador" class="border-important" />
  </label>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnCambiarOk" class="button round success">Aprobar</a> <span class="msgbox pLeft5"></span>
</div>


<?php include_once('code/script.php'); ?>
<script>
  $(document).on('ready', iniciar);
  var ajaxReq = 'jupiter/api.php';
  var auth = 0;
  var curControl = null;
  var rel  = null;
  var href = null;
  var tr   = null;
  var code = -1;
  var jsonCity  = <?php print json_encode($resSuc); ?>;

  function iniciar(){
    $(document).foundation();
    $('#btnBorrarOk').on('click', deleteUser);
    $('#btnBuscar').on('click', searchParticipantes);

    $('a.custom-close-reveal-modal').click(function(){
      $('#myDelete').foundation('reveal', 'close');
    });
  };

  function searchParticipantes(e){
    var nom = $('#txtNombre').val();
    var doc = $('#txtDocumento').val();
    var distro = $('#cboBuscarDistro').val();
    var ciudad = $('#cboBuscarSucursal').val();

    // Preparar Busqueda
  }

  function reloadParticipantes(e){
    $('#lblTitle').html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
  }

  function changeDistribuidora(){
    fillSucursalCBO( $(this).val() );
  };

  function fillSucursalCBO(distributor_id){
    var cbo =  $('#cboChangeSucursal');
    cbo.empty();
    cbo.append('<option value="-1">Seleccionar...</option>');
    $.each(jsonCity, function(i, item) {
      if ( parseInt(item.distributor_id) === parseInt(distributor_id) ){
        cbo.append('<option value="' + item.subsidiary_id + '">' + item.subsidiary_name + '</option>');
      }
    });
  };

</script>

  </body>
</html>

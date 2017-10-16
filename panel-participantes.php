<?php
include_once('config.php');
$q = (isset($_GET['q'])) ? $_GET['q'] : ROL_NEW;

switch ($q) {
  case 'admin':
    $viewRol = ROL_ADMIN;
    $view    = 'ADMINISTRADORES';
    break;

  case 'super':
    $viewRol = ROL_SUPERVISOR;
    $view    = 'SUPERVISORES';
    break;

  case 'user':
    $viewRol = ROL_USER;
    $view    = 'PARTICIPANTES';
    break;

  case 'search':
    $nom = (isset($_POST['txtNombre']))    ? $_POST['txtNombre']    : '';
    $doc = (isset($_POST['txtDocumento'])) ? $_POST['txtDocumento'] : '';
    $distro = (isset($_POST['cboBuscarDistro']))   ? $_POST['cboBuscarDistro']   : '';
    $ciudad = (isset($_POST['cboBuscarSucursal'])) ? $_POST['cboBuscarSucursal'] : '';
    $sexo   = (isset($_POST['opSexo'])) ? $_POST['opSexo'] : '';
    $filter = array('nom' => $nom, 'doc' => $doc, 'distro' => $distro, 'ciudad' => $ciudad, 'sexo' => $sexo );
    $view   = 'BUSQUEDA: '. $nom. ' '. $doc;
    break;

  case 'perfil':
    $perfil_id = (isset($_GET['s']))    ? $_GET['s']    : -1;
    $view    = 'PERFILES';
    break;

  default:
    $viewRol = ROL_NEW;
    $view    = 'NUEVOS PARTICIPANTES';
    break;
}

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados  = ($q !== 'search') ? ( ($q === 'perfil') ? getUsuariosByPerfil($perfil_id, $db) : getUsuariosByType($viewRol, $db) ) : getUsuariosBySearch($filter, $db);
  $tblUsuarios = genUsuariosTR($resultados);

  $resSuper = getUsuariosByType(ROL_SUPERVISOR, $db);
  $opSuper  = genUsuariosOP($resSuper, '');

  $resDis = getDistribuidoras($db);
  $opDistribuidoras = genDistribuidorasOP($resDis, '');

  $resSuc = getSucursalesFull($db);
  $opSucursales = genSucursalesOP($resSuc, '');

  $opRol  = genRolOP();

  $resPerfil = getPerfilesFull($db);
  $opPerfil  = genPerfilesOP($resPerfil, '');
  $liPerfil  = genPerfilesLI($resPerfil, '');

  // Vars
  $title = 'Panel Usuarios P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('participante', null);
  $mnuMainMobile = crearMnuAdminMobile('participante', null);
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

          <div>
            <h4 class="azulMain left bold">PANEL DE PARTICIPANTES</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <div class="small-12 medium-12 large-9 columns">
              <h4 class="azulMain left"><?php print $view; ?> <span id="lblTitle" class="orange"></span></h4>
              <a href="#" data-options="align:left" data-dropdown="lstPerfilUser" class="right mIzq1"><i class="fa fa-filter"></i> Perfiles</a>
              <ul id="lstPerfilUser" class="small f-dropdown" data-dropdown-content>
                <li><a href="?q=perfil&amp;s=-1">Sin Asignar</a></li>
                <?php print $liPerfil; ?>
              </ul>
              <hr class="mTop0" />

              <table id="tblUsuarios" width="100%" class="responsive">
                <thead>
                  <tr>
                    <th width="20%">Nombre</th>
                    <th width="5%">Documento</th>
                    <th width="10%">Distribuidora</th>
                    <th width="10%">Sucursal</th>
                    <th width="10%">Supervisor</th>
                    <th width="10%">Perfil</th>
                    <th width="5%">Activar</th>
                    <th width="30%">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php print($tblUsuarios); ?>
                </tbody>
              </table>
              <p id="msgbox" class="text-center"></p>
            </div>

            <div class="small-12 medium-12 large-3 columns">
              <div class="row">

                <div class="large-12 columns">
                  <h4 class="azulMain">BUSCAR PARTICIPANTES</h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <form id="frmBuscar" action="panel-participantes.php?q=search" method="post">

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
                      <div class="large-12 columns">
                        <label>Distribuidora
                          <select id="cboBuscarDistro" name="cboBuscarDistro">
                            <?php print($opDistribuidoras); ?>
                          </select>
                        </label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="large-12 columns">
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
                        <input type="radio" name="opSexo" value="Masculino" id="opMasculino"><label for="opMasculino">Hombre</label>
                        <input type="radio" name="opSexo" value="Femenino" id="opFemenino"><label for="opFemenino">Mujer</label><br>
                        <input type="radio" name="opSexo" value="Cualquiera" id="opCualquiera" checked><label for="opCualquiera">Ambos</label>
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

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="myDelete" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">BORRAR PARTICIPANTE</h2>
  <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnBorrarOk" class="button alert">Borrar</a> <span class="msgbox pLeft5"></span>
</div>

<div id="myCambiar" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">CAMBIAR CONTRASEÑA</h2>

  <label>
    <input type="password" value="" id="txtContrasena" placeholder="Contraseña" required />
  </label>
  <label>
    <input type="password" value="" id="txtRepcontrasena" placeholder="Repetir Contraseña" required />
  </label>
  <label>
    <input type="password" value="" id="txtContrasenaSuper" placeholder="Contraseña del Autorizador" class="border-important" required />
  </label>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnCambiarOk" class="button round success">Cambiar</a> <span class="msgbox pLeft5"></span>
</div>

<div id="myLocation" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">ASIGNAR LOCACIÓN</h2>

  <label>Distribuidora
    <select id="cboChangeDistribuidora">
      <?php echo $opDistribuidoras; ?>
    </select>
  </label>
  <label>Sucursal
    <select id="cboChangeSucursal">
      <?php echo $opSucursales; ?>
    </select>
  </label>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnGuardarDS" class="button round success">Guardar</a> <span class="msgbox pLeft5"></span>
</div>

<div id="mySupervisor" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">ASIGNAR SUPERVISOR</h2>

  <label>Supervisor
    <select id="cboChangeSupervisor">
      <?php echo $opSuper; ?>
    </select>
  </label>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnSaveSupervisor" class="button round success">Guardar</a> <span class="msgbox pLeft5"></span>
</div>

<div id="myRol" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">ASIGNAR ROL</h2>

  <label>
    <select id="cboChangeRol">
      <?php print $opRol; ?>
    </select>
  </label>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnSaveRol" class="button round success">Guardar</a> <span class="msgbox pLeft5"></span>
</div>

<div id="myPerfil" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">ASIGNAR PERFIL</h2>

  <label>
    <select id="cboChangePerfil">
      <?php print $opPerfil; ?>
    </select>
  </label>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnSavePerfil" class="button round success">Guardar</a> <span class="msgbox pLeft5"></span>
</div>

<?php include_once('code/script.php'); ?>
<script src="js/responsive-tables.js"></script>
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
    $('#tblUsuarios tbody tr td.tableActs .delete').on('click', preDelete);
    $('#tblUsuarios tbody tr td input.activar').on('click', activarUser);
    $('#tblUsuarios tbody tr td.tableActs .location').on('click', changeLocation);
    $('#tblUsuarios tbody tr td.tableActs .supervisor').on('click', changeSupervisor);
    $('#tblUsuarios tbody tr td.tableActs .rol').on('click', changeRol);
    $('#tblUsuarios tbody tr td.tableActs .perfil').on('click', changePerfil);
    $('#tblUsuarios tbody tr td.tableActs .pass').on('click', changePass);
    $('#btnGuardarDS').on('click', saveLocation);
    $('#btnSaveSupervisor').on('click', saveSupervisor);
    $('#btnSaveRol').on('click', saveRol);
    $('#btnSavePerfil').on('click', savePerfil);
    $('#btnCambiarOk').on('click', saveChangePass);
    $('#cboChangeDistribuidora').on('change', changeDistribuidora);
    $('#btnBorrarOk').on('click', deleteUser);
    $('#lstTypeUser li a').on('click', reloadParticipantes);
    $('#lstPerfilUser li a').on('click', reloadParticipantes);
    $('#btnBuscar').on('click', searchParticipantes);

    $('a.custom-close-reveal-modal').click(function(){
      $('#myDelete').foundation('reveal', 'close');
    });
  };

  function isBlank( data ) {
    return ( $.trim(data).length == 0 );
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

  function saveSupervisor(e){
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    var supervisor = $('#cboChangeSupervisor').val();

    $.post(ajaxReq, {action:"setUserSupervisor", eAuth:auth, eRel:rel, eSuper:supervisor, rand:Math.random()},
    function(data){
        if( data.status === 'ok' ){
          // OK!
          $msgbox.html(' Grabado correctamente.');
          $('#myLocation').foundation('reveal', 'close').fadeOut( "slow" );
        } else {
          $msgbox.html(' Error: ' + data.info);
        }
    });
  }

  function saveRol(e){
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    var rol = $('#cboChangeRol').val();

    $.post(ajaxReq, {action:"setUserRol", eAuth:auth, eRel:rel, eRol:rol, rand:Math.random()},
    function(data){
        if( data.status === 'ok' ){
          // OK!
          $msgbox.html(' Grabado correctamente.');
          $('#myRol').foundation('reveal', 'close').fadeOut( "slow" );
        } else {
          $msgbox.html(' Error: ' + data.info);
        }
    });
  };

  function savePerfil(e){
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    var perfil = $('#cboChangePerfil').val();

    $.post(ajaxReq, {action:"setUserPerfil", eAuth:auth, eRel:rel, ePerfil:perfil, rand:Math.random()},
    function(data){
        if( data.status === 'ok' ){
          // OK!
          $msgbox.html(' Grabado correctamente.');
          $('#myPerfil').foundation('reveal', 'close').fadeOut( "slow" );
        } else {
          $msgbox.html(' Error: ' + data.info);
        }
    });
  };

  function changePass(e){
    e.preventDefault();
    rel = $(this).attr('rel');

    $('#myCambiar span.msgbox').html('');
    $('#myCambiar').foundation('reveal', 'open');
  };

  function saveChangePass(e){
    var $msgbox = $(this).next('span.msgbox');
    var pass1 = $('#txtContrasena').val();
    var pass2 = $('#txtRepcontrasena').val();
    var pass3 = $('#txtContrasenaSuper').val();

    if (pass1 !== pass2){
      $msgbox.html('<i class="fa fa-exclamation-triangle"></i> Error: Las Contraseñas no coinciden.');
      $('#txtContrasena').focus();
      return false;
    }

    if ( isBlank($('#txtContrasenaSuper').val()) ){
      $msgbox.html('<i class="fa fa-exclamation-triangle"></i> Error: Ingresar contraseña del Autorizador.');
      $('#txtContrasenaSuper').focus();
      return false;
    }

    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    $.post(ajaxReq, {action:"saveChangePass", eAuth:auth, eRel:rel, ePass1:pass1, ePass2:pass2, ePass3:pass3, rand:Math.random()},
    function(data){
        if( data.status === 'ok' ){
          // OK!
          $msgbox.html(' Grabado correctamente.');
          $('#myCambiar').foundation('reveal', 'close').fadeOut( "slow" );
        } else {
          $msgbox.html(' Error: ' + data.status);
        }
    });
  };

  function changeRol(e){
    e.preventDefault();
    rel = $(this).attr('rel');
    var href = $(this).attr('href');
    $('#cboChangeRol').val(href);

    $('#myRol span.msgbox').html('');
    $('#myRol').foundation('reveal', 'open');
  };

  function changePerfil(e){
    e.preventDefault();
    rel = $(this).attr('rel');
    var href = $(this).attr('href');
    $('#cboChangePerfil').val(href);

    $('#myPerfil span.msgbox').html('');
    $('#myPerfil').foundation('reveal', 'open');
  };

  function changeSupervisor(e){
    e.preventDefault();
    rel = $(this).attr('rel');
    var href = $(this).attr('href');

    $('#cboChangeSupervisor').val(href);

    $('#mySupervisor span.msgbox').html('');
    $('#mySupervisor').foundation('reveal', 'open');
  };

  function preDelete() {
    curControl = $(this);
    rel = curControl.attr('rel');
    tr  = curControl.parent().parent();
    $('#myDelete').foundation('reveal', 'open');
  };

  function deleteUser(){
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    $.post(ajaxReq, {action:"delUser", eAuth:auth, eRel:rel, rand:Math.random()},
    function(data){
        if( data.status === 'ok' ){
          // OK!
          $msgbox.html(' Eliminado correctamente.');
          $('#myLocation').foundation('reveal', 'close').fadeOut( "slow" );
          tr.remove();
        } else {
          $msgbox.html(' Error: ' + data.info);
        }
    });
  };

  function changeLocation(e){
    e.preventDefault();
    rel = $(this).attr('rel');
    var str1 = $(this).attr('href');
    href = str1.split(',');

    $('#cboChangeDistribuidora').val(href[0]);
    fillSucursalCBO(href[0]);
    $('#cboChangeSucursal').val(href[1]);

    $('#myLocation span.msgbox').html('');
    $('#myLocation').foundation('reveal', 'open');
  };

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

  function saveLocation(e){
    e.preventDefault();
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    var locD = $('#cboChangeDistribuidora').val();
    var locS = $('#cboChangeSucursal').val();

    $.post(ajaxReq, {action:'saveLocation', eAuth:auth, eRel:rel, eLocD:locD, eLocS:locS, rand:Math.random()},
    function(data){
        if ( data.status == '2' || data.status == 2 ){
          // OK!
          $('a[rel="' + rel + '"]').attr('href', locD + ',' + locS);
          $msgbox.html(' Guardado correctamente.');
          $('#myLocation').foundation('reveal', 'close').fadeOut( "slow" );
        } else {
          $msgbox.html(' Error: ' + data.info);
        }
    });
  };

  function activarUser() {
    try {
      curControl = $(this);
      rel = curControl.attr('rel');
      var check = curControl.is(':checked');

      if( check ) {
        var info  = $('#tblUsuarios tbody tr td a[rel*="' + rel + '"]');
        var info_nombre = info.text().trim();
        var info_email  = info.attr('data');
        activarUserRelly('activateUser', info_nombre, info_email);
      } else {
        activarUserRelly('deactivateUser', '', '');
      }
    }
    catch(err) {
      alert(err.message);
    }
  };

  function activarUserRelly(eAction, nombre, email){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:eAction, eAuth:auth, eRel:rel, eNom:nombre, eEmail:email, rand:Math.random()},
    function(data){
      if ( data.status == 'ok' ){
        // OK!
        $('#msgbox').html('Procesado correctamente.');
      } else {
        $('#msgbox').html('Msg: [ ' + data.status + ' ]');
      }
    });
  };
</script>

  </body>
</html>

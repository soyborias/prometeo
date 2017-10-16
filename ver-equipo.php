<?php
include_once('config.php');
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : 0;

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resEquipos = array();
  if ( $_SESSION['rol'] == ROL_ADMIN ){
    $resEquipos = getEquipos(0, $db);
  } elseif ( $_SESSION['rol'] == ROL_SUPERVISOR ){
    $resEquipos = getEquiposBySup($_SESSION['userID'], $db);
  } elseif ( $_SESSION['rol'] == ROL_JEFE ) {
    $supJefe    = getSupByJefe($_SESSION['userID'], $db);
    $resEquipos = getEquiposByJefe($supJefe, $db);
  }
  $opEquipos  = genEquiposOP($resEquipos, $pid);

  $resEquipo  = getEquipoByIdFull($pid, $db);
  //$usersTeam  = getUsuariosIdsByTeam($pid, $db);
  //$resUsers   = ( !empty($usersTeam[0]['Usuarios']) ) ? getUsuariosByTeam($usersTeam[0]['Usuarios'], $db) : array();
  $resUsers   = getUsuariosByTeamRelly($pid, $db);
  $tblUsuarios = genUsuariosTRSmart($resUsers);

  // Vars
  $title = 'Ver Equipo P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('equipo', null);
  $mnuMainMobile = crearMnuAdminMobile('equipo', null);
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

      <div class="row pTop1 text-left">
        <div class="small-12 medium-6 columns" id="frmDetalle">

          <div class="row">
            <div class="small-12 columns" id="frmData">
              <div>
                <h4 class="azulMain left bold">DETALLE DEL EQUIPO</h4> <span id="msgbox" class="pLeft5 rojo"></span>
                <hr class="mTop0" />
              </div>

              <label>
                <select id="cboEquipos">
                  <?php echo $opEquipos; ?>
                </select>
              </label>

            </div>
          </div>

          <div class="row">
            <div class="small-12 columns">
              <dl>
                <dt>Distribuidora</dt>
                <dd><?php print($resEquipo[0]['distributor_name']); ?></dd>
              </dl>
              <dl>
                <dt>Sucursal</dt>
                <dd><?php print($resEquipo[0]['subsidiary_name']); ?></dd>
              </dl>
              <dl>
                <dt>Supervirsor</dt>
                <dd><?php print($resEquipo[0]['usuario_nombre']); ?></dd>
              </dl>
            </div>
          </div>

        </div>

        <div class="small-12 medium-6 columns">
          <div class="row">
            <div class="small-12 columns" id="frmMiembros">
              <div>
                <h4 class="azulMain left bold">MIEMBROS DEL EQUIPO</h4>
                <hr class="mTop0" />
              </div>
              <table id="tblUsuarios" width="100%">
                <thead>
                  <tr>
                    <th width="20%">Nombre</th>
                    <th width="5%">Documento</th>
                    <th width="10%">Distribuidora</th>
                    <th width="10%">Sucursal</th>
                    <th width="10%">Perfil</th>
                    <th width="10%">Borrar</th>
                  </tr>
                </thead>
                <tbody>
                  <?php print($tblUsuarios); ?>
                </tbody>
              </table>
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

<div id="myDelete" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">BORRAR RELACIÓN</h2>
  <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnBorrarOk" class="button alert">Borrar</a>
</div>

<div id="myLocation" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">ASIGNAR DISTRIBUIDORA</h2>

  <label>Distribuidora
    <select id="cboChangeDistribuidora">
      <?php echo $opDistribuidoras; ?>
    </select>
  </label>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnGuardarD" class="button round success">Agregar</a> <span class="msgbox pLeft5"></span>
</div>

<div id="myCity" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">ASIGNAR SUCURSAL</h2>

  <label>Sucursal
    <select id="cboChangeSucursal">
      <?php echo $opSucursales; ?>
    </select>
  </label>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary round custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnGuardarCity" class="button round success">Guardar</a> <span class="msgbox pLeft5"></span>
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

<div id="myParticipante" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">AGREGAR MIEMBRO</h2>

  <div class="row">
    <div class="large-12 columns">
      <div class="row collapse">
        <div class="small-6 columns">
          <input id="txtNombre" name="txtNombre" type="text" placeholder="Nombre del usuario" />
        </div>
        <div class="small-4 columns">
          <input id="txtDocumento" name="txtDocumento" type="text" placeholder="Documento de identidad" />
        </div>
        <div class="small-2 columns">
          <a id="btnBuscarParticipante" href="#" class="button postfix">Go</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div id="divParticipante"></div>
    <span id="lblParticipante" class="msgbox pLeft5"></span>
  </div>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
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

<?php include_once('code/script.php'); ?>

<script>
  $(document).foundation();
  $('#cboEquipos').on('change', reloadMaster);
  $('#tblUsuarios tbody tr td.tableActs .delete').on('click', preDelete);
  $('#btnBorrarOk').on('click', eliminarFilaOk);

  var auth = 0;
  var ajaxReq = "jupiter/api.php";
  var curControl = null;
  var json = null;
  var code = -1;
  //var jsonCity  = <?php //print json_encode($resSuc); ?>;

  function reloadMaster(){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    var master = $('#cboEquipos').val();
    window.location.replace('ver-equipo.php?pid=' + master);
  };

  $('#frmDetalle .close').on('click', delTeamList);

  function verEquipoDetalles(){
    //Get Detalle TeamList
    $('#myDetalles').foundation('reveal', 'open');
    $('#lblDetalle').html('<i class="fa fa-cog fa-spin"></i> Cargando Detalles');
    $.post(ajaxReq, {action:"getTeamListByTeam", eAuth:auth, eRel:code, rand:Math.random()},
    function(data){
      json = data;

      $('#lblDetalle').html('');
      $.each(json, function(i, item) {
        if (item.list_type == 'Distribuidora') { $('#divDistribuidora').append( genAlert(item) ); }
        if (item.list_type == 'Ciudad')        { $('#divSucursal').append( genAlert(item) ); }
        if (item.list_type == 'Supervisor')    { $('#divSupervisor').append( genAlert(item) ); }
      });
      $('#frmDetalle .close').on('click', delTeamList);
    });
  }

  function genAlert(item){
    return '<div data-alert class="alert-box info round">'+ item.list_name+ '<a href="#" rel="'+ item.id+ '" class="close">&times;</a></div>'
  }

  function verEquipoMiembros(){
    //Get Detalle TeamList
    $('#myMiembros').foundation('reveal', 'open');
    $('#lblMiembros').html('<i class="fa fa-cog fa-spin"></i> Cargando Detalles');
    $.post(ajaxReq, {action:"getTeamListByTeam", eAuth:auth, eRel:code, rand:Math.random()},
    function(data){
      json = data;

      $('#lblMiembros').html('');
      $.each(json, function(i, item) {
        $('#divMiembros').append( item );
      });
      $('#divMiembros').append( '...' );
    });
  }

  function delTeamList(e){
    e.preventDefault();
    rel = $(this).attr('rel');

    $.post(ajaxReq, {action:"delTeamList", eAuth:auth, eRel:rel, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $(this).hide();
      } else {
        //
      }
    });
  };

  function preDelete(e) {
    e.preventDefault();

    curControl = $(this);
    rel = curControl.attr('rel');
    tr  = curControl.parent().parent();
    $('#myDelete').foundation('reveal', 'open');
  };

  function eliminarFilaOk(){
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    $.post(ajaxReq, {action:"delEquipoMiembro", eAuth:auth, eRel:rel, rand:Math.random()},
    function(data){
        if( data !== '-1' || data !== -1 ){
          // OK!
          $msgbox.html(' Eliminado correctamente.');
          $('#myDelete').foundation('reveal', 'close').fadeOut( "slow" );
          tr.remove();
          $('.tooltip').hide();
        } else {
          $msgbox.html(' Error: ' + data.info);
        }
    });
  };
</script>

  </body>
</html>

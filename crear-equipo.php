<?php
include_once('config.php');

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $_SESSION['equipo_filtro'] = '';
  $_SESSION['perfil_filtro'] = '';
  if ( $_SESSION['rol'] == ROL_SUPERVISOR ){
    $resEquipos  = getEquiposBySupervisor($_SESSION['userID'], $db);
    $_SESSION['equipo_filtro'] = getUsuariosAllTeamBySupervisor($resEquipos, $db);

    $perfil_sup = getSupervisorRel($_SESSION['userID'], 'perfil_sup', $db);
    $_SESSION['perfil_filtro'] = getCurriculaByPerfil($perfil_sup, $db);
  } elseif ( $_SESSION['rol'] == ROL_JEFE ) {
    $supJefe    = getSupByJefe($_SESSION['userID'], $db);
    $resEquipos = getEquiposAllByJefe($supJefe, $db);
    $_SESSION['equipo_filtro'] = getUsuariosAllTeamBySupervisor($resEquipos, $db);

    $perfil_sup = getSupervisorRel($_SESSION['userID'], 'perfil_sup', $db);
    $_SESSION['perfil_filtro'] = getCurriculaByPerfil($perfil_sup, $db);
  };

  $sup_id    = -1;
  $readonly  = '';
  $readonly2 = '';
  $loc_d = -1;
  $loc_s = -1;
  if ( $_SESSION['rol'] == ROL_ADMIN ){
    $resultados = getEquiposDS($db);
  } elseif ( $_SESSION['rol'] == ROL_SUPERVISOR ){
    $resultados = getEquiposBySup($_SESSION['userID'], $db);
    $readonly   = ' readonly="readonly" disabled="true" ';
    $readonly2  = ' readonly="readonly" disabled="true" ';
    $sup_id     = $_SESSION['userID'];
    $sup_loc    = getLocationUser($_SESSION['userID'], $db);
    $loc_d      = $sup_loc['distributor_id'];
    $loc_s      = $sup_loc['subsidiary_id'];
  } elseif ( $_SESSION['rol'] == ROL_JEFE ){
    $readonly   = ' readonly="readonly" disabled="true" ';
    $supJefe    = getSupByJefe($_SESSION['userID'], $db);
    $resultados = getEquiposByJefe($supJefe, $db);
    $sup_loc    = getLocationUser($_SESSION['userID'], $db);
    $loc_d      = $sup_loc['distributor_id'];
    $loc_s      = $sup_loc['subsidiary_id'];
  }
  $tblEquipo  = genEquiposTR($resultados);

  $resDis = getDistribuidoras($db);
  $opDistribuidoras = genDistribuidorasOP($resDis, $loc_d);

  $resSuc = getSucursalesFull($db);
  $opSucursales = genSucursalesOPFull($resSuc, $loc_s);

  if ( $_SESSION['rol'] == ROL_JEFE ){
    $resSuperJefe   = getSupByJefe($_SESSION['userID'], $db);
    $resSuper       = getUsuariosByFilter($resSuperJefe, $db);
  } else {
    $resSuper       = getUsuariosByType(ROL_SUPERVISOR, $db);
  }
  $opSuper  = genUsuariosOP($resSuper, $sup_id);

  // Vars
  $title = 'Crear Equipo P&G';
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

      <div class="row pTop1">
        <div class="large-12 columns">

          <div>
            <h4 class="azulMain left bold">PANEL DE EQUIPOS</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <div class="small-12 medium-5 columns" id="frmData">

              <div class="row">
                <div class="large-12 columns">
                  <h4 class="azulMain"><a id="btnNewItem" title="Crear Nuevo">CREAR EQUIPO</a></h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <label>Equipo
                    <input type="text" value="" id="txtEquipo" placeholder="Nombre del Equipo" />
                  </label>
                </div>

                <div class="large-12 columns">
                  <label>Descripción
                    <input type="text" value="" id="txtDescripcion" placeholder="Descripción del Equipo" />
                  </label>
                </div>

                <div class="large-12 columns">
                  <label>Distribuidora
                    <select id="cboEquipoDistribuidora" <?php print($readonly) ?>>
                      <?php echo $opDistribuidoras; ?>
                    </select>
                  </label>
                </div>

                <div class="large-12 columns">
                  <label>Sucursal
                    <select id="cboEquipoSucursal" <?php print($readonly) ?>>
                      <?php echo $opSucursales; ?>
                    </select>
                  </label>
                </div>

                <div class="large-12 columns">
                  <label>Supervisor
                    <select id="cboEquipoSupervisor" <?php print($readonly2) ?>>
                      <?php echo $opSuper; ?>
                    </select>
                  </label>
                </div>
              </div>

              <div class="row">
                <div class="large-12 columns">
                  <a class="small round button success" id="btnGrabar">GRABAR</a> <span id="msgbox"></span>
                </div>
              </div>

            </div>

            <div class="small-12 medium-7 columns">

              <div class="row">

                <div class="large-12 columns">
                  <h4 class="azulMain5">LISTA DE EQUIPOS</h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <table id="tblEquipo" width="100%">
                    <thead>
                      <tr>
                        <th width="30%">Lista</th>
                        <th width="40%">Info</th>
                        <th width="30%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php print($tblEquipo); ?>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>

          <div class="row text-left">
            <div class="small-12 medium-6 columns" id="frmDetalle">

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
  <h2 id="modalTitle">BORRAR EQUIPO</h2>
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

<div id="myDetalles" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="titleEquipo">Detalle</h2>
  <span id="lblDetalle"></span>

  <table id="tblEquipo" width="100%">
    <tbody>
      <tr>
        <th width="30%">Distribuidora</th>
        <th width="70%">
          <div id="divDistribuidora"></div>
        </th>
      </tr>
      <tr>
        <th>Sucursal</th>
        <th>
          <div id="divSucursal"></div>
        </th>
      </tr>
      <tr>
        <th>Supervisor</th>
        <th>
          <div id="divSupervisor"></div>
        </th>
      </tr>
    </tbody>
  </table>

  <a class="small round button default" id="btnEquipoMiembros">Ver Miembros</a>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="myMiembros" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="titleMiembros">Miembros de</h2>
  <span id="lblMiembros"></span>
  <div id="divMiembros"></div>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<?php include_once('code/script.php'); ?>

<script>
  $(document).foundation();
  $('a.custom-close-reveal-modal').click(function(){
    $('#myDelete').foundation('reveal', 'close');
  });

  var auth = <?php print $_SESSION['userID']; ?>;
  var ajaxReq = "jupiter/api.php";
  var curControl = null;
  var json = null;
  var code = -1;
  var jsonCity  = <?php print json_encode($resSuc); ?>;
  var city = -1;

  $('#btnGrabar').on('click', grabarDB);
  $('#btnNewItem').on('click', newItem);
  $('#tblEquipo tbody tr td.tableActs .delete').on('click', eliminarFila);
  $('#tblEquipo tbody tr td .edit').on('click', editarFila);
  $('#btnBorrarOk').on('click', eliminarFilaOk);

  $('#tblEquipo tbody tr td.tableActs .location').on('click', changeLocation);
  $('#tblEquipo tbody tr td.tableActs .city').on('click', changeCity);
  $('#tblEquipo tbody tr td.tableActs .participante').on('click', addParticipante);
  $('#tblEquipo tbody tr td.tableActs .supervisor').on('click', addSupervisor);

  $('#btnBuscarParticipante').on('click', searchParticipantes);
  $('#btnGuardarD').on('click', asignarDistribuidora);
  $('#btnGuardarCity').on('click', asignarSucursal);
  $('#btnSaveSupervisor').on('click', asignarSupervisor);

  //$('#frmDetalle').on('close.fndtn.alert', delTeamList);
  $('#frmDetalle .close').on('click', delTeamList);

  $('#cboEquipoDistribuidora').on('change', changeDistribuidora);
  fillSucursalCBO( $('#cboEquipoDistribuidora').val() );
  $('#cboEquipoSucursal').val(<?php print($loc_s); ?>).change();

  function isBlank( data ) {
    return ( $.trim(data).length == 0 );
  }

  function verifyData () {
    var rpta = true;
    $('#txtEquipo').removeClass('err');
    $('#txtDescripcion').removeClass('err');

    if ( isBlank($('#txtEquipo').val()) ){
      $('#txtEquipo').addClass('err');
      rpta = false;
    }
    if ( isBlank($('#txtDescripcion').val()) ){
      $('#txtDescripcion').addClass('err');
      rpta = false;
    }

    return rpta;
  };

  function grabarDB(){
    if (verifyData()){
      var nombre   = $("#txtEquipo").val();
      var descrip  = $("#txtDescripcion").val();
      var distributor = $("#cboEquipoDistribuidora").val();
      var sucursal    = $("#cboEquipoSucursal").val();
      var supervisor  = $("#cboEquipoSupervisor").val();

      if (code === -1){
        grabarInsert(nombre, descrip, distributor, sucursal, supervisor);
      } else {
        grabarUpdate(nombre, descrip, distributor, sucursal, supervisor);
      }
    }
  };

  function grabarInsert(nombre, descrip, distributor, sucursal, supervisor){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newEquipo", eAuth:auth, eNombre:nombre, eDescrip:descrip, eDistID:distributor, eSucID:sucursal, eSuperID:supervisor, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agregó el equipo satisfactoriamente.');
        //$('#tblEquipo > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-sucursal.php\" title=\"Ver Sucursal\"><i class=\"fa fa-university\"></i></a><a rel="'+ code + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');
        $('#tblEquipo > Tbody:first').append('<tr><td>'
          + '  <a rel=\"'+ data+ '\" class=\"edit\">'+ nombre+ '</a></td><td class=\"tableActs\">'
          //+ '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top location" href="-1" rel="'+ data+ '" title="Agregar Distribuidora">'
          //+ '  <i class="fa fa-university"></i></a>'
          //+ '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top city" href="-1" rel="'+ data+ '" title="Agregar Sucursal">'
          //+ '  <i class="fa fa-map-marker"></i></a>'
          + '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top participante" href="-1" rel="'+ data+ '" title="Agregar Miembros">'
          + '  <i class="fa fa-user-plus"></i></a>'
          //+ '  <a data-tooltip aria-haspopup="true" rel="'+ data+ '" href="-1" class="pLeft5 tip-top supervisor" title="Agregar Supervisor">'
          //+ '  <i class="fa fa-shield fa-lg"></i></a>'
          + '  <a data-tooltip aria-haspopup="true" href="ver-equipo.php?pid='+ data+ '" class="pLeft5 tip-top" title="Ver Equipo">'
          + '  <i class="fa fa-eye fa-lg"></i></a>'
          + '  <a data-tooltip aria-haspopup="true" rel="'+ data+ '" data-reveal-id="myDelete" class="pLeft5 tip-top delete rojo" title="Borrar">'
          + '  <i class="fa fa-trash-o fa-lg"></i></a>'
          + '</td></tr>');

        $('#tblEquipo tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblEquipo tbody tr td.tableActs .location').on('click', changeLocation);
        $('#tblEquipo tbody tr td.tableActs .city').on('click', changeCity);
        $('#tblEquipo tbody tr td.tableActs .participante').on('click', addParticipante);
        $('#tblEquipo tbody tr td.tableActs .supervisor').on('click', addSupervisor);
        $('#tblEquipo tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  }

   function grabarUpdate(nombre, descrip, distributor, sucursal, supervisor){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateEquipo", eAuth:auth, eRel:code, eNombre:nombre, eDescrip:descrip, eDistID:distributor, eSucID:sucursal, eSuperID:supervisor, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizó el equipo satisfactoriamente.');
        curControl.text(nombre);

        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  }

  function eliminarFila() {
    curControl = $(this);
    rel = curControl.attr('rel');
    tr  = curControl.parent().parent();
  };

  function eliminarFilaOk(){
    newItem();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Eliminando');
    $.post(ajaxReq, {action:"delEquipo", eAuth:auth, eRel:rel, rand:Math.random()},
    function(data){
        if( data !== '-1' || data !== -1 ){
          // OK!
          $('#myDelete').foundation('reveal', 'close');
          tr.remove();
          $('.tooltip').hide();
          $('#msgbox').html('Eliminado correctamente.');
        } else {
          $('#msgbox').html('Msg: [' + data + ']');
        }
    });
  };

  function editarFila(){
    curControl = $(this);
    $("#tblEquipo tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');
    code = curControl.attr('rel');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getEquipo", eAuth:auth, eRel:code, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      fillSucursalCBO( json[0].distributor );
      $('#cboEquipoDistribuidora').val(json[0].distributor).change();
      $('#txtEquipo').val(json[0].nombre);
      $('#txtDescripcion').val(json[0].descrip);
      $('#cboEquipoSupervisor').val(json[0].supervisor).change();
      $('#cboEquipoSucursal').val(json[0].subsidiary).change();
      $('#titleEquipo').html('Detalle de '+ json[0].nombre);
      $('#titleMiembros').html('Miembros de '+ json[0].nombre);
    });
  };

  function genAlert(item){
    return '<div data-alert class="alert-box info round">'+ item.list_name+ '<a href="#" rel="'+ item.id+ '" class="close">&times;</a></div>'
  }

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

  function newItem() {
    code = -1;
    $("#tblEquipo tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar () {
    $('#frmData input').removeClass('err');

    $('#divDistribuidora').html('');
    $('#divSucursal').html('');
    $('#divSupervisor').html('');

    $('#txtEquipo').val('');
    $('#txtDescripcion').val('');
    $('#txtDistribuidora').focus();

    //$('#cboEquipoDistribuidora').val(-1).change();
    $('#cboEquipoDistribuidora').val(<?php print($loc_d); ?>).change();
    //$('#cboEquipoSucursal').val(-1).change();
    $('#cboEquipoSucursal').val(<?php print($loc_s); ?>).change();
    //$('#cboEquipoSupervisor').val(-1).change();
    $('#cboEquipoSupervisor').val(<?php print($sup_id); ?>).change();
  };

  //+++
  function changeLocation(e){
    e.preventDefault();
    rel = $(this).attr('rel');
    var str1 = $(this).attr('href');
    href = str1.split(',');

    $('#cboChangeDistribuidora').val(href[0]);

    $('#myLocation span.msgbox').html('');
    $('#myLocation').foundation('reveal', 'open');
  };

  function changeCity(e){
    e.preventDefault();
    rel = $(this).attr('rel');
    var str1 = $(this).attr('href');
    href = str1.split(',');

    $('#cboChangeSucursal').val(href[0]);

    $('#myCity span.msgbox').html('');
    $('#myCity').foundation('reveal', 'open');
  }

  function addParticipante(e){
    e.preventDefault();
    rel = $(this).attr('rel');

    $('#divParticipante').html('');
    $('#lblParticipante').html('Busca al usuario que quieres agregar');
    $('#myParticipante').foundation('reveal', 'open');
  }

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

  function searchParticipantes(e){
    e.preventDefault();
    var $msgbox = $('#lblParticipante');
    $msgbox.html(' Buscando <i class="fa fa-spinner fa-pulse"></i>');

    var nom  = $('#txtNombre').val();
    var doc  = $('#txtDocumento').val();
    var html = '';

    $.post(ajaxReq, {action:'searchParticipantes', eAuth:auth, eNom:nom, eDoc:doc, rand:Math.random()},
    function(data){
      for(i=0;i<data.length;i++){
        html += prepareHtml(data[i]);
      }
      $msgbox.html('<table id="tblResultados" width="100%"><thead><tr><th width="60%">Nombre</th><th width="30%">Documento</th><th width="10%"></th></tr></thead><tbody>' + html + '</tbody></table> <span class="msgbox pLeft5"></span>');
      $('#tblResultados .addUsuario').on('click', asignarUsuario);
    });
  }

  function prepareHtml(data){
    var html = '';
      html += '<tr>';
      html += '<td class="nombre"><a rel="' + data.id + '" href="#" title="' + data.email + '">' + data.nombre + '</a></td>';
      html += '<td>' + data.doc + '</td>';
      html += '<td class="tableActs">';
      html += '  <a rel="' + data.id + '" class="pLeft5 tip-top addUsuario" title="Agregar Miembro">';
      html += '  <i class="fa fa-plus-circle fa-lg"></i></a>';
      html += '</td></tr>';

      return html;
  }

  function asignarDistribuidora(e){
    e.preventDefault();
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    var listId    = $('#cboChangeDistribuidora').val();
    var listName  = $('#cboChangeDistribuidora option:selected').text();
    var listType  = 'Distribuidora';

    if (listId == -1) { $msgbox.html(' Selecciona un valor'); return false; }
    $.post(ajaxReq, {action:"newTeamList", eAuth:auth, eRel:rel, eListName:listName, eListType:listType, eListTypeID:listId, rand:Math.random()},
    function(data){
      if( data !== 'false' || data !== false ){
        // OK!
        $msgbox.html('¡Bien!');
        $('#divDistribuidora').append('<div data-alert class="alert-box info radius">'+ listName+ '<a href="#" class="close">&times;</a></div>');
        $(document).foundation('alert', 'reflow');
        $(document).foundation('tooltip', 'reflow');
      } else {
        $msgbox.html('Msg: [' + data + ']');
      }
    });
  };

  function asignarSucursal(e){
    e.preventDefault();
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    var listId    = $('#cboChangeSucursal').val();
    var listName  = $('#cboChangeSucursal option:selected').text();
    var listType  = 'Ciudad';

    $.post(ajaxReq, {action:"newTeamList", eAuth:auth, eRel:rel, eListName:listName, eListType:listType, eListTypeID:listId, rand:Math.random()},
    function(data){
      if( data !== 'false' || data !== false ){
        // OK!
        $msgbox.html('¡Bien!');
        $('#divSucursal').append('<div data-alert class="alert-box info radius">'+ listName+ '<a href="#" class="close">&times;</a></div>');
        $(document).foundation('tooltip', 'reflow');
      } else {
        $msgbox.html('Msg: [' + data + ']');
      }
    });
  };

  function asignarSupervisor(e){
    e.preventDefault();
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    var listId    = $('#cboChangeSupervisor').val();
    var listName  = $('#cboChangeSupervisor option:selected').text();
    var listType  = 'Supervisor';

    $.post(ajaxReq, {action:"newTeamList", eAuth:auth, eRel:rel, eListName:listName, eListType:listType, eListTypeID:listId, rand:Math.random()},
    function(data){
      if( data !== 'false' || data !== false ){
        // OK!
        $msgbox.html('¡Bien!');
        $('#divSupervisor').append('<div data-alert class="alert-box info radius">'+ listName+ '<a href="#" class="close">&times;</a></div>');
        $(document).foundation('tooltip', 'reflow');
      } else {
        $msgbox.html('Msg: [' + data + ']');
      }
    });
  };

  function asignarUsuario(e){
    e.preventDefault();
    var listId    = $(this).attr('rel');
    var listType  = 'Usuario';
    var listName  = $(".nombre a[rel='"+ listId +"']").text();
    var $msgbox = $('#lblParticipante');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');

    $.post(ajaxReq, {action:"newTeamList", eAuth:auth, eRel:rel, eListName:listName, eListType:listType, eListTypeID:listId, rand:Math.random()},
    function(data){
      if( data !== 'false' || data !== false ){
        // OK!
        $msgbox.html('¡Bien!');
        //$('#divSupervisor').append('<div data-alert class="alert-box info radius">'+ listName+ '<a href="#" class="close">&times;</a></div>');
        $(document).foundation('tooltip', 'reflow');
      } else {
        $msgbox.html('Msg: [' + data + ']');
      }
    });
  };

  function grabarTeamList(listType, listId){
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    $.post(ajaxReq, {action:"newTeamList", eAuth:auth, eRel:code, eLT:listType, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $msgbox.html('¡Bien!');

        $(document).foundation('tooltip', 'reflow');
      } else {
        $msgbox.html('Msg: [' + data + ']');
      }
    });
  };

  function addSupervisor(e){
    e.preventDefault();
    rel = $(this).attr('rel');
    var href = $(this).attr('href');

    $('#cboChangeSupervisor').val(href);

    $('#mySupervisor span.msgbox').html('');
    $('#mySupervisor').foundation('reveal', 'open');
  };

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
  }

  function changeDistribuidora(){
    fillSucursalCBO( $(this).val() );
  };

  function fillSucursalCBO(distributor_id){
    var cbo =  $('#cboEquipoSucursal');
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

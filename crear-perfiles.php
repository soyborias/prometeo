<?php
include_once('config.php');

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getPerfiles(0, $db);
  $tblEquipo  = genPerfilesTR($resultados);

  // Vars
  $title = 'Crear Perfiles P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('perfiles', null);
  $mnuMainMobile = crearMnuAdminMobile('perfiles', null);
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
            <h4 class="azulMain left bold">PANEL DE PERFILES</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <div class="small-12 medium-6 columns" id="frmData">

              <div class="row">
                <div class="large-12 columns">
                  <h4 class="azulMain"><a id="btnNewItem" title="Crear Nuevo">CREAR PERFIL</a></h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <label>Perfil
                    <input type="text" value="" id="txtEquipo" placeholder="Nombre del Perfil" />
                  </label>
                </div>

                <div class="large-12 columns">
                  <label>Descripción
                    <input type="text" value="" id="txtDescripcion" placeholder="Descripción del Perfil" />
                  </label>
                </div>

                <div class="large-12 columns">
                  <div class="clearfix">
                    <a class="small round button success" id="btnGrabar">GRABAR</a> <span id="msgbox"></span>
                  </div>
                </div>
              </div>

            </div>

            <div class="small-12 medium-6 columns">

              <div class="row">

                <div class="large-12 columns">
                  <h4 class="azulMain5">LISTA DE PERFILES</h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <table id="tblEquipo" width="100%">
                    <thead>
                      <tr>
                        <th width="70%">Lista</th>
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

        </div>
      </div>

    </section>

  </main>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

  <div id="myDelete" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h2 id="modalTitle">BORRAR PERFIL</h2>
    <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
    <a id="btnBorrarOk" class="button alert">Borrar</a>
  </div>

<?php include_once('code/script.php'); ?>

<script>
  $(document).foundation();
  $('a.custom-close-reveal-modal').click(function(){
    $('#myDelete').foundation('reveal', 'close');
  });

  var auth = 0;
  var ajaxReq = "jupiter/api.php";
  var curControl = null;
  var json = null;
  var code = -1;

  $('#btnGrabar').on('click', grabarDB);
  $('#btnNewItem').on('click', newItem);
  $('#tblEquipo tbody tr td.tableActs .delete').on('click', eliminarFila);
  $('#tblEquipo tbody tr td .edit').on('click', editarFila);
  $('#btnBorrarOk').on('click', eliminarFilaOk);

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

      if (code === -1){
        grabarInsert(nombre, descrip);
      } else {
        grabarUpdate(nombre, descrip);
      }
    }
  };

  function grabarInsert(nombre, descrip){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newPerfil", eAuth:auth, eNombre:nombre, eDescrip:descrip, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agregó el equipo satisfactoriamente.');
        $('#tblEquipo > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-sucursal.php\" title=\"Ver Ciudad\"><i class=\"fa fa-university\"></i></a><a rel="'+ code + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblEquipo tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblEquipo tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  }

   function grabarUpdate(nombre, descrip){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updatePerfil", eAuth:auth, eRel:code, eNombre:nombre, eDescrip:descrip, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizó el perfil satisfactoriamente.');
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
    $.post(ajaxReq, {action:"delPerfil", eAuth:auth, eRel:rel, rand:Math.random()},
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
    $.post(ajaxReq, {action:"getPerfil", eAuth:auth, eRel:code, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtEquipo').val(json[0].nombre);
      $('#txtDescripcion').val(json[0].descrip);
    });
  };

  function newItem() {
    code = -1;
    $("#tblEquipo tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar () {
    $('#frmData input').removeClass('err');

    $('#txtEquipo').val('');
    $('#txtDescripcion').val('');
    $('#txtDistribuidora').focus();
  };

</script>

  </body>
</html>

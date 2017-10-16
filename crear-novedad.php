<?php
include_once('config.php');

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resNovedades = getNovedades(100, $db);
  $tblNovedades = genNovedadesTR($resNovedades);

  // Vars
  $title = 'Crear Novedad P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('novedad', null);
  $mnuMainMobile = crearMnuAdminMobile('novedad', null);
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
            <h4 class="azulMain left bold">PANEL DE NOVEDADES</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">

              <div class="small-12 medium-6 columns">
                <div class="row" id="frmData"><form action="">

                  <div class="large-12 columns">
                    <h4 class="azulMain"><a id="btnNewItem" title="Crear Nuevo">CREAR NOVEDAD</a></h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <label>Nombre del curso
                      <input type="text" value="" id="txtNombre" placeholder="Nombre del Curso" maxlength="64" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Objetivo
                      <input type="text" value="" id="txtObjetivo" placeholder="Objetivo del Curso" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Descripción
                      <input type="text" value="" id="txtDescrip" placeholder="Descripción del Curso" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns" id="chkTipoMaterial">
                    <label>Tipo de Material &nbsp;
                      <input name="chkTipoMaterial" value="Video" id="chkTMvideo" type="radio" checked="checked"><label for="chkTMvideo">Video</label>
                      <input name="chkTipoMaterial" value="Imagen" id="chkTMimg" type="radio"><label for="chkTMimg">Imagen</label>
                      <input name="chkTipoMaterial" value="PDF" id="chkTMpdf" type="radio"><label for="chkTMpdf">PDF</label>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>
                      <input type="text" value="http://" id="txtMaterial" placeholder="http://" maxlength="256" required/>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <div class="clearfix">
                      <a class="small round button success" id="btnGrabar">GRABAR</a> <span id="msgbox"></span>
                    </div>
                  </div>

                </form></div>
              </div>

              <div class="small-12 medium-6 columns">
                <div class="row">

                  <div class="large-12 columns">
                    <h4 class="azulMain">LISTA DE NOVEDADES</h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <table id="tblDatos" width="100%">
                      <thead>
                        <tr>
                          <th width="70%">Novedades</th>
                          <th width="20%">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php print $tblNovedades; ?>
                      </tbody>
                    </table>
                  </div>

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
  <h2 id="modalTitle">BORRAR PREGUNTA</h2>
  <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnBorrarOk" class="button alert">Borrar</a>
</div>

<?php include_once('code/script.php'); ?>
<script>
  var auth = 0;
  var ajaxReq = "jupiter/api.php";
  var curControl = null;
  var rel = null;
  var tr = null;
  var json = null;
  var codigo = -1;

  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();

    $('a.custom-close-reveal-modal').on('click', closeModal);
    $('#btnGrabar').on('click', grabarDB);
    $('#btnNewItem').on('click', newItem);
    $('#tblDatos tbody tr td.tableActs .delete').on('click', eliminarFila);
    $('#tblDatos tbody tr td .edit').on('click', editarFila);
    $('#btnBorrarOk').on('click', eliminarFilaOk);
  };

  function closeModal(){
    $('#myDelete').foundation('reveal', 'close');
  }

  function isBlank( data ) {
    return ( $.trim(data).length == 0 );
  };

  function is_url(str) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
    return regexp.test(str);
  };

  function verifyData () {
    var rpta = true;
    $("#frmData :input").each(function(){
      $(this).removeClass('err');
    });

    $("#frmData input[type=text]").each(function(){
      var input = $(this);
      if ( isBlank(input.val()) ){
        input.addClass('err');
        rpta = false;
      }
    });

    if ( !is_url($('#txtMaterial').val()) ){
      //$('#txtMaterial').addClass('err');
      //rpta = false;
    }

    return rpta;
  };

  function grabarDB(){
    if (verifyData()){
      var nombre   = $('#txtNombre').val();
      var objetivo = $('#txtObjetivo').val();
      var descrip  = $('#txtDescrip').val();
      var chkTM    = $("#chkTipoMaterial input[type='radio']:checked").val();
      var material = $('#txtMaterial').val();

      if (codigo === -1){
        grabarInsert(nombre, objetivo, descrip, chkTM, material);
      } else {
        grabarUpdate(nombre, objetivo, descrip, chkTM, material);
      }
    } else {
      $('#msgbox').html('Revisa los campos marcados.');
    }
  };

  function grabarInsert(nombre, objetivo, descrip, chkTM, material){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newNovedad", eAuth:auth, eNombre:nombre, eObjetivo:objetivo, eDescrip:descrip, eChkTM:chkTM, eMaterial:material, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agregó satisfactoriamente.');
        var j1 = '<a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="crear-cuestionario.php?pid="' + data + ' title="Juego 1"><i class="fa fa-trophy fa-lg"></i></a>';
        $('#tblDatos > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"> ' + j1 + '<a rel="'+ data + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblDatos tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblDatos tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        limpiar();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

  function grabarUpdate(nombre, objetivo, descrip, chkTM, material){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateNovedad", eAuth:auth, eRel:codigo, eNombre:nombre, eObjetivo:objetivo, eDescrip:descrip, eChkTM:chkTM, eMaterial:material, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizó satisfactoriamente.');
        curControl.text(nombre);

        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

  function eliminarFila() {
    curControl = $(this);
    rel = curControl.attr('rel');
    tr  = curControl.parent().parent();
  };

  function eliminarFilaOk(){
    newItem();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Eliminando');
    $.post(ajaxReq, {action:"delNovedad", eAuth:auth, eRel:rel, rand:Math.random()},
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
    codigo = curControl.attr('rel');
    $("#tblDatos tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getNovedad", eAuth:auth, eRel:codigo, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtNombre').val(json[0].nombre);
      $('#txtObjetivo').val(json[0].objetivo);
      $('#txtDescrip').val(json[0].descrip);
      $('input[name="chkTipoMaterial"][value="' + json[0].tMaterial + '"]').prop('checked', true);
      $('#txtMaterial').val(json[0].material);
    });
  };

  function newItem() {
    codigo = -1;
    $("#tblDatos tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    $('#msgbox').html('');
    limpiar();
  };

  function limpiar() {
    $('#frmData input').removeClass('err');

    $("#frmData input[type=text]").each(function(){
      var input = $(this);
      input.val('');
    });

    $('input[name="chkTipoMaterial"][value="Video"]').prop('checked', true);
    $('#txtNombre').focus();
  };

</script>

  </body>
</html>

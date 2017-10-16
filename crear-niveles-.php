<?php
include_once('config.php');

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getPerfiles(null, $db);
  $tblPerfil  = genPerfilesTR($resultados);

  // Vars
  $title = 'Crear Niveles P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('equipo', null);
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
            <h4 class="azulMain left bold">PANEL DE NIVELES</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <div class="small-12 medium-6 columns" id="frmData">

              <div class="row">
                <div class="large-12 columns">
                  <h4 class="azulMain">CREAR NIVEL <a id="btnNewItem" class="right" data-tooltip aria-haspopup="true" title="Agregar nueva distribuidora"><i class="fa fa-plus"></i></a></h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <label>Nivel
                    <input type="text" value="" id="txtEquipo" placeholder="Nombre del Equipo" />
                  </label>
                </div>

                <div class="large-12 columns">
                  <label>Descripción
                    <input type="text" value="" id="txtDescripcion" placeholder="Descripción del Equipo" />
                  </label>
                </div>

                <div class="large-12 columns">
                  <form id="frmPicture" action="code/upload-picture.php" class="dropzone">
                    <div id="image-holder2"><img src="static/images/niveles/nivel01.jpg" alt="Picture" /></div>
                    <input type="file" name="fileUpload" id="fileUpload" class="inputfile" />
                    <label for="fileUpload"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span id="lblFileUpload"> Seleccionar  un archivo&hellip;</span></label>
                    <p id="lblUploadMsg">La imagen debera tener como<br/>máximo 250x350px<br/>en formato jpg o png</p>
                  </form>
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
                  <h4 class="azulMain5">LISTA DE NIVELES<a id="btnNew" class="right" href="crear-distribuidora.php" data-tooltip aria-haspopup="true" title="Actualizar lista de distribuidoras"><i class="fa fa-refresh"></i></a></h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <table id="tblPerfil" width="100%">
                    <thead>
                      <tr>
                        <th width="70%">Lista</th>
                        <th width="30%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php print($tblPerfil); ?>
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
  <div id="myDelete" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h2 id="modalTitle">BORRAR NIVEL</h2>
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
  $('#tblPerfil tbody tr td.tableActs .delete').on('click', eliminarFila);
  $('#tblPerfil tbody tr td .edit').on('click', editarFila);
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
    $.post(ajaxReq, {action:"newEquipo", eAuth:auth, eNombre:nombre, eDescrip:descrip, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agrego el equipo satisfactoriamente.');
        $('#tblDetalle > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-sucursal.php\" title=\"Ver Ciudad\"><i class=\"fa fa-university\"></i></a><a rel="'+ code + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblDetalle tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblDetalle tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  }

   function grabarUpdate(nombre, descrip){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateEquipo", eAuth:auth, eRel:code, eNombre:nombre, eDescrip:descrip, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizo el equipo satisfactoriamente.');
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

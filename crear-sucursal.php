<?php
include_once('config.php');
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : 0;

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getDistribuidoras($db);
  $opDistribuidoras = genDistribuidorasOP($resultados, $pid);

  $resultados = getSucursales($pid, $db);
  $tblSucursal = genSucursalesTR($resultados);

  // Vars
  $title = 'Crear Sucursal P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('distribuidora', null);
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
            <h4 class="azulMain left bold">PANEL DE SUCURSALES</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <div class="small-12 medium-6 columns" id="frmData">

              <div class="row">
                <div class="large-12 columns">
                  <h4 class="azulMain">DISTRIBUIDORAS</h4><hr class="mTop0" />
                  <label>
                    <select id="cboDistribuidoras">
                      <?php echo $opDistribuidoras; ?>
                    </select>
                  </label>
                </div>

                <div class="large-12 columns">
                  <h4 class="azulMain"><a id="btnNewItem" title="Crear Nuevo">CREAR SUCURSAL</a></h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <label>Sucursal
                    <input type="text" value="" id="txtSucursal" placeholder="Nombre de la Sucursal" />
                  </label>
                </div>
                <div class="large-12 columns">
                  <label>Descripción
                    <input type="text" value="" id="txtDescripcion" placeholder="Descripción de la Sucursal" />
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
                  <h4 class="azulMain">SUCURSALES</h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <table id="tblSucursal" width="100%">
                    <thead>
                      <tr>
                        <th width="70%">Sucursales</th>
                        <th width="30%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php print($tblSucursal); ?>
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
    <h2 id="modalTitle">BORRAR SUCURSAL</h2>
    <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
    <a id="btnBorrarOk" class="button alert">Borrar</a>
  </div>

<?php include_once('code/script.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
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
  $('#tblSucursal tbody tr td.tableActs .delete').on('click', eliminarFila);
  $('#tblSucursal tbody tr td .edit').on('click', editarFila);
  $('#btnBorrarOk').on('click', eliminarFilaOk);
  $('#cboDistribuidoras').on('change', reloadCiudades);

  function isBlank( data ) {
    return ( $.trim(data).length == 0 );
  }

  function verifyData () {
    var rpta = true;
    $('#txtSucursal').removeClass('err');
    $('#txtDescripcion').removeClass('err');

    if ( isBlank($('#txtSucursal').val()) ){
      $('#txtSucursal').addClass('err');
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
      var nombre   = $('#txtSucursal').val();
      var descrip  = $('#txtDescripcion').val();
      var distID   = $('#cboDistribuidoras').val();

      if (code === -1){
        grabarInsert(nombre, descrip, distID);
      } else {
        grabarUpdate(nombre, descrip, distID);
      }
    }
  };

  function grabarInsert(nombre, descrip, distID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newSucursal", eAuth:auth, eNombre:nombre, eDescrip:descrip, eDistID:distID, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agrego satisfactoriamente.');
        $('#tblSucursal > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a rel="'+ code + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblSucursal tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblSucursal tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

   function grabarUpdate(nombre, descrip){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateSucursal", eAuth:auth, eRel:code, eNombre:nombre, eDescrip:descrip, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizo satisfactoriamente.');
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
    $.post(ajaxReq, {action:"delSucursal", eAuth:auth, eRel:rel, rand:Math.random()},
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
    $("#tblSucursal tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');
    code = curControl.attr('rel');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getSucursal", eAuth:auth, eRel:code, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtSucursal').val(json[0].nombre);
      $('#txtDescripcion').val(json[0].descrip);
    });
  };

  function newItem() {
    code = -1;
    $("#tblSucursal tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar () {
    $('#frmData input').removeClass('err');

    $('#txtSucursal').val('');
    $('#txtDescripcion').val('');
    $('#txtSucursal').focus();
  };

  function reloadCiudades(){
    newItem();
    var master = $('#cboDistribuidoras').val();
    $("#tblSucursal tbody").html('');

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getSucursales", eAuth:auth, eRel:master, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtSucursal').focus();
      for (i in json) {
        $('#tblSucursal > Tbody:first').append('<tr><td><a rel="' + json[i].id + '" class="edit">' + json[i].nombre + '</a></td><td class=\"tableActs\"><a rel="'+ json[i].id + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');
      }

      // reFlow
      $('#tblSucursal tbody tr td.tableActs .delete').on('click', eliminarFila);
      $('#tblSucursal tbody tr td .edit').on('click', editarFila);
      $(document).foundation('tooltip', 'reflow');
    });
  };
</script>

  </body>
</html>

<?php
include_once('config.php');
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : 0;

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getPerfiles(NULL, $db);
  $opPerfiles = genPerfilesOP($resultados, $pid);

  $resultados = getEntrenamientos($db);
  $opDetalle = genEntrenamientosOPver($resultados, '', false);

  $resultados = getNovedades(100, $db);
  $opNovedad  = genNovedadesOP($resultados, '', false);

  // Vars
  $title = 'Crear Currícula P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('curricula', null);
  $mnuMainMobile = crearMnuAdminMobile('curricula', null);
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
            <h4 class="azulMain left bold">PANEL DE CURRÍCULA</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <div class="small-12 medium-6 columns" id="frmData">

              <div class="row">
                <div class="large-12 columns">
                  <h4 class="azulMain">PERFILES</h4><hr class="mTop0" />
                  <label>
                    <select id="cboPerfiles">
                      <?php echo $opPerfiles; ?>
                    </select>
                  </label>
                </div>

                <div class="large-12 columns">
                  <h4 class="azulMain">Entrenamientos para el perfil</h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                    <select multiple="multiple" id="cboEntrenamientos" name="cboEntrenamientos[]">
                      <?php print($opDetalle) ?>
                    </select>
                </div>

                <div class="large-12 columns">
                  <h4 class="azulMain">Novedades para el perfil</h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                    <select multiple="multiple" id="cboNovedades" name="cboNovedades[]">
                      <?php print($opNovedad) ?>
                    </select>
                </div>
              </div>

              <div class="row" style="margin-top: 1em;">
                <div class="large-12 columns">
                  <div class="clearfix">
                    <a class="small round button success" id="btnGrabar">GRABAR</a> <span id="msgbox"></span>
                  </div>
                </div>
              </div>

            </div>

            <div class="small-12 medium-6 columns">

              <div class="row">

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
    <h2 id="modalTitle">BORRAR</h2>
    <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
    <a id="btnBorrarOk" class="button alert">Borrar</a>
  </div>

<?php include_once('code/script.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
<script src="/js/jquery.multi-select.js"></script>
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
  $('#btnBorrarOk').on('click', eliminarFilaOk);
  $('#cboPerfiles').on('change', reloadMaster);
  $('#cboEntrenamientos').multiSelect();
  $('#cboNovedades').multiSelect();

  function grabarDB(){
      var perfil   = $('#cboPerfiles').val();
      var entrena  = $('#cboEntrenamientos').val();
      var novedad  = $('#cboNovedades').val();

      if (code === -1){
        grabarInsert(perfil, entrena, novedad);
      } else {
        grabarUpdate(perfil, entrena, novedad);
      }
  };

  function grabarInsert(perfil, entrena, novedad){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newCurricula", eAuth:auth, ePerfil:perfil, eEntrena:entrena, eNovedad:novedad, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agregó correctamente.');
        //$('#tblDetalle > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a rel="'+ code + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        //newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

   function grabarUpdate(perfil, entrena, novedad){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateCurricula", eAuth:auth, eRel:code, ePerfil:perfil, eEntrena:entrena, eNovedad:novedad, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizó correctamente.');
        //curControl.text(nombre);

        //$(document).foundation('tooltip', 'reflow');
        //newItem();
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
    $.post(ajaxReq, {action:"delNivel", eAuth:auth, eRel:rel, rand:Math.random()},
    function(data){
        if( data !== '-1' || data !== -1 ){
          // OK!
          $('#myDelete').foundation('reveal', 'close');
          //tr.remove();
          $('.tooltip').hide();
          $('#msgbox').html('Eliminado correctamente.');
        } else {
          $('#msgbox').html('Msg: [' + data + ']');
        }
    });
  };

  function newItem() {
    code = -1;
    //$("#tblDetalle tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar () {
    $('#frmData input').removeClass('err');

    $('#cboEntrenamientos').multiSelect('deselect_all');
    $('#cboNovedades').multiSelect('deselect_all');
  };

  function reloadMaster(){
    newItem();
    var master = $('#cboPerfiles').val();
    $('#cboEntrenamientos').multiSelect('deselect_all');
    $('#cboNovedades').multiSelect('deselect_all');

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getCurricula", eAuth:auth, eRel:master, rand:Math.random()},
    function(json){
      $('#msgbox').html('');
      if( json[0] ){
        var array = json[0].entrenamientos.split(',');
        $('#cboEntrenamientos').multiSelect('select', array);
        var array2 = json[0].novedades.split(',');
        $('#cboNovedades').multiSelect('select', array2);
      } else {
        $('#cboEntrenamientos').multiSelect('deselect_all');
        $('#cboNovedades').multiSelect('deselect_all');
      }
    });
  };
</script>

  </body>
</html>

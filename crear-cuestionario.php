<?php
include_once('config.php');
$pid  = (isset($_GET['pid']))  ? $_GET['pid']  : 0;
$rel  = (isset($_GET['rel']))  ? $_GET['rel']  : 0;

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados   = getCursos($rel, $db);
  $opCursos     = genCursosOP($resultados, $pid);

  $resPreguntas = getPreguntas($pid, $db);
  $tblPreguntas = genPreguntasTR($resPreguntas);

  // Vars
  $title = 'Crear Cuestionario P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('entrenamiento', null);
  $mnuMainMobile = crearMnuAdminMobile('entrenamiento', null);
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

          <div class="row pDown1">
            <div class="small-12 columns">
              <div class="text-left azulMain">
                <ul class="breadcrumbs mDown0">
                  <li><a href="crear-entrenamiento.php">Entrenamientos</a></li>
                  <li><a href="crear-curso.php">Cursos</a></li>
                  <li><a href="crear-cuestionario.php">Cuestionario</a></li>
                  <li class="current"><a href="#">Crear</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row text-left">
            <form>

              <div class="small-12 medium-6 columns">
                <div class="row" id="frmData">

                  <div class="large-12 columns">
                    <h4 class="azulMain">Cursos</h4><hr class="mTop0" />
                    <label>
                      <select id="cboCursos">
                        <?php echo $opCursos; ?>
                      </select>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <h4 class="azulMain">CREAR PREGUNTA <a id="btnNewItem" class="right" data-tooltip aria-haspopup="true" title="Agregar Nuevo"><i class="fa fa-plus"></i></a></h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <label>PREGUNTA
                      <input id="txtPregunta" type="text" placeholder="Ingresa Pregunta" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Respuesta CORRECTA
                      <input id="txtRpta1" type="text" placeholder="Respuesta Correcta" maxlength="128" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 1
                      <input id="txtRpta2" type="text" placeholder="Distractor 1" maxlength="128" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 2
                      <input id="txtRpta3" type="text" placeholder="Distractor 2" maxlength="128" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 3
                      <input id="txtRpta4" type="text" placeholder="Distractor 3" maxlength="128" required/>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <div class="clearfix">
                      <a class="small round button success" id="btnGrabar">GRABAR PREGUNTA</a> <span id="msgbox"></span>
                    </div>
                  </div>

                </div>
              </div>

              <div class="small-12 medium-6 columns">

                <div class="row">
                  <div class="large-12 columns">
                    <h4 class="azulMain">PREGUNTAS</h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <table id="tblPreguntas" width="100%">
                      <thead>
                        <tr>
                          <th width="50%">Pregunta</th>
                          <th width="30%">Respuesta</th>
                          <th width="20%">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php print($tblPreguntas); ?>
                      </tbody>
                    </table>
                  </div>

                </div>

              </div>

            </form>
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

    $('a.custom-close-reveal-modal').click(function(){
      $('#myDelete').foundation('reveal', 'close');
    });

    $('#btnGrabar').on('click', grabarDB);
    $('#btnNewItem').on('click', newItem);
    $('#tblPreguntas tbody tr td.tableActs .delete').on('click', eliminarFila);
    $('#tblPreguntas tbody tr td .edit').on('click', editarFila);
    $('#btnBorrarOk').on('click', eliminarFilaOk);
    $('#cboCursos').on('change', reloadPreguntas);
  };

  function isBlank( data ) {
    return ( $.trim(data).length == 0 );
  };

  function verifyData () {
    var rpta = true;
    $('#txtPregunta').removeClass('err');
    $('#txtRpta1').removeClass('err');
    $('#txtRpta2').removeClass('err');
    $('#txtRpta3').removeClass('err');
    $('#txtRpta4').removeClass('err');
    $('#cboCursos').removeClass('err');

    if ( $('#cboCursos').val() == -1){
      $('#cboCursos').addClass('err');
      rpta = false;
    }

    if ( isBlank($('#txtRpta1').val()) ){
      $('#txtRpta1').addClass('err');
      rpta = false;
    }
    if ( isBlank($('#txtRpta2').val()) ){
      $('#txtRpta2').addClass('err');
      rpta = false;
    }
    if ( isBlank($('#txtRpta3').val()) ){
      $('#txtRpta3').addClass('err');
      rpta = false;
    }
    if ( isBlank($('#txtRpta4').val()) ){
      $('#txtRpta4').addClass('err');
      rpta = false;
    }

    return rpta;
  };

  function grabarDB(){
    if (verifyData()){
      var pregunta = $('#txtPregunta').val();
      var rpta1    = $('#txtRpta1').val();
      var rpta2    = $('#txtRpta2').val();
      var rpta3    = $('#txtRpta3').val();
      var rpta4    = $('#txtRpta4').val();
      var masterID = $('#cboCursos').val();

      if (codigo === -1){
        grabarInsert(pregunta, rpta1, rpta2, rpta3, rpta4, masterID);
      } else {
        grabarUpdate(pregunta, rpta1, rpta2, rpta3, rpta4, masterID);
      }
    }
  };

  function grabarInsert(pregunta, rpta1, rpta2, rpta3, rpta4, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newPregunta", eAuth:auth, ePregunta:pregunta, eRpta1:rpta1, eRpta2:rpta2, eRpta3:rpta3, eRpta4:rpta4, eMaster:masterID, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agrego satisfactoriamente.');
        $('#tblPreguntas > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + pregunta + '</a></td><td>' + rpta1 + '</td><td class=\"tableActs\"> <a rel="'+ data + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblPreguntas tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblPreguntas tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        limpiar();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

  function grabarUpdate(pregunta, rpta1, rpta2, rpta3, rpta4, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updatePregunta", eAuth:auth, eRel:codigo, ePregunta:pregunta, eRpta1:rpta1, eRpta2:rpta2, eRpta3:rpta3, eRpta4:rpta4, eMaster:masterID, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizo satisfactoriamente.');
        curControl.text(pregunta);

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
    $.post(ajaxReq, {action:"delPregunta", eAuth:auth, eRel:rel, rand:Math.random()},
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
    $("#tblPreguntas tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getPregunta", eAuth:auth, eRel:codigo, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtPregunta').val(json[0].pregunta);
      $('#txtRpta1').val(json[0].rpta1);
      $('#txtRpta2').val(json[0].rpta2);
      $('#txtRpta3').val(json[0].rpta3);
      $('#txtRpta4').val(json[0].rpta4);
    });
  };

  function newItem() {
    codigo = -1;
    $("#tblPreguntas tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar() {
    $('#frmData input').removeClass('err');

    $('#txtPregunta').val('');
    $('#txtRpta1').val('');
    $('#txtRpta2').val('');
    $('#txtRpta3').val('');
    $('#txtRpta4').val('');
    $('#txtPregunta').focus();
  };

  function reloadPreguntas(){
    newItem();
    var master = $('#cboCursos').val();
    $("#tblPreguntas tbody").html('');

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getPreguntas", eAuth:auth, eRel:master, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtPregunta').focus();
      for (i in json) {
        $('#tblPreguntas > Tbody:first').append('<tr><td><a rel="' + json[i].id + '" class="edit">' + json[i].pregunta + '</a></td><td>' + json[i].rpta1 + '</td><td class=\"tableActs\"> <a rel="'+ json[i].id + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');
      }

      // reFlow
      $('#tblPreguntas tbody tr td.tableActs .delete').on('click', eliminarFila);
      $('#tblPreguntas tbody tr td .edit').on('click', editarFila);
      $(document).foundation('tooltip', 'reflow');
    });
  };
</script>

  </body>
</html>

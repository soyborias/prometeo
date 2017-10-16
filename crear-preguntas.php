<?php
include_once('config.php');
$pid   = (isset($_GET['pid']))  ? $_GET['pid']  : 0;
$rel   = (isset($_GET['rel']))  ? $_GET['rel']  : 0;
$table = (isset($_GET['m']))    ? $_GET['m']    : '';
$master_table = base64_decode($table);

if ( (isset($_SESSION['username'])) && ($_SESSION['rol']) == ROL_ADMIN ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados   = getNovedades($rel, $db);
  $opMaster     = genNovedadesOP($resultados, $pid);

  $resPreguntas = getQuestions($master_table, $pid, $db);
  $tblPreguntas = genQuestionsTR($resPreguntas);

  // Vars
  $title = 'Crear Cuestionario P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('novedad', null);
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
            <h4 class="azulMain left bold">PANEL DE NOVEDADES > Crear Cuestionario</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">
            <form>

              <div class="small-12 medium-6 columns">
                <div class="row" id="frmData">

                  <div class="large-12 columns">
                    <h4 class="azulMain">Novedades</h4><hr class="mTop0" />
                    <label>
                      <select id="cboMaster">
                        <?php echo $opMaster; ?>
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
<div id="myDelete" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">BORRAR PREGUNTA</h2>
  <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
  <a id="btnBorrarOk" class="button alert">Borrar</a> <span class="msgbox pLeft5"></span>
</div>

<?php include_once('code/script.php'); ?>
<script>
  $(document).on('ready', iniciar);
  var auth = 0;
  var ajaxReq = "jupiter/api.php";
  var curControl = null;
  var rel = null;
  var tr  = null;
  var json = null;
  var codigo = -1;
  var master = "<?php print($master_table); ?>";

  function iniciar(){
    $(document).foundation();

    $('#btnGrabar').on('click', grabarDB);
    $('#btnNewItem').on('click', newItem);
    $('#tblPreguntas tbody tr td.tableActs .delete').on('click', eliminarFila);
    $('#tblPreguntas tbody tr td .edit').on('click', editarFila);
    $('#btnBorrarOk').on('click', eliminarFilaOk);
    $('#cboMaster').on('change', reloadPreguntas);

    $('a.custom-close-reveal-modal').click(function(){
      $('#myDelete').foundation('reveal', 'close');
    });
  };

  function isBlank( data ) {
    return ( $.trim(data).length == 0 );
  };

  function verifyData () {
    var rpta = true;
    $("#frmData :input").each(function(){
      $(this).removeClass('err');
    });

    if ( $('#cboMaster').val() == -1){
      $('#cboMaster').addClass('err');
      rpta = false;
    }

    $("#frmData input[type=text]").each(function(){
      var input = $(this);
      if ( isBlank(input.val()) ){
        input.addClass('err');
        rpta = false;
      }
    });

    return rpta;
  };

  function grabarDB(){
    if (verifyData()){
      var question  = $('#txtPregunta').val();
      var answer_1  = $('#txtRpta1').val();
      var answer_2  = $('#txtRpta2').val();
      var answer_3  = $('#txtRpta3').val();
      var answer_4  = $('#txtRpta4').val();
      var masterID  = $('#cboMaster').val();

      if (codigo === -1){
        grabarInsert(question, answer_1, answer_2, answer_3, answer_4, masterID);
      } else {
        grabarUpdate(question, answer_1, answer_2, answer_3, answer_4, masterID);
      }
    }
  };

  function grabarInsert(question, answer_1, answer_2, answer_3, answer_4, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newQuestion", eAuth:auth, eQuestion:question, eAnswer_1:answer_1, eAnswer_2:answer_2, eAnswer_3:answer_3, eAnswer_4:answer_4, eMaster:master, eMasterId:masterID, rand:Math.random()},
    function(data){
      if( data.status === 'ok' ){
        // OK!
        $('#msgbox').html('¡Bien! Se agrego satisfactoriamente.');
        $('#tblPreguntas > Tbody:first').append('<tr><td><a rel="' + data.info + '" class="edit">' + question + '</a></td><td>' + answer_1 + '</td><td class=\"tableActs\"> <a rel="'+ data.info + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblPreguntas tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblPreguntas tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        limpiar();
      } else {
        $('#msgbox').html('Msg: [' + data.info + ']');
      }
    });
  };

  function grabarUpdate(question, answer_1, answer_2, answer_3, answer_4, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateQuestion", eAuth:auth, eRel:codigo, eQuestion:question, eAnswer_1:answer_1, eAnswer_2:answer_2, eAnswer_3:answer_3, eAnswer_4:answer_4, eMaster:master, eMasterId:masterID, rand:Math.random()},
    function(data){
      if( data.status === 'ok' ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizo satisfactoriamente.');
        curControl.text(question);

        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data.info + ']');
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
    var $msgbox = $(this).next('span.msgbox');
    $msgbox.html(' Procesando <i class="fa fa-spinner fa-pulse"></i>');
    $.post(ajaxReq, {action:"delQuestion", eAuth:auth, eRel:rel, rand:Math.random()},
    function(data){
        if( data.status == 'ok' ){
          // OK!
          $('#myDelete').foundation('reveal', 'close');
          tr.remove();
          $('.tooltip').hide();
          $('#msgbox').html('Eliminado correctamente.');
        } else {
          $('#msgbox').html('Msg: [' + data.info + ']');
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
    $.post(ajaxReq, {action:"getQuestion", eAuth:auth, eRel:codigo, rand:Math.random()},
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

    $("#frmData input[type=text]").each(function(){
      $(this).val('');
    });

    $('#txtPregunta').focus();
  };

  function reloadPreguntas(){
    newItem();
    var master = $('#cboMaster').val();
    $("#tblPreguntas tbody").html('');

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getQuestions", eAuth:auth, eRel:master, rand:Math.random()},
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

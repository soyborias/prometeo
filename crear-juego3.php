<?php
include_once('config.php');
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$rel = (isset($_GET['rel'])) ? $_GET['rel'] : 0;

if ( (isset($_SESSION['username'])) && ($_SESSION['rol']) == ROL_ADMIN ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getTemas($rel, $db);
  $opTemas    = genTemasOP($resultados, $pid);

  $jTipo     = 3;
  $resJuegos = getJuegos($pid, $jTipo, $db);
  $tblJuego  = genJuegosTR($resJuegos);

  // Vars
  $title = 'Crear Juegos P&G';
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
                  <li><a href="crear-tema.php?id=<?php print($pid); ?>">Temas</a></li>
                  <li><a href="">Juegos</a></li>
                  <li class="current"><a href="#">Crear</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row text-left">

              <div class="small-12 medium-6 columns">
                <div class="row" id="frmData"><form action="">

                <div class="large-12 columns">
                  <h4 class="azulMain">TEMAS</h4><hr class="mTop0" />
                  <label>
                    <select id="cboTemas">
                      <?php echo $opTemas; ?>
                    </select>
                  </label>
                </div>

                <div class="large-12 columns">
                  <h4 class="azulMain">CREAR PREGUNTA PARA JUEGO<a id="btnNewItem" class="right" data-tooltip aria-haspopup="true" title="Agregar Nuevo"><i class="fa fa-plus"></i></a></h4><hr class="mTop0" />
                </div>

                  <div class="large-12 columns">
                    <label>PREGUNTA
                      <input type="text" id="txtPregunta" placeholder="Ingresa Pregunta" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Respuesta CORRECTA
                      <input type="text" id="txtCorrecta" placeholder="Respuesta Correcta" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 1
                      <input type="text" id="txtDistractor1" placeholder="Distractor 1" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 2
                      <input type="text" id="txtDistractor2" placeholder="Distractor 2" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 3
                      <input type="text" id="txtDistractor3" placeholder="Distractor 3" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Pista
                      <input type="text" id="txtPista" placeholder="Pista" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Feedback
                      <input type="text" id="txtFeedback" placeholder="Feedback" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Ganancia del cliente
                      <input type="text" id="txtGanancia1" placeholder="Ganancia" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Perdida del cliente
                      <input type="text" id="txtPerdida1" placeholder="Perdida" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Ganancia del trabajador
                      <input type="text" id="txtGanancia2" placeholder="Ganancia" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Perdida del trabajador
                      <input type="text" id="txtPerdida2" placeholder="Perdida" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <div class="clearfix">
                      <a class="small round button success" id="btnGrabar">GRABAR PREGUNTA</a> <span id="msgbox"></span>
                    </div>
                  </div>

                </form></div>
              </div>

              <div class="small-12 medium-6 columns">
                <div class="row">

                  <div class="large-12 columns">
                    <h4 class="azulMain">PREGUNTAS DEL JUEGO</h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <table id="tblJuego" width="100%">
                      <thead>
                        <tr>
                          <th width="50%">Pregunta</th>
                          <th width="30%">Respuesta</th>
                          <th width="20%">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php print $tblJuego; ?>
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
  var tr  = null;
  var json = null;
  var codigo = -1;
  var jTipo  = 3;

  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();

    $('a.custom-close-reveal-modal').click(function(){
      $('#myDelete').foundation('reveal', 'close');
    });

    $('#btnGrabar').on('click', grabarDB);
    $('#btnNewItem').on('click', newItem);
    $('#tblJuego tbody tr td.tableActs .delete').on('click', eliminarFila);
    $('#tblJuego tbody tr td .edit').on('click', editarFila);
    $('#btnBorrarOk').on('click', eliminarFilaOk);
    $('#cboTemas').on('change', reloadJuegos);
  };

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

    if ( $('#cboTemas').val() == -1){
      $('#cboTemas').addClass('err');
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
      var pregunta    = $('#txtPregunta').val();
      var correcta    = $('#txtCorrecta').val();
      var distractor1 = $('#txtDistractor1').val();
      var distractor2 = $('#txtDistractor2').val();
      var distractor3 = $('#txtDistractor3').val();
      var pista       = $('#txtPista').val();
      var feedback    = $('#txtFeedback').val();
      var ganancia1   = $('#txtGanancia1').val();
      var perdida1    = $('#txtPerdida1').val();
      var ganancia2   = $('#txtGanancia2').val();
      var perdida2    = $('#txtPerdida2').val();
      var masterID    = $('#cboTemas').val();

      if (codigo === -1){
        grabarInsert(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, ganancia1, perdida1, ganancia2, perdida2, masterID);
      } else {
        grabarUpdate(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, ganancia1, perdida1, ganancia2, perdida2, masterID);
      }
    } else {
      $('#msgbox').html('Revisa los campos marcados.');
    }
  };

  function grabarInsert(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, ganancia1, perdida1, ganancia2, perdida2, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newJuego", eAuth:auth, eJuegoTipo:jTipo, ePregunta:pregunta, eCorrecta:correcta, eDistractor1:distractor1, eDistractor2:distractor2, eDistractor3:distractor3, ePista:pista, eFeedback:feedback, eGanancia1:ganancia1, ePerdida1:perdida1, eGanancia2:ganancia2, ePerdida2:perdida2, eMasterID:masterID, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agrego satisfactoriamente.');
        $('#tblJuego > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + pregunta + '</a></td><td>' + correcta + '</td><td class=\"tableActs\"> <a rel="'+ data + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblJuego tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblJuego tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        limpiar();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

  function grabarUpdate(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, ganancia1, perdida1, ganancia2, perdida2, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateJuego", eAuth:auth, eRel:codigo, eJuegoTipo:jTipo, ePregunta:pregunta, eCorrecta:correcta, eDistractor1:distractor1, eDistractor2:distractor2, eDistractor3:distractor3, ePista:pista, eFeedback:feedback, eGanancia1:ganancia1, ePerdida1:perdida1, eGanancia2:ganancia2, ePerdida2:perdida2, eMasterID:masterID, rand:Math.random()},
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
    $.post(ajaxReq, {action:"delJuego", eAuth:auth, eRel:rel, rand:Math.random()},
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
    $("#tblJuego tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getJuego", eAuth:auth, eRel:codigo, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtPregunta').val(json[0].pregunta);
      $('#txtCorrecta').val(json[0].respuesta);
      $('#txtDistractor1').val(json[0].distractor1);
      $('#txtDistractor2').val(json[0].distractor2);
      $('#txtDistractor3').val(json[0].distractor3);
      $('#txtPista').val(json[0].pista);
      $('#txtFeedback').val(json[0].feedback);
      $('#txtGanancia1').val(json[0].ganancia1);
      $('#txtPerdida1').val(json[0].perdida1);
      $('#txtGanancia2').val(json[0].ganancia2);
      $('#txtPerdida2').val(json[0].perdida2);
    });
  };

  function newItem() {
    codigo = -1;
    $("#tblJuego tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar() {
    $('#frmData input').removeClass('err');

    $("#frmData input[type=text]").each(function(){
      var input = $(this);
      input.val('');
    });

    $('#txtPregunta').focus();
  };

  function reloadJuegos(){
    newItem();
    var master = $('#cboTemas').val();
    $("#tblJuego tbody").html('');

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getJuegos", eAuth:auth, eRel:master, eJuegoTipo:jTipo, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtNombre').focus();
      for (i in json) {
        $('#tblJuego > Tbody:first').append('<tr><td><a rel="' + json[i].id + '" class="edit">' + json[i].pregunta + '</a></td><td>' + json[i].respuesta + '</td><td class=\"tableActs\"> <a rel="'+ json[i].id + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');
      }

      // reFlow
      $('#tblJuego tbody tr td.tableActs .delete').on('click', eliminarFila);
      $('#tblJuego tbody tr td .edit').on('click', editarFila);
      $(document).foundation('tooltip', 'reflow');
    });
  };
</script>

  </body>
</html>

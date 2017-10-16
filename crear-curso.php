<?php
include_once('config.php');
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : 0;

if ( (isset($_SESSION['username'])) && ($_SESSION['rol']) == ROL_ADMIN ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getEntrenamientos($db);
  $opEntrenamientos = genEntrenamientosOP($resultados, $pid);

  $resCursos = getCursos($pid, $db);
  $tblCursos = genCursosTR($resCursos);

  // Vars
  $title = 'Crear Cursos P&G';
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
                  <li class="current">Crear</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row text-left">

              <div class="small-12 medium-6 columns">
                <div class="row" id="frmData">

                  <div class="large-12 columns">
                    <h4 class="azulMain">ENTRENAMIENTO</h4><hr class="mTop0" />
                    <label>
                      <select id="cboEntrenamiento">
                        <?php echo $opEntrenamientos; ?>
                      </select>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <h4 class="azulMain">CREAR CURSO <a id="btnNewItem" class="right" data-tooltip aria-haspopup="true" title="Agregar nuevo curso"><i class="fa fa-plus"></i></a></h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <label class="azulMain">Curso
                      <input type="text" value="" id="txtCurso" placeholder="Nombre del curso" maxlength="64" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Objetivo
                      <input type="text" value="" id="txtObjetivo" placeholder="Objetivo del curso" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Descripción
                      <input type="text" value="" id="txtDescripcion" placeholder="Descripción del curso" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>URL Video
                      <input type="text" value="http://" id="txtVideo" placeholder="http://" maxlength="256" required/>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <div class="clearfix">
                      <a class="small round button success" id="btnGrabar">GRABAR CURSO</a> <span id="msgbox"></span>
                    </div>
                  </div>

                </div>
              </div>

              <div class="small-12 medium-6 columns">
                <div class="row">

                  <div class="large-12 columns">
                    <h4 class="azulMain">CURSOS</h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <table id="tblCursos" width="100%">
                      <thead>
                        <tr>
                          <th width="70%">Cursos</th>
                          <th width="30%">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php print($tblCursos); ?>
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
  <h2 id="modalTitle">BORRAR CURSO</h2>
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
    $('#tblCursos tbody tr td.tableActs .delete').on('click', eliminarFila);
    $('#tblCursos tbody tr td .edit').on('click', editarFila);
    $('#btnBorrarOk').on('click', eliminarFilaOk);
    $('#cboEntrenamiento').on('change', reloadCursos);
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
    $('#txtCurso').removeClass('err');
    $('#txtObjetivo').removeClass('err');
    $('#txtDescripcion').removeClass('err');
    $('#txtVideo').removeClass('err');
    $('#cboEntrenamiento').removeClass('err');

    if ( $('#cboEntrenamiento').val() == -1){
      $('#cboEntrenamiento').addClass('err');
      rpta = false;
    }

    if ( isBlank($('#txtCurso').val()) ){
      $('#txtCurso').addClass('err');
      rpta = false;
    }
    if ( isBlank($('#txtObjetivo').val()) ){
      $('#txtObjetivo').addClass('err');
      rpta = false;
    }
    if ( isBlank($('#txtDescripcion').val()) ){
      $('#txtDescripcion').addClass('err');
      rpta = false;
    }
    if ( isBlank($('#txtVideo').val()) ){
      $('#txtVideo').addClass('err');
      rpta = false;
    }
    if ( !is_url($('#txtVideo').val()) ){
      //$('#txtVideo').addClass('err');
      //rpta = false;
    }

    return rpta;
  };

  function grabarDB(){
    if (verifyData()){
      var nombre   = $('#txtCurso').val();
      var objetivo = $('#txtObjetivo').val();
      var descrip  = $('#txtDescripcion').val();
      var video    = $('#txtVideo').val();
      var masterID = $('#cboEntrenamiento').val();

      if (codigo === -1){
        grabarInsert(nombre, objetivo, descrip, video, masterID);
      } else {
        grabarUpdate(nombre, objetivo, descrip, video, masterID);
      }
    }
  };

  function grabarInsert(nombre, objetivo, descrip, video, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newCurso", eAuth:auth, eNombre:nombre, eObjetivo:objetivo, eDescrip:descrip, eVideo:video, eMaster:masterID, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agrego el curso satisfactoriamente.');
        $('#tblCursos > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-cuestionario.php?pid=' + data + '\" title=\"Ver Cuestionario\"><i class=\"fa fa-list fa-lg\"></i></a> <a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-tema.php?pid=' + data + '\" title=\"Ver Temas\"><i class=\"fa fa-book fa-lg\"></i></a> <a rel="'+ data + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblCursos tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblCursos tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        limpiar();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

  function grabarUpdate(nombre, objetivo, descrip, video, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateCurso", eAuth:auth, eRel:codigo, eNombre:nombre, eObjetivo:objetivo, eDescrip:descrip, eVideo:video, eMaster:masterID, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizo el curso satisfactoriamente.');
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
    $.post(ajaxReq, {action:"delCurso", eAuth:auth, eRel:rel, rand:Math.random()},
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
    $("#tblCursos tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getCurso", eAuth:auth, eRel:codigo, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtCurso').val(json[0].nombre);
      $('#txtObjetivo').val(json[0].objetivo);
      $('#txtDescripcion').val(json[0].descrip);
      $('#txtVideo').val(json[0].video);
    });
  };

  function newItem() {
    codigo = -1;
    $("#tblCursos tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar() {
    $('#frmData input').removeClass('err');

    $('#txtCurso').val('');
    $('#txtDescripcion').val('');
    $('#txtObjetivo').val('');
    $('#txtVideo').val('');
    $('#txtCurso').focus();
  };

  function reloadCursos(){
    newItem();
    var master = $('#cboEntrenamiento').val();
    $("#tblCursos tbody").html('');

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getCursos", eAuth:auth, eRel:master, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtCurso').focus();
      for (i in json) {
        $('#tblCursos > Tbody:first').append('<tr><td><a rel="' + json[i].id + '" class="edit">' + json[i].nombre + '</a></td><td class=\"tableActs\"><a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-cuestionario.php?pid=' + json[i].id + '&rel=' + json[i].master_id + '\" title=\"Ver Cuestionario\"><i class=\"fa fa-list fa-lg\"></i></a> <a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-tema.php?pid=' + json[i].id + '\" title=\"Ver Temas\"><i class=\"fa fa-book fa-lg\"></i></a> <a rel="'+ json[i].id + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');
      }

      // reFlow
      $('#tblCursos tbody tr td.tableActs .delete').on('click', eliminarFila);
      $('#tblCursos tbody tr td .edit').on('click', editarFila);
      $(document).foundation('tooltip', 'reflow');
    });
  };
</script>

  </body>
</html>

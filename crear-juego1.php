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

  $jTipo     = 1;
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
                <div class="row" id="frmData">

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
                    <div class="clearfix">
                      <a class="small round button success" id="btnGrabar">GRABAR PREGUNTA</a> <span id="msgbox"></span>
                    </div>
                  </div>
                  <div class="large-12 columns">
                    <div class="row">
                      <div class="large-6 columns"><h6>Portada</h6>
                        <form id="frmPicture1" action="code/upload-picture.php" class="dropzone">
                          <div id="picture1"><img src="static/images/pantallas/pregunta02.jpg" alt="Picture" /></div>
                          <input type="file" name="fileUpload" id="fileUpload1" class="inputfile" />
                          <label for="fileUpload1"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                        </form>
                      </div>
                      <div class="large-6 columns"><h6>Fondo</h6>
                        <form id="frmPicture2" action="code/upload-picture.php" class="dropzone">
                          <div id="picture2"><img src="static/images/pantallas/pregunta02.jpg" alt="Picture" /></div>
                          <input type="file" name="fileUpload" id="fileUpload2" class="inputfile" />
                          <label for="fileUpload2"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                        </form>
                      </div>
                    </div>
                  </div>

                </div>
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
  var tr = null;
  var json = null;
  var codigo = -1;
  var jTipo = 1;

  function basename(str, sep) {
    return str.substr(str.lastIndexOf(sep) + 1);
  };

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

    $('#fileUpload1').on('change', showFile1);
    $("#fileUpload2").on('change', showFile2);
    $('#frmPicture1').on('submit', uploadData);
    $('#frmPicture2').on('submit', uploadData);
  };

  $( '.inputfile' ).each( function(){
    var $input   = $( this ),
      $label   = $input.next( 'label' ),
      labelVal = $label.html();

    $input.on( 'change', function( e ){
      var fileName = '';

      if( this.files && this.files.length > 1 )
        fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
      else if( e.target.value )
        fileName = e.target.value.split( '\\' ).pop();

      if( fileName )
        $label.find( 'span' ).html( fileName );
      else
        $label.html( labelVal );
    });

    // Firefox bug fix
    $input
    .on( 'focus', function(){ $input.addClass( 'has-focus' ); })
    .on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
  });

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
      var masterID    = $('#cboTemas').val();

      var pic1     = $("#frmPicture1 label span.msgbox").html();
      var pic2     = $("#frmPicture2 label span.msgbox").html();

      if (codigo === -1){
        grabarInsert(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, pic1, pic2, masterID);
      } else {
        grabarUpdate(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, pic1, pic2, masterID);
      }
    } else {
      $('#msgbox').html('Revisa los campos marcados.');
    }
  };

  function grabarInsert(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, pic1, pic2, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newJuego", eAuth:auth, eJuegoTipo:jTipo, ePregunta:pregunta, eCorrecta:correcta, eDistractor1:distractor1, eDistractor2:distractor2, eDistractor3:distractor3, ePista:pista, eFeedback:feedback, ePic1:pic1, ePic2:pic2, eMasterID:masterID, rand:Math.random()},
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

  function grabarUpdate(pregunta, jTipo, correcta, distractor1, distractor2, distractor3, pista, feedback, pic1, pic2, masterID){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateJuego", eAuth:auth, eRel:codigo, eJuegoTipo:jTipo, ePregunta:pregunta, eCorrecta:correcta, eDistractor1:distractor1, eDistractor2:distractor2, eDistractor3:distractor3, ePista:pista, eFeedback:feedback, ePic1:pic1, ePic2:pic2, eMasterID:masterID, rand:Math.random()},
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
      $('#picture1').html('<img src="'+ json[0].portada+ '">');
      $('#picture2').html('<img src="'+ json[0].fondo+ '">');
      $("#frmPicture1 label span.msgbox").html(basename(json[0].portada, '/'));
      $("#frmPicture2 label span.msgbox").html(basename(json[0].fondo, '/'));
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

    $("#frmPicture1 label span.msgbox").html('');
    $("#frmPicture2 label span.msgbox").html('');

    $('#picture1').html('');
    $('#picture2').html('');

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

  function showFile1(e){ showFile($(this)[0], $('#picture1'), '#frmPicture1') };
  function showFile2(e){ showFile($(this)[0], $('#picture2'), '#frmPicture2') };

  function showFile($this, image_holder, form){
    var countFiles = $this.files.length;
    var imgPath = $this.value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
    image_holder.empty();
    var fileName = "";

    if (extn == "png" || extn == "jpg" || extn == "jpeg") {
      if (typeof (FileReader) != "undefined") {

        //loop for each file selected for uploaded.
        for (var i = 0; i < countFiles; i++) {
          var reader = new FileReader();
          reader.onload = function (e) {
            fileName = e.target.result.split( '\\' ).pop();
            $("<img />", {
              "src": e.target.result,
              "class": "thumb-image"
            }).appendTo(image_holder);
          }
          image_holder.show();
          reader.readAsDataURL($this.files[i]);
        }
        //$('#frmPicture1').submit();
        $( form ).submit();

      } else {
        alert("El formato de imagen no es soportado. Intente con JPG o PNG");
      }
    } else {
      alert("Solo valido para imagenes JPG | PNG");
    }
  };

  function uploadData(e){
    e.preventDefault();
    var $msgbox  = $(this).find('span.msgbox');
    var $details = $(this).find('span.icon');
    $details.html('<i class="fa fa-spinner fa-pulse"></i> ');
    $msgbox.html('Cargando');

    $.ajax({
      url: "code/upload-picture.php?f=juegos",
      type: "POST",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData:false,
      success: function(data){
        data = jQuery.parseJSON( data );
        if (data.rpta == 'ok'){
          $details.html('<i class="fa fa-check-circle-o"></i> ');
          $msgbox.html(data.msg);
        } else {
          $details.html('<i class="fa fa-exclamation-triangle"></i> ');
          $msgbox.html(' No permitido');
          //$('#lblUploadMsg').html('Error: ' + data.msg);
        }
      }
    });
  };
</script>

  </body>
</html>

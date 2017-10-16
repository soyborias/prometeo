<?php
include_once('config.php');
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : 0;

if ( (isset($_SESSION['username'])) && ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_SUPERVISOR ) ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getPerfiles(NULL, $db);
  $opPerfiles = genPerfilesOP($resultados, $pid);

  $resultados = getNiveles($pid, $db);
  $tblDetalle = genNivelesTR($resultados);

  // Vars
  $title = 'Crear Niveles P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuAdmin('perfiles', null);
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
                  <h4 class="azulMain">PERFILES</h4><hr class="mTop0" />
                  <label>
                    <select id="cboPerfiles">
                      <?php echo $opPerfiles; ?>
                    </select>
                  </label>
                </div>

                <div class="large-12 columns">
                  <h4 class="azulMain"><a id="btnNewItem" title="Crear Nuevo">CREAR NIVEL</a></h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <label>Nivel
                    <input type="text" value="" id="txtNivel" placeholder="Nombre del Nivel" />
                  </label>
                </div>
                <div class="large-12 columns">
                  <label>Descripción
                    <input type="text" value="" id="txtDescripcion" placeholder="Descripción del Nivel" />
                  </label>
                </div>
                <div class="large-12 columns">
                  <div class="row row text-center">
                    <div class="large-4 columns"><h6>Normal</h6>
                      <form id="frmPicture1" action="code/upload-picture.php" class="dropzone">
                        <div id="picture1"><img src="static/images/niveles/nivel01.jpg" alt="Picture" /></div>
                        <input type="file" name="fileUpload" id="fileUpload1" class="inputfile" />
                        <label for="fileUpload1"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                      </form>
                    </div>
                    <div class="large-4 columns"><h6>Gana</h6>
                      <form id="frmPicture2" action="code/upload-picture.php" class="dropzone">
                        <div id="picture2"><img src="static/images/niveles/v-ganador.jpg" alt="Picture" /></div>
                        <input type="file" name="fileUpload" id="fileUpload2" class="inputfile" />
                        <label for="fileUpload2"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                      </form>
                    </div>
                    <div class="large-4 columns"></div>
                  </div>
                  <div class="row text-center">
                    <div class="large-4 columns"><h6>Feedback</h6>
                      <form id="frmPicture3" action="code/upload-picture.php" class="dropzone">
                        <div id="picture3"><img src="static/images/niveles/v-feedback.jpg" alt="Picture" /></div>
                        <input type="file" name="fileUpload" id="fileUpload3" class="inputfile" />
                        <label for="fileUpload3"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                      </form>
                    </div>
                    <div class="large-4 columns"><h6>Pierde</h6>
                      <form id="frmPicture4" action="code/upload-picture.php" class="dropzone">
                        <div id="picture4"><img src="static/images/niveles/v-duda.jpg" alt="Picture" /></div>
                        <input type="file" name="fileUpload" id="fileUpload4" class="inputfile" />
                        <label for="fileUpload4"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                      </form>
                    </div>
                    <div class="large-4 columns"><h6>Celebra</h6>
                      <form id="frmPicture5" action="code/upload-picture.php" class="dropzone">
                        <div id="picture5"><img src="static/images/niveles/v-ganador.jpg" alt="Picture" /></div>
                        <input type="file" name="fileUpload" id="fileUpload5" class="inputfile" />
                        <label for="fileUpload5"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                      </form>
                    </div>
                  </div>
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
                  <h4 class="azulMain">NIVELES</h4>
                  <hr class="mTop0" />
                </div>

                <div class="large-12 columns">
                  <table id="tblDetalle" width="100%">
                    <thead>
                      <tr>
                        <th width="70%">Niveles</th>
                        <th width="30%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php print($tblDetalle); ?>
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
    <h2 id="modalTitle">BORRAR</h2>
    <p id="modalDescrip" class="rojo">¿Estas seguro de borrarlo?</p>

    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    <a class="button secondary custom-close-reveal-modal">Cancelar</a> &nbsp;
    <a id="btnBorrarOk" class="button alert">Borrar</a>
  </div>

<?php include_once('code/script.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
<script>
  var auth = 0;
  var ajaxReq = "jupiter/api.php";
  var curControl = null;
  var json = null;
  var code = -1;

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
    $('#tblDetalle tbody tr td.tableActs .delete').on('click', eliminarFila);
    $('#tblDetalle tbody tr td .edit').on('click', editarFila);
    $('#btnBorrarOk').on('click', eliminarFilaOk);
    $('#cboPerfiles').on('change', reloadMaster);

    $('#fileUpload1').on('change', showFile1);
    $("#fileUpload2").on('change', showFile2);
    $("#fileUpload3").on('change', showFile3);
    $("#fileUpload4").on('change', showFile4);
    $("#fileUpload5").on('change', showFile5);
    $('#frmPicture1').on('submit', uploadData);
    $('#frmPicture2').on('submit', uploadData);
    $('#frmPicture3').on('submit', uploadData);
    $('#frmPicture4').on('submit', uploadData);
    $('#frmPicture5').on('submit', uploadData);
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
  }

  function verifyData () {
    var rpta = true;
    $('#txtNivel').removeClass('err');
    $('#txtDescripcion').removeClass('err');

    if ( isBlank($('#txtNivel').val()) ){
      $('#txtNivel').addClass('err');
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
      var nombre   = $('#txtNivel').val();
      var descrip  = $('#txtDescripcion').val();
      var distID   = $('#cboPerfiles').val();

      var pic1     = $("#frmPicture1 label span.msgbox").html();
      var pic2     = $("#frmPicture2 label span.msgbox").html();
      var pic3     = $("#frmPicture3 label span.msgbox").html();
      var pic4     = $("#frmPicture4 label span.msgbox").html();
      var pic5     = $("#frmPicture5 label span.msgbox").html();
      var pictures = "{'normal':'"+ pic1+ "','gana':'"+ pic2+ "','feedback':'"+ pic3+ "','pierde':'"+ pic4+ "','celebra':'"+ pic5+ "'}";

      if (code === -1){
        grabarInsert(nombre, descrip, distID, pictures);
      } else {
        grabarUpdate(nombre, descrip, distID, pictures);
      }
    }
  };

  function grabarInsert(nombre, descrip, distID, pictures){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newNivel", eAuth:auth, eNombre:nombre, eDescrip:descrip, ePerfilID:distID, ePics:pictures, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agrego satisfactoriamente.');
        $('#tblDetalle > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a rel="'+ code + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblDetalle tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblDetalle tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        newItem();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

   function grabarUpdate(nombre, descrip, distID, pictures){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateNivel", eAuth:auth, eRel:code, eNombre:nombre, eDescrip:descrip, ePerfilID:distID, ePics:pictures, rand:Math.random()},
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
    $.post(ajaxReq, {action:"delNivel", eAuth:auth, eRel:rel, rand:Math.random()},
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
    $("#tblDetalle tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');
    code = curControl.attr('rel');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getNivel", eAuth:auth, eRel:code, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtNivel').val(json[0].nombre);
      $('#txtDescripcion').val(json[0].descrip);
      var img = json[0].image;
      img = img.replace(/'/g,'"');
      var jImg = JSON.parse(img);
      $('#picture1').html('<img src="'+ json[0].path+ jImg.normal+ '">');
      $('#picture2').html('<img src="'+ json[0].path+ jImg.gana+ '">');
      $('#picture3').html('<img src="'+ json[0].path+ jImg.feedback+ '">');
      $('#picture4').html('<img src="'+ json[0].path+ jImg.pierde+ '">');
      $('#picture5').html('<img src="'+ json[0].path+ jImg.celebra+ '">');
      $("#frmPicture1 label span.msgbox").html(basename(jImg.normal, '/'));
      $("#frmPicture2 label span.msgbox").html(basename(jImg.gana, '/'));
      $("#frmPicture3 label span.msgbox").html(basename(jImg.feedback, '/'));
      $("#frmPicture4 label span.msgbox").html(basename(jImg.pierde, '/'));
      $("#frmPicture5 label span.msgbox").html(basename(jImg.celebra, '/'));
    });
  };

  function newItem() {
    code = -1;
    $("#tblDetalle tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar () {
    $('#frmData input').removeClass('err');

    $('#txtNivel').val('');
    $('#txtDescripcion').val('');

    $("#frmPicture1 label span.msgbox").html('');
    $("#frmPicture2 label span.msgbox").html('');
    $("#frmPicture3 label span.msgbox").html('');
    $("#frmPicture4 label span.msgbox").html('');
    $("#frmPicture5 label span.msgbox").html('');
    $('#picture1').html('');
    $('#picture2').html('');
    $('#picture3').html('');
    $('#picture4').html('');
    $('#picture5').html('');

    $('#txtNivel').focus();
  };

  function reloadMaster(){
    newItem();
    var master = $('#cboPerfiles').val();
    $("#tblDetalle tbody").html('');

    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getNiveles", eAuth:auth, eRel:master, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtNivel').focus();
      for (i in json) {
        $('#tblDetalle > Tbody:first').append('<tr><td><a rel="' + json[i].id + '" class="edit">' + json[i].nombre + '</a></td><td class=\"tableActs\"><a rel="'+ json[i].id + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');
      }

      // reFlow
      $('#tblDetalle tbody tr td.tableActs .delete').on('click', eliminarFila);
      $('#tblDetalle tbody tr td .edit').on('click', editarFila);
      $(document).foundation('tooltip', 'reflow');
    });
  };

  function showFile1(e){ showFile($(this)[0], $('#picture1'), '#frmPicture1') };
  function showFile2(e){ showFile($(this)[0], $('#picture2'), '#frmPicture2') };
  function showFile3(e){ showFile($(this)[0], $('#picture3'), '#frmPicture3') };
  function showFile4(e){ showFile($(this)[0], $('#picture4'), '#frmPicture4') };
  function showFile5(e){ showFile($(this)[0], $('#picture5'), '#frmPicture5') };

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
      url: "code/upload-picture.php?f=personajes",
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

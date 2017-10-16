<?php
include_once('config.php');

if ( (isset($_SESSION['username'])) && ($_SESSION['rol']) == ROL_ADMIN ){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resultados = getEntrenamientos($db);
  $tblEntrenamientos = genEntrenamientosTR($resultados);

  // Vars
  $title = 'Crear Entrenamiento P&G';
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

          <div>
            <h4 class="azulMain left bold">PANEL DE ENTRENAMIENTOS</h4>
            <hr class="mTop0" />
          </div>

          <div class="row text-left">

              <div class="small-12 medium-6 columns" id="frmData">
                <div class="row">
                  <div class="large-12 columns">
                    <h4 class="azulMain"><a id="btnNewItem" title="Crear Nuevo">CREAR ENTRENAMIENTO</a></h4>
                    <hr class="mTop0" />
                  </div>
                </div>
                <div class="row">
                  <div class="small-5 large-4 columns">
                    <label style="line-height:2;">Disponible para el público</label>
                  </div>
                  <div class="small-5 large-8 columns">
                    <div class="switch round"><input id="chkActivo" type="checkbox" class="activar"><label for="chkActivo"><span class="switch-on">Sí</span><span class="switch-off">No</span></label></div>
                  </div>
                </div>
                <div class="row">
                  <div class="large-12 columns">
                    <label>Entrenamiento
                      <input type="text" value="" id="txtEntrenamiento" placeholder="Nombre del Entrenamiento" maxlength="64" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Objetivo
                      <input type="text" value="" id="txtObjetivo" placeholder="Objetivo del Entrenamiento" maxlength="256" required/>
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Descripción
                      <input type="text" value="" id="txtDescripcion" placeholder="Descripción del Entrenamiento" maxlength="256" required/>
                    </label>
                  </div>
                </div>
                <div class="row text-center">
                  <div class="small-12 large-4 columns">
                    <form id="frmPicture1" action="code/upload-picture.php" class="dropzone">
                      <h6>Trofeo Oro</h6>
                      <div id="picture1"><img src="/static/images/copas/oro.png" alt="" width="100px" height="100px"></div>
                      <input type="file" name="fileUpload" id="fileUpload1" class="inputfile" />
                      <label for="fileUpload1"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                    </form>
                  </div>
                  <div class="small-12 large-4 columns">
                    <form id="frmPicture2" action="code/upload-picture.php" class="dropzone">
                      <h6>Trofeo Plata</h6>
                      <div id="picture2"><img src="/static/images/copas/plata.png" alt="" width="100px" height="100px"></div>
                      <input type="file" name="fileUpload" id="fileUpload2" class="inputfile" />
                      <label for="fileUpload2"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                    </form>
                  </div>
                  <div class="small-12 large-4 columns">
                    <form id="frmPicture3" action="code/upload-picture.php" class="dropzone">
                      <h6>Trofeo Bronce</h6>
                      <div id="picture3"><img src="/static/images/copas/bronce.png" alt="" width="100px" height="100px"></div>
                      <input type="file" name="fileUpload" id="fileUpload3" class="inputfile" />
                      <label for="fileUpload3"><span class="icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span> <span class="msgbox">Seleccionar  un archivo&hellip;</span></label>
                    </form>
                  </div>
                  <p>Las imágenes en formato PNG de 200x200px</p>
                </div>
                <div class="row">
                  <div class="large-12 columns">
                    <div class="clearfix"><br>
                      <a class="small round button success" id="btnGrabar">GRABAR</a> <span id="msgbox"></span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="small-12 medium-6 columns">
                <div class="row">

                  <div class="large-12 columns">
                    <h4 class="azulMain">LISTA DE ENTRENAMIENTOS</h4>
                    <hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <table id="tblEntrenamientos" width="100%">
                      <thead>
                        <tr>
                          <th width="70%">Entrenamientos</th>
                          <th width="30%">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php print($tblEntrenamientos); ?>
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

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>

<div id="myDelete" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle">BORRAR ENTRENAMIENTO</h2>
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
  var pic = [];

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
    $('#tblEntrenamientos tbody tr td.tableActs .delete').on('click', eliminarFila);
    $('#tblEntrenamientos tbody tr td .edit').on('click', editarFila);
    $('#btnBorrarOk').on('click', eliminarFilaOk);
    $('#fileUpload1').on('change', showFile1);
    $("#fileUpload2").on('change', showFile2);
    $("#fileUpload3").on('change', showFile3);
    $('#frmPicture1').on('submit', uploadData);
    $('#frmPicture2').on('submit', uploadData);
    $('#frmPicture3').on('submit', uploadData);
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

  function showFile1(e){ showFile($(this)[0], $('#picture1'), '#frmPicture1') };
  function showFile2(e){ showFile($(this)[0], $('#picture2'), '#frmPicture2') };
  function showFile3(e){ showFile($(this)[0], $('#picture3'), '#frmPicture3') };

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
      url: "code/upload-picture.php?f=trofeos",
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

  function isBlank( data ) {
    return ( $.trim(data).length == 0 );
  };

  function verifyData () {
    var rpta = true;
    $('#txtEntrenamiento').removeClass('err');
    $('#txtObjetivo').removeClass('err');
    $('#txtDescripcion').removeClass('err');

    if ( isBlank($('#txtEntrenamiento').val()) ){
      $('#txtEntrenamiento').addClass('err');
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

    return rpta;
  };

  function grabarDB(){
    if (verifyData()){
      var nombre   = $("#txtEntrenamiento").val();
      var objetivo = $("#txtObjetivo").val();
      var descrip  = $("#txtDescripcion").val();
      var activo   = $("#chkActivo").is(':checked');
      var pic1     = $("#frmPicture1 label span.msgbox").html();
      var pic2     = $("#frmPicture2 label span.msgbox").html();
      var pic3     = $("#frmPicture3 label span.msgbox").html();

      if (codigo === -1){
        grabarInsert(nombre, objetivo, descrip, activo, pic1, pic2, pic3);
      } else {
        grabarUpdate(nombre, objetivo, descrip, activo, pic1, pic2, pic3);
      }
    }
  };

  function grabarInsert(nombre, objetivo, descrip, activo, pic1, pic2, pic3){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"newEntrenamiento", eAuth:auth, eNombre:nombre, eObjetivo:objetivo, eDescrip:descrip, eActivo:activo, ePic1:pic1, ePic2:pic2, ePic3:pic3, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se agregó correctamente.');
        $('#tblEntrenamientos > Tbody:first').append('<tr><td><a rel="' + data + '" class="edit">' + nombre + '</a></td><td class=\"tableActs\"><a data-tooltip aria-haspopup=\"true\" class=\"pLeft5 tip-top\" href=\"crear-curso.php?pid=' + data + '\" title=\"Ver Cursos\"><i class=\"fa fa-book fa-lg\"></i></a><a rel="'+ data + '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar"><i class=\"fa fa-trash-o fa-lg\"></i></a></td></tr>');

        $('#tblEntrenamientos tbody tr td.tableActs .delete').on('click', eliminarFila);
        $('#tblEntrenamientos tbody tr td .edit').on('click', editarFila);
        $(document).foundation('tooltip', 'reflow');
        limpiar();
      } else {
        $('#msgbox').html('Msg: [' + data + ']');
      }
    });
  };

  function grabarUpdate(nombre, objetivo, descrip, activo, pic1, pic2, pic3){
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Procesando');
    $.post(ajaxReq, {action:"updateEntrenamiento", eAuth:auth, eRel:codigo, eNombre:nombre, eObjetivo:objetivo, eDescrip:descrip, eActivo:activo, ePic1:pic1, ePic2:pic2, ePic3:pic3, rand:Math.random()},
    function(data){
      if( data !== '-1' || data !== -1 ){
        // OK!
        $('#msgbox').html('¡Bien! Se actualizó correctamente.');
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
    $.post(ajaxReq, {action:"delEntrenamiento", eAuth:auth, eRel:rel, rand:Math.random()},
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
    $("#tblEntrenamientos tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    curControl.parent().addClass('bgVerdeL');

    limpiar();
    $('#msgbox').html('<i class="fa fa-cog fa-spin"></i> Cargando');
    $.post(ajaxReq, {action:"getEntrenamiento", eAuth:auth, eRel:codigo, rand:Math.random()},
    function(data){
      json = data;

      $('#msgbox').html('');
      $('#txtEntrenamiento').val(json[0].nombre);
      $('#txtObjetivo').val(json[0].objetivo);
      $('#txtDescripcion').val(json[0].descrip);
      $("#frmPicture1 label span.msgbox").html(basename(json[0].pic1, '/'));
      $("#frmPicture2 label span.msgbox").html(basename(json[0].pic2, '/'));
      $("#frmPicture3 label span.msgbox").html(basename(json[0].pic3, '/'));
      $('#picture1').html('<img src="'+ json[0].pic1 +'">');
      $('#picture2').html('<img src="'+ json[0].pic2 +'">');
      $('#picture3').html('<img src="'+ json[0].pic3 +'">');
      if(json[0].activo == 'Si'){
        $( "#chkActivo" ).prop( "checked", true );
      } else {
        $( "#chkActivo" ).prop( "checked", false );
      }
    });
  };

  function newItem() {
    codigo = -1;
    $("#tblEntrenamientos tbody tr td.bgVerdeL").removeClass('bgVerdeL');
    limpiar();
  };

  function limpiar () {
    $('#frmData input').removeClass('err');

    $('#txtEntrenamiento').val('');
    $('#txtObjetivo').val('');
    $('#txtDescripcion').val('');
    $( "#chkActivo" ).prop( "checked", false );
    $("#frmPicture1 label span.msgbox").html('');
    $("#frmPicture2 label span.msgbox").html('');
    $("#frmPicture3 label span.msgbox").html('');
    $('#picture1').html('');
    $('#picture2').html('');
    $('#picture3').html('');
    $('#txtEntrenamiento').focus();
  };
</script>

  </body>
</html>

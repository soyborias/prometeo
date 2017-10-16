<?php
include_once('config.php');

if (isset($_SESSION['username'])){
  include_once('jupiter/code/fxModelo.php');
  include_once('jupiter/code/fxVista.php');
  include_once('jupiter/code/db_procter.php');

  $resUser = getUserByIdFull($_SESSION['userID'], $db);
  $user    = $resUser[0];
  $picture = genPathPicture($user['usuario_picture'], 'profile/');
  $opSex   = genGeneroCHK($user['usuario_genero']);

  $rank    = getRankByDistribuidora($_SESSION['userID'], $user['distributor_id'], $db);
  $total   = getTotalByDis($user['distributor_id'], $db);

  // GetNivelesByPerfil
  $resNiveles = getNiveles($user['usuario_perfil'], $db);
  $dvNiveles  = genNivelesDiv($resNiveles, $user['usuario_perfil_nivel']);

  // Vars
  $title = 'Perfil Usuario P&G';
  include_once('jupiter/code/fxMenu.php');
  $mnuMain = crearMnuMain('perfil', null);
  $mnuMainMobile = crearMnuMainMobile('perfil', null);
} else {
  // User No logeado
  header ('Location: index.php');
}
?>
<!doctype html>
<html class="no-js" lang="es">
  <head>
    <?php include_once('code/header.php'); ?>
    <link rel="stylesheet" href="static/tpl/v1/css/foundation-datepicker.min.css">
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
      <?php include_once('code/top-bar-title.php'); ?>

      <section class="top-bar-section">
        <?php include_once('code/top-bar-user.php'); ?>
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

          <div class="row">
            <div class="large-12 columns">
              <h4 class="azulMain left bold">MI PERFIL</h4>
              <a class="right" href="trofeos.php"><i class="fa fa-trophy"></i> Ver Trofeos</a>
              <hr class="mTop0" />
            </div>
          </div>

          <div class="row">
            <div class="small-12 medium-5 large-3 columns text-center">
              <form id="frmPicture" action="code/upload-picture.php" class="dropzone">
                <div id="image-holder" class="margin-center"><img src="<?php print($picture); ?>" alt="Picture" /></div>
                <input type="file" name="fileUpload" id="fileUpload" class="inputfile" />
                <label for="fileUpload"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span id="lblFileUpload"> Seleccionar  un archivo&hellip;</span></label>
                <p id="lblUploadMsg">La foto debera tener como<br/>máximo 500x500px<br/>en formato jpg o png</p>
              </form>
            </div>
            <div class="small-12 medium-7 large-9 columns text-left">
              <div class="row">
                <div class="large-12 columns">
                  <div class="row">
                    <div class="small-8 columns">
                      <div class="row">
                        <div class="small-6 columns">
                          <p for="right-label" class="inline azulMain">PUNTAJE GANADO</p>
                        </div>
                        <div class="small-6 columns">
                          <?php print($_SESSION['puntos']); ?> Pts.
                        </div>
                      </div>
                      <div class="row">
                        <div class="small-6 columns">
                          <p for="right-label" class="inline azulMain">PUNTAJE CANJEADO</p>
                        </div>
                        <div class="small-6 columns">
                          <?php print($_SESSION['puntos2']); ?> Pts.
                        </div>
                      </div>
                      <div class="row">
                        <div class="small-6 columns">
                          <p for="right-label" class="inline azulMain">PUNTOS PARA CANJEAR</p>
                        </div>
                        <div class="small-6 columns">
                          <?php print($_SESSION['puntos3']); ?> Pts.
                        </div>
                      </div>
                      <div class="row">
                        <div class="small-6 columns">
                          <p for="right-label" class="inline azulMain">PERFIL</p>
                        </div>
                        <div class="small-6 columns">
                          <?php print $user['perfil_name']; ?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="small-6 columns">
                          <p for="right-label" class="inline azulMain">NIVEL</p>
                        </div>
                        <div class="small-6 columns">
                          <?php print $user['nivel_name']; ?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="small-6 columns">
                          <p for="right-label" class="inline azulMain">RANKING</p>
                        </div>
                        <div class="small-6 columns">
                          <?php print $rank; ?> de <?php print $total; ?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="small-6 columns">
                          <p for="right-label" class="inline azulMain">EQUIPO</p>
                        </div>
                        <div class="small-6 columns">
                          <?php print $user['team_name']; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="large-12 columns text-center azulMain">
                  <div class="row">
                    <?php print($dvNiveles); ?>
                  </div><br>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="small-12 medium-3 columns"></div>
            <div class="small-12 medium-9 columns text-left">
              <div class="row">
                <form action="" id="frmPerfil">
                  <div class="large-12 columns">
                    <h4 class="azulMain">DATOS PERSONALES</h4><hr class="mTop0" />
                  </div>
                  <div class="large-12 columns pTop1">
                    <label>Nombre
                      <input id="txtNombre" type="text" placeholder="Nombre y Apellido" value="<?php print ucwords($user['usuario_nombre']); ?>" required />
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Documento
                      <input id="txtDoc" type="text" placeholder="Documento de identidad" value="<?php print $user['usuario_doc']; ?>" readonly />
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Email
                      <input id="txtEmail" type="email" placeholder="Email" value="<?php print $user['usuario_email']; ?>" required />
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Teléfono
                      <input id="txtTelefono" type="text" placeholder="teléfono" value="<?php print $user['usuario_telefono']; ?>" required />
                    </label>
                  </div>
                  <div class="large-12 columns" id="chkGenero">
                    <label>Sexo</label>
                    <?php print($opSex); ?>
                  </div>
                  <div class="large-12 columns">
                    <label>Fecha nacimiento
                      <input id="FchNac" type="text" placeholder="yyyy-mm-dd" value="<?php print $user['usuario_fch_nac']; ?>" data-date />
                    </label>
                  </div>
                  <div class="large-12 columns text-center">
                    <button type="submit" class="round button success">Grabar PERFIL</button>
                    <span id="msgbox"></span>
                  </div>
                </form>
                <form action="">
                <div style="padding:0 15px">
                  <div class="row">
                    <div class="large-12 columns">
                      <h4 class="azulMain">DATOS LABORALES</h4><hr class="mTop0" />
                    </div>
                  </div>
                  <div class="row">
                    <div class="small-4 medium-3 columns">
                      <p for="right-label" class="inline azulMain">Distribuidora</p>
                    </div>
                    <div class="small-8 medium-9 columns">
                      <?php print ucwords($user['distributor_name']); ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="small-4 medium-3 columns">
                      <p for="right-label" class="inline azulMain">Sucursal</p>
                    </div>
                    <div class="small-8 medium-9 columns">
                      <?php print ucwords($user['subsidiary_name']); ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="small-4 medium-3 columns">
                      <p for="right-label" class="inline azulMain">Supervisor</p>
                    </div>
                    <div class="small-8 medium-9 columns">
                      <?php print ucwords($user['supersivor_nombre']); ?>
                    </div>
                  </div>
                </div>
                </form>
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

<?php include_once('code/script.php'); ?>
<script src="js/foundation-datepicker.min.js" ></script>
<script src="js/locales/foundation-datepicker.es.js" ></script>
<script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
<script>
  $(document).on('ready', iniciar);

  function iniciar(){
    $(document).foundation();

    $('#FchNac').fdatepicker({
      format: 'yyyy-mm-dd',
      language: 'es',
      disableDblClickSelection: true
    });

    $('#txtTelefono').inputmask('(99) 9999[9]-9999');
    $('#FchNac').inputmask('9999-9[9]-9[9]');
    $('#frmPicture').on('submit', uploadData);
  };

  function uploadData(e){
    e.preventDefault();
    $("#lblFileUpload").html(' Cargando <i class="fa fa-spinner fa-pulse"></i>');
    $('#lblUploadMsg').html('La foto debera tener como<br/>maximo 500x500px<br/>en formato jpg o png');

    $.ajax({
      url: "code/upload-picture.php",
      type: "POST",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData:false,
      success: function(data){
        data = jQuery.parseJSON( data );
        if (data.rpta == 'ok'){
          $('#lblFileUpload').html(' Actualizado <i class="fa fa-check-circle-o"></i>');
        } else {
          $('#lblFileUpload').html(' No permitido <i class="fa fa-exclamation-triangle"></i>');
          $('#lblUploadMsg').html('Error: ' + data.msg);
        }
      }
    });
  };

  $('#frmPerfil').submit(function(){
    $("#msgbox").html(' <i class="fa fa-spinner fa-pulse"></i>');
    var nombre = $('#txtNombre').val();
    var email  = $('#txtEmail').val();
    var tel    = $('#txtTelefono').val();
    var fchNac = $('#FchNac').val();
    var genero = $("#chkGenero input[type='radio']:checked").val();

    var ajaxReq = "jupiter/api.php";
    $.post(ajaxReq, {action:"updateUserPerfil", eNom:nombre, eEmail:email, eTel:tel, eFchNac:fchNac, eGenero:genero, rand:Math.random()},
    function(data){
      if((data.status == '2') || (data.status == 2)){
        $("#msgbox").html(' Se grabó satisfactoriamente.').fadeTo(900,1);
      } else {
        $("#msgbox").html( 'Error: '+ data.status ).fadeTo(900,1);
      }
    });

    return false;
  });

  $("#fileUpload").on('change', function () {
     var countFiles = $(this)[0].files.length;

     var imgPath = $(this)[0].value;
     var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
     var image_holder = $("#image-holder");
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

          $("#lblFileUpload").html(fileName);
          $("#lblFileUpload").html(' Cargando <i class="fa fa-spinner fa-pulse"></i>');
          image_holder.show();
          reader.readAsDataURL($(this)[0].files[i]);
        }

        $('#frmPicture').submit();

      } else {
        alert("El formato de imagen no es soportado. Intente con JPG o PNG");
      }
    } else {
      alert("Solo valido para imagenes");
    }
  });

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
</script>

  </body>
</html>

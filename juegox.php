<?php
include_once('config.php');

if (isset($_SESSION['username'])){

  // Vars
  $pid = (isset($_GET['pid'])) ? $_GET['pid'] : -1;
  $retry = (isset($_GET['retry'])) ? 1 : 0;
  $_SESSION['puntos']  = 0;
  $title = 'Juego P&G';

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
      <ul class="title-area">
        <li class="name">
          <h1>
            <a href="#">
              <img src="static/images/isotipo.png" alt="P&G">
              <span> Get Trained</span>
            </a>
          </h1>
        </li>
      </ul>

      <section class="top-bar-section">
        <ul class="right bgAzukMain">
          <li>
            <input type="text" name="busqueda" id="autocomplete" placeholder="Entrenamientos / Cursos / Temas" class="w240 border-radius">
          </li>
          <li>
            <a href="#"><i class="fa fa-search"></i></a>
          </li>
          <li class="has-dropdown">
            <a href="#"><i class="fa fa-bell"></i></a>
            <ul class="dropdown">
              <li><a href="#">No tienes notificaciones</a></li>
            </ul>
          </li>
          <li>
            <a href="#">Puntaje <span>17500</span></a>
          </li>
        </ul>
      </section>
    </nav>


    <!-- Menu -->
    <div class="left-sidebar show-for-large-up">
        <ul class="property-nav text-center">
          <li>
            <a href="perfil.php" data-tooltip aria-haspopup="true" class="tip-right" title="Usuario"><i class="fa fa-user"></i></a>
          </li>
          <li class="current">
            <a href="dashboard.php" data-tooltip aria-haspopup="true" class="tip-right" title="Entrenamientos"><i class="fa fa-graduation-cap"></i></a>
          </li>
          <li>
            <a href="reporte.php" data-tooltip aria-haspopup="true" class="tip-right" title="Reportes"><i class="fa fa-indent"></i></a>
          </li>
          <li>
            <a href="index.php?logout" data-tooltip aria-haspopup="true" class="tip-right" title="Cerrar Sesión"><i class="fa fa-sign-out"></i></a>
          </li>
        </ul>
    </div>


  <main id="page" role="main" class="main mIzq45">

    <section>
      <div class="row pTop1">
        <div class="medium-12 columns">
          <div class="text-left">
            <ul class="breadcrumbs mDown0">
              <li><a href="dashboard.php">Entrenamientos</a></li>
              <li><a href="entrenamiento.php">Pampers</a></li>
              <li><a href="curso.php">Fundamentos Pampers</a></li>
              <li><a href="tema.php">Familia de productos</a></li>
              <li><a href="juego1.php">Juego 1</a></li>
              <li class="current"><a href="#">Jugando</a></li>
            </ul>
            <h1 class="m0 azulMain">JUEGO: FAMILIA DE PRODUCTOS</h1>
            <h5 class="subheader azulSub">INSTRUCCIONES: Arrastra la respuesta correcta al área punteada para pasar al siguiente nivel.</h5>
          </div>
          <hr class="mTop0" />

          <div class="row">
            <div id="portada">
              <img src="static/images/juegos/portada01.jpg" alt="Portada"><br/>
              <a id="btnInstrucciones" href="#" class="button large round warning btnShadow" data-reveal-id="Instrucciones">Instrucciones</a>
              <a id="btnJugar" href="#" class="button large round success btnShadow">Jugar</a>
            </div>

            <div id="juego" class="hide">
              <img src="static/images/juegos/juego01.png" alt="juego">

              <div id="caja1" class="suelta" class="suelta btn btn-default" role="button">
                <span>Descubre que pañal<br/>
                 se EQUIVOCARON<br/>
                en la fabrica<br/>
                de Pampers</span>
              </div>

              <div id="cajaA" class="objeto arrastrable" id="elemento-a">
                <img src="static/images/juegos-suenos.png">
              </div>

              <div id="cajaB" class="objeto arrastrable" id="elemento-b">
                <img src="static/images/premium-care.png">
              </div>

              <div id="cajaC" class="objeto arrastrable" id="elemento-c">
                <img src="static/images/dulces-suenos.png">
              </div>
            </div>
          </div>

        </div>
      </div>

    </section>

  </main>
<div id="Instrucciones" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="blanco">¡INSTRUCCIONES!</h2>
  <div>
    <p class="lead">Arrastra la respuesta correcta al área punteada para pasar al siguiente nivel.</p>
  </div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoNext" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="blanco">¡EXCELENTE! <strong>+100 Puntos</strong></h2>
  <div>
    <p class="lead">Superaste los 5 niveles, ahora pasa al 2do juego</p>
    <a href="juego2.php" class="btnMsg button large round warning">Avanzar</a>
  </div>
</div>

<div id="juegoOk" class="reveal-modal msgOk" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="blanco">¡EXCELENTE! <strong>+100 Puntos</strong></h2>
  <div>
    <p class="lead">Respuesta Correcta</p>
    <p>EL CLIENTE GANA: ...<br/>
    TU GANAS: ...</p>
    <a href="juego1.php" class="btnMsg button large round warning">Avanzar</a>
  </div>
</div>

<div id="juegoPista" class="reveal-modal msgAlert" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="blanco">¡SIGUE INTENTANDO!</h2>
  <p class="lead">PISTA: Te dare una pista para ayudarte</p>
  <a href="juego1-a.php?pid=<?php print $pid; ?>&retry" class="btnMsg button large round info">Reintentar</a>
</div>

<div id="juegoFeedback" class="reveal-modal msgBad" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="blanco">¡VUELVE A INTENTARLO!</h2>
  <p class="lead">RESPUESTA CORRECTA: "Dulces Sueños"</p>
  <p>EL CLIENTE PERDIO: ...</br>
    TU PERDISTE: ...</p>
  <a href="juego1.php?retry=<?php print $pid; ?>" class="btnMsg button large round info">Reiniciar</a>
</div>

    <script src="js/vendor/jquery.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="https://cdn.linearicons.com/free/1.0.0/svgembedder.min.js"></script>
    <script>
      $(document).foundation();
        var n = 0;
        var ok = "cajaC";
        var puntos = 100;
        var rel = "<?php echo $pid; ?>";
        var ajaxReq = "jupiter/api.php";
        var auth = 0;
        var retry = <?php print $retry; ?>;
        var pid = "<?php print $pid; ?>";

        $('#btnJugar').on('click', abrirJuego);

        function abrirJuego (){
           $( "#portada" ).fadeOut( "slow" );
           $( "#juego" ).show();
        }

      // Game
      $(".arrastrable").draggable({ containment: '#juego' });
      $(".arrastrable").data("soltado", false);
      $(".suelta").data("nombre", $(".objeto").attr("id"));

      $(".suelta").droppable({
        activate: function( event, ui ) {
          $(".suelta").addClass("borderRojo");
        },
        deactivate: function( event, ui ) {
          $(".suelta").removeClass("borderRojo");
        },
         drop: function( event, ui ) {
            if (!ui.draggable.data("soltado")){
                var draggableId = ui.draggable.attr("id");
                //alert(draggableId);
                if (draggableId === ok){
                  grabarWin();
                  if (pid === 5) {
                    $('#juegoNext').foundation('reveal', 'open');
                  } else {
                    $('#juegoOk').foundation('reveal', 'open');
                  }
                } else {
                  n++;
                  if (retry == 1){
                    $('#juegoFeedback').foundation('reveal', 'open');
                  }
                  if ( n == 1 ){
                    $('#juegoPista').foundation('reveal', 'open');
                  } else {
                    $('#juegoFeedback').foundation('reveal', 'open');
                  }

                  ui.draggable.css({ 'top': 250, 'right': 200 })
                }
            }
         },
         out: function( event, ui ) {
          $(".suelta").removeClass("borderRojo");
         }
      });

      function grabarWin(){
        $.post(ajaxReq, {action:"saveGameWin", eAuth:auth, eRel:rel, rand:Math.random()},
          function(data){
            //alert(data);
        });
      };

    </script>

  </body>
</html>
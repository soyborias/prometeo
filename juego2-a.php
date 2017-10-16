<!doctype html>
<html class="no-js" lang="es">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">

    <title>Juego P&G</title>
    <meta name="description" content="Get Trained P&G">
    <meta name="author" content="RC">

    <link rel="stylesheet" href="static/tpl/v1/css/foundation.css" />
    <link rel="stylesheet" href="static/tpl/v1/css/css.css?v=5">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Titillium+Web:400,700,600">
    <script src="js/vendor/modernizr.js"></script>
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
              <li class="current"><a href="#">Juego 1</a></li>
            </ul>
            <h1 class="m0 azulMain">JUEGO: FAMILIA DE PRODUCTOS</h1>
            <h5 class="subheader grisDark">INSTRUCCIONES: Arrastra la respuesta correcta donde Don Pepe para resolver el juego</h5>
          </div>
          <hr class="mTop0" />

          <div class="row">
            <div class="small-12 columns">

              <div class="juego-1">
                <a id="suelta" class="suelta btn btn-default" href="#" role="button">
                  <div class="imagen">
                    <img src="static/images/pregunta2.jpg" class="pad1">
                  </div>
                </a>
              </div><br><br><br>

              <div class="row">
                <div class="medium-3 columns">
                  <div class="objeto arrastrable opBlue" id="elemento-a">
                    <h3 class="blanco"><i class="fa fa-lightbulb-o"></i></h3>
                    <h4 class="blanco">TRIPACKS</h4>
                  </div>
                </div>

                <div class="medium-3 columns">
                  <div class="objeto arrastrable opBlue" id="elemento-b">
                    <h3 class="blanco"><i class="fa fa-lightbulb-o"></i></h3>
                    <h4 class="blanco">PACK ECONÓMICO</h4>
                  </div>
                </div>

                <div class="medium-3 columns">
                  <div class="objeto arrastrable opBlue" id="elemento-c">
                    <h3 class="blanco"><i class="fa fa-lightbulb-o"></i></h3>
                    <h4 class="blanco">HÍPER PACK</h4>
                  </div>
                </div>

                <div class="medium-3 columns">
                  <div class="objeto arrastrable opBlue" id="elemento-d">
                    <h3 class="blanco"><i class="fa fa-lightbulb-o"></i></h3>
                    <h4 class="blanco">MEGA PACK</h4>
                  </div>
                </div>
              </div>

            </div>

          </div>
          <p>&nbsp;</p>

        </div>

      </div>



    </section>

  </main>
<div id="juegoOk" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="azulMain">¡EXCELENTE!</h2>
  <p class="lead text-center">Respuesta Correcta <strong>+100 Puntos</strong></p>
  <p  class="text-center"><img src="static/images/juegoOk.jpg" alt=""></p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoPista" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="amarillo">¡UPS! Respuesta Incorrecta</h2>
  <p class="lead">PISTA: Fíjate bien en la presentación, una de éstas no corresponde a la familia Juegos y Sueños de Pampers.</p>
  <p class="text-center"><img src="static/images/juegoBad.jpg" alt=""></p>
  <p class="text-center">EL CLIENTE PERDIO: ...</p>
  <p class="text-center">TU PERDISTE: ...</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div id="juegoFeedback" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
  <h2 id="modalTitle" class="rojo">¡UPS! RESPUESTA INCORRECTA</h2>
  <p class="lead">La respuesta correcta es "ABC"</p>
  <p class="text-center"><img src="static/images/juegoBad.jpg" alt=""></p>
  <p class="text-center">EL CLIENTE PERDIO: ...</p>
  <p class="text-center">TU PERDISTE: ...</p>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

    <script src="js/vendor/jquery.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="https://cdn.linearicons.com/free/1.0.0/svgembedder.min.js"></script>
    <script>
      $(document).foundation();
        var n = 0;
        var ok = "elemento-d";


      $(".arrastrable").draggable();
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
                  $('#juegoOk').foundation('reveal', 'open');
                } else {
                  n++;
                  if ( n<2 ){
                    $('#juegoPista').foundation('reveal', 'open');
                  } else {
                    $('#juegoFeedback').foundation('reveal', 'open');
                  }
                }

            }
         },
         out: function( event, ui ) {
          $(".suelta").removeClass("borderRojo");
         }

      });
    </script>

  </body>
</html>
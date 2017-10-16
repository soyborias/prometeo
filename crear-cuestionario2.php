<!doctype html>
<html class="no-js" lang="es">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">

    <title>Crear Cuestionario P&G</title>
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
        <ul class="right">
          <li class="active"><a href="#"><i class="fa fa-search"></i></a></li>
          <li class="active"><a href="#"><i class="fa fa-bell"></i></a></li>
          <li class="active"><a href="#">Puntaje <span>17500</span></a></li>
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
        <div class="large-12 columns">

          <div class="row pDown1">
            <div class="small-8 medium-9 columns">
              <div class="text-left azulMain">
                <ul class="breadcrumbs mDown0">
                  <li><a href="">Entrenamientos</a></li>
                  <li><a href="">Cuestionario</a></li>
                  <li class="current"><a href="#">Crear</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row text-left">
            <form>

              <div class="small-12 medium-6 columns">

                <div class="row">
                  <div class="large-12 columns">
                    <h4 class="azulMain">CREAR CUESTIONARIO</h4><hr class="mTop0" />
                  </div>

                  <div class="large-12 columns">
                    <label>PREGUNTA
                      <input type="text" placeholder="Ingresa Pregunta" />
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Respuesta CORRECTA
                      <input type="text" placeholder="Respuesta Correcta" />
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 1
                      <input type="text" placeholder="Distractor 1" />
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 2
                      <input type="text" placeholder="Distractor 2" />
                    </label>
                  </div>
                  <div class="large-12 columns">
                    <label>Distractor 3
                      <input type="text" placeholder="Distractor 3" />
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <label>Pista
                      <input type="text" placeholder="Pista" />
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <label>Feedback
                      <textarea placeholder="Feedback"></textarea>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <label>Ganancia del cliente
                      <textarea placeholder="Feedback"></textarea>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <label>Perdida del cliente
                      <textarea placeholder="Feedback"></textarea>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <label>Ganancia del trabajador
                      <textarea placeholder="Feedback"></textarea>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <label>Perdida del trabajador
                      <textarea placeholder="Feedback"></textarea>
                    </label>
                  </div>

                  <div class="large-12 columns">
                    <div class="clearfix">
                      <a class="small round button success left">Agregar Pregunta</a>
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
                    <table>
                      <thead>
                        <tr>
                          <th>Pregunta</th>
                          <th width="200">Respuesta</th>
                          <th width="150">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>This is longer content Donec id elit non mi porta gravida at eget metus.</td>
                          <td>Respuesta correcta ...</td>
                          <td>
                            <a data-tooltip aria-haspopup="true" class="tip-top" href="#" title="Editar">
                              <i class="fa fa-pencil fa-lg"></i></a>
                            <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="#" title="Borrar">
                              <i class="fa fa-trash-o fa-lg"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td>This is longer Content Goes Here Donec id elit non mi porta gravida at eget metus.</td>
                          <td>Respuesta correcta ...</td>
                          <td>
                            <a data-tooltip aria-haspopup="true" class="tip-top" href="#" title="Editar">
                              <i class="fa fa-pencil fa-lg"></i></a>
                            <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="#" title="Borrar">
                              <i class="fa fa-trash-o fa-lg"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td>This is longer Content Goes Here Donec id elit non mi porta gravida at eget metus.</td>
                          <td>Respuesta correcta ...</td>
                          <td>
                            <a data-tooltip aria-haspopup="true" class="tip-top" href="#" title="Editar">
                              <i class="fa fa-pencil fa-lg"></i></a>
                            <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="#" title="Borrar">
                              <i class="fa fa-trash-o fa-lg"></i></a>
                          </td>
                        </tr>
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

    <div>


    </div>

  </main>

    <script src="js/vendor/jquery.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="https://cdn.linearicons.com/free/1.0.0/svgembedder.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.4/jquery.easypiechart.min.js"></script>
    <script>
      $(document).foundation();

      function crearEntrenamiento(){


      }

    </script>

  </body>
</html>
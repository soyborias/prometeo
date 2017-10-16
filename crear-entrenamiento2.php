<!doctype html>
<html class="no-js" lang="es">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">

    <title>Cuestionario P&G</title>
    <meta name="description" content="Get Trained P&G">
    <meta name="author" content="RC">

    <link rel="stylesheet" href="static/tpl/v1/css/foundation.css" />
    <link rel="stylesheet" href="static/tpl/v1/css/css.css?v=5">
    <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
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
          <li class="active"><a href="#"><span class="lnr lnr-magnifier"></span></a></li>
          <li class="active"><a href="#"><span class="lnr lnr-bubble"></span></a></li>
          <li class="active"><a href="#">Puntaje <span>17500</span></a></li>
        </ul>
      </section>
    </nav>


    <!-- Menu -->
    <div class="left-sidebar show-for-large-up">
        <ul class="property-nav">
          <li>
            <a href="perfil.php" data-tooltip aria-haspopup="true" class="tip-right" title="Usuario"><span class="lnr lnr-user"></span></a>
          </li>
          <li class="current">
            <a href="dashboard.php" data-tooltip aria-haspopup="true" class="tip-right" title="Entrenamientos"><span class="lnr lnr-graduation-hat"></span></a>
          </li>
          <li>
            <a href="reporte.php" data-tooltip aria-haspopup="true" class="tip-right" title="Reportes"><span class="lnr lnr-indent-increase"></span></a>
          </li>
          <li>
            <a href="index.php?logout" data-tooltip aria-haspopup="true" class="tip-right" title="Cerrar Sesión"><span class="lnr lnr-enter"></span></a>
          </li>
        </ul>
    </div>


  <main id="page" role="main" class="main mIzq45">

    <section>

      <div class="row w90 text-left">
        <div class="large-12 columns">
          <h1>Cuestionario</h1><hr/>

          <div class="row">
            <p>1. ¿Lorem ipsum dolor sit amet, consectetur adipisicing elit.?</p>

            <form>
              <div class="row">
                <div class="large-12 columns">
                  <label>Input Label
                    <input type="text" placeholder="large-9.columns" />
                  </label>
                </div>
              </div>
              <div class="row">
                <div class="large-12 columns">
                  <label>Select Box
                    <select>
                      <option value="husker">Husker</option>
                      <option value="starbuck">Starbuck</option>
                      <option value="hotdog">Hot Dog</option>
                      <option value="apollo">Apollo</option>
                    </select>
                  </label>
                </div>
              </div>
              <div class="row">
                <div class="large-6 columns">
                  <label>Choose Your Favorite</label>
                  <input type="radio" name="pokemon" value="Red" id="pokemonRed"><label for="pokemonRed">Red</label>
                  <input type="radio" name="pokemon" value="Blue" id="pokemonBlue"><label for="pokemonBlue">Blue</label>
                </div>
                <div class="large-6 columns">
                  <label>Check these out</label>
                  <input id="checkbox1" type="checkbox"><label for="checkbox1">Checkbox 1</label>
                  <input id="checkbox2" type="checkbox"><label for="checkbox2">Checkbox 2</label>
                </div>
              </div>
              <div class="row">
                <div class="large-12 columns">
                  <label>Textarea Label
                    <textarea placeholder="small-12.columns"></textarea>
                  </label>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <script>
      $(document).foundation();

var options = [];
var data = [
    {
        value: 75,
        color:"#F7464A",
        highlight: "#FF5A5E",
        label: "Avance"
    },
    {
        value: 25,
        color: "#999999",
        highlight: "#808080",
        label: "Falta"
    }
]
var data2 = [
    {
        value: 100,
        color:"#999999",
        highlight: "#808080",
        label: "Sin iniciar"
    }
]

var ctx1 = $("#myChart1").get(0).getContext("2d");
var myDoughnutChart = new Chart(ctx1).Doughnut(data,options);

var ctx2 = $("#myChart2").get(0).getContext("2d");
var myDoughnutChart = new Chart(ctx2).Doughnut(data,options);

var ctx3 = $("#myChart3").get(0).getContext("2d");
var myDoughnutChart = new Chart(ctx3).Doughnut(data,options);

var ctx4 = $("#myChart4").get(0).getContext("2d");
var myDoughnutChart = new Chart(ctx4).Doughnut(data2,options);

var ctx5 = $("#myChart5").get(0).getContext("2d");
var myDoughnutChart = new Chart(ctx5).Doughnut(data2,options);

var ctx6 = $("#myChart6").get(0).getContext("2d");
var myDoughnutChart = new Chart(ctx6).Doughnut(data2,options);

    </script>

  </body>
</html>
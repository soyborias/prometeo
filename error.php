<?php
    $msg = (isset($_POST['mensajeError'])) ? $_POST['mensajeError'] : 'Mensaje No Encontrado';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Entrenate PG - ERROR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

        * {
            line-height: 1.2;
            margin: 0;
        }

        html {
            background: #003c73;
            color: #fff;
            display: table;
            font-family: sans-serif;
            height: 100%;
            text-align: center;
            width: 100%;
        }

        body {
            display: table-cell;
            vertical-align: middle;
            margin: 2em auto;
        }

        h1 {
            color: #fff;
            font-size: 2em;
            font-weight: 400;
        }

        p {
            margin: 0 auto;
            width: 280px;
        }

        @media only screen and (max-width: 280px) {

            body, p {
                width: 95%;
            }

            h1 {
                font-size: 1.5em;
                margin: 0 0 0.3em;
            }

        }

    </style>
</head>
<body>
    <p><img src="static/images/logoPG.png" alt="P&amp;G"></p>
    <h1>ERROR</h1>
    <p><?php print $msg; ?></p>
</body>
</html>

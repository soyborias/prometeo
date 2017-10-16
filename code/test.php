<?php
define('CHECKOUT_SECURITY_URL', 'http://payment.opencheckout.co/api/transaccion.json');
define('CHECKOUT_PARTNER_URL', 'http://payment.opencheckout.co/pgp/payment/create-cart/');

if($_POST) {
    $productos = array(
        1 => array(
            'PROD_CODIGO'=> 1,
            'PROD_NOMBRE'=> 'Estudiantes y Embajadores',
            'PROD_PRECIO'=> 120,
            'PROD_CANTIDAD'=> 1,
        ),
        2 => array(
            'PROD_CODIGO'=> 2,
            'PROD_NOMBRE'=> 'General',
            'PROD_PRECIO'=> 150,
            'PROD_CANTIDAD'=> 1,
        ),
        3 => array(
            'PROD_CODIGO'=> 3,
            'PROD_NOMBRE'=> 'VIP',
            'PROD_PRECIO'=> 200,
            'PROD_CANTIDAD'=> 1,
        ),
        4 => array(
            'PROD_CODIGO'=> 4,
            'PROD_NOMBRE'=> 'Platiniun',
            'PROD_PRECIO'=> 250,
            'PROD_CANTIDAD'=> 1,
        )
    );
    $params = array(
        'REFERENCE' => '561b17eb5c6c7',
        'DPAGO' => array(
            'EMAIL' => 'likerow@hotmail.com',
            'NOMBRES' => 'Jared+Cusi+Guizado'
        ),
        'DCONFIG' => array(
            'AUTOMONTO' => 1
        ),
        'partner' => 'eventos',
        'PRODUCTO_DETALLE' =>'Marketing personal',
        'PRODUCTO_DETALLE_LARGO' => '',
        'PRODUCTO_DIRECCION' => '',
        'PRODUCTO_REFERENCIA' => '',
        'PRODUCTO_FECHA_INICIAL' => '',
        'PRODUCTO_FECHA_FINAL' => '',
        'PRODUCTO_ORGANIZADOR' => 'BiiLab Eventos',
        'PRODUCTO_UBIGEO' => 'LIMA,PE',
        'PRODUCTO_IMAGEN' => 'https://s3-us-west-2.amazonaws.com/eventos.biialab.org/imagenes/publications/semimedi/1054_biialab_3485_biialab_ser-autn-ntico.png',
        'KEY' => '0812e6a5577708bcc4ddabb96b3fa11f',
        'PRODUCTOS' => $productos[$_POST['tipo']]
    );





    try {
        $c = curl_init(CHECKOUT_SECURITY_URL);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
        curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        echo curl_exec($c);
        curl_close($c);
        exit;
    } catch (Exception $ex) {
        echo $ex->getMessage();exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <form method="post" id="formToken">
            <input type="button" name="Checkout" id="val1" value="Checkout 120" onclick="enviarForm(1)">
            <input type="button" name="Checkout" id="val2" value="Checkout 150" onclick="enviarForm(2)">
            <input type="button" name="Checkout" id="val3" value="Checkout 200" onclick="enviarForm(3)">
            <input type="button" name="Checkout" id="val4" value="Checkout 250" onclick="enviarForm(4)">

        </form>
    </body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
function enviarForm(tipoParams)
{
    $.ajax({
        url: "test.php",
        data : {tipo:tipoParams},
        type : 'POST',
        dataType: "json",
        beforeSend: function( xhr ) {
          xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
        }
      }).done(function( data )
      {
      location.href='<?php echo CHECKOUT_PARTNER_URL?>?TOKEN='+data.token
        console.log(data)
      });
}

</script>
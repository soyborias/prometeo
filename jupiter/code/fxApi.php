<?php
/**
  Funciones API
**/

function getUserByDni($code, $db){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`,  `t1`.`usuario_activo`,
    IFNULL( SUM(`t2`.`puntos_valor`), 0 ) `puntos_suma`
    FROM `pg_usuarios` `t1`
    INNER JOIN `pg_puntos` `t2`
      ON `t2`.`user_id` = `t1`.`usuario_id` AND `t2`.`puntos_estado` = "Ganado"
    WHERE `t1`.`usuario_doc` = "%s" ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getPuntosCanjeByUser($code, $db){
  $sql = 'SELECT IFNULL( SUM(`t3`.`puntos_canje`), 0 ) `puntos_canje`
    FROM `pg_canjes` `t3`
    WHERE `t3`.`user_id` = "%s" ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['puntos_canje'] ) : 0;
  return $rpta;
}

function genUserDataJS($resultados, $puntos_canje){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["nombre"]    = html_entity_decode($item['usuario_nombre']);
      $post["dni"]       = $item['usuario_doc'];
      $post["puntaje"]   = $item['puntos_suma'] - $puntos_canje;
      $post["estado_activo"] = $item['usuario_activo'];

      array_push($response, $post);
    }
  } else {
    $post = array();
    $post["nombre"]    = 'Not found';
    $post["dni"]       = 0;
    $post["puntaje"]   = 0;
    $post["estado_activo"] = 'No';

    array_push($response, $post);
  }

  return $response;
}

function setScoreUser($user_id, $puntaje, $codigo, $db){
   $data = Array (
    'user_id'          => $user_id,
    'puntos_canje'     => $puntaje,
    'codigo_ref'       => $codigo,
    'puntos_fch_reg'   => $db->now()
  );

  $id = $db->insert ('pg_canjes', $data);
  $status = ($id) ? 'ok' : 'error';
  $info   = ($id) ? $id    : $db->getLastError();

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

function genScoreDataJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["dni"]       = $item['usuario_doc'];
      $post["t_estado"]  = "No";
      $post["t_info"]    = "Error general";
      $post["t_codigo"]  = $codigo;

      array_push($response, $post);
    }
  } else {
    $post = array();
    $post["dni"]       = $dni;
    $post["t_estado"]  = "No";
    $post["t_info"]    = "Error general";
    $post["t_codigo"]  = $codigo;

    array_push($response, $post);
  }

  return $response;
}
?>
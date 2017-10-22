<?php
/**
  Login
**/
function getUserLogin($user_doc, $password, $db){
  $sql = 'SELECT `usuario_id`, `usuario_clave`, `usuario_rol`, `usuario_activo`, `usuario_picture`, `usuario_nombre`, `usuario_perfil`, `usuario_perfil_nivel`
    FROM `pg_usuarios`
    WHERE `usuario_email` = "%s"
    LIMIT 1; ';
  $query = sprintf($sql, $user_doc);
  $result = $db->rawQuery($query);

  $rpta = Array (
    'rpta' => 0,
    'usuario_id'  => 0,
    'usuario_rol' => 0
  );

  if ( count($result)>0 ){
    foreach($result as $item){
      if ( $item['usuario_clave'] === $password ){
        if ( $item['usuario_activo'] === 'Si'){
          $rpta['rpta']            = 2; // Ok
          $rpta['usuario_id']      = $item['usuario_id'];
          $rpta['usuario_rol']     = $item['usuario_rol'];
          $rpta['usuario_nombre']  = $item['usuario_nombre'];
          $rpta['usuario_picture'] = $item['usuario_picture'];
          $rpta['usuario_perfil']  = $item['usuario_perfil'];
          $rpta['usuario_nivel']   = $item['usuario_perfil_nivel'];
        } else {
          $rpta['rpta'] = 3; // Inactivo
        }
      }
    }
  } else {
      $rpta['rpta'] = 4; // Wrong
  }

  return $rpta;
}

function verifyLogin($user_id, $password, $db){
  $sql = 'SELECT `usuario_id`, `usuario_clave`
    FROM `pg_usuarios`
    WHERE `usuario_id` = "%d"
    AND `usuario_activo` = "Si"
    LIMIT 1; ';
  $query = sprintf($sql, $user_id);
  $result = $db->rawQuery($query);

  $rpta = MSG_ERROR;
  if ( count($result)>0 ){
    if ( $result[0]['usuario_clave'] === $password ){
      $rpta = MSG_OK;
    }
  }

  return $rpta;
}

function setLog($user_id, $ip, $db){
  //ip2long :: long2ip
  $data = Array (
    'user_id'   => $user_id,
    'remote_ip' => ip2long($ip),
    'fch_reg'   => $db->now()
  );

  $id = $db->insert ('pg_log', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function getUsuarios($db){
  $query = 'SELECT `usuario_id`, `usuario_nombre`, `usuario_doc`, `usuario_activo`
    FROM `pg_usuarios`
    ORDER BY `usuario_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getListUsers($ids, $db){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    WHERE `t1`.`usuario_activo` = "Si"
    AND `t1`.`usuario_id` IN ( %s )
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $ids);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getListUserTotalesFilter($search, $filtro_search, $db){
  $arr  = preg_split('/#/', $search);
  $s1 = ' AND `t1`.`usuario_id` = -1 ';
  $s2 = '';

  # Distribuidora
  if ($arr[0] == "D") {
    $s1 = ' AND t2.distributor_id = %d ';
    $s1 = sprintf($s1, $arr[2]);

    switch ($arr[1]) {
      case 'T': # Totales
        $s2 = '';
        break;

      case 'I': # Inactivos
        $s2 = ' AND `num_fin` = 0 ';
        break;

      case 'A': # Activos
        $s2 = ' AND `num_fin` <= 3 AND `num_fin` > 0 ';
        break;

      case 'F': # Entrenados
        $s2 = ' AND `num_fin` = 3 ';
        break;

      case 'P': # Entrenados
        $s2 = ' AND `num_fin` < 3 AND `num_fin` > 0 ';
        break;
    }
    $query = genSQLforD($s1, $s2);
  }

  # Sucursal
  if ($arr[0] == "S") {
    $s1 = ' AND t2.subsidiary_id = %d ';
    $s1 = sprintf($s1, $arr[2]);

    switch ($arr[1]) {
      case 'T': # Totales
        $s2 = '';
        break;

      case 'I': # Inactivos
        $s2 = ' AND `num_fin` = 0 ';
        break;

      case 'A': # Activos
        $s2 = ' AND `num_fin` <= 3 AND `num_fin` > 0 ';
        break;

      case 'F': # Entrenados
        $s2 = ' AND `num_fin` = 3 ';
        break;

      case 'P': # Entrenados
        $s2 = ' AND `num_fin` < 3 AND `num_fin` > 0 ';
        break;
    }
    $query = genSQLforS($s1, $s2);
  }

  # Equipo
  if ($arr[0] == "E") {
    $s1 = ' AND t1.team_id = %d ';
    $s1 = sprintf($s1, $arr[2]);

    switch ($arr[1]) {
      case 'T': # Totales
        $s2 = '';
        break;

      case 'I': # Inactivos
        $s2 = ' AND `num_fin` = 0 ';
        break;

      case 'A': # Activos
        $s2 = ' AND `num_fin` <= 3 AND `num_fin` > 0 ';
        break;

      case 'F': # Entrenados
        $s2 = ' AND `num_fin` = 3 ';
        break;

      case 'P': # Entrenados
        $s2 = ' AND `num_fin` < 3 AND `num_fin` > 0 ';
        break;
    }
    $query = genSQLforE($s1, $s2);
  }

  # Entrenamiento
  if ($arr[0] == "T") {
    //$s1 = ' AND FIND_IN_SET (%d, t7.curricula_entrenamientos) ';
    $s1 = ' entrenamiento_id = %d ';
    $s1 = sprintf($s1, $arr[2]);

    switch ($arr[1]) {
      case 'T': # Totales
        $s2 = '';
        break;

      case 'I': # Inactivos
        $s2 = ' AND `Estado` = "Inactivo" ';
        break;

      case 'A': # Activos
        $s2 = ' AND `Estado` IN ("Activo", "Finish", "Start") ';
        break;

      case 'F': # Entrenados
        $s2 = ' AND `Estado` = "Finish" ';
        break;

      case 'P': # Entrenando
        $s2 = ' AND `Estado` IN ("Activo", "Start") ';
        break;
    }

    // Filter
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t1`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $s3 = $equipo. $perfil. $distro. $sucursal;
    $query = genSQLforEntrenamientos_v2($s1, $s2, $s3);
  }

  # Temas
  if ($arr[0] == "TE") {
    $s1 = $arr[2];
    $s2 = '';

    switch ($arr[1]) {
      case 'T': # Totales
        $s2 = '';
        break;

      case 'I': # Inactivos
        $s2 = '';
        break;

      case 'A': # Activos
        $s2 = ' AND ( (t17.tema_id IS NOT NULL) OR (t18.tema_id IS NOT NULL) ) ';
        break;

      case 'F': # Entrenados
        $s2 = ' AND t18.tema_id IS NOT NULL ';
        break;

      case 'P': # Entrenando
        $s2 = ' AND t17.tema_id IS NOT NULL     AND t18.tema_id IS NULL ';
        break;
    }

    // Filter
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t1`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $s3 = $equipo. $perfil. $distro. $sucursal;
    $query = genSQLforTema($s1, $s2, $s3);
  }

  // Procesa Query
  $result = $db->rawQuery($query);
  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function genSQLforD($s1, $s2){
  $sql = 'SELECT * FROM
    (
      SELECT `t0`.`usuario_id`, `t0`.`usuario_nombre`, `t0`.`usuario_doc`, `t0`.`usuario_email`
      , t1.distributor_id, t1.distributor_name
      , SUM(IF(`t15`.`avance` >= 100, 1,0)) AS num_fin
      , IFNULL(GROUP_CONCAT( distinct `t15`.`entrenamiento_id`), "") `num_fin_ids`
      , IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`

      FROM supply_distributor t1

      INNER JOIN pg_user_location t2
        ON t2.distributor_id = t1.distributor_id %s

      INNER JOIN pg_usuarios t0
        ON t0.usuario_id = t2.usuario_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

      LEFT JOIN `pg_avances` `t15`
        ON `t15`.user_id = `t2`.`usuario_id` AND `t15`.`entrenamiento_id` != 0

      LEFT JOIN `supply_subsidiary` `t4`
        ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`

      GROUP BY `t0`.`usuario_id`
      ORDER BY `t0`.`usuario_id` ASC
      LIMIT 2000
    ) tblMain
    WHERE 1=1
    %s ;';
  $query = sprintf($sql, $s1, $s2);

  return $query;
}

function genSQLforS($s1, $s2){
  $sql = 'SELECT * FROM
    (
      SELECT `t0`.`usuario_id`, `t0`.`usuario_nombre`, `t0`.`usuario_doc`, `t0`.`usuario_email`
      , t1.subsidiary_id, t1.subsidiary_name, t4.distributor_name
      , SUM(IF(`t15`.`avance` >= 100, 1,0)) AS num_fin
      , IFNULL(GROUP_CONCAT( distinct `t15`.`entrenamiento_id`), "") `num_fin_ids`

      FROM supply_subsidiary t1

      INNER JOIN pg_user_location t2
        ON t2.subsidiary_id = t1.subsidiary_id %s

      INNER JOIN `supply_distributor` `t4`
        ON t4.distributor_id = t1.distributor_id

      INNER JOIN pg_usuarios t0
        ON t0.usuario_id = t2.usuario_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

      LEFT JOIN `pg_avances` `t15`
        ON `t15`.user_id = `t2`.`usuario_id` AND `t15`.`entrenamiento_id` != 0

      GROUP BY t0.usuario_id
    ) tblMain
    WHERE 1=1
    %s ;';
  $query = sprintf($sql, $s1, $s2);

  return $query;
}

function genSQLforE($s1, $s2){
  $sql = 'SELECT * FROM
    (
      SELECT `t0`.`usuario_id`, `t0`.`usuario_nombre`, `t0`.`usuario_doc`, `t0`.`usuario_email`
      , t1.team_id, t1.team_name, t1.distributor_id, t1.subsidiary_id
      , t4.distributor_name, t5.subsidiary_name
      , SUM(IF(`t15`.`avance` >= 100, 1,0)) AS num_fin
      , IFNULL(GROUP_CONCAT( distinct `t15`.`entrenamiento_id`), "") `num_fin_ids`

      FROM supply_team t1

        INNER JOIN `supply_team_list` `t2`
          ON t2.team_id = t1.team_id AND t2.list_type = "Usuario" %s

        LEFT JOIN `supply_distributor` t4
          ON t4.distributor_id = t1.distributor_id

        LEFT JOIN `supply_subsidiary` t5
          ON t5.subsidiary_id = t1.subsidiary_id

        INNER JOIN pg_usuarios t0
          ON t0.usuario_id = t2.list_type_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

        LEFT JOIN `pg_avances` `t15`
          ON `t15`.user_id = t2.list_type_id AND `t15`.`entrenamiento_id` != 0

      GROUP BY t0.usuario_id
    ) tblMain
    WHERE 1=1
    %s ;';
  $query = sprintf($sql, $s1, $s2);

  return $query;
}

function genSQLforAll($s1){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`,
    t1.usuario_perfil, t7.curricula_entrenamientos,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2`   ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4`  ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`
    LEFT JOIN `pg_avances` `t15`        ON `t15`.`user_id`   = `t1`.`usuario_id`
    LEFT JOIN `supply_team_list` t5     ON t5.list_type_id = t1.usuario_id AND t5.list_type = "Usuario"
    LEFT JOIN pg_perfiles_curricula t7  ON t7.perfil_id = t1.usuario_perfil

    WHERE `t1`.`usuario_activo` = "Si"
    AND `t1`.`usuario_rol` != 5

    %s

    GROUP BY `t1`.`usuario_id`
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $s1);

  return $query;
}

function genSQLforAll_v2($s1){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`,
    t1.usuario_perfil, t7.curricula_entrenamientos,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2`   ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4`  ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`
    LEFT JOIN `pg_avances` `t15`        ON `t15`.`user_id`   = `t1`.`usuario_id`
    LEFT JOIN `supply_team_list` t5     ON t5.list_type_id = t1.usuario_id AND t5.list_type = "Usuario"
    LEFT JOIN pg_perfiles_curricula t7  ON t7.perfil_id = t1.usuario_perfil

    WHERE `t1`.`usuario_activo` = "Si"
    AND `t1`.`usuario_rol` != 5

    %s

    GROUP BY `t1`.`usuario_id`
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $s1);

  return $query;
}

function genSQLforEntrenamientos($s1, $s2){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`,
    t1.usuario_perfil,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IF(`t20`.`avance` >= 100, "Finish",
      IF(`t20`.`avance` >= 0 AND `t20`.`avance` < 100, "Activo",
        IF(`t20`.`avance` IS NULL, "Inactivo", "Start")
       )
     ) Estado
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_avances` `t20`        ON t20.user_id = t1.usuario_id
    LEFT JOIN `pg_user_location` `t2`   ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4`  ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`
    LEFT JOIN `supply_team_list` t5     ON t5.list_type_id = t1.usuario_id AND t5.list_type = "Usuario"

    WHERE `t1`.`usuario_activo` = "Si"
    AND `t1`.`usuario_rol` != 5

    %s

    GROUP BY `t1`.`usuario_id`

    %s

    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $s1, $s2);

  return $query;
}

function genSQLforEntrenamientos_v2($s1, $s2, $s3){
  $sql = 'SELECT entrenamiento_id, entrenamiento_nombre, Estado, `tbl1`.`user_id`,
        `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`,
          t1.usuario_perfil, distributor_name, subsidiary_name

          FROM (
            SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
            IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
            IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
             t20.user_id, t20.avance,
              IF(`t20`.`avance` >= 100, "Finish",
              IF(`t20`.`avance` >= 0 AND `t20`.`avance` < 100, "Activo",
                IF(`t20`.`avance` IS NULL, "Inactivo", "Start")
               )
             ) Estado
              FROM `pg_entrenamiento` `t1`

              LEFT JOIN `pg_avances`         `t20`  ON `t20`.`entrenamiento_id` = `t1`.`entrenamiento_id`
              INNER JOIN `pg_user_location`   `t2`  ON `t2`.`usuario_id`        = `t20`.`user_id`
              LEFT  JOIN `supply_distributor` `t3`  ON `t3`.`distributor_id`    = `t2`.`distributor_id`
              LEFT  JOIN `supply_subsidiary`  `t4`  ON `t4`.`subsidiary_id`     = `t2`.`subsidiary_id`

              LEFT JOIN (
                SELECT * FROM `supply_team_list` WHERE list_type = "Usuario" GROUP by list_type_id ORDER BY list_id ASC
              ) t5      ON t5.list_type_id = t20.user_id

              WHERE t1.entrenamiento_activo = "Si"
                %s
              ORDER BY `t1`.`entrenamiento_id` ASC, Estado ASC
          )  tbl1

      LEFT JOIN `pg_usuarios` `t1` ON `t1`.usuario_id = tbl1.user_id

      WHERE %s
      %s
      LIMIT 2000 ;';
  $query = sprintf($sql, $s3, $s1, $s2);

  return $query;

}

function genSQLforTema($tema_id, $filter, $filter_search){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`
#    t17.tema_id `Start`, t18.tema_id `Finish`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2`   ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4`  ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`
    LEFT JOIN `pg_avances` `t15`        ON `t15`.`user_id`   = `t1`.`usuario_id`
    LEFT JOIN `supply_team_list` t5     ON t5.list_type_id = t1.usuario_id AND t5.list_type = "Usuario"

    LEFT JOIN `pg_enroll` `t17`
      ON t17.tema_id = %d AND t17.master_tbl = "pg_temas" AND `t17`.`status` = "Start" AND t17.user_id = t1.usuario_id

    LEFT JOIN `pg_enroll` `t18`
      ON t18.tema_id = %d AND t18.master_tbl = "pg_temas" AND `t18`.`status` = "Finish" AND t18.user_id = t1.usuario_id

    WHERE `t1`.`usuario_activo` = "Si"
    AND `t1`.`usuario_rol` != 5
#    AND ( (t17.tema_id IS NOT NULL) OR (t18.tema_id IS NOT NULL) )
#    AND t17.tema_id IS NOT NULL
#    AND t18.tema_id IS NOT NULL
    %s

    #AND `t3`.`distributor_id` = 26
    %s

    GROUP BY `t1`.`usuario_id`
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000 ;';
    $query = sprintf($sql, $tema_id, $tema_id, $filter, $filter_search);

    return $query;
}

function getUsuariosByType($rol, $db){
  //$active = ($rol === ROL_NEW) ? ' AND `t1`.`usuario_activo` = "%s" ' : '';
  //$query2 = sprintf($active, 'No');
  $query2 = '';

  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`,
    IFNULL(`t7`.`rel_id`, -1) `perfil_sup`,
    IFNULL(`t8`.`supervisores`, -1) `supervisores`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    LEFT JOIN `pg_supervisor` `t7` ON `t7`.`usuario_id` = `t1`.`usuario_id` AND `t7`.`rel_name` = "perfil_sup"
    LEFT JOIN `pg_jefes_sup` `t8` ON `t8`.`usuario_id` = `t1`.`usuario_id`
    WHERE `t1`.`usuario_rol` = "%s"
    %s
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $rol, $query2);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosByPerfil($perfil, $db){
  //$active = ($rol === ROL_NEW) ? ' AND `t1`.`usuario_activo` = "%s" ' : '';
  //$query2 = sprintf($active, 'No');
  $query2 = '';

  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`,
    IFNULL(`t7`.`rel_id`, -1) `perfil_sup`,
    IFNULL(`t8`.`supervisores`, -1) `supervisores`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    LEFT JOIN `pg_supervisor` `t7` ON `t7`.`usuario_id` = `t1`.`usuario_id` AND `t7`.`rel_name` = "perfil_sup"
    LEFT JOIN `pg_jefes_sup` `t8` ON `t8`.`usuario_id` = `t1`.`usuario_id`
    WHERE `t1`.`usuario_perfil` = %d
    AND `t1`.`usuario_rol` != 5
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $perfil);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosByFilter($filter, $db){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`,
    IFNULL(`t7`.`rel_id`, -1) `perfil_sup`,
    IFNULL(`t8`.`supervisores`, -1) `supervisores`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    LEFT JOIN `pg_supervisor` `t7` ON `t7`.`usuario_id` = `t1`.`usuario_id` AND `t7`.`rel_name` = "perfil_sup"
    LEFT JOIN `pg_jefes_sup` `t8` ON `t8`.`usuario_id` = `t1`.`usuario_id`
    WHERE `t1`.`usuario_id` IN ( %s )

    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $filter);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosBySupervisor($supervisor, $db){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    WHERE `t1`.`supervisor_id` = "%s"
    AND `t1`.`usuario_activo` = "Si"
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $supervisor);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUserListBySupervisor($supervisor, $db){
  $sql = 'SELECT `t1`.`usuario_id`
    FROM `pg_usuarios` `t1`
    WHERE `t1`.`supervisor_id` = "%s"
    AND `t1`.`usuario_activo` = "Si"
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $supervisor);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosBySearch($var, $db){
  $nom = strtolower(htmlentities($var['nom'], ENT_QUOTES));
  $doc = htmlentities($var['doc'], ENT_QUOTES);
  $dis = intval($var['distro']);
  $suc = intval($var['ciudad']);
  $sex = htmlentities($var['sexo'], ENT_QUOTES);

  $nombre   = ( strlen($nom)>2 ) ? ' AND `t1`.`usuario_nombre` LIKE "%'. $nom. '%" ' : '';
  $document = ( strlen($doc)>2 ) ? ' AND `t1`.`usuario_doc`    = "'. $doc. '" '      : '';
  $distro   = ( $dis != -1 ) ? ' AND `t2`.`distributor_id` = "'. $dis. '" '          : '';
  $sucursal = ( $suc != -1 ) ? ' AND `t2`.`subsidiary_id`  = "'. $suc. '" '          : '';
  $genero   = ( $sex != 'Cualquiera') ? ' AND `t1`.`usuario_genero` = "'. $sex. '" ' : '';
  $query2   = $nombre. $document. $distro. $sucursal. $genero;

  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`,
    IFNULL(`t7`.`rel_id`, -1) `perfil_sup`,
    IFNULL(`t8`.`supervisores`, -1) `supervisores`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    LEFT JOIN `pg_supervisor` `t7` ON `t7`.`usuario_id` = `t1`.`usuario_id` AND `t7`.`rel_name` = "perfil_sup"
    LEFT JOIN `pg_jefes_sup` `t8` ON `t8`.`usuario_id` = `t1`.`usuario_id`
    WHERE `t1`.`usuario_activo` = "Si"
    %s
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $query2);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosBySearchSmall($var, $filter, $db){
  $nom = strtolower(htmlentities($var['nom'], ENT_QUOTES));
  $doc = htmlentities($var['doc'], ENT_QUOTES);

  $nombre   = ( strlen($nom)>2 ) ? ' AND `t1`.`usuario_nombre` LIKE "%'. $nom. '%" ' : '';
  $document = ( strlen($doc)>2 ) ? ' AND `t1`.`usuario_doc`    = "'. $doc. '" '      : '';

  $query2   = $nombre. $document;

  // filter usuarios by jefe / sup
  $filter1 = '';
  if ($filter !== '') {
    $filter1 = ' AND t1.usuario_id IN ('. $filter. ') ';
  }

  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    WHERE `t1`.`usuario_activo` = "Si"
    %s
    %s
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $query2, $filter1);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosBySearchBySupervisor($var, $supervisor, $db){
  $nom = strtolower(htmlentities($var['nom'], ENT_QUOTES));
  $doc = htmlentities($var['doc'], ENT_QUOTES);
  $dis = intval($var['distro']);
  $suc = intval($var['ciudad']);
  $sex = htmlentities($var['sexo'], ENT_QUOTES);

  $nombre   = ( strlen($nom)>2 ) ? ' AND `t1`.`usuario_nombre` LIKE "%'. $nom. '%" ' : '';
  $document = ( strlen($doc)>2 ) ? ' AND `t1`.`usuario_doc`    = "'. $doc. '" '      : '';
  $distro   = ( $dis != -1 ) ? ' AND `t2`.`distributor_id` = "'. $dis. '" '          : '';
  $sucursal = ( $suc != -1 ) ? ' AND `t2`.`subsidiary_id`  = "'. $suc. '" '          : '';
  $genero   = ( $sex != 'Cualquiera') ? ' AND `t1`.`usuario_genero` = "'. $sex. '" ' : '';
  $query2   = $nombre. $document. $distro. $sucursal. $genero;

  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    WHERE `t1`.`usuario_activo` = "Si"
    AND `t1`.`supervisor_id` = "%s"
    %s
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $supervisor, $query2);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEquiposBySupervisor($supervisor_id, $db){
  $sql = 'SELECT IFNULL(GROUP_CONCAT( distinct `team_id`), -1) `team_ids`
    FROM `supply_team`
    WHERE `supervisor_id` = %d
    LIMIT 2000; ';
  $query = sprintf($sql, $supervisor_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['team_ids'] ) : -1;
  return $rpta;
}

function getEquiposAllByJefe($supervisores, $db){
  $sql = 'SELECT IFNULL(GROUP_CONCAT( distinct `team_id`), 0) `team_ids`
    FROM `supply_team`
    WHERE `supervisor_id` IN ( %s )
    LIMIT 2000; ';
  $query = sprintf($sql, $supervisores);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['team_ids'] ) : "";
  return $rpta;
}

function getUsuariosAllTeamBySupervisor($lista_usuarios, $db){
  $sql = 'SELECT IFNULL(GROUP_CONCAT( distinct `list_type_id`), 0) `lista_usuarios`
    FROM `supply_team_list`
    WHERE `team_id` IN ( %s )
    AND `list_type` = "Usuario"
    LIMIT 2000; ';
  $query = sprintf($sql, $lista_usuarios);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['lista_usuarios'] ) : -1;
  return $rpta;
}

function getUsuariosListByTeamOk1($team_id, $db){
  $sql = 'SELECT IFNULL(GROUP_CONCAT( distinct `list_type_id`), "") `lista_usuarios`
    FROM `supply_team_list`
    WHERE `team_id` = %d
    AND `list_type` = "Usuario"
    GROUP BY `team_id`
    LIMIT 2000; ';
  $query = sprintf($sql, $team_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosListByTeamOk2($supervisor_id, $db){
  $sql = 'SELECT IFNULL(GROUP_CONCAT( distinct `usuario_id`), "") `lista_usuarios`
    FROM `pg_usuarios`
    WHERE `supervisor_id` = %d
    AND `usuario_activo` = "Si"
    GROUP BY `supervisor_id`
    LIMIT 2000; ';
  $query = sprintf($sql, $supervisor_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosByTeam2($var, $db){
  $dis = implode($var['Distribuidora'], ',');
  $suc = implode($var['Ciudad'], ',');

  $distro   = ( !empty($dis) ) ? ' AND `t2`.`distributor_id` IN ('. $dis. ') '   :  '';
  $union    = ( !empty($dis) ) ? ' OR ' : ' AND ';
  $sucursal = ( !empty($suc) ) ? ' `t2`.`subsidiary_id`  IN ('. $suc. ') '   :  '';
  $query2   = $distro. $union. $sucursal;

  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`, `t1`.`usuario_perfil_nivel`
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    WHERE `t1`.`usuario_activo` = "Si"
    %s
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $query2);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUserById($code, $db){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`, `t1`.`usuario_activo`, `t1`.`usuario_fch_nac`, `t1`.`usuario_telefono`, `t1`.`usuario_genero`, `t1`.`usuario_picture`, `t1`.`cargo`, `t1`.`tipo_vendedor`, `t1`.`supervisor_id`, `t1`.`usuario_perfil`, `t1`.`usuario_perfil_nivel`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    WHERE `t1`.`usuario_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUserByIdFull($code, $db){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_email`, `t1`.`usuario_activo`, `t1`.`usuario_fch_nac`, `t1`.`usuario_telefono`, `t1`.`usuario_genero`, `t1`.`usuario_picture`, `t1`.`cargo`, `t1`.`tipo_vendedor`, `t1`.`supervisor_id`, `t1`.`usuario_perfil`, `t1`.`usuario_perfil_nivel`,
    IFNULL(`t3`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`,
    IFNULL(`t7`.`nivel_name`, "-") `nivel_name`,
    IFNULL(`t7`.`nivel_imagen`, "-") `nivel_imagen`,
    IFNULL(`t8`.`team_id`, -1) `team_id`,
    IFNULL(`t9`.`team_name`, "") `team_name`
    FROM `pg_usuarios` `t1`

    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    LEFT JOIN `pg_perfiles_niveles` `t7` ON `t7`.`nivel_id` = `t1`.`usuario_perfil_nivel`
    LEFT JOIN `supply_team_list` `t8` ON `t8`.`list_type_id` = `t1`.`usuario_id` AND `t8`.`list_type` = "Usuario"
    LEFT JOIN `supply_team` `t9` ON `t9`.`team_id` = `t8`.`team_id`

    WHERE `t1`.`usuario_activo` = "Si"
    AND `t1`.`usuario_id` = %d
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 1; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUserByEmail($email, $db){
  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_activo`, `t1`.`usuario_email`
    FROM `pg_usuarios` `t1`
    WHERE `t1`.`usuario_email` = "%s"
    LIMIT 1; ';
  $query = sprintf($sql, $email);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function generateRandomPasswordOld() {
  $password = '';
  $desired_length = rand(8, 12);

  for($length = 0; $length < $desired_length; $length++) {
    $password .= chr(rand(32, 126));
  }

  return $password;
}

function generateRandomPassword($length = 12) {
    $characters = '23456789abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

function updateUserVerifyCode($user_id, $verifyCode, $db){
  $data = Array (
    'verify_code' => $verifyCode
  );

  $db->where ('usuario_id', $user_id);
  $status = ( $db->update ('pg_usuarios', $data) ) ? MSG_OK : MSG_ERROR;
  $info   = ($status === MSG_ERROR) ? $db->getLastError() : '';

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

function getUserByVerify($email, $verify, $db){
  $sql = 'SELECT `usuario_id`
    FROM `pg_usuarios`
    WHERE `usuario_email` = "%s"
    AND `verify_code` = "%s"
    AND `verify_code` != "-"
    LIMIT 1; ';
  $query = sprintf($sql, $email, $verify);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['usuario_id'] ) : MSG_ERROR;
  return $rpta;
}

function updateUserPass($user_id, $verify, $pass, $db){
  $data = Array (
    'usuario_clave' => $pass,
    'verify_code' => '-'
  );

  $db->where ('usuario_id', $user_id);
  $db->where ('verify_code', $verify);
  $status = ( $db->update ('pg_usuarios', $data) ) ? MSG_OK : MSG_ERROR;
  $info   = ($status === MSG_ERROR) ? $db->getLastError() : '';

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

function newUserRegister($dni, $nombre, $email, $password, $db){
  $data = Array (
    'usuario_doc'    => $dni,
    'usuario_nombre' => $nombre,
    'usuario_email'  => $email,
    'usuario_clave'  => $password,
    'usuario_rol'    => -1,
    'usuario_fch_creacion'  => $db->now()
  );

  $id = $db->insert ('pg_usuarios', $data);
  $status = ($id) ? 'ok' : 'error';
  $info   = ($id) ? $id : $db->getLastError();

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

function newUserRegisterFull($dni, $nombre, $email, $password, $rol, $supervisor, $perfil, $db){
  $data = Array (
    'usuario_doc'    => $dni,
    'usuario_nombre' => $nombre,
    'usuario_email'  => $email,
    'usuario_clave'  => $password,
    'usuario_rol'    => $rol,
    'supervisor_id'  => $supervisor,
    'usuario_perfil' => $perfil,
    'usuario_activo' => 'Si',
    'usuario_fch_creacion'  => $db->now()
  );

  $id = $db->insert ('pg_usuarios', $data);
  $status = ($id) ? MSG_OK : MSG_ERROR;
  $info   = ($id) ? $id : $db->getLastError();

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

function updateActivateUser($id, $activate, $db){
  $data = Array (
    'usuario_activo' => $activate
  );

  $db->where ('usuario_id', $id);
  $status = ( $db->update ('pg_usuarios', $data) ) ? MSG_OK : MSG_ERROR;
  $info   = ( $status === MSG_ERROR ) ? $db->getLastError() : '';

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

function updateUserPerfil($id, $nombre, $email, $tel, $fchNac, $genero, $db){
  $data = Array (
    'usuario_nombre' => $nombre,
    'usuario_email'  => $email,
    'usuario_telefono' => $tel,
    'usuario_fch_nac'  => $fchNac,
    'usuario_genero'   => $genero
  );

  $db->where ('usuario_id', $id);
  $rpta  = ( $db->update ('pg_usuarios', $data) ) ? 2 : -1;

  $result = array('status' => $rpta);
  return $result;
}

function updateUserPicture($id, $filename, $db){
  $data = Array (
    'usuario_picture' => $filename
  );

  $db->where ('usuario_id', $id);
  $rpta  = ( $db->update ('pg_usuarios', $data) ) ? 2 : -1;

  $result = array('status' => $rpta);
  return $result;
}

function delUser($code, $db){
  $db->where('usuario_id', $code);
  $rpta = ( $db->delete('pg_usuarios') ) ? 'ok' : 'error';
  $info = $db->getLastError();

  $result = array('status' => $rpta, 'info' => $info);
  return $result;
}

function setUserRol($id, $rol, $db){
  $data = Array (
    'usuario_rol' => $rol
  );

  $db->where ('usuario_id', $id);
  $rpta  = ( $db->update ('pg_usuarios', $data) ) ? 'ok' : 'error';

  $result = array('status' => $rpta);
  return $result;
}

function setUserPerfil($id, $perfil, $db){
  $data = Array (
    'usuario_perfil' => $perfil
  );

  $db->where ('usuario_id', $id);
  $rpta  = ( $db->update ('pg_usuarios', $data) ) ? 'ok' : 'error';

  $result = array('status' => $rpta);
  return $result;
}

function setUserSupervisor($id, $super, $db){
  $data = Array (
    'supervisor_id' => $super
  );

  $db->where ('usuario_id', $id);
  $rpta  = ( $db->update ('pg_usuarios', $data) ) ? 'ok' : 'error';

  $result = array('status' => $rpta);
  return $result;
}

function saveChangePass($id, $pass, $db){
  $data = Array (
    'usuario_clave' => $pass
  );

  $db->where ('usuario_id', $id);
  $rpta  = ( $db->update ('pg_usuarios', $data) ) ? MSG_OK : MSG_ERROR;

  $result = array('status' => $rpta);
  return $result;
}

function setUserLaboral($code, $cargo, $tVendedor, $supervisor, $db){
  $data = Array (
    'cargo' => $cargo,
    'tipo_vendedor' => $tVendedor,
    'supervisor_id' => $supervisor
  );

  $db->where ('usuario_id', $code);
  $rpta  = ( $db->update ('pg_usuarios', $data) ) ? 'ok' : 'error';

  $result = array('status' => $rpta);
  return $result;
}

/**
  Trofeos
**/

function calcularTrofeo($trofeo_aciertos){
  $rpta = 'No';
  switch (true) {
    case ($trofeo_aciertos > 90):
      $rpta = 'Oro';
      break;
    case ($trofeo_aciertos > 70):
      $rpta = 'Plata';
      break;
    case ($trofeo_aciertos > 0):
      $rpta = 'Bronce';
      break;
  }
  return $rpta;
}

function getTrofeosByUser($user_id, $db){
  $sql = 'SELECT IFNULL(`t1`.`trofeo_id`, "-") `trofeo_id`,
    `t1`.`user_id`, `t1`.`trofeo_tipo`, `t1`.`trofeo_aciertos`, `t1`.`entrenamiento_id`,
    IFNULL( CONVERT_TZ(MAX(`t1`.`trofeo_fch_reg`),"+00:00","-05:00"), "-" ) `trofeo_fch_reg`,
    IFNULL(`t2`.`entrenamiento_nombre`, "-") `entrenamiento_nombre`,
    IFNULL(`t2`.`entrenamiento_t_oro`, "-") `trofeo_oro`,
    IFNULL(`t2`.`entrenamiento_t_plata`, "-") `trofeo_plata`,
    IFNULL(`t2`.`entrenamiento_t_bronce`, "-") `trofeo_bronce`
    FROM `pg_trofeos` `t1`
    LEFT JOIN `pg_entrenamiento` `t2` ON `t2`.`entrenamiento_id` = `t1`.`entrenamiento_id`
    WHERE `t1`.`user_id` = %d
    ORDER BY `t1`.`trofeo_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $user_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getTrofeos($code = 0, $db){
  $sql = 'SELECT `trofeo_id`, `user_id`, `trofeo_tipo`, `trofeo_aciertos`, `entrenamiento_id`, `trofeo_fch_reg`
    FROM `pg_trofeos`
    WHERE `entrenamiento_id` = %d
    ORDER BY `curso_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getTrofeoById($code, $db){
  $sql = 'SELECT `trofeo_id`, `user_id`, `trofeo_tipo`, `trofeo_aciertos`, `entrenamiento_id`, `trofeo_fch_reg`
    FROM `pg_trofeos`
    WHERE `trofeo_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newTrofeo($user_id, $trofeo_aciertos, $entrenamiento_id, $db){
  $trofeo_tipo = calcularTrofeo($trofeo_aciertos);

  $data = Array (
    'user_id'          => $user_id,
    'trofeo_tipo'      => $trofeo_tipo,
    'trofeo_aciertos'  => $trofeo_aciertos,
    'entrenamiento_id' => $entrenamiento_id,
    'trofeo_fch_reg'   => $db->now()
  );

  $id = $db->insert ('pg_trofeos', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function getAciertoByEnt($user_id, $entrenamiento_id, $db){
  $sql = 'SELECT ROUND( IFNULL( AVG(NULLIF(`puntos_aciertos`, 0))*100, 0 ), 2) `puntos_aciertos`
    FROM `pg_puntos`
    WHERE `user_id` = %d
    AND `hito_id` NOT IN (1,5,13,14)
    AND `puntos_estado` = "Ganado"
    AND `entrenamiento_id` = %d ;';
  $query = sprintf($sql, $user_id, $entrenamiento_id);
  $result = $db->rawQuery($query);

  $rpta = 0;
  if ( count($result)>0 ){
    $rpta = $result[0]['puntos_aciertos'];
  }

  return $rpta;
}

function updateTrofeo($code, $user_id, $trofeo_aciertos, $entrenamiento_id, $db){
  $trofeo_tipo = calcularTrofeo($trofeo_aciertos);

  $data = Array (
    'user_id'          => $user_id,
    'trofeo_tipo'      => $trofeo_tipo,
    'trofeo_aciertos'  => $trofeo_aciertos,
    'entrenamiento_id' => $entrenamiento_id,
    'trofeo_fch_reg'   => $db->now()
  );

  $db->where ('trofeo_id', $code);

  $rpta  = ( $db->update ('pg_trofeos', $data) ) ? 2 : -1;
  return $rpta;
}

function delTrofeo($code, $db){
  $db->where('trofeo_id', $code);

  $rpta = ( $db->delete('pg_trofeos') ) ? 2 : -1;
  return $rpta;
}

/**
  Puntos
**/

function newAccion($user_id, $hito_id, $puntos_valor, $puntos_estado, $porcentaje, $entrenamiento_id, $curso_id, $tema_id, $master_tabla, $master_id, $aciertos, $db){

  $data = Array (
    'user_id'             => $user_id,
    'hito_id'             => $hito_id,
    'puntos_valor'        => $puntos_valor,
    'puntos_estado'       => $puntos_estado,
    'puntos_fch_reg'      => $db->now(),
    'puntos_porcentaje'   => $porcentaje,
    'entrenamiento_id'    => $entrenamiento_id,
    'curso_id'            => $curso_id,
    'tema_id'             => $tema_id,
    'puntos_master_tabla' => $master_tabla,
    'puntos_master_id'    => $master_id,
    'puntos_aciertos'     => $aciertos
  );

  $id = $db->insert ('pg_puntos', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function isActionDuply($user_id, $hito_id, $master_tabla, $master_id, $db){
  $sql = 'SELECT COUNT(`puntos_id`) AS `n`
    FROM `pg_puntos`
    WHERE `user_id` = %d
    AND `hito_id` = %d
    AND `puntos_master_tabla` = "%s"
    AND `puntos_master_id` = %d
    LIMIT 1 ;';
  $query = sprintf($sql, $user_id, $hito_id, $master_tabla, $master_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['n'] ) : 0;
  return $rpta;
}

function checkMatricula($user_id, $pid, $db){
  $sql = 'SELECT COUNT(`puntos_id`) AS `n`
    FROM `pg_puntos`
    WHERE
  ';
}

function getSumaPuntos($user_id, $estado, $db){
  $sql = 'SELECT IFNULL( SUM(`puntos_valor`), 0 ) `puntos_suma`
    FROM `pg_puntos`
    WHERE `user_id` = %d
    AND `puntos_estado` = "%s"
    AND `entrenamiento_id` != 0 AND `tema_id` != 30 ;';
  $query = sprintf($sql, $user_id, $estado);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['puntos_suma'] ) : 0;
  return $rpta;
}

function getSumaPuntosCanjeados($user_id, $db){
  $sql = 'SELECT IFNULL( SUM(`t3`.`puntos_canje`), 0 ) `puntos_canje`
    FROM `pg_canjes` `t3`
    WHERE `t3`.`user_id` = "%s" ';
  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['puntos_canje'] ) : 0;
  return $rpta;
}

function getTotalByDis($distro, $db){
  $sql = 'SELECT *, count(usuario_id) total
    FROM (
    SELECT t1.usuario_id, t1.usuario_doc, t1.usuario_nombre
    FROM `pg_usuarios` `t1`
      INNER JOIN `pg_user_location` t2
          ON t2.usuario_id = `t1`.`usuario_id`
    WHERE t2.distributor_id = %d
    GROUP BY t1.usuario_id
    ) `p`';
  $query = sprintf($sql, $distro);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['total'] ) : 0;
  return $rpta;
}

function getRankByDistribuidora($user_id, $distro, $db){
  $sql = 'SELECT *
    FROM (SELECT *,
      @curRank := @curRank + 1 AS rank
      FROM (
        SELECT t1.usuario_id, t1.usuario_doc, t1.usuario_nombre,
        IFNULL( SUM(`t3`.`puntos_valor`), 0 ) `puntos_suma`,
        ROUND( IFNULL( AVG(NULLIF(`t3`.`puntos_aciertos`, 0))*100, 0 ), 2) `puntos_aciertos`,
        IFNULL( MAX(`t3`.`puntos_valor`), 0 ) `puntos_max`
        FROM `pg_usuarios` `t1`
        INNER JOIN `pg_user_location` t2
            ON t2.usuario_id = `t1`.`usuario_id`
        LEFT JOIN `pg_puntos` `t3`
            ON `t3`.`user_id` = `t1`.`usuario_id` AND `t3`.`puntos_estado` = "Ganado" AND `t3`.`entrenamiento_id` != 0 AND `t3`.`tema_id` != 30
        WHERE t2.distributor_id = %d
        GROUP BY t1.usuario_id
        ORDER BY `puntos_suma` desc, `puntos_aciertos` desc, `puntos_max` desc
      ) `p`, (SELECT @curRank := 0) `r`
    ) `p1`
    WHERE usuario_id = %d; ';
  $query = sprintf($sql, $distro, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['rank'] ) : 0;
  return $rpta;
}

function getUltimoAcceso($user_id, $db){
  $sql = 'SELECT
    IFNULL( CONVERT_TZ(MAX(`fch_reg`),"+00:00","-05:00"), "-" ) `fch_reg`
    FROM `pg_log`
    WHERE `user_id` = %d
    ORDER BY `log_id` DESC
    LIMIT 1 ;';
  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = isset($result[0]) ? $result[0]['fch_reg'] : '-';
  return $rpta;
}

function getTotalAccesos($user_id, $db){
  $sql = 'SELECT
    COUNT(`log_id`) `total`
    FROM `pg_log`
    WHERE `user_id` = %d ;';
  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = isset($result[0]) ? $result[0]['total'] : 0;
  return $rpta;
}

function getResumenByUser($user_id, $db){
  $sql = 'SELECT
    IFNULL( MAX(`puntos_valor`), 0 ) `puntos_max`,
    IFNULL( SUM(`puntos_valor`), 0 ) `puntos_suma`,
    ROUND( IFNULL( AVG(`puntos_valor`), 0 ), 2) `puntos_avg`,
    ROUND( IFNULL( AVG(NULLIF(`puntos_aciertos`, 0))*100, 0 ), 2) `puntos_aciertos`
    FROM `pg_puntos`
    WHERE `user_id` = %d
    AND `puntos_estado` = "Ganado"
    AND `entrenamiento_id` != 0 AND `tema_id` != 30
    GROUP BY `user_id` ;';
  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $null = array('puntos_max' => 0, 'puntos_suma' => 0, 'puntos_avg' => 0, 'puntos_aciertos' => 0);
  $rpta = isset($result[0]) ? $result[0] : $null;
  return $rpta;
}

function getEntrenamientosFin($user_id, $db){
  $sql = 'SELECT SUM(entrenamiento_id) `num_entrenamientos`
    FROM pg_enroll
    WHERE `master_tbl` = "pg_entrenamiento"
    AND `status` = "Finish"
    AND `user_id` = %d ;';
  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ?  $result[0]['num_entrenamientos']  : 0;
  return $rpta;
}

function getEntrenamientosFinOk($user_id, $db){
  $sql = 'SELECT GROUP_CONCAT( distinct `entrenamiento_id`) `entrenamiento_fin`
    FROM `pg_avances`
    WHERE `avance` >= 100
    AND `user_id` = %d
    GROUP BY `user_id` ;';

  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ?  $result[0]['entrenamiento_fin']  : "";
  return $rpta;
}

function getEntrenamientosProgOk($user_id, $db){
  $sql = 'SELECT GROUP_CONCAT( distinct `entrenamiento_id`) `entrenamiento_fin`
    FROM `pg_avances`
    WHERE `avance` >= 0 AND `avance` < 100
    AND `user_id` = %d
    GROUP BY `user_id` ;';

  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ?  $result[0]['entrenamiento_fin']  : "";
  return $rpta;
}


function getEntrenamientoByUserByPerfil($user_id, $filtro, $db){
  //COUNT( DISTINCT DATE(t5.puntos_fch_reg) ) `numero_ingresos`
  $sql = 'SELECT *, IFNULL(t6.trofeo_tipo, "-") `trofeo_tipo`
    FROM (
    SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`, `t1`.`max_puntaje`,
    IFNULL(`t4`.`avance`, 0) `avance`,
    IFNULL(`t4`.`user_id`, 0) user_codigo,
    IFNULL(SUM(t5.puntos_valor), 0) `puntos`,
    ROUND( IFNULL( AVG(NULLIF(`puntos_aciertos`, 0))*100, 0 ), 2) `puntos_aciertos`,
    IFNULL( CONVERT_TZ(MAX(t5.puntos_fch_reg),"+00:00","-05:00"), "-" ) `ingreso`

    FROM `pg_entrenamiento` `t1`
    LEFT JOIN `pg_avances` t4
      ON t4.entrenamiento_id = t1.entrenamiento_id AND t4.user_id = %d
    LEFT JOIN `pg_puntos` `t5`
      ON `t5`.`entrenamiento_id` = `t1`.`entrenamiento_id` AND t5.user_id = %d AND `t5`.`puntos_estado` = "Ganado"

    GROUP BY `t1`.`entrenamiento_id`

    HAVING `user_codigo` = %d OR `user_codigo` = 0

    AND `t1`.`entrenamiento_id` IN ( %s )

    ORDER BY `t1`.`entrenamiento_id` ASC
    ) tbl
    LEFT JOIN `pg_trofeos` `t6`
      ON `t6`.`entrenamiento_id` = `tbl`.`entrenamiento_id`

    GROUP BY `tbl`.`entrenamiento_id` ;';
  $query = sprintf($sql, $user_id, $user_id, $user_id, $filtro);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoByUser($user_id, $db){
  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    IFNULL(`t4`.`avance`, 0) `avance`,
    IFNULL(`t4`.`user_id`, 0) user_codigo,
    IFNULL(SUM(t5.puntos_valor), 0) `puntos`,
    IFNULL( CONVERT_TZ(MAX(t5.puntos_fch_reg),"+00:00","-05:00"), "-" ) `ingreso`,
    COUNT( DISTINCT DATE(t5.puntos_fch_reg) ) `numero_ingresos`

    FROM `pg_entrenamiento` `t1`
    LEFT JOIN `pg_avances` t4
      ON t4.entrenamiento_id = t1.entrenamiento_id AND t4.user_id = %d
    LEFT JOIN `pg_puntos` `t5`
      ON `t5`.`entrenamiento_id` = `t1`.`entrenamiento_id` AND t5.user_id = %d AND `t5`.`puntos_estado` = "Ganado"

    GROUP BY `t1`.`entrenamiento_id`

    HAVING `user_codigo` = %d OR `user_codigo` = 0

    ORDER BY `t1`.`entrenamiento_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $user_id, $user_id, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRpt($filter, $perfil_filtro, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' WHERE t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filter1 = ''; $filter2 = '';
  if ($filter !== '') {
    $filter1 = ' AND t16.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t15.user_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    COUNT( distinct `t16`.`user_id`) `start`,
    IFNULL(GROUP_CONCAT( distinct `t16`.`user_id`), "") `start_ids`,
    COUNT( distinct `t15`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t15`.`user_id`), "") `finish_ids`,
    (COUNT( distinct `t16`.`user_id`) + COUNT( distinct `t15`.`user_id`)) `activos`

    FROM `pg_entrenamiento` `t1`

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    %s

    GROUP BY `t1`.`entrenamiento_id`
    ORDER BY `t1`.`entrenamiento_id` ASC ;';
  $query = sprintf($sql, $filter1, $filter2, $filter5);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptFull($filter, $perfil_filtro, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' WHERE t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filter1 = ''; $filter2 = '';
  if ($filter !== '') {
    $filter1 = ' AND t16.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t15.user_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT * FROM (
  SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    COUNT( distinct `t16`.`user_id`) `start`,
    IFNULL(GROUP_CONCAT( distinct `t16`.`user_id`), "") `start_ids`,
    COUNT( distinct `t15`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t15`.`user_id`), "") `finish_ids`,
    (COUNT( distinct `t16`.`user_id`) + COUNT( distinct `t15`.`user_id`)) `activos`,

   IFNULL(GROUP_CONCAT( distinct `t0`.`usuario_perfil`), "") `perfiles`

    FROM `pg_entrenamiento` `t1`

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    INNER JOIN pg_usuarios t0
      ON t0.usuario_id = t15.user_id

    GROUP BY `t1`.`entrenamiento_id`
    ORDER BY `t1`.`entrenamiento_id` ASC
  ) tbl1

  INNER JOIN (
    SELECT `t1`.`usuario_perfil`,
    COUNT( distinct `t1`.`usuario_id`) `usuarios_full`,
    t7.curricula_entrenamientos

    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`

    INNER JOIN pg_perfiles_curricula t7
      ON t7.perfil_id = t1.usuario_perfil

    WHERE `t1`.`usuario_rol` != 5 AND `t1`.`usuario_activo` = "Si"

   GROUP BY t1.usuario_perfil
  ) tbl2
    ON  FIND_IN_SET (tbl2.usuario_perfil, tbl1.perfiles) ;';
  $query = sprintf($sql, $filter1, $filter2);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptFullOk_old($filter, $perfil_filtro, $filtro_search, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' AND t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filter1 = ''; $filter2 = '';
  if ($filter !== '') {
    $filter1 = ' AND t16.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t15.user_id IN ('. $filter. ') ';
  }

  $filterS = '';
  if ($filtro_search !== '') {
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t1`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterS = $equipo. $perfil. $distro. $sucursal;
  }

  $sql = 'SELECT * FROM (
  SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    COUNT( distinct `t16`.`user_id`) `start`,
    IFNULL(GROUP_CONCAT( distinct `t16`.`user_id`), "") `start_ids`,
    COUNT( distinct `t15`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t15`.`user_id`), "") `finish_ids`,
    (COUNT( distinct `t16`.`user_id`) + COUNT( distinct `t15`.`user_id`)) `activos`,

   IFNULL(GROUP_CONCAT( distinct `t0`.`usuario_perfil`), "") `perfiles`

    FROM `pg_entrenamiento` `t1`

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    INNER JOIN pg_usuarios t0
      ON ( (t0.usuario_id = t15.user_id) or (t0.usuario_id = t16.user_id) )

    WHERE 1=1
    %s
    %s
    %s

    GROUP BY `t1`.`entrenamiento_id`
    ORDER BY `t1`.`entrenamiento_id` ASC
  ) tbl1

  INNER JOIN (
    SELECT `t1`.`usuario_perfil`,
    COUNT( distinct `t1`.`usuario_id`) `usuarios_full`,
    t7.curricula_entrenamientos

    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `supply_team_list` `t5` ON `t5`.`list_type_id` = `t1`.`usuario_id` AND `t5`.`list_type` = "Usuario"

    INNER JOIN pg_perfiles_curricula t7
      ON t7.perfil_id = t1.usuario_perfil

    WHERE `t1`.`usuario_rol` != 5 AND `t1`.`usuario_activo` = "Si"
    %s

    GROUP BY t1.usuario_perfil
  ) tbl2
    ON  FIND_IN_SET (tbl2.usuario_perfil, tbl1.perfiles) ;';
  $query = sprintf($sql, $filter1, $filter2, $filter5, $filterS);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptFullOk_old2($filter, $perfil_filtro, $filtro_search, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' AND t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filter1 = ''; $filter2 = '';
  if ($filter !== '') {
    $filter1 = ' AND t16.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t15.user_id IN ('. $filter. ') ';
  }

  $filterS = '';
  if ($filtro_search !== '') {
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t0`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterS = $equipo. $perfil. $distro. $sucursal;
  }

  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    COUNT( distinct `t16`.`user_id`) `start`,
    COUNT( distinct `t15`.`user_id`) `finish`,
    ( COUNT( distinct `t16`.`user_id`) + COUNT( distinct `t15`.`user_id`) ) `activos`,

   IFNULL(GROUP_CONCAT( distinct `t0`.`usuario_perfil`), "") `perfiles`

    FROM `pg_entrenamiento` `t1`

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.entrenamiento_id = `t1`.`entrenamiento_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    INNER JOIN pg_usuarios t0
      ON ( ( (t0.usuario_id = t15.user_id) or (t0.usuario_id = t16.user_id) ) AND `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si" )

    INNER JOIN pg_user_location t2
      ON t0.usuario_id = t2.usuario_id

    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4`  ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`
    LEFT JOIN `supply_team_list` t5     ON t5.list_type_id = t0.usuario_id AND t5.list_type = "Usuario"

    WHERE 1=1
    %s
    %s
    %s
    %s

    GROUP BY `t1`.`entrenamiento_id`
    ORDER BY `t1`.`entrenamiento_id` ASC ;';
  $query = sprintf($sql, $filter1, $filter2, $filter5, $filterS);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptFullOk($filter, $perfil_filtro, $filtro_search, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' AND t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filterA = '';
  if ($filter !== '') {
    $filter1 = ' AND t16.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t15.user_id IN ('. $filter. ') ';
    $filterA = $filter1. $filter2;
  }

  $filterS = '';
  if ($filtro_search !== '') {
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t0`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterS = $equipo. $perfil. $distro. $sucursal;
  }

  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    COUNT( distinct `t16`.`user_id`) `start`,
    COUNT( distinct `t15`.`user_id`) `finish`,
    ( COUNT( distinct `t16`.`user_id`) + COUNT( distinct `t15`.`user_id`) ) `activos`,
    IFNULL(GROUP_CONCAT( distinct `t0`.`usuario_perfil`), "") `perfiles`

    FROM `pg_entrenamiento` `t1`

    LEFT JOIN
    (
      SELECT tx0.*, t7.curricula_entrenamientos FROM pg_usuarios tx0

        INNER JOIN pg_user_location t2
          ON tx0.usuario_id = t2.usuario_id

        LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
        LEFT JOIN `supply_subsidiary` `t4`  ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`
        LEFT JOIN `supply_team_list` t5     ON t5.list_type_id = tx0.usuario_id AND t5.list_type = "Usuario"
        LEFT JOIN pg_perfiles_curricula t7  ON t7.perfil_id = tx0.usuario_perfil

      WHERE `tx0`.`usuario_rol` != 5
      AND `tx0`.`usuario_activo` = "Si"
      %s

      GROUP BY `tx0`.`usuario_id`

    ) t0
    ON  FIND_IN_SET (`t1`.`entrenamiento_id`, t0.curricula_entrenamientos)

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.user_id = `t0`.`usuario_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.user_id = `t0`.`usuario_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    WHERE t1.entrenamiento_activo = "Si"
    %s
    %s

    GROUP BY `t1`.`entrenamiento_id`
    ORDER BY `t1`.`entrenamiento_id` ASC ;';
  $query = sprintf($sql, $filterS, $filterA, $filter5);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptFullv1($filter, $perfil_filtro, $filtro_search, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' AND t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filterA = '';
  if ($filter !== '') {
    $filter1 = ' AND t16.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t15.user_id IN ('. $filter. ') ';
    $filterA = $filter1. $filter2;
  }

  $filterS = '';
  if ($filtro_search !== '') {
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t0`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterS = $equipo. $perfil. $distro. $sucursal;
  }

  $sql = 'SELECT entrenamiento_id, entrenamiento_nombre, Estado,
    COUNT( distinct `user_id`) `Numero`

    FROM (
      SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
       t20.user_id, t20.avance,
        IF(`t20`.`avance` >= 100, "Finish",
        IF(`t20`.`avance` >= 0 AND `t20`.`avance` < 100, "Activo",
          IF(`t20`.`avance` IS NULL, "Inactivo", "Start")
         )
       ) `Estado`
        FROM `pg_entrenamiento` `t1`

        LEFT JOIN `pg_avances` `t20`
          ON `t20`.`entrenamiento_id` = `t1`.`entrenamiento_id`

        WHERE `t1`.`entrenamiento_activo` = "Si"
        ORDER BY `t1`.`entrenamiento_id` ASC, `Estado` ASC
    )  tbl1

  GROUP BY `entrenamiento_id`, `Estado` ;';
  //$query = sprintf($sql, $filterS, $filterA, $filter5);
  $query = $sql;

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptFullv2($filter, $perfil_filtro, $filtro_search, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' AND t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filterA = '';
  if ($filter !== '') {
    $filterA = ' AND t20.user_id IN ('. $filter. ') ';
  }

  $filterS = '';
  if ($filtro_search !== '') {
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t0`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterS = $equipo. $perfil. $distro. $sucursal;
  }

  $sql = 'SELECT entrenamiento_id, entrenamiento_nombre, Estado,
    sum(if(Estado = "Finish", 1, 0))  nFinish,
    sum(if(Estado = "Activo", 1, 0))  nActivo,
    sum(if(Estado = "Start", 1, 0))     nStart,
    sum(if(Estado = "Inactivo", 1, 0))  nInactivo

    FROM (
      SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
       t20.user_id, t20.avance,
        IF(`t20`.`avance` >= 100, "Finish",
        IF(`t20`.`avance` >= 0 AND `t20`.`avance` < 100, "Activo",
          IF(`t20`.`avance` IS NULL, "Inactivo", "Start")
         )
       ) Estado
        FROM `pg_entrenamiento` `t1`

        LEFT JOIN `pg_avances`         `t20`  ON `t20`.`entrenamiento_id` = `t1`.`entrenamiento_id`
        INNER JOIN `pg_user_location`   `t2`  ON `t2`.`usuario_id`        = `t20`.`user_id`
        LEFT  JOIN `supply_distributor` `t3`  ON `t3`.`distributor_id`    = `t2`.`distributor_id`
        LEFT  JOIN `supply_subsidiary`  `t4`  ON `t4`.`subsidiary_id`     = `t2`.`subsidiary_id`

        LEFT JOIN (
          SELECT * FROM `supply_team_list` WHERE list_type = "Usuario" GROUP by list_type_id ORDER BY list_id ASC
        ) t5      ON t5.list_type_id = t20.user_id

        WHERE t1.entrenamiento_activo = "Si"
          %s
          %s
          %s
        ORDER BY `t1`.`entrenamiento_id` ASC, Estado ASC
    )  tbl1

    GROUP BY `entrenamiento_id` ;';
  $query = sprintf($sql, $filterS, $filterA, $filter5);
  //$query = $sql;

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoTemasRpt($filter, $perfil_filtro, $filtro_search, $curso, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' AND t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filterA = '';
  if ($filter !== '') {
    $filter1 = ' AND t17.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t18.user_id IN ('. $filter. ') ';
    $filterA = $filter1. $filter2;
  }

  $filterS = '';
  if ($filtro_search !== '') {
    $equ = intval($filtro_search['equipo']);
    $per = intval($filtro_search['perfil']);
    $dic = intval($filtro_search['distro']);
    $suc = intval($filtro_search['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t5`.`team_id`         = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t0`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t3`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t4`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterS = $equipo. $perfil. $distro. $sucursal;
  }
  $sql = 'SELECT `t1`.`tema_id`, `t1`.`tema_nombre`,
    COUNT( distinct `t17`.`user_id`) `activos`,
    COUNT( distinct `t18`.`user_id`) `finish`,
    ( COUNT( distinct `t17`.`user_id`) - COUNT( distinct `t18`.`user_id`) ) `start`

    FROM `pg_temas` `t1`

    INNER JOIN
    (
      SELECT tx0.* FROM pg_usuarios tx0

        INNER JOIN pg_user_location t2
          ON tx0.usuario_id = t2.usuario_id

        LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
        LEFT JOIN `supply_subsidiary` `t4`  ON `t4`.`subsidiary_id`  = `t2`.`subsidiary_id`
        LEFT JOIN `supply_team_list` t5     ON t5.list_type_id = tx0.usuario_id AND t5.list_type = "Usuario"

      WHERE `tx0`.`usuario_rol` != 5
      AND `tx0`.`usuario_activo` = "Si"
      %s

      GROUP BY `tx0`.`usuario_id`
    ) t0

      LEFT JOIN `pg_enroll` `t17`
        ON t17.tema_id = t1.tema_id AND t17.master_tbl = "pg_temas" AND `t17`.`status` = "Start" AND t17.user_id = t0.usuario_id

      LEFT JOIN `pg_enroll` `t18`
        ON t18.tema_id = t1.tema_id AND t18.master_tbl = "pg_temas" AND `t18`.`status` = "Finish" AND t18.user_id = t0.usuario_id

    WHERE `t1`.`curso_id` = %d
    %s

    GROUP BY `t1`.`tema_id`
    ORDER BY `t1`.`tema_id` ASC ;';
  $query = sprintf($sql, $filterS, $curso, $filterA);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRpt2($filter, $perfil_filtro, $filtroSearch, $db){
  $filter3 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $perfil_filtro[0]['curricula_entrenamientos'] : -1;
  $filter4 = ' WHERE t1.entrenamiento_id IN ('. $filter3. ') ';
  $filter5 = ( is_array($perfil_filtro) && count($perfil_filtro)>0 ) ? $filter4 : '';

  $filterB = '';
  if ($filtroSearch !== '') {
    $equ = intval($filtroSearch['equipo']);
    $per = intval($filtroSearch['perfil']);
    $dic = intval($filtroSearch['distro']);
    $suc = intval($filtroSearch['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t10`.`team_id`        = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t1`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t6`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t7`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterB = $equipo. $perfil. $distro. $sucursal;
  }

  $filter1 = ''; $filter2 = '';
  if ($filter !== '') {
    $filter1 = ' AND t4.user_id IN ('. $filter. ') ';
    $filter2 = ' AND t15.user_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    COUNT( distinct `t15`.`user_id`) `start`,
    IFNULL(GROUP_CONCAT( distinct `t15`.`user_id`), "") `start_ids`,
    COUNT( distinct `t4`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t4`.`user_id`), "") `finish_ids`,

    IFNULL(`t6`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t7`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t6`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t7`.`subsidiary_name`, "-") `subsidiary_name`

    FROM `pg_entrenamiento` `t1`

    LEFT JOIN `pg_enroll` `t4`
      ON t4.entrenamiento_id = t1.entrenamiento_id AND t4.master_tbl = "pg_entrenamiento" %s

    LEFT JOIN `pg_enroll` `t15`
       ON t15.entrenamiento_id = t1.entrenamiento_id AND t15.master_tbl = "pg_cursos" %s

    LEFT JOIN `pg_user_location` `t5`   ON `t5`.`usuario_id`     = `t15`.`usuario_id`
    LEFT JOIN `supply_distributor` `t6` ON `t6`.`distributor_id` = `t5`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t7`  ON `t7`.`subsidiary_id`  = `t5`.`subsidiary_id`

    %s
    %s

    GROUP BY `t1`.`entrenamiento_id`
    ORDER BY `t1`.`entrenamiento_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $filter1, $filter2, $filter5, $filterB);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getRptEntByDis($user_id, $db){
  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
  COUNT( distinct `t4`.`user_id`) `usuarios`,
  COUNT( t4.avance >= 100) `aprobados`

  FROM `pg_entrenamiento` `t1`

  LEFT JOIN `pg_avances` t4
    ON t4.entrenamiento_id = t1.entrenamiento_id

  LEFT JOIN `pg_puntos` t5
    ON t5.entrenamiento_id = t1.entrenamiento_id

  GROUP BY `t1`.`entrenamiento_id`

  ORDER BY `t1`.`entrenamiento_id` ASC
  LIMIT 2000 ;';
  $query = $sql;

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByCity($filter, $db){
  $filter1 = '';
  if ($filter !== '') {
    $filter1 = ' AND t2.usuario_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT `t1`.`subsidiary_id`, `t1`.`subsidiary_name`,
    t4.distributor_name,

    COUNT(distinct `t2`.`usuario_id`) `usuarios`,
    IFNULL(GROUP_CONCAT( distinct `t2`.`usuario_id`), "") `user_ids`,

    COUNT( distinct `t15`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t15`.`user_id`), "") `finish_ids`,
    COUNT( distinct `t16`.`user_id`) `start`,
    IFNULL(GROUP_CONCAT( distinct `t16`.`user_id`), "") `start_ids`

#AND `t15`.`avance`  IS NULL

    FROM `supply_subsidiary` `t1`

    INNER JOIN `pg_user_location` `t2`
      ON t2.subsidiary_id = t1.subsidiary_id %s

    INNER JOIN pg_usuarios t0
      ON t0.usuario_id = t2.usuario_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

    INNER JOIN `supply_distributor` `t4`
      ON t4.distributor_id = t1.distributor_id

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.user_id = `t2`.`usuario_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.user_id = `t2`.`usuario_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    GROUP BY t1.subsidiary_id
    ORDER BY `t4`.`distributor_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $filter1);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByCity_v2($filter, $db){
  $filter1 = '';
  if ($filter !== '') {
    $filter1 = ' AND t2.usuario_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT *
    , COUNT(distinct `usuario_id`) `usuarios`
    , SUM(IF(`num_fin` = 3, 1,0)) AS `fin_100`
    , SUM(IF(`num_fin` < 3 AND `num_fin` > 0, 1,0)) AS `en_proceso`
    , SUM(IF(`num_fin` = 0, 1,0)) AS `no_empezaron`

    FROM
    (

      SELECT t1.subsidiary_id, t1.subsidiary_name, t4.distributor_name
      , t0.usuario_id
      , SUM(IF(`t15`.`avance` >= 100, 1,0)) AS num_fin
      , IFNULL(GROUP_CONCAT( distinct `t15`.`entrenamiento_id`), "") `num_fin_ids`

          FROM supply_subsidiary t1

          INNER JOIN pg_user_location t2
            ON t2.subsidiary_id = t1.subsidiary_id %s

          INNER JOIN `supply_distributor` `t4`
            ON t4.distributor_id = t1.distributor_id

          INNER JOIN pg_usuarios t0
            ON t0.usuario_id = t2.usuario_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

          LEFT JOIN `pg_avances` `t15`
            ON `t15`.user_id = `t2`.`usuario_id` AND `t15`.`entrenamiento_id` != 0

      GROUP BY t0.usuario_id

    ) tbl_main
    GROUP BY subsidiary_id ;';
  $query = sprintf($sql, $filter1);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByDis($filter, $db){
  $filter1 = '';
  if ($filter !== '') {
    $filter1 = ' AND t2.usuario_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT t1.distributor_id, t1.distributor_name,
    COUNT(distinct `t2`.`usuario_id`) `usuarios`,
    IFNULL(GROUP_CONCAT( distinct `t2`.`usuario_id`), "") `user_ids`,

    COUNT( distinct `t15`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t15`.`user_id`), "") `finish_ids`,
    COUNT( distinct `t16`.`user_id`) `start`,
    IFNULL(GROUP_CONCAT( distinct `t16`.`user_id`), "") `start_ids`

    FROM supply_distributor t1

    INNER JOIN pg_user_location t2
      ON t2.distributor_id = t1.distributor_id %s

    INNER JOIN pg_usuarios t0
      ON t0.usuario_id = t2.usuario_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.user_id = `t2`.`usuario_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.user_id = `t2`.`usuario_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    GROUP BY t1.distributor_id ;';
  $query = sprintf($sql, $filter1);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByDis_v2($filter, $db){
  $filter1 = '';
  if ($filter !== '') {
    $filter1 = ' AND t2.usuario_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT *
    , COUNT(distinct `usuario_id`) `usuarios`
    , SUM(IF(`num_fin` = 3, 1,0)) AS `fin_100`
    , SUM(IF(`num_fin` < 3 AND `num_fin` > 0, 1,0)) AS `en_proceso`
    , SUM(IF(`num_fin` = 0, 1,0)) AS `no_empezaron`

    FROM
    (

      SELECT t1.distributor_id, t1.distributor_name
      , t0.usuario_id
      , SUM(IF(`t15`.`avance` >= 100, 1,0)) AS num_fin
      , IFNULL(GROUP_CONCAT( distinct `t15`.`entrenamiento_id`), "") `num_fin_ids`

          FROM supply_distributor t1

          INNER JOIN pg_user_location t2
            ON t2.distributor_id = t1.distributor_id %s

          INNER JOIN pg_usuarios t0
            ON t0.usuario_id = t2.usuario_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

          LEFT JOIN `pg_avances` `t15`
            ON `t15`.user_id = `t2`.`usuario_id` AND `t15`.`entrenamiento_id` != 0

      GROUP BY t0.usuario_id

    ) tbl_main
    GROUP BY distributor_id ;';
  $query = sprintf($sql, $filter1);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByEquipo_old($filter, $db){
  $filter1 = '';
  if ($filter !== '') {
    $filter1 = ' WHERE t2.list_type_id IN ('. $filter. ') ';
  }
  $sql = 'SELECT t1.team_name, t1.distributor_id, t1.subsidiary_id,
    t4.distributor_name, t5.subsidiary_name,
    COUNT(distinct t2.list_type_id) `usuarios`,
    IFNULL(GROUP_CONCAT( distinct `t2`.`list_type_id`), "") `user_ids`,
    COUNT( distinct `t3`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t3`.`user_id`), "") `finish_ids`

    FROM `supply_team` t1

    INNER JOIN `supply_team_list` t2
      ON t2.team_id = t1.team_id AND t2.list_type = "Usuario"

    LEFT JOIN `pg_enroll` t3
      ON t3.user_id = t2.list_type_id AND t3.master_tbl = "pg_entrenamiento" AND t3.`status` = "Finish"

    INNER JOIN `supply_distributor` t4
      ON t4.distributor_id = t1.distributor_id

    INNER JOIN `supply_subsidiary` t5
      ON t5.subsidiary_id = t1.subsidiary_id

    %s

    GROUP BY t1.team_id
    ORDER BY t1.distributor_id, t1.subsidiary_id, t1.team_id ;';
  $query = sprintf($sql, $filter1);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByEquipo($filter, $db){
  $filter1 = ($filter !== '')  ?  ' WHERE t2.list_type_id IN ('. $filter. ') '  :  '';
  $sql = 'SELECT t1.team_id, t1.team_name, t1.distributor_id, t1.subsidiary_id,
    t4.distributor_name, t5.subsidiary_name,
    COUNT(distinct `t2`.`list_type_id`) `usuarios`,
    IFNULL(GROUP_CONCAT( distinct `t2`.`list_type_id`), "") `user_ids`,

    COUNT( distinct `t15`.`user_id`) `finish`,
    IFNULL(GROUP_CONCAT( distinct `t15`.`user_id`), "") `finish_ids`,
    COUNT( distinct `t16`.`user_id`) `start`,
    IFNULL(GROUP_CONCAT( distinct `t16`.`user_id`), "") `start_ids`

    FROM `supply_team` `t1`

    INNER JOIN `supply_team_list` `t2`
      ON t2.team_id = t1.team_id AND t2.list_type = "Usuario"

    LEFT JOIN `pg_avances` `t15`
      ON `t15`.user_id = `t2`.`list_type_id` AND `t15`.`avance` >= 100

    LEFT JOIN `pg_avances` `t16`
      ON `t16`.user_id = `t2`.`list_type_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

    LEFT JOIN `supply_distributor` t4
      ON t4.distributor_id = t1.distributor_id

    LEFT JOIN `supply_subsidiary` t5
      ON t5.subsidiary_id = t1.subsidiary_id

    INNER JOIN pg_usuarios t0
      ON t0.usuario_id = t2.list_type_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

    %s

    GROUP BY t1.team_id
    ORDER BY t1.distributor_id, t1.subsidiary_id, t1.team_id ;';
  $query = sprintf($sql, $filter1);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByEquipo_v2($filter, $db){
  $filter1 = ($filter !== '')  ?  ' WHERE t2.list_type_id IN ('. $filter. ') '  :  '';
  $sql = 'SELECT *
    , COUNT(distinct `usuario_id`) `usuarios`
    , SUM(IF(`num_fin` = 3, 1,0)) AS `fin_100`
    , SUM(IF(`num_fin` < 3 AND `num_fin` > 0, 1,0)) AS `en_proceso`
    , SUM(IF(`num_fin` = 0, 1,0)) AS `no_empezaron`

    FROM
    (

      SELECT t1.team_id, t1.team_name, t1.distributor_id, t1.subsidiary_id
      , t4.distributor_name, t5.subsidiary_name
      , t0.usuario_id
      , SUM(IF(`t15`.`avance` >= 100, 1,0)) AS num_fin
      , IFNULL(GROUP_CONCAT( distinct `t15`.`entrenamiento_id`), "") `num_fin_ids`

      FROM supply_team t1

        INNER JOIN `supply_team_list` `t2`
          ON t2.team_id = t1.team_id AND t2.list_type = "Usuario"

        LEFT JOIN `supply_distributor` t4
          ON t4.distributor_id = t1.distributor_id

        LEFT JOIN `supply_subsidiary` t5
          ON t5.subsidiary_id = t1.subsidiary_id

        INNER JOIN pg_usuarios t0
          ON t0.usuario_id = t2.list_type_id AND  `t0`.`usuario_rol` != 5 AND `t0`.`usuario_activo` = "Si"

        LEFT JOIN `pg_avances` `t15`
          ON `t15`.user_id = t2.list_type_id AND `t15`.`entrenamiento_id` != 0

      %s

      GROUP BY t0.usuario_id

    ) tbl_main
    GROUP BY team_id
    ORDER BY distributor_id, subsidiary_id, team_id ;';
  $query = sprintf($sql, $filter1);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByParticipante($filter1, $filter2, $db){
  $filterA = '';
  if ($filter1 !== '') {
    $filterA = ' AND t1.usuario_id IN ('. $filter1. ') ';
  }
  $filterB = '';
  if ($filter2 !== '') {
    $equ = intval($filter2['equipo']);
    $per = intval($filter2['perfil']);
    $dic = intval($filter2['distro']);
    $suc = intval($filter2['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t10`.`team_id`        = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `t1`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t6`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t7`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterB = $equipo. $perfil. $distro. $sucursal;
  }
  $sql = 'SELECT *,
    @curRank := @curRank + 1 AS rank
    FROM (
    SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL( MAX(`t2`.`entrenamiento_id`), 0 ) `entrenamientos_fin`,
    IFNULL( MAX(`t3`.`puntos_valor`), 0 ) `puntos_max`,
    IFNULL( SUM(`t3`.`puntos_valor`), 0 ) `puntos_suma`,
    ROUND( IFNULL( AVG(`t3`.`puntos_valor`), 0 ), 2) `puntos_avg`,
    ROUND( IFNULL( AVG(NULLIF(`t3`.`puntos_aciertos`, 0))*100, 0 ), 2) `puntos_aciertos`,
    IFNULL( CONVERT_TZ(MAX(`t3`.`puntos_fch_reg`),"+00:00","-05:00"), "-" ) `fecha_last`,

    IFNULL(`t6`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t7`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t6`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t7`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t8`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t9`.`usuario_nombre`, "-") `supersivor_nombre`

    FROM `pg_usuarios` `t1`

    LEFT JOIN `pg_enroll` `t2`
      ON `t2`.`user_id` = `t1`.`usuario_id` AND `t2`.`master_tbl` = "pg_entrenamiento" AND `t2`.`status` = "Finish"

    LEFT JOIN `pg_puntos` `t3`
      ON `t3`.`user_id` = `t1`.`usuario_id` AND `t3`.`puntos_estado` = "Ganado" AND `t3`.`entrenamiento_id` != 0 AND `t3`.`tema_id` != 30

    LEFT JOIN `pg_user_location` `t5` ON `t5`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t6` ON `t6`.`distributor_id` = `t5`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t7` ON `t7`.`subsidiary_id` = `t5`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t8` ON `t8`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t9` ON `t9`.`usuario_id` = `t1`.`supervisor_id`
    LEFT JOIN `supply_team_list` `t10` ON `t10`.`list_type_id` = `t1`.`usuario_id` AND `t10`.`list_type` = "Usuario"

    WHERE t1.usuario_rol = 1
    AND t1.usuario_activo = "Si"
    %s
    %s
    GROUP BY t1.usuario_id
    ORDER BY `puntos_suma` DESC, `puntos_aciertos` DESC, `puntos_max` DESC
    LIMIT 2000
    ) `p`, (SELECT @curRank := 0) `r`';
  $query = sprintf($sql, $filterA, $filterB);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoRptByVendedor($filter1, $filter2, $db){
  $filterA = '';
  if ($filter1 !== '') {
    $filterA = ' AND t1.usuario_id IN ('. $filter1. ') ';
  }
  $filterB = '';
  if ($filter2 !== '') {
    $equ = intval($filter2['equipo']);
    $per = intval($filter2['perfil']);
    $dic = intval($filter2['distro']);
    $suc = intval($filter2['sucursal']);
    $equipo   = ( $equ != -1 ) ? ' AND `t11`.`team_id`   = "'. $equ. '" '        : '';
    $perfil   = ( $per != -1 ) ? ' AND `tbl`.`usuario_perfil`  = "'. $per. '" '        : '';
    $distro   = ( $dic != -1 ) ? ' AND `t6`.`distributor_id`  = "'. $dic. '" '        : '';
    $sucursal = ( $suc != -1 ) ? ' AND `t7`.`subsidiary_id`   = "'. $suc. '" '        : '';

    $filterB = $equipo. $perfil. $distro. $sucursal;
  }
  $sql = 'SELECT tbl.*,
IFNULL( t12.curricula_entrenamientos, "" ) `curricula_entrenamientos`,

IFNULL(`t6`.`distributor_id`, -1) `distributor_id`,
IFNULL(`t6`.`distributor_name`, "-") `distributor_name`,
IFNULL(`t7`.`subsidiary_id`, -1) `subsidiary_id`,
IFNULL(`t7`.`subsidiary_name`, "-") `subsidiary_name`,
IFNULL(`t8`.`perfil_name`, "-") `perfil_name`,

IFNULL(`t12`.`usuario_nombre`, "-") `supersivor_nombre`,

IFNULL(`t11`.`team_id`, "") `team_id`,
IFNULL(`t11`.`team_name`, "-") `team_name`,

GROUP_CONCAT( distinct `t15`.`entrenamiento_id`) `entrenamiento_fin`,
GROUP_CONCAT( distinct `t16`.`entrenamiento_id`) `entrenamiento_progeso`

FROM (
  SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL( MAX(`t3`.`puntos_valor`), 0 ) `puntos_max`,
    IFNULL( SUM(`t3`.`puntos_valor`), 0 ) `puntos_suma`,
    ROUND( IFNULL( AVG(`t3`.`puntos_valor`), 0 ), 2) `puntos_avg`,
    ROUND( IFNULL( AVG(NULLIF(`t3`.`puntos_aciertos`, 0))*100, 0 ), 2) `puntos_aciertos`,
    IFNULL( CONVERT_TZ(MAX(`t3`.`puntos_fch_reg`),"+00:00","-05:00"), "-" ) `fecha_last`

    FROM `pg_usuarios` `t1`

    LEFT JOIN `pg_puntos` `t3`
      ON `t3`.`user_id` = `t1`.`usuario_id` AND `t3`.`puntos_estado` = "Ganado" AND `t3`.`entrenamiento_id` != 0 AND `t3`.`tema_id` != 30

    WHERE t1.usuario_rol = 1
    AND t1.usuario_activo = "Si"
    %s
    GROUP BY t1.usuario_id
    ORDER BY t1.usuario_nombre ASC
    LIMIT 2000
) tbl

LEFT JOIN ( SELECT *
  FROM (SELECT * FROM pg_perfiles_curricula ORDER BY curricula_id DESC) tbl2
  GROUP BY tbl2.perfil_id ) `t12`
  ON `t12`.`perfil_id` = `tbl`.`usuario_perfil`

LEFT JOIN `pg_user_location` `t5`   ON `t5`.`usuario_id` = `tbl`.`usuario_id`
LEFT JOIN `supply_distributor` `t6` ON `t6`.`distributor_id` = `t5`.`distributor_id`
LEFT JOIN `supply_subsidiary` `t7`  ON `t7`.`subsidiary_id` = `t5`.`subsidiary_id`

LEFT JOIN `pg_perfiles` `t8` ON `t8`.`perfil_id` = `tbl`.`usuario_perfil`

LEFT JOIN ( SELECT *
  FROM (SELECT * FROM supply_team_list ORDER BY list_id DESC) tbl3
  GROUP BY tbl3.list_type_id ) `t10`
  ON `t10`.`list_type_id` = `tbl`.`usuario_id` AND `t10`.`list_type` = "Usuario"

LEFT JOIN `supply_team` `t11` ON `t11`.`team_id` = `t10`.`team_id`

LEFT JOIN `pg_usuarios` `t12` ON `t12`.`usuario_id` = `t11`.`supervisor_id`

LEFT JOIN `pg_avances` `t15`
  ON `t15`.user_id = `tbl`.`usuario_id` AND `t15`.`avance` >= 100

LEFT JOIN `pg_avances` `t16`
  ON `t16`.user_id = `tbl`.`usuario_id` AND `t16`.`avance` >= 0 AND `t16`.`avance` < 100

WHERE 1=1
%s

GROUP BY tbl.usuario_id ;';
  $query = sprintf($sql, $filterA, $filterB);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getNumC($code, $db){
  $sql = 'SELECT COUNT(`t1`.`curso_id`) AS num
    FROM `pg_cursos` `t1`
    WHERE `t1`.`entrenamiento_id` = %d ;';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['num'] ) : 0;
  return $rpta;
}

function getNumT($code, $db){
  $sql = 'SELECT COUNT(`t1`.`tema_id`) AS num
    FROM `pg_temas` `t1`
    WHERE `t1`.`curso_id` = %d ;';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['num'] ) : 0;
  return $rpta;
}

function getPorcentajeHito($code, $db){
  $sql = 'SELECT `t1`.`hito_porcentaje`
    FROM `pg_hitos` `t1`
    WHERE `t1`.`hito_id` = %d ;';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['hito_porcentaje'] ) : 0;
  return $rpta;
}

function calcPorcentajeAvance($hito, $porcentaje, $numC, $numT){
  $valor = 0;
  switch ($hito) {
    case 'cursoVerVideo':
    case 'cursoIniciarVideo':
    case 'cursoCuestionarioPerfecto':
    case 'cursoCuestionarioNormal':
      $valor = round($porcentaje/$numC, 2);
      break;

    case 'temaVerMaterial':
    case 'juegoFinal1':
    case 'juegoFinal2':
    case 'juegoFinal3':
    case 'ejercicioFinalPerfecto':
    case 'ejercicioFinalNormal':
      $valor = round($porcentaje/$numC/$numT*0.9, 2);
      break;

    case 'novedadVerMaterial':
    case 'novedadCuestionarioPerfecto':
    case 'novedadCuestionarioNormal':
      $valor = $porcentaje;
      break;


    default:
      $valor = 0;
      break;
  }
  return $valor;
}

function sumarAvanceEntrenamiento($user_id, $avance, $entrenamiento_id, $db){
  // BuscarDuplicado ? Insert : Update
  $sql = 'SELECT COUNT(`user_id`) `num`
    FROM `pg_avances`
    WHERE `user_id` = %d
    AND `entrenamiento_id` = %d ;';
  $query = sprintf($sql, $user_id, $entrenamiento_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['num'] ) : 0;

  $rpta2 = array('status' => MSG_ERROR );
  if ($rpta === 0){
    // Insert
    $rpta2 = insertAvanceEntrenamiento($user_id, $avance, $entrenamiento_id, $db);
  } else {
    // Update
    $rpta2 = updateAvanceEntrenamiento($user_id, $avance, $entrenamiento_id, $db);
  }

  return $rpta2;
}

function insertAvanceEntrenamiento($user_id, $avance, $entrenamiento_id, $db){
  $data = Array (
    'user_id'          => $user_id,
    'entrenamiento_id' => $entrenamiento_id,
    'avance'           => $avance
  );

  $id = $db->insert ('pg_avances', $data);
  $status = ($id) ? MSG_OK : MSG_ERROR;
  $info   = ($id) ? $id    : $db->getLastError();

  $result = Array ('status' => $status, 'info' => $avance);
  return $result;
}

function updateAvanceEntrenamiento($user_id, $avance, $entrenamiento_id, $db){
  $sql = 'UPDATE pg_avances
    SET avance = avance + %g
    WHERE user_id = %d
    AND entrenamiento_id = %d ';
  $query = sprintf($sql, $avance, $user_id, $entrenamiento_id);

  $result = $db->rawQuery($query);

  //$status = ( count($result) ) ? MSG_OK : MSG_ERROR;
  //$info   = ($status === MSG_ERROR) ? $db->getLastError() : '';

  //$result = Array ('status' => $status, 'info' => $info);
  $result = Array ('status' => MSG_OK, 'info' => $avance);
  return $result;
}

/**
  Reportes
**/

  function getReporteByUserByEntrenamiento(){
    $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,

      IFNULL(`t3`.`avance`, 0) `avance`

      FROM `pg_entrenamiento` `t1`
      LEFT JOIN (
        SELECT * FROM `pg_avances`
        WHERE user_id = 1
      ) `t3` ON `t3`.`entrenamiento_id` = `t1`.`entrenamiento_id`

      ORDER BY `t1`.`entrenamiento_id` ASC
      LIMIT 2000 ;';


  }

  function getAvanceByEntrenamientoByUser($user_id, $entrenamiento_id, $db){
    $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
      IFNULL(`t3`.`avance`, 0) `avance`
      FROM `pg_entrenamiento` `t1`
      LEFT JOIN (
        SELECT * FROM `pg_avances`
        WHERE user_id = %d
      ) `t3` ON `t3`.`entrenamiento_id` = `t1`.`entrenamiento_id`

      WHERE `t1`.`entrenamiento_id` = %d
      ORDER BY `t1`.`entrenamiento_id` ASC
      LIMIT 1 ;';
    $query = sprintf($sql, $user_id, $entrenamiento_id);

    $result = $db->rawQuery($query);

    $null = array('entrenamiento_id' => '', 'entrenamiento_nombre' => '', 'avance' => 0);
    $rpta = isset($result[0]) ? $result[0] : $null;
    return $rpta;
  }


/**
  Hitos
**/
function getHitoById($hito_id, $db){
  $query = 'SELECT `hito_id`, `hito_nombre`, `hito_tipo`, `hito_puntaje_1`, `hito_puntaje_2`, `hito_puntaje_3`
    FROM `pg_hitos`
    WHERE `hito_id` = ?
    LIMIT 1; ';

  $result = $db->rawQuery($query);

  $status = ( count($result) ) ? MSG_OK : MSG_ERROR;
  $info   = ($status === MSG_ERROR) ? $db->getLastError() : '';

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

function getPuntajeHito($hito_nombre, $hito_campo, $db){
  $sql = 'SELECT `%s` AS `puntaje`
    FROM `pg_hitos`
    WHERE `hito_nombre` = "%s"
    LIMIT 1; ';
  $query = sprintf($sql, $hito_campo, $hito_nombre);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result[0]['puntaje'] : 0;
  return $rpta;
}

function getPuntajeHitoFull($hito_nombre, $db){
  $sql = 'SELECT `hito_puntaje_1`, `hito_puntaje_2`, `hito_puntaje_3` AS `puntaje`
    FROM `pg_hitos`
    WHERE `hito_nombre` = "%s"
    LIMIT 1; ';
  $query = sprintf($sql, $hito_nombre);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

/**
  Busquedas
**/

function getBusqueda($q, $db){
  $q = '%'. $q. '%';
  $sql = 'SELECT * FROM (
    (SELECT `entrenamiento_id` AS `tbl_master_id`, `entrenamiento_nombre`, "entrenamiento" AS `tbl_master`
    FROM `pg_entrenamiento`
    WHERE `entrenamiento_nombre` like "%s"
    ORDER BY `entrenamiento_id` ASC
    LIMIT 10)

    UNION
    (SELECT `curso_id` AS `tbl_master_id`, `curso_nombre`, "curso" AS `tbl_master`
    FROM `pg_cursos`
    WHERE `curso_nombre` like "%s"
    ORDER BY `curso_id` ASC
    LIMIT 10)

    UNION
    (SELECT `tema_id` AS `tbl_master_id`, `tema_nombre`, "tema" AS `tbl_master`
    FROM `pg_temas`
    WHERE `tema_nombre` like "%s"
    ORDER BY `tema_id` ASC
    LIMIT 10)

    ) AS `Resultados`
    LIMIT 10; ';
  $query = sprintf($sql, $q, $q, $q);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : '';
  return $rpta;
}

/**
  REPORTES
**/
function getReporte($db){
  $query = 'SELECT `subsidiary_id`, `subsidiary_name`, `subsidiary_descrip`
    FROM `supply_subsidiary`
    ORDER BY `subsidiary_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

/**
  TEAM LIST
**/
function getTeamListFull($db){
  $query = 'SELECT `t1`.`list_id`, `t1`.`team_id`, `t1`.`list_name`, `t1`.`list_type`, `t1`.`list_type_id`
    FROM `supply_team_list` `t1`
    ORDER BY `t1`.`list_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getTeamList($code = 0, $db){
  $sql = 'SELECT `list_id`, `team_id`, `list_name`, `list_type`, `list_type_id`
    FROM `supply_team_list`
    WHERE `list_id` = %d
    ORDER BY `list_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getTeamListById($code, $db){
  $sql = 'SELECT `list_id`, `team_id`, `list_name`, `list_type`, `list_type_id`
    FROM `supply_team_list`
    WHERE `list_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getTeamListByTeam($code, $db){
  $sql = 'SELECT `list_id`, `team_id`, `list_name`, `list_type`, `list_type_id`
    FROM `supply_team_list`
    WHERE `team_id` = %d
    ORDER BY `list_type`, `list_id`
    LIMIT 2000; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosIdsByTeam($code, $db){
  $sql = 'SELECT GROUP_CONCAT(`list_type_id`) `Usuarios`
    FROM `supply_team_list`
    WHERE `team_id` = %d
    AND list_type = "Usuario"
    GROUP BY `team_id`
    ORDER BY `list_id`
    LIMIT 2000; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosByTeamRelly($var, $db){
  $sql = 'SELECT `t0`.`list_id`, `t0`.`list_type_id`,
    `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`

    FROM `supply_team_list` t0

    INNER JOIN `pg_usuarios` t1
      ON t1.usuario_id = t0.list_type_id

    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`

    WHERE `t0`.`team_id` = %d
    AND `t0`.`list_type` = "Usuario"
    AND `t1`.`usuario_activo` = "Si"

    ORDER BY `t1`.`usuario_nombre` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $var);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getUsuariosByTeam($var, $db){
  $query2 = ( !empty($var) ) ? ' AND `t1`.`usuario_id` IN ('. $var. ') '   :  '';

  $sql = 'SELECT `t1`.`usuario_id`, `t1`.`usuario_nombre`, `t1`.`usuario_doc`, `t1`.`usuario_activo`, `t1`.`supervisor_id`, `t1`.`usuario_rol`, `t1`.`usuario_perfil`, `t1`.`usuario_email`,
    IFNULL(`t2`.`distributor_id`, -1) `distributor_id`,
    IFNULL(`t2`.`subsidiary_id`, -1) `subsidiary_id`,
    IFNULL(`t3`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t4`.`subsidiary_name`, "-") `subsidiary_name`,
    IFNULL(`t5`.`perfil_name`, "-") `perfil_name`,
    IFNULL(`t6`.`usuario_nombre`, "-") `supersivor_nombre`
    FROM `pg_usuarios` `t1`
    LEFT JOIN `pg_user_location` `t2` ON `t2`.`usuario_id` = `t1`.`usuario_id`
    LEFT JOIN `supply_distributor` `t3` ON `t3`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t4` ON `t4`.`subsidiary_id` = `t2`.`subsidiary_id`
    LEFT JOIN `pg_perfiles` `t5` ON `t5`.`perfil_id` = `t1`.`usuario_perfil`
    LEFT JOIN `pg_usuarios` `t6` ON `t6`.`usuario_id` = `t1`.`supervisor_id`
    WHERE `t1`.`usuario_activo` = "Si"
    %s
    ORDER BY `t1`.`usuario_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $query2);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newTeamList($team_id, $list_name, $list_type, $list_type_id, $db){
  $team_id      = htmlentities($team_id, ENT_QUOTES);
  $list_name    = htmlentities($list_name, ENT_QUOTES);
  $list_type    = htmlentities($list_type, ENT_QUOTES);
  $list_type_id = htmlentities($list_type_id, ENT_QUOTES);

  $data = Array (
    'team_id'       => $team_id,
    'list_name'     => $list_name,
    'list_type'     => $list_type,
    'list_type_id'  => $list_type_id
  );

  $id = $db->insert ('supply_team_list', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delTeamList($code, $db){
  $db->where('list_id', $code);

  $rpta = ( $db->delete('supply_team_list') ) ? 2 : -1;
  return $rpta;
}

function updateTeamList($code, $team_id, $list_name, $list_type, $list_type_id, $db){
  $team_id      = htmlentities($team_id, ENT_QUOTES);
  $list_name    = htmlentities($list_name, ENT_QUOTES);
  $list_type    = htmlentities($list_type, ENT_QUOTES);
  $list_type_id = htmlentities($list_type_id, ENT_QUOTES);

  $data = Array (
    'team_id'       => $team_id,
    'list_name'     => $list_name,
    'list_type'     => $list_type,
    'list_type_id'  => $list_type_id
  );

  $db->where ('list_id', $code);

  $rpta  = ( $db->update ('supply_team_list', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  NIVELES
**/
function getNivelesFull($db){
  $query = 'SELECT `t1`.`nivel_id`, `t1`.`nivel_name`, `t1`.`perfil_id`, `t1`.`nivel_imagen`,
    `t2`.`perfil_name`
    FROM `pg_perfiles_niveles` `t1`
    INNER JOIN `pg_perfiles` `t2` ON `t1`.`perfil_id` = `t2`.`perfil_id`
    ORDER BY `t1`.`perfil_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getNiveles($code = 0, $db){
  $sql = 'SELECT `nivel_id`, `nivel_name`, `nivel_descrip`, `nivel_imagen`, `perfil_id`
    FROM `pg_perfiles_niveles`
    WHERE `perfil_id` = %d
    ORDER BY `nivel_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getNivelById($code, $db){
  $query = 'SELECT `nivel_id`, `nivel_name`, `nivel_descrip`, `nivel_imagen`, `perfil_id`
    FROM `pg_perfiles_niveles`
    WHERE `nivel_id` = ?
    LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newNivel($nombre, $descrip, $perfil_id, $images, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);
  //$image    = htmlentities($images, ENT_QUOTES);

  $data = Array (
    'nivel_name'    => $nombre,
    'nivel_descrip' => $descrip,
    'nivel_imagen'  => $images,
    'perfil_id'     => $perfil_id
  );

  $id = $db->insert ('pg_perfiles_niveles', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delNivel($code, $db){
  $db->where('nivel_id', $code);

  $rpta = ( $db->delete('pg_perfiles_niveles') ) ? 2 : -1;
  return $rpta;
}

function updateNivel($code, $nombre, $descrip, $perfil_id, $images, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);
  //$image    = htmlentities($images, ENT_QUOTES);

  $data = Array (
    'nivel_name'     => $nombre,
    'nivel_descrip'  => $descrip,
    'nivel_imagen'   => $images,
    'perfil_id'      => $perfil_id
  );

  $db->where ('nivel_id', $code);

  $rpta  = ( $db->update ('pg_perfiles_niveles', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  PERFILES
**/
function getPerfilesFull($db){
  $query = 'SELECT `perfil_id`, `perfil_name`, `perfil_descrip`
    FROM `pg_perfiles`
    ORDER BY `perfil_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getPerfiles($code = 0, $db){
  $sql = 'SELECT `perfil_id`, `perfil_name`, `perfil_descrip`
    FROM `pg_perfiles`
    ORDER BY `perfil_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($sql);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getPerfilById($code, $db){
  $query = 'SELECT `perfil_id`, `perfil_name`, `perfil_descrip`
    FROM `pg_perfiles`
    WHERE `perfil_id` = ?
    LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newPerfil($nombre, $descrip, $distributor_id, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'perfil_name'    => $nombre,
    'perfil_descrip' => $descrip
  );

  $id = $db->insert ('pg_perfiles', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delPerfil($code, $db){
  $db->where('perfil_id', $code);

  $rpta = ( $db->delete('pg_perfiles') ) ? 2 : -1;
  return $rpta;
}

function updatePerfil($code, $nombre, $descrip, $distributor_id, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'perfil_name'     => $nombre,
    'perfil_descrip'  => $descrip
  );

  $db->where ('perfil_id', $code);

  $rpta  = ( $db->update ('pg_perfiles', $data) ) ? 2 : -1;
  return $rpta;
}

function getPerfilImagenes($perfil_id, $nivel_id, $db){
  $sql = 'SELECT `nivel_imagen`
    FROM `pg_perfiles_niveles`
    WHERE `perfil_id` = %d
    #AND `nivel_id` = %d
    ORDER BY `nivel_id` ASC
    LIMIT 1; ';
  $query = sprintf($sql, $perfil_id, $nivel_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['nivel_imagen'] ) : IMAGES_DEFAULT;
  return $rpta;
}

/**
  CURRICULA
**/
function getCurriculasFull($db){
  $query = 'SELECT `curricula_id`, `curricula_entrenamientos`, `perfil_id`, `curricula_novedades`
    FROM `pg_perfiles_curricula`
    ORDER BY `curricula_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getCurriculas($code = 0, $db){
  $sql = 'SELECT `curricula_id`, `curricula_entrenamientos`, `perfil_id`, `curricula_novedades`
    FROM `pg_perfiles_curricula`
    ORDER BY `curricula_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($sql);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getCurriculaById($code, $db){
  $sql = 'SELECT `curricula_id`, `curricula_entrenamientos`, `perfil_id`, `curricula_novedades`
    FROM `pg_perfiles_curricula`
    WHERE `curricula_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getCurriculaByPerfil($code, $db){
  $sql = 'SELECT `curricula_id`, `curricula_entrenamientos`, `perfil_id`, `curricula_novedades`
    FROM `pg_perfiles_curricula` `t1`
    WHERE `t1`.`perfil_id` = %d
    ORDER BY `curricula_id` DESC
    LIMIT 1; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newCurricula($entrenamientos, $novedades, $perfil, $db){
  $data = Array (
    'curricula_entrenamientos'  => $entrenamientos,
    'curricula_novedades'       => $novedades,
    'perfil_id'                 => $perfil
  );

  $id = $db->insert ('pg_perfiles_curricula', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delCurricula($code, $db){
  $db->where('curricula_id', $code);

  $rpta = ( $db->delete('pg_perfiles_curricula') ) ? 2 : -1;
  return $rpta;
}

function updateCurricula($code, $entrenamientos, $novedades, $perfil, $db){
  $data = Array (
    'curricula_entrenamientos' => $nombre,
    'curricula_novedades'      => $novedades,
    'perfil_id'                => $perfil,
  );

  $db->where ('curricula_id', $code);

  $rpta  = ( $db->update ('pg_perfiles_curricula', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  EQUIPOS
**/
function getEquiposFull($db){
  $query = 'SELECT `team_id`, `team_name`, `team_descrip`, `distributor_id`, `subsidiary_id`, `supervisor_id`
    FROM `supply_team`
    ORDER BY `team_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEquiposDS($db){
  $sql = 'SELECT `t1`.`team_id`, `t1`.`team_name`, `t1`.`team_descrip`, `t1`.`distributor_id`, `t1`.`subsidiary_id`, `t1`.`supervisor_id`,
    IFNULL(`t6`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t7`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `supply_team` t1

    LEFT JOIN `supply_distributor` `t6` ON `t6`.`distributor_id` = `t1`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t7`  ON `t7`.`subsidiary_id` = `t1`.`subsidiary_id`

    ORDER BY `t1`.`team_id` ASC
    LIMIT 2000 ;';

  $result = $db->rawQuery($sql);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEquipos($code = 0, $db){
  $sql = 'SELECT `team_id`, `team_name`, `team_descrip`, `distributor_id`, `subsidiary_id`, `supervisor_id`
    FROM `supply_team`
    ORDER BY `team_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($sql);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEquipoById($code, $db){
  $sql = 'SELECT `team_id`, `team_name`, `team_descrip`, `distributor_id`, `subsidiary_id`, `supervisor_id`
    FROM `supply_team`
    WHERE `team_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEquiposBySup($code, $db){
  $sql = 'SELECT `t1`.`team_id`, `t1`.`team_name`, `t1`.`team_descrip`, `t1`.`distributor_id`, `t1`.`subsidiary_id`, `t1`.`supervisor_id`,
    IFNULL(`t6`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t7`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `supply_team` t1

    LEFT JOIN `supply_distributor` `t6` ON `t6`.`distributor_id` = `t1`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t7`  ON `t7`.`subsidiary_id` = `t1`.`subsidiary_id`

    WHERE `t1`.`supervisor_id` = %d

    ORDER BY `t1`.`team_id` ASC
    LIMIT 2000 ;';

  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEquiposByJefe($code, $db){
  $sql = 'SELECT `t1`.`team_id`, `t1`.`team_name`, `t1`.`team_descrip`, `t1`.`distributor_id`, `t1`.`subsidiary_id`, `t1`.`supervisor_id`,
    IFNULL(`t6`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t7`.`subsidiary_name`, "-") `subsidiary_name`
    FROM `supply_team` t1

    LEFT JOIN `supply_distributor` `t6` ON `t6`.`distributor_id` = `t1`.`distributor_id`
    LEFT JOIN `supply_subsidiary` `t7`  ON `t7`.`subsidiary_id` = `t1`.`subsidiary_id`

    WHERE `t1`.`supervisor_id` IN ( %s )

    ORDER BY `t1`.`team_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEquipoByIdFull($code, $db){
  $sql = 'SELECT `t1`.`team_id`, `t1`.`team_name`, `t1`.`team_descrip`, `t1`.`distributor_id`, `t1`.`subsidiary_id`, `t1`.`supervisor_id`,
    IFNULL(`t2`.`distributor_name`, "-") `distributor_name`,
    IFNULL(`t3`.`subsidiary_name`, "") `subsidiary_name`,
    IFNULL(`t4`.`usuario_nombre`, "") `usuario_nombre`
    FROM `supply_team` `t1`
    LEFT JOIN `supply_distributor` `t2` ON `t1`.`distributor_id` = `t2`.`distributor_id`
    LEFT JOIN `supply_subsidiary`  `t3` ON `t1`.`subsidiary_id` = `t3`.`subsidiary_id`
    LEFT JOIN `pg_usuarios` `t4` ON `t1`.`supervisor_id` = `t4`.`usuario_id`
    WHERE `t1`.`team_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newEquipo($nombre, $descrip, $distributor_id, $subsidiary_id, $supervisor_id, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'team_name'      => $nombre,
    'team_descrip'   => $descrip,
    'distributor_id' => $distributor_id,
    'subsidiary_id'  => $subsidiary_id,
    'supervisor_id'  => $supervisor_id
  );

  $id = $db->insert ('supply_team', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delEquipo($code, $db){
  $db->where('team_id', $code);

  $rpta = ( $db->delete('supply_team') ) ? 2 : -1;
  return $rpta;
}

function delEquipoMiembro($code, $db){
  $db->where('list_id', $code);

  $rpta = ( $db->delete('supply_team_list') ) ? 2 : -1;
  return $rpta;
}

function updateEquipo($code, $nombre, $descrip, $distributor_id, $subsidiary_id, $supervisor_id, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'team_name'      => $nombre,
    'team_descrip'   => $descrip,
    'distributor_id' => $distributor_id,
    'subsidiary_id'  => $subsidiary_id,
    'supervisor_id'  => $supervisor_id
  );

  $db->where ('team_id', $code);

  $rpta  = ( $db->update ('supply_team', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  SUCURSALES
**/
function getSucursalesFull($db){
  $query = 'SELECT `t1`.`subsidiary_id`, `t1`.`subsidiary_name`, `t1`.`distributor_id`,
    `t2`.`distributor_name`
    FROM `supply_subsidiary` `t1`
    INNER JOIN `supply_distributor` `t2` ON `t1`.`distributor_id` = `t2`.`distributor_id`
    ORDER BY `t1`.`subsidiary_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getSucursales($code = 0, $db){
  $sql = 'SELECT `subsidiary_id`, `subsidiary_name`, `subsidiary_descrip`, `distributor_id`
    FROM `supply_subsidiary`
    WHERE `distributor_id` = %d
    ORDER BY `subsidiary_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getSucursalById($code, $db){
  $query = 'SELECT `subsidiary_id`, `subsidiary_name`, `subsidiary_descrip`, `distributor_id`
    FROM `supply_subsidiary`
    WHERE `subsidiary_id` = ?
    LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newSucursal($nombre, $descrip, $distributor_id, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'subsidiary_name'    => $nombre,
    'subsidiary_descrip' => $descrip,
    'distributor_id'     => $distributor_id
  );

  $id = $db->insert ('supply_subsidiary', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delSucursal($code, $db){
  $db->where('subsidiary_id', $code);

  $rpta = ( $db->delete('supply_subsidiary') ) ? 2 : -1;
  return $rpta;
}

function updateSucursal($code, $nombre, $descrip, $distributor_id, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'subsidiary_name'     => $nombre,
    'subsidiary_descrip'  => $descrip,
    'distributor_id'      => $distributor_id
  );

  $db->where ('subsidiary_id', $code);

  $rpta  = ( $db->update ('supply_subsidiary', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  Distribuidora
**/
function getDistribuidoras($db){
  $query = 'SELECT `distributor_id`, `distributor_name`, `distributor_descrip`
    FROM `supply_distributor`
    ORDER BY `distributor_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getDistribuidoraById($code, $db){
  $query = 'SELECT `distributor_id`, `distributor_name`, `distributor_descrip`
    FROM `supply_distributor`
    WHERE `distributor_id` = ?
    LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newDistribuidora($nombre, $descrip, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'distributor_name'    => $nombre,
    'distributor_descrip' => $descrip
  );

  $id = $db->insert ('supply_distributor', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delDistribuidora($code, $db){
  $db->where('distributor_id', $code);

  $rpta = ( $db->delete('supply_distributor') ) ? 2 : -1;
  return $rpta;
}

function updateDistribuidora($code, $nombre, $descrip, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'distributor_name'    => $nombre,
    'distributor_descrip' => $descrip
  );

  $db->where ('distributor_id', $code);

  $rpta  = ( $db->update ('supply_distributor', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  Supervisor custom
**/
function savePerfilSup($code, $rel_name, $rel_id, $db){
  $data = Array (
    'rel_id'      => $rel_id,
    'rel_name'    => $rel_name,
    'usuario_id'  => $code
  );
  $update = Array (
    'rel_id'      => $rel_id,
    'rel_name'    => $rel_name
  );

  $id = $db->insert ('pg_supervisor', $data);

  $status = ($id) ? 2 : -1;
  $info   = ($id) ? $id : $db->getLastError();

  $rpta = $status;
  if ($status === -1){
    $db->where ('usuario_id', $code);
    $status  = ( $db->update ('pg_supervisor', $update) ) ? MSG_OK : MSG_ERROR;
  }

  $result = Array ('status' => $status, 'info' => $info, 'original' => $rpta);
  return $result;
}

function getSupervisorRel($user_id, $rel_name, $db){
  $sql = 'SELECT `rel_id`
    FROM `pg_supervisor`
    WHERE `usuario_id` = %d
    AND `rel_name` = "%s"
    LIMIT 1; ';
  $query = sprintf($sql, $user_id, $rel_name);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['rel_id'] ) : -1;
  return $rpta;
}

/**
  Location
**/
function saveLocation($code, $locID, $locD, $locS, $db){
  $data = Array (
    'distributor_id' => $locD,
    'subsidiary_id'  => $locS,
    'usuario_id'     => $code
  );
  $update = Array (
    'distributor_id' => $locD,
    'subsidiary_id'  => $locS
  );

  $id = $db->insert ('pg_user_location', $data);

  $status = ($id) ? 2 : -1;
  $info   = ($id) ? $id : $db->getLastError();

  $rpta = $status;
  if ($status === -1){
    $db->where ('usuario_id', $code);
    $status  = ( $db->update ('pg_user_location', $update) ) ? 2 : -1;
  }

  $result = Array ('status' => $status, 'info' => $info, 'original' => $rpta);
  return $result;
}

function getLocationUser($code, $db){
  $sql = 'SELECT `distributor_id`, `subsidiary_id`
    FROM `pg_user_location`
    WHERE `usuario_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $default = Array ('distributor_id' => -1, 'subsidiary_id' => -1);
  $rpta = ( count($result)>0 ) ? ( $result[0] ) : $default;
  return $rpta;
}

/**
  Jefe -> Supervisor
**/
function saveJefeSup($jefe, $supervisores, $db){
  $data = Array (
    'supervisores'   => $supervisores,
    'usuario_id'     => $jefe
  );
  $update = Array (
    'supervisores'   => $supervisores
  );

  $id = $db->insert ('pg_jefes_sup', $data);

  $status = ($id) ? MSG_OK : MSG_ERROR;
  $info   = ($id) ? $id : $db->getLastError();

  $rpta = $status;
  if ($status === MSG_ERROR){
    $db->where ('usuario_id', $jefe);
    $status  = ( $db->update ('pg_jefes_sup', $update) ) ? MSG_OK : MSG_ERROR;
  }

  $result = Array ('status' => $status, 'info' => $info, 'original' => $rpta);
  return $result;
}

function addJefeSup($jefe, $supervisores, $db){
  $data = Array (
    'supervisores'   => $supervisores,
    'usuario_id'     => $jefe
  );

  $id = $db->insert ('pg_jefes_sup', $data);

  $status = ($id) ? MSG_OK : MSG_ERROR;
  $info   = ($id) ? $id : $db->getLastError();

  $rpta = $status;
  if ($status === MSG_ERROR){
    $oldSup = getSupByJefe($jefe, $db);
    $newSup = $oldSup. ','. $supervisores;
    $update = Array (
      'supervisores'   => $newSup
    );

    $db->where ('usuario_id', $jefe);
    $status  = ( $db->update ('pg_jefes_sup', $update) ) ? MSG_OK : MSG_ERROR;
  }

  $result = Array ('status' => $status, 'info' => $info, 'original' => $rpta);
  return $result;
}


function getSupByJefe($code, $db){
  $sql = 'SELECT `supervisores`
    FROM `pg_jefes_sup`
    WHERE `usuario_id` = %d
    LIMIT 2000; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? ( $result[0]['supervisores'] ) : -1;
  return $rpta;
}

/**
  Linaje
**/

function getLinajeOne($entrenamiento_id, $curso_id, $tema_id, $db){
  if ($entrenamiento_id !== 0) {
    $sql2 = '`t1`.`entrenamiento_id` = %d';
    $query2 = sprintf($sql2, $entrenamiento_id);
  }
  if ($curso_id !== 0) {
    $sql2 = '`t4`.`curso_id` = %d';
    $query2 = sprintf($sql2, $curso_id);
  }
  if ($tema_id !== 0) {
    $sql2 = '`t5`.`tema_id` = %d';
    $query2 = sprintf($sql2, $tema_id);
  }
  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
  IFNULL(`t4`.`curso_id`, "") `curso_id`, IFNULL(`t4`.`curso_nombre`, "") `curso_nombre`,
  IFNULL(`t5`.`tema_id`, "") `tema_id`, IFNULL(`t5`.`tema_nombre`, "") `tema_nombre`,

  CONCAT(
    IF(`t1`.`entrenamiento_id` IS NULL, "" , `t1`.`entrenamiento_id`),
    IF(`t4`.`curso_id` IS NULL,"", ","),
    IF(`t4`.`curso_id` IS NULL,"", `t4`.`curso_id`),
    IF(`t5`.`tema_id` IS NULL,"", ","),
    IF(`t5`.`tema_id` IS NULL,"", `t5`.`tema_id`)
    ) `linaje`

  FROM `pg_entrenamiento` `t1`
  LEFT JOIN
    `pg_cursos` `t4`
    ON `t4`.`entrenamiento_id` = `t1`.`entrenamiento_id`
  LEFT JOIN
    `pg_temas` `t5`
    ON `t5`.`curso_id` = `t4`.`curso_id`

  WHERE %s

  ORDER BY `t1`.`entrenamiento_id` ASC, `t4`.`curso_id` ASC
  LIMIT 1 ;';
  $query = sprintf($sql, $query2);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getLinajeFull($db){
  $query = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`,
    IFNULL(t4.curso_id, "") `curso_id`, IFNULL(t4.curso_nombre, "") `curso_nombre`,
    IFNULL(t5.tema_id, "") `tema_id`, IFNULL(t5.tema_nombre, "") `tema_nombre`,

    CONCAT(
      IF(`t1`.`entrenamiento_id` IS NULL, "" , `t1`.`entrenamiento_id`),
      IF(t4.curso_id IS NULL,"", ","),
      IF(t4.curso_id IS NULL,"", t4.curso_id),
      IF(t5.tema_id IS NULL,"", ","),
      IF(t5.tema_id IS NULL,"", t5.tema_id)
    ) AS `linaje`

    FROM `pg_entrenamiento` `t1`
    LEFT JOIN
      `pg_cursos` `t4`
      ON t4.entrenamiento_id = t1.entrenamiento_id
    LEFT JOIN
      `pg_temas` `t5`
      ON t5.curso_id = t4.curso_id

    ORDER BY `t1`.`entrenamiento_id` ASC, `t4`.`curso_id` ASC, `t5`.`tema_id` ASC
    LIMIT 2000 ;';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

/**
  Disponibilidad - Enroll
**/

function firtsCurso($entrenamiento_id, $db){
  $sql = 'SELECT `curso_id`
    FROM `pg_cursos`
    WHERE `entrenamiento_id` = %d
    ORDER BY `curso_id` ASC
    LIMIT 1; ';
  $query = sprintf($sql, $entrenamiento_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result[0]['curso_id'] : 0;
  return $rpta;
}

function checkEnrollStatus($user_id, $master_table, $master_id, $db){
  $sql = 'SELECT `status`, `progress`, `points`
    FROM `pg_enroll`
    WHERE user_id = %d
    AND `master_tbl` = "%s"
    AND `master_id` = %d
    ORDER BY `enroll_id` DESC
    LIMIT 1 ;';
  $query = sprintf($sql, $user_id, $master_table, $master_id);
  $result = $db->rawQuery($query);

  //$null = array ('status' => $query, 'progress' => 0, 'points' => 0);
  $null = array ('status' => 'No', 'progress' => 0, 'points' => 0);
  $rpta = ( count($result)>0 ) ? $result[0] : $null;
  return $rpta;
}

function setEnrollStatus($user_id, $master_table, $master_id, $arrLinaje, $status, $db){
  $data = Array (
    'user_id'          => $user_id,
    'entrenamiento_id' => $arrLinaje[0],
    'curso_id'         => $arrLinaje[1],
    'tema_id'          => $arrLinaje[2],
    'master_tbl'       => $master_table,
    'master_id'        => $master_id,
    'status'           => $status,
    'progress'         => 0,
    'points'           => 0,
    'creation_at'      => $db->now()
  );

  $id = $db->insert ('pg_enroll', $data);
  $status = ($id) ? MSG_OK : MSG_ERROR;
  $info   = ($id) ? $id    : $db->getLastError();

  $result = Array ('status' => $status, 'info' => $info);
  return $result;
}

/**
  Entrenamientos
**/
function getEntrenamientosByPerfil($user_id, $perfil_entrenamientos, $db){
  $perfil_entrenamientos = (strlen($perfil_entrenamientos)>0) ? $perfil_entrenamientos : '-1';
  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`, `t1`.`entrenamiento_logo`, `t1`.`entrenamiento_activo`, `t1`.`entrenamiento_t_oro`, `t1`.`entrenamiento_t_plata`, `t1`.`entrenamiento_t_bronce`,
    IFNULL( `t3`.`avance`, 0) `avance`
    FROM `pg_entrenamiento` `t1`
    LEFT JOIN `pg_avances` `t3` ON `t3`.`entrenamiento_id` = `t1`.`entrenamiento_id` AND `t3`.`user_id` = %d
    WHERE `t1`.`entrenamiento_activo` = "Si"
    AND `t1`.`entrenamiento_id` IN ('. $perfil_entrenamientos. ')
    ORDER BY `t1`.`entrenamiento_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientosLista($user_id, $db){
  $sql = 'SELECT `t1`.`entrenamiento_id`, `t1`.`entrenamiento_nombre`, `t1`.`entrenamiento_logo`, `t1`.`entrenamiento_activo`, `t1`.`entrenamiento_t_oro`, `t1`.`entrenamiento_t_plata`, `t1`.`entrenamiento_t_bronce`,
    IFNULL(`t3`.`avance`, 0) `avance`
    FROM `pg_entrenamiento` `t1`
    LEFT JOIN (
      SELECT * FROM `pg_avances`
      WHERE user_id = %d
    ) `t3` ON `t3`.`entrenamiento_id` = `t1`.`entrenamiento_id`
    WHERE `t1`.`entrenamiento_activo` = "Si"
    ORDER BY `t1`.`entrenamiento_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $user_id);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientos($db){
  $query = 'SELECT `entrenamiento_id`, `entrenamiento_nombre`, `entrenamiento_objetivo`, `entrenamiento_logo`, `entrenamiento_descrip`, `entrenamiento_t_oro`, `entrenamiento_t_plata`, `entrenamiento_t_bronce`
    FROM `pg_entrenamiento`
    ORDER BY `entrenamiento_id` ASC
    LIMIT 2000; ';

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientosVarios($filter, $db){
  $sql = 'SELECT `entrenamiento_id`, `entrenamiento_nombre`
    FROM `pg_entrenamiento`
    WHERE `entrenamiento_id` IN (%s) ;';
  $query = sprintf($sql, $filter);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getEntrenamientoById($code, $db){
  $sql = 'SELECT `entrenamiento_id`, `entrenamiento_nombre`, `entrenamiento_objetivo`, `entrenamiento_descrip`, `entrenamiento_activo`, `entrenamiento_t_oro`, `entrenamiento_t_plata`, `entrenamiento_t_bronce`
    FROM `pg_entrenamiento`
    WHERE `entrenamiento_id` = %d LIMIT 1; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newEntrenamiento($nombre, $objetivo, $descrip, $activo, $pic1, $pic2, $pic3, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $objetivo = htmlentities($objetivo, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'entrenamiento_nombre'   => $nombre,
    'entrenamiento_objetivo' => $objetivo,
    'entrenamiento_descrip'  => $descrip,
    'entrenamiento_activo'   => valueToSiNo($activo),
    'entrenamiento_fch_creacion' => $db->now(),
    'entrenamiento_t_oro'    => $pic1,
    'entrenamiento_t_plata'  => $pic2,
    'entrenamiento_t_bronce' => $pic3
  );

  $id = $db->insert ('pg_entrenamiento', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delEntrenamiento($code, $db){
  $db->where('entrenamiento_id', $code);

  $rpta = ( $db->delete('pg_entrenamiento') ) ? 2 : -1;
  return $rpta;
}

function updateEntrenamiento($code, $nombre, $objetivo, $descrip, $activo, $pic1, $pic2, $pic3, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $objetivo = htmlentities($objetivo, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'entrenamiento_nombre'   => $nombre,
    'entrenamiento_objetivo' => $objetivo,
    'entrenamiento_descrip'  => $descrip,
    'entrenamiento_activo'   => valueToSiNo($activo),
    'entrenamiento_fch_edicion' => $db->now(),
    'entrenamiento_t_oro'    => $pic1,
    'entrenamiento_t_plata'  => $pic2,
    'entrenamiento_t_bronce' => $pic3
  );

  $db->where ('entrenamiento_id', $code);

  $rpta  = ( $db->update ('pg_entrenamiento', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  Cursos
**/

function getCursosByUser($user_id, $master_id, $db){
  $sql = 'SELECT `t1`.`curso_id`, `t1`.`curso_nombre`, `t1`.`curso_objetivo`, `t1`.`curso_descrip`, `t1`.`curso_video`, `t1`.`entrenamiento_id`,
    IFNULL(`t2`.`status`, "No") `status`
    FROM `pg_cursos` `t1`
    LEFT JOIN (
      SELECT *
        FROM (SELECT `enroll_id`, `master_tbl`, `master_id`, `status`
        FROM pg_enroll
        WHERE `user_id` = %d AND `master_tbl` = "pg_cursos"
        ORDER BY `enroll_id` DESC) `tx`
        GROUP BY `tx`.`master_tbl`, `tx`.`master_id`
    ) `t2`
      ON `t2`.`master_id` = `t1`.`curso_id`
    WHERE `t1`.`entrenamiento_id` = %d
    ORDER BY `t1`.`curso_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $user_id, $master_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getCursos($code = 0, $db){
  $sql = 'SELECT `curso_id`, `curso_nombre`, `curso_objetivo`, `curso_descrip`, `curso_video`, `entrenamiento_id`
    FROM `pg_cursos`
    WHERE `entrenamiento_id` = %d
    ORDER BY `curso_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getCursoById($code, $db){
  $sql = 'SELECT `curso_id`, `curso_nombre`, `curso_objetivo`, `curso_descrip`, `curso_video`, `entrenamiento_id`
    FROM `pg_cursos`
    WHERE `curso_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);
  //var_dump($query);
  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getCursoByEntrenamiento($code, $db){
  $sql = 'SELECT `curso_id`, `curso_nombre`, `curso_objetivo`, `curso_descrip`, `curso_video`, `entrenamiento_id`
    FROM `pg_cursos`
    WHERE `entrenamiento_id` = %d
    ORDER BY `curso_id` DESC
    LIMIT 1; ';
  $query = sprintf($sql, $code);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newCurso($nombre, $objetivo, $descrip, $video, $master, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $objetivo = htmlentities($objetivo, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'curso_nombre'   => $nombre,
    'curso_objetivo' => $objetivo,
    'curso_descrip'  => $descrip,
    'curso_video'  => $video,
    'entrenamiento_id'  => $master,
    'curso_fch_creacion' => $db->now()
  );

  $id = $db->insert ('pg_cursos', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delCurso($code, $db){
  $db->where('curso_id', $code);

  $rpta = ( $db->delete('pg_cursos') ) ? 2 : -1;
  return $rpta;
}

function updateCurso($code, $nombre, $objetivo, $descrip, $video, $master, $db){
  $nombre   = htmlentities($nombre, ENT_QUOTES);
  $objetivo = htmlentities($objetivo, ENT_QUOTES);
  $descrip  = htmlentities($descrip, ENT_QUOTES);

  $data = Array (
    'curso_nombre'   => $nombre,
    'curso_objetivo' => $objetivo,
    'curso_descrip'  => $descrip,
    'curso_video'    => $video,
    'entrenamiento_id'  => $master
  );

  $db->where ('curso_id', $code);

  $rpta  = ( $db->update ('pg_cursos', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  Temas
**/
function getTemasByUser($user_id, $master_id, $db){
  $sql = 'SELECT `t1`.`tema_id`, `t1`.`tema_nombre`, `t1`.`tema_objetivo`, `t1`.`tema_descrip`, `t1`.`tema_tipo_material`, `t1`.`tema_material`, `t1`.`curso_id`,
    IFNULL(`t2`.`status`, "No") `status`
    FROM `pg_temas` `t1`
    LEFT JOIN (
      SELECT *
        FROM (SELECT `enroll_id`, `master_tbl`, `master_id`, `status`
        FROM pg_enroll
        WHERE `user_id` = %d AND `master_tbl` = "pg_temas"
        ORDER BY `enroll_id` DESC) `tx`
        GROUP BY `tx`.`master_id`
    ) `t2`
      ON `t2`.`master_id` = `t1`.`tema_id`
    WHERE `t1`.`curso_id` = %d
    ORDER BY `t1`.`tema_id` ASC
    LIMIT 2000 ;';
  $query = sprintf($sql, $user_id, $master_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getTemas($code = 0, $db){
  $query = 'SELECT `tema_id`, `tema_nombre`, `tema_objetivo`, `tema_descrip`, `tema_tipo_material`, `tema_material`, `curso_id`
    FROM `pg_temas`
    WHERE `curso_id` = ?
    ORDER BY `tema_id` ASC
    LIMIT 2000; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getTemaById($code, $db){
  $query = 'SELECT `tema_id`, `tema_nombre`, `tema_objetivo`, `tema_descrip`, `tema_tipo_material`, `tema_material`, `curso_id`
    FROM `pg_temas`
    WHERE `tema_id` = ? LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newTema($nombre, $objetivo, $descrip, $tMaterial, $material, $master, $db){
  $nombre    = htmlentities($nombre, ENT_QUOTES);
  $objetivo  = htmlentities($objetivo, ENT_QUOTES);
  $descrip   = htmlentities($descrip, ENT_QUOTES);
  $tMaterial = htmlentities($tMaterial, ENT_QUOTES);
  $material  = htmlentities($material, ENT_QUOTES);

  $data = Array (
    'tema_nombre'   => $nombre,
    'tema_objetivo' => $objetivo,
    'tema_descrip'  => $descrip,
    'tema_tipo_material' => $tMaterial,
    'tema_material' => $material,
    'curso_id'      => $master
  );

  $id = $db->insert ('pg_temas', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delTema($code, $db){
  $db->where('tema_id', $code);

  $rpta = ( $db->delete('pg_temas') ) ? 2 : -1;
  return $rpta;
}

function updateTema($code, $nombre, $objetivo, $descrip, $tMaterial, $material, $master, $db){
  $nombre    = htmlentities($nombre, ENT_QUOTES);
  $objetivo  = htmlentities($objetivo, ENT_QUOTES);
  $descrip   = htmlentities($descrip, ENT_QUOTES);
  $tMaterial = htmlentities($tMaterial, ENT_QUOTES);
  $material  = htmlentities($material, ENT_QUOTES);

  $data = Array (
    'tema_nombre'   => $nombre,
    'tema_objetivo' => $objetivo,
    'tema_descrip'  => $descrip,
    'tema_tipo_material' => $tMaterial,
    'tema_material' => $material,
    'curso_id'      => $master
  );

  $db->where ('tema_id', $code);

  $rpta  = ( $db->update ('pg_temas', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  Novedades
**/

function getNovedadesByPerfil($user_id, $perfil_novedades, $db){
  $perfil_novedades = (strlen($perfil_novedades)>0) ? $perfil_novedades : '-1';
  $sql = 'SELECT `t1`.`novedad_id`, `t1`.`novedad_nombre`, `t1`.`novedad_logo`,
    IFNULL(t4.suma, 0) `avance`
    FROM `pg_novedades` `t1`
    INNER JOIN (
      SELECT SUM(t3.hito_porcentaje) `suma`
      FROM `pg_puntos` `t2`
      LEFT JOIN `pg_hitos` `t3` ON `t3`.`hito_id` = `t2`.`hito_id`
      WHERE `t2`.`puntos_estado` = "Ganado"
      AND `t2`.`user_id` = %d
      AND `t2`.`puntos_master_tabla` IN ("pg_novedades", "pg_questions")
    ) t4 ON 1=1
    AND `t1`.`novedad_id` IN ( %s )
    ORDER BY t1.novedad_id DESC
    LIMIT 2000; ';
  $query = sprintf($sql, $user_id, $perfil_novedades);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getNovedadesLista($user_id, $limit, $db){
  // Falta verificar integridad de los masters table & id
  $sql = 'SELECT `t1`.`novedad_id`, `t1`.`novedad_nombre`, `t1`.`novedad_logo`,
    IFNULL(t4.suma, 0) `avance`
    FROM `pg_novedades` `t1`
    LEFT JOIN (
      SELECT SUM(t3.hito_porcentaje) `suma`
      FROM `pg_puntos` `t2`
      LEFT JOIN `pg_hitos` `t3` ON `t3`.`hito_id` = `t2`.`hito_id`
      WHERE `t2`.`puntos_estado` = "Ganado"
      AND `t2`.`user_id` = %d
      AND `t2`.`puntos_master_tabla` IN ("pg_novedades", "pg_questions")
    ) t4 ON 1=1
    ORDER BY t1.novedad_id DESC
    LIMIT %d; ';
  $query = sprintf($sql, $user_id, $limit);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getNovedades($limit, $db){
  $sql = 'SELECT `novedad_id`, `novedad_nombre`, `novedad_objetivo`, `novedad_descrip`, `novedad_tipo_material`, `novedad_material`, `novedad_logo`
    FROM `pg_novedades`
    ORDER BY `novedad_id` DESC
    LIMIT %d; ';
  $query = sprintf($sql, $limit);

  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getNovedadById($code, $db){
  $query = 'SELECT `novedad_id`, `novedad_nombre`, `novedad_objetivo`, `novedad_descrip`, `novedad_tipo_material`, `novedad_material`
    FROM `pg_novedades`
    WHERE `novedad_id` = ? LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newNovedad($nombre, $objetivo, $descrip, $tMaterial, $material, $db){
  $nombre    = htmlentities($nombre, ENT_QUOTES);
  $objetivo  = htmlentities($objetivo, ENT_QUOTES);
  $descrip   = htmlentities($descrip, ENT_QUOTES);
  $tMaterial = htmlentities($tMaterial, ENT_QUOTES);
  $material  = htmlentities($material, ENT_QUOTES);

  $data = Array (
    'novedad_nombre'   => $nombre,
    'novedad_objetivo' => $objetivo,
    'novedad_descrip'  => $descrip,
    'novedad_tipo_material' => $tMaterial,
    'novedad_material' => $material,
    'novedad_fch_at'   => $db->now()
  );

  $id = $db->insert ('pg_novedades', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delNovedad($code, $db){
  $db->where('novedad_id', $code);

  $rpta = ( $db->delete('pg_novedades') ) ? 2 : -1;
  return $rpta;
}

function updateNovedad($code, $nombre, $objetivo, $descrip, $tMaterial, $material, $db){
  $nombre    = htmlentities($nombre, ENT_QUOTES);
  $objetivo  = htmlentities($objetivo, ENT_QUOTES);
  $descrip   = htmlentities($descrip, ENT_QUOTES);
  $tMaterial = htmlentities($tMaterial, ENT_QUOTES);
  $material  = htmlentities($material, ENT_QUOTES);

  $data = Array (
    'novedad_nombre'   => $nombre,
    'novedad_objetivo' => $objetivo,
    'novedad_descrip'  => $descrip,
    'novedad_tipo_material' => $tMaterial,
    'novedad_material' => $material
  );

  $db->where ('novedad_id', $code);

  $rpta  = ( $db->update ('pg_novedades', $data) ) ? 2 : -1;
  return $rpta;
}

/**
  Preguntas
**/
function getPreguntas($code = 0, $db){
  $query = 'SELECT `pregunta_id`, `pregunta`, `respuesta_1`, `respuesta_2`, `respuesta_3`, `respuesta_4`, `curso_id`
    FROM `pg_preguntas`
    WHERE `curso_id` = ?
    ORDER BY `pregunta_id` ASC
    LIMIT 2000; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getPreguntaById($code, $db){
  $query = 'SELECT `pregunta_id`, `pregunta`, `respuesta_1`, `respuesta_2`, `respuesta_3`, `respuesta_4`, `curso_id`
    FROM `pg_preguntas`
    WHERE `pregunta_id` = ? LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newPregunta($pregunta, $rpta1, $rpta2, $rpta3, $rpta4, $master, $db){
  $pregunta = htmlentities($pregunta, ENT_QUOTES);
  $rpta1    = htmlentities($rpta1, ENT_QUOTES);
  $rpta2    = htmlentities($rpta2, ENT_QUOTES);
  $rpta3    = htmlentities($rpta3, ENT_QUOTES);
  $rpta4    = htmlentities($rpta4, ENT_QUOTES);

  $data = Array (
    'pregunta'    => $pregunta,
    'respuesta_1' => $rpta1,
    'respuesta_2' => $rpta2,
    'respuesta_3' => $rpta3,
    'respuesta_4' => $rpta4,
    'curso_id'    => $master
  );

  $id = $db->insert ('pg_preguntas', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delPregunta($code, $db){
  $db->where('pregunta_id', $code);

  $rpta = ( $db->delete('pg_preguntas') ) ? 2 : -1;
  return $rpta;
}

function updatePregunta($code, $pregunta, $rpta1, $rpta2, $rpta3, $rpta4, $master, $db){
  $pregunta = htmlentities($pregunta, ENT_QUOTES);
  $rpta1    = htmlentities($rpta1, ENT_QUOTES);
  $rpta2    = htmlentities($rpta2, ENT_QUOTES);
  $rpta3    = htmlentities($rpta3, ENT_QUOTES);
  $rpta4    = htmlentities($rpta4, ENT_QUOTES);

  $data = Array (
    'pregunta'    => $pregunta,
    'respuesta_1' => $rpta1,
    'respuesta_2' => $rpta2,
    'respuesta_3' => $rpta3,
    'respuesta_4' => $rpta4,
    'curso_id'    => $master
  );

  $db->where ('pregunta_id', $code);

  $rpta  = ( $db->update ('pg_preguntas', $data) ) ? 2 : -1;
  return $rpta;
}

function getPreguntasRnd($curso_id, $limit, $db){
  $query = 'SELECT `pregunta_id`, `pregunta`, `respuesta_1`, `respuesta_2`, `respuesta_3`, `respuesta_4`, `curso_id`
    FROM `pg_preguntas`
    WHERE `curso_id` = ?
    ORDER BY RAND()
    LIMIT ?; ';

  $params = Array($curso_id, $limit);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

/**
  Questions
**/
function getQuestions($master_table, $master_id, $db){
  $sql = 'SELECT `question_id`, `question_type`, `question`, `answer_1`, `answer_2`, `answer_3`, `answer_4`, `master_table`, `master_id`
    FROM `pg_questions`
    WHERE `master_table` = "%s"
    AND `master_id` = %d
    ORDER BY `question_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $master_table, $master_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getQuestionById($code, $db){
  $sql = 'SELECT `question_id`, `question_type`, `question`, `answer_1`, `answer_2`, `answer_3`, `answer_4`, `master_table`, `master_id`
    FROM `pg_questions`
    WHERE `question_id` = %d
    LIMIT 1; ';
  $query = sprintf($sql, $code);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newQuestion($question_type, $question, $answer_1, $answer_2, $answer_3, $answer_4, $master_table, $master_id, $db){
  $question = htmlentities($question, ENT_QUOTES);
  $answer_1 = htmlentities($answer_1, ENT_QUOTES);
  $answer_2 = htmlentities($answer_2, ENT_QUOTES);
  $answer_3 = htmlentities($answer_3, ENT_QUOTES);
  $answer_4 = htmlentities($answer_4, ENT_QUOTES);

  $data = Array (
    'question_type' => $question_type,
    'question'      => $question,
    'answer_1'      => $answer_1,
    'answer_2'      => $answer_2,
    'answer_3'      => $answer_3,
    'answer_4'      => $answer_4,
    'master_table'  => $master_table,
    'master_id'     => $master_id
  );

  $id = $db->insert ('pg_questions', $data);
  $status = ($id) ? MSG_OK : MSG_ERROR;
  $info   = ($id) ? $id    : $db->getLastError();

  $rpta = Array ('status' => $status, 'info' => $info);
  return $rpta;
}

function updateQuestion($code, $question_type, $question, $answer_1, $answer_2, $answer_3, $answer_4, $master_table, $master_id, $db){
  $question = htmlentities($question, ENT_QUOTES);
  $answer_1 = htmlentities($answer_1, ENT_QUOTES);
  $answer_2 = htmlentities($answer_2, ENT_QUOTES);
  $answer_3 = htmlentities($answer_3, ENT_QUOTES);
  $answer_4 = htmlentities($answer_4, ENT_QUOTES);

  $data = Array (
    'question_type' => $question_type,
    'question'      => $question,
    'answer_1'      => $answer_1,
    'answer_2'      => $answer_2,
    'answer_3'      => $answer_3,
    'answer_4'      => $answer_4,
    'master_table'  => $master_table,
    'master_id'     => $master_id
  );

  $db->where ('question_id', $code);

  $status  = ( $db->update ('pg_questions', $data) ) ? MSG_OK : MSG_ERROR;
  $info    = ($status === MSG_ERROR) ?  $db->getLastError()  :  '';

  $rpta = Array ('status' => $status, 'info' => $info);
  return $rpta;
}

function delQuestion($code, $db){
  $db->where('question_id', $code);

  $status = ( $db->delete('pg_questions') ) ? MSG_OK : MSG_ERROR;
  $info   = ($status === MSG_ERROR) ?  $db->getLastError()  :  '';

  $rpta = Array ('status' => $status, 'info' => $info);
  return $rpta;
}

function getQuestionsRnd($master_table, $master_id, $limit, $db){
  $sql = 'SELECT `question_id`, `question_type`, `question`, `answer_1`, `answer_2`, `answer_3`, `answer_4`, `master_table`, `master_id`
    FROM `pg_questions`
    WHERE `master_table` = "%s"
    AND `master_id` = %d
    ORDER BY RAND()
    LIMIT %d; ';
  $query = sprintf($sql, $master_table, $master_id, $limit);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

/**
  Juegos
**/

function getJuegosByUser($user_id, $master_id, $db){
  $sql = 'SELECT *
    FROM (SELECT `enroll_id`, `master_tbl`, `master_id`, `status`
      FROM `pg_enroll`
      WHERE `user_id` = %d AND `master_tbl` IN ("pg_juegos1", "pg_juegos2", "pg_juegos3") AND `master_id` = %d
      ORDER BY `enroll_id` DESC) `tx`
    GROUP BY `tx`.`master_tbl` ;';
  $query = sprintf($sql, $user_id, $master_id);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getJuegos($code = 0, $tipoJuego = 1, $db){
  $query = 'SELECT `juego_id`, `juego_pregunta`, `juego_respuesta`, `juego_respuesta_data`, `juego_distractor1`, `juego_distractor1_data`, `juego_distractor2`, `juego_distractor2_data`, `juego_distractor3`, `juego_distractor3_data`, `juego_pista`, `juego_feedback`, `tema_id`,
    `juego_titulo`, `juego_descrip`, `juego_portada`, `juego_fondo`
    FROM `pg_juegos`
    WHERE `tema_id` = ?
    AND `juego_tipo` = ?
    ORDER BY `juego_id` ASC
    LIMIT 2000; ';

  $params = Array($code, $tipoJuego);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function getJuegoById($code, $db){
  $query = 'SELECT `juego_id`, `juego_pregunta`, `juego_respuesta`, `juego_respuesta_data`, `juego_distractor1`, `juego_distractor1_data`, `juego_distractor2`, `juego_distractor2_data`, `juego_distractor3`, `juego_distractor3_data`, `juego_pista`, `juego_feedback`, `tema_id`,
    `juego_titulo`, `juego_descrip`, `juego_portada`, `juego_fondo`
    FROM `pg_juegos`
    WHERE `juego_id` = ?
    LIMIT 1; ';

  $params = Array($code);
  $result = $db->rawQuery($query, $params);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

function newJuego($tJuego, $pregunta, $respuesta, $distractor1, $distractor2, $distractor3, $pista, $feedback, $portada, $fondo, $master, $db){
  $pregunta    = htmlentities($pregunta, ENT_QUOTES);
  $respuesta   = htmlentities($respuesta, ENT_QUOTES);
  $distractor1 = htmlentities($distractor1, ENT_QUOTES);
  $distractor2 = htmlentities($distractor2, ENT_QUOTES);
  $distractor3 = htmlentities($distractor3, ENT_QUOTES);
  $pista       = htmlentities($pista, ENT_QUOTES);
  $feedback    = htmlentities($feedback, ENT_QUOTES);
  $master      = htmlentities($master, ENT_QUOTES);
  $titulo      = ''; //htmlentities($titulo, ENT_QUOTES);
  $descrip     = ''; //htmlentities($descrip, ENT_QUOTES);
  $portada     = htmlentities($portada, ENT_QUOTES);
  $fondo       = htmlentities($fondo, ENT_QUOTES);

  $data = Array (
    'juego_tipo'        => $tJuego,
    'juego_pregunta'    => $pregunta,
    'juego_respuesta'   => $respuesta,
    'juego_distractor1' => $distractor1,
    'juego_distractor2' => $distractor2,
    'juego_distractor3' => $distractor3,
    'juego_pista'       => $pista,
    'juego_feedback'    => $feedback,
    'tema_id'           => $master,
    'juego_titulo'      => $titulo,
    'juego_descrip'     => $descrip,
    'juego_portada'     => $portada,
    'juego_fondo'       => $fondo
  );

  $id = $db->insert ('pg_juegos', $data);
  $rpta = ($id) ? $id : $db->getLastError();

  return $rpta;
}

function delJuego($code, $db){
  $db->where('juego_id', $code);

  $rpta = ( $db->delete('pg_juegos') ) ? 2 : -1;
  return $rpta;
}

function updateJuego($code, $tJuego, $pregunta, $respuesta, $distractor1, $distractor2, $distractor3, $pista, $feedback, $portada, $fondo, $master, $db){
  $pregunta    = htmlentities($pregunta, ENT_QUOTES);
  $respuesta   = htmlentities($respuesta, ENT_QUOTES);
  $distractor1 = htmlentities($distractor1, ENT_QUOTES);
  $distractor2 = htmlentities($distractor2, ENT_QUOTES);
  $distractor3 = htmlentities($distractor3, ENT_QUOTES);
  $pista       = htmlentities($pista, ENT_QUOTES);
  $feedback    = htmlentities($feedback, ENT_QUOTES);
  $master      = htmlentities($master, ENT_QUOTES);
  $titulo      = ''; //htmlentities($titulo, ENT_QUOTES);
  $descrip     = ''; //htmlentities($descrip, ENT_QUOTES);
  $portada     = htmlentities($portada, ENT_QUOTES);
  $fondo       = htmlentities($fondo, ENT_QUOTES);

  $data = Array (
    'juego_tipo'        => $tJuego,
    'juego_pregunta'    => $pregunta,
    'juego_respuesta'   => $respuesta,
    'juego_distractor1' => $distractor1,
    'juego_distractor2' => $distractor2,
    'juego_distractor3' => $distractor3,
    'juego_pista'       => $pista,
    'juego_feedback'    => $feedback,
    'tema_id'           => $master,
    'juego_titulo'      => $titulo,
    'juego_descrip'     => $descrip,
    'juego_portada'     => $portada,
    'juego_fondo'       => $fondo
  );

  $db->where ('juego_id', $code);

  $rpta  = ( $db->update ('pg_juegos', $data) ) ? 2 : -1;
  return $rpta;
}

function getJuegoRnd($tema_id, $juego_tipo, $limit, $repetidos, $db){
  $sql = 'SELECT `juego_id`, `juego_pregunta`, `juego_respuesta`, `juego_distractor1`, `juego_distractor2`, `juego_distractor3`, `juego_pista`, `juego_feedback`, `tema_id`,
    `juego_titulo`, `juego_descrip`, `juego_portada`, `juego_fondo`,
    FLOOR(1 + RAND() * `juego_id`) `rand_ind`
    FROM `pg_juegos`
    WHERE `tema_id`  = %d
    AND `juego_tipo` = %s
    AND `juego_id` NOT IN( %s )
    ORDER BY `rand_ind` DESC
    LIMIT %d ;';

  $query = sprintf($sql, $tema_id, $juego_tipo, $repetidos, $limit);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

/**
  Ejercicio
**/

function getAccionesRnd($tema_id, $limit, $db){
  $sql = 'SELECT * FROM (
    (SELECT `juego_id`, `juego_ganancia1` AS `accion`, "G1" AS `grupo`
        FROM `pg_juegos`
        WHERE `tema_id` = %d
        ORDER BY RAND()
        LIMIT 3)

    UNION
    (SELECT `juego_id`, `juego_ganancia2` AS `accion`, "G2" AS `grupo`
        FROM `pg_juegos`
        WHERE `tema_id` = %d
        ORDER BY RAND()
        LIMIT 3)

    UNION
    (SELECT `juego_id`, `juego_perdida1` AS `accion`, "P1" AS `grupo`
        FROM `pg_juegos`
        WHERE `tema_id` = %d
        ORDER BY RAND()
        LIMIT 3)

    UNION
    (SELECT `juego_id`, `juego_perdida2` AS `accion`, "P2" AS `grupo`
        FROM `pg_juegos`
        WHERE `tema_id` = %d
        ORDER BY RAND()
        LIMIT 3)
    ) AS `Acciones`
    ORDER BY RAND()
    LIMIT %d ; ';

  $query = sprintf($sql, $tema_id, $tema_id, $tema_id, $tema_id, $limit);
  $result = $db->rawQuery($query);

  $rpta = ( count($result)>0 ) ? $result : array();
  return $rpta;
}

/**
  Otros
**/

function getCourseById($courseId, $db){
  $sql = 'SELECT `t1`.`producto_nombre`,
    `t2`.`user_id`
    FROM `vedu_productos` `t1`
    INNER JOIN `edu_autores` `t2` ON `t2`.`producto_id` = `t1`.`producto_id`
    WHERE `t1`.`producto_id` = "%d"
    LIMIT 1; ';
  $query = sprintf($sql, $courseId);

  $db->setQuery($query);
  $resultados = $db->loadObjectList();

  $rpta = count($resultados) ? $resultados : NULL;

  return $rpta;
}

function getSessionsByCourse($courseId, $db){
  $sql = 'SELECT `t1`.`class_titulo` `class_titulo`, `t1`.`class_video`, `t1`.`class_id`, `t1`.`class_video_url`,
    IFNULL(`t1`.`class_video_provider`, "") `class_video_provider`,
    IFNULL(`t2`.`classre_id`, 0) `class_view`
    FROM `vedu_clasess` `t1`
    LEFT JOIN `edu_clases_revisado` `t2` ON `t2`.`producto_id` = `t1`.`producto_id`
    WHERE `t1`.`producto_id` = "%d"
    GROUP BY `class_id`
    ORDER BY `class_id` ASC
    LIMIT 2000; ';
  $query = sprintf($sql, $courseId);

  $db->setQuery($query);
  $resultados = $db->loadObjectList();

  $rpta = count($resultados) ? $resultados : NULL;

  return $rpta;
}

<?php
/**
  Funciones Utiles
**/
function isSelected($value, $current){
  $rpta = ($value == $current) ? ' selected' : '';
  return $rpta;
}

function isChecked($value, $current){
  $rpta = ($value == $current) ? ' checked' : '';
  return $rpta;
}

function checkSiNo($value){
  $rpta = ($value == 'Si') ? ' checked' : '';
  return $rpta;
}

function valueToSiNo($value){
  $rpta = ($value == 'true') ? 'Si' : 'No';
  return $rpta;
}

function genPathPicture($filename, $folder = '', $default = 'default.jpg'){
  $file = ( strlen($filename) > 3 ) ? $filename : $default;
  return S3_PATH. $folder. $file;
}

function genBreadCrumbs($type, $profundidad, $game, $arrGame, $resultados){
  $html = '';
  if ($type = 'dashboard'){
    $html = '<li><a href="dashboard.php">Dashboard</a></li>';
    if (count($resultados)){
      if ($profundidad === 2){
        $entrenamiento_id = encrypt($resultados[0]['entrenamiento_id'], $_SESSION['k']);
        $html .= '<li><a href="entrenamiento.php?pid='. $entrenamiento_id. '">'. html_entity_decode($resultados[0]['entrenamiento_nombre']). '</a></li>';
        $html .= '<li class="current">'. html_entity_decode($resultados[0]['curso_nombre']). '</li>';
      }
      if ($profundidad === 3){
        $entrenamiento_id = encrypt($resultados[0]['entrenamiento_id'], $_SESSION['k']);
        $curso_id = encrypt($resultados[0]['curso_id'], $_SESSION['k']);
        $html .= '<li><a href="entrenamiento.php?pid='. $entrenamiento_id. '">'. html_entity_decode($resultados[0]['entrenamiento_nombre']). '</a></li>';
        //$html .= '<li><a href="curso.php?pid='. $curso_id. '">'. html_entity_decode($resultados[0]['curso_nombre']). '</a></li>';
        $html .= '<li class="current">'. html_entity_decode($resultados[0]['tema_nombre']). '</li>';
      }
      if ($profundidad === 4){
        $entrenamiento_id = encrypt($resultados[0]['entrenamiento_id'], $_SESSION['k']);
        $curso_id = encrypt($resultados[0]['curso_id'], $_SESSION['k']);
        $tema_id = encrypt($resultados[0]['tema_id'], $_SESSION['k']);
        $html .= '<li><a href="entrenamiento.php?pid='. $entrenamiento_id. '">'. html_entity_decode($resultados[0]['entrenamiento_nombre']). '</a></li>';
        //$html .= '<li><a href="curso.php?pid='. $curso_id. '">'. html_entity_decode($resultados[0]['curso_nombre']). '</a></li>';
        $html .= '<li><a href="tema.php?pid='. $tema_id. '">'. html_entity_decode($resultados[0]['tema_nombre']). '</a></li>';
        $html .= '<li class="current">'. $game. '</li>';
      }
      if ($profundidad === 5){
        $entrenamiento_id = encrypt($resultados[0]['entrenamiento_id'], $_SESSION['k']);
        $curso_id = encrypt($resultados[0]['curso_id'], $_SESSION['k']);
        $tema_id = encrypt($resultados[0]['tema_id'], $_SESSION['k']);
        $html .= '<li><a href="entrenamiento.php?pid='. $entrenamiento_id. '">'. html_entity_decode($resultados[0]['entrenamiento_nombre']). '</a></li>';
        //$html .= '<li><a href="curso.php?pid='. $curso_id. '">'. html_entity_decode($resultados[0]['curso_nombre']). '</a></li>';
        $html .= '<li><a href="tema.php?pid='. $tema_id. '">'. html_entity_decode($resultados[0]['tema_nombre']). '</a></li>';
        $html .= '<li><a href="'. $arrGame['url']. '">'. $arrGame['link']. '</a></li>';
        $html .= '<li class="current">JUGANDO</li>';
      }
    }
  }
  return $html;
}

/**
  Usuarios
**/
function genUsuariosTRSmart($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $n = 0;
    $check = '';
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id    = $item['usuario_id'];
      $super = $item['supervisor_id'];
      $rol   = $item['usuario_rol'];
      $perfil  = $item['usuario_perfil'];
      $href    = $item['distributor_id']. ','. $item['subsidiary_id'];

      $html .= '<tr>';
      //$html .= '<td><a rel="'. $id. '" href="#" data="'. $item['usuario_email']. '" class="edit">'. ucwords($item['usuario_nombre']). '</a></td>';
      $html .= '<td>'. ucwords($item['usuario_nombre']). '</td>';
      $html .= '<td>'. $item['usuario_doc']. '</td>';
      $html .= '<td>'. $item['distributor_name']. '</td>';
      $html .= '<td>'. $item['subsidiary_name']. '</td>';
      $html .= '<td>'. $item['perfil_name']. '</td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $item['list_id']. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Eliminar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genUsuariosTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $n = 0;
    $check = '';
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id    = $item['usuario_id'];
      $super = $item['supervisor_id'];
      $rol   = $item['usuario_rol'];
      $perfil  = $item['usuario_perfil'];
      $href    = $item['distributor_id']. ','. $item['subsidiary_id'];
      $check   = 'chkActivar'. $n++;
      $active  = checkSiNo($item['usuario_activo']);

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" href="#" data="'. $item['usuario_email']. '" class="edit">'. ucwords($item['usuario_nombre']). '</a></td>';
      $html .= '<td>'. $item['usuario_doc']. '</td>';
      $html .= '<td>'. $item['distributor_name']. '</td>';
      $html .= '<td>'. $item['subsidiary_name']. '</td>';
      $html .= '<td>'. $item['supersivor_nombre']. '</td>';
      $html .= '<td>'. $item['perfil_name']. '</td>';
      $html .= '<td><div class="switch round"><input rel="'. $id. '" id="'. $check. '" type="checkbox" class="activar"'. $active. '><label for="'. $check. '"><span class="switch-on">Sí</span><span class="switch-off">No</span></label></div></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" class="pLeft5 tip-top pass" title="Cambiar Clave">';
      $html .= '  <i class="fa fa-key fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" href="'. $href. '" class="pLeft5 tip-top location" title="Cambiar locación">';
      $html .= '  <i class="fa fa-map-marker fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" href="'. $super. '" class="pLeft5 tip-top supervisor" title="Cambiar Supervisor">';
      $html .= '  <i class="fa fa-shield fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" href="'. $perfil. '" class="pLeft5 tip-top perfil" title="Cambiar Perfil">';
      $html .= '  <i class="fa fa-sitemap fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Eliminar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genUsuariosTRRoles($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $n = 0;
    $check = '';
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id    = $item['usuario_id'];
      $super = $item['supervisor_id'];
      $rol   = $item['usuario_rol'];
      $perfil  = $item['usuario_perfil'];
      $href    = $item['distributor_id']. ','. $item['subsidiary_id'];
      $check   = 'chkActivar'. $n++;
      $active  = checkSiNo($item['usuario_activo']);

      $supervisor  = '  <a rel="'. $id. '" href="'. $item['perfil_sup']. '" class="pLeft5 tip-top perfil" title="Cambiar perfil a supervisar">';
      $supervisor .= '  <i class="fa fa-sitemap fa-lg"></i></a>';

      $jefe   = '  <a rel="'. $id. '" href="'. $super. '" class="pLeft5 tip-top supervisor" title="Cambiar Supervisor">';
      $jefe  .= '  <i class="fa fa-shield fa-lg"></i></a>';
      $jefe   = '';

      $admin  = '  <a rel="'. $id. '" href="'. $item['supervisores']. '" class="pLeft5 tip-top jefe" title="Cambiar supervisores del jefe">';
      $admin .= '  <i class="fa fa-users fa-lg"></i></a>';

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" href="#" data="'. $item['usuario_email']. '" class="edit">'. ucwords($item['usuario_nombre']). '</a></td>';
      $html .= '<td>'. $item['usuario_doc']. '</td>';
      $html .= '<td>'. $item['distributor_name']. '</td>';
      $html .= '<td>'. $item['subsidiary_name']. '</td>';
      $html .= '<td>'. convertRol($item['usuario_rol']). '</td>';
      $html .= '<td><div class="switch round"><input rel="'. $id. '" id="'. $check. '" type="checkbox" class="activar"'. $active. '><label for="'. $check. '"><span class="switch-on">Sí</span><span class="switch-off">No</span></label></div></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" class="pLeft5 tip-top pass" title="Cambiar Clave">';
      $html .= '  <i class="fa fa-key fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" href="'. $href. '" class="pLeft5 tip-top location" title="Cambiar locación">';
      $html .= '  <i class="fa fa-map-marker fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" href="'. $rol. '" class="pLeft5 tip-top rol" title="Cambiar Rol">';
      $html .= '  <i class="fa fa-wrench fa-lg"></i></a>';
      $html .= ( $item['usuario_rol'] == ROL_SUPERVISOR || $item['usuario_rol'] == ROL_JEFE )  ?  $supervisor  :  '' ;
      $html .= ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_JEFE )  ?  $jefe  :  '' ;
      $html .= ( $_SESSION['rol'] == ROL_ADMIN && $item['usuario_rol'] == ROL_JEFE )  ?  $admin  :  '' ;
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Eliminar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function convertRol($rol){
  $rpta = '-';
  if ($rol == ROL_USER)       { $rpta = 'Participante'; }
  if ($rol == ROL_SUPERVISOR) { $rpta = 'Supervisor'; }
  if ($rol == ROL_JEFE)       { $rpta = 'Jefe'; }
  if ($rol == ROL_ADMIN)      { $rpta = 'Administrador'; }

  return $rpta;
}

function genUsuariosTRSmall($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $n = 0;
    $check = '';
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id    = $item['usuario_id'];
      $super = $item['supervisor_id'];
      $rol   = $item['usuario_rol'];
      $href  = $item['distributor_id']. ','. $item['subsidiary_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" href="#" title="'. $item['usuario_email']. '" class="edit">'. ucwords($item['usuario_nombre']). '</a></td>';
      $html .= '<td>'. $item['usuario_doc']. '</td>';
      $html .= '<td>'. $item['distributor_name']. '</td>';
      $html .= '<td>'. $item['subsidiary_name']. '</td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myCambiar" class="pLeft5 tip-top" title="Cambiar Clave">';
      $html .= '  <i class="fa fa-key fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genUsuariosSearchTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $n = 0;
    $check = '';
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id    = $item['usuario_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" href="#" title="'. $item['usuario_email']. '">'. ucwords($item['usuario_nombre']). '</a></td>';
      $html .= '<td>'. $item['usuario_doc']. '</td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myParticipante" class="pLeft5 tip-top" title="Agregar Miembro">';
      $html .= '  <i class="fa fa-plus-circle fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genUsuariosSearchJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]       = intval($item['usuario_id']);
      $post["email"]    = html_entity_decode($item['usuario_email']);
      $post["nombre"]   = html_entity_decode($item['usuario_nombre']);
      $post["doc"]      = html_entity_decode($item['usuario_doc']);

      array_push($response, $post);
    }
  }

  return $response;
}

function genListaUsuarios($resultados){
  $html = '';
  if (count($resultados)){
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $html .= $item['usuario_id']. ',';
    }
  }
  $html = rtrim($html, ',');

  return $html;
}

function genRolOP($rol = ROL_SUPERVISOR){
  $rpta = '<option value="-1">Seleccionar ...</option>;
    <option value="'. ROL_USER. '">Participante</option>';
  if ( $rol == ROL_JEFE || $rol == ROL_ADMIN ){
    $rpta .=  '<option value="'. ROL_SUPERVISOR. '">Supervisor</option>';
  }
  if ( $rol == ROL_ADMIN ){
    $rpta .= '<option value="'. ROL_JEFE. '">Jefe</option>
      <option value="'. ROL_ADMIN. '">Administrador</option>';
  }
  return $rpta;
}

function genGeneroCHK($genero){
  $rpta = '';
  if ( $genero === 'Masculino' ){
    $rpta = '<input type="radio" value="Masculino" id="opSexH" name="opSex" checked="checked"><label for="opSexH">Masculino</label>
      <input type="radio" value="Femenino"  id="opSexF" name="opSex"><label for="opSexF">Femenino</label>';
  } else {
    $rpta = '<input type="radio" value="Masculino" id="opSexH" name="opSex"><label for="opSexH">Masculino</label>
      <input type="radio" value="Femenino"  id="opSexF" name="opSex" checked="checked"><label for="opSexF">Femenino</label>';
  }
  return $rpta;
}

function genUsuariosOP($resultados, $cur, $sel = true){
  $html = ($sel) ? '<option value="-1">Seleccionar ...</option>'  :  '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['usuario_id'], $cur);
      $html .= '<option value = "'. $item['usuario_id']. '" '. $sel. '>'. $item['usuario_nombre']. '</option>';
    }
  }

  return $html;
}

function genUserCargoOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';
  $resultados = array(
    2 => 'Representante de ventas',
    3 => 'Jefe de ventas',
    4 => 'Supervisors',
    5 => 'Vendedor'
  );

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['usuario_id'], $cur);
      $html .= '<option value = "'. $item['usuario_id']. '" '. $sel. '>'. $item['usuario_nombre']. '</option>';
    }
  }

  return $html;
}

function genUserTipoVendedorOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['usuario_id'], $cur);
      $html .= '<option value = "'. $item['usuario_id']. '" '. $sel. '>'. $item['usuario_nombre']. '</option>';
    }
  }

  return $html;
}


/**
  Busquedas
**/

function genResultadosLI($resultados){
  $html = '';

  if (is_array($resultados)){
    foreach($resultados as $item){
      //$link = genLinkBusqueda($item['tbl_master'], $item['tbl_master_id']);
      $link = $item['tbl_master']. '.php?pid='. encrypt($item['tbl_master_id'], $_SESSION['k']);
      $html .= '<li><a href="'. $link. '">'. $item['entrenamiento_nombre']. '</a> ('. $item['tbl_master']. ')</li>';
    }
  } else {
    $html = '<li>No hay resultados</li>';
  }

  return $html;
}

function genLinkBusqueda($tabla, $id){

  switch ($tabla) {
    case 'tbl_master':

      break;

    default:
      # code...
      break;
  }
}

/**
  Webservices
**/

function GenUserDataJS($resultados){
  $response = array();
  //if (count($resultados)){
    //foreach($resultados as $item){
      $post = array();
      $post["name"]    = 'Nombre del usuario';
      $post["dni"]     = '12345678';
      $post["socre"]   = 0;
      $post["status"]  = 'Activo';

      array_push($response, $post);
    //}
  //}

  return $response;
}

function genScoreUser($resultados){
  $response = array();
  //if (count($resultados)){
    //foreach($resultados as $item){
      $post = array();
      $post["dni"]     = '12345678';
      $post["response"]  = 'Ok';

      array_push($response, $post);
    //}
  //}

  return $response;
}

function SetRewards($resultados){
  $response = array();
  //if (count($resultados)){
    //foreach($resultados as $item){
      $post = array();
      $post["response"]  = 'Ok';

      array_push($response, $post);
    //}
  //}

  return $response;
}

/**
  Linaje
**/

function genNextLink($linaje, $resultados){
  $html = 'dashboard.php';
  $x = 0;
  $current = 0;
  $pre = '';
  if (count($resultados)){
    foreach($resultados as $item){
      if ($linaje == $item['linaje']){
        $current = $x;
        break;
      }
      //$pre = '::'. $linaje. '-'. $item['linaje'];
      $x++;
    }
  }
  // Es el ultimo
  if ($current === count($resultados) - 1 ){
    $html = 'dashboard.php';
  } else {
    // Si estoy en el mismo curso
    if ($resultados[$current]['curso_id'] === $resultados[$current + 1]['curso_id']){
      $next = $resultados[$current + 1]['tema_id'];
      $html = 'tema.php?pid='. encrypt($next, $_SESSION['k']);
    } else {
      // Cambia de curso
      if ($resultados[$current + 1]['curso_id'] != ""){
        //$next = $resultados[$current + 1]['curso_id'];
        //$html = 'curso.php?pid='. encrypt($next, $_SESSION['k']);
        $next = $resultados[$current + 1]['entrenamiento_id'];
        $html = 'entrenamiento.php?pid='. encrypt($next, $_SESSION['k']);
      } else {
        $html = 'dashboard.php';
      }
    }
  }
  return $html;
}

function genNextLinaje($linaje, $resultados){
  $rpta    = '';
  $next    = false;
  if (count($resultados)){
    foreach($resultados as $item){
      if ($next) { $rpta = $item['linaje']; break; }
      if ($linaje == $item['linaje']){ $next = true; }
    }
  }
  return $rpta;
}

/**
  REPORTE
**/

function genReporteUsuarioTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['entrenamiento_id'];

      $html .= '<tr>';
      $html .= '<td><div>'. $item['entrenamiento_nombre']. '</div></td>';
      $html .= '<td><div>'. $item['puntos']. '</div></td>';
      $html .= '<td><div>'. $item['max_puntaje']. '</div></td>';
      $html .= '<td><div>'. $item['avance']. '%</div></td>';
      $html .= '<td><div>'. $item['puntos_aciertos']. '%</div></td>';
      $html .= '<td><div>'. $item['trofeo_tipo']. '</div></td>';
      $html .= '</tr>';
    }
  }

  return $html;
}

function genReporteEntrenamientosTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    // recorre iten por item los resultados
    foreach($resultados as  $item){
      $id = $item['entrenamiento_id'];
      $usuarios   = $item['start'];
      $aprobados  = $item['finish'];
      $en_proceso = $usuarios - $aprobados;
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pAvances   = '0';

      // En Proceso
      $arr1 = explode(',', $item['start_ids']);
      $arr2 = explode(',', $item['finish_ids']);
      $arr3 = array_diff ($arr1, $arr2);
      $en_proceso_ids = implode($arr3, ',');

      // Sumatorias
      $inscritos    += $usuarios;
      $completados  += $aprobados;
      $entrenandose += $en_proceso;

      $html .= '<tr>';
      $html .= '<td><div>'. $item['entrenamiento_nombre']. '</div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['start_ids']. '" class="lista">'. $usuarios. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['finish_ids']. '" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="'. $en_proceso_ids. '" class="lista">'. $en_proceso. '</a></div></td>';
      $html .= '<td><div>'. $pAvances. '%</div></td>';
      $html .= '</tr>';
    }
  }
  $footer  = '<div class="panel callout radius">
    <h5>Tienes un TOTAL de %d participantes.</h5>
    <p><span class="verde">%d completaron el entrenamiento</span> y <span class="rojo">%d están entrenándose</span>.</p>
    </div>';
  $footer = sprintf($footer, $inscritos, $completados, $entrenandose);

  return '<tbody>'. $html. '</tbody></table>'. $footer;
}

function genResultadosEntrenamientosTRv2($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    // recorre iten por item los resultados
    foreach($resultados as  $item){
      $id = $item['entrenamiento_id'];

      $usuarios   = $item['nActivo'] + $item['nFinish'] + $item['nStart'];
      $aprobados  = $item['nFinish'];
      $proceso    = $item['nActivo'] + $item['nStart'];
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pProceso   = ($usuarios !== 0) ? round($proceso/$usuarios*100)  :  0;

      $html .= '<tr>';
      $html .= '<td><div><a href="resultados-temas.php" rel="'. encrypt($item['entrenamiento_id'], $_SESSION['k']). '" class="parent">'. $item['entrenamiento_nombre']. '</a></div></td>';
      //$html .= '<td><div><a href="#" rel="T#T#'. $item['entrenamiento_id'] .'" class="lista">'. $usuariosF. '</a></div></td>';
      //$html .= '<td><div><a href="#" rel="T#I#'. $item['entrenamiento_id'] .'" class="lista">'. $usuariosI. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="T#A#'. $item['entrenamiento_id'] .'" class="lista">'. $usuarios. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="T#F#'. $item['entrenamiento_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="T#P#'. $item['entrenamiento_id'] .'" class="lista">'. $proceso. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genResultadosEntrenamientosTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    // recorre iten por item los resultados
    foreach($resultados as  $item){
      $id = $item['entrenamiento_id'];
      $usuarios   = $item['activos'];
      $aprobados  = $item['finish'];
      $start      = $item['start'];
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pProceso   = ($usuarios !== 0) ? round($start/$usuarios*100)  :  0;

      $html .= '<tr>';
      $html .= '<td><div><a href="resultados-temas.php" rel="'. encrypt($item['entrenamiento_id'], $_SESSION['k']). '" class="parent">'. $item['entrenamiento_nombre']. '</a></div></td>';
      //$html .= '<td><div><a href="#" rel="T#T#'. $item['entrenamiento_id'] .'" class="lista">'. $usuariosF. '</a></div></td>';
      //$html .= '<td><div><a href="#" rel="T#I#'. $item['entrenamiento_id'] .'" class="lista">'. $usuariosI. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="T#A#'. $item['entrenamiento_id'] .'" class="lista">'. $item['activos']. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="T#F#'. $item['entrenamiento_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="T#P#'. $item['entrenamiento_id'] .'" class="lista">'. $item['start']. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genResultadosTemasTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    // recorre iten por item los resultados
    foreach($resultados as  $item){
      $id = $item['tema_id'];
      $usuarios   = $item['activos'];
      $aprobados  = $item['finish'];
      $start      = $item['start'];
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pProceso   = ($usuarios !== 0) ? round($start/$usuarios*100)  :  0;

      $html .= '<tr>';
      $html .= '<td><div>'. $item['tema_nombre']. '</div></td>';
      $html .= '<td><div><a href="#" rel="TE#A#'. $item['tema_id'] .'" class="lista">'. $item['activos']. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="TE#F#'. $item['tema_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="TE#P#'. $item['tema_id'] .'" class="lista">'. $start. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genReporteEAcumuladoCityTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['subsidiary_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['finish'];
      $en_proceso = $usuarios - $aprobados;
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pAvances   = '';

      // En Proceso
      $arr1 = explode(',', $item['user_ids']);
      $arr2 = explode(',', $item['finish']);
      $arr3 = array_diff ($arr1, $arr2);
      $en_proceso_ids = implode($arr3, ',');

      // Sumatorias
      $inscritos    += $usuarios;
      $completados  += $aprobados;
      $entrenandose += $en_proceso;

      $html .= '<tr>';
      $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['user_ids']. '" class="lista">'. $item['usuarios']. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['finish']. '" class="lista">'. $item['finish']. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="'. $en_proceso_ids. '" class="lista">'. $en_proceso. '</a></div></td>';
      $html .= '</tr>';
    }
  }

  $footer  = '<div class="panel callout radius">
    <h5>Tienes un TOTAL de %d participantes.</h5>
    <p><span class="verde">%d completaron el entrenamiento</span> y <span class="rojo">%d están entrenándose</span>.</p>
    </div>';
  $footer = sprintf($footer, $inscritos, $completados, $entrenandose);

  return '<tbody>'. $html. '</tbody></table>'. $footer;
}

function genReporteGeneralCityTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['subsidiary_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['finish']; // - $item['start'];
      $start      = $item['start'];
      $en_proceso = $usuarios - $aprobados;
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pProceso   = ($usuarios !== 0) ? round($start/$usuarios*100)  :  0;

      if ($usuarios == $aprobados){
        $activos = $usuarios;
        $inactivos = 0;
      } else {
        $activos    = $start + $aprobados;
        $inactivos  = $usuarios - $activos;

          if ($activos > $usuarios){
            $activos = $usuarios;
            $inactivos = 0;
          }
      }

      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="S#T#'. $item['subsidiary_id'] .'" class="lista">'. $usuarios. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="S#I#'. $item['subsidiary_id'] .'" class="lista">'. $inactivos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="S#A#'. $item['subsidiary_id'] .'" class="lista">'. $activos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="S#F#'. $item['subsidiary_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="S#P#'. $item['subsidiary_id'] .'" class="lista">'. $start. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genReporteGeneralCityTR_v2($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['subsidiary_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['fin_100'];
      $en_proceso = $item['en_proceso'];
      $no_empezaron = $item['no_empezaron'];

      $activos    = $aprobados + $en_proceso;
      $inactivos  = $no_empezaron;

      $pAprobados = ($activos !== 0) ? round($aprobados/$activos*100)  :  0;
      $pProceso   = ($activos !== 0) ? round($en_proceso/$activos*100)  :  0;

/*
      if ($usuarios == $aprobados){
        $activos = $usuarios;
        $inactivos = 0;
      } else {
        $activos    = $start + $aprobados;
        $inactivos  = $usuarios - $activos;

          if ($activos > $usuarios){
            $activos = $usuarios;
            $inactivos = 0;
          }
      }
*/
      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="S#T#'. $item['subsidiary_id'] .'" class="lista">'. $usuarios. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="S#I#'. $item['subsidiary_id'] .'" class="lista">'. $inactivos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="S#A#'. $item['subsidiary_id'] .'" class="lista">'. $activos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="S#F#'. $item['subsidiary_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="S#P#'. $item['subsidiary_id'] .'" class="lista">'. $en_proceso. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genReporteEAcumuladoDisTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['distributor_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['finish'];
      $en_proceso = $usuarios - $aprobados;
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pAvances   = '';

      // En Proceso
      $arr1 = explode(',', $item['user_ids']);
      $arr2 = explode(',', $item['finish']);
      $arr3 = array_diff ($arr1, $arr2);
      $en_proceso_ids = implode($arr3, ',');

      // Sumatorias
      $inscritos    += $usuarios;
      $completados  += $aprobados;
      $entrenandose += $en_proceso;

      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['user_ids']. '" class="lista">'. $item['usuarios']. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['finish']. '" class="lista">'. $item['finish']. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="'. $en_proceso_ids. '" class="lista">'. $en_proceso. '</a></div></td>';
      $html .= '</tr>';
    }
  }

  $footer  = '<div class="panel callout radius">
    <h5>Tienes un TOTAL de %d participantes.</h5>
    <p><span class="verde">%d completaron el entrenamiento</span> y <span class="rojo">%d están entrenándose</span>.</p>
    </div>';
  $footer = sprintf($footer, $inscritos, $completados, $entrenandose);

  return '<tbody>'. $html. '</tbody></table>'. $footer;
}

function genReporteGeneralDisTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['distributor_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['finish'];// - $item['start'];
      $start      = $item['start'];
      $en_proceso = $usuarios - $aprobados;
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pProceso   = ($usuarios !== 0) ? round($start/$usuarios*100)  :  0;

      $activos    = $start + $aprobados;
      $inactivos  = $usuarios - $activos;

      if ($usuarios == $aprobados){
        $activos = $usuarios;
        $inactivos = 0;
      } else {
        $activos    = $start + $aprobados;
        $inactivos  = $usuarios - $activos;

          if ($activos > $usuarios){
            $activos = $usuarios;
            $inactivos = 0;
          }
      }

      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="D#T#'. $item['distributor_id'] .'" class="lista">'. $item['usuarios']. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="D#I#'. $item['distributor_id'] .'" class="lista">'. $inactivos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="D#A#'. $item['distributor_id'] .'" class="lista">'. $activos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="D#F#'. $item['distributor_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="D#P#'. $item['distributor_id'] .'" class="lista">'. $start. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genReporteGeneralDisTR_v2($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['distributor_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['fin_100'];
      $en_proceso = $item['en_proceso'];
      $no_empezaron = $item['no_empezaron'];

      $activos    = $aprobados + $en_proceso;
      $inactivos  = $no_empezaron;

      $pAprobados = ($activos !== 0) ? round($aprobados/$activos*100)  :  0;
      $pProceso   = ($activos !== 0) ? round($en_proceso/$activos*100)  :  0;

/*
      if ($usuarios == $aprobados){
        $activos = $usuarios;
        $inactivos = 0;
      } else {
        $activos    = $start + $aprobados;
        $inactivos  = $usuarios - $activos;

          if ($activos > $usuarios){
            $activos = $usuarios;
            $inactivos = 0;
          }
      }
*/
      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="D#T#'. $item['distributor_id'] .'" class="lista">'. $usuarios. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="D#I#'. $item['distributor_id'] .'" class="lista">'. $inactivos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="D#A#'. $item['distributor_id'] .'" class="lista">'. $activos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="D#F#'. $item['distributor_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="D#P#'. $item['distributor_id'] .'" class="lista">'. $en_proceso. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genReporteGeneralDisTR_old($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['distributor_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['finish'];
      $start      = $item['start'];
      $en_proceso = $usuarios - $aprobados;
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pProceso   = ($usuarios !== 0) ? round($start/$usuarios*100)  :  0;

      $activos    = $start + $aprobados;
      $inactivos  = $usuarios - $activos;
      // En Proceso
      $arr1 = preg_split('/,/', $item['user_ids']);
      $arr2 = preg_split('/,/', $item['finish_ids']);
      $arr3 = preg_split('/,/', $item['start_ids']);

      $arr4 = array_merge($arr2, $arr3);
      $arr5 = array_diff($arr1, $arr4);

      $activos_ids = implode($arr4, ',');
      $inactivos_ids = implode($arr5, ',');

      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['user_ids']. '" class="lista">'. $item['usuarios']. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $inactivos_ids. '" class="lista">'. $inactivos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $activos_ids. '" class="lista">'. $activos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['finish_ids']. '" class="lista">'. $item['finish']. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['start_ids']. '" class="lista">'. $start. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genReporteGeneralEquipoTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['distributor_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['finish'];// - $item['start'];
      $start      = $item['start'];
      $en_proceso = $usuarios - $aprobados;
      $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
      $pProceso   = ($usuarios !== 0) ? round($start/$usuarios*100)  :  0;

      $activos    = $start + $aprobados;
      $inactivos  = $usuarios - $activos;

      if ($usuarios == $aprobados){
        $activos = $usuarios;
        $inactivos = 0;
      } else {
        $activos    = $start + $aprobados;
        $inactivos  = $usuarios - $activos;

          if ($activos > $usuarios){
            $activos = $usuarios;
            $inactivos = 0;
          }
      }

      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
      $html .= '<td><div>'. $item['team_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="E#T#'. $item['team_id'] .'" class="lista">'. $usuarios. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="E#I#'. $item['team_id'] .'" class="lista">'. $inactivos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="E#A#'. $item['team_id'] .'" class="lista">'. $activos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="E#F#'. $item['team_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="E#P#'. $item['team_id'] .'" class="lista">'. $start. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genReporteGeneralEquipoTR_v2($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $inscritos    = 0;
    $completados  = 0;
    $entrenandose = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['team_id'];
      $usuarios   = $item['usuarios'];
      $aprobados  = $item['fin_100'];
      $en_proceso = $item['en_proceso'];
      $no_empezaron = $item['no_empezaron'];

      $activos    = $aprobados + $en_proceso;
      $inactivos  = $no_empezaron;

      $pAprobados = ($activos !== 0) ? round($aprobados/$activos*100)  :  0;
      $pProceso   = ($activos !== 0) ? round($en_proceso/$activos*100)  :  0;

/*
      if ($usuarios == $aprobados){
        $activos = $usuarios;
        $inactivos = 0;
      } else {
        $activos    = $start + $aprobados;
        $inactivos  = $usuarios - $activos;

          if ($activos > $usuarios){
            $activos = $usuarios;
            $inactivos = 0;
          }
      }
*/
      $html .= '<tr>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
      $html .= '<td><div>'. $item['team_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="E#T#'. $item['team_id'] .'" class="lista">'. $usuarios. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="E#I#'. $item['team_id'] .'" class="lista">'. $inactivos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="E#A#'. $item['team_id'] .'" class="lista">'. $activos. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="E#F#'. $item['team_id'] .'" class="lista">'. $aprobados. '</a></div></td>';
      $html .= '<td><div>'. $pAprobados. '%</div></td>';
      $html .= '<td><div><a href="#" rel="E#P#'. $item['team_id'] .'" class="lista">'. $en_proceso. '</a></div></td>';
      $html .= '<td><div>'. $pProceso. '%</div></td>';
      $html .= '</tr>';
    }
  }

  return '<tbody>'. $html. '</tbody></table>';
}

function genResultadosParticipanteTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['usuario_id'];
      $full = count( preg_split('/,/', $item['curricula_entrenamientos'], -1, PREG_SPLIT_NO_EMPTY) );
      $fin  = count( preg_split('/,/', $item['entrenamiento_fin'], -1, PREG_SPLIT_NO_EMPTY) );
      $prog = count( preg_split('/,/', $item['entrenamiento_progeso'], -1, PREG_SPLIT_NO_EMPTY) );

      $html .= '<tr>';
      $html .= '<td><div>'. ucwords($item['usuario_nombre']). '</div></td>';
      $html .= '<td><div>'. $item['usuario_doc']. '</div></td>';
      $html .= '<td><div>'. $item['usuario_email']. '</div></td>';
      $html .= '<td><div>'. $item['perfil_name']. '</div></td>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
      $html .= '<td><div>'. $item['supersivor_nombre']. '</div></td>';
      $html .= '<td><div>'. $item['team_name']. '</div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['curricula_entrenamientos']. '" class="viewEnt">'. $full. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['entrenamiento_fin']. '" class="viewEnt">'. $fin. '</a></div></td>';
      $html .= '<td><div><a href="#" rel="'. $item['entrenamiento_progeso']. '" class="viewEnt">'. $prog. '</a></div></td>';
      $html .= '<td><div>'. $item['puntos_suma']. '</div></td>';
      $html .= '<td><div>'. $item['puntos_aciertos']. '%</div></td>';
      $html .= '<td><div>'. $item['fecha_last']. '</div></td>';
      $html .= '</tr>';
    }
  }

  return $html;
}

function genReporteEParticipanteTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['usuario_id'];

      $html .= '<tr>';
      $html .= '<td><div>'. ucwords($item['usuario_nombre']). '</div></td>';
      $html .= '<td><div>'. $item['usuario_doc']. '</div></td>';
      $html .= '<td><div>'. $item['usuario_email']. '</div></td>';
      $html .= '<td><div>'. $item['perfil_name']. '</div></td>';
      $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
      $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
      $html .= '<td><div>'. $item['supersivor_nombre']. '</div></td>';
      //$html .= '<td><div>'. $item['rank']. '</div></td>';
      $html .= '<td><div>'. $item['puntos_suma']. '</div></td>';
      $html .= '<td><div>'. $item['puntos_aciertos']. '%</div></td>';
      $html .= '<td><div>'. $item['entrenamientos_fin']. '</div></td>';
      $html .= '<td><div>'. $item['fecha_last']. '</div></td>';
      $html .= '</tr>';
    }
  }

  return $html;
}

function genReporteTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['subsidiary_id'];

      $html .= '<tr>';
      $html .= '<td><div>0</div></td>';
      $html .= '<td><div>0</div></td>';
      $html .= '<td><div>0</div></td>';
      $html .= '<td><div>0</div></td>';
      $html .= '<td><div>0</div></td>';
      $html .= '<td><div>0</div></td>';
    }
  }

  return $html;
}

function genRptCustomETB($resultados, $cols){
    $head = '<table class="tblAzul responsive" id="tblShow">
      <thead>
          <tr>';
    $head .= (in_array('a', $cols)) ? '<th>Entrenamiento</td>' : '';
    $head .= (in_array('b', $cols)) ? '<th>Inscritos</td>' : '';
    $head .= (in_array('c', $cols)) ? '<th>Aprobados</td>' : '';
    $head .= (in_array('d', $cols)) ? '<th>% Aprobados</td>' : '';
    $head .= (in_array('e', $cols)) ? '<th>En Proceso</td>' : '';
    $head .= (in_array('f', $cols)) ? '<th>% En Proceso</td>' : '';
    $head .= '</tr>
      </thead>
      <tbody>';

    $xHtml = '<tr><td colspan="6">No hay resultados</td></tr>';
    if (count($resultados)){
        $xHtml = '';
        foreach($resultados as $item){
          $xHtml .= genRptCustomETR($item, $cols);
        }
    }

    return $head. $xHtml. '</tbody></table>';
}

function genRptCustomETR($item, $cols){
  $id = $item['entrenamiento_id'];
  $usuarios   = $item['start'];
  $aprobados  = $item['finish'];
  $en_proceso = $usuarios - $aprobados;
  $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;
  $pAvances   = '';

  $html = '<tr>';
  $html .= (in_array('a', $cols)) ? '<td><div>'. $item['entrenamiento_nombre']. '</div></td>' : '';
  $html .= (in_array('b', $cols)) ? '<td><div><a href="#" rel="'. $item['start_ids']. '" class="lista">'. $usuarios. '</a></div></td>'    : '';
  $html .= (in_array('c', $cols)) ? '<td><div><a href="#" rel="'. $item['finish_ids']. '" class="lista">'. $aprobados. '</a></div></td>'  : '';
  $html .= (in_array('d', $cols)) ? '<td><div>'. $pAprobados. '%</div></td>'   : '';
  $html .= (in_array('e', $cols)) ? '<td><div>'. $en_proceso. '</div></td>'    : '';
  $html .= (in_array('f', $cols)) ? '<td><div>'. $pAvances. '%</div></td>'     : '';
  $html .= '</tr>';

  return $html;
}

function genRptCustomATB($titulo, $resultados, $cols){
    $head = '<table class="tblAzul responsive" id="tblShow">
      <thead>
          <tr>';
    $head .= (in_array('a', $cols) || in_array('b', $cols)) ? '<th>'. $titulo. '</td>' : '';
    $head .= (in_array('c', $cols)) ? '<th>Inscritos</td>' : '';
    $head .= (in_array('d', $cols)) ? '<th>Aprobados</td>' : '';
    $head .= (in_array('e', $cols)) ? '<th>% Aprobados</td>' : '';
    $head .= '</tr>
      </thead>
      <tbody>';

    $xHtml = '<tr><td colspan="6">No hay resultados</td></tr>';
    if (count($resultados)){
        $xHtml = '';
        foreach($resultados as $item){
          $xHtml .= genRptCustomATR($item, $cols);
        }
    }

    return $head. $xHtml. '</tbody></table>';
}

function genRptCustomATR($item, $cols){
  $usuarios   = $item['usuarios'];
  $aprobados  = $item['finish'];
  $pAprobados = ($usuarios !== 0) ? round($aprobados/$usuarios*100)  :  0;

  $html = '<tr>';
  $html .= (in_array('a', $cols)) ? '<td><div>'. $item['distributor_name']. '</div></td>' : '';
  $html .= (in_array('b', $cols)) ? '<td><div>'. $item['subsidiary_name']. '</div></td>'             : '';
  $html .= (in_array('c', $cols)) ? '<td><div>'. $item['usuarios']. '</div></td>'   : '';
  $html .= (in_array('d', $cols)) ? '<td><div>'. $item['finish']. '</div></td>'  : '';
  $html .= (in_array('e', $cols)) ? '<td><div>'. $pAprobados. '%'. '</div></td>'   : '';
  $html .= '</tr>';

  return $html;
}

function genRptCustomVTB($resultados, $cols){
    $head = '<table class="tblAzul responsive" id="tblShow">
      <thead>
          <tr>';
    $head .= (in_array('a', $cols)) ? '<th>Vendedor</td>' : '';
    $head .= (in_array('b', $cols)) ? '<th>Documento</td>' : '';
    $head .= (in_array('c', $cols)) ? '<th>Puntos<br/>Ganados</td>' : '';
    $head .= (in_array('d', $cols)) ? '<th>Entrenamientos<br/>Completados</td>' : '';
    $head .= (in_array('e', $cols)) ? '<th>Puntaje<br/>Promedio</td>' : '';
    $head .= (in_array('f', $cols)) ? '<th>Ultimo<br/>Acceso</td>' : '';
    $head .= '</tr>
      </thead>
      <tbody>';

    $xHtml = '<tr><td colspan="6">No hay resultados</td></tr>';
    if (count($resultados)){
        $xHtml = '';
        foreach($resultados as $item){
          $xHtml .= genRptCustomVTR($item, $cols);
        }
    }

    return $head. $xHtml. '</tbody></table>';
}

function genRptCustomVTR($item, $cols){
  $html = '<tr>';
  $html .= (in_array('a', $cols)) ? '<td><div>'. $item['usuario_nombre']. '</div></td>' : '';
  $html .= (in_array('b', $cols)) ? '<td><div>'. $item['usuario_doc']. '</div></td>'             : '';
  $html .= (in_array('c', $cols)) ? '<td><div>'. $item['puntos_suma']. '</div></td>'   : '';
  $html .= (in_array('d', $cols)) ? '<td><div>'. $item['entrenamientos_fin']. '</div></td>'  : '';
  $html .= (in_array('e', $cols)) ? '<td><div>'. $item['puntos_avg']. '</div></td>'   : '';
  $html .= (in_array('f', $cols)) ? '<td><div>'. $item['fecha_last']. '</div></td>'  : '';
  $html .= '</tr>';

  return $html;
}

function genRptListUsersTB($resultados){
    $head = '<table class="tblAzul responsive" id="tblShow">
      <thead>
        <tr>
          <th>#</th>
          <th>Vendedor</td>
          <th>DNI</td>
          <th>Email</td>
          <th>Distribuidor</td>
          <th>Sucursal</td>
        </tr>
      </thead>
      <tbody>';

    $xHtml = '<tr><td colspan="5">No hay resultados</td></tr>';
    if (count($resultados)){
        $xHtml = '';
        $x = 0;
        foreach($resultados as $item){
          $xHtml .= genRptListUsersTR(++$x, $item);
        }
    }

    return $head. $xHtml. '</tbody></table>';
}

function genRptListUsersTR($num, $item){
  $html = '<tr>';
  $html .= '<td>'. $num. '</td>';
  $html .= '<td><div><a href="#">'. ucwords($item['usuario_nombre']). '</a></div></td>';
  $html .= '<td><div>'. $item['usuario_doc']. '</div></td>';
  $html .= '<td><div>'. $item['usuario_email']. '</div></td>';
  $html .= '<td><div>'. $item['distributor_name']. '</div></td>';
  $html .= '<td><div>'. $item['subsidiary_name']. '</div></td>';
  $html .= '</tr>';

  return $html;
}

/**
  Trofeos
**/
function genTrofeosTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      if ( $item['trofeo_id'] != '-'){
        $id   = $item['trofeo_id'];
        $path = 'trofeo_'. strtolower($item['trofeo_tipo']);
        $pathImg = genPathPicture($item[$path], 'trofeos/', 'bronce.png');

        $html .= '<tr><td class="text-center">';
        $html .= '<a class="image-modal" href="#" data-img-src="'. $pathImg. '" data-reveal-id="myModal"><img src="'. $pathImg. '" alt="trofeo"></a>';
        $html .= '<td class="text-center">'. $item['entrenamiento_nombre']. '</td>';
        $html .= '<td class="text-center">'. $item['trofeo_fch_reg']. '</td>';
        $html .= '</td></tr>';
      }
    }
  }

  return $html;
}

function genTrofeosDiv($resultados, $cur){
  $html = '';
  $x = 1;
  if (count($resultados)){
    foreach($resultados as $item){
      $sel  = ($x == $cur) ? ' bgVerdeL' : '';
      $imgPath = str_replace("'", '"', $item['nivel_imagen']);
      $imgPath = json_decode($imgPath , true);
      $path = genPathPicture($imgPath['normal'], 'personajes/', 'default.png');
      $html .= '<div class="small-3 columns '. $sel. '"><img src="'. $path. '"><br>'. $item['nivel_name']. '</div>';
      $x++;
    }
  }

  return $html;
}

function genTrofeosOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['nivel_id'], $cur);
      $html .= '<option value = "'. $item['nivel_id']. '" '. $sel. '>'. $item['nivel_name']. '</option>';
    }
  }

  return $html;
}

function genTrofeosJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['nivel_id']);
      $post["nombre"]    = html_entity_decode($item['nivel_name']);
      $post["descrip"]   = html_entity_decode($item['nivel_descrip']);
      //$post["image"]     = html_entity_decode($item['nivel_imagen']);
      $post["image"]     = $item['nivel_imagen'];
      $post["path"]      = genPathPicture('', 'personajes/', '');

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Niveles
**/
function genNivelesTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['nivel_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['nivel_name']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genNivelesDiv($resultados, $cur){
  $html = '';
  $x = 1;
  if (count($resultados)){
    foreach($resultados as $item){
      $sel  = ($x == $cur) ? ' bgVerdeL' : '';
      $imgPath = str_replace("'", '"', $item['nivel_imagen']);
      $imgPath = json_decode($imgPath , true);
      $path = genPathPicture($imgPath['normal'], 'personajes/', 'default.png');
      $html .= '<div class="small-3 medium-3 columns '. $sel. '"><img src="'. $path. '"><br>'. $item['nivel_name']. '</div>';
      $x++;
    }
  }

  return $html;
}

function genNivelesOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['nivel_id'], $cur);
      $html .= '<option value = "'. $item['nivel_id']. '" '. $sel. '>'. $item['nivel_name']. '</option>';
    }
  }

  return $html;
}

function genNivelesJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['nivel_id']);
      $post["nombre"]    = html_entity_decode($item['nivel_name']);
      $post["descrip"]   = html_entity_decode($item['nivel_descrip']);
      //$post["image"]     = html_entity_decode($item['nivel_imagen']);
      $post["image"]     = $item['nivel_imagen'];
      $post["path"]      = genPathPicture('', 'personajes/', '');

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Perfiles
**/
function genPerfilesTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['perfil_id'];
      $link = 'crear-niveles.php?pid='. $id;

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['perfil_name']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link. '" title="Niveles">';
      $html .= '  <i class="fa fa-level-up"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genPerfilesOP($resultados, $cur, $ver=true){
  $html = ($ver) ? '<option value="-1">Seleccionar ...</option>' : '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['perfil_id'], $cur);
      $html .= '<option value = "'. $item['perfil_id']. '" '. $sel. '>'. $item['perfil_name']. '</option>';
    }
  }

  return $html;
}

function genPerfilesLI($resultados, $cur){
  $html = '';

  if (count($resultados)){
    foreach($resultados as $item){
      $html .= '<li><a href="?q=perfil&s='. $item['perfil_id']. '">'. $item['perfil_name']. '</a></li>';
    }
  }

  return $html;
}

function genPerfilesJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['perfil_id']);
      $post["nombre"]    = html_entity_decode($item['perfil_name']);
      $post["descrip"]   = html_entity_decode($item['perfil_descrip']);

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  TeamList
**/
function genTeamListsTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['team_id'];
      $link = ''. $id;

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['team_name']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top location" href="-1" rel="'. $id. '" title="Agregar Distribuidora">';
      $html .= '  <i class="fa fa-university"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top city" href="-1" rel="'. $id. '" title="Agregar Sucursal">';
      $html .= '  <i class="fa fa-map-marker"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top participante" href="-1" rel="'. $id. '" title="Agregar Miembros">';
      $html .= '  <i class="fa fa-user-plus"></i></a>';
      $html .= '  <a rel="97" href="-1" class="pLeft5 tip-top supervisor" title="Asignar Supervisor">';
      $html .= '  <i class="fa fa-shield fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genTeamListsOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['team_id'], $cur);
      $html .= '<option value = "'. $item['team_id']. '" '. $sel. '>'. $item['team_name']. '</option>';
    }
  }

  return $html;
}

function genTeamListsJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]           = intval($item['list_id']);
      $post["list_name"]    = html_entity_decode($item['list_name']);
      $post["list_type"]    = html_entity_decode($item['list_type']);
      $post["list_type_id"] = html_entity_decode($item['list_type_id']);

      array_push($response, $post);
    }
  }

  return $response;
}

function genTeamListsJSDiv($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]           = intval($item['list_id']);
      $post["list_name"]    = html_entity_decode($item['list_name']);
      $post["list_type"]    = html_entity_decode($item['list_type']);
      $post["list_type_id"] = html_entity_decode($item['list_type_id']);

      array_push($response, $post);
    }
  }

  return $response;
}

function genTeamListsDiv($resultados){
  $html = '';
  $response = array('Distribuidora' => '', 'Ciudad' => '', 'Usuario' => '', 'Supervisor' => '');
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]           = intval($item['list_id']);
      $post["list_name"]    = html_entity_decode($item['list_name']);
      $post["list_type"]    = html_entity_decode($item['list_type']);
      $post["list_type_id"] = html_entity_decode($item['list_type_id']);
      $html = genAlertBox($post["list_name"], $post["id"]);

      $response[ $post["list_type"] ] .= $html;
    }
  }

  return $response;
}

function genTeamListsVar($resultados){
  $html = '';
  $response = array('Distribuidora' => array(), 'Ciudad' => array(), 'Usuario' => array(), 'Supervisor' => array());
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      //$post["id"]           = intval($item['list_id']);
      //$post["list_type"]    = html_entity_decode($item['list_type']);
      //$post["list_type_id"] = html_entity_decode($item['list_type_id']);

      array_push($response[ $item["list_type"] ], $item['list_type_id']);
    }
  }

  return $response;
}

function genAlertBox($list_name, $list_id){
  return '<div data-alert class="alert-box info round">'. $list_name. '<a href="#" rel="'. $list_id. '" class="close">&times;</a></div>';
}

/**
  Curricula
**/
function genCurriculasTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['curricula_id'];
      $link = ''. $id;

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['team_name']. '</a></td>';
      $html .= '<td class="tableActs">';
      //$html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top location" href="-1" rel="'. $id. '" title="Agregar Distribuidora">';
      //$html .= '  <i class="fa fa-university"></i></a>';
      //$html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top city" href="-1" rel="'. $id. '" title="Agregar Sucursal">';
      //$html .= '  <i class="fa fa-map-marker"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top participante" href="-1" rel="'. $id. '" title="Agregar Miembros">';
      $html .= '  <i class="fa fa-user-plus"></i></a>';
      //$html .= '  <a data-tooltip aria-haspopup="true" rel="'. $id. '" href="-1" class="pLeft5 tip-top supervisor" title="Agregar Supervisor">';
      //$html .= '  <i class="fa fa-shield fa-lg"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" href="ver-equipo.php?pid='. $id. '" class="pLeft5 tip-top" title="Ver Equipo">';
      $html .= '  <i class="fa fa-eye fa-lg"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 tip-top delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genCurriculasOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['curricula_id'], $cur);
      $html .= '<option value = "'. $item['curricula_id']. '" '. $sel. '>'. $item['team_name']. '</option>';
    }
  }

  return $html;
}

function genCurriculasJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]              = intval($item['curricula_id']);
      $post["entrenamientos"]  = $item['curricula_entrenamientos'];
      $post["novedades"]       = $item['curricula_novedades'];
      $post["perfil"]          = intval($item['perfil_id']);

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Equipo
**/
function genEquiposTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['team_id'];
      $link = ''. $id;

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['team_name']. '</a></td>';
      $html .= '<td>'. $item['distributor_name']. ' > '. $item['subsidiary_name']. '</td>';
      $html .= '<td class="tableActs">';
      //$html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top location" href="-1" rel="'. $id. '" title="Agregar Distribuidora">';
      //$html .= '  <i class="fa fa-university"></i></a>';
      //$html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top city" href="-1" rel="'. $id. '" title="Agregar Sucursal">';
      //$html .= '  <i class="fa fa-map-marker"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top participante" href="-1" rel="'. $id. '" title="Agregar Miembros">';
      $html .= '  <i class="fa fa-user-plus"></i></a>';
      //$html .= '  <a data-tooltip aria-haspopup="true" rel="'. $id. '" href="-1" class="pLeft5 tip-top supervisor" title="Agregar Supervisor">';
      //$html .= '  <i class="fa fa-shield fa-lg"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" href="ver-equipo.php?pid='. $id. '" class="pLeft5 tip-top" title="Ver Equipo">';
      $html .= '  <i class="fa fa-eye fa-lg"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 tip-top delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genEquiposOP($resultados, $cur, $ver=true){
  $html = ($ver) ? '<option value="-1">Seleccionar ...</option>' : '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['team_id'], $cur);
      $html .= '<option value = "'. $item['team_id']. '" '. $sel. '>'. $item['team_name']. '</option>';
    }
  }

  return $html;
}

function genEquiposLI($resultados, $cur){
  $html = '';
  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['team_id'], $cur);
      $html .= '<li value="'. $item['team_id']. '" '. $sel. '>'. $item['team_name']. '</li>';
    }
  }

  return $html;
}


function genEquiposJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['team_id']);
      $post["nombre"]    = html_entity_decode($item['team_name']);
      $post["descrip"]   = html_entity_decode($item['team_descrip']);
      $post["distributor"]  = intval($item['distributor_id']);
      $post["subsidiary"]   = intval($item['subsidiary_id']);
      $post["supervisor"]   = intval($item['supervisor_id']);

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Distribuidora
**/
function genDistribuidorasTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $link = '';
    // recorre iten por item los resultados
    foreach($resultados as $item){
      $id = $item['distributor_id'];
      $link = 'crear-sucursal.php?pid='. $id;

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['distributor_name']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link. '" title="Ver Sucursales">';
      $html .= '  <i class="fa fa-map-marker"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genDistribuidorasOP($resultados, $cur, $ver=true){
  $html = ($ver) ? '<option value="-1">Seleccionar ...</option>' : '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['distributor_id'], $cur);
      $html .= '<option value = "'. $item['distributor_id']. '" '. $sel. '>'. $item['distributor_name']. '</option>';
    }
  }

  return $html;
}

function genDistribuidorasJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['distributor_id']);
      $post["nombre"]    = html_entity_decode($item['distributor_name']);
      $post["descrip"]   = html_entity_decode($item['distributor_descrip']);

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  SUCURSALES
**/
function genSucursalesTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['subsidiary_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['subsidiary_name']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genSucursalesOPFull($resultados, $cur, $ver = true){
  $html = ($ver) ? '<option value="-1">Seleccionar ...</option>' : '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['subsidiary_id'], $cur);
      $html .= '<option value = "'. $item['subsidiary_id']. '" '. $sel. '>'. $item['distributor_name']. ' > '. $item['subsidiary_name']. '</option>';
    }
  }

  return $html;
}

function genSucursalesOP($resultados, $cur, $ver = true){
  $html = ($ver) ? '<option value="-1">Seleccionar ...</option>' : '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['subsidiary_id'], $cur);
      $html .= '<option value = "'. $item['subsidiary_id']. '" '. $sel. '>'. $item['subsidiary_name']. '</option>';
    }
  }

  return $html;
}

function genSucursalesJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['subsidiary_id']);
      $post["nombre"]    = html_entity_decode($item['subsidiary_name']);
      $post["descrip"]   = html_entity_decode($item['subsidiary_descrip']);

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Cursos
**/
function genCursosDIV($resultados){
  $html = '';
  $temp = '';
  if (count($resultados)){
    $x = 0;
    $initial1 = '';
    $initial2 = '';
    $label    = '';
    $status   = 'No';
    $opacity  = '';
    foreach($resultados as $item){
      $initial1 = ($x === 0) ? 'small-12 large-6' : 'small-12 large-3 left';
      $initial2 = ($x === 0) ? 'vp-initial' : '';

      $id = encrypt($item['curso_id'], $_SESSION['k']);
      $status = $item['status'];
      $portada  = 'static/images/portadas/portada01.jpg';

      if ($x === 0 OR $status !== 'No' ){
        $label = 'Empezar';
        $class = 'warning';
        $icon  = '<i class="fa fa-play-circle-o amarillo"></i>';
      } else {
        $label = 'Bloqueado';
        $class = 'alert';
        $icon  = '<i class="fa fa-lock rojo"></i>';
        $opacity = 'lessOpacity';
      }

      $temp = '<div class="%s columns">
        <div class="vPlayer %s"><a href="#" rel="'. $id. '" class="'. $class. '">
          <img class="caratula %s" src="%s" alt="Portada">
          <span class="status label %s"><i class="fa fa-chevron-right"></i> %s</span>
          <span class="play">%s</span>
          <div class="title">
            <h5>%s</h5>
            <span>%s</span>
          </div>
        </a></div>
      </div>';
      $html .= sprintf($temp, $initial1, $initial2, $opacity, $portada, $class, $label, $icon, $item['curso_nombre'], $item['curso_descrip']);
      $x++;
    }
  }

  return $html;
}

function genCursosTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['curso_id'];
      $link1 = 'crear-cuestionario.php?pid='. $id. '&rel='. $item['entrenamiento_id'];
      $link2 = 'crear-tema.php?pid='. $id. '&rel='. $item['entrenamiento_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['curso_nombre']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link1. '" title="Ver Cuestionario">';
      $html .= '  <i class="fa fa-list fa-lg"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link2. '" title="Ver Temas">';
      $html .= '  <i class="fa fa-book fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genCursosOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['curso_id'], $cur);
      $html .= '<option value = "'. $item['curso_id']. '" '. $sel. '>'. $item['curso_nombre']. '</option>';
    }
  }

  return $html;
}

function genCursosJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['curso_id']);
      $post["nombre"]    = html_entity_decode($item['curso_nombre']);
      $post["objetivo"]  = html_entity_decode($item['curso_objetivo']);
      $post["descrip"]   = html_entity_decode($item['curso_descrip']);
      $post["video"]     = html_entity_decode($item['curso_video']);
      $post["master_id"] = $item['entrenamiento_id'];

      array_push($response, $post);
    }
  }

  return $response;
}

/**
 Entrenamientos
**/
function genEntrenamientosTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['entrenamiento_id'];
      $linkCursos = 'crear-curso.php?pid='. $id;

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['entrenamiento_nombre']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $linkCursos. '" title="Ver Cursos">';
      $html .= '  <i class="fa fa-book fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genEntrenamientosOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['entrenamiento_id'], $cur);
      $html .= '<option value = "'. $item['entrenamiento_id']. '" '. $sel. '>'. $item['entrenamiento_nombre']. '</option>';
    }
  }

  return $html;
}

function genEntrenamientosOPver($resultados, $cur, $verSel = true){
  $html = ($verSel) ? '<option value="-1">Seleccionar ...</option>'  :  '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['entrenamiento_id'], $cur);
      $html .= '<option value = "'. $item['entrenamiento_id']. '" '. $sel. '>'. $item['entrenamiento_nombre']. '</option>';
    }
  }

  return $html;
}

function genEntrenamientosJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['entrenamiento_id']);
      $post["nombre"]    = html_entity_decode($item['entrenamiento_nombre']);
      $post["objetivo"]  = html_entity_decode($item['entrenamiento_objetivo']);
      $post["descrip"]   = html_entity_decode($item['entrenamiento_descrip']);
      $post["activo"]    = $item['entrenamiento_activo'];
      $post["pic1"]      = genPathPicture($item['entrenamiento_t_oro'], 'trofeos/', 'trofeo.png');
      $post["pic2"]      = genPathPicture($item['entrenamiento_t_plata'], 'trofeos/', 'trofeo.png');
      $post["pic3"]      = genPathPicture($item['entrenamiento_t_bronce'], 'trofeos/', 'trofeo.png');

      array_push($response, $post);
    }
  }

  return $response;
}

function genEntrenamientosJSmin($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['entrenamiento_id']);
      $post["nombre"]    = html_entity_decode($item['entrenamiento_nombre']);

      array_push($response, $post);
    }
  }

  return $response;
}

function genEntrenamientosDIV($resultados){
  $html = '';
  $full = 'full';
  $id   = 0;
  $logo = '';

  if (count($resultados)){
    foreach($resultados as $item){
      $id = encrypt($item['entrenamiento_id'], $_SESSION['k']);
      $logo = genPathPicture($item['entrenamiento_logo'], 'plataforma/', 'logo-clean.png');
      $html .= '<div class="small-12 medium-6 large-4 columns">
        <a href="entrenamiento.php?pid='. $id. '" class="item" rel="'. $item['entrenamiento_activo']. '">
          <span class="chart" data-percent="'. $item['avance']. '">
            <span class="percent"></span><br/>
            <img src="'. $logo. '" alt="pg">
          </span>
          <h4>'. $item['entrenamiento_nombre']. '</h4>
        </a>
      </div>';
    }
  }

  return $html;
}

/**
  Temas
**/
function genTemasDIV($resultados){
  $html = '';
  $temp = '';
  if (count($resultados)){
    $x = 0;
    $label    = '';
    $opacity  = '';
    $status   = 'No';
    foreach($resultados as $item){
      $id = encrypt($item['tema_id'], $_SESSION['k']);
      $portada  = 'static/images/portadas/portada02.jpg';
      $status = $item['status'];

      if ($status !== 'No'){
        $label = 'Empezar';
        $class = 'warning';
        $icon  = '<i class="fa fa-play-circle-o amarillo"></i>';
      } else {
        $label = 'Bloqueado';
        $class = 'alert';
        $icon  = '<i class="fa fa-lock rojo"></i>';
        $opacity = 'lessOpacity';
      }

      $temp = '<div class="small-12 large-6 columns left">
        <div class="vPlayer left"><a href="#" rel="'. $id. '" class="'. $class. '">
          <img class="caratula %s" src="%s" alt="Portada">
          <span class="status label %s"><i class="fa fa-chevron-right"></i> %s</span>
          <span class="play">%s</span>
          <div class="title">
            <h5>%s</h5>
            <span>%s</span>
          </div>
        </a></div>
      </div>';
      $html .= sprintf($temp, $opacity, $portada, $class, $label, $icon, $item['tema_nombre'], $item['tema_descrip']);
      $x++;
    }
  }

  return $html;
}

function genTemasDIV2($resultados){
  $html = '';
  $temp = '';
  if (count($resultados)){
    $x = 0;
    $label    = '';
    $opacity  = '';
    $status   = 'No';
    foreach($resultados as $item){
      $id = encrypt($item['tema_id'], $_SESSION['k']);
      $portada  = 'static/images/portadas/portada02.jpg';
      $status = $item['status'];

      if ($status !== 'No'){
        $label = 'Empezar';
        $class = 'warning';
        $icon  = '<i class="fa fa-play-circle-o amarillo"></i>';
      } else {
        $label = 'Bloqueado';
        $class = 'alert';
        $icon  = '<i class="fa fa-lock rojo"></i>';
        $opacity = 'lessOpacity';
      }

      $temp = '<div class="small-12 large-4 columns left">
        <div class="vPlayer left"><a href="#" rel="'. $id. '" class="'. $class. '">
          <img class="caratula %s" src="%s" alt="Portada">
          <span class="status label %s"><i class="fa fa-chevron-right"></i> %s</span>
          <span class="play">%s</span>
          <div class="title">
            <h5>%s</h5>
            <span>%s</span>
          </div>
        </a></div>
      </div>';
      $html .= sprintf($temp, $opacity, $portada, $class, $label, $icon, $item['tema_nombre'], $item['tema_descrip']);
      $x++;
    }
  }

  return $html;
}

function genTemasTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['tema_id'];
      $link1 = 'crear-juego1.php?pid='. $id. '&rel='. $item['curso_id'];
      $link2 = 'crear-juego2.php?pid='. $id. '&rel='. $item['curso_id'];
      $link3 = 'crear-juego3.php?pid='. $id. '&rel='. $item['curso_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['tema_nombre']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link1. '" title="Juego 1">';
      $html .= '  <i class="fa fa-trophy fa-lg"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link2. '" title="Juego 2">';
      $html .= '  <i class="fa fa-trophy fa-lg"></i></a>';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link3. '" title="Juego 3">';
      $html .= '  <i class="fa fa-trophy fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genTemasOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['tema_id'], $cur);
      $html .= '<option value = "'. $item['tema_id']. '" '. $sel. '>'. $item['tema_nombre']. '</option>';
    }
  }

  return $html;
}

function genTemasJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['tema_id']);
      $post["nombre"]    = html_entity_decode($item['tema_nombre']);
      $post["objetivo"]  = html_entity_decode($item['tema_objetivo']);
      $post["descrip"]   = html_entity_decode($item['tema_descrip']);
      $post["tMaterial"] = html_entity_decode($item['tema_tipo_material']);
      $post["material"]  = html_entity_decode($item['tema_material']);
      $post["master_id"] = $item['curso_id'];

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Novedades
**/
function genNovedadesDIV($resultados){
  $html = '';
  $full = 'full';
  $id   = 0;
  $logo = '';

  if (count($resultados)){
    foreach($resultados as $item){
      $id = encrypt($item['novedad_id'], $_SESSION['k']);
      $logo = genPathPicture($item['novedad_logo'], 'plataforma/', 'logo-clean.png');
      $html .= '<div class="small-12 columns">
        <a href="novedad.php?pid='. $id. '">
          <span class="chart" data-percent="'. intval($item['avance']). '">
            <span class="percent"></span><br/>
            <img src="'. $logo. '" alt="pg">
          </span>
          <h4>'. $item['novedad_nombre']. '</h4>
        </a>
      </div>';
    }
  }

  return $html;
}

function genNovedadesTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['novedad_id'];
      $table = base64_encode('pg_novedades');
      $link1 = 'crear-preguntas.php?m='. $table. '&pid='. $id. '&rel='. $item['novedad_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['novedad_nombre']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a data-tooltip aria-haspopup="true" class="pLeft5 tip-top" href="'. $link1. '" title="Cuestionario">';
      $html .= '  <i class="fa fa-book fa-lg"></i></a>';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genNovedadesOP($resultados, $cur, $ver = true){
  $html = ($ver) ? '<option value="-1">Seleccionar ...</option>' : '';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['novedad_id'], $cur);
      $html .= '<option value = "'. $item['novedad_id']. '" '. $sel. '>'. $item['novedad_nombre']. '</option>';
    }
  }

  return $html;
}

function genNovedadesJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['novedad_id']);
      $post["nombre"]    = html_entity_decode($item['novedad_nombre']);
      $post["objetivo"]  = html_entity_decode($item['novedad_objetivo']);
      $post["descrip"]   = html_entity_decode($item['novedad_descrip']);
      $post["tMaterial"] = html_entity_decode($item['novedad_tipo_material']);
      $post["material"]  = html_entity_decode($item['novedad_material']);

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Juegos
**/
function genJuegosDIV($resultados){
  $html = '';

  //$j1 = genJuego('pg_juegos1', '1', 'juego1', $resultados);
  $j1 = genJuego('pg_juegos1', '1', 'juego01', $resultados);
  //$j2 = genJuego('pg_juegos2', '2', 'juego2', $resultados);
  $j2 = genJuego('pg_juegos2', '2', 'juego02', $resultados);
  //$j3 = genJuego('pg_juegos3', '3', 'ejercicio', $resultados);
  $j3 = genJuego('pg_juegos3', '3', 'juego03', $resultados);

  return $j1. $j2. $j3;
}

function genJuego($juego, $num, $rel, $resultados){
  $label = 'Bloqueado';
  $class = 'alert';
  $icon  = '<i class="fa fa-lock rojo"></i>';
  $opacity = 'lessOpacity';
  $portada = 'static/images/portadas/juego1.jpg';
  $status  = '';

  //$label = 'Empezar';
  //$class = 'warning';
  //$icon  = '<i class="fa fa-play-circle-o amarillo"></i>';
  //$opacity = '';

  if (count($resultados)){
    foreach($resultados as $item){
      if ($item['master_tbl'] === $juego){
        $status  = $item['status'];
        //$status  = ($status !== 'No') ? $status : 'Si'; // Aperturamos los juegos

        $label = 'Bloqueado';
        $class = 'alert';
        $icon  = '<i class="fa fa-lock rojo"></i>';
        $opacity = 'lessOpacity';
        if ($status == 'Finish'){
          $label = 'Completado';
          $class = 'success';
          $icon  = '<i class="fa fa-check-circle-o verde"></i>';
          $opacity = '';
        };
        if ($status == 'Start'){
          $label = 'Empezar';
          $class = 'warning';
          $icon  = '<i class="fa fa-play-circle-o amarillo"></i>';
          $opacity = '';
        }
      }
    }
  }

  $temp = '<div class="small-4 columns">
    <div class="vPlayer" id="desafio'. $num. '"><a href="#" rel="'. $rel. '" class="'. $class. '">
        <img class="caratula %s" src="%s" alt="portada">
        <span class="status label %s"><i class="fa fa-chevron-right"></i> %s</span>
        <span class="play">%s</span>
        <div class="title">
          <h5>DESAFÍO %d</h5>
        </div>
    </a></div>
  </div>';
  $html = sprintf($temp, $opacity, $portada, $class, $label, $icon, $num);

  return $html;
}

function juegoStatusICO($status){
  $rpta = '';

  switch ($status) {
    case 'play':
      $rpta = '<i class="fa fa-play amarillo"></i>';
      break;

    case 'lock':
      $rpta = '<i class="fa fa-lock"></i>';
      break;

    case 'win':
      $rpta = '<i class="fa fa-check-circle-o verde"></i>';
      break;
  }

  return $rpta;
}

function genJuegosTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['juego_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['juego_pregunta']. '</a></td>';
      $html .= '<td>'. $item['juego_respuesta']. '</td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genJuegosOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['juego_id'], $cur);
      $html .= '<option value = "'. $item['juego_id']. '" '. $sel. '>'. $item['juego_pregunta']. '</option>';
    }
  }

  return $html;
}

function genJuegosJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]          = intval($item['juego_id']);
      $post["pregunta"]    = html_entity_decode($item['juego_pregunta']);
      $post["respuesta"]   = html_entity_decode($item['juego_respuesta']);
      $post["respuesta_data"]    = html_entity_decode($item['juego_respuesta_data']);
      $post["distractor1"]       = html_entity_decode($item['juego_distractor1']);
      $post["distractor1_data"]  = html_entity_decode($item['juego_distractor1_data']);
      $post["distractor2"]       = html_entity_decode($item['juego_distractor2']);
      $post["distractor2_data"]  = html_entity_decode($item['juego_distractor2_data']);
      $post["distractor3"]       = html_entity_decode($item['juego_distractor3']);
      $post["distractor3_data"]  = html_entity_decode($item['juego_distractor3_data']);
      $post["pista"]     = html_entity_decode($item['juego_pista']);
      $post["feedback"]  = html_entity_decode($item['juego_feedback']);
      $post["portada"]   = html_entity_decode($item['juego_portada']);
      $post["portada"]   = genPathPicture($post["portada"], 'juegos/', 'default.jpg');
      $post["fondo"]     = html_entity_decode($item['juego_fondo']);
      $post["fondo"]     = genPathPicture($post["fondo"], 'juegos/', 'default.jpg');
      $post["master_id"] = $item['tema_id'];

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Preguntas
**/
function genPreguntasTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['pregunta_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['pregunta']. '</a></td>';
      $html .= '<td>'. $item['respuesta_1']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genPreguntasOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['pregunta_id'], $cur);
      $html .= '<option value = "'. $item['pregunta_id']. '" '. $sel. '>'. $item['pregunta']. '</option>';
    }
  }

  return $html;
}

function genPreguntasJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['pregunta_id']);
      $post["pregunta"]  = html_entity_decode($item['pregunta']);
      $post["rpta1"]     = html_entity_decode($item['respuesta_1']);
      $post["rpta2"]     = html_entity_decode($item['respuesta_2']);
      $post["rpta3"]     = html_entity_decode($item['respuesta_3']);
      $post["rpta4"]     = html_entity_decode($item['respuesta_4']);
      $post["master_id"] = $item['curso_id'];

      array_push($response, $post);
    }
  }

  return $response;
}

function genPreguntasOLRnd($resultados){
  $rpta = array('preguntas' => '', 'answers' => '');
  $html = '';
  $id   = '';
  $right  = '';
  $master = array();
  $mini   = array();
  $values = array('a', 'b', 'c', 'd');
  $rptas  = array(1, 2, 3, 4);
  shuffle($rptas);
  $true = false;

  $x = 1; $y = 1;
  if (count($resultados)){
    foreach($resultados as $item){
      shuffle($rptas);
      $id = 'pk'. $x;
      $html .= '<h5 id="title'. $x. '" rel="'. $item['pregunta_id']. '">'. $x. ' '. $item['pregunta']. '</h5>
        <ol type="a" id="'. $id. '">';
      $y = 0;
      foreach ($rptas as $value) {
        $right = ( $value == 1 ) ? $values[$y] : $right;
        $html .= genAlternativasLI($id, $values[$y], $item['respuesta_'. $value]);
        $y++;
      }
      $html .= '</ol>';
      $mini = array($id, $right);
      array_push($master, $mini);
      $x++;
    }
  }

  $rpta['preguntas'] = $html;
  $rpta['answers'] = $master;
  return $rpta;
}

function genAlternativasLI($id, $val, $rpta){
  return '<li><input type="radio" name="'. $id. '" value="'. $val. '" id="'. $id. $val. '"><label for="'. $id. $val. '">'. $rpta. '</label></li>';
}

/**
  Questions
**/
function genQuestionsTR($resultados){
  $html = '';
  if (count($resultados)){
    $id = 0;
    $linkCursos = '';
    foreach($resultados as $item){
      $id = $item['question_id'];

      $html .= '<tr>';
      $html .= '<td><a rel="'. $id. '" class="edit">'. $item['question']. '</a></td>';
      $html .= '<td>'. $item['answer_1']. '</a></td>';
      $html .= '<td class="tableActs">';
      $html .= '  <a rel="'. $id. '" data-reveal-id="myDelete" class="pLeft5 delete rojo" title="Borrar">';
      $html .= '  <i class="fa fa-trash-o fa-lg"></i></a>';
      $html .= '</td></tr>';
    }
  }

  return $html;
}

function genQuestionsOP($resultados, $cur){
  $html = '<option value="-1">Seleccionar ...</option>';

  if (count($resultados)){
    foreach($resultados as $item){
      $sel = isSelected($item['question_id'], $cur);
      $html .= '<option value = "'. $item['question_id']. '" '. $sel. '>'. $item['question']. '</option>';
    }
  }

  return $html;
}

function genQuestionsJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item['question_id']);
      $post["pregunta"]  = html_entity_decode($item['question']);
      $post["rpta1"]     = html_entity_decode($item['answer_1']);
      $post["rpta2"]     = html_entity_decode($item['answer_2']);
      $post["rpta3"]     = html_entity_decode($item['answer_3']);
      $post["rpta4"]     = html_entity_decode($item['answer_4']);
      $post["master"]    = $item['master_table'];
      $post["master_id"] = $item['master_id'];

      array_push($response, $post);
    }
  }

  return $response;
}

function genQuestionsOLRnd($resultados){
  $rpta = array('preguntas' => '', 'answers' => '');
  $html = '';
  $id   = '';
  $right  = '';
  $master = array();
  $mini   = array();
  $values = array('a', 'b', 'c', 'd');
  $rptas  = array(1, 2, 3, 4);
  shuffle($rptas);
  $true = false;

  $x = 1; $y = 1;
  if (count($resultados)){
    foreach($resultados as $item){
      shuffle($rptas);
      $id = 'pk'. $x;
      $html .= '<h5 id="title'. $x. '" rel="'. $item['question_id']. '">'. $x. ' '. $item['question']. '</h5>
        <ol type="a" id="'. $id. '">';
      $y = 0;
      foreach ($rptas as $value) {
        $right = ( $value == 1 ) ? $values[$y] : $right;
        $html .= genAlternativasLI($id, $values[$y], $item['answer_'. $value]);
        $y++;
      }
      $html .= '</ol>';
      $mini = array($id, $right);
      array_push($master, $mini);
      $x++;
    }
  }

  $rpta['preguntas'] = $html;
  $rpta['answers'] = $master;
  return $rpta;
}

function genChoisesLI($id, $val, $rpta){
  return '<li><input type="radio" name="'. $id. '" value="'. $val. '" id="'. $id. $val. '"><label for="'. $id. $val. '">'. $rpta. '</label></li>';
}

/**
  Ejercicio
**/

function genAccionesRnd($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]      = intval($item['juego_id']);
      $post["accion"]  = $item['accion'];
      $post["grupo"]   = $item['grupo'];

      array_push($response, $post);
    }
  }

  return $response;
}

function genAccionesJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["juego_id"]  = intval($item['juego_id']);
      $post["accion"]    = html_entity_decode($item['accion']);
      $post["grupo"]     = html_entity_decode($item['grupo']);

      array_push($response, $post);
    }
  }

  return $response;
}

/**
  Mensajes
**/
function genMensajeTry($intento, $perfect){
  $rpta = array('msg' => '', 'puntos' => 0);

    switch ($intento) {
      case 0: // 1er
        if ($perfect){
          $rpta['msg'] = '¡EXCELENTE!';
          $rpta['puntos'] = 0; //100;
        } else {
          $rpta['msg'] = 'MUY BIEN!';
          $rpta['puntos'] = 0; //75;
        }
        break;

      case 1: // 2do
        if ($perfect){
          $rpta['msg'] = '¡MUY BIEN!';
          $rpta['puntos'] = 0; //75;
        } else {
          $rpta['msg'] = '¡BIEN!';
          $rpta['puntos'] = 0; //50;
        }
        break;

      case 0: // 3ro +
        if ($perfect){
          $rpta['msg'] = '¡ESFUÉRZATE MÁS!';
          $rpta['puntos'] = 0; //20;
        } else {
          $rpta['msg'] = '¡ESFUÉRZATE MÁS!';
          $rpta['puntos'] = 0; //10;
        }
        break;

      default: // retry
        if ($perfect){
          $rpta['msg'] = '¡MUY BIEN!';
          $rpta['puntos'] = 0;
        } else {
          $rpta['msg'] = '¡BIEN!';
          $rpta['puntos'] = 0;
        }
        break;
    }

  return $rpta;
}

/**
  Otros
**/

function genPerfilSmallJS($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["id"]        = intval($item->user_id);
      $post["name"]      = $item->displayname;
      $post["email"]     = $item->email;
      $post["picture"]   = genPathPicture($item->user_picture);
      $post["embajador"] = $item->PackEmbajador;

      array_push($response, $post);
    }
  }

  return $response;
}

function genPrevNext($sessionId, $courseId, $resultadosSessions){
  $btn = array('prev' => '#', 'next' => '#');
  $total = count($resultadosSessions);

  if ($total > 0){
    if ($sessionId > 1){
      $prev = $sessionId - 1;
      $btn['prev'] = '//plataforma.biialab.org/profile/index.php#/classroom/'. $courseId. '/session/'. $prev;
    }
    if ($sessionId < $total){
      $next = $sessionId + 1;
      $btn['next'] = '//plataforma.biialab.org/profile/index.php#/classroom/'. $courseId. '/session/'. $next;
    }
  }
  return $btn;
}

function esBlanco($cad){
  $rpta = $cad;
  if (!isset($cad)) {
    $rpta = "";
  }
  return $rpta;
}

function genCourseInfoJS($courseId, $sessionId, $resultadosCurso, $resultadosSessions, $resultadosRecursos, $resProfe){
  $btn = genPrevNext($sessionId, $courseId, $resultadosSessions);
  $x = $sessionId - 1;
  $rpta = '[{
  "courseId": "'. $courseId. '",
  "courseName": "'. addslashes($resultadosCurso[0]->producto_nombre). '",
  "teacherName": "'. addslashes(esBlanco($resProfe[0]->displayname)). '",
  "teacherPicture": "'. genPathPicture(esBlanco($resProfe[0]->user_picture)). '",
  "teacherId": '. $resultadosCurso[0]->user_id. ',
  "teacherInfo": "'. trim(esBlanco($resProfe[0]->info_personal)). '",
  "sessionCount": '. count($resultadosSessions). ',
  "sessionId": '. $sessionId. ',
  "sessionName": "'. htmlentities($resultadosSessions[$x]->class_titulo). '",
  "sessionDescription": "",
  "sessionVideo": "'. addslashes($resultadosSessions[$x]->class_video). '",
  "sessionUrlVideo": "'. $resultadosSessions[$x]->class_video_url. '",
  "sessionOrder": '. $x. ',
  "sessionPrev": "'. $btn['prev']. '",
  "sessionNext": "'. $btn['next']. '",
  "sessions": '. json_encode(genSesiones($resultadosSessions)). ',
  "exams": [{
    "examId": 0,
    "examUrl": "#",
    "UserStatus": "0"
  }],
  "resources": '. json_encode(genRecursos($resultadosRecursos)). '
}]';

return utf8_encode($rpta);
}

function genCourseInfoJSFake(){
  $rpta = '[{
  "courseId": "1053",
  "courseName": "Coaching de Emprendimiento",
  "teacherName": "Josue Moya",
  "teacherPicture": "http://biia.s3.amazonaws.com/temp/josue.jpg",
  "teacherId": 1,
  "teacherInfo": "",
  "sessionName": "Coaching de Emprendimiento",
  "sessionDescription": "",
  "sessionVideo": "",
  "sessionUrlVideo": "",
  "sessionOrder": 0,
  "sessions": "",
  "exams": [{
    "examId": 0,
    "examUrl": "#",
    "UserStatus": "0"
  }],
  "resources": ""
}]';

return utf8_encode($rpta);
}

function genSesiones($resultados){
  $x = 0;
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["sessionOrder"] = ++$x;
      $post["class_id"]     = intval($item->class_id);
      $post["sessionName"]  = utf8_encode($item->class_titulo);
      $post["UserStatus"]   = 0;
      $post["sessionLink"]  = '#';

      array_push($response, $post);
    }
  }

  return $response;
}

function genRecursos($resultados){
  $response = array();
  if (count($resultados)){
    foreach($resultados as $item){
      $post = array();
      $post["resId"]    = intval($item->adj_id);
      $post["resType"]  = "pdf";
      $post["resName"]  = utf8_encode($item->adj_titulo);
      $post["resUrl"]   = AWS_RECURSOS. utf8_encode($item->adj_uri);

      array_push($response, $post);
    }
  }

  return $response;
}

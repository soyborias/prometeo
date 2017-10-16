<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set( 'display_errors','1');

include_once('../config.php');
include_once('code/fxModelo.php');
include_once('code/fxVista.php');

$action = NULL;
if (isset($_GET['action'])){$action = $_GET['action'];}
if (isset($_POST['action'])){$action = $_POST['action'];}

if ( !empty($action) )
  include_once('code/db_procter.php');
else
  exit( json( array('rpta' => 'FALSE' )) );

$user_id = (isset($_SESSION['userID'])) ? $_SESSION['userID'] : 0;

function json($data){
  if(is_array($data)){
    return json_encode($data);
  }
}

/**
 API General
**/
switch ($action) {
  case 'login':
    $username = $_POST["eUser"];
    $password = $_POST["ePass"];

    $username = strtolower(htmlentities($username, ENT_QUOTES));
    $password = crypt($password, SALT);
    $result = getUserLogin($username, $password, $db);

    if ( $result['rpta'] === 2){
      $_SESSION['username'] = $username;
      $_SESSION['userID']   = $result['usuario_id'];
      $_SESSION['rol']      = $result['usuario_rol'];
      $_SESSION['nombre']   = $result['usuario_nombre'];
      $_SESSION['perfil']   = $result['usuario_perfil'];
      $_SESSION['nivel']    = $result['usuario_nivel'];
      $_SESSION['picture']  = genPathPicture($result['usuario_picture'], 'profile/');
      $_SESSION['puntos']   = getSumaPuntos($result['usuario_id'], 'Ganado', $db);    // Puntos Acumulados
      $_SESSION['puntos2']  = getSumaPuntosCanjeados($result['usuario_id'], $db);     // Puntos Canjeados
      $_SESSION['puntos3']  = $_SESSION['puntos'] - $_SESSION['puntos2'];
      $_SESSION["fchOLD"]   = date("Y-n-j H:i:s"); // Now
      $_SESSION['k'] = strtoupper(md5(uniqid(rand(), true))); //Cte random
      $ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
      setLog($_SESSION['userID'], $ip, $db);
    } else {
      session_unset();
      session_destroy();
    }
    print json($result);
    break;

  case 'newUserRegister':
    $dni      = (isset($_POST['eDni']))   ? $_POST['eDni']    : '';
    $nombre   = (isset($_POST['eNom']))   ? $_POST['eNom']    : '';
    $email    = (isset($_POST['eEmail'])) ? $_POST['eEmail']  : '';
    $pass     = (isset($_POST['ePass']))  ? $_POST['ePass']   : '';

    $dni      = strtolower(htmlentities($dni, ENT_QUOTES));
    $nombre   = strtolower(htmlentities($nombre, ENT_QUOTES));
    $password = crypt($pass, SALT);

    $result = newUserRegister($dni, $nombre, $email, $password, $db);
    print json($result);
    break;

  case 'activateUser':
    $id    = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $nom   = (isset($_POST['eNom'])) ? $_POST['eNom'] : '';
    $email = (isset($_POST['eEmail'])) ? $_POST['eEmail'] : '';
    $result = updateActivateUser($id, 'Si', $db);
    if ($result['status'] == 'ok') {
      // Send Email
      $email_nombre  = $nom;
      $email_destiny = $email;
      $actionEmail   = 'emailActivateUser';
      include_once('fxEmail.php');
      $result['info'] .= $email_result;
    }
    print json($result);
    break;

  case 'saveNewUser': //FULL
    $auth     = (isset($_POST['eAuth']))  ? $_POST['eAuth']   : '';
    $dni      = (isset($_POST['eDni']))   ? $_POST['eDni']    : '';
    $nombre   = (isset($_POST['eNom']))   ? $_POST['eNom']    : '';
    $email    = (isset($_POST['eEmail'])) ? $_POST['eEmail']  : '';
    $pass     = (isset($_POST['ePass']))  ? $_POST['ePass']   : '';
    $rol      = (isset($_POST['eRol']))   ? $_POST['eRol']    : '';
    $sucursal = (isset($_POST['eSucursal'])) ? $_POST['eSucursal']   : '';
    $distro   = (isset($_POST['eDistro']))   ? $_POST['eDistro']     : '';
    $perfil   = (isset($_POST['ePerfil']))   ? $_POST['ePerfil']     : '';
    $super    = (isset($_POST['eSuper']))    ? $_POST['eSuper']      : '';
    $equipo   = (isset($_POST['eEquipo']))   ? $_POST['eEquipo']     : '';

    $dni      = strtolower(htmlentities($dni, ENT_QUOTES));
    $nombre   = strtolower(htmlentities($nombre, ENT_QUOTES));
    $password = crypt($pass, SALT);

    $result = newUserRegisterFull($dni, $nombre, $email, $password, $rol, $super, $perfil, $db);
    $v1 = 0;
    $v2 = 0;
    if ($result['status'] == MSG_OK){
      saveLocation($result['info'], 0, $distro, $sucursal, $db);
      if ($rol == ROL_USER){
        // Save to equipo
        $v1 = newTeamList($equipo, $nombre, 'Usuario', $result['info'], $db);
      } elseif ($rol == ROL_JEFE) {
        // Nada mas
      } elseif ($rol == ROL_SUPERVISOR) {
        // Asigna jefe al supervisor
        $v2 = addJefeSup($auth, $result['info'], $db);
      }
    }
    //print json($result);
    print json(array('rpta' => $result, 'v1' => $v1, 'v2' => $v2 ));
    break;

  case 'sendRecovery':
    $email = (isset($_POST['eEmail'])) ? $_POST['eEmail'] : '';
    $agent = (isset($_POST['eAgent'])) ? $_POST['eAgent'] : '';

    $datos = getUserByEmail($email, $db);
    if ( count($datos)>0 ){
      $email_nombre  = $datos[0]['usuario_nombre'];
      $email_destiny = $datos[0]['usuario_email'];

      $verifyCode = generateRandomPassword();
      updateUserVerifyCode($datos[0]['usuario_id'], $verifyCode, $db);
      $email_link = CANONICAL. '/recordar.php?a='. base64_encode($email_destiny). '&b='. base64_encode($verifyCode);
      $actionEmail   = 'emailRecoveryPass';
      include_once('fxEmail.php');
      $result = array('status' => 'ok' );
    } else { $result = array('status' => 'error' ); }
    print json($result);
    break;

  case 'deactivateUser':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = updateActivateUser($id, 'No', $db);
    print json($result);
    break;

  case 'updateUserPerfil':
    $nombre = (isset($_POST['eNom']))    ? $_POST['eNom']    : '';
    $email  = (isset($_POST['eEmail']))  ? $_POST['eEmail']  : '';
    $tel    = (isset($_POST['eTel']))    ? $_POST['eTel']    : '';
    $fchNac = (isset($_POST['eFchNac'])) ? $_POST['eFchNac'] : '1900-01-01';
    $genero = (isset($_POST['eGenero'])) ? $_POST['eGenero'] : 'Masculino';

    $result = updateUserPerfil($user_id, $nombre, $email, $tel, $fchNac, $genero, $db);
    print json($result);
    break;

    case 'updateUserLaboral':
    $distribuidora = (isset($_POST['eDistribuidora'])) ? $_POST['eDistribuidora']  : -1;
    $sucursal      = (isset($_POST['eSucursal']))      ? $_POST['eSucursal']       : -1;
    $cargo         = (isset($_POST['eCargo']))         ? $_POST['eCargo']          : -1;
    $tVendedor     = (isset($_POST['eTVendedor']))     ? $_POST['eTVendedor']      : -1;
    $supervisor    = (isset($_POST['eSupervisor']))    ? $_POST['eSupervisor']     : -1;

    saveLocation($user_id, 0, $distribuidora, $sucursal, $db);
    $result = setUserLaboral($user_id, $cargo, $tVendedor, $supervisor, $db);
    print json($result);
    break;

  case 'delUser':
    $auth = (isset($_POST['eAuth']))     ? $_POST['eAuth']     : '';
    $rel  = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';

    $result = delUser($rel, $db);
    print json($result);
    break;

  case 'setUserRol':
    $auth = (isset($_POST['eAuth']))     ? $_POST['eAuth']     : '';
    $rel  = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $rol  = (isset($_POST['eRol']))      ? $_POST['eRol']      : '';

    $result = setUserRol($rel, $rol, $db);
    print json($result);
    break;

  case 'setUserPerfil':
    $auth = (isset($_POST['eAuth']))     ? $_POST['eAuth']     : '';
    $rel  = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $per  = (isset($_POST['ePerfil']))   ? $_POST['ePerfil']      : '';

    $result = setUserPerfil($rel, $per, $db);
    print json($result);
    break;

  case 'setUserSupervisor':
    $auth  = (isset($_POST['eAuth']))     ? $_POST['eAuth']     : '';
    $rel   = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $super = (isset($_POST['eSuper']))    ? $_POST['eSuper']    : '';

    $result = setUserSupervisor($rel, $super, $db);
    print json($result);
    break;

  case 'saveChangePass':
    $auth  = (isset($_POST['eAuth']))     ? $_POST['eAuth']     : '';
    $rel   = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $pass1 = (isset($_POST['ePass1']))    ? $_POST['ePass1']    : '';
    $pass2 = (isset($_POST['ePass2']))    ? $_POST['ePass2']    : '';
    $pass3 = (isset($_POST['ePass3']))    ? $_POST['ePass3']    : '';

    $password = crypt($pass3, SALT);
    $verify = verifyLogin($user_id, $password, $db);

    if ($verify === MSG_OK){
      $pass1 = crypt($pass1, SALT);
      $result = saveChangePass($rel, $pass1, $db);
    } else {
      $result = array('status' => MSG_ERROR);
    }

    print json($result);
    break;

  case 'userChangePass':
    $auth  = (isset($_POST['eAuth']))     ? $_POST['eAuth']     : '';
    $a     = (isset($_POST['eVarA']))     ? $_POST['eVarA']     : '';
    $b     = (isset($_POST['eVarB']))     ? $_POST['eVarB']     : '';
    $pass = (isset($_POST['ePass']))     ? $_POST['ePass']     : '';

    $email  = base64_decode($a);
    $verify = base64_decode($b);
    $password = crypt($pass, SALT);

    $usuario = getUserByVerify($email, $verify, $db);
    if ($usuario != MSG_ERROR){
      $result = updateUserPass($usuario, $verify, $password, $db);
    } else {
      $result = array('status' => MSG_ERROR);
    }

    print json($result);
    break;

  case 'searchParticipantes':
    $nom = (isset($_POST['eNom'])) ? $_POST['eNom'] : 0;
    $doc = (isset($_POST['eDoc'])) ? $_POST['eDoc'] : 0;
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $filter = array('nom' => $nom, 'doc' => $doc, 'distro' => '', 'ciudad' => '', 'sexo' => '' );
    $resultados = getUsuariosBySearchSmall($filter, $_SESSION['equipo_filtro'], $db);
    $result = genUsuariosSearchJS($resultados);
    print json($result);
    break;

/**
  TEAM LIST
**/
  case 'getTeamLists':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getTeamLists($rel, $db);
    $result = genTeamListsJS($resultados);
    print json($result);
    break;

  case 'getTeamList':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getTeamListsById($id, $db);
    $result = genTeamListsJS($resultados);
    print json($result);
    break;

  case 'getTeamListByTeam':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getTeamListByTeam($id, $db);
    //$result = genTeamListsJS($resultados);
    $result = genTeamListsJSDiv($resultados);
    print json($result);
    break;

  case 'newTeamList':
    $identyId = uniqid();
    $team_id      = (isset($_POST['eRel']))        ? $_POST['eRel']        : '';
    $list_name    = (isset($_POST['eListName']))   ? $_POST['eListName']   : '';
    $list_type    = (isset($_POST['eListType']))   ? $_POST['eListType']   : '';
    $list_type_id = (isset($_POST['eListTypeID'])) ? $_POST['eListTypeID'] : '';

    $value = newTeamList($team_id, $list_name, $list_type, $list_type_id, $db);
    print $value;
    break;

  case 'updateTeamList':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $image    = (isset($_POST['eImage']))    ? $_POST['eImage']    : '';
    $distID   = (isset($_POST['ePerfilID'])) ? $_POST['ePerfilID'] : '';

    $value = updateTeamList($code, $nombre, $descrip, $distID, $db);
    print $value;
    break;

  case 'delTeamList':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delTeamList($id, $db);
    print $result;
    break;

/**
  NIVELES
**/
  case 'getNiveles':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getNiveles($rel, $db);
    $result = genNivelesJS($resultados);
    print json($result);
    break;

  case 'getNivel':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getNivelById($id, $db);
    $result = genNivelesJS($resultados);
    print json($result);
    break;

  case 'newNivel':
    $identyId = uniqid();
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $images   = (isset($_POST['ePics']))     ? $_POST['ePics']     : '';
    $perfilID = (isset($_POST['ePerfilID'])) ? $_POST['ePerfilID'] : '';

    $value = newNivel($nombre, $descrip, $perfilID, $images, $db);
    print $value;
    break;

  case 'updateNivel':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $images   = (isset($_POST['ePics']))     ? $_POST['ePics']     : '';
    $perfilID = (isset($_POST['ePerfilID'])) ? $_POST['ePerfilID'] : '';

    $value = updateNivel($code, $nombre, $descrip, $perfilID, $images, $db);
    print $value;
    break;

  case 'delNivel':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delNivel($id, $db);
    print $result;
    break;

/**
  PERFILES
**/
  case 'getPerfiles':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getPerfiles($rel, $db);
    $result = genPerfilesJS($resultados);
    print json($result);
    break;

  case 'getPerfil':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getPerfilById($id, $db);
    $result = genPerfilesJS($resultados);
    print json($result);
    break;

  case 'newPerfil':
    $identyId = uniqid();
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $distID   = (isset($_POST['eDistID']))   ? $_POST['eDistID']   : '';

    $value = newPerfil($nombre, $descrip, $distID, $db);
    print $value;
    break;

  case 'updatePerfil':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $distID   = (isset($_POST['eDistID']))   ? $_POST['eDistID']   : '';

    $value = updatePerfil($code, $nombre, $descrip, $distID, $db);
    print $value;
    break;

  case 'delPerfil':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delPerfil($id, $db);
    print $result;
    break;

/**
  EQUIPOS
**/
  case 'getEquipos':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getEquipos($rel, $db);
    $result = genEquiposJS($resultados);
    print json($result);
    break;

  case 'getEquipo':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getEquipoById($id, $db);
    $result = genEquiposJS($resultados);
    print json($result);
    break;

  case 'newEquipo':
    $identyId = uniqid();
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $distID   = (isset($_POST['eDistID']))   ? $_POST['eDistID']   : '';
    $sucID    = (isset($_POST['eSucID']))    ? $_POST['eSucID']    : '';
    $superID  = (isset($_POST['eSuperID']))  ? $_POST['eSuperID']  : '';

    $value = newEquipo($nombre, $descrip, $distID, $sucID, $superID, $db);
    print $value;
    break;

  case 'updateEquipo':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $distID   = (isset($_POST['eDistID']))   ? $_POST['eDistID']   : '';
    $sucID    = (isset($_POST['eSucID']))    ? $_POST['eSucID']    : '';
    $superID  = (isset($_POST['eSuperID']))  ? $_POST['eSuperID']  : '';

    $value = updateEquipo($code, $nombre, $descrip, $distID, $sucID, $superID, $db);
    print $value;
    break;

  case 'delEquipo':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delEquipo($id, $db);
    print $result;
    break;

  case 'delEquipoMiembro':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delEquipoMiembro($id, $db);
    print $result;
    break;

/**
  CURRICULA
**/
  case 'getCurriculas':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getCurriculas($rel, $db);
    $result = genCurriculasJS($resultados);
    print json($result);
    break;

  case 'getCurricula':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getCurriculaByPerfil($id, $db);
    $result = genCurriculasJS($resultados);
    print json($result);
    break;

  case 'newCurricula':
    $identyId = uniqid();
    $entrenamientos  = (isset($_POST['eEntrena']))    ? $_POST['eEntrena']    :  array(0);
    $novedades       = (isset($_POST['eNovedad']))    ? $_POST['eNovedad']    :  array(0);
    $perfil          = (isset($_POST['ePerfil']))     ? $_POST['ePerfil']     :  -1;
    $entrenamientos  = ( is_array($entrenamientos) ) ? implode(',', $entrenamientos) :  '-1';
    $novedades       = ( is_array($novedades) )      ? implode(',', $novedades)      :  '-1';

    $value = newCurricula($entrenamientos, $novedades, $perfil, $db);
    print $value;
    break;

  case 'updateCurricula':
    $code            = (isset($_POST['eRel']))       ? $_POST['eRel']        : '';
    $entrenamientos  = (isset($_POST['eEntrena']))   ? $_POST['eEntrena']    : array(0);
    $novedades       = (isset($_POST['eNovedad']))   ? $_POST['eNovedad']    : array(0);
    $perfil          = (isset($_POST['ePerfil']))    ? $_POST['ePerfil']     : -1;
    $entrenamientos  = ( is_array($entrenamientos) ) ? implode(',', $entrenamientos) :  '-1';
    $novedades       = ( is_array($novedades) )      ? implode(',', $novedades)      :  '-1';

    $value = updateCurricula($code, $entrenamientos, $novedades, $perfil, $db);
    print $value;
    break;

  case 'delCurricula':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delEquipo($id, $db);
    print $result;
    break;

/**
  SUCURSALES
**/
  case 'getSucursales':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getSucursales($rel, $db);
    $result = genSucursalesJS($resultados);
    print json($result);
    break;

  case 'getSucursal':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getSucursalById($id, $db);
    $result = genSucursalesJS($resultados);
    print json($result);
    break;

  case 'newSucursal':
    $identyId = uniqid();
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $distID   = (isset($_POST['eDistID']))   ? $_POST['eDistID']   : '';

    $value = newSucursal($nombre, $descrip, $distID, $db);
    print $value;
    break;

  case 'updateSucursal':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $distID   = (isset($_POST['eDistID']))   ? $_POST['eDistID']   : '';

    $value = updateSucursal($code, $nombre, $descrip, $distID, $db);
    print $value;
    break;

  case 'delSucursal':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delSucursal($id, $db);
    print $result;
    break;

/**
  ENTRENAMIENTOS
**/
  case 'viewEntrenamientos':
    $filter     = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = ( strlen($filter)>0 ) ? getEntrenamientosVarios($filter, $db)  :  array();
    $result     = genEntrenamientosJSmin($resultados);
    print json($result);
    break;

  case 'getEntrenamientos':
    $resultados = getEntrenamientos($db);
    $result = genEntrenamientosJS($resultados);
    print json($result);
    break;

  case 'getEntrenamiento':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getEntrenamientoById($id, $db);
    $result = genEntrenamientosJS($resultados);
    print json($result);
    break;

  case 'newEntrenamiento':
    $identyId = uniqid();
    $nombre   = (isset($_POST['eNombre']))   ?  $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ?  $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ?  $_POST['eDescrip']  : '';
    $activo   = (isset($_POST['eActivo']))   ?  $_POST['eActivo']   : '';
    $pic1     = (isset($_POST['ePic1']))     ?  $_POST['ePic1']     : '';
    $pic2     = (isset($_POST['ePic2']))     ?  $_POST['ePic2']     : '';
    $pic3     = (isset($_POST['ePic3']))     ?  $_POST['ePic3']     : '';

    $value = newEntrenamiento($nombre, $objetivo, $descrip, $activo, $pic1, $pic2, $pic3, $db);
    print $value;
    break;

  case 'updateEntrenamiento':
    $code     = (isset($_POST['eRel']))      ?  $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ?  $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ?  $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ?  $_POST['eDescrip']  : '';
    $activo   = (isset($_POST['eActivo']))   ?  $_POST['eActivo']   : '';
    $pic1     = (isset($_POST['ePic1']))     ?  $_POST['ePic1']     : '';
    $pic2     = (isset($_POST['ePic2']))     ?  $_POST['ePic2']     : '';
    $pic3     = (isset($_POST['ePic3']))     ?  $_POST['ePic3']     : '';

    $value = updateEntrenamiento($code, $nombre, $objetivo, $descrip, $activo, $pic1, $pic2, $pic3, $db);
    print $value;
    break;

  case 'delEntrenamiento':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delEntrenamiento($id, $db);
    print $result;
    break;

/**
  CURSOS
**/
  case 'getCursos':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getCursos($rel, $db);
    $result = genCursosJS($resultados);
    print json($result);
    break;

  case 'getCurso':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getCursoById($id, $db);
    $result = genCursosJS($resultados);
    print json($result);
    break;

  case 'newCurso':
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ? $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $video    = (isset($_POST['eVideo']))    ? $_POST['eVideo']    : '';
    $master   = (isset($_POST['eMaster']))   ? $_POST['eMaster']   : 0;

    $value = newCurso($nombre, $objetivo, $descrip, $video, $master, $db);
    print $value;
    break;

  case 'updateCurso':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ? $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $video    = (isset($_POST['eVideo']))    ? $_POST['eVideo']    : '';
    $master   = (isset($_POST['eMaster']))   ? $_POST['eMaster']   : 0;

    $value = updateCurso($code, $nombre, $objetivo, $descrip, $video, $master, $db);
    print $value;
    break;

  case 'delCurso':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delCurso($id, $db);
    print $result;
    break;

/**
  TEMAS
**/
  case 'getTemas':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getTemas($rel, $db);
    $result = genTemasJS($resultados);
    print json($result);
    break;

  case 'getTema':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getTemaById($id, $db);
    $result = genTemasJS($resultados);
    print json($result);
    break;

  case 'newTema':
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ? $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $tipoM    = (isset($_POST['eChkTM']))    ? $_POST['eChkTM']    : '';
    $material = (isset($_POST['eMaterial'])) ? $_POST['eMaterial'] : '';
    $master   = (isset($_POST['eMasterID'])) ? $_POST['eMasterID'] : 0;

    $value = newTema($nombre, $objetivo, $descrip, $tipoM, $material, $master, $db);
    print $value;
    break;

  case 'updateTema':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ? $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $tipoM    = (isset($_POST['eChkTM']))    ? $_POST['eChkTM']    : '';
    $material = (isset($_POST['eMaterial'])) ? $_POST['eMaterial'] : '';
    $master   = (isset($_POST['eMasterID'])) ? $_POST['eMasterID'] : 0;

    $value = updateTema($code, $nombre, $objetivo, $descrip, $tipoM, $material, $master, $db);
    print $value;
    break;

  case 'delTema':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delTema($id, $db);
    print $result;
    break;

/**
  NOVEDADES
**/
  case 'getNovedades':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getNovedades($rel, $db);
    $result = genNovedadesJS($resultados);
    print json($result);
    break;

  case 'getNovedad':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getNovedadById($id, $db);
    $result = genNovedadesJS($resultados);
    print json($result);
    break;

  case 'newNovedad':
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ? $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $tipoM    = (isset($_POST['eChkTM']))    ? $_POST['eChkTM']    : '';
    $material = (isset($_POST['eMaterial'])) ? $_POST['eMaterial'] : '';

    $value = newNovedad($nombre, $objetivo, $descrip, $tipoM, $material, $db);
    print $value;
    break;

  case 'updateNovedad':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $objetivo = (isset($_POST['eObjetivo'])) ? $_POST['eObjetivo'] : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';
    $tipoM    = (isset($_POST['eChkTM']))    ? $_POST['eChkTM']    : '';
    $material = (isset($_POST['eMaterial'])) ? $_POST['eMaterial'] : '';

    $value = updateNovedad($code, $nombre, $objetivo, $descrip, $tipoM, $material, $db);
    print $value;
    break;

  case 'delNovedad':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delNovedad($id, $db);
    print $result;
    break;

/**
  PREGUNTAS
**/
  case 'getPreguntas':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getPreguntas($rel, $db);
    $result = genPreguntasJS($resultados);
    print json($result);
    break;

  case 'getPregunta':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getPreguntaById($id, $db);
    $result = genPreguntasJS($resultados);
    print json($result);
    break;

  case 'newPregunta':
    $pregunta = (isset($_POST['ePregunta'])) ? $_POST['ePregunta'] : '';
    $rpta1    = (isset($_POST['eRpta1']))    ? $_POST['eRpta1']    : '';
    $rpta2    = (isset($_POST['eRpta2']))    ? $_POST['eRpta2']    : '';
    $rpta3    = (isset($_POST['eRpta3']))    ? $_POST['eRpta3']    : '';
    $rpta4    = (isset($_POST['eRpta4']))    ? $_POST['eRpta4']    : '';
    $master   = (isset($_POST['eMaster']))   ? $_POST['eMaster']   : 0;

    $value = newPregunta($pregunta, $rpta1, $rpta2, $rpta3, $rpta4, $master, $db);
    print $value;
    break;

  case 'updatePregunta':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $pregunta = (isset($_POST['ePregunta'])) ? $_POST['ePregunta'] : '';
    $rpta1    = (isset($_POST['eRpta1']))    ? $_POST['eRpta1']    : '';
    $rpta2    = (isset($_POST['eRpta2']))    ? $_POST['eRpta2']    : '';
    $rpta3    = (isset($_POST['eRpta3']))    ? $_POST['eRpta3']    : '';
    $rpta4    = (isset($_POST['eRpta4']))    ? $_POST['eRpta4']    : '';
    $master   = (isset($_POST['eMaster']))   ? $_POST['eMaster']   : 0;

    $value = updatePregunta($code, $pregunta, $rpta1, $rpta2, $rpta3, $rpta4, $master, $db);
    print $value;
    break;

  case 'delPregunta':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delPregunta($id, $db);
    print $result;
    break;

/**
  QUESTIONS
**/
  case 'getQuestions':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getQuestions($rel, $db);
    $result = genQuestionsJS($resultados);
    print json($result);
    break;

  case 'getQuestion':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getQuestionById($id, $db);
    $result = genQuestionsJS($resultados);
    print json($result);
    break;

  case 'newQuestion':
    $typeQ     = (isset($_POST['eTypeQ']))    ? $_POST['eTypeQ']    : '';
    $question  = (isset($_POST['eQuestion'])) ? $_POST['eQuestion'] : '';
    $answer_1  = (isset($_POST['eAnswer_1'])) ? $_POST['eAnswer_1'] : '';
    $answer_2  = (isset($_POST['eAnswer_2'])) ? $_POST['eAnswer_2'] : '';
    $answer_3  = (isset($_POST['eAnswer_3'])) ? $_POST['eAnswer_3'] : '';
    $answer_4  = (isset($_POST['eAnswer_4'])) ? $_POST['eAnswer_4'] : '';
    $master    = (isset($_POST['eMaster']))   ? $_POST['eMaster']   : 0;
    $masterId  = (isset($_POST['eMasterId'])) ? $_POST['eMasterId'] : 0;

    $result = newQuestion($typeQ, $question, $answer_1, $answer_2, $answer_3, $answer_4, $master, $masterId, $db);
    print json($result);
    break;

  case 'updateQuestion':
    $code      = (isset($_POST['eRel']))      ? $_POST['eRel']      : 0;
    $typeQ     = (isset($_POST['eTypeQ']))    ? $_POST['eTypeQ']    : 0;
    $question  = (isset($_POST['eQuestion'])) ? $_POST['eQuestion'] : '';
    $answer_1  = (isset($_POST['eAnswer_1'])) ? $_POST['eAnswer_1'] : '';
    $answer_2  = (isset($_POST['eAnswer_2'])) ? $_POST['eAnswer_2'] : '';
    $answer_3  = (isset($_POST['eAnswer_3'])) ? $_POST['eAnswer_3'] : '';
    $answer_4  = (isset($_POST['eAnswer_4'])) ? $_POST['eAnswer_4'] : '';
    $master    = (isset($_POST['eMaster']))   ? $_POST['eMaster']   : 0;
    $masterId  = (isset($_POST['eMasterId'])) ? $_POST['eMasterId'] : 0;

    $result = updateQuestion($code, $typeQ, $question, $answer_1, $answer_2, $answer_3, $answer_4, $master, $masterId, $db);
    print json($result);
    break;

  case 'delQuestion':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delQuestion($id, $db);
    print json($result);
    break;

/**
  JUEGOS
**/
  case 'getJuegos':
    $rel = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $tj  = (isset($_POST['eJuegoTipo'])) ? $_POST['eJuegoTipo'] : 0;
    $resultados = getJuegos($rel, $tj, $db);
    $result = genJuegosJS($resultados);
    print json($result);
    break;

  case 'getJuego':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $tj  = (isset($_POST['eJuegoTipo'])) ? $_POST['eJuegoTipo'] : 0;
    $resultados = getJuegoById($id, $db);
    $result = genJuegosJS($resultados);
    print json($result);
    break;

  case 'newJuego':
    $tJuego      = (isset($_POST['eJuegoTipo']))   ? $_POST['eJuegoTipo']   : '';
    $pregunta    = (isset($_POST['ePregunta']))    ? $_POST['ePregunta']    : '';
    $correcta    = (isset($_POST['eCorrecta']))    ? $_POST['eCorrecta']    : '';
    $distractor1 = (isset($_POST['eDistractor1'])) ? $_POST['eDistractor1'] : '';
    $distractor2 = (isset($_POST['eDistractor2'])) ? $_POST['eDistractor2'] : '';
    $distractor3 = (isset($_POST['eDistractor3'])) ? $_POST['eDistractor3'] : '';
    $pista       = (isset($_POST['ePista']))       ? $_POST['ePista']       : '';
    $feedback    = (isset($_POST['eFeedback']))    ? $_POST['eFeedback']    : '';
    $pic1        = (isset($_POST['ePic1']))        ? $_POST['ePic1']        : '';
    $pic2        = (isset($_POST['ePic2']))        ? $_POST['ePic2']        : '';
    $master      = (isset($_POST['eMasterID']))    ? $_POST['eMasterID']    : 0;

    $value = newJuego($tJuego, $pregunta, $correcta, $distractor1, $distractor2, $distractor3, $pista, $feedback, $pic1, $pic2, $master, $db);
    print $value;
    break;

  case 'updateJuego':
    $code        = (isset($_POST['eRel']))         ? $_POST['eRel']         : '';
    $tJuego      = (isset($_POST['eJuegoTipo']))   ? $_POST['eJuegoTipo']   : '';
    $pregunta    = (isset($_POST['ePregunta']))    ? $_POST['ePregunta']    : '';
    $correcta    = (isset($_POST['eCorrecta']))    ? $_POST['eCorrecta']    : '';
    $distractor1 = (isset($_POST['eDistractor1'])) ? $_POST['eDistractor1'] : '';
    $distractor2 = (isset($_POST['eDistractor2'])) ? $_POST['eDistractor2'] : '';
    $distractor3 = (isset($_POST['eDistractor3'])) ? $_POST['eDistractor3'] : '';
    $pista       = (isset($_POST['ePista']))       ? $_POST['ePista']       : '';
    $feedback    = (isset($_POST['eFeedback']))    ? $_POST['eFeedback']    : '';
    $pic1        = (isset($_POST['ePic1']))        ? $_POST['ePic1']        : '';
    $pic2        = (isset($_POST['ePic2']))        ? $_POST['ePic2']        : '';
    $master      = (isset($_POST['eMasterID']))    ? $_POST['eMasterID']    : 0;

    $value = updateJuego($code, $tJuego, $pregunta, $correcta, $distractor1, $distractor2, $distractor3, $pista, $feedback, $pic1, $pic2, $master, $db);
    print $value;
    break;

  case 'delJuego':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delJuego($id, $db);
    print $result;
    break;

/**
  ACCIONES
**/
  case 'updateAction':
    $linaje = (isset($_POST['eLinaje'])) ? $_POST['eLinaje']  : '';
    $hito   = (isset($_POST['eHito']))   ? $_POST['eHito']    : '';
    $hitoID = (isset($_POST['eHitoID'])) ? $_POST['eHitoID']  : 0;
    $tblNom = (isset($_POST['eTabla']))  ? $_POST['eTabla']   : '';
    $tblID  = (isset($_POST['eRel']))    ? $_POST['eRel']     : 0;
    $firts  = (isset($_POST['eFirts']))  ? $_POST['eFirts']   : 0;
    $status = (isset($_POST['eStatus'])) ? $_POST['eStatus']  : 0;
    $status2   = (isset($_POST['eStatus2'])) ? $_POST['eStatus2']  : 'No';
    $avance    = 0; $rpta = '';
    $arrLinaje = explode(',', $linaje);
    $firts  = intval($firts);
    $status = intval($status);

    //check duply
    $duply  = isActionDuply($user_id, $hitoID, $tblNom, $tblID, $db);
    $duply  = intval($duply);

    $campo   = ($duply == 0) ? 'hito_puntaje_1' : 'hito_puntaje_3';
    $estado  = ($duply == 0) ? 'Ganado'         : 'Duplicado';
    $puntaje = getPuntajeHito($hito, $campo, $db);
    $porcentaje = 0;
    $avance  = 0;

    switch ($hito) {
      case 'cursoVerVideo':
        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
          // auto ENROLL 1er curso
          if ($firts == 1){ $debug = setEnrollStatus($user_id, 'pg_cursos', $arrLinaje[1], $arrLinaje, 'Start', $db); }
          $debug = print_r($rpta, true);
        }
        if ($firts == 0 && $status == 0){
          // No Autorizado
          $estado  = 'Temporal';
          $avance  = 0;
          $puntaje = 0;
        }
        $aciertos = 0;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        $_SESSION['puntos'] += $puntaje;
        break;

      case 'cursoCuestionarioPerfecto':
      case 'cursoCuestionarioNormal':
        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
          // enable tema 1 del curso actual (current linaje)
          $debug = setEnrollStatus($user_id, 'pg_preguntas', $arrLinaje[2], $arrLinaje, 'Finish', $db);
          $debug = setEnrollStatus($user_id, 'pg_temas', $arrLinaje[2], $arrLinaje, 'Start', $db);
          $debug = print_r($debug, true);
        }
        // Calc % acierto :: puntajeReal / puntajeMax
        $PjeMax   = 10;
        $aciertos = $puntaje/$PjeMax;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        $_SESSION['puntos'] += $puntaje;
        break;

      case 'temaVerMaterial':
        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
          $debug  = setEnrollStatus($user_id, 'pg_juegos1', $arrLinaje[2], $arrLinaje, 'Start', $db);
          $debug  = setEnrollStatus($user_id, 'pg_juegos2', $arrLinaje[2], $arrLinaje, 'Start', $db);
          $debug  = setEnrollStatus($user_id, 'pg_juegos3', $arrLinaje[2], $arrLinaje, 'Start', $db);
          $debug = print_r($rpta, true);
        }
        $aciertos = 0;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        $_SESSION['puntos'] += $puntaje;
        break;

      case 'novedadVerMaterial':
      case 'cursoIniciarVideo':
        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
        }
        $PjeMax   = 10;
        $aciertos = $puntaje/$PjeMax;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        $_SESSION['puntos'] += $puntaje;
        break;

      case 'novedadCuestionarioPerfecto':
      case 'novedadCuestionarioNormal':
        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
        }
        $aciertos = 0;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        $_SESSION['puntos'] += $puntaje;
        break;

      case 'juegoFinal1':
      case 'juegoFinal2':
        $game    = (isset($_POST['eGame'])) ?  $_POST['eGame']  :  0;
        $pid     = (isset($_POST['ePid']))  ?  $_POST['ePid']   :  0;
        $tblNext = (isset($_POST['eTblNext']))  ?  $_POST['eTblNext']  : '';
        $campo   = ($duply == 0) ? 'hito_puntaje_1' : 'hito_puntaje_3';
        $estado  = ($duply == 0) ? 'Ganado'         : 'Duplicado';
        //$estado  = ($duply == 0) ? 'Temporal'       : 'Duplicado';
        $game   .= $pid;
        $puntaje = $_SESSION[$game]['puntos'];
        $_SESSION['puntos'] += $puntaje; // check
        $avance  = 0;

        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
        }
        // Calc % acierto :: puntajeReal / puntajeMax
        $PjeMax   = 50;
        $aciertos = $puntaje/$PjeMax;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        // Terminar current & empezar nextJuego
        setEnrollStatus($user_id, $tblNom, $arrLinaje[2], $arrLinaje, 'Finish', $db);
        setEnrollStatus($user_id, $tblNext, $arrLinaje[2], $arrLinaje, 'Start', $db);

        //check if finish 3 juegos x tema => cierra tema
        //check if finish temas => cierra curso
        //check if finish cursos => cierra entrenamiento
        break;

      case 'juegoFinal3':
        $game    = (isset($_POST['eGame'])) ?  $_POST['eGame']  :  0;
        $pid     = (isset($_POST['ePid']))  ?  $_POST['ePid']   :  0;
        $tblNext = (isset($_POST['eTblNext']))  ?  $_POST['eTblNext']  : '';
        $next     = (isset($_POST['eTblNext']))  ?  $_POST['eTblNext']  : '';
        $linaje2  = (isset($_POST['eLinaje2']))  ?  $_POST['eLinaje2']  : '';
        $arrLin2  = explode(',', $linaje2);
        $campo    = ($duply == 0) ? 'hito_puntaje_1' : 'hito_puntaje_3';
        $estado   = ($duply == 0) ? 'Ganado'         : 'Duplicado';
        $game    .= $pid;
        //$puntaje  = getPuntajeHito($hito, $campo, $db);
        $puntaje  = $_SESSION[$game]['puntos'];
        $_SESSION['puntos'] += $puntaje;
        $avance   = 0;
        $debug    = $linaje2; // Check pls

        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
          //$debug  = $porcentaje. ' - '. $avance;
        }
        if ($status === 0){
          $avance  = 0;
          $puntaje = 0;
        }
        // Calc % acierto :: puntajeReal / puntajeMax
        $PjeMax   = 50;
        $aciertos = $puntaje/$PjeMax;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        // Finish current => close related && Empezar next
        setEnrollStatus($user_id, $tblNom, $arrLinaje[2], $arrLinaje, 'Finish', $db);
        if ($next === 'tema') {
          // check
          setEnrollStatus($user_id, 'pg_temas', $arrLinaje[2], $arrLinaje, 'Finish', $db);
          setEnrollStatus($user_id, 'pg_temas', $arrLin2[2], $arrLin2, 'Start', $db);
        }
        if ($next === 'curso') {
          setEnrollStatus($user_id, 'pg_temas', $arrLinaje[2], $arrLinaje, 'Finish', $db);
          setEnrollStatus($user_id, 'pg_cursos', $arrLin2[1], $arrLin2, 'Start', $db);
        }
        if ($next === 'entrenamiento' || $next === 'dashboard') {
          setEnrollStatus($user_id, 'pg_temas', $arrLinaje[2], $arrLinaje, 'Finish', $db);
          setEnrollStatus($user_id, 'pg_cursos', $arrLinaje[1], $arrLinaje, 'Finish', $db);
          setEnrollStatus($user_id, 'pg_entrenamiento', $arrLinaje[0], $arrLinaje, 'Finish', $db);
          // Win Trofeo
          $trofeo_aciertos = getAciertoByEnt($user_id, $arrLinaje[0], $db);
          $debug = $trofeo_aciertos;
          newTrofeo($user_id, $trofeo_aciertos, $arrLinaje[0], $db);
          // Upgrade nivel ...

        }

        // Reset
        //$_SESSION[$game]['puntos'] = 0;
        $_SESSION['j1-'. $tblID]['puntos'] = 0;
        $_SESSION['j2-'. $tblID]['puntos'] = 0;
        $_SESSION['j3-'. $tblID]['puntos'] = 0;
        break;

      case 'ejercicioFinalPerfecto':
      case 'ejercicioFinalNormal':
        $rptas    = (isset($_POST['eRptas']))  ?  $_POST['eRptas']  : 0;
        $pa       = (isset($_POST['ePA']))     ?  $_POST['ePA']     : 0;
        $next     = (isset($_POST['eTblNext']))  ?  $_POST['eTblNext']  : '';
        $linaje2  = (isset($_POST['eLinaje2']))  ?  $_POST['eLinaje2']  : '';
        $arrLin2  = explode(',', $linaje2);
        $campo    = ($duply == 0) ? 'hito_puntaje_1' : 'hito_puntaje_3';
        $estado   = ($duply == 0) ? 'Ganado'         : 'Duplicado';
        $puntos3  = $rptas*$pa;
        $puntaje  = getPuntajeHito($hito, $campo, $db);
        // Sum puntos J1 + J2 + J3
        $puntaje += $_SESSION['j1-'. $tblID]['puntos'] + $_SESSION['j2-'. $tblID]['puntos'] + $puntos3;
        $_SESSION['puntos'] += $puntaje;
        $avance   = 0;
        $debug    = $linaje2; // Check pls

        if ($duply == 0){
          $numC   = getNumC($arrLinaje[0], $db);
          $numT   = getNumT($arrLinaje[1], $db);
          $porcentaje = getPorcentajeHito($hitoID, $db);
          $avance = calcPorcentajeAvance($hito, $porcentaje, $numC, $numT);
          $rpta   = sumarAvanceEntrenamiento($user_id, $avance, $arrLinaje[0], $db);
          //$debug  = $porcentaje. ' - '. $avance;
        }
        if ($status === 0){
          $avance  = 0;
          $puntaje = 0;
        }
        $aciertos = 0;
        newAccion($user_id, $hitoID, $puntaje, $estado, $avance, $arrLinaje[0], $arrLinaje[1], $arrLinaje[2], $tblNom, $tblID, $aciertos, $db);
        // Finish current => close related && Empezar next
        setEnrollStatus($user_id, $tblNom, $arrLinaje[2], $arrLinaje, 'Finish', $db);
        if ($next === 'tema') {
          setEnrollStatus($user_id, 'pg_cursos', $arrLinaje[1], $arrLinaje, 'Finish', $db);
          setEnrollStatus($user_id, 'pg_temas', $arrLin2[2], $arrLin2, 'Start', $db);
        }
        if ($next === 'curso') {
         setEnrollStatus($user_id, 'pg_cursos', $arrLin2[1], $arrLin2, 'Start', $db);
        }
        if ($next === 'dashboard') {
          setEnrollStatus($user_id, 'pg_cursos', $arrLinaje[1], $arrLinaje, 'Finish', $db);
          setEnrollStatus($user_id, 'pg_entrenamiento', $arrLinaje[0], $arrLinaje, 'Finish', $db);
        }

        // Reset
        //$_SESSION[$game]['puntos'] = 0;
        $_SESSION['j1-'. $tblID]['puntos'] = 0;
        $_SESSION['j2-'. $tblID]['puntos'] = 0;
        break;

      default:
        $estado = 'vacio';
        break;
    }

    print json(array('rpta' => $puntaje, 'data' => $campo. ', '. $duply. ', '. $estado. ', +'. $avance. ' :: '. $debug ));
    break;

/**
  DISTRIBUIDORA
**/
  case 'getDistribuidoras':
    $resultados = getDistribuidoras($db);
    $result = genDistribuidorasJS($resultados);
    echo json($result);
    break;

  case 'getDistribuidora':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $resultados = getDistribuidoraById($id, $db);
    $result = genDistribuidorasJS($resultados);
    echo json($result);
    break;

  case 'newDistribuidora':
    $identyId = uniqid();
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';

    $value = newDistribuidora($nombre, $descrip, $db);
    echo $value;
    break;

  case 'updateDistribuidora':
    $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
    $nombre   = (isset($_POST['eNombre']))   ? $_POST['eNombre']   : '';
    $descrip  = (isset($_POST['eDescrip']))  ? $_POST['eDescrip']  : '';

    $value = updateDistribuidora($code, $nombre, $descrip, $db);
    echo $value;
    break;

  case 'delDistribuidora':
    $id = (isset($_POST['eRel'])) ? $_POST['eRel'] : 0;
    $result = delDistribuidora($id, $db);
    echo $result;
    break;

/**
  Location
**/
  case 'saveLocation':
  $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
  $locID    = (isset($_POST['eLoc']))      ? $_POST['eLoc']      : '';
  $locD     = (isset($_POST['eLocD']))     ? $_POST['eLocD']     : '';
  $locS     = (isset($_POST['eLocS']))     ? $_POST['eLocS']     : '';

  $result   = saveLocation($code, $locID, $locD, $locS, $db);
  print json($result);
  break;

/**
  Jefe Supervisor
**/
  case 'SaveSupJefe':
  $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      :  '';
  $sup      = (isset($_POST['eSup']))      ? $_POST['eSup']      :  array();
  $sup      = ( is_array($sup) ) ? implode(',', $sup)            :  '-1';

  $result   = saveJefeSup($code, $sup, $db);
  print json($result);
  break;

/**
  Supervisor Custom
**/
  case 'SavePerfilSup':
  $code     = (isset($_POST['eRel']))      ? $_POST['eRel']      : '';
  $rel_id   = (isset($_POST['eRelId']))    ? $_POST['eRelId']    : '';
  $rel_name = 'perfil_sup';

  $result   = savePerfilSup($code, $rel_name, $rel_id, $db);
  print json($result);
  break;

/**
  Webservices
**/
  case 'GetUserData':
    $id = (isset($_POST['document'])) ? $_POST['document'] : 0;
    //$resultados = GetUserData($id, $db);
    $resultados = array(1,2,3);
    $result = GenUserDataJS($resultados);
    echo json($result);
    break;

  case 'SetScoreUser':
    $id = (isset($_POST['document'])) ? $_POST['document'] : 0;
    $score  = (isset($_POST['score'])) ? $_POST['score'] : 0;
    $reward = (isset($_POST['reward_code'])) ? $_POST['reward_code'] : 0;
    //$resultados = SetScoreUser($id, $score, $reward, $db);
    $resultados = array(1,2,3);
    $result = genScoreUser($resultados);
    echo json($result);
    break;

  case 'SetRewards':
    $code = (isset($_POST['rewards'])) ? $_POST['rewards'] : 0;
    //$resultados = setRewards($code, $db);
    $resultados = array(1,2,3);
    $result = genRewards($resultados);
    echo json($result);
    break;

/**
  Games
**/
  case 'saveGameWin':
  case 'saveGameWinFull':
    $rel  = (isset($_POST['eRel'])) ? $_POST['eRel']   : 0;
    $pid  = (isset($_POST['ePid'])) ? $_POST['ePid']   : 0;
    $game = (isset($_POST['eGame'])) ? $_POST['eGame'] : 0;
    $gameID = (isset($_POST['eGameId'])) ? $_POST['eGameId'] : 0;
    $estado = (isset($_POST['eStatus'])) ? $_POST['eStatus'] : 'No';
    $item   = 'n'. $rel;
    $game  .= $pid;

    $puntaje = 0;
    $msg     = '';
    $retry   = ( $_SESSION[$game]['retry'] ) ? intval($_SESSION[$game]['retry']) : 0;

    // CheckStatusGame
    $firts = (isset($_SESSION[$game]['status']))  ?  $_SESSION[$game]['status']  :  'new';
    if ($firts === 'new'){
      // reset history
      $_SESSION[$game]['history']   = array(0);
      $_SESSION[$game]['puntos']    = 0;
      $_SESSION[$game]['retry']     = 0;
      $_SESSION[$game]['status']    = 'play';
    }
    //$hito_puntaje   = ( !in_array($pid, $_SESSION[$game]['history']) )  ?  'hito_puntaje_1'  :  'hito_puntaje_2';
    $hito_puntaje   = 'hito_puntaje_1';
    $juego_pregunta = ( $retry  === 0 )  ?  'juegoPreguntaPerfecto'  :  'juegoPreguntaNormal';
    $msg = genMensajeTry($retry, true);
    $puntaje = getPuntajeHito($juego_pregunta, $hito_puntaje, $db);

    //if ($estado === 'No' || $estado === 'Finish'){
    if ( $estado === 'Finish' ){
      $msg = genMensajeTry(5, false);
      $puntaje = 0;
    }
    $_SESSION[$game][$item] = isset($_SESSION[$game][$item]) ? $_SESSION[$game][$item] : -1;
    $debug = $_SESSION[$game][$item];
    if ( $_SESSION[$game][$item] === juegoStatusICO('win') ){
      $msg = genMensajeTry(5, false);
      $puntaje = 0;
    }
    $_SESSION[$game]['puntos'] += $puntaje;
    $_SESSION[$game][$item]     = juegoStatusICO('win');
    $_SESSION[$game]['retry']   = 0;

    // Reset history c/5
    //if ( count($_SESSION[$game]['history'])>4 ) {
      //$_SESSION[$game]['history'] = array();
    //}
    $current = isset($_SESSION[$game]['current']) ? $_SESSION[$game]['current'] : 1;
    $_SESSION[$game]['current'] = ($current > $rel) ? $current : $rel;

    print json(array( 'rpta' => $msg, 'pje' => $puntaje, 'info' => $debug ));
    break;

  case 'saveGamePista':
    $rel  = (isset($_POST['eRel'])) ? $_POST['eRel']   : 0;
    $pid  = (isset($_POST['ePid'])) ? $_POST['ePid']   : 0;
    $game = (isset($_POST['eGame'])) ? $_POST['eGame'] : 0;
    $gameID = (isset($_POST['eGameId'])) ? $_POST['eGameId'] : 0;
    $item   = 'n'. $rel;
    $game   .= $pid;

    $_SESSION[$game]['retry'] = 1;

    print json(array( 'rpta' => 'pista', 'pje' => 0 ));
    break;

  case 'saveGameFail':
    $rel  = (isset($_POST['eRel'])) ? $_POST['eRel']   : 0;
    $pid  = (isset($_POST['ePid'])) ? $_POST['ePid']   : 0;
    $game = (isset($_POST['eGame'])) ? $_POST['eGame'] : 0;
    $gameID = (isset($_POST['eGameId'])) ? $_POST['eGameId'] : 0;
    $item   = 'n'. $rel;
    $game  .= $pid;

    $_SESSION[$game]['retry'] = 2;
    array_push($_SESSION[$game]['history'], $gameID);

    print json(array( 'rpta' => 'feedback', 'pje' => 0 ));
    break;

  default:
    print json(array('rpta' => 'false' ));
}

?>

<?php

function setActive($value, $current){
  $rpta = ($value === $current) ? ' class="active"' : '';
  return $rpta;
}

function setCurrent($value, $current){
  $rpta = ($value === $current) ? ' class="current"' : '';
  return $rpta;
}

function crearMnuMain($cur, $json){
  $menu = '<ul class="property-nav text-center">
      <li'. setCurrent($cur, 'perfil'). '>
        <a href="perfil.php" data-tooltip aria-haspopup="true" class="tip-right" title="Usuario"><i class="fa fa-user"></i></a>
      </li>
      <li'. setCurrent($cur, 'dashboard'). '>
        <a href="dashboard.php" data-tooltip aria-haspopup="true" class="tip-right" title="Entrenamientos"><i class="fa fa-graduation-cap"></i></a>
      </li>
      <li'. setCurrent($cur, 'reporte'). '>
        <a href="reporte.php" data-tooltip aria-haspopup="true" class="tip-right" title="Reportes"><i class="fa fa-indent"></i></a>
      </li>
      <li>
        <a href="index.php?logout" data-tooltip aria-haspopup="true" class="tip-right" title="Cerrar Sesión"><i class="fa fa-sign-out"></i></a>
      </li>
    </ul>';

  return $menu;
}

function crearMnuMainMobile($cur, $json){
  $menu = '<ul class="MnuMobile">
      <li'. setCurrent($cur, 'perfil'). '>
        <a href="perfil.php"><i class="fa fa-user"></i> Perfil</a>
      </li>
      <li'. setCurrent($cur, 'dashboard'). '>
        <a href="dashboard.php"><i class="fa fa-graduation-cap"></i> Dashboard</a>
      </li>
      <li'. setCurrent($cur, 'reporte'). '>
        <a href="reporte.php"><i class="fa fa-indent"></i> Reporte</a>
      </li>
      <li>
        <a href="index.php?logout"><i class="fa fa-sign-out"></i> Salir</a>
      </li>
    </ul>';

  return $menu;
}

function crearMnuAdmin($cur, $json){
  $menu = '<ul class="property-nav text-center">
      <li'. setCurrent($cur, 'dashboard'). '>
        <a href="dashboard-admin.php" data-tooltip aria-haspopup="true" class="tip-right" title="Dashboard"><i class="fa fa-home"></i></a>
      </li>';
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .='<li'. setCurrent($cur, 'participante'). '>
        <a href="panel-participantes.php" data-tooltip aria-haspopup="true" class="tip-right" title="Participantes"><i class="fa fa-users"></i></a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_JEFE ):
      $menu .= '<li'. setCurrent($cur, 'roles'). '>
        <a href="panel-participantes-roles.php" data-tooltip aria-haspopup="true" class="tip-right" title="Roles"><i class="fa fa-shield"></i></a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'perfiles'). '>
        <a href="crear-perfiles.php" data-tooltip aria-haspopup="true" class="tip-right" title="Perfiles"><i class="fa fa-male" aria-hidden="true"></i></a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'distribuidora'). '>
        <a href="crear-distribuidora.php" data-tooltip aria-haspopup="true" class="tip-right" title="Distribuidora"><i class="fa fa-university"></i></a>
      </li>';
    endif;
      $menu .= '<li'. setCurrent($cur, 'equipo'). '>
        <a href="crear-equipo.php" data-tooltip aria-haspopup="true" class="tip-right" title="Equipo"><i class="fa fa-joomla" aria-hidden="true"></i></a>
      </li>';
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'entrenamiento'). '>
        <a href="crear-entrenamiento.php" data-tooltip aria-haspopup="true" class="tip-right" title="Entrenamientos"><i class="fa fa-graduation-cap"></i></a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'novedad'). '>
        <a href="crear-novedad.php" data-tooltip aria-haspopup="true" class="tip-right" title="Novedades"><i class="fa fa-gg-circle"></i></a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .='<li'. setCurrent($cur, 'curricula'). '>
        <a href="crear-curricula.php" data-tooltip aria-haspopup="true" class="tip-right" title="Currícula"><i class="fa fa-leanpub"></i></a>
      </li>';
    endif;
    $menu .= '<li'. setCurrent($cur, 'reporte'). '>
        <a href="resultados-generales.php" data-tooltip aria-haspopup="true" class="tip-right" title="Reportes"><i class="fa fa-indent"></i></a>
      </li>
      <li>
        <a href="index.php?logout" data-tooltip aria-haspopup="true" class="tip-right" title="Cerrar Sesión"><i class="fa fa-sign-out"></i></a>
      </li>
    </ul>';

  return $menu;
}

function crearMnuAdminMobile($cur, $json){
  $menu = '<ul class="MnuMobile">
      <li'. setCurrent($cur, 'dashboard'). '>
        <a href="dashboard-admin.php"><i class="fa fa-home"></i> Dashboard</a>
      </li>';
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .='<li'. setCurrent($cur, 'participante'). '>
        <a href="panel-participantes.php"><i class="fa fa-users"></i> Participantes</a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN || $_SESSION['rol'] == ROL_JEFE ):
      $menu .= '<li'. setCurrent($cur, 'roles'). '>
        <a href="panel-participantes-roles.php"><i class="fa fa-shield"></i> Roles</a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'perfiles'). '>
        <a href="crear-perfiles.php"><i class="fa fa-male" aria-hidden="true"></i> Perfiles</a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'distribuidora'). '>
        <a href="crear-distribuidora.php"><i class="fa fa-university"></i> Distribuidora</a>
      </li>';
    endif;
      $menu .= '<li'. setCurrent($cur, 'equipo'). '>
        <a href="crear-equipo.php"><i class="fa fa-joomla" aria-hidden="true"></i> Equipo</a>
      </li>';
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'entrenamiento'). '>
        <a href="crear-entrenamiento.php"><i class="fa fa-graduation-cap"></i> Entrenamientos</a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .= '<li'. setCurrent($cur, 'novedad'). '>
        <a href="crear-novedad.php"><i class="fa fa-gg-circle"></i> Novedades</a>
      </li>';
    endif;
    if ( $_SESSION['rol'] == ROL_ADMIN ):
      $menu .='<li'. setCurrent($cur, 'curricula'). '>
        <a href="crear-curricula.php"><i class="fa fa-leanpub"></i> Currícula</a>
      </li>';
    endif;
    $menu .= '<li'. setCurrent($cur, 'reporte'). '>
        <a href="resultados-generales.php"><i class="fa fa-indent"></i> Reportes</a>
      </li>
      <li>
        <a href="index.php?logout"><i class="fa fa-sign-out"></i> Salir</a>
      </li>
    </ul>';

  return $menu;
}

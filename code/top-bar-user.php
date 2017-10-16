<div class="hide-for-medium-up right" style="margin-top: -2.8125rem; margin-right: 1rem;">
    <a class="left-off-canvas-toggle">
        <i class="fa fa-bars" aria-hidden="true"></i> <span>Menú</span>
    </a>
</div>
<ul class="right bgAzukMain">
  <form action="busqueda.php" method="POST">
    <?php if ( $_SESSION['rol'] == ROL_SUPERVISOR ||  $_SESSION['rol'] == ROL_JEFE ): ?>
      <li><a href="dashboard-admin.php"><i class="fa fa-cog" aria-hidden="true"></i> Supervisar </a></li>
    <?php endif; ?>
    <li>
      <input type="text" name="txtBusqueda" id="txtBusqueda" placeholder="Entrenamientos / Temas" class="w240 border-radius" maxlength="32" required oninvalid="this.setCustomValidity('Por favor, llene este campo')" />
    </li>
    <li>
      <button type="submit" class="radius"><i class="fa fa-search"></i></button></a>
    </li>
    <li>
      <a id="lblPuntaje" style="cursor:default; background: #003c73;">Puntaje <span><?php print($_SESSION['puntos']); ?></span></a>
    </li>
    <li><a href="#" style="cursor:default; background: #003c73;"><div class="perfil-nombre"><img class="img-circle" src="<?php print($_SESSION['picture']); ?>" width="80%" alt="User" /></div><span><?php print($_SESSION['username']); ?></span></a></li>
  </form>
</ul>

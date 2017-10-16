<div class="hide-for-medium-up right" style="margin-top: -2.8125rem; margin-right: 1rem;">
    <a class="left-off-canvas-toggle">
        <i class="fa fa-bars" aria-hidden="true"></i> <span>Menú</span>
    </a>
</div>
<ul class="right bgAzukMain">
    <?php if ( $_SESSION['rol'] == ROL_SUPERVISOR || $_SESSION['rol'] == ROL_JEFE ): ?>
        <li><a href="dashboard.php"><i class="fa fa-leanpub" aria-hidden="true"></i> Entrenar</a></li>
    <?php endif; ?>
    <li><a href="#"><div class="perfil-nombre"><img class="img-circle" src="<?php print($_SESSION['picture']); ?>" width="80%" alt="User" /></div><span><?php print($_SESSION['username']); ?></span></a></li>
</ul>

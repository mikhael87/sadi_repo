  <div class="error">
      ¡ESTE MODULO DE CONFIGURACION ES EN EXTREMO DELICADO!<br/>
      cualquier cambio sin conocimiento podria podria hacer que 
      deje de funcionar la aplicacion de correspondencia en todo el instituto.
  </div>

<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="notice"><?php echo __($sf_user->getFlash('notice'), array(), 'sf_admin') ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo __($sf_user->getFlash('error'), array(), 'sf_admin') ?></div>
<?php endif; ?>

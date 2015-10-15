<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="notice"><?php echo html_entity_decode(__($sf_user->getFlash('notice'), array(), 'sf_admin')) ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo html_entity_decode(__($sf_user->getFlash('error'), array(), 'sf_admin')) ?></div>
<?php endif; ?>

<?php if ($sf_user->getAttribute($error_namen)): ?>
  <ul class="error_list"><li><?php echo $sf_user->getAttribute($error_namen); ?></li></ul>
<?php
    $sf_user->getAttributeHolder()->remove($error_namen);
    endif;
?>

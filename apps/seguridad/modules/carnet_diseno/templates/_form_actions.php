<ul class="sf_admin_actions">
<?php if ($form->isNew()): ?>
    <?php echo $helper->linkToList(array(  'params' =>   array(  ),  'class_suffix' => 'list',  'label' => 'Back to list',)) ?>
    <input id="button_guardar" disabled="disabled" type="submit" value="Guardar">
<?php endif; ?>
</ul>
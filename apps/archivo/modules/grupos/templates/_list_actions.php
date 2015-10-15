<?php if($sf_user->getAttribute('pae_funcionario_unidad_id')) : ?>
<li class="sf_admin_action_regresar_modulo">
    <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>configuracion/index?opcion=grupoArchivo">Regresar</a>
</li>
<?php endif; ?>
<?php echo $helper->linkToNew(array(  'params' =>   array(  ),  'class_suffix' => 'new',  'label' => 'New',)) ?>
<li class="sf_admin_action_historico">
  <?php echo link_to(__('Histórico', array(), 'messages'), 'grupos/historico', array()) ?>
</li>

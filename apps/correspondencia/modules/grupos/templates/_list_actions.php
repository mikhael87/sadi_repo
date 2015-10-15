<?php use_helper('jQuery') ?>
<?php if($sf_user->getAttribute('pae_funcionario_unidad_id')) : ?>
<li class="sf_admin_action_regresar_modulo">
    <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>configuracion/index?opcion=grupoCorrespondencia">Regresar</a>
</li>
<?php endif; ?>
<?php echo $helper->linkToNew(array(  'label' => 'Nuevo permiso',  'params' =>   array(  ),  'class_suffix' => 'new',)) ?>
<li class="sf_admin_action_pordefecto">
  <?php
    echo link_to(__('Asignación automática', array(), 'messages'), 'grupos/permisosPorDefecto', array('confirm' => 'Se borran los permisos creados de todas las unidades listadas. Continuar?', 'class' => 'tooltip', 'title' => 'Asigna permisos a todos los miembros de la unidad'))
  ?>
</li>
<!--<li class="sf_admin_action_pordefecto">
    <a href="#" onclick="open_window_right(); sondeo_vistobueno(); return false;">Detectar Visto buenos</a>
</li>-->
<li class="sf_admin_action_historico">
    <?php echo link_to(__('Histórico', array(), 'messages'), 'grupos/historico') ?>
</li>
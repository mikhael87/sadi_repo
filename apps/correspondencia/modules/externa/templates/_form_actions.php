<script>      
    function verificar_emisor(){
        var emisor_nuevo = $("#autocomplete_correspondencia_correspondencia_emisor_organismo_id").val();
        var emisor_viejo = $("#correspondencia_correspondencia_emisor_organismo_id").val();

        if(emisor_viejo == '' && emisor_nuevo != '')
        { 
            var agree=confirm('Esta seguro de que esta bien escrito el nombre del Organismo que ingresa la correspondencia.');
            if (agree) return true ;
            else return false;
        }
        
        return true;
    };
</script>  

<ul class="sf_admin_actions">
<?php if ($form->isNew()): ?>
  <?php // echo $helper->linkToList(array(  'params' =>   array(  ),  'class_suffix' => 'list',  'label' => 'Back to list',)) ?>
    <li class="sf_admin_action_cancelar"><a href="<?php echo sfConfig::get('sf_app_correspondencia_url') ?>externa">Cancelar</a></li>
    <input type='submit' value='Guardar y regresar' onclick='return verificar_emisor();'/>
  <?php //echo $helper->linkToSave($form->getObject(), array('label' => 'Guardar y regresar', 'class_suffix' => 'save',)) ?>
<?php else: ?>
  <?php echo $helper->linkToDelete($form->getObject(), array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
  <?php echo $helper->linkToList(array(  'params' =>   array(  ),  'class_suffix' => 'list',  'label' => 'Back to list',)) ?>
  <input type='submit' value='Guardar y regresar' onclick='return verificar_emisor();'/>
  <?php //echo $helper->linkToSave($form->getObject(), array(  'params' =>   array(  ),  'class_suffix' => 'save',  'label' => 'Save',)) ?>
  <?php echo $helper->linkToSaveAndAdd($form->getObject(), array(  'params' =>   array(  ),  'class_suffix' => 'save_and_add',  'label' => 'Save and add',)) ?>
<?php endif; ?>
</ul>

<?php use_helper('jQuery'); ?>


<div id="sf_admin_container">
  <h1>Directorio Telefónico</h1>

<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
<?php endif; ?>

  <div id="sf_admin_bar">
    <?php //include_partial('mensajes/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id trans">
    <div>
        <label for="directorio_unidad_id">Unidad</label>
        <div class="content">
            <select name="directorio_unidad_id" id="directorio_unidad_id" onchange="
                <?php
                        echo jq_remote_function(array('update' => 'funcionarios_list',
                        'url' => 'directorio_interno/funcionarios',
                        'with'     => "'u_id=' +this.value",)) ?>">
                    <option value=""></option>

                <?php foreach( $unidades as $clave=>$valor ) { 
                    if($clave != '') { ?>
                    <option value="<?php echo $clave; ?>">
                        <?php echo html_entity_decode($valor); ?>
                    </option>
                <?php }} ?>
            </select>

        </div>
        <div class="help">Seleccione la unidad que desea ver.</div>
    </div>
</div>
      <br/><br/>
      <div id="funcionarios_list"></div>
  </div>

  <div id="sf_admin_footer">
  </div>
</div>
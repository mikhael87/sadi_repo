<?php
if(count($sf_user->getAttribute('expediente.filters', array(), 'admin_module')) != 0) {
    $ext= '_active';
    $status= 'Activo';
} else {
    $ext= '';
    $status= 'Inactivo';
}
?>
<li>
<?php echo image_tag('icon/find24'.$ext, array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right; vertical-align: middle', 'title' => 'Filtrar Expedientes: <b>'.$status.'</b>', 'class' => 'tooltip')); ?>&nbsp;
<form style="display: inline;" id="quickfilter" method="post" action="<?php echo url_for('expediente/multipleBandeja') ?>">
          <select id="clon" name="bandeja_archivo">
              <option value="0" <?php echo (($sf_context->getModuleName()== 'expediente') ? 'selected' : ''); ?>>Mis expedientes</option>
              <option value="1" <?php echo (($sf_context->getModuleName()== 'prestamos_solicitados') ? 'selected' : ''); ?>>Expedientes que solicito</option>
              <option value="2" <?php echo (($sf_context->getModuleName()== 'expedientes_compartidos') ? 'selected' : ''); ?>>Expedientes compartidos</option>
          </select>
</form>
</li>
<?php
$cor_externa_cargo= '';
if($sf_user->hasAttribute('emisor_externo')) {
    $datos_emisor= $sf_user->getAttribute('emisor_externo');
    $parts_ext= explode('#', $datos_emisor);
    $cor_externa_cargo= $parts_ext[2];
}
?>
<div style="position: absolute; left: 140px; top: -30px; width: 100%;" id="flashes_cargo">
<?php if ($sf_user->hasFlash('notice_cargo')): ?>
  <div class="notice"><?php echo $sf_user->getFlash('notice_cargo'); ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error_cargo')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error_cargo'); ?></div>
<?php endif; ?>
</div>

<script>
  $(document).ready(function () {
      $('#flashes_cargo').fadeOut(8000);
  });
</script>

<div style="position: absolute; top: -20px;">
    <div style="position: absolute; left: 0px; cursor: pointer;" id="div_cargo_button_new" onclick="javascript:toggleCargo();"><?php echo image_tag('icon/new.png'); ?></div>
    <div style="position: absolute; left: 20px; display: none;" id="div_cargo_nombre"><input type="text" id="cargo_nombre" size="25"/></div>
    <div style="position: absolute; top: 3px; left: 237px; cursor: pointer; display: none;" id="div_cargo_button_save" onclick="javascript:saveCargo();"><?php echo image_tag('icon/filesave.png'); ?></div>
</div>

<select id="select_cargos" name="<?php echo $cargo_name; ?>" onChange="javascript: agregarOtro(this.value)">
    <option value=""></option>
    <?php 
        foreach ($cargos as $cargo) { 
            if(trim($cargo->getNombre())!='') {
    ?>
    <option <?php echo (($cor_externa_cargo == $cargo->getId())? 'selected' : '') ?> value="<?php echo $cargo->getId(); ?>" <?php if(isset($id_save)) { if($id_save == $cargo->getId()) { echo "selected"; } } ?>><?php echo $cargo->getNombre(); ?></option>
    <?php }} ?>
</select>
<?php use_helper('jQuery'); ?>
<?php
$cor_externa_persona= 0;
if($sf_user->hasAttribute('emisor_externo')) {
    $datos_emisor= $sf_user->getAttribute('emisor_externo');
    $parts_ext= explode('#', $datos_emisor);
    $cor_externa_persona= $parts_ext[1];
}
?>
<div style="position: absolute; left: 140px; top: -30px; width: 100%;" id="flashes_persona">
<?php if ($sf_user->hasFlash('notice_persona')): ?>
  <div class="notice"><?php echo $sf_user->getFlash('notice_persona'); ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error_persona')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error_persona'); ?></div>
<?php endif; ?>
</div>

<script>
    $(document).ready(function () {
        $('#flashes_persona').fadeOut(8000);
    });

    <?php
    if(isset($id_save)){
        echo jq_remote_function(array('update' => 'lista_cargos',
        'url' => 'organismo/listarCargos',
        'with'     => "'p_id=".$id_save."&cargo_name=".$cargo_name."'",));
    } else {
        echo jq_remote_function(array('update' => 'lista_cargos',
        'url' => 'organismo/listarCargos',
        'with'     => "'p_id=".$cor_externa_persona."&cargo_name=".$cargo_name."'",));
    }
    ?>
</script>

<div style="position: absolute; top: -20px;">
    <div style="position: absolute; left: 0px; cursor: pointer;" id="div_persona_button_new" onclick="togglePersona();"><?php echo image_tag('icon/new.png'); ?></div>
    <div style="position: absolute; left: 20px; display: none;" id="div_persona_cedula"><input type="text" id="persona_cedula" size="7"/></div>
    <?php
        $sf_seguridad = sfYaml::load(sfConfig::get('sf_root_dir') . "/config/siglas/seguridad.yml");
        if($sf_seguridad['conexion_saime']['activo']==true){
    ?>
    <div style="position: absolute; left: 100px; display: none; cursor: pointer;" id="div_persona_button_validate" title="Verificar cedula" onclick="verificarCedula(); return false;"><?php echo image_tag('icon/2execute.png'); ?></div>
    <div style="position: absolute; top: -5px; left: 125px; display: none; width: 200px; z-index: 100;" id="div_espera_verificar_cedula"></div>
    <?php } else { ?>
    <div style="position: absolute; left: 100px; display: none;" id="div_persona_button_validate"><?php echo image_tag('icon/2execute_gray.png'); ?></div>
    <?php } ?>
    <div style="position: absolute; left: 125px; display: none; z-index: 99;" id="div_persona_nombre"><input type="text" id="persona_nombre" size="25"/></div>
    <div style="position: absolute; top: 3px; left: 345px; cursor: pointer; display: none;" id="div_persona_button_save" onclick="verificarPersona(); return false;"><?php echo image_tag('icon/filesave.png'); ?></div>
</div>

<select id="select_persona" name="<?php echo $persona_name; ?>" onchange="listarCargos();">
    <option value="0"></option>
    <?php
        foreach ($personas as $persona_id => $persona_nombre) {
            if(trim($persona_nombre)!='') {
    ?>
    <option <?php echo (($cor_externa_persona == $persona_id)? 'selected' : '') ?> value="<?php echo $persona_id; ?>" <?php if(isset($id_save)) { if($id_save == $persona_id) { echo "selected"; } } ?>><?php echo $persona_nombre; ?></option>
    <?php }} ?>
</select>
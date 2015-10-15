<?php use_helper('jQuery'); ?>
<?php $organismos = Doctrine::getTable('Organismos_Organismo')->todosNoHidratados(); ?>

<div style="position: absolute; left: 20px; top: -30px; width: 100%;" id="flashes_organismo">
<?php if ($sf_user->hasFlash('notice_organismo')): ?>
  <div class="notice"><?php echo $sf_user->getFlash('notice_organismo'); ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error_organismo')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error_organismo'); ?></div>
<?php endif; ?>
</div>

<script>
    $(document).ready(function () {
      $('#flashes_organismo').fadeOut(8000);
    });

    <?php
    echo jq_remote_function(array('update' => 'lista_personas',
    'url' => 'organismo/listarPersonas',
    'with'     => "'o_id=".$id_save."&persona_name=".$persona_name."&cargo_name=".$cargo_name."'",));
    ?>
</script>

<select id="select_organismo" name="<?php echo $organismo_name; ?>" onchange="listarPersonas();">
    <option value="0"></option>
    <?php foreach ($organismos as $organismo_id => $organismo_nombre) { ?>
    <option value="<?php echo $organismo_id; ?>" <?php if($id_save == $organismo_id) echo "selected"; ?>><?php echo $organismo_nombre; ?></option>
    <?php } ?>
</select>

<?php foreach ($formatos_legitimos as $formato_id => $formato_nombre) { ?>
    <input type="checkbox" name="formato[<?php echo $formato_id; ?>]" 
           <?php if(isset($formatos_asignados[$formato_id])) echo "checked"?>><?php echo $formato_nombre; ?><br/>
<?php } ?>

<script>
    $(".sf_admin_action_save").show(); 
    $(".sf_admin_action_save_and_add").show();
</script>
                

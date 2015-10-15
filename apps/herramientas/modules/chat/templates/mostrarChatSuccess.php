<script>
    <?php foreach($mensajes_sesion as $key => $mensaje) { 
        $funcionario_n = Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($key);
        foreach($funcionario_n as $f_n)
        {?>
            nombre = <?php echo '"'.$f_n->getPrimerNombre().' '.$f_n->getPrimerApellido().'"'; }?>;
        if (!$('#win_<?php echo $key; ?>').length){
            open_chat(<?php echo $key; ?>);
        } else {
            open_chat(<?php echo $key; ?>,'<?php echo html_entity_decode($mensaje); ?>');
        }
    <?php } ?>
</script>
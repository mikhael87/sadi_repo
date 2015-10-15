<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_docuementos">

<?php if ($sf_user->hasFlash('sin_formatos')): ?>
  <div class="error"><?php echo __($sf_user->getFlash('sin_formatos'), array(), 'sf_admin') ?></div>
<?php endif; ?>

    <div>
        <label for="documentos">Tipos de Documento</label>
        <div class="content" id="div_correlativo_formatos">
            
            <div class="notice">Seleccione la unidad a la que le asignara el correlativo.</div>
                
        </div>
    </div>

    <div class="help">Seleccione los tipos de documentos asociados al correlativo.</div>

</div>

<?php 
$funcionario_unidades = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($sf_user->getAttribute('funcionario_id'));

if(count($funcionario_unidades) == 1) { 
?>

    <script>
        <?php
        echo jq_remote_function(array('update' => 'div_correlativo_formatos',
        'url' => 'correlativos/formatos',
        'with'=> "'unidad_id='+$('#correspondencia_unidad_correlativo_unidad_id').val()",));
        ?>
    </script>
<?php } ?>
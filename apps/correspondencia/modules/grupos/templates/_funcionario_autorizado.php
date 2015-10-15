<?php if($form['dependencia_unidad_id']->getValue()) { ?>
    <script>
        <?php
        echo jq_remote_function(array('update' => 'funidad',
        'url' => 'grupos/funcionarioAutorizadoCorrespondencia',
        'with'     => "'u_id=".$form['dependencia_unidad_id']->getValue()."&f_id=".$form['funcionario_id']->getValue()."'",))
        ?>
    </script>
<?php } ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_funcionario_id">
    <div>
        <label for="correspondencia_funcionario_unidad_funcionario_id">Funcionario</label>

        <div class="content">

            <div id="funidad">
                <select name="correspondencia_funcionario_unidad[funcionario_id]" id="correspondencia_funcionario_unidad_funcionario_id">
                    <option value=""></option>
                </select>
            </div>

        </div>

        <div class="help">Seleccione el funcionario que autorizara a recibir y redactar las correspondencias de la unidad.</div>
    </div>
</div>
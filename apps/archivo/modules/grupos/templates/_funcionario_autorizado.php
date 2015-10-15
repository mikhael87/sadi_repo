<?php if($form['dependencia_unidad_id']->getValue()) { ?>
    <script>
        <?php
        echo jq_remote_function(array('update' => 'funidad',
        'url' => 'grupos/funcionarioAutorizadoArchivo',
        'with'     => "'u_id=".$form['dependencia_unidad_id']->getValue()."&f_id=".$form['funcionario_id']->getValue()."'",))
        ?>
    </script>
<?php } ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_funcionario_id">
    <div>
        <label for="archivo_funcionario_unidad_funcionario_id">Funcionario</label>

        <div class="content">

            <div id="funidad">
                <select name="archivo_funcionario_unidad[funcionario_id]" id="archivo_funcionario_unidad_funcionario_id">
                    <option value=""></option>
                </select>
            </div>

        </div>

        <div class="help">Seleccione el funcionario que autorizara a manipular los archivos de la unidad.</div>
    </div>
</div>
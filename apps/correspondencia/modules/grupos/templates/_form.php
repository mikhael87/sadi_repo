<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_helper('jQuery'); ?>
<?php include_partial('grupos/assets') ?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(TRUE); ?>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@correspondencia_funcionario_unidad', array('id'=> 'form_grupo')) ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('grupos/form_fieldset', array('correspondencia_funcionario_unidad' => $correspondencia_funcionario_unidad, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <div id="vistos_bueno_ruta" style="display: none">
        <fieldset id="sf_fieldset_visto_bueno">
            <h2>Visto Bueno</h2>
            <div class="sf_admin_form_row sf_admin_boolean sf_admin_form_field_unidad" style="height: 20px">
                <div style="float: left; width: 215px">
                    <label for="correspondencia_funcionario_unidad_firmar_ninguno">Ning&uacute;no</label>
                    <div class="content">
                        <?php
                        $alguno= '';
                        $ninguno= '';
                        if (!$form->isNew()) {
                            echo '<input type="hidden" name="correspondencia_funcionario_unidad[dependencia_unidad_id]" value="' . $correspondencia_funcionario_unidad->getDependenciaUnidadId() . '">';
                            echo '<input type="hidden" name="correspondencia_funcionario_unidad[funcionario_id]" value="' . $correspondencia_funcionario_unidad->getFuncionarioId() . '">';
                            echo '<input type="hidden" name="correspondencia_funcionario_unidad[autorizada_unidad_id]" value="' . $correspondencia_funcionario_unidad->getAutorizadaUnidadId() . '">';
                            $vb_config= Doctrine::getTable('Correspondencia_VistobuenoConfig')->vistobuenoConfigUnidad($correspondencia_funcionario_unidad->getId());
                            if(count($vb_config) > 0)
                                $alguno= 'checked';
                            else
                                $ninguno= 'checked';
                        } ?>
                        <input id="correspondencia_funcionario_unidad_firmar_ninguno" type="radio" name="vistobueno" value="N" <?php echo $ninguno; ?> onChange="javascript: conmutar_opcion()">
                    </div>
                </div>
                <div style="float: left">
                    <label for="correspondencia_funcionario_unidad_firmar_alguno">Alguno</label>
                    <div class="content">
                        <input id="correspondencia_funcionario_unidad_firmar_alguno" type="radio" name="vistobueno" value="A" <?php echo $alguno; ?> onChange="javascript: conmutar_opcion()">
                    </div>
                </div>
            </div>
            <div id="ninguno_div" style="display: block">
                <div style="color: #f33; padding-left: 1em">Los documentos <b>generados en esta unidad</b> que firmar&aacute; este funcionario, no pasar&aacute;n por ning&uacute;n <b>Visto bueno</b> previo a la firma.</div>
            </div>
            <div id="alguno_div" style="display: none">
                <div style="color: #f33; padding-left: 1em">Le permite definir quienes deben dar el <b>Visto bueno</b> a documentos que tengan como firmante a este funcionario.</div>
                <br/>
                <div id="div_table_unidad_funcionario" style="padding-left: 1em">
                    <table id="table_vistobuenos">
                        <a class='partial_new_view partial' href="#" onclick="javascript: show_select_funcionario(); return false;">Agregar otro</a>
                        <?php if (!$form->isNew()) : ?>
                                <thead><tr><th>Orden</th><th>Funcionario</th><th>Funci&oacute;n</th><th>Acci&oacute;n</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class='index' style='font-weight: bolder; font-size: 20px; text-align: center; vertical-align: middle'><?php echo count($vb_config)+ 1; ?></td>
                                        <td style='color: #666;'><?php echo $nombre[0]['unombre'].'<br/>'.$nombre[0]['ctnombre'].'/ '.$nombre[0]['fnombre'].' '.$nombre[0]['fapellido'] ?></td>
                                        <td style='color: #666; text-align: center; vertical-align: middle'>Firma final</td>
                                    </tr>
                                    <?php
                                    $counter= 2;
                                    foreach($vb_config as $value) {
                                        $datos_fun= Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($value->getFuncionarioId());

                                        $cadena = "<tr>";
                                        $cadena .= "<td class='index' style='font-weight: bolder; font-size: 20px; text-align: center; vertical-align: middle'>" . $value->getOrden() . "</td>";
                                        if(!count($datos_fun)> 0 || $value->getStatus()== 'D') {
                                            $sin_cargo= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($value->getFuncionarioId());
                                            $cadena .= "<td><font style='color: #666'>" . $sin_cargo[0]['primer_nombre'] . " " . $sin_cargo[0]['primer_apellido']  . "</font></td>";
                                        }else {
                                            $cadena .= "<td>" . $datos_fun[0]['unombre'] . "<br/>" . $datos_fun[0]['ctnombre']  . "/ " . $datos_fun[0]['fnombre'] . " " . $datos_fun[0]['fapellido'] . "</td>";
                                        }
                                        $cadena .= "<td style='text-align: center; vertical-align: middle'>" . (($value->getStatus()== 'A')? 'Visto bueno': '<font style="color: #666">Desincorporado(a) del Cargo</font>') . "</td>";
                                        $cadena .= "<td style='text-align: center; vertical-align: middle'><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                        $cadena .= "<input type='hidden' name='funcionarios_vb[]' value='" . $value->getFuncionarioId() . "#" . $value->getFuncionarioCargoId() . "#" . $value->getStatus() . "'/>";
                                        $cadena .= "</tr>";

                                        $counter++;
                                        echo $cadena;
                                    } ?>
                                </tbody>
                        <?php endif; ?>
                    </table>
                    <div style="padding-left: 1em; color: #aaa"><font style="color: #ff5a5a">*</font>&nbsp;El documento deber&aacute; ser verificado por los funcionarios de la lista en orden ascendente</div>

                    <div id="div_unidad_funcionario" style="padding-left: 1em; display: none">
                        <div id="div_vistobueno_unidad">
                            <br />Unidad:<br />
                            <select name="vistobueno_unidad" id="vistobueno_unidad" onchange="
                                <?php
                                echo jq_remote_function(array('update' => 'div_vistobueno_funcionario',
                                                        'url' => 'grupos/vistobuenoUnidades',
                                                        'with' => "'unidad_id=' +this.value",))
                                ?>"> <?php
                                foreach ($unidades as $clave => $valor) {
                                    $list_id = explode("&&", $clave); ?>
                                    <option value="<?php echo $list_id[0]; ?>">
                                        <?php echo $valor; ?>
                                    </option>
                                    <?php } ?>
                            </select>
                        </div>

                        <div id="div_vistobueno_funcionario">
                            <br />Funcionario:<br />
                            <select name="vistobueno_funcionario" id="vistobueno_funcionario">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="val_input" id="val_input" value="">
            <br/>
        </fieldset>
    </div>
    <?php include_partial('grupos/form_actions', array('correspondencia_funcionario_unidad' => $correspondencia_funcionario_unidad, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
    </form>
</div>

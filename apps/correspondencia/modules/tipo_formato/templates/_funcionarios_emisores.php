<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $emisores= $parametros_all['emisores']['funcionarios'];
}else
    $emisores= Array('firma_uno'=> array('nombre_firma'=> 'Firmante', 'tipo_cargos'=> array(0=> 'autorizados')));
?>
<script>
    $(document).ready(function(){
        funcionarios_emisores_especificos_eliminar();

        $("form").submit(function() {
            if($("#radio_funcionarios_emisores_especificos_1").is(':checked')) { 
                funcionarios_emisores_especificos_agregar(1);
                
                if (parseInt($("#funcionarios_emisores_especificos_1_count").val()) > 0) {
                    $("#div_funcionarios_emisores_1_seteados").remove();
//                    return true;
                } else  {
                    return false;
                }
            } else {
                $("#div_funcionarios_emisores_1_especificos").remove();
            }
            
            if($("#cantidad_funcionarios_emisores").val() > 1) { 
                if($("#radio_funcionarios_emisores_especificos_2").is(':checked')) { 
                    funcionarios_emisores_especificos_agregar(2);

                    if (parseInt($("#funcionarios_emisores_especificos_2_count").val()) > 0) {
                        $("#div_funcionarios_emisores_2_seteados").remove();
//                        return true;
                    } else  {
                        return false;
                    }
                } else {
                    $("#div_funcionarios_emisores_2_especificos").remove();
                }
            } else {
                $("#div_funcionarios_emisores_firma_2").remove();
            }
            
            
            if($("#cantidad_funcionarios_emisores").val() > 2) { 
                if($("#radio_funcionarios_emisores_especificos_3").is(':checked')) { 
                    funcionarios_emisores_especificos_agregar(3);

                    if (parseInt($("#funcionarios_emisores_especificos_3_count").val()) > 0) {
                        $("#div_funcionarios_emisores_3_seteados").remove();
//                        return true;
                    } else  {
                        return false;
                    }
                } else {
                    $("#div_funcionarios_emisores_3_especificos").remove();
                }
            } else {
                $("#div_funcionarios_emisores_firma_3").remove();
            }
        });
    });
    
    function funcionarios_emisores_seteado(seteo,firma){
        $("#funcionarios_emisores_seteado_"+firma+"_hidden").val(seteo);
        
        $("#div_funcionarios_emisores_"+firma+"_seteados").show();
        $("#div_funcionarios_emisores_"+firma+"_especificos").hide();
    }
    
    function funcionarios_emisores_especificos(firma){
        $("#div_funcionarios_emisores_"+firma+"_especificos").show();
        $("#div_funcionarios_emisores_"+firma+"_seteados").hide();
    }
    
    function funcionarios_emisores_especificos_agregar(firma){
        if($("#funcionarios_emisores_especificos_"+firma+"_id").val())
        {
            var firmas_array = new Array();
            firmas_array[1] = 'firma_uno';
            firmas_array[2] = 'firma_dos';
            firmas_array[3] = 'firma_tres';
            
            cadena = "<tr>";
            cadena += "<td>";
            cadena += "<font class='f16n'>" + $("#funcionarios_emisores_especificos_"+firma+"_id option:selected").text() + "</font>";
            cadena += "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios]["+firmas_array[firma]+"][tipo_cargos][]' type='hidden' value='" + $("#funcionarios_emisores_especificos_"+firma+"_id").val() + "'/>" + "</td>";
            cadena += "<td><a class='funcionarios_emisores_especificos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            cadena += "</tr>";
            $("#grilla_funcionarios_emisores_especificos_"+firma+" tbody").append(cadena);
            funcionarios_emisores_especificos_eliminar();
            $("#funcionarios_emisores_especificos_"+firma+"_count").val(parseInt($("#funcionarios_emisores_especificos_"+firma+"_count").val())+1);
            $("#funcionarios_emisores_especificos_"+firma+"_id option[value='']").attr('selected', 'selected');
        }
        else
        { alert('Debe seleccionar el cargo especifico que firma para poder agregar otro'); }
    };    
    
    function funcionarios_emisores_especificos_eliminar(){
        $("a.funcionarios_emisores_especificos_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    function cantidad_firmas(){
        if($("#cantidad_funcionarios_emisores").val()>1)
            $("#div_funcionarios_emisores_firma_2").show();
        else 
            $("#div_funcionarios_emisores_firma_2").hide();
        
        if($("#cantidad_funcionarios_emisores").val()>2)
            $("#div_funcionarios_emisores_firma_3").show();
        else
            $("#div_funcionarios_emisores_firma_3").hide();
    }
</script>


<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_parametros">
    <div>
        <label>Funcionarios emisores</label>
        <div class="content">
            Cantidad de Firmas
            <select id="cantidad_funcionarios_emisores" onchange="javascript: cantidad_firmas();">
                <option value="1" <?php echo ((count($emisores)== 1) ? 'selected' : ''); ?>>1</option>
                <option value="2" <?php echo ((count($emisores)== 2) ? 'selected' : ''); ?>>2</option>
                <option value="3" <?php echo ((count($emisores)== 3) ? 'selected' : ''); ?>>3</option>
            </select><br/>
            
            <div id="div_funcionarios_emisores_firma_1">
                <br/>Firma Uno:<br/>
                <input type="text" name="correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_uno][nombre_firma]" value="<?php echo $emisores['firma_uno']['nombre_firma'] ?>"/><br/>
                <input type="radio" name="tipo_funcionarios_emisores_1" onclick="javascript:funcionarios_emisores_seteado('solicitante',1)" id="radio_funcionarios_emisores_solicitante" value="solicitante" <?php echo ((count($emisores['firma_uno']['tipo_cargos'])== 1 && $emisores['firma_uno']['tipo_cargos'][0]== 'solicitante') ? 'checked' : '') ?>/> Quien redacta o solicita<br/>
                <input type="radio" name="tipo_funcionarios_emisores_1" onclick="javascript:funcionarios_emisores_seteado('supervisor',1)" id="radio_funcionarios_emisores_supervisor" value="supervisor" <?php echo ((count($emisores['firma_uno']['tipo_cargos'])== 1 && $emisores['firma_uno']['tipo_cargos'][0]== 'supervisor') ? 'checked' : '') ?>/> Supervisor inmediato<br/>
                <input type="radio" name="tipo_funcionarios_emisores_1" onclick="javascript:funcionarios_emisores_seteado('todos',1)" id="radio_funcionarios_emisores_todos" value="todos" <?php echo ((count($emisores['firma_uno']['tipo_cargos'])== 1 && $emisores['firma_uno']['tipo_cargos'][0]== 'todos') ? 'checked' : '') ?>/> Cualquier funcionario de la unidad<br/>
                <input type="radio" name="tipo_funcionarios_emisores_1" onclick="javascript:funcionarios_emisores_seteado('autorizados',1)" id="radio_funcionarios_emisores_autorizados" value="autorizados" <?php echo ((count($emisores['firma_uno']['tipo_cargos'])== 1 && $emisores['firma_uno']['tipo_cargos'][0]== 'autorizados') ? 'checked' : '') ?>/> Autorizados a firmar en la unidad<br/>
                <input type="radio" name="tipo_funcionarios_emisores_1" onclick="javascript:funcionarios_emisores_especificos(1)" id="radio_funcionarios_emisores_especificos_1" value="especificos" <?php echo ((count($emisores['firma_uno']['tipo_cargos']) > 1) ? 'checked' : '') ?>/> Cargos especificos

                <div id="div_funcionarios_emisores_1_seteados">
                    <input type="hidden" value="solicitante" id="funcionarios_emisores_seteado_1_hidden" name="correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_uno][tipo_cargos][]"/>
                </div>

                <div id="div_funcionarios_emisores_1_especificos" style="display: <?php echo ((count($emisores['firma_uno']['tipo_cargos']) > 1) ? 'block' : 'none') ?>;">
                    <?php
                        $tipo_cargos_emisores = Doctrine::getTable('Organigrama_CargoTipo')
                                                ->createQuery('a')
                                                ->orderBy('nombre')->execute();
                    ?>
                    <select name="funcionarios_emisores_especificos_1_id" id="funcionarios_emisores_especificos_1_id">
                            <option value=""><- seleccione -></option>
                        <?php foreach( $tipo_cargos_emisores as $tipo_cargo_emisor ) { ?>
                            <option value="<?php echo $tipo_cargo_emisor->getId(); ?>"><?php echo $tipo_cargo_emisor->getNombre(); ?></option>
                        <?php } ?>
                    </select>
                    <div class='partial_new_view partial'><a href="#" onclick="javascript: funcionarios_emisores_especificos_agregar(1); return false;">Agregar otro</a></div>
                    <div id="div_funcionarios_emisores_especificos_1_array">                
                        <table id="grilla_funcionarios_emisores_especificos_1" class="lista">
                            <tbody>
                                <?php
                                if(count($emisores['firma_uno']['tipo_cargos']) > 1) {
                                    $cadena1= "";
                                    foreach ($emisores['firma_uno']['tipo_cargos'] as $value) {
                                        $cargo_tipo = Doctrine::getTable('Organigrama_CargoTipo')->find($value);
                                        $cadena1.= "<tr>";
                                        $cadena1.= "<td>";
                                        $cadena1.= "<font class='f16n'>" . $cargo_tipo->getNombre() . "</font>";
                                        $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_uno][tipo_cargos][]' type='hidden' value='". $cargo_tipo->getId() ."'/></td>";
                                        $cadena1.= "<td><a class='funcionarios_emisores_especificos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                        $cadena1.= "</tr>";
                                    }
                                    echo $cadena1;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" id="funcionarios_emisores_especificos_1_count" value="<?php echo ((count($emisores['firma_uno']['tipo_cargos']) > 1)? count($emisores['firma_uno']['tipo_cargos']) : '0') ?>"/>
            </div>
            <div id="div_funcionarios_emisores_firma_2" style="display: <?php echo ((isset($emisores['firma_dos']))? 'block' : 'none') ?>;">
                <br/>Firma Dos:<br/>
                <input type="text" name="correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_dos][nombre_firma]" value="<?php echo ((isset($emisores['firma_dos'])) ? $emisores['firma_dos']['nombre_firma'] : '') ?>"/><br/>
                <input type="radio" name="tipo_funcionarios_emisores_2" onclick="javascript:funcionarios_emisores_seteado('solicitante',2)" id="radio_funcionarios_emisores_solicitante" value="solicitante" <?php echo ((isset($emisores['firma_dos']) && count($emisores['firma_dos']['tipo_cargos'])== 1 && $emisores['firma_dos']['tipo_cargos'][0]== 'solicitante') ? 'checked' : '') ?>/> Quien redacta o solicita<br/>
                <input type="radio" name="tipo_funcionarios_emisores_2" onclick="javascript:funcionarios_emisores_seteado('supervisor',2)" id="radio_funcionarios_emisores_supervisor" value="supervisor" <?php echo ((isset($emisores['firma_dos']) && count($emisores['firma_dos']['tipo_cargos'])== 1 && $emisores['firma_dos']['tipo_cargos'][0]== 'supervisor') ? 'checked' : '') ?>/> Supervisor inmediato<br/>
                <input type="radio" name="tipo_funcionarios_emisores_2" onclick="javascript:funcionarios_emisores_seteado('todos',2)" id="radio_funcionarios_emisores_todos" value="todos" <?php echo ((isset($emisores['firma_dos']) && count($emisores['firma_dos']['tipo_cargos'])== 1 && $emisores['firma_dos']['tipo_cargos'][0]== 'todos') ? 'checked' : '') ?>/> Cualquier funcionario de la unidad<br/>
                <input type="radio" name="tipo_funcionarios_emisores_2" onclick="javascript:funcionarios_emisores_seteado('autorizados',2)" id="radio_funcionarios_emisores_autorizados" value="autorizados" <?php echo ((isset($emisores['firma_dos']) && count($emisores['firma_dos']['tipo_cargos'])== 1 && $emisores['firma_dos']['tipo_cargos'][0]== 'autorizados') ? 'checked' : ((!isset($emisores['firma_dos'])) ? 'checked' : '')) ?>/> Autorizados a firmar en la unidad<br/>
                <input type="radio" name="tipo_funcionarios_emisores_2" onclick="javascript:funcionarios_emisores_especificos(2)" id="radio_funcionarios_emisores_especificos_2" value="especificos" <?php echo ((isset($emisores['firma_dos']) && count($emisores['firma_dos']['tipo_cargos']) > 1) ? 'checked' : '') ?>/> Cargos especificos

                <div id="div_funcionarios_emisores_2_seteados">
                    <input type="hidden" value="solicitante" id="funcionarios_emisores_seteado_2_hidden" name="correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_dos][tipo_cargos][]"/>
                </div>

                <div id="div_funcionarios_emisores_2_especificos" style="display: <?php echo ((isset($emisores['firma_dos']))? ((count($emisores['firma_dos']['tipo_cargos']) > 1) ? 'block' : 'none') : 'none'); ?>;">
                    <?php
                        $tipo_cargos_emisores = Doctrine::getTable('Organigrama_CargoTipo')
                                                ->createQuery('a')
                                                ->orderBy('nombre')->execute();
                    ?>
                    <select name="funcionarios_emisores_especificos_2_id" id="funcionarios_emisores_especificos_2_id">
                            <option value=""><- seleccione -></option>
                        <?php foreach( $tipo_cargos_emisores as $tipo_cargo_emisor ) { ?>
                            <option value="<?php echo $tipo_cargo_emisor->getId(); ?>"><?php echo $tipo_cargo_emisor->getNombre(); ?></option>
                        <?php } ?>
                    </select>
                    <div class='partial_new_view partial'><a href="#" onclick="javascript: funcionarios_emisores_especificos_agregar(2); return false;">Agregar otro</a></div>
                    <div id="div_funcionarios_emisores_especificos_2_array">                
                        <table id="grilla_funcionarios_emisores_especificos_2" class="lista">
                            <tbody>
                                <?php
                                if(isset($emisores['firma_dos'])) {
                                    if(count($emisores['firma_dos']['tipo_cargos']) > 1) {
                                        $cadena2= "";
                                        foreach ($emisores['firma_dos']['tipo_cargos'] as $value) {
                                            $cargo_tipo = Doctrine::getTable('Organigrama_CargoTipo')->find($value);
                                            $cadena2.= "<tr>";
                                            $cadena2.= "<td>";
                                            $cadena2.= "<font class='f16n'>" . $cargo_tipo->getNombre() . "</font>";
                                            $cadena2.= "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_dos][tipo_cargos][]' type='hidden' value='". $cargo_tipo->getId() ."'/></td>";
                                            $cadena2.= "<td><a class='funcionarios_emisores_especificos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                            $cadena2.= "</tr>";
                                        }
                                        echo $cadena2;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" id="funcionarios_emisores_especificos_2_count" value="<?php echo ((isset($emisores['firma_dos'])? ((count($emisores['firma_dos']['tipo_cargos']) > 1)? count($emisores['firma_dos']['tipo_cargos']) : '0') : '0')); ?>"/>
            </div>
            <div id="div_funcionarios_emisores_firma_3" style="display: <?php echo ((isset($emisores['firma_tres']))? 'block' : 'none') ?>;">
                <br/>Firma Tres:<br/>
                <input type="text" name="correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_tres][nombre_firma]" value="<?php echo ((isset($emisores['firma_tres'])) ? $emisores['firma_tres']['nombre_firma'] : '') ?>"/><br/>
                <input type="radio" name="tipo_funcionarios_emisores_3" onclick="javascript:funcionarios_emisores_seteado('solicitante',3)" id="radio_funcionarios_emisores_solicitante" value="solicitante" <?php echo ((isset($emisores['firma_tres']) && count($emisores['firma_tres']['tipo_cargos'])== 1 && $emisores['firma_tres']['tipo_cargos'][0]== 'solicitante') ? 'checked' : '') ?>/> Quien redacta o solicita<br/>
                <input type="radio" name="tipo_funcionarios_emisores_3" onclick="javascript:funcionarios_emisores_seteado('supervisor',3)" id="radio_funcionarios_emisores_supervisor" value="supervisor" <?php echo ((isset($emisores['firma_tres']) && count($emisores['firma_tres']['tipo_cargos'])== 1 && $emisores['firma_tres']['tipo_cargos'][0]== 'supervisor') ? 'checked' : '') ?>/> Supervisor inmediato<br/>
                <input type="radio" name="tipo_funcionarios_emisores_3" onclick="javascript:funcionarios_emisores_seteado('todos',3)" id="radio_funcionarios_emisores_todos" value="todos" <?php echo ((isset($emisores['firma_tres']) && count($emisores['firma_tres']['tipo_cargos'])== 1 && $emisores['firma_tres']['tipo_cargos'][0]== 'todos') ? 'checked' : '') ?>/> Cualquier funcionario de la unidad<br/>
                <input type="radio" name="tipo_funcionarios_emisores_3" onclick="javascript:funcionarios_emisores_seteado('autorizados',3)" id="radio_funcionarios_emisores_autorizados" value="autorizados" <?php echo ((isset($emisores['firma_tres']) && count($emisores['firma_tres']['tipo_cargos'])== 1 && $emisores['firma_tres']['tipo_cargos'][0]== 'autorizados') ? 'checked' : ((!isset($emisores['firma_tres']))? 'checked' : '')) ?>/> Autorizados a firmar en la unidad<br/>
                <input type="radio" name="tipo_funcionarios_emisores_3" onclick="javascript:funcionarios_emisores_especificos(3)" id="radio_funcionarios_emisores_especificos_3" value="especificos" <?php echo ((isset($emisores['firma_tres']) && count($emisores['firma_tres']['tipo_cargos']) > 1) ? 'checked' : '') ?>/> Cargos especificos

                <div id="div_funcionarios_emisores_3_seteados">
                    <input type="hidden" value="solicitante" id="funcionarios_emisores_seteado_3_hidden" name="correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_tres][tipo_cargos][]"/>
                </div>

                <div id="div_funcionarios_emisores_3_especificos" style="display: <?php echo (isset($emisores['firma_tres'])? ((count($emisores['firma_tres']['tipo_cargos']) > 1) ? 'block' : 'none') : 'none'); ?>;">
                    <?php
                        $tipo_cargos_emisores = Doctrine::getTable('Organigrama_CargoTipo')
                                                ->createQuery('a')
                                                ->orderBy('nombre')->execute();
                    ?>
                    <select name="funcionarios_emisores_especificos_id" id="funcionarios_emisores_especificos_3_id">
                            <option value=""><- seleccione -></option>
                        <?php foreach( $tipo_cargos_emisores as $tipo_cargo_emisor ) { ?>
                            <option value="<?php echo $tipo_cargo_emisor->getId(); ?>"><?php echo $tipo_cargo_emisor->getNombre(); ?></option>
                        <?php } ?>
                    </select>
                    <div class='partial_new_view partial'><a href="#" onclick="javascript: funcionarios_emisores_especificos_agregar(3); return false;">Agregar otro</a></div>
                    <div id="div_funcionarios_emisores_especificos_3_array">                
                        <table id="grilla_funcionarios_emisores_especificos_3" class="lista">
                            <tbody>
                                <?php
                                if(isset($emisores['firma_tres'])) {
                                    if(count($emisores['firma_tres']['tipo_cargos']) > 1) {
                                        $cadena3= "";
                                        foreach ($emisores['firma_tres']['tipo_cargos'] as $value) {
                                            $cargo_tipo = Doctrine::getTable('Organigrama_CargoTipo')->find($value);
                                            if($cargo_tipo) {
                                                $cadena3.= "<tr>";
                                                $cadena3.= "<td>";
                                                $cadena3.= "<font class='f16n'>" . $cargo_tipo->getNombre() . "</font>";
                                                $cadena3.= "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][funcionarios][firma_tres][tipo_cargos][]' type='hidden' value='". $cargo_tipo->getId() ."'/></td>";
                                                $cadena3.= "<td><a class='funcionarios_emisores_especificos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                                $cadena3.= "</tr>";
                                            }
                                        }
                                        echo $cadena3;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" id="funcionarios_emisores_especificos_3_count" value="<?php echo ((isset($emisores['firma_tres']))? ((count($emisores['firma_tres']['tipo_cargos']) > 1)? count($emisores['firma_tres']['tipo_cargos']) : '0') : '0'); ?>"/>
            </div>
            
        </div>
        <div class="help">Seleccione los funcionarios o cargos que pueden firmar este tipo de formato.</div>
    </div>
</div>
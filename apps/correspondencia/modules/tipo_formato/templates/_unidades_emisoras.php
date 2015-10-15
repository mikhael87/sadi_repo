<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $emisores= $parametros_all['emisores']['unidades'];
}else
    $emisores= Array('todas'=> 'true', 'especificas'=> 'false');
?>
<script>    
    $(document).ready(function(){
        unidades_emisoras_especificas_eliminar();
        unidades_emisoras_tipos_eliminar();

        $("form").submit(function() {
            if($("#radio_unidades_emisoras_todas").is(':checked')) {  
                $("#div_unidades_emisoras_especificas").remove();
            } else {
                unidades_emisoras_especificas_agregar();
                unidades_emisoras_tipos_agregar();
                
                if ((parseInt($("#unidades_emisoras_especificas_count").val()) > 0) || (parseInt($("#unidades_emisoras_tipos_count").val()) > 0)) {
                    $("#div_unidades_emisoras_todas").remove();
                    return true;
                } else  {
                    return false;
                }
            }
        });
    });
    
    function unidades_emisoras_todas(){
        $("#div_unidades_emisoras_todas").show();
        $("#div_unidades_emisoras_especificas").hide();
    }
    
    function unidades_emisoras_especificas(){
        $("#div_unidades_emisoras_especificas").show();
        $("#div_unidades_emisoras_todas").hide();
    }
    
    function unidades_emisoras_especificas_agregar(){
        if($("#unidades_emisoras_especificas_id").val())
        {
            cadena = "<tr>";
            cadena += "<td>";
            cadena += "<font class='f16n'>" + $("#unidades_emisoras_especificas_id option:selected").text() + "</font>";
            cadena += "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][unidades][especificas][]' type='hidden' value='" + $("#unidades_emisoras_especificas_id").val() + "'/>" + "</td>";
            cadena += "<td><a class='unidades_emisoras_especificas_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            cadena += "</tr>";
            $("#grilla_unidades_emisoras_especificas tbody").append(cadena);
            unidades_emisoras_especificas_eliminar();
            $("#unidades_emisoras_especificas_count").val(parseInt($("#unidades_emisoras_especificas_count").val())+1);
            $("#unidades_emisoras_especificas_id option[value='']").attr('selected', 'selected');
        }
        else
        { alert('Debe seleccionar la unidad emisora para poder agregar otra'); }
    };    
    
    function unidades_emisoras_especificas_eliminar(){
        $("a.unidades_emisoras_especificas_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    
    function unidades_emisoras_tipos_agregar(){
        if($("#unidades_emisoras_tipos_id").val())
        {
            cadena = "<tr>";
            cadena += "<td>";
            cadena += "<font class='f16n'>" + $("#unidades_emisoras_tipos_id option:selected").text() + "</font>";
            cadena += "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][unidades][tipos][]' type='hidden' value='" + $("#unidades_emisoras_tipos_id").val() + "'/>" + "</td>";
            cadena += "<td><a class='unidades_emisoras_tipos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            cadena += "</tr>";
            $("#grilla_unidades_emisoras_tipos tbody").append(cadena);
            unidades_emisoras_tipos_eliminar();
            $("#unidades_emisoras_tipos_count").val(parseInt($("#unidades_emisoras_tipos_count").val())+1);
            $("#unidades_emisoras_tipos_id option[value='']").attr('selected', 'selected');
        }
        else
        { alert('Debe seleccionar el tipo de unidad emisora para poder agregar otro'); }
    };    
    
    function unidades_emisoras_tipos_eliminar(){
        $("a.unidades_emisoras_tipos_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };    
</script>


<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_parametros">
    <div>
        <label>Unidades emisoras</label>
        <div class="content">
            <input type="radio" name="tipo_unidades_emisoras" onclick="javascript:unidades_emisoras_todas()" id="radio_unidades_emisoras_todas" value="todas" <?php echo (($emisores['todas']== 'true') ? 'checked': ''); ?>/> Todas las unidades &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="tipo_unidades_emisoras" onclick="javascript:unidades_emisoras_especificas()" id="radio_unidades_emisoras_especificas" value="especificas" <?php echo (($emisores['todas']== 'false') ? 'checked': ''); ?>/> Unidades seleccionadas
            
            <div id="div_unidades_emisoras_todas">
                <input type="hidden" value="true" name="correspondencia_tipo_formato[parametros_contenido][emisores][unidades][todas]"/>
            </div>
            
            <div id="div_unidades_emisoras_especificas" style="display: <?php echo ((is_array($emisores['especificas']))? 'block' : 'none') ?>">
                <br/>
                Unidades especificas<br/>
                <?php $unidades_emisoras = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>
                <select name="unidades_emisoras_especificas_id" id="unidades_emisoras_especificas_id">
                    <?php foreach( $unidades_emisoras as $clave=>$valor ) { ?>
                        <option value="<?php echo $clave; ?>"><?php echo $valor; ?></option>
                    <?php } ?>
                </select>
                <div class='partial_new_view partial'><a href="#" onclick="javascript: unidades_emisoras_especificas_agregar(); return false;">Agregar otra</a></div>
                <div id="div_unidades_emisoras_especificas_array">                
                    <table id="grilla_unidades_emisoras_especificas" class="lista">
                        <tbody>
                            <?php
                            if(is_array($emisores['especificas'])) {
                                $cadena1= "";
                                foreach($emisores['especificas'] as $value) {
                                    $unidad = Doctrine::getTable('Organigrama_Unidad')->find($value);
                                    $cadena1.= "<tr>";
                                    $cadena1.= "<td>";
                                    $cadena1.= "<font class='f16n'>" . $unidad->getNombre() . "</font>";
                                    $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][unidades][tipos][]' type='hidden' value='". $unidad->getId() ."'/></td>";
                                    $cadena1.= "<td><a class='unidades_emisoras_especificas_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                    $cadena1.= "</tr>";
                                }
                                echo $cadena1;
                            } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="unidades_emisoras_especificas_count" value="<?php echo ((isset($emisores['especificos']) && is_array($emisores['especificas']))? count($emisores['especificas']) : '0'); ?>"/>
                
                
                Unidades segun tipo<br/>
                <?php 
                    $tipo_unidades = Doctrine::getTable('Organigrama_UnidadTipo')
                                            ->createQuery('a')
                                            ->orderBy('nombre')->execute();
                ?>
                <select name="unidades_emisoras_tipos_id" id="unidades_emisoras_tipos_id">
                        <option value=""><- Seleccione -></option>
                    <?php foreach( $tipo_unidades as $tipo_unidad ) { ?>
                        <option value="<?php echo $tipo_unidad->getId(); ?>"><?php echo $tipo_unidad->getNombre(); ?></option>
                    <?php } ?>
                </select>
                <div class='partial_new_view partial'><a href="#" onclick="javascript: unidades_emisoras_tipos_agregar(); return false;">Agregar otra</a></div>
                <div id="div_unidades_emisoras_tipos_array">                
                    <table id="grilla_unidades_emisoras_tipos" class="lista">
                        <tbody>
                            <?php
                            if(isset($emisores['tipos']) && is_array($emisores['tipos'])) {
                                $cadena2= "";
                                foreach($emisores['tipos'] as $value) {
                                    $unidad = Doctrine::getTable('Organigrama_UnidadTipo')->find($value);
                                    $cadena2.= "<tr>";
                                    $cadena2.= "<td>";
                                    $cadena2.= "<font class='f16n'>" . $unidad->getNombre() . "</font>";
                                    $cadena2.= "<input name='correspondencia_tipo_formato[parametros_contenido][emisores][unidades][especificas][]' type='hidden' value='". $unidad->getId() ."'/></td>";
                                    $cadena2.= "<td><a class='unidades_emisoras_especificas_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                    $cadena2.= "</tr>";
                                }
                                echo $cadena2;
                            } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="unidades_emisoras_tipos_count" value="<?php echo ((isset($emisores['tipos']) && is_array($emisores['tipos']) ? count($emisores['tipos']) : '0')); ?>"/>
            </div>
        </div>
        <div class="help">Seleccione las unidades o tipos de unidades que pueden emitir este tipo de formato.</div>
    </div>
</div>
<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $receptores= $parametros_all['receptores']['unidades'];
}else
    $receptores= Array('seteada'=> 'todas', 'especificas'=> 'false', 'tipos'=> 'false');
?>
<script>    
    
    $(document).ready(function(){
        unidades_receptoras_especificas_eliminar();
        unidades_receptoras_tipos_eliminar();
        
        $("form").submit(function() {
            if($("#radio_unidades_receptoras_especificas").is(':checked')) {  
                unidades_receptoras_especificas_agregar();
                unidades_receptoras_tipos_agregar();
                
                if ((parseInt($("#unidades_receptoras_especificas_count").val()) > 0) || (parseInt($("#unidades_receptoras_tipos_count").val()) > 0)) {
                    $("#div_unidades_receptoras_seteadas").remove();
                    return true;
                } else  {
                    return false;
                }
            } else {
                $("#div_unidades_receptoras_especificas").remove();
            }
        });
    });
    
    function unidades_receptoras_setear(seteo){
        $("#unidades_receptoras_seteadas").val(seteo);
        
        $("#div_unidades_receptoras_seteadas").show();
        $("#div_unidades_receptoras_especificas").hide();
    }
    
    function unidades_receptoras_especificas(){
        $("#div_unidades_receptoras_especificas").show();
        $("#div_unidades_receptoras_seteadas").hide();
    }
    
    function unidades_receptoras_especificas_agregar(){
        if($("#unidades_receptoras_especificas_id").val())
        {
            cadena = "<tr>";
            cadena += "<td>";
            cadena += "<font class='f16n'>" + $("#unidades_receptoras_especificas_id option:selected").text() + "</font>";
            cadena += "<input name='correspondencia_tipo_formato[parametros_contenido][receptores][unidades][especificas][]' type='hidden' value='" + $("#unidades_receptoras_especificas_id").val() + "'/>" + "</td>";
            cadena += "<td><a class='unidades_receptoras_especificas_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            cadena += "</tr>";
            $("#grilla_unidades_receptoras_especificas tbody").append(cadena);
            unidades_receptoras_especificas_eliminar();
            $("#unidades_receptoras_especificas_count").val(parseInt($("#unidades_receptoras_especificas_count").val())+1);
            $("#unidades_receptoras_especificas_id option[value='']").attr('selected', 'selected');
        }
        else
        { alert('Debe seleccionar la unidad receptora para poder agregar otra'); }
    };    
    
    function unidades_receptoras_especificas_eliminar(){
        $("a.unidades_receptoras_especificas_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    
    function unidades_receptoras_tipos_agregar(){
        if($("#unidades_receptoras_tipos_id").val())
        {
            cadena = "<tr>";
            cadena += "<td>";
            cadena += "<font class='f16n'>" + $("#unidades_receptoras_tipos_id option:selected").text() + "</font>";
            cadena += "<input name='correspondencia_tipo_formato[parametros_contenido][receptores][unidades][tipos][]' type='hidden' value='" + $("#unidades_receptoras_tipos_id").val() + "'/>" + "</td>";
            cadena += "<td><a class='unidades_receptoras_tipos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            cadena += "</tr>";
            $("#grilla_unidades_receptoras_tipos tbody").append(cadena);
            unidades_receptoras_tipos_eliminar();
            $("#unidades_receptoras_tipos_count").val(parseInt($("#unidades_receptoras_tipos_count").val())+1);
            $("#unidades_receptoras_tipos_id option[value='']").attr('selected', 'selected');
        }
        else
        { alert('Debe seleccionar el tipo de unidad receptora para poder agregar otra'); }
    };    
    
    function unidades_receptoras_tipos_eliminar(){
        $("a.unidades_receptoras_tipos_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
</script>


<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_parametros">
    <div>
        <label>Unidades receptoras</label>
        <div class="content">
            <input type="radio" name="tipo_unidades_receptoras" onclick="javascript:unidades_receptoras_setear('todas');" id="radio_unidades_receptoras_todas" value="todas" <?php echo (($receptores['seteada']== 'todas')? 'checked' : ''); ?>/> Todas las unidades<br/>
            <input type="radio" name="tipo_unidades_receptoras" onclick="javascript:unidades_receptoras_setear('inicial');" id="radio_unidades_receptoras_inicial" value="inicial" <?php echo (($receptores['seteada']== 'inicial')? 'checked' : ''); ?>/> La unidad que inicio el proceso<br/>
            <input type="radio" name="tipo_unidades_receptoras" onclick="javascript:unidades_receptoras_setear('admin3');" id="radio_unidades_receptoras_inicial" value="admin3" <?php echo (($receptores['seteada']== 'admin3')? 'checked' : ''); ?>/> Administrativo grado 3<br/>
            <input type="radio" name="tipo_unidades_receptoras" onclick="javascript:unidades_receptoras_especificas();" id="radio_unidades_receptoras_especificas" value="especificas" <?php echo (($receptores['seteada']== 'false')? 'checked' : ''); ?>/> Unidades seleccionadas<br/>
            
            <div id="div_unidades_receptoras_seteadas">
                <input type="hidden" value="<?php echo $receptores['seteada']; ?>" id="unidades_receptoras_seteadas" name="correspondencia_tipo_formato[parametros_contenido][receptores][unidades][seteada]"/>
            </div>
            
            <div id="div_unidades_receptoras_especificas" style="display: <?php echo (($receptores['seteada']== 'false')? 'block' : 'none'); ?>;">
                <br/>
                Unidades especificas<br/>
                <?php $unidades_receptoras = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>
                <select name="unidades_receptoras_especificas_id" id="unidades_receptoras_especificas_id">
                    <?php foreach( $unidades_receptoras as $clave=>$valor ) { ?>
                        <option value="<?php echo $clave; ?>"><?php echo $valor; ?></option>
                    <?php } ?>
                </select>
                <div class='partial_new_view partial'><a href="#" onclick="javascript: unidades_receptoras_especificas_agregar(); return false;">Agregar otra</a></div>
                <div id="div_unidades_receptoras_especificas_array">                
                    <table id="grilla_unidades_receptoras_especificas" class="lista">
                        <tbody>
                            <?php
                            if(is_array($receptores['especificas'])) {
                                $cadena1= "";
                                foreach($receptores['especificas'] as $value) {
                                    $unidad = Doctrine::getTable('Organigrama_Unidad')->find($value);
                                    $cadena1.= "<tr>";
                                    $cadena1.= "<td>";
                                    $cadena1.= "<font class='f16n'>" . $unidad->getNombre() . "</font>";
                                    $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][receptores][unidades][especificas][]' type='hidden' value='". $unidad->getId() ."'/></td>";
                                    $cadena1.= "<td><a class='unidades_receptoras_especificas_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                    $cadena1.= "</tr>";
                                }
                                echo $cadena1;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="unidades_receptoras_especificas_count" value="<?php echo ((is_array($receptores['especificas']) ? count($receptores['especificas']) : '0')); ?>"/>
                
                
                Unidades segun tipo<br/>
                <?php 
                    $tipo_unidades = Doctrine::getTable('Organigrama_UnidadTipo')
                                            ->createQuery('a')
                                            ->orderBy('nombre')->execute();
                ?>
                <select name="unidades_receptoras_tipos_id" id="unidades_receptoras_tipos_id">
                        <option value=""><- Seleccione -></option>
                    <?php foreach( $tipo_unidades as $tipo_unidad ) { ?>
                        <option value="<?php echo $tipo_unidad->getId(); ?>"><?php echo $tipo_unidad->getNombre(); ?></option>
                    <?php } ?>
                </select>
                <div class='partial_new_view partial'><a href="#" onclick="javascript: unidades_receptoras_tipos_agregar(); return false;">Agregar otra</a></div>
                <div id="div_unidades_receptoras_tipos_array">                
                    <table id="grilla_unidades_receptoras_tipos" class="lista">
                        <tbody>
                            <?php
                            if(is_array($receptores['tipos'])) {
                                $cadena2= '';
                                foreach($receptores['tipos'] as $value) {
                                    $unidad = Doctrine::getTable('Organigrama_UnidadTipo')->find($value);
                                    $cadena2.= "<tr>";
                                    $cadena2.= "<td>";
                                    $cadena2.= "<font class='f16n'>" . $unidad->getNombre() . "</font>";
                                    $cadena2.= "<input name='correspondencia_tipo_formato[parametros_contenido][receptores][unidades][tipos][]' type='hidden' value='". $unidad->getId() ."'/></td>";
                                    $cadena2.= "<td><a class='unidades_receptoras_tipos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                    $cadena2.= "</tr>";
                                }
                                echo $cadena2;
                            } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="unidades_receptoras_tipos_count" value="<?php echo ((is_array($receptores['tipos']) ? count($receptores['tipos']) : '0')); ?>"/>
                
            </div>
        </div>
        <div class="help">Seleccione las unidades o tipos de unidades que pueden recibir este tipo de formato.</div>
    </div>
</div>
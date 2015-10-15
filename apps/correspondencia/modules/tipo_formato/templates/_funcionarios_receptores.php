<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $receptores= $parametros_all['receptores']['funcionarios'];
}else
    $receptores= Array(0=> 'todos');
?>
<script>    
    $(document).ready(function(){
        funcionarios_receptores_especificos_eliminar();
        
        $("form").submit(function() {
            if($("#radio_funcionarios_receptores_especificos").is(':checked')) { 
                funcionarios_receptores_especificos_agregar();
                
                if (parseInt($("#funcionarios_receptores_especificos_count").val()) > 0) {
                    $("#div_funcionarios_receptores_seteados").remove();
                    return true;
                } else  {
                    return false;
                }
            } else {
                $("#div_funcionarios_receptores_especificos").remove();
            }
        });
    });
    
    function funcionarios_receptores_seteado(seteo){
        $("#funcionarios_receptores_seteado_hidden").val(seteo);
        
        $("#div_funcionarios_receptores_seteados").show();
        $("#div_funcionarios_receptores_especificos").hide();
    }
    
    function funcionarios_receptores_especificos(){
        $("#div_funcionarios_receptores_especificos").show();
        $("#div_funcionarios_receptores_seteados").hide();
    }
    
    function funcionarios_receptores_especificos_agregar(){
        if($("#funcionarios_receptores_especificos_id").val())
        {
            cadena = "<tr>";
            cadena += "<td>";
            cadena += "<font class='f16n'>" + $("#funcionarios_receptores_especificos_id option:selected").text() + "</font>";
            cadena += "<input name='correspondencia_tipo_formato[parametros_contenido][receptores][funcionarios][]' type='hidden' value='" + $("#funcionarios_receptores_especificos_id").val() + "'/>" + "</td>";
            cadena += "<td><a class='funcionarios_receptores_especificos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            cadena += "</tr>";
            $("#grilla_funcionarios_receptores_especificos tbody").append(cadena);
            funcionarios_receptores_especificos_eliminar();
            $("#funcionarios_receptores_especificos_count").val(parseInt($("#funcionarios_receptores_especificos_count").val())+1);
            $("#funcionarios_receptores_especificos_id option[value='']").attr('selected', 'selected');
        }
        else
        { alert('Debe seleccionar el cargo especifico que recibe para poder agregar otro'); }
    };    
    
    function funcionarios_receptores_especificos_eliminar(){
        $("a.funcionarios_receptores_especificos_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
</script>


<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_parametros">
    <div>
        <label>Funcionarios receptores</label>
        <div class="content">
            <input type="radio" name="tipo_funcionarios_receptores" onclick="javascript:funcionarios_receptores_seteado('todos')" id="radio_funcionarios_receptores_todos" value="todos" <?php echo (($receptores[0]== 'todos') ? 'checked' : ''); ?>/> Cualquier funcionario de la unidad<br/>
            <input type="radio" name="tipo_funcionarios_receptores" onclick="javascript:funcionarios_receptores_especificos()" id="radio_funcionarios_receptores_especificos" value="especificos" <?php echo ((count($receptores) > 1) ? 'checked' : ''); ?>/> Cargos especificos
            
            <div id="div_funcionarios_receptores_seteados">
                <input type="hidden" value="<?php echo (($receptores[0]== 'todos') ? 'todos' : ''); ?>" id="funcionarios_receptores_seteado_hidden" name="correspondencia_tipo_formato[parametros_contenido][receptores][funcionarios][]"/>
            </div>
            
            <div id="div_funcionarios_receptores_especificos" style="display: <?php echo ((count($receptores) > 1) ? 'block' : 'none'); ?>;">
                <?php
                    $tipo_cargos_receptores = Doctrine::getTable('Organigrama_CargoTipo')
                                            ->createQuery('a')
                                            ->orderBy('nombre')->execute();
                ?>
                <select name="funcionarios_receptores_especificos_id" id="funcionarios_receptores_especificos_id">
                        <option value=""><- seleccione -></option>
                    <?php foreach( $tipo_cargos_receptores as $tipo_cargo_emisor ) { ?>
                        <option value="<?php echo $tipo_cargo_emisor->getId(); ?>"><?php echo $tipo_cargo_emisor->getNombre(); ?></option>
                    <?php } ?>
                </select>
                <div class='partial_new_view partial'><a href="#" onclick="javascript: funcionarios_receptores_especificos_agregar(); return false;">Agregar otro</a></div>
                <div id="div_funcionarios_receptores_especificos_array">                
                    <table id="grilla_funcionarios_receptores_especificos" class="lista">
                        <tbody>
                            <?php
                            if(count($receptores) > 1) {
                                $cadena1= "";
                                foreach($receptores as $value) {
                                    $cargo_tipo = Doctrine::getTable('Organigrama_CargoTipo')->find($value);
                                    if($cargo_tipo) {
                                        $cadena1.= "<tr>";
                                        $cadena1.= "<td>";
                                        $cadena1.= "<font class='f16n'>" . $cargo_tipo->getNombre() . "</font>";
                                        $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][receptores][funcionarios][]' type='hidden' value='". $cargo_tipo->getId() ."'/></td>";
                                        $cadena1.= "<td><a class='funcionarios_receptores_especificos_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                        $cadena1.= "</tr>";
                                    }
                                }
                                echo $cadena1;
                            } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="funcionarios_receptores_especificos_count" value="<?php echo ((count($receptores) > 1)? count($receptores) : '0'); ?>"/>
            </div>
        </div>
        <div class="help">Seleccione los funcionarios o cargos que pueden recibir este tipo de formato.</div>
    </div>
</div>
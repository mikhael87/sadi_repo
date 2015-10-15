<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $formulario= $parametros_all['formulario'];
}else
    $formulario= Array();
?>
<script>
    $(document).ready(function(){
        formulario_campos_creados_eliminar();
        
        $("form").submit(function() {
            if (parseInt($("#formulario_campos_creados_count").val()) > 0) {
                if($('#vb_fijo').is(':checked')) {
                    if($('#table_rutas >tbody >tr').length) {
                        if($('#table_rutas >tbody >tr >td').html() === 'Sin ruta') {
                            alert('No hay rutas generales definidas.');
                            return false;
                        }else {
                            return true;
                        }
                    }else {
                        alert('No hay rutas generales definidas.');
                        return false;
                    }
                }else
                    return true;
            } else  {
                return false;
            }
        });
    });
    
    function formulario_campos_creados_agregar(){
        if($("#formulario_nombre_campo").val())
        {
            if($("#correspondencia_tipo_formato_classe").val())
            {
                cadena = "<tr>";
                cadena += "<td><font class='f16n'>" + $("#formulario_nombre_campo").val() + "</font></td>";
                cadena += "<td><font class='f16n'>" + $("#formulario_tipo_campo option:selected").text() + "</font></td>";

                requerido = '';
                requerido_valor = 'false'; 
                if($("#formulario_requerido_true").is(':checked')) {
                    requerido_valor = 'true'; requerido = '<img src="/images/icon/tick.png"/>'
                }

                cadena += "<td><font class='f16n'>" + requerido + "</font></td>";
                cadena += "<td>" +
                             "<input name='correspondencia_tipo_formato[parametros_contenido][formulario][" + $("#formulario_campos_creados_count").val() + "][nombre_campo]' type='hidden' value='" + $("#formulario_nombre_campo").val() + "'/>" +
                             "<input name='correspondencia_tipo_formato[parametros_contenido][formulario][" + $("#formulario_campos_creados_count").val() + "][tipo_campo]' type='hidden' value='" + $("#formulario_tipo_campo").val() + "'/>" +
                             "<input name='correspondencia_tipo_formato[parametros_contenido][formulario][" + $("#formulario_campos_creados_count").val() + "][requerido]' type='hidden' value='" + requerido_valor + "'/>" +
                             "<a class='formulario_campos_creados_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a>" +
                          "</td>";
                cadena += "</tr>";

                $("#grilla_formulario_campos_creados tbody").append(cadena);
                formulario_campos_creados_eliminar();
                $("#formulario_campos_creados_count").val(parseInt($("#formulario_campos_creados_count").val())+1);
                $("#formulario_nombre_campo").val('');
            
            } else { alert('Antes de agregar un campo debe definir el nombre de la classe del formato'); }
        } else { alert('Debe escribir el nombre del campo para poder agregar otro'); }
    };    
    
    function formulario_campos_creados_eliminar(){
        $("a.formulario_campos_creados_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
</script>



<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_plantilla_formulario">
    <div>
        <label>Nombre del Campo</label>
        <div class="content">
            <input type="text" name="input_nombre_campo" id="formulario_nombre_campo"/>
        </div>
        <div class="help">Nombre que veran los usuarios finales del formato.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_plantilla_formulario">
    <div>
        <label>Tipo de campo</label>
        <div class="content">
            <select name="input_tipo_campo" id="formulario_tipo_campo">
                <option value="texto">Texto</option>
                <option value="numero">Número</option>
                <option value="combo">Combo o lista</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
                <option value="archivo">Archivo (Tipologia Documental)</option>
            </select>    
        </div>
        <div class="help">Seleccione el tipo de campo.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_plantilla_formulario">
    <div>
        <label>¿Requerido?</label>
        <div class="content">
            <input type="radio" name="input_requerido_campo" id="formulario_requerido_true" value="true" checked="checked"/> SI &nbsp;&nbsp;
            <input type="radio" name="input_requerido_campo" id="formulario_requerido_false" value="false"/> NO
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_plantilla_formulario">
    <div>
        <label>Campos creados</label>
        <div class="content">
            <div class='partial_new_view partial'><a href="#" onclick="javascript: formulario_campos_creados_agregar(); return false;">Agregar otro</a></div>
            <div id="div_formulario_campos_creados_array">                
                <table id="grilla_formulario_campos_creados" class="lista">
                    <tbody>
                        <th>Nombre del campo</th>
                        <th>Tipo de campo</th>
                        <th>Requerido</th>
                        <th>&nbsp;</th>
                        <?php
                        $cadena1= "";
                        $cont= 0;
                        foreach ($formulario as $value) {
                            $cadena1.= "<tr>";
                            $cadena1.= "<td><font class='f16n'>" . $value['nombre_campo'] . "</font></td>";
                            $cadena1.= "<td><font class='f16n'>" . $value['tipo_campo'] . "</font></td>";
                            $cadena1.= "<td>";
                            if($value['requerido']== 'true')
                                $cadena1.= "<img src='/images/icon/tick.png' />";
                            $cadena1.= "</td>";
                            $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][formulario][" .$cont. "][nombre_campo]' type='hidden' value='". $value['nombre_campo'] ."'/></td>";
                            $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][formulario][" .$cont. "][tipo_campo]' type='hidden' value='".  $value['tipo_campo'] ."'/></td>";
                            $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][formulario][" .$cont. "][requerido]' type='hidden' value='". $value['requerido'] ."'/></td>";
                            $cadena1.= "<td><a class='formulario_campos_creados_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                            $cadena1.= "</tr>";
                            $cont++;
                        }
                        echo $cadena1;
                        ?>
                    </tbody>
                </table>
            </div>
            <input type="hidden" id="formulario_campos_creados_count" value="<?php echo count($formulario); ?>"/>
        </div>
    </div>
</div>

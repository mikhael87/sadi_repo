<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        $("form").submit(function() {
            if($("#externo_movil").val())
                fn_agregar();
            
            return true;
        });
    });
    
    function fn_agregar(){
        if($("#externo_movil").val())
        {
            if((/^\d{7,7}$/.test($("#externo_movil").val()))) {
                i= $("#tlf_guardados tbody").find("tr").length
                cadena = "<tr>";
                cadena = cadena + "<td><font class='f16b'>" + jQuery.trim($("#codigo_movil option:selected").text())+"-"+$("#externo_movil").val() + "</font><br/>";
                if ($("#externo_nombre").val()) {
                    cadena = cadena + "<font class='f16n'>" + $("#externo_nombre").val() + "</font>";
                }
                cadena = cadena + "<input name='telf[otros]["+i+"][tlf]' type='hidden' value='" + $("#codigo_movil").val() + $("#externo_movil").val() + "'/>" + "</td>";
                if ($("#externo_nombre").val()) {
                    cadena = cadena + "<input name='telf[otros]["+i+"][nombre]' type='hidden' value='" + $("#externo_nombre").val() + "'/>" + "</td>";
                }
                cadena = cadena + "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                $("#tlf_guardados tbody").append(cadena);
                document.getElementById("archivo").value="";
                document.getElementById("externo_movil").value="";
                document.getElementById("externo_nombre").value="";
                fn_dar_eliminar();
                fn_cantidad();
            }else{
                alert('El número no es correcto');
            }
        }
        else
        { alert('Debe escribir un número telefónico valido para agregar otro.'); }
    };
    
    function fn_cantidad(){
            cantidad = $("#tlf_guardados tbody").find("tr").length;
            $("#span_cantidad").html(cantidad);
    };    

    function fn_dar_eliminar(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    function fn_conExcel(){
        $("#externo_movil").removeClass('required');
    }
</script>

<style>
    #externo_movil{ width: 110px !important; }
    #externo_nombre{ width: 175px !important; }
</style>
<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div>
        <label for="telefonos_id">Tel&eacute;fono Movil</label>
        <div class="content">
            <select name="codigo_movil" id="codigo_movil">
                <option value="0412">0412</option>
                <option value="0414">0414</option>
                <option value="0424">0424</option>
                <option value="0416">0416</option>
                <option value="0426">0426</option>
            </select>
            <input id="externo_movil" name="telf[unico][tlf]" value="" />
        </div>
        <div class="help">Seleccione el codigo y escriba el n&uacute;mero telef&oacute;nico.</div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div>
        <label for="telefonos_id">Personalizar mensaje</label>
        <div class="content">
            <input id="externo_nombre" name="telf[unico][nombre]" value="" />
        </div>
        <div class="help">Escriba el nombre de receptor.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div class="content">
        
        <a class='partial_new_view partial' href="#" onClick="javascript: fn_agregar(); return false;">Agregar otro receptor</a>  
        <div style="max-height: 200px; overflow-y: auto; overflow-x: hidden; width: 300px;">
            <table id="tlf_guardados" width="300">
            <tbody>
                <?php 
                if($sf_user->getAttribute('sms_reutilizado')){ 
                    
                    $yml_variables = Doctrine::getTable('Public_MensajesMasivos')->findByMensajesId($sf_user->getAttribute('sms_reutilizado'));

                    $td = ''; $th = ''; $k = 0;
                    $telefono = '';
                    $nombre = '';

                    foreach ($yml_variables as $yml_variable){
                        $variables = sfYaml::load($yml_variable->getVariables());

                        foreach ($variables as $variable) {
                            $td .= "<tr>";
                            foreach ($variable as $key => $value) {
                                $td .= "<td>".$value."</td>";
                                
                                if($key=='TELF') $telefono = $value;
                                if($key=='NOMBRE') $nombre = $value;
                            }
                                $td .= '<td>
                                            <input name="telf[otros]['.$k.'][tlf]" type="hidden" value="'.$telefono.'"/>
                                            <input name="telf[otros]['.$k.'][nombre]" type="hidden" value="'.$nombre.'"/>
                                            <a class="elimina" style="cursor: pointer;">
                                                <img src="/images/icon/delete.png"/>
                                            </a>
                                        </td>';
                            $td .= "</tr>";
                            $k++;
                        }
                    }

                    echo $td;
                    echo "<script>
                            fn_dar_eliminar(); 
                            fn_cantidad(); 
                            
                            $(document).ready(function(){
                                fn_conExcel();
                            });
                        </script>";
                }
                
                ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        fn_orden_cla();
        fn_eliminar_cla();
        
        $("form").submit(function() {
            
            if($("#clasificador").val())
                fn_agregar_cla();

            if (parseInt($("#clasificador_count").val()) > 0) {
                return true;
            } else  {
                alert('Debe agregar al menos un descriptor que identifique la Serie Documental');
                return false;
            }
        });
    });

    function fn_agregar_cla(){

        if($("#clasificador").val())
        {
            error = false;
            
            cadena = "<tr>";
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#clasificador").val()) + "</font><br/>";
            valor = $("#clasificador").val() + "&&&" + $("#tipo_dato").val() + "&&&";

            vacio = '';
            var check = 'f'; if($("#vacio").is(':checked')) { check = 't'; vacio = '<img src="/images/icon/tick.png"/>'}
            
            valor += check + "&&&";
            
            if($("#tipo_dato").val()=='texto'){
                if(/^\d+$/.test(jQuery.trim($("#texto_maximo").val()))){ 
                    valor += jQuery.trim($("#texto_maximo").val());
                    parametros = "maximo de caracteres: "+jQuery.trim($("#texto_maximo").val());
                } else {
                    error = true;
                    alert('El maximo de caracteres debe ser un número entero');
                }
            } else if ($("#tipo_dato").val()=='numero'){
                if(/^\d+$/.test(jQuery.trim($("#numero_minimo").val())) && /^\d+$/.test(jQuery.trim($("#numero_maximo").val()))){ 
                    valor += jQuery.trim($("#numero_minimo").val())+'-'+jQuery.trim($("#numero_maximo").val());
                    parametros = "Nº Minimo: "+jQuery.trim($("#numero_minimo").val())+' - Nº Maximo: '+jQuery.trim($("#numero_maximo").val());
                } else {
                    error = true;
                    alert('El número minimo y número maximo deben ser números enteros');
                }
            } else if ($("#tipo_dato").val()=='listado'){
                if (jQuery.trim($("#listado_opciones").val()) != ''){ 
                    valor += jQuery.trim($("#listado_opciones").val());
                    parametros = "Opciones: "+jQuery.trim($("#listado_opciones").val());
                } else {
                    error = true;
                    alert('Escriba al menos una opción para el listado');
                }
            } else if ($("#tipo_dato").val()=='fecha'){
                valor += $("#ano_desde").val()+'-'+$("#ano_hasta").val();
                parametros = "Desde: "+$("#ano_desde").val()+' - Hasta: '+$("#ano_hasta").val();
            } else {
                error = true;
                alert('Seleccione el tipo de descriptor y configure sus parametros');
            }
            
            if(error == false){ 
                cadena += "<input name='clasificadores[]' type='hidden' value='" + valor + "'/>";
                cadena += "</td>";
                cadena += "<td>"+$("#tipo_dato").val()+"</td>";
                cadena += "<td>"+vacio+"</td>";
                cadena += "<td>"+parametros+"</td>";
                cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a>";
                cadena += "<a class='up' style='cursor: pointer;' title='Subir'><img src='/images/icon/desc.png'/></a>";
                cadena += "<a class='down' style='cursor: pointer;' title='Bajar'><img src='/images/icon/asc.png'/></a></td>";
                cadena += "</tr>";
                $("#grilla_clasificadores tbody").append(cadena);
                fn_eliminar_cla();
                fn_orden_cla();

                $("#clasificador").val('');
                $("#parametros").html('<br/>');
                $("#tipo_dato option[value='']").attr('selected', 'selected');
                $('#vacio').attr('checked', false);

                $("#clasificador_count").val(parseInt($("#clasificador_count").val())+1);
            }
        }
        else
        { alert('Debe escribir el nombre del descriptor para poder agregar otro'); }
    };

    function fn_eliminar_cla(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    function fn_orden_cla(){
        $(".up,.down").click(function(){
            var row = $(this).parents("tr:first");
            if ($(this).is(".up")) {
                row.insertBefore(row.prev());
            } else {
                row.insertAfter(row.next());
            }
        });
    };
    
    function fn_parametros(){
        if($("#tipo_dato").val()=='texto'){
            cadena = 'Tamaño maximo de caracteres: <input id="texto_maximo" type="text" size="4" name="parametros[][texto_maximo]">';
        } else if ($("#tipo_dato").val()=='numero'){
            cadena = 'Nº minimo: <input type="text" size="4" id="numero_minimo" name="parametros[][numero_maximo]"> - Nº maximo: <input type="text" size="4" id="numero_maximo" name="parametros[][numero_minimo]">';
        } else if ($("#tipo_dato").val()=='listado'){
            cadena = 'Escriba las opciones separadas por punto y coma (;) <input type="text" id="listado_opciones" name="parametros[][listado_opciones]">';
        } else if ($("#tipo_dato").val()=='fecha'){
            cadena = 'Año inicial: ';
            cadena += '<?php echo '<select id="ano_desde" name="parametros[][ano_desde]">'; for($i=1900;$i<2050;$i++) { echo '<option value="'.$i.'"'; if($i == date('Y')) echo ' selected '; echo '>'.$i.'</option>'; } echo '</select>'; ?>';
            cadena += ' Año Final: ';
            cadena += '<?php echo '<select id="ano_hasta" name="parametros[][ano_hasta]">'; for($i=1901;$i<2051;$i++) { echo '<option value="'.$i.'"'; if($i == date('Y')) echo ' selected '; echo '>'.$i.'</option>'; } echo '</select>'; ?>';
        }
        
        $("#parametros").html('<br/>'+cadena+'<br/><br/>');
    };
   
</script>

<div class="sf_admin_form_row sf_admin_text">

    <div>
        <label for="">Descriptor</label>
        <div class="content">
            <input id="clasificador"/>
            <select id="tipo_dato" onchange="javascript: fn_parametros();">
                <option value=""><- Tipo de dato -></option>
                <option value="texto">Texto</option>
                <option value="numero">Número</option>
                <option value="listado">Listado</option>
                <option value="fecha">Fecha</option>
            </select>
            Permitir vacio <input type="checkbox" id="vacio"/>
            <div id="parametros"></div>
            <div class='partial_new_view partial'><a href="#" onclick="javascript: fn_agregar_cla(); return false;">Agregar otro</a></div>
        </div>
    </div>

    <div class="help">Escriba el nombre del descriptor que se usara para diferenciar los diferentes expedientes de la misma serie documental.</div>
    
    <div>
        <div class="content">
            <table id="grilla_clasificadores" class="lista">
                <tbody>
                        <tr id="titulos"> <!--style="display: none;"-->
                            <th>Nombre</th>
                            <th>Tipo de Dato</th>
                            <th>Vacio</th>
                            <th>Parametros</th>
                            <th></th>
                        </tr>
                <?php 
                    $clasificadores = array();
                    if(!$form->isNew()){
                    $clasificadores = Doctrine::getTable('Archivo_Clasificador')->detallesClasificadores($form['id']->getValue());
                    
                    foreach ($clasificadores as $clasificador) { ?>
                        <tr>
                            <td><font class='f16b'><?php echo $clasificador->getNombre(); ?></font><br/>
                            <input name='clasificadores[]' type='hidden' value='<?php echo $clasificador->getId(); ?>%%%edit'/></td>
                            <td><?php echo $clasificador->getTipoDato(); ?></td>
                            <td><?php if($clasificador->getVacio()=='t') echo '<img src="/images/icon/tick.png"/>'; ?></td>
                            <td>
                                <?php 
                                if($clasificador->getTipoDato()=='texto'){
                                    echo "maximo de caracteres: ".$clasificador->getParametros();
                                } else if ($clasificador->getTipoDato()=='numero'){
                                    list($n_minimo,$n_maximo) = explode('-',$clasificador->getParametros());
                                    echo "Nº Minimo: ".$n_minimo." - Nº Maximo: ".$n_maximo;
                                } else if ($clasificador->getTipoDato()=='listado'){
                                    echo "Opciones: ".$clasificador->getParametros();
                                } else if ($clasificador->getTipoDato()=='fecha'){
                                    list($desde,$hasta) = explode('-',$clasificador->getParametros());
                                    echo "Desde: ".$desde." - Hasta: ".$hasta;
                                }
                                ?>
                            </td>
                            <td>
                                <?php if($clasificador->getClasificados()==0) { ?>
                                <a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a>
                                <?php } else { ?>
                                <img src='/images/icon/delete_lock.png' title="No puede eliminar este clasificador, actualmente existen <?php echo $clasificador->getClasificados(); ?> expedientes clasificados."/>
                                <?php } ?>
                                <a class='up' style='cursor: pointer;' title="Subir"><img src='/images/icon/desc.png'/></a>
                                <a class='down' style='cursor: pointer;' title="Bajar"><img src='/images/icon/asc.png'/></a>
                            </td>
                        </tr>
                <?php }} ?>
                </tbody>
            </table>
            <input type="hidden" id="clasificador_count" value="<?php echo count($clasificadores); ?>"/>
        </div>
    </div>
</div>
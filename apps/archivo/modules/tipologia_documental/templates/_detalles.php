<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        fn_orden_eti();
        fn_eliminar_eti();
        
        $("form").submit(function() {
            
            if(jQuery.trim($("#etiqueta").val()))
                fn_agregar_eti();

            if (parseInt($("#etiqueta_count").val()) > 0) {
                return true;
            } else  {
                alert('Debe agregar al menos un descriptor que identifique la Tipologia Documental');
                return false;
            }
        });
    });

    function fn_agregar_eti(){

        if(jQuery.trim($("#etiqueta").val()))
        {
            error = false;
            
            cadena = "<tr>";
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#etiqueta").val()) + "</font><br/>";
            
            valor = jQuery.trim($("#etiqueta").val()) + "&&&" + $("#tipo_dato").val() + "&&&";

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
                cadena += "<input name='detalles[etiquetas][]' type='hidden' value='" + valor + "'/>";
                cadena += "</td>";
                cadena += "<td>"+$("#tipo_dato").val()+"</td>";
                cadena += "<td>"+vacio+"</td>";
                cadena += "<td>"+parametros+"</td>";
                cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a>";
                cadena += "<a class='up' style='cursor: pointer;' title='Subir'><img src='/images/icon/desc.png'/></a>";
                cadena += "<a class='down' style='cursor: pointer;' title='Bajar'><img src='/images/icon/asc.png'/></a></td>";
                cadena += "</tr>";
                $("#grilla_etiquetas tbody").append(cadena);
                fn_eliminar_eti();
                fn_orden_eti();

                $("#etiqueta").val('');
                $("#parametros").html('<br/>');
                $("#tipo_dato option[value='']").attr('selected', 'selected');
                $('#vacio').attr('checked', false);

                $("#etiqueta_count").val(parseInt($("#etiqueta_count").val())+1);
            }
        }
        else
        { alert('Debe escribir el nombre de la etiqueta para poder agregar otra'); }
    };

    function fn_eliminar_eti(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    function fn_orden_eti(){
        $(".up,.down").click(function(){
            var row = $(this).parents("tr:first");
            if ($(this).is(".up")) {
                row.insertBefore(row.prev());
            } else {
                row.insertAfter(row.next());
            }
        });
    };
    
    function toggleCuerpo()
    {
        $("#div_cuerpo_nombre").toggle("slow");
        $("#div_cuerpo_button_save").toggle("slow");
        $("#cuerpo_nombre").val(null);
    }
    
    function saveCuerpo()
    {
        if($("#cuerpo_nombre").val()) {

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>cuerpo_documental/saveCuerpo',
                type:'POST',
                dataType:'html',
                data:'serie_id='+<?php echo $sf_user->getAttribute('serie_documental_id'); ?>+
                    '&cuerpo='+$("#cuerpo_nombre").val(),
                success:function(data, textStatus){
                    jQuery('#select_cuerpos').append(data);
                }})

            $("#div_cuerpo_nombre").hide();
            $("#div_cuerpo_button_save").hide();
        } else {
            alert('Escriba el nombre de la sección.');
        }
    }
    
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

    <div><br/>
        <label for="">Sección</label>
        <div class="content" style="position: relative;">
            
<div style="position: absolute; top: -20px;">
    <div style="position: absolute; left: 0px; cursor: pointer;" id="div_cuerpo_button_new" onclick="javascript:toggleCuerpo();"><?php echo image_tag('icon/new.png'); ?></div>
    <div style="position: absolute; left: 20px; display: none;" id="div_cuerpo_nombre"><input type="text" id="cuerpo_nombre" size="25"/></div>
    <div style="position: absolute; top: 3px; left: 237px; cursor: pointer; display: none;" id="div_cuerpo_button_save" onclick="javascript:saveCuerpo();"><?php echo image_tag('icon/filesave.png'); ?></div>
</div>

<select id="select_cuerpos" name="detalles[cuerpo]">
    <option value=""></option>
                <?php 
                    $cuerpos = Doctrine::getTable('Archivo_CuerpoDocumental')->findBySerieDocumentalId($sf_user->getAttribute('serie_documental_id'));
                    
                    foreach ($cuerpos as $cuerpo) { 
                        $select='';
                        if($cuerpo->getId() == $form['cuerpo_documental_id']->getValue()) $select='selected="selected"';
                        ?>
                    <option value="<?php echo $cuerpo->getId(); ?>" <?php echo $select; ?>><?php echo $cuerpo->getNombre(); ?></option>
                <?php } ?>
</select>
        </div>
    </div>

    <div class="help">De ser necesario seleccione o agrege un cuerpo donde se almacenara el documento dentro de la serie.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">

    <div>
        <label for="">Etiqueta</label>
        <div class="content">
            <input name="detalles[etiquetas][]" id="etiqueta"/>
            <select id="tipo_dato" onchange="javascript: fn_parametros();">
                <option value=""><- Tipo de dato -></option>
                <option value="texto">Texto</option>
                <option value="numero">Número</option>
                <option value="listado">Listado</option>
                <option value="fecha">Fecha</option>
            </select>
            Permitir vacio <input type="checkbox" id="vacio"/>
            <div id="parametros"></div>
            <div class='partial_new_view partial'><a href="#" onclick="javascript: fn_agregar_eti(); return false;">Agregar otra</a></div>
        </div>
    </div>

    <div class="help">Escriba la etiqueta o información a capturar que se utilizará para esta tipologia documental o tipo de documento.</div>
    
    <div>
        <div class="content">
            <table id="grilla_etiquetas" class="lista">
                <tbody>
                        <tr id="titulos"> <!--style="display: none;"-->
                            <th>Nombre</th>
                            <th>Tipo de Dato</th>
                            <th>Vacio</th>
                            <th>Parametros</th>
                            <th></th>
                        </tr>
                <?php 
                    $etiquetas = array();
                    if(!$form->isNew()){
                    $etiquetas = Doctrine::getTable('Archivo_Etiqueta')->detallesEtiquetaciones($form['id']->getValue());
                    
                    foreach ($etiquetas as $etiqueta) { ?>
                        <tr>
                            <td><font class='f16b'><?php echo $etiqueta->getNombre(); ?></font><br/>
                            <input name='detalles[etiquetas][]' type='hidden' value='<?php echo $etiqueta->getId(); ?>%%%edit'/></td>
                            <td><?php echo $etiqueta->getTipoDato(); ?></td>
                            <td><?php if($etiqueta->getVacio()=='t') echo '<img src="/images/icon/tick.png"/>'; ?></td>
                            <td>
                                <?php 
                                if($etiqueta->getTipoDato()=='texto'){
                                    echo "maximo de caracteres: ".$etiqueta->getParametros();
                                } else if ($etiqueta->getTipoDato()=='numero'){
                                    list($n_minimo,$n_maximo) = explode('-',$etiqueta->getParametros());
                                    echo "Nº Minimo: ".$n_minimo." - Nº Maximo: ".$n_maximo;
                                } else if ($etiqueta->getTipoDato()=='listado'){
                                    echo "Opciones: ".$etiqueta->getParametros();
                                } else if ($etiqueta->getTipoDato()=='fecha'){
                                    list($desde,$hasta) = explode('-',$etiqueta->getParametros());
                                    echo "Desde: ".$desde." - Hasta: ".$hasta;
                                }
                                ?>
                            </td>
                            <td>
                                <?php if($etiqueta->getEtiquetados()==0) { ?>
                                <a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a>
                                <?php } else { ?>
                                <img src='/images/icon/delete_lock.png' title="No puede eliminar esta etiqueta, actualmente existen <?php echo $etiqueta->getEtiquetados(); ?> documentos etiquetados."/>
                                <?php } ?>
                                <a class='up' style='cursor: pointer;' title='Subir'><img src='/images/icon/desc.png'/></a>
                                <a class='down' style='cursor: pointer;' title='Bajar'><img src='/images/icon/asc.png'/></a>
                            </td>
                        </tr>
                <?php }} ?>
                </tbody>
            </table>
            <input type="hidden" id="etiqueta_count" value="<?php echo count($etiquetas); ?>"/>
        </div>
    </div>
</div>
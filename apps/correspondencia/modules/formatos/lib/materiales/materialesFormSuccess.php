<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/materiales/assets.php'); ?>

<script>
    $(document).ready(function(){
        fn_dar_eliminar_mat();
        fn_cantidad_mat();
    });

    function fn_agregar_mat(){

        if($("#correspondencia_formato_materiales_material_id").val() && $("#correspondencia_formato_materiales_cantidad").val())
        {
            cadena = "<tr>";
            cadena = cadena + "<td><font class='f16b'>" + jQuery.trim($("#correspondencia_formato_materiales_material_id option:selected").text()) + "</font><br/>";
            cadena = cadena + "<input name='correspondencia[formato][materiales_material][copias][]' type='hidden' value='" + $("#correspondencia_formato_materiales_material_id").val() + "#" + $("#correspondencia_formato_materiales_cantidad").val() + "'/>" + "</td>";
            cadena = cadena + "<td>" + $("#correspondencia_formato_materiales_cantidad").val() + "</td>";
            cadena = cadena + "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            $("#grilla_materiales tbody").append(cadena);
            fn_dar_eliminar_mat();
            fn_cantidad_mat();
            
            $("#correspondencia_formato_materiales_cantidad").val('');
        }
        else
        { alert('Debe seleccionar el material y escribir la cantidad para poder agregar otro'); }
    };

    function fn_cantidad_mat(){
            cantidad = $("#grilla tbody").find("tr").length;
            $("#span_cantidad").html(cantidad);
    };    

    function fn_dar_eliminar_mat(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
</script>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'materiales_material')) ?>

    <div>
        <label for="materiales_material">Materiales</label>
        <div class="content">
            
            <?php 
                $materiales_group = Doctrine::getTable('Extenciones_Materiales')->comboMateriales();
                $w = new sfWidgetFormChoice(array('choices' => $materiales_group));
                
                echo $w->render('correspondencia[formato][materiales_material][id]');
            ?>
            Cantidad <input name="correspondencia[formato][materiales_material][cantidad]" id="correspondencia_formato_materiales_cantidad" size="4"/>

        <div class='partial_new_view partial'><a href="#" onclick="javascript: fn_agregar_mat(); return false;">Guardar y agregar otro</a></div>
            <div><br/>
                <table id="grilla_materiales" class="lista">
                    <tbody>
                        <?php 
                            if (isset($formulario['materiales_material'])) {
                            if (isset($formulario['materiales_material']['copias'])) {
                                $materiales = $formulario['materiales_material']['copias'];

                                foreach ($materiales as $material)
                                {
                                    list($material_id,$material_cantidad) = explode ('#',$material);
                                    $material_detalle = Doctrine::getTable('Extenciones_Materiales')->find($material_id);
                                    
                                    $cadena = "<tr>";
                                    $cadena .= "<td><font class='f16b'>" . $material_detalle->getNombre() . "</font><br/>";
                                    $cadena .= "<input name='correspondencia[formato][materiales_material][copias][]' type='hidden' value='" . $material_id . "#" . $material_cantidad . "'/>" . "</td>";
                                    $cadena .= "<td>".$material_cantidad."</td>";
                                    $cadena .= "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                    $cadena .= "</tr>";
                                    echo $cadena;
                                }
                            }}
                        ?>
                    </tbody>
                </table>
            </div>
            
            
        </div>

    </div>

    <div class="help">Seleccione el material que necesita.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_contenido">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'materiales_observaciones')) ?>

    <div>
        <label for="materiales_observaciones">Observaciones</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="60" name="correspondencia[formato][materiales_observaciones]" id="servicios_generales_descripcion"><?php if(isset($formulario['materiales_observaciones'])) echo $formulario['materiales_observaciones']; ?></textarea>
        </div>
    </div>

    <div class="help">Escriba de ser necesario las observaciones a esta requisición.</div>

</div>
</fieldset>
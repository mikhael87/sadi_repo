<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/proveeduria/assets.php'); ?>

<script>
    function keyupFunc() {
        var filter = $('#articulo_nombre').val(), count = 0;
        $("#select_articulos option").each(function () {
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
            } else {
                $(this).show();
                count++;
            }
        });
    }
    
    function toggleFinder()
    {
        $("#articulo_nombre").toggle("slow");
        $("#articulo_nombre").val('');
    }
    
    $(document).ready(function(){
        eliminar_articulo();
    });

    function agregar_articulo(){

        if($("#select_articulos").val() && $("#cantidad_articulos").val())
        {
            cadena = "<tr>";
            cadena = cadena + "<td>" + $("#select_articulos option:selected").text();
            cadena = cadena + "<input name='correspondencia[formato][proveeduria_articulos][" + $("#select_articulos").val() + "]' type='hidden' value='" + $("#cantidad_articulos").val() + "'/></td>";
            cadena = cadena + "<td>" + $("#cantidad_articulos").val() + "</td>";
            cadena = cadena + "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            $("#grilla_articulos tbody").append(cadena);
            eliminar_articulo();
            
            $("#cantidad_articulos").val('');
            $("#articulo_nombre").val('');
            $("#articulo_nombre").hide("slow");
            
            $("#select_articulos option").each(function () {
                $(this).show();
            });
        }
        else
        { alert('Debe seleccionar el articulo y escribir la cantidad para poder agregar otro'); }
    };  

    function eliminar_articulo(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
</script>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_foreignkey">
    <div>
        <label for="inventario_articulo_solicitud_tipo_articulo">Tipo de articulo</label>
        <div class="content">
            <?php echo image_tag('icon/find.png', array('onClick' => 'javascript: toggleFinder()', 'style'=> 'float: left')); ?>
            <input style="left: 25px; display: none;" type="text" id="articulo_nombre" size="25" onKeyUp="keyupFunc()"/>
            <?php  $articulos = Doctrine::getTable('Inventario_Articulo')->articulosActivosOrden('nombre'); ?>
            <select id="select_articulos">
                <option value="0"><- Seleccione -></option>
                <?php foreach ($articulos as $articulo) { ?>
                    <option value="<?php echo $articulo->getId(); ?>"><?php echo $articulo->getCodigo().' - '.$articulo->getNombre().' - '.$articulo->getUnidadMedida(); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_foreignkey">
    <div>
        <label for="inventario_articulo_solicitud_tipo_articulo">Cantidad a solicitar</label>
        <div class="content">
            <input id="cantidad_articulos" value="" size="3" maxlength="6"/>
            <a class='partial_new_view partial' href="#" onclick="javascript: agregar_articulo(); return false;">Agregar otro</a>

            <div><br/>
                <table id="grilla_articulos" class="lista">
                    <tbody>
                        <tr><th>Articulo</th><th>Cantidad</th><th></th></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_foreignkey ">
    <div>
        <label for="inventario_articulo">Observacion</label>
        <div class="content">
            <textarea rows="4" cols="30" name="correspondencia[formato][proveeduria_observacion]"></textarea>
        </div>
    </div>
    <div class="help">Agregue las observaciones o motivo de solicitud de los articulos.</div>
</div>

</fieldset>
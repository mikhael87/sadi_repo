<?php
$parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
$hijos= false;
if(isset($parametros_all['hijos']))
    $hijos= $parametros_all['hijos'];
?>
<script>    
    
    $(document).ready(function(){
        $("form").submit(function() {
            formatos_hijos_agregar();
        });
    });
    
    function formatos_hijos_agregar(){
        if($("#formatos_hijos_id").val())
        {
            cadena = "<tr id='tr_hijo_"+$("#formatos_hijos_id").val()+"'>";
            cadena += "<td>";
            cadena += "<font class='f16n'>" + $("#formatos_hijos_id option:selected").text() + "</font>";
            cadena += "<input name='correspondencia_tipo_formato[parametros_contenido][hijos][]' type='hidden' value='" + $("#formatos_hijos_id").val() + "'/>" + "</td>";
            cadena += "<td><a href='#' onclick='formatos_hijos_eliminar("+$("#formatos_hijos_id").val()+"); return false;'><img src='/images/icon/delete.png'/></a></td>";
            cadena += "</tr>";
            $("#grilla_formatos_hijos tbody").append(cadena);
            $("#formatos_hijos_id option[value='']").attr('selected', 'selected');
        }
    };    
    
    function formatos_hijos_eliminar(hijo_id){
        $("#tr_hijo_"+hijo_id).fadeOut("normal", function(){
                $("#tr_hijo_"+hijo_id).remove();
            });
    };
</script>


<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_parametros">
    <div>
        <label>Formatos hijos</label>
        <div class="content">
            <?php
                $formatos_hijos = Doctrine::getTable('Correspondencia_TipoFormato')
                                        ->createQuery('a')
                                        ->orderBy('nombre')->execute();
            ?>
            <select name="formatos_hijos_id" id="formatos_hijos_id">
                    <option value=""><- seleccione -></option>
                <?php foreach( $formatos_hijos as $formato_hijo ) { ?>
                    <option value="<?php echo $formato_hijo->getId(); ?>"><?php echo $formato_hijo->getNombre(); ?></option>
                <?php } ?>
            </select>
            <div class='partial_new_view partial'><a href="#" onclick="formatos_hijos_agregar(); return false;">Agregar otro</a></div>
            <div id="div_formatos_hijos_array">                
                <table id="grilla_formatos_hijos" class="lista">
                    <tbody>
                        <?php
                        if($hijos) {
                            $cadena1= '';
                            foreach ($hijos as $value) {
                                $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($value);
                                $cadena1.= "<tr id='tr_hijo_".$tipo_formato->getId()."'>";
                                $cadena1.= "<td>";
                                $cadena1.= "<font class='f16n'>" . $tipo_formato->getNombre() . "</font>";
                                $cadena1.= "<input name='correspondencia_tipo_formato[parametros_contenido][hijos][]' type='hidden' value='". $tipo_formato->getId() ."'/></td>";
                                $cadena1.= "<td><a href='#' onclick='formatos_hijos_eliminar(". $tipo_formato->getId() ."); return false;'><img src='/images/icon/delete.png'/></a></td>";
                                $cadena1.= "</tr>";
                            }
                            echo $cadena1;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="help">Seleccione los formatos que serviran para responder este formato.</div>
    </div>
</div>
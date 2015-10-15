<script>
    $(document).ready(function(){
        fn_dar_eliminar();
                        fn_cantidad();
        $("#frm_usu").validate();
    });

    function fn_agregar(){
        if($("#receptor_interno_funcionario_id").val() && $("#receptor_interno_unidad_id").val())
        {
            cadena = "<tr>";
            cadena = cadena + "<td><font class='f16b'>" + jQuery.trim($("#receptor_interno_unidad_id option:selected").text()) + "</font><br/>";
            cadena = cadena + "<font class='f16n'>" + $("#receptor_interno_funcionario_id option:selected").text() + "</font>";
            cadena = cadena + "<input name='receptores[]' type='hidden' value='" + $("#receptor_interno_unidad_id").val() + "#" + $("#receptor_interno_funcionario_id").val() + "'/>" + "</td>";
            cadena = cadena + "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            $("#grilla tbody").append(cadena);
            fn_dar_eliminar();
            fn_cantidad();
        }
        else
        { alert('Debe seleccionar la unidad y funcionario receptor para poder agregar otro'); }
    };

    function fn_cantidad(){
            cantidad = $("#grilla tbody").find("tr").length;
            $("#span_cantidad").html(cantidad);
    };    

    function fn_dar_eliminar(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
</script>


<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_funcionario">
    <div>
        <label for="receptor_interno_funcionario">Funcionario</label>

        <div class="content">

            <div id="funidad">
                <select name="receptor_interno[funcionario_id]" id="receptor_interno_funcionario_id">
                    <option value=""></option>
                </select>
                <input type="hidden" name="val_correspondencia_externa_funcionario" />
            </div>

        </div>

        <div class="help">
            Seleccione el funcionario que autorizara a recibir y redactar las correspondencias de la unidad.
            
            <div><br/>
                <table id="grilla" class="lista">
                    <tbody>
                        <?php 
                            if($sf_user->getAttribute('receptores_internos'))
                            {
                                $receptores = $sf_user->getAttribute('receptores_internos');
                                foreach ($receptores as $receptor)
                                {
                                    list($unidad_id,$funcionario_id) = explode ('#',$receptor);
                                    $receptor = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoFuncionario($unidad_id,$funcionario_id);

                                    if ($receptor[0]['sexo'] == 'M') {
                                        $cargo_nombre = str_replace('@', 'o', $receptor[0]['ctnombre']);
                                        $cargo_nombre = str_replace('(a)', '', $cargo_nombre);
                                    } else {
                                        $cargo_nombre = str_replace('@', 'a', $receptor[0]['ctnombre']);
                                        $cargo_nombre = str_replace('(a)', 'a', $cargo_nombre);
                                    }

                                    $funcionario_nombre = $receptor[0]['primer_nombre'].' '; 
                                    $funcionario_nombre .= $receptor[0]['segundo_nombre'].', ';
                                    $funcionario_nombre .= $receptor[0]['primer_apellido'].' ';
                                    $funcionario_nombre .= $receptor[0]['segundo_apellido'];
                                    
                                    $cadena = "<tr>";
                                    $cadena .= "<td><font class='f16b'>" . $receptor[0]['unidad'] . "</font><br/>";
                                    $cadena .= "<font class='f16n'>" . $cargo_nombre . " / " .$funcionario_nombre. "</font>";
                                    $cadena .= "<input name='receptores[]' type='hidden' value='" . $unidad_id . "#" . $funcionario_id . "'/>" . "</td>";
                                    $cadena .= "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                    $cadena .= "</tr>";
                                    echo $cadena;

                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$receptor = $sf_user->getAttribute('receptor_interno');
if($receptor['unidad_id']){ 
?>
    <script>
        <?php
        echo jq_remote_function(array('update' => 'funidad',
        'url' => 'externa/receptorFuncionario',
        'with'     => "'u_id=".$receptor['unidad_id']."'",))
        ?>
    </script>
<?php } ?>

    
    
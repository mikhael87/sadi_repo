<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        fn_dar_eliminar();
        fn_cantidad();
        $("#frm_usu").validate();
    });

    function fn_agregar(){
        if($("#archivo_solicitante_funcionario_id").val() && $("#archivo_solicitante_unidad_id").val())
        {
            cadena = "<tr>";
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#archivo_solicitante_unidad_text").html()) + "</font><br/>";
            cadena += "<font class='f16n'>" + $("#archivo_solicitante_funcionario_id option:selected").text() + "</font>";
            cadena += "<input name='prestamo_archivo[solicitante][copias][]' type='hidden' value='" + $("#archivo_solicitante_unidad_id").val() + "#" + $("#archivo_solicitante_funcionario_id").val() + "'/>" + "</td>";
            cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            $("#grilla tbody").append(cadena);
            fn_dar_eliminar();
            fn_cantidad();
        }
        else
        { alert('Debe seleccionar la unidad y funcionario al que hara el prestamo para poder agregar otro'); }
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

    function fn_toogle_unidad(arbol){
        $("#grupo_"+arbol).toggle();

        if($("#imagen_"+arbol).attr("src")=='/images/icon/plus.gif')
            $("#imagen_"+arbol).attr("src", '/images/icon/minus.gif');
        else
            $("#imagen_"+arbol).attr("src", '/images/icon/plus.gif');
    };

    function fn_open_combounidad(combo){
        if ($('#archivo_solicitante_unidad').is (':visible')){
            $("#"+combo).slideUp(300);
        }else {
            $("#"+combo).slideDown(300);
        }
    };

    function fn_close_combounidad(combo){
        $("#"+combo).slideUp(300);
    };

    function fn_select_combounidad(unida_id,unidad_nombre,combo){
        $('#'+combo+'_id').val(unida_id);
        $('#'+combo+'_text').html(unidad_nombre);

        $.ajax(
        {
            url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>prestamo/funcionariosSolicitantes',
            type:'POST',
            dataType:'html',
            data:'unidad_id='+unida_id,
            success:function(data, textStatus) {
                $("#div_solicitantes_funcionarios").html(data);
            }
        });


        fn_close_combounidad(combo);
    };
</script>



    <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_n_correspondencia_emisor">
        <div>
            <label for="prestamo_archivo[solicitante][unidad_id]">Unidad</label>
            <div class="content">

                &nbsp;
                <div style="position: relative; z-index: 1;">
                    <input type="hidden" name="prestamo_archivo[solicitante][unidad_id]" id="archivo_solicitante_unidad_id" value=""/>
                    <div style="position: absolute; top: -23px;"><select style="width: 678px;"><option value=""></option></select></div>
                    <div id="archivo_solicitante_unidad_text" style="position: absolute; top: -21px; width: 655px; padding-left: 25px; height: 25px;" onclick="javascript: fn_open_combounidad('archivo_solicitante_unidad');">< -- Seleccione --></div>
                    <div id="archivo_solicitante_unidad" style="position: absolute; top: -3px; border: solid 1px #AAAAAA; background-color: #ECECEC; padding-left: 25px; <!--max-height: 300px; overflow-y: auto;--> max-width: 650px; min-width: 650px; display: none;">
                    <?php
                    $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(TRUE);
                    $veces = 0;
                    foreach( $unidades as $clave=>$valor ) {
                        list($unidad_id, $tipo) = explode("&&", $clave);

                        if(!strpos($valor, "- Seleccione -")){
                            $veces_tmp = substr_count($valor, "nbsp;");

                            if(!isset($contador[$veces_tmp])) $contador[$veces_tmp]=0;
                            $contador[$veces_tmp]++;

                            $id=''; $i=0;
                            while($i<$veces_tmp){
                                $id .= $contador[$i].'_';
                                $i += 6;
                            }

                            if($veces_tmp < $veces){

                                foreach ($contador as $espacios => $value) {
                                    if($espacios > $veces_tmp) {
                                        unset($contador[$espacios]);
                                        echo "</div>";
                                    }
                                }
                            } else if ($veces_tmp > $veces) {
                                $left = $veces*10;

                                $toogle_imagen = "plus.gif";
                                $toogle_display = "none";
                                if(substr_count($id, "_")<=2) {
                                    $toogle_imagen = "minus.gif";
                                    $toogle_display = "block";
                                }

                                echo "<div style='position: relative;'>
                                            <div style='position: absolute; top:-13px; left:-16px; cursor: pointer;' onclick=\"javascript: fn_toogle_unidad('".$id."');\">
                                                <img id='imagen_".$id."' src='/images/icon/".$toogle_imagen."'/>
                                            </div>
                                        </div>
                                        <div id='grupo_".$id."' style='padding-left:25px; display: ".$toogle_display.";'>";
                            }

//                            $valor = str_replace("&nbsp;","",trim(html_entity_decode($valor)));
                            $valor = str_replace("&nbsp;","",trim($valor));

                            $veces = $veces_tmp;
                            $id .= $contador[$veces_tmp];

                            echo '<div style="cursor: pointer;" id="'.$id.'" onclick="javascript: fn_select_combounidad('.$unidad_id.',\''.$valor.'\',\'archivo_solicitante_unidad\');">'.$valor.'</div>';
                        }
                    } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_n_correspondencia_emisor" style="">
        <div>
            <label for="prestamo_archivo[solicitante][funcionario_id]">Funcionario</label>
            <div class="content">
                <div id="div_solicitantes_funcionarios">
                    <select name="prestamo_archivo[solicitante][funcionario_id]" id="archivo_solicitante_funcionario_id">
                        <option value=""></option>
                    </select>
                </div>

            <div><br/>
                <table id="grilla" class="lista">
                    <tbody>
                        <?php
                            if (isset($prestamo['solicitante'])) {
                            if (isset($prestamo['solicitante']['copias'])) {
                                $solicitantes = $prestamo['solicitante']['copias'];

                                foreach ($solicitantes as $solicitante)
                                {
                                    list($unidad_id,$funcionario_id) = explode ('#',$solicitante);
                                    $solicitante = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoFuncionario($unidad_id,$funcionario_id);

                                    if ($solicitante[0]['sexo'] == 'M') {
                                        $cargo_nombre = str_replace('@', 'o', $solicitante[0]['ctnombre']);
                                        $cargo_nombre = str_replace('(a)', '', $cargo_nombre);
                                    } else {
                                        $cargo_nombre = str_replace('@', 'a', $solicitante[0]['ctnombre']);
                                        $cargo_nombre = str_replace('(a)', 'a', $cargo_nombre);
                                    }

                                    $funcionario_nombre = $solicitante[0]['primer_nombre'].' ';
                                    $funcionario_nombre .= $solicitante[0]['segundo_nombre'].', ';
                                    $funcionario_nombre .= $solicitante[0]['primer_apellido'].' ';
                                    $funcionario_nombre .= $solicitante[0]['segundo_apellido'];

                                    $cadena = "<tr>";
                                    $cadena .= "<td><font class='f16b'>" . $solicitante[0]['unidad'] . "</font><br/>";
                                    $cadena .= "<font class='f16n'>" . $cargo_nombre . " / " .$funcionario_nombre. "</font>";

                                    if($copia=='S'){
                                        $cadena .= "<font class='f16b azul'>&nbsp;CC</font>";
                                    }

                                    $cadena .= "<input name='prestamo_archivo[copias][]' type='hidden' value='".$unidad_id."#".$funcionario_id."'/>" . "</td>";
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
    </div>
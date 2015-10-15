<?php use_helper('jQuery'); ?>

<script>
    modificado = 0;
    agregado = 0;
    var cantidad = 0;
    var agregar = 0;
    
    $(document).ready(function(){
        fn_dar_eliminar();
        fn_cantidad();
        $("#frm_usu").validate();
        
        $('#grupoReceptor').change(function (){
            grupo_id = $('#grupoReceptor').val();
            $.ajax(
            {
                url: '<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/funcionariosGrupo',
                type:'POST',
                dataType:'html',
                data:'grupo_id='+grupo_id+"&tipo=I",
                success:function(data, textStatus) {
                    modificado = 0;
                    $("#CargarGrupo").html(data);
                }
            });
        });
    });
    
    function fn_agregar(){
        if($("#correspondencia_receptor_funcionario_id").val() && $("#correspondencia_receptor_unidad_id").val())
        {
            cadena = "<tr>";
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#correspondencia_receptor_unidad_text").html()) + "</font><br/>";
            cadena += "<font class='f16n'>" + $("#correspondencia_receptor_funcionario_id option:selected").text() + "</font>";
            var copia = 'N';
            if($("#correspondencia_receptor_copia").is(':checked')){
                cadena += "<font class='f16b azul'>&nbsp;CC</font>";
                copia = 'S';
            }
            cadena += "<input name='correspondencia[receptor][copias][]' type='hidden' value='" + $("#correspondencia_receptor_unidad_id").val() + "#" + $("#correspondencia_receptor_funcionario_id").val() + '#' + copia + "'/>" + "</td>";
            cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            comp = $("#correspondencia_receptor_unidad_id").val() + "#" + $("#correspondencia_receptor_funcionario_id").val() + '#' + copia;
            if(cantidad > 0){
                agregar = 0;
                $("#grilla tbody tr td input[name='correspondencia[receptor][copias][]']").each(function (){
                    if($(this).val() == comp){ agregar = 1; }
                });
                if(agregar == 0)
                {
                    $("#grilla tbody").append(cadena);
                    modificado = 1;
                    fn_dar_eliminar();
                    fn_cantidad();
                    agregar = 0;
                }
            }
            else
            {
                $("#grilla tbody").append(cadena);
                modificado = 1;
                fn_dar_eliminar();
                fn_cantidad();
            }
            fn_select_combounidad('0', '<- Seleccione ->', 'correspondencia_receptor_unidad');
        }
        else
        { alert('Debe seleccionar la unidad y funcionario receptor para poder agregar otro'); }
    };
    
    function fn_agregar_grupo(unidad_nombre,unidad_id,funcionario_id,funcionario_nombre){
            cadena = "<tr>";
            cadena += "<td><font class='f16b'>" + unidad_nombre + "</font><br/>";
            cadena += "<font class='f16n'>" + funcionario_nombre + "</font>";
            cadena += "<input name='correspondencia[receptor][copias][]' type='hidden' value='" + unidad_id + "#" + funcionario_id + "#N'/>" + "</td>";
            cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            comp = unidad_id + "#" + funcionario_id + "#N";
            if(cantidad > 0){
                agregar = 0;
                $("#grilla tbody tr td input[name='correspondencia[receptor][copias][]']").each(function (){
                    if($(this).val() == comp){ agregar = 1; }
                });
                if(agregar == 0)
                {
                    $("#grilla tbody").append(cadena);
                    fn_dar_eliminar();
                    fn_cantidad();
                    agregar = 0;
                }
            }
            else
            {
                $("#grilla tbody").append(cadena);
                fn_dar_eliminar();
                fn_cantidad();
            }
            
    };

    function fn_cantidad(){
            cantidad = $("#grilla tbody").find("tr").length;
            $("#span_cantidad").html(cantidad);
            if(cantidad > 2)
                fn_guardar_grupo();
            else
                $("#guardarGrupo").remove();
    };
    
    var mod = 0;
    function fn_dar_eliminar(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                  $(this).remove(); 
                  modificado = 1;
                  fn_cantidad();
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
        if ($('#correspondencia_receptor_unidad').is (':visible')){
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
        var url= document.URL;
        url= url.replace('/index', '');
        var formato_id= $('#formato_tipo_formato_id').val();
        
        $.ajax(
        {
            url: '<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/funcionariosReceptores',
            type:'POST',
            dataType:'html',
            data:'unidad_id='+unida_id+'&tipo_formato_id='+formato_id,
            success:function(data, textStatus) {
                $("#div_receptores_funcionarios").html(data);
            }
        });


        fn_close_combounidad(combo);
    };
    
    function fn_guardar_grupo(click)
    {
        if(<?php echo $editado; ?> == 0){
            if(click == 1)
            {
                $("#guardarGrupo").slideToggle();
                $("#guardarGrupo").remove();
                agregar = '<td id="guardarGrupo"><input type="text" name="nombreGrupo" /><input type="button" onclick="fn_guardar_grupo(2);" value="Guardar" /></td>';
                $("#grilla > tbody > tr:first").append(agregar);
            }
            if(click == 2)
            {
                var d = 0;
                var grupoId = '';
                var nombreGrupo;

                $("#grilla tbody tr td input[name='correspondencia[receptor][copias][]']").each(function (){

                    if(d==0){grupoId = $(this).val(); d = 1;}
                    else{grupoId = grupoId+","+$(this).val();}

                });
                nombreGrupo = $("input[name='nombreGrupo']").val();
                $.ajax(
                {
                    url: '<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/guardarGrupo',
                    type:'POST',
                    dataType:'html',
                    data:'grupoId='+grupoId+'&nombreGrupo='+nombreGrupo+'&tipo=I',
                    success:function(data, textStatus) {
                        $("#guardarGrupo").slideToggle();
                        $("#guardarGrupo").remove();
                    }
                });
            }
            else
            {
                if((($("#grupoReceptor").val() != "") && (modificado == 1)) || ($("#grupoReceptor").val() == ""))
                {
                    if($("#guardarGrupo").length <= 0){
                        agregar = '<td id="guardarGrupo"><input type="button" onclick="fn_guardar_grupo(1);" value="Guardar grupo" /></td>';
                        $("#grilla > tbody > tr:first").append(agregar);
                    }
                }
            }
        }
    }
</script>

<fieldset id="sf_fieldset_receptores">
    <h2>Receptores</h2>
    <?php if(count($grupoReceptores)>0) { ?>
    <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_grupo_receptor">
        <div>
            <label for="correspondencia[receptor][grupo]">Grupos</label>
            <div class="content">
                <select id="grupoReceptor">
                    <option value=""><- Seleccione -></option>
                    <?php foreach($grupoReceptores as $grupoReceptor) { ?>
                    <option value="<?php echo $grupoReceptor->getGrupoId(); ?>"><?php echo $grupoReceptor->getNombre(); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_unidad_receptor">
        <div>
            <label for="correspondencia[receptor][unidad_id]">Unidad</label>
            <div class="content">

                &nbsp;
                <div style="position: relative; z-index: 1;">
                    <input type="hidden" name="correspondencia[receptor][unidad_id]" id="correspondencia_receptor_unidad_id" value=""/>
                    <div style="position: absolute; top: -23px;"><select style="width: 678px;"><option value=""></option></select></div>
                    <div id="correspondencia_receptor_unidad_text" style="position: absolute; top: -21px; width: 655px; padding-left: 25px; height: 25px;" onclick="javascript: fn_open_combounidad('correspondencia_receptor_unidad');">
                        <?php if(count($unidades)==0) { ?>
                            <fond class="rojo">Error: no existe una unidad receptora definida en los parametros del tipo de documento.</fond>
                        <?php } else { ?>
                            <- Seleccione ->
                        <?php } ?>
                    </div>
                    <div id="correspondencia_receptor_unidad" style="position: absolute; top: -3px; border: solid 1px #AAAAAA; background-color: #ECECEC; padding-left: 25px; <!--max-height: 300px; overflow-y: auto;--> max-width: 650px; min-width: 650px; display: none;">
                    <?php
                    
                    $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($sf_user->getAttribute('funcionario_id'));

                    //Bloqueo de unidades propias
//                    $cargo_array= array();
//                    foreach($funcionario_unidades_cargo as $unidades_cargo) {
//                        $cargo_array[]= $unidades_cargo->getUnidadId();
//                    }

                    $veces = 0;
                    foreach( $unidades as $clave=>$valor ) {
                        if($clave != ''){
                        list($unidad_id, $tipo) = explode("&&", $clave);

                        if(!strpos($valor, "- Seleccione -")){
                            $veces_tmp = substr_count($valor, "nbsp;");

                            if(!isset($contador[$veces_tmp])) $contador[$veces_tmp]=0;
                            $contador[$veces_tmp]++;

                            $id=''; $i=0;
                            while($i<$veces_tmp){
                                if(isset($contador[$i])){
                                    $id .= $contador[$i].'_';}
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

                            $valor = str_replace("&nbsp;","",trim(html_entity_decode($valor)));

                            $veces = $veces_tmp;
                            $id .= $contador[$veces_tmp];
                            
//                            if(in_array($unidad_id, $cargo_array))
//                                echo '<div style="cursor: pointer;" id="'.$id.'"><font style="color: #8c8c8c" title="No puede enviar comunicaciones a su misma unidad">'.$valor.'</font></div>';
//                            else
                                echo '<div style="cursor: pointer;" id="'.$id.'" onclick="javascript: fn_select_combounidad('.$unidad_id.',\''.$valor.'\',\'correspondencia_receptor_unidad\');">'.$valor.'</div>';
                        }
                    }}
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_funcionario_receptor">
        <div>
            <label for="correspondencia[receptor][funcionario_id]">Funcionario</label>
            <div class="content">
                <div id="div_receptores_funcionarios">
                    <select name="correspondencia[receptor][funcionario_id]" id="correspondencia_receptor_funcionario_id">
                        <option value=""></option>
                    </select>
                    <input type="hidden" name="val_funcionario_interno" />
                </div>

                <div>
                    <br/>
                    <table id="grilla" class="lista">
                        <tbody>
                            <?php
                                if (isset($correspondencia['receptor'])) {
                                if (isset($correspondencia['receptor']['copias'])) {
                                    $receptores = $correspondencia['receptor']['copias'];

                                    foreach ($receptores as $receptor)
                                    {
                                        list($unidad_id,$funcionario_id,$copia) = explode ('#',$receptor);
                                        $receptor = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoFuncionario($unidad_id,$funcionario_id);
                                        
                                        if(count($receptor)>0){
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

                                        if($copia=='S'){
                                            $cadena .= "<font class='f16b azul'>&nbsp;CC</font>";
                                        }

                                        $cadena .= "<input name='correspondencia[receptor][copias][]' type='hidden' value='".$unidad_id."#".$funcionario_id."#".$copia."'/>" . "</td>";
                                        $cadena .= "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                        $cadena .= "</tr>";
                                        echo $cadena;
                                        }
                                    }
                                }}
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="val_receptor_interno_repetido" />
                </div>
            </div>
        </div>
    </div>

</fieldset>

<div id="CargarGrupo">
    
</div>
<?php use_helper('I18N', 'Date') ?>
<?php include_partial('grupos/assets') ?>

<?php use_helper('jQuery') ?>

<?php $nombre_fun= Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($correspondencia_funcionario_unidad->getFuncionarioId()); ?>

<script>
    $(document).ready(function(){
        document.getElementById("correspondencia_funcionario_unidad_firmar").setAttribute("onChange","javascript:conmutar_vistobueno();");
        fn_dar_eliminar();

        if ($('input[id=correspondencia_funcionario_unidad_firmar]').attr('checked')) {
                $("#vistos_bueno_ruta").show();
            }

        if ($('input[id=correspondencia_funcionario_unidad_firmar_alguno]').attr('checked')) {
            conmutar_opcion();
            }
    });

    function conmutar_vistobueno(){
        if ($('input[id=correspondencia_funcionario_unidad_firmar]').attr('checked') && $('#correspondencia_funcionario_unidad_funcionario_id').val() != ''){
            $("#vistos_bueno_ruta").slideDown();
            document.getElementById("correspondencia_funcionario_unidad_firmar_ninguno").setAttribute("checked","ckecked");
        }else{
            $("#vistos_bueno_ruta").slideUp();
        }
    };

    function conmutar_opcion(){
        if ($('input[id=correspondencia_funcionario_unidad_firmar_ninguno]').attr('checked')){
            $("#ninguno_div").show();
            $("#alguno_div").hide();
        }
        if ($('input[id=correspondencia_funcionario_unidad_firmar_alguno]').attr('checked')){
            $("#alguno_div").show();
            $("#ninguno_div").hide();
            cadena = "<thead><tr><th>Orden</th><th>Funcionario</th><th>Funci&oacute;n</th><th>Acci&oacute;n</th></tr></thead>";
            cadena = cadena + "<tbody><tr>";
            cadena = cadena + "<td class='index' style='font-weight: bolder; font-size: 20px; text-align: center; vertical-align: middle'>1</td>";
            cadena = cadena + "<td style='color: #666;'>" + '<?php echo $nombre_fun[0]['fnombre'].' '.$nombre_fun[0]['fapellido'] ?>' + "</td>";
            cadena = cadena + "<td style='color: #666; text-align: center; vertical-align: middle'>Firma final</td>";
            cadena = cadena + "</tr></tbody>";
        }
    };

    function fn_counter_order(){
        count= $('#table_vistobuenos >tbody >tr').length;
        $('#table_vistobuenos > tbody > tr').each(function() {
            $(this).children('td:first').html(count);
            count= parseInt(count) -1;
        });
    }

    function fn_dar_eliminar(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                  $(this).remove();
                  fn_counter_order();
                });
        });
    };

    function show_select_funcionario(){
        $("#div_unidad_funcionario").show();
    }

    function fn_agregar_vistobueno(){
        order= $('#table_vistobuenos >tbody >tr').length;
        cadena = "<tr>";
        cadena = cadena + "<td class='index' style='font-weight: bolder; font-size: 20px; text-align: center; vertical-align: middle'>1</td>";
        cadena = cadena + "<td>" + jQuery.trim($("#vistobueno_unidad option:selected").text()) + "<br/>" + jQuery.trim($("#vistobueno_funcionario option:selected").text()) + "</td>";
        cadena = cadena + "<td style='text-align: center; vertical-align: middle'>Visto bueno</td>";
        cadena = cadena + "<td style='text-align: center; vertical-align: middle'><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
        cadena = cadena + "<input type='hidden' name='funcionarios_vb[]' value='" + $("#vistobueno_funcionario option:selected").val() + "#A'/>";
        cadena = cadena + "</tr>";
        $("#table_vistobuenos > tbody").append(cadena);
        fn_dar_eliminar();

        <?php echo jq_remote_function(array('update' => 'div_vistobueno_funcionario',
                                                        'url' => 'grupos/vistobuenoUnidades',
                                                        'with' => "'unidad_id= 0'",)); ?>

        document.getElementById("vistobueno_unidad").selectedIndex = "";

        $("#div_unidad_funcionario").hide();

        fn_counter_order();
    }
</script>

<div id="sf_admin_container">
  <h1><?php echo __('Edición de permiso del funcionario: %%funcionario_id%%', array('%%funcionario_id%%' => $nombre_fun[0]['fnombre'].' '.$nombre_fun[0]['fapellido']), 'messages') ?></h1>

  <?php include_partial('grupos/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('grupos/form_header', array('correspondencia_funcionario_unidad' => $correspondencia_funcionario_unidad, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('grupos/form', array('correspondencia_funcionario_unidad' => $correspondencia_funcionario_unidad, 'nombre' => $nombre_fun,'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('grupos/form_footer', array('correspondencia_funcionario_unidad' => $correspondencia_funcionario_unidad, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>
</div>
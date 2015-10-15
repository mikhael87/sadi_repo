<!--<link rel="stylesheet" type="text/css" media="screen" href="/js/docsupport/style.css" />-->
<!--<link rel="stylesheet" type="text/css" media="screen" href="/js/docsupport/style.css" />-->
<link rel="stylesheet" type="text/css" media="screen" href="/css/chosen.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/jquery.stepper.css"/>
<script type="text/javascript" src="/js/chosen.jquery.js"></script>
<script type="text/javascript" src="/js/docsupport/prism.js"></script>
<script type="text/javascript" src="/js/jquery.stepper.js"></script>

<div>
<?php
echo '<input type="hidden" name="tipo[op]" value="'.$tipo_juego.$tipo_marcador.'">';

if($tipo_juego == 'V') : ?>
    <table>
        <tr style="border: none">
            <td style="border: none">
                <select name="tipo[equipos][0][id]" data-placeholder="Seleccione un equipo..." class="chosen-select" style="width:350px;" tabindex="2">
                    <option value=""></option>
                    <?php
                    foreach($empresas as $value) {
                        echo '<option value="'.$value->getRegistro().'" ';
                        if($registros != '') {
                            if(isset($registros[0])) {
                                if($registros[0]['registro_id'] == $value->getRegistro()) {
                                    echo 'selected';
                                }
                            }
                        }
                        echo '>'.$value->getNombre().'</option>';
                    }
                    ?>
                </select>
            </td>
            <td style="border: none"><font style="font-size: 21px; font-weight: bold">Vs.</font></td>
            <td style="border: none">
                <select name="tipo[equipos][1][id]" data-placeholder="Seleccione un equipo..." class="chosen-select" style="width:350px;" tabindex="2">
                    <option value=""></option>
                    <?php
                    foreach($empresas as $value) {
                        echo '<option value="'.$value->getRegistro().' "';
                        if($registros != '') {
                            if(isset($registros[1])) {
                                if($registros[1]['registro_id'] == $value->getRegistro()) {
                                    echo 'selected';
                                }
                            }
                        }
                        echo '>'.$value->getNombre().'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <?php if($tipo_marcador == 'S') { ?>
        <tr style="border: none"><td  colspan="3" style="text-align: center; border: none"><font style="font-size: 14px; color: #666; font-weight: bold">¿Quien gano el encuentro?</font></td></tr>
        <tr style="border: none">
            <td style="text-align: right; border: none"><input type="radio" name="tipo[equipos][0][marcador]" value="0" <?php if($registros != '') {if(isset($registros[0])) {if($registros[0]['marcador'] == 1) {echo 'checked';}}}else {echo 'checked';} ?>/></td>
            <td style="border: none"></td>
            <td style="text-align: left; border: none"><input type="radio" name="tipo[equipos][0][marcador]" value="1" <?php if($registros != '') {if(isset($registros[1])) {if($registros[1]['marcador'] == 1) {echo 'checked';}}} ?>/></td>
        </tr>
        <?php }elseif($tipo_marcador == 'P') { ?>
        <tr style="border: none"><td colspan="3" style="text-align: center; border: none"><font style="font-size: 14px; color: #666; font-weight: bold">Marque el puntaje</font></td></tr>
        <tr style="border: none">
            <td style="text-align: right; border: none"><input type="text" id="stepperA" size="2" name="tipo[equipos][0][marcador]" value="<?php if($registros != '') {if(isset($registros[0])) {echo $registros[0]['marcador'];}else{echo '0';}}else{echo '0';} ?>"/></td>
            <td style="border: none"></td>
            <td style="text-align: left; border: none"><input type="text" id="stepperB" size="2" name="tipo[equipos][1][marcador]" value="<?php if($registros != '') {if(isset($registros[1])) {echo $registros[1]['marcador'];}else{echo '0';}}else{echo '0';} ?>"/></td>
        </tr>
        <?php }else { ?>
        <tr style="border: none"><td colspan="3" style="text-align: center; border: none"><font style="font-size: 14px; color: #666; font-weight: bold">Indique posiciones</font></td></tr>
        <tr style="border: none">
            <td style="text-align: right; border: none">
                <font id="posi_a" style="font-size: 35px; font-weight: bold">1</font>°
                <input type="hidden" id="id_equipo_a" name="marcador_equipo_a"/>
            </td>
            <td style="border: none"><img src="/images/icon/reset.jpg" style="cursor: pointer" onClick="javascript: alter_position(); return false;"/></td>
            <td style="text-align: left; border: none">
                <font id="posi_b" style="font-size: 35px; font-weight: bold">2</font>°
                <input type="hidden" id="id_equipo_b" name="marcador_equipo_b"/>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php else : ?>
    <table>
        <tr style="border: none"><td colspan="3" style="text-align: center; border: none"><font style="font-size: 14px; color: #666; font-weight: bold">Indique equipos y posiciones</font></td></tr>
        <tr style="border: none">
            <td style="border: none" id="positions">
                    <?php if($registros == ''): ?>
                        <div id="div_pos_1">
                            <x class="pos" id="pos_1"><font style="font-size: 22px; font-weight: bold; vertical-align: middle">1°</font>&nbsp;&nbsp;</x>
                            <select name="tipo[equipos][1][id]" data-placeholder="Seleccione un equipo..." class="team-select chosen-select" style="width:350px;" tabindex="2">
                                <option value=""></option>
                                <?php
                                foreach($empresas as $value) {
                                    echo '<option value="'.$value->getRegistro().'">'.$value->getNombre().'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    <?php else:
                        $pot= 1;
                        foreach($registros as $registro) { ?>
                            <div id="div_pos_<?php echo $pot; ?>">
                                <x class="pos" id="pos_<?php echo $pot; ?>"><font style="font-size: 22px; font-weight: bold; vertical-align: middle"><?php echo $pot; ?>°</font>&nbsp;&nbsp;</x>
                                <select name="tipo[equipos][<?php echo $pot; ?>][id]" data-placeholder="Seleccione un equipo..." class="team-select chosen-select" style="width:350px;" tabindex="2">
                                    <option value=""></option>
                                    <?php
                                    foreach($empresas as $value) {
                                        echo '<option value="'.$value->getRegistro().'" '.(($registro->getRegistroId() == $value->getRegistro())? "selected":"").'>'.$value->getNombre().'</option>';
                                    }
                                    ?>
                                </select>
                                <a id="<?php echo $pot; ?>" href="#" onclick="javascript: delete_team(this); return false;"><img src="/images/icon/delete.png" style=" vertical-align: middle"/></a><br/>
                            </div>
                        <?php $pot++; }
                    endif; ?>
            </td>
        </tr>
        <tr style="border: none">
            <td style="border: none">
                <a href="#" id="add_link" onclick="javascript: add_team(); return false;"><img src="/images/icon/new.png"/>&nbsp;Agregar otro equipo</a>
            </td>
        </tr>
    </table>
<?php endif;
//TIPOS DE JUEGO MULTIPLES CON MARCADORES POR ORDEN
?>
</div>

<script type="text/javascript">
    $('#stepperA').stepper({ limit: [0, 80] });
    $('#stepperB').stepper({ limit: [0, 80] });
    
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, no se encontro!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
    
    function alter_position() {
        var a= $('#posi_a').html();
        var b= $('#posi_b').html();
        
        $('#posi_a').html(b);
        $('#posi_b').html(a);
        
        $('#id_equipo_a').val(b);
        $('#id_equipo_b').val(a);
    }
    
    function add_team() {
        var pos= (parseInt($('.pos').length) + parseInt(1));
        var disc_v= $('#variante_select').val();
     
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>juego/addTeam',
            type:'POST',
            dataType:'html',
            data: 'pos='+pos+'&disc_v='+disc_v,
            success:function(data, textStatus){
                $('#positions').append(data);
            }});
    }
    
    function delete_team(pos) {
        $('#div_pos_'+pos.id).remove();
        reorder();
        checkId();
    }
    
    function repeatId(id) {
        var max= 0;
        $('.pos').each(function() {
            if(id === $(this).next('select').val()) {
                max++;
            }
        });
        return max;
    }
    
    function checkId() {
        var repeat= false;
        $('.pos').each(function() {
            var max= repeatId($(this).next('select').val());
            
            if(max > 1) {
                repeat= true;
            }
        });
        
        if(repeat) {
            $('.sf_admin_action_save').children().attr('disabled','disabled');
            $('.sf_admin_action_save_and_add').children().attr('disabled','disabled');
            alert('Por favor, verifica los equipos, hay uno o más repetidos');
        }else {
            $('.sf_admin_action_save').children().attr('disabled',false);
            $('.sf_admin_action_save_and_add').children().attr('disabled',false);
        }
    }
    
    function reorder() {
        var pos= 1;
        $('.pos').each(function() {
            $(this).children().html(pos+'°');
            var strx= $(this).parent().find("select").attr("name");
            strx= strx.replace(strx, 'tipo[equipos]['+pos+'][id]');
            $(this).parent().find("select").attr("name", strx);
            pos++;
        });
    }
    
    $(document).ready(function (){
        $('.team-select').change(function() {
            checkId();
        });
    });
</script>
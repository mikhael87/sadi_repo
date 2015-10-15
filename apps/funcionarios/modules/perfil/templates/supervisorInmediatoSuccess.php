<script>
    function carga_fun(val) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/funcionarioUnidad',
            type:'POST',
            dataType:'html',
            data: 'u_id='+val,
            beforeSend: function(Obj){
                $('#funidad').html('<img src="/images/icon/cargando.gif" />&nbsp;<font style="color: #666; font-size: 12px">Cargando funcionarios...</font>');
            },
            success:function(data, textStatus){
                $('#funidad').html(data);
                $('#submit_div').show();
            }});
    }
</script>
<?php
$session_cargos = $sf_user->getAttribute('session_cargos');
$datos_ar= array();
$i= 0;
foreach ($session_cargos as $session_cargo) {
    $datos_cargo_unidad= Doctrine::getTable('Organigrama_Unidad')->datosCargoUnidad($session_cargo['cargo_id']);
    foreach($datos_cargo_unidad as $values) {
        $datos_ar[$i]['cargo_id']= $values['cargo_id'];
        $datos_ar[$i]['cargo_nombre']= $values['cargo_nombre'];
        $datos_ar[$i]['unidad_nombre']= $values['unidad_nombre'];
        $i++;
    }
} ?>
<h1>Supervisor Inmediato</h1>

<?php echo link_to(image_tag('icon/back.png', array('style' => 'vertical-align: middle')).' Regresar al perfil', sfConfig::get('sf_app_funcionarios_url').'perfil', array('style'=> 'text-decoration: none')); ?>
<br/><br/>
<font style="color: #666">
Seleccione la unidad espec&iacute;fica donde labora, luego selecciones su supervisor inmediato.<br/>Si pertecene a una coordinaci&oacute;n deber&aacute; seleccionar al coordinador de dicha unidad.
</font><br/><br/>

<form id="formactu2" name="formactu2" method="post" action="<?php echo sfConfig::get('sf_app_organigrama_url'); ?>cargo/actualizarInformacionInicialLaboral">
    <?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>
    <input type='hidden' name='perfil' value='TRUE' />
    <?php if(count($datos_ar) > 1) { ?>
        <img src='/images/icon/info.png' style='vertical-align: middle' />&nbsp;<font style='font-size: 14px'>Usted posee mas de un cargo asociado, por favor seleccione el cargo para modificar su supervisor inmediato.</font>
        <br/><br/>
        <select name="datos_iniciales_laborales[cargo_id]">
            <?php foreach($datos_ar as $value) {
                echo '<option value="'. $value['cargo_id'] .'">'. preg_replace("/\((.*?)\)/", "", $value['cargo_nombre']) .' - '. $value['unidad_nombre'] .'</option>';
            } ?>
        </select><br/><br/>
    <?php } else {
        foreach($datos_ar as $value) {
            echo '<input type="hidden" name="datos_iniciales_laborales[cargo_id]" value="'. $value['cargo_id'] .'"/>';
        }
    } ?>
    <select name="datos_iniciales_laborales[unidad_id]" id="unidad_id" onchange="carga_fun(this.value)">

            <option value=""></option>

        <?php foreach( $unidades as $clave=>$valor ) { ?>
            <option value="<?php echo $clave; ?>">
                <?php echo $valor; ?>
            </option>
        <?php } ?>
    </select>
    <br/><br/>
    <div id="funidad"></div>
    <br/><br/>
    <div id="submit_div" style="display: none">
        <input id="submit" name="submit" type = "submit" value="Actualizar"/>
    </div>
</form>

                    
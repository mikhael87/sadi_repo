<?php
$value_nombre='';
if($sf_user->getAttribute('sms_reutilizado')){
    $mensajes_reutilizado = Doctrine::getTable('Public_Mensajes')->find($sf_user->getAttribute('sms_reutilizado'));

    if($mensajes_reutilizado->getNombreExterno()!='')
        $value_nombre = $mensajes_reutilizado->getNombreExterno();
}
$device= Sms::count_device();

$mis_modems= Sms::modems_per_user();
if (empty($mis_modems)) {
    $mis_modems = array(0 => '');
}
?>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_modem">
    <div>
        <label for="public_mensajes_masivos_modem"><?php
        if(count($device) > 1 || $device ==0) {
            if(count($mis_modems) > 1) {
                echo 'Dispositivo';
            }
        }?>
        </label>
        <div class="content">
            <?php
            $helpme='';
            if ($device== 'no_conn') {
                echo 'Sin conexión a Base de Datos Gammu...';
                $helpme= 'Debe configurar una base de datos gammu para continuar con el proceso.';
            }else {
                if ($device != 'no_conn' && $device != '') {
                    if(count($device) > 1) {
                        if(count($mis_modems)> 1 || $mis_modems[0] == 'all') {//Evaluar el funcionamiento de esta if
                            for ($i = 0; $i < count($device); $i++) {
                                if (in_array($device[$i]['id'], $mis_modems) || $mis_modems[0] == 'all') {
                                    ?><input type="radio" id="public_mensajes_masivos_modem" name="modem_masivos" value="<?php echo $device[$i]['id']; ?>"/><font style="vertical-align: top">&nbsp;<?php echo $device[$i]['id']; ?></font>&nbsp;&nbsp;&nbsp;<?php    
                                }
                            }
                            ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" id="public_mensajes_masivos_modem" name="modem_masivos" value="auto" checked/><font style="vertical-align: top">Autom&aacute;tico</font><?php
                            $helpme= 'Seleccione el dispositivo para este envio. Por defecto, se utilizará el dispositivo menos ocupado.';
                        }elseif(count($mis_modems)== 1 && $mis_modems[0] != 'all') {
                            //Tiene un solo modem asignado en ese caso, imprime un hidden con el valor SOLO SI ESTA ACTIVO
                            //si no es asi imprime un hidden con valor modem1 y mensaje
                            $count_apper= '';
                            for ($i = 0; $i < count($device); $i++) {
                                if (in_array($device[$i]['id'], $mis_modems)) {
                                    $count_apper= $device[$i]['id'];
                                }
                            }
                            if($count_apper== '') {
                                echo image_tag('icon/error.png', array('style'=> 'vertical-align: middle')).' No posee modems de envío asignados activos actualmente'
                                ?><input type="hidden" id="public_mensajes_masivos_modem" name="modem_masivos" value="modem1"/><?php
                                $helpme = 'Comuníquese con el administrador SIGLAS para asignación de modems válidos de envío.';
                            }else {
                                ?><input type="hidden" id="public_mensajes_masivos_modem" name="modem_masivos" value="<?php echo $count_apper; ?>"/><?php
                            }
                        }
                    }else {
                        if (in_array($device[0]['id'], $mis_modems) || $mis_modems[0] == 'all') {
                            echo "<input type='hidden' id='public_mensajes_masivos_modem' name='modem_masivos' value='". $device[0]['id'] ."' />";
                        }else {
                            echo image_tag('icon/error.png', array('style'=> 'vertical-align: middle')).' No posee modems de envío asignados activos actualmente';
                            echo "<input type='hidden' id='public_mensajes_masivos_modem' value='modem1' />";
                            $helpme = 'Comuníquese con el administrador SIGLAS para asignación de modems válidos de envío.';
                        }
                    }
                }elseif($device != 'no_conn') {
                    echo image_tag('icon/error.png', array('style'=> 'vertical-align: middle')).' No hay Dispositivos conectados...';
                    $helpme= 'Los mensajes se guardaran sin ser enviados<br/>Si es usted Administrador, revise la configuración Gammu.';
                    ?><input type="hidden" id="public_mensajes_masivos_modem" name="modem_masivos" value="modem1"/><?php
                }
            }
            ?>
        </div>
        <div class="help"><?php echo $helpme; ?></div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_prioridad">
    <div>
        <label for="public_mensajes_masivos_prioridad">Prioridad</label>
        <div class="content">
            <input type="radio" id="public_mensajes_masivos_prioridad" name="prioridad_masivos" value="2" checked/><font style="vertical-align: top">Normal</font>&nbsp;
            <input type="radio" id="public_mensajes_masivos_prioridad" name="prioridad_masivos" value="1"/><font style="vertical-align: top">Urgente</font>
        </div>

        <div class="help">Si selecciona "Urgente" sera lo proximo que en enviar antes que otros urgentes cargados anteriormente en la cola.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_nombre_externo">
    <div>
        <label for="public_mensajes_nombre_externo">Grupo</label>
        <div class="content"><input type="text" id="public_mensajes_nombre_externo" name="public_mensajes[nombre_externo]" value="<?php echo $value_nombre; ?>"></div>

        <div class="help">Identifique si desea el listado de destinatarios mediante un nombre.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_informes">
    <div>
        <label for="public_mensajes_n_informe_progreso">Informes</label>
        <div class="content">
            <input type="text" id="public_mensajes_n_informe_progreso" name="public_mensajes[n_informe_progreso]"/>
        </div>

        <div class="help">Escriba uno o varios números moviles (sin guiones, puntos o slash) separados por punto y coma (;) si desea recibir mensajes del progreso de envio.</div>
    </div>
</div>
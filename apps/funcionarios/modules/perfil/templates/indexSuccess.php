<?php
$session_usuario = $sf_user->getAttribute('session_usuario');
$months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
$f_nacimiento = $datosfuncionario_list[0]['fecha_nacimiento'];
$f_nacimiento = date('d', strtotime($f_nacimiento)).' de '. ucwords($months[(int)date('m', strtotime($f_nacimiento))]) .' de '. date('Y', strtotime($f_nacimiento));
?>

<style>
    #table_perfil {
        margin-top: 20px
    }
    
    #table_perfil td {
        padding: 5px;
    }
    
    .dato {
        font-size: 18px;
        font-weight: bold;
    }
    
    .label {
        color: #222222;
        font-size: 16px;
    }
    
    .link {
        font-size: 13px;
        font-weight: normal
    }
    
    .crosslines {
        position: absolute;
        top: 120px;
        left: 220px;
        z-index: 1
    }
</style>

<h1>MI PERFIL</h1>

<table id="table_perfil" style="width: 850px;">
    <tr class="trans_claro">
        <td class="label" style="width: 150px;">Nombres:</td>
        <td class="dato" style="width: 550px;"><?php echo $datosfuncionario_list[0]['primer_nombre'].' '.$datosfuncionario_list[0]['segundo_nombre'] ?></td>
        <?php
        if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$datosfuncionario_list[0]['ci'].'.jpg'))
            $route_personal= '/images/fotos_personal/'.$datosfuncionario_list[0]['ci'].'.jpg';
        else {
            $route_personal= '/images/other/siglas_photo_small_'.$datosfuncionario_list[0]['sexo'].substr($datosfuncionario_list[0]['ci'], -1).'.png';
        } ?>
        <td rowspan="4" class="trans" style="width: 150px;">
            <div class='crosslines'>
                <img src='/images/icon/crosslines.png' style='width: 550px; height: 80px; z-index: 1'/>
            </div>
            <div style="z-index: 2; position: relative; border: solid 2px #222222; box-shadow: 2px 2px 5px #222222">
                <div id="bright" style="position: absolute"><img src="/images/icon/foto_brillo.png"/>
                </div>
                <img src="<?php echo $route_personal; ?>" width="150"/>
            </div>
        </td>
    </tr>
    <tr class="trans_claro">
        <td class="label">Apellidos:</td>
        <td class="dato"><?php echo $datosfuncionario_list[0]['primer_apellido'].' '.$datosfuncionario_list[0]['segundo_apellido'] ?></td>
    </tr>
    <tr class="trans_claro">
        <td class="label">C&eacute;dula:</td>
        <td class="dato"><?php echo $datosfuncionario_list[0]['ci'] ?></td>
    </tr>
    <tr class="trans_claro">
        <td class="label">F. nacimiento:</td>
        <td class="dato"><?php echo $f_nacimiento ?></td>
    </tr>
    <tr class="trans_claro">
        <td class="label">Edad:</td>
        <td class="dato"><?php echo date('Y') - date('Y', strtotime($datosfuncionario_list[0]['fecha_nacimiento'])) ?></td>
        <td rowspan="5" valign='top' class="trans">
            <font style='font-size: 12px; color: #666'>&Uacute;ltima conexi&oacute;n:</font><br/>
            <font style='font-size: 11px'><?php echo date('d-m-Y g:i:s a', strtotime($session_usuario['ultima_conexion'])); ?></font><br/>
            <font style='font-size: 12px; color: #666'>Vis&iacute;tas:</font><br/>
            <font style='font-size: 11px'><?php echo $session_usuario['visitas']; ?></font><br/>
            <div style='position: absolute'><br/>
                <?php 
                $i= 0;
                foreach ($datosfuncionario_list as $datosfuncionario) {
                    echo '<font style="color: #383838; font-size: 11px">Cargo:</font> '.$datosfuncionario->getCtnombre().'<br/>';
                    echo '<font style="color: #383838; font-size: 11px">Unidad:</font> '.$datosfuncionario->getUnombre().'<br/>';
                    $supervisor_datos= Doctrine::getTable('Funcionarios_Funcionario')->busquedaSupervisor($datosfuncionario->getPadreCargoId());
                    echo '<font style="color: #383838; font-size: 11px">Supervisor:</font> '. $supervisor_datos[0]['primer_nombre'] .'&nbsp;'. $supervisor_datos[0]['primer_apellido'] .'<br/>';
                    $i++;
                    echo (count($datosfuncionario_list) > $i) ? '<hr/>' : '';
                } ?>
            </div>
        </td>
    </tr>
<!--    <tr>
        <td>Edo. de nacimiento:</td>
        <td></td>
        <td></td>
    </tr>-->
    <tr class="trans_claro">
        <td class="label">Sexo:</td>
        <td class="dato"><?php echo (($datosfuncionario_list[0]['sexo']== 'M')? 'Masculino' : 'Femenino') ?></td>
    </tr>
    <tr class="trans_claro">
        <td class="label">Estado civil</td>
        <td class="dato"><?php switch ($datosfuncionario_list[0]['edo_civil']) {
                case 'S':
                    echo 'Soltero';
                    break;
                case 'C':
                    echo 'Casado';
                    break;
                case 'D':
                    echo 'Divorsiado';
                    break;
                case 'V':
                    echo 'Viudo';
                    break;
            } ?></td>
    </tr>
    <tr class="trans_claro">
        <td class="label">Tel&eacute;fono movil:</td>
        <td class="dato"><?php echo $datosfuncionario_list[0]['telf_movil'] ?></td>
    </tr>
    <tr class="trans_claro">
        <td class="label">@mail:</td>
        <td class="dato"><?php echo $datosfuncionario_list[0]['email_personal'] ?></td>
    </tr>
    <tr>
        <td colspan="4"><hr/></td>
    </tr>
    <tr class="trans">
        <td class='link' colspan="3"><a href='<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>perfil/supervisorInmediato'>Cambio de supervisor inmediato</a></td>
    </tr>
    <tr class="trans">
        <td class='link' colspan="3"><a href='<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/cambioClave?user=<?php echo $session_usuario['usuario_nombre']; ?>'>Cambio de contrase&ntilde;a</a></td>
    </tr>
<!--    <tr class="trans">
        <td class='link' colspan="3"><a href='<?php // echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha'>Modificar mis datos b&aacute;sicos</a></td>
    </tr>-->
    <tr class="trans">
        <td class='link' colspan="3"><a href='<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo/coletilla?from=inicio'>Editar mi coletilla de firma</a></td>
    </tr>
    
    <tr class="trans">
        <td colspan="3">
            <div style="position: relative;">
              <a href="#" onclick="$('#menu_expira_perfil').toggle(); return false;">Cambiar el tiempo maximo de sesion inactiva</a>
              <div id="menu_expira_perfil" style="display: none; width:100px; position: absolute; top: -10px; left: 300px; background-color: #F5F5F5; border: solid 1px; text-align: justify; padding: 10px;">
                  <select onchange="cambiar_tiempo_expira($(this).val()); $('#menu_expira_perfil').toggle();">
                      <option value="6" <?php if($sf_user->getAttribute('sf_session_expira') == 6) echo "selected"; ?>>6 minutos</option>
                      <option value="10" <?php if($sf_user->getAttribute('sf_session_expira') == 10) echo "selected"; ?>>10 minutos</option>
                      <option value="20" <?php if($sf_user->getAttribute('sf_session_expira') == 20) echo "selected"; ?>>20 minutos</option>
                      <option value="30" <?php if($sf_user->getAttribute('sf_session_expira') == 30) echo "selected"; ?>>30 minutos</option>
                      <option value="60" <?php if($sf_user->getAttribute('sf_session_expira') == 60) echo "selected"; ?>>1 hora</option>
                      <option value="120" <?php if($sf_user->getAttribute('sf_session_expira') == 120) echo "selected"; ?>>2 horas</option>
                      <option value="300" <?php if($sf_user->getAttribute('sf_session_expira') == 300) echo "selected"; ?>>5 horas</option>
                      <option value="100000" <?php if($sf_user->getAttribute('sf_session_expira') == 100000) echo "selected"; ?>>No expirar</option>
                  </select>
              </div>
            </div>
        </td>
    </tr>
</table>
<?php

class funcionario_cargoNotify
{
  public function notifyDeskCorrespondencia($funcionario_afectado, $funcionario_autor)
  {
    $migrupo= Doctrine::getTable('Correspondencia_FuncionarioUnidad')->findByFuncionarioIdAndStatus($funcionario_afectado,'A');
    $count='0';
    $ids= array(0);
    //arreglo con id de grupos
    foreach($migrupo as $value){
        if($value->getAutorizadaUnidadId() != sfContext::getInstance()->getUser()->getAttribute('funcionario_unidad_id')) {
            $ids[] = $value->getAutorizadaUnidadId();
        }
        $count++;
    }

    if($count > 0) {
        $unidad_autoridad= Doctrine::getTable('Organigrama_Cargo')->autoridadesPorUnidad($ids);
    }

    foreach($unidad_autoridad as $value) {
        $html= null;

        $datos_funcio_afectado= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($funcionario_afectado);

        $datos_funcio_autor= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($funcionario_autor);

        if(count($datos_funcio_afectado) > 0 && count($datos_funcio_autor) > 0) {
            if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$datos_funcio_afectado[0]['ci'].'.jpg'))
                $route_personal= '/images/fotos_personal/'.$datos_funcio_afectado[0]['ci'].'.jpg';
            else {
                $route_personal= '/images/other/siglas_photo_small_'.$datos_funcio_afectado[0]['sexo'].substr($datos_funcio_afectado[0]['ci'], -1).'.png';
            }

            $registro_funcionario_unidad_id= Doctrine::getTable('Correspondencia_FuncionarioUnidad')->findOneByFuncionarioIdAndAutorizadaUnidadIdAndStatus($datos_funcio_afectado[0]['id'], $value['oc_unidad'],'A');

            if(count($registro_funcionario_unidad_id) > 1) {
                $html= '
                        <table width="100%">
                            <tr>
                                <td rowspan="3" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' . $route_personal . '" width="60"/></td>
                                <td class="f11n">' . date('d-m-Y h:i A', strtotime($datos_funcio_afectado[0]['created_at'])) . '</td>
                            </tr>
                            <tr>
                                <td>El funcionario <b>' . $datos_funcio_afectado[0]['primer_nombre'].' '.$datos_funcio_afectado[0]['primer_apellido'] . '</b> ha sido movido de <b>Unidad</b></td>
                            </tr>
                            <tr>
                                <td class="f14n">
                                    <div id="sf_admin_container" style="width: 230px; float: left">
                                        <div class="partial_find_eliminar partial" style="height: 20px;">
                                            <a href="javascript: group_correspondencia_eliminar(\''. $registro_funcionario_unidad_id->getId() .'\', \'$#$\', \'eventos\')" onclick="return confirm(\'Se eliminaran permisos de este funcionario sobre su Grupo. Esta usted seguro?\')">Eliminar de mi Grupo (correspondencia)</a>
                                        </div>
                                        <div class="partial_find_permitir partial" style="height: 20px;">
                                            <a href="javascript: borrar_individual(\'$#$\', \'eventos\')">Permitir en mi Grupo (correspondencia)</a>
                                        </div>
                                    </div>
                                    <div style="float: left; max-width: 490px; border: 1px dashed #bc6f00; padding: 3px">
                                        <font style="font-size: 10px; color: #683e02">Funcionario movido a la unidad <b>' . $datos_funcio_afectado[0]['unombre'] . '</b> por <b>' . $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'].'('.$datos_funcio_autor[0]['ctnombre'].')' . '</b>. Puede editar los privilegios de este Usuario desde la opci&oacute;n <a href="' . sfConfig::get('sf_app_correspondencia_url') . 'grupos">Permisos del Grupo de Correspondencia</a></font>
                                    </div>
                                </td>
                            </tr>
                    </table>';
                }
        }

        if($html) {
            $comunicaciones_notificacion = new Comunicaciones_Notificacion();
            $comunicaciones_notificacion->setFuncionarioId($value['funcionario_id']);
            $comunicaciones_notificacion->setAplicacionId(6); //funcionarios
            $comunicaciones_notificacion->setFormaEntrega('desk');
            $comunicaciones_notificacion->setMetodoId(3); //cambio de unidad
            $comunicaciones_notificacion->setFEntrega(date('Y-m-d H:i:s'));
            $comunicaciones_notificacion->setMensaje($html);
            $comunicaciones_notificacion->setStatus('A');

            $comunicaciones_notificacion->save();

            $comunicaciones_notificacion->setMensaje(str_replace('$#$', $comunicaciones_notificacion->getId(), $comunicaciones_notificacion->getMensaje($html)));

            $comunicaciones_notificacion->save();
        }
    }
  }
  
  public function notifyDeskArchivo($funcionario_afectado, $funcionario_autor)
  {
    $migrupo= Doctrine::getTable('Archivo_FuncionarioUnidad')->findByFuncionarioIdAndStatus($funcionario_afectado,'A');
    $count='0';
    $ids= array(0);
    //arreglo con id de grupos
    foreach($migrupo as $value){
        if($value->getAutorizadaUnidadId() != sfContext::getInstance()->getUser()->getAttribute('funcionario_unidad_id')) {
            $ids[] = $value->getAutorizadaUnidadId();
        }
        $count++;
    }

    if($count > 0) {
        $unidad_autoridad= Doctrine::getTable('Organigrama_Cargo')->autoridadesPorUnidad($ids);
    }

    foreach($unidad_autoridad as $value) {
        $html= null;

        $datos_funcio_afectado= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($funcionario_afectado);

        $datos_funcio_autor= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($funcionario_autor);

        if(count($datos_funcio_afectado) > 0 && count($datos_funcio_autor) > 0) {
            if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$datos_funcio_afectado[0]['ci'].'.jpg'))
                $route_personal= '/images/fotos_personal/'.$datos_funcio_afectado[0]['ci'].'.jpg';
            else {
                $route_personal= '/images/other/siglas_photo_small_'.$datos_funcio_afectado[0]['sexo'].substr($datos_funcio_afectado[0]['ci'], -1).'.png';
            }

            $registro_funcionario_unidad_id= Doctrine::getTable('Archivo_FuncionarioUnidad')->findOneByFuncionarioIdAndAutorizadaUnidadIdAndStatus($datos_funcio_afectado[0]['id'], $value['oc_unidad'],'A');

            if(count($registro_funcionario_unidad_id) > 1) {
                $html= '
                        <table width="100%">
                            <tr>
                                <td rowspan="3" width="65"><img src="' . $route_personal . '" width="60"/></td>
                                <td class="f11n">' . date('d-m-Y h:i A', strtotime($datos_funcio_afectado[0]['created_at'])) . '</td>
                            </tr>
                            <tr>
                                <td>El funcionario <b>' . $datos_funcio_afectado[0]['primer_nombre'].' '.$datos_funcio_afectado[0]['primer_apellido'] . '</b> ha sido movido de <b>Unidad</b></td>
                            </tr>
                            <tr>
                                <td class="f14n">
                                    <div id="sf_admin_container" style="width: 230px; float: left">
                                        <div class="partial_find_eliminar partial" style="height: 20px;">
                                            <a href="javascript: group_archivo_eliminar(\''. $registro_funcionario_unidad_id->getId() .'\', \'$#$\', \'eventos\')" onclick="return confirm(\'Se eliminaran permisos de este funcionario sobre su Grupo. Esta usted seguro?\')">Eliminar de mi Grupo (archivo)</a>
                                        </div>
                                        <div class="partial_find_permitir partial" style="height: 20px;">
                                            <a href="javascript: borrar_individual(\'$#$\', \'eventos\')">Permitir en mi Grupo (archivo)</a>
                                        </div>
                                    </div>
                                    <div style="float: left; max-width: 490px; border: 1px dashed #bc6f00; padding: 3px">
                                        <font style="font-size: 10px; color: #683e02">Funcionario movido a la unidad <b>' . $datos_funcio_afectado[0]['unombre'] . '</b> por <b>' . $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'].'('.$datos_funcio_autor[0]['ctnombre'].')' . '</b>. Puede editar los privilegios de este Usuario desde la opci&oacute;n <a href="' . sfConfig::get('sf_app_archivo_url') . 'grupos">Permisos del Grupo de Archivo</a></font>
                                    </div>
                                </td>
                            </tr>
                    </table>';
                }
        }

        if($html) {
            $comunicaciones_notificacion = new Comunicaciones_Notificacion();
            $comunicaciones_notificacion->setFuncionarioId($value['funcionario_id']);
            $comunicaciones_notificacion->setAplicacionId(6); //funcionarios
            $comunicaciones_notificacion->setFormaEntrega('desk');
            $comunicaciones_notificacion->setMetodoId(3); //cambio de unidad
            $comunicaciones_notificacion->setFEntrega(date('Y-m-d H:i:s'));
            $comunicaciones_notificacion->setMensaje($html);
            $comunicaciones_notificacion->setStatus('A');

            $comunicaciones_notificacion->save();

            $comunicaciones_notificacion->setMensaje(str_replace('$#$', $comunicaciones_notificacion->getId(), $comunicaciones_notificacion->getMensaje($html)));

            $comunicaciones_notificacion->save();
        }
    }
  }

  public function notifySms()
  {
      //Notificiones con formato para envio de sms de texto
  }

  public function notifyEmail()
  {
      //Notificiones con formato para envio de correo electronico
  }
}
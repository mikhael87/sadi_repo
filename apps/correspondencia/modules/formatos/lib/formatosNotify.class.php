<?php

class formatosNotify
{
  public function notifyDesk($funcionario_afectado, $funcionario_autor, $n_correspondencia_emisor, $id_correspondencia, $tipo)
  {
    //PARAMETRO TIPO "NORMAL": NOTIFICACION DE EDICION DE DOC AL CREDOR
    //PARAMETRO TIPO "VISTOBUENO": NOTIFICACION PARA LOS QUE HAN DADO VISTO BUENO Y TRAS ESTA EDICION YA NO VERAN EL DOC
    //PARAMETRO ID_CORRESPONDENCIA UTILIZADO PARA FUTURAS COMPARACIONES Y ANIDACIONES DE NOTIFICACIONES
    $html= null;

    $datos_funcio_autor= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($funcionario_autor);

    if(count($datos_funcio_autor) > 0) {
        if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$datos_funcio_autor[0]['ci'].'.jpg'))
            $route_personal= '/images/fotos_personal/'.$datos_funcio_autor[0]['ci'].'.jpg';
        else {
            $route_personal= '/images/other/siglas_photo_small_'.$datos_funcio_autor[0]['sexo'].substr($datos_funcio_autor[0]['ci'], -1).'.png';
        }

        if($tipo== 'normal') {
            $html= '
                <table width="100%">
                    <tr>
                        <td rowspan="4" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' .  $route_personal  . '" width="60"/></td>
                        <td class="f11n">Edici&oacute;n de correspondencia</td>
                    </tr>
                    <tr>
                        <td>El funcionario <b>' .  $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'] .' (' . $datos_funcio_autor[0]['ctnombre'] . ') ' . '</b> de <b>' .  $datos_funcio_autor[0]['unombre']  . '</b></td>
                    </tr>
                    <tr>
                        <td>ha editado su correspondencia N° <b>' .  $n_correspondencia_emisor . '</b>, #string_to_replace#</td>
                    </tr>
                </table>';
        }else {
            $html= '
                <table width="100%">
                    <tr>
                        <td rowspan="5" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' .  $route_personal  . '" width="60"/></td>
                        <td class="f11n">Edici&oacute;n de correspondencia</td>
                    </tr>
                    <tr>
                        <td>Su Visto bueno sobre la correspondencia <b>'. $n_correspondencia_emisor .'</b> ha sido revertido,</td>
                    </tr>
                    <tr>
                        <td>a menos que sea el primero en ruta de visto bueno, no podr&aacute ver el documento, hasta que los anteriores visto buenos sean aprobados.</td>
                    </tr>
                    <tr>
                        <td style="font-size: 10px; color: #666">Documento editado por el funcionario <b>' .  $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'] .' (' . $datos_funcio_autor[0]['ctnombre'] . ') ' . '</b> de <b>' .  $datos_funcio_autor[0]['unombre']  . '</b>, #string_to_replace#</td>
                    </tr>
                </table>';
        }
    }
    
    if($html) {
        $parametros_ar= Array('fecha' => strtotime ( '-2 minute' , strtotime ( date('Y-m-d H:i:s') ) ));
        $parametros = sfYAML::dump($parametros_ar);

        $comunicaciones_notificacion = new Comunicaciones_Notificacion();
        $comunicaciones_notificacion->setFuncionarioId($funcionario_afectado);
        $comunicaciones_notificacion->setAplicacionId(1); //correspondencia
        $comunicaciones_notificacion->setFormaEntrega('desk');
        $comunicaciones_notificacion->setMetodoId(7); //edicion de correspondencia
        $comunicaciones_notificacion->setFEntrega(date('Y-m-d H:i:s'));
        $comunicaciones_notificacion->setParametros($parametros);
        $comunicaciones_notificacion->setMensaje($html);
        $comunicaciones_notificacion->setStatus('A');

        $comunicaciones_notificacion->save();
    }
  }

  public function notifySms()
  {
      //Notificiones con formato para envio de sms de texto
  }

  public function notifyEmail($funcionario_afectado, $funcionario_autor, $n_correspondencia_emisor, $tipo)
  {
        $funcionario_datos = Doctrine::getTable('Funcionarios_Funcionario')->find($funcionario_afectado);

        if($funcionario_datos['email_institucional'] != '' || $funcionario_datos['email_personal'] != '')
        {
            $session_funcionario= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($funcionario_autor);

            $mensaje['mensaje'] = sfConfig::get('sf_organismo')."<br/>";
            $mensaje['mensaje'] .= "Sistema Integral para la Gestión Laboral, Administrativa y de Servicios (SIGLAS)<br/><br/><br/>";

            $mensaje['mensaje'] .= "Sr(a).-<br/>";
            $mensaje['mensaje'] .= "<b>" . $funcionario_datos['primer_nombre'] . " " . $funcionario_datos['primer_apellido'] . "</b><br/><br/><br/>";
            
            if($tipo== 'normal') {
                $mensaje['mensaje'] .= "Reciba un cordial saludo Bolivariano y Revolucionario, nos dirigimos a usted en la " .
                        "oportunidad de informarle que su correspondencia <b>\"" . $n_correspondencia_emisor . "\"</b> ";
                $mensaje['mensaje'] .= "ha sido editada por el funcionario \"" . $session_funcionario[0]['primer_nombre'] . " " . $session_funcionario[0]['primer_apellido'] . "\" hace menos de 5 minutos.<br/><br/> ";
            }else {
                $mensaje['mensaje'] .= "Reciba un cordial saludo Bolivariano y Revolucionario, nos dirigimos a usted en la " .
                        "oportunidad de informarle que su Visto bueno sobre la correspondencia <b>\"". $n_correspondencia_emisor ."\"</b> ha sido revertido, a menos que sea el primero en ruta de visto bueno, no podrá ver el documento, hasta que los anteriores visto buenos sean aprobados.<br/>";
                $mensaje['mensaje'] .= "Documento editado por el funcionario \"" . $session_funcionario[0]['primer_nombre'] . " " . $session_funcionario[0]['primer_apellido'] . "\" hace menos de 5 minutos.<br/><br/> ";
            }
            
            $mensaje['mensaje'] .= "<hr/><font style='font-size: 9px; color: #666'>Esta es una cuenta de correo no monitoriada. Por favor, no responda o reenv&iacute;e mensajes a esta direcci&oacute;n. Si tiene alguna duda con respecto a este servicio o desea hacer sugerencias contacte al administrador SIGLAS directamente.</font>";
            $mensaje['emisor'] = 'Correspondencia';
            $mensaje['receptor'] = $funcionario_datos['primer_nombre'] . " " . $funcionario_datos['primer_apellido'];

            $email= ($funcionario_datos['email_personal'] != '')?  $funcionario_datos['email_personal'] : $funcionario_datos['email_institucional'];

            Email::notificacion('correspondencia', $email, $mensaje, 'inmediata');
        }
  }
}
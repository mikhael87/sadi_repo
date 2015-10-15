<?php

class mensajesNotify
{
  public function notifyDesk($funcionario_receptor, $funcionario_autor, $fecha, $contenido, $mensaje_id)
  {
    $html= null;

    $datos_funcio_autor= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($funcionario_autor);

    if(count($datos_funcio_autor) > 0) {
        if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$datos_funcio_autor[0]['ci'].'.jpg')){
            $route_personal= '/images/fotos_personal/'.$datos_funcio_autor[0]['ci'].'.jpg';
        } else {
            $route_personal= '/images/other/siglas_photo_small_'.$datos_funcio_autor[0]['sexo'].substr($datos_funcio_autor[0]['ci'], -1).'.png';
        }

        $html= '
            <table width="100%">
                <tr>
                    <td rowspan="3" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' . $route_personal . '" width="60"/></td>
                    <td class="f11n">Enviado #string_to_replace#</td>
                </tr>
                <tr>
                    <td><b>'. $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'] .'</b></td>
                </tr>
                <tr>
                    <td class="f15b">
                        <img style="width: 10px; height: 10px; vertical-align: middle" src="/images/icon/resultset_next.png"  /><font style="color: #1b1b1b; font-weight: bold">'. $contenido .'</font>
                    </td>
                </tr>
        </table>';
    }

    if($html) {
        $parametros= Array('fecha' => strtotime ( $fecha ), 'mensaje_id' => $mensaje_id);
        $parametros = sfYAML::dump($parametros);

        $comunicaciones_notificacion = new Comunicaciones_Notificacion();
        $comunicaciones_notificacion->setFuncionarioId($funcionario_receptor);
        $comunicaciones_notificacion->setAplicacionId(8); //herramientas
        $comunicaciones_notificacion->setFormaEntrega('desk');
        $comunicaciones_notificacion->setMetodoId(8); //mensajes internos
        $comunicaciones_notificacion->setFEntrega(date('Y-m-d H:i:s'));
        $comunicaciones_notificacion->setParametros($parametros);
        $comunicaciones_notificacion->setMensaje($html);
        $comunicaciones_notificacion->setStatus('A');

        $comunicaciones_notificacion->save();
    }
  }

  public function notifySms($receptor_tlf, $mensaje)
  {
      Sms::notificacion('mensajes', $receptor_tlf, $mensaje, 'auto');
  }

  public function notifyEmail($receptor_mail, $mensaje)
  {
      Email::notificacion('mensajes', $receptor_mail, $mensaje, 'inmediata');
  }
}
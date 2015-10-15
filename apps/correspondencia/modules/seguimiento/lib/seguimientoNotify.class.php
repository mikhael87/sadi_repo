<?php

class seguimientoNotify
{
  public function notifyDesk($funcionario_autor, $funcionario_receptor_noti, $comment, $correspondencia_id)
  {
    $correspondencia_datos= Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);
      
    $datos_funcio_autor= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($funcionario_autor);

    if(count($datos_funcio_autor) > 0) {
        if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$datos_funcio_autor[0]['ci'].'.jpg'))
            $route_personal= '/images/fotos_personal/'.$datos_funcio_autor[0]['ci'].'.jpg';
        else {
            $route_personal= '/images/other/siglas_photo_small_'.$datos_funcio_autor[0]['sexo'].substr($datos_funcio_autor[0]['ci'], -1).'.png';
        }

        $html= '
                <table width="100%">
                    <tr>
                        <td rowspan="4" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' . $route_personal . '" width="60"/></td>
                        <td class="f11n">' . date('d-m-Y h:i a') . '</td>
                    </tr>
                    <tr>
                        <td>Han publicado un comentario en el <b>Seguimiento</b> de correspondencia N° <a href="' . sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_datos->getId().'/seguimiento'  . '" title="seguimiento">' . $correspondencia_datos->getNCorrespondenciaEmisor() . '</a></td>
                    </tr>
                    <tr>
                        <td class="f14b">
                            <img style="width: 10px; height: 10px; vertical-align: middle" src="/images/icon/resultset_next.png"  /><font style="color: #1b1b1b; font-weight: bold">'. $comment .'</font>
                        </td>
                    </tr>
                    <tr>
                        <td class="f14n">
                            Publicado por <b>' . $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'].'('.$datos_funcio_autor[0]['ctnombre'].')' . 'de la unidad ' . $datos_funcio_autor[0]['unombre'] . '</b>
                        </td>
                    </tr>
            </table>';
    }

    if($html) {
        $comunicaciones_notificacion = new Comunicaciones_Notificacion();
        $comunicaciones_notificacion->setFuncionarioId($funcionario_receptor_noti);
        $comunicaciones_notificacion->setAplicacionId(1); //correspondencia
        $comunicaciones_notificacion->setFormaEntrega('desk');
        $comunicaciones_notificacion->setMetodoId(4); //mini-foro correspondencia
        $comunicaciones_notificacion->setFEntrega(date('Y-m-d H:i:s'));
        $comunicaciones_notificacion->setMensaje($html);
        $comunicaciones_notificacion->setStatus('A');

        $comunicaciones_notificacion->save();
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
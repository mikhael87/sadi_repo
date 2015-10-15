<?php

class enviadaNotify
{
  public function notifyDeskResumenRecibidas($funcionario_noti_receptor, $funcionario_autor, $formato, $unidad_emisor, $f_envio, $n_correspondencia_emisor)
  {
    $html= null;

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
                    <td rowspan="4" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' .  $route_personal  . '" width="60"/></td>
                    <td><font style="font-size: 14px; color: blue; font-weight: bold;">'. $n_correspondencia_emisor .'</font>&nbsp;<font style="color: #5fab58; font-size: 14px">['. $formato .']</font></td>
                </tr>
                <tr>
                    <td>Unidad: '. $unidad_emisor .'</td>
                </tr>
                <tr>
                    <td>Funcionario: <b>' .  $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'] .' (' . $datos_funcio_autor[0]['ctnombre'] . ') ' . '</b></td>
                </tr>
                <tr>
                    <td class="f11n">Enviada a las '. date('h:i:s A', strtotime ( $f_envio )) .'</td>
                </tr>
            </table>';
    }

    if($html) {

        $resumen_diario= Doctrine::getTable('Comunicaciones_Notificacion')->dailySummary($funcionario_noti_receptor, 'desk');

        if(count($resumen_diario) > 0) {
            //Si no existe un resumen diario o ya paso las 6 am, crea un nuevo resumen
            foreach($resumen_diario as $resumen) {
                $resumen->setMensaje($html.'<hr style="width: 845px"/>'.$resumen->getMensaje());

                $resumen->save();
            }
        }else {
            $comunicaciones_notificacion = new Comunicaciones_Notificacion();
            $comunicaciones_notificacion->setFuncionarioId($funcionario_noti_receptor);
            $comunicaciones_notificacion->setAplicacionId(1); //correspondencia
            $comunicaciones_notificacion->setFormaEntrega('desk');
            $comunicaciones_notificacion->setMetodoId(6); //resumen diario
            $comunicaciones_notificacion->setFEntrega(date('Y-m-d', strtotime ( '+1 day' , strtotime ( date('Y-m-d') ) )).' 06:00:00');
            $comunicaciones_notificacion->setMensaje($html);
            $comunicaciones_notificacion->setStatus('A');

            $comunicaciones_notificacion->save();
        }
    }
  }

  public function notifyDeskAnulada($funcionario_noti_receptor, $funcionario_autor, $formato, $n_correspondencia_emisor)
  {
    $html= null;

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
                    <td rowspan="3" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' .  $route_personal  . '" width="60"/></td>
                    <td class="f11n">Anulaci&oacute;n de correspondencia</td>
                </tr>
                <tr>
                    <td>El funcionario <b>' .  $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'] .' (' . $datos_funcio_autor[0]['ctnombre'] . ') ' . '</b> de <b>' .  $datos_funcio_autor[0]['unombre']  . '</b></td>
                </tr>
                <tr>
                    <td>ha <b>anulado</b> su correspondencia N° <b>' .  $n_correspondencia_emisor . '</b> <font style="color: #ff4dda; font-weight: bold">['. $formato .']</font>, #string_to_replace#</td>
                </tr>
            </table>';
    }

    if($html) {
        $parametros_ar= Array('fecha' => strtotime ( '-2 minute' , strtotime ( date('Y-m-d H:i:s') ) ));
        $parametros = sfYAML::dump($parametros_ar);

        $comunicaciones_notificacion = new Comunicaciones_Notificacion();
        $comunicaciones_notificacion->setFuncionarioId($funcionario_noti_receptor);
        $comunicaciones_notificacion->setAplicacionId(1); //correspondencia
        $comunicaciones_notificacion->setFormaEntrega('desk');
        $comunicaciones_notificacion->setMetodoId(10); //anulacion correspondencia
        $comunicaciones_notificacion->setFEntrega(date('Y-m-d H:i:s'));
        $comunicaciones_notificacion->setParametros($parametros);
        $comunicaciones_notificacion->setMensaje($html);
        $comunicaciones_notificacion->setStatus('A');

        $comunicaciones_notificacion->save();
    }
  }

  public function notifyDeskEnviada($funcionario_noti_receptor, $funcionario_autor, $formato, $n_correspondencia_emisor, $correspondencia_id)
  {
    $html= null;

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
                    <td rowspan="3" width="65"><img style="border-radius: 7px; border: 1px solid #a6a6a6" src="' .  $route_personal  . '" width="60"/></td>
                    <td class="f11n">Env&iacute;o de correspondencia</td>
                </tr>
                <tr>
                    <td>El documento <b>'. $formato .'</b> con el n&uacute;mero <b><a href="' . sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_id.'/seguimiento'  . '" title="seguimiento">' .  $n_correspondencia_emisor . '</a></b>, ha sido <b>Firmado y Enviado</b></td>
                </tr>
                <tr>
                    <td>por '. $datos_funcio_autor[0]['primer_nombre'].' '.$datos_funcio_autor[0]['primer_apellido'] .' (' . $datos_funcio_autor[0]['ctnombre'] . ') ' .' de ' .  $datos_funcio_autor[0]['unombre']  . ', #string_to_replace#</td>
                </tr>
            </table>';
    }

    if($html) {
        $parametros_ar= Array('fecha' => strtotime ( '-2 minute' , strtotime ( date('Y-m-d H:i:s') ) ));
        $parametros = sfYAML::dump($parametros_ar);

        $comunicaciones_notificacion = new Comunicaciones_Notificacion();
        $comunicaciones_notificacion->setFuncionarioId($funcionario_noti_receptor);
        $comunicaciones_notificacion->setAplicacionId(1); //correspondencia
        $comunicaciones_notificacion->setFormaEntrega('desk');
        $comunicaciones_notificacion->setMetodoId(11); //firmado correspondencia
        $comunicaciones_notificacion->setFEntrega(date('Y-m-d H:i:s'));
        $comunicaciones_notificacion->setParametros($parametros);
        $comunicaciones_notificacion->setMensaje($html);
        $comunicaciones_notificacion->setStatus('A');

        $comunicaciones_notificacion->save();
    }
  }

  public function notifySms($telf_movil, $unidad_autor, $formato, $f_envio, $n_correspondencia_emisor)
  {
      //Parametros:
      //1- array con telefonos de receptores
      //2- la unidad del funcionario que firma la correspondencia (dato de sesion)
      //3- nombre del formato
      //4- fecha de envio de la correspondencia firmada
      //5- numero de correlativo de corrsp firmada

        $months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $date = date('d', strtotime($f_envio)).' de '. $months[(int)date('m', strtotime($f_envio))] .' de '. date('Y', strtotime($f_envio)) .', a las '.date('h:i A', strtotime($f_envio));

        $mensaje['emisor'] = 'Correspondencia';
        $mensaje['mensaje'] = "Reciba un cordial saludo, nos dirigimos a usted en la " .
                "oportunidad de informarle que tiene un nuevo " . $formato . " de la unidad " . $unidad_autor . " con el número \"" .
                $n_correspondencia_emisor .
                "\" enviada en fecha " . $date . ".";

        for ($i = 0; $i < count($telf_movil); $i++)
            Sms::notificacion_sistema('correspondencia', $telf_movil[$i], $mensaje);
  }

  public function notifyEmail($email_internos, $unidad_autor, $formato, $f_envio, $n_correspondencia_emisor)
  {
      //Parametros:
      //1- array con email internos (incluye los personales e institucionales) de receptores
      //2- la unidad del funcionario que firma la correspondencia (dato de sesion)
      //3- nombre del formato
      //4- fecha de envio de la correspondencia firmada
      //5- numero de correlativo de corrsp firmada

        $months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $date = date('d', strtotime($f_envio)).' de '. $months[(int)date('m', strtotime($f_envio))] .' de '. date('Y', strtotime($f_envio)) .', a las '.date('h:i A', strtotime($f_envio));

        $mensaje['mensaje'] = sfConfig::get('sf_organismo') . "<br/>";
        $mensaje['mensaje'] .= "Sistema Integral para la Gestión Laboral, Administrativa y de Servicios (SIGLAS)<br/><br/><br/>";

        for ($i = 0; $i < count($email_internos); $i++) {
            list($nombre, $cargo, $unidad, $email) = explode('%%%', $email_internos[$i]);

            $mensaje['mensaje'] .= "Srs.-<br/>";
            $mensaje['mensaje'] .= $unidad . "<br/>";
            $mensaje['mensaje'] .= $nombre . "<br/>";
            $mensaje['mensaje'] .= $cargo . "<br/><br/><br/>";

            $mensaje['mensaje'].="Reciba un cordial saludo Bolivariano y Revolucionario, nos dirigimos a usted en la " .
                    "oportunidad de informarle que tiene un nuevo <b>" . $formato . "</b> de la unidad " . $unidad_autor . " con el número <b>\"" .
                    $n_correspondencia_emisor .
                    "\"</b> enviada en fecha <b>" . $date . "</b>.";

            $mensaje['mensaje'] .= "<br/><br/>Con la intención de que sean atendidos los planteamientos de dicho " . $formato .
                    " y que se informen los resultados obtenidos, " .
                    "le invitamos a hacer uso del SIGLAS-" . sfConfig::get('sf_siglas') . ".<br/><br/>" .
                    "Reiterándole el compromiso de trabajo colectivo para la construcción de la patria socialista, " .
                    "se despide. <br/><br/>" . sfConfig::get('sf_organismo');

            $mensaje['emisor'] = 'Correspondencia';
            $mensaje['receptor'] = $nombre;

            Email::notificacion('correspondencia', $email, $mensaje, 'inmediata');
        }
  }

  public function notiPdf($id) {

        $noti= Doctrine::getTable('Comunicaciones_Notificacion')->find($id);

        // pdf object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetMargins(20, 20, 20);
//        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//        $pdf->SetHeaderData('gob_pdf.png', 120, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(20);
        $pdf->SetBarcode(date('d-m-Y'));

        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $date = date('d', strtotime('-1 day', strtotime($noti->getFEntrega()))) .' de '. $months[(int)date('m', strtotime($noti->getFEntrega()))] .' del '. date('Y', strtotime($noti->getFEntrega()));

        $contenido = str_replace('font-size: 14px;', '', $noti->getMensaje());


        $tbl = <<<EOD
<h1>Relaci&oacute;n de Recepci&oacute;n de Correspondencia</h1>
<font style="font-size: 30px; color: #666">$date</font><br/>
<table width="500" "center">
    <tr>
        <td width="500">
            $contenido
        </td>
    </tr>
</table>

EOD;

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output('Resumen_diario'.'__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }
}
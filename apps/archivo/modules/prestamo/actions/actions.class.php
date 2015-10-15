<?php

require_once dirname(__FILE__).'/../lib/prestamoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/prestamoGeneratorHelper.class.php';

/**
 * prestamo actions.
 *
 * @package    siglas
 * @subpackage prestamo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class prestamoActions extends autoPrestamoActions
{
  public function executeRegresarExpediente(sfWebRequest $request)
  {
      $this->getUser()->getAttributeHolder()->remove('expediente_id');
      $this->redirect(sfConfig::get('sf_app_archivo_url').'expediente');
  }
  
  public function executeFuncionariosSolicitantes(sfWebRequest $request)
  {
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('unidad_id')));
  }
  
  public function executeRegistroRetiroFisico(sfWebRequest $request)
  {
        $datos_retiro = $request->getParameter('retiro');
      
        $usuario=strtolower($datos_retiro['usuario_retira']);
        $contrasena=strtolower($datos_retiro['contrasena_retira']);

        // ########### ENCRIPTAMIENTO DE LA CONTRASEÑA PARA LA BUSQUEDA EN TABLA ############

        $contrasena = crypt($contrasena,$usuario);
        $usuario_retira = Doctrine::getTable('Acceso_Usuario')->findOneByNombreAndClave($usuario,$contrasena);

        // ########### SI EL USUARIO FUE ENCONTRADO ############
        if($usuario_retira){
            $codigo_prestamo=strtolower($datos_retiro['codigo_prestamo']);
            $prestamo=strtolower($datos_retiro['prestamo']);
            
            $retiro_validado = Doctrine::getTable('Archivo_PrestamoArchivo')->find($prestamo);
            $codigo_prestamo = crypt($codigo_prestamo,$retiro_validado->getFuncionarioId());
            $retiro_validado = Doctrine::getTable('Archivo_PrestamoArchivo')->findByIdAndCodigoPrestamoFisico($prestamo,$codigo_prestamo);
            
            if(count($retiro_validado)>0){
                $retiro_validado[0]->setFEntregaFisico(date('Y-m-d h:i:s'));
                $retiro_validado[0]->setReceptorEntregaFisicoId($usuario_retira->getUsuarioEnlaceId());
                $retiro_validado[0]->save();
                
                $this->getUser()->setFlash('notice', 'Se ha registrado con exito el retiro de expediente fisico.');
            } else {
                $this->getUser()->setFlash('error', 'Error. El codigo de prestamo no coincide con el registrado.');
            }
                
        } else {
            $this->getUser()->setFlash('error', 'Error. El usuario o contraseña del funcionario que retira no coinciden.');
        }
        
        $this->redirect('prestamo/index');
  }
  
  
  public function executeRegistroDevolucionFisico(sfWebRequest $request)
  {
        $id = $request->getParameter('id');
        $retiro_devuelto = Doctrine::getTable('Archivo_PrestamoArchivo')->find($id);
        
        $retiro_devuelto->setFDevolucionFisico(date('Y-m-d h:i:s'));
        $retiro_devuelto->setReceptorDevolucionFisicoId($this->getUser()->getAttribute('funcionario_id'));
        $retiro_devuelto->save();

        $this->getUser()->setFlash('notice', 'Se ha registrado con exito la devolución de expediente fisico.');
        
        $this->redirect('prestamo/index');
  }
  
  
  public function executeDeshabilitar(sfWebRequest $request)
  {
        $id = $request->getParameter('id');
        
        $prestamo = Doctrine::getTable('Archivo_PrestamoArchivo')->find($id);
        $prestamo->setStatus('D');
        $prestamo->save();
            
        $this->getUser()->setFlash('notice', 'Prestamo deshabilitado con éxito.');
        $this->redirect('prestamo/index');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('archivo_prestamo_archivo');
    $prestamo_archivo = $request->getParameter('prestamo_archivo');
    $solicitante = $prestamo_archivo['solicitante'];
    $documentos_ids = $prestamo_archivo['documentos_ids'];
        
    $datos['unidad_id']=$solicitante['unidad_id'];
    $datos['funcionario_id']=$solicitante['funcionario_id'];
    
    $documentos_ids_good = '';
    foreach ($documentos_ids as $value)
        $documentos_ids_good .= $value.',';
    
    $documentos_ids_good .= '$%&';
    $documentos_ids_good = str_replace(',$%&', '', $documentos_ids_good);    
    
    $datos['documentos_ids']=$documentos_ids_good;
    
    $error_fisico = FALSE;
    $codigo_validacion_email='';
    if(isset($datos['fisico']))
        if($datos['fisico']==TRUE){
            $codigo_validacion = rand(100000, 999999);
            $codigo_validacion = substr(dechex($codigo_validacion),0,4);
            $codigo_validacion_email = $codigo_validacion;
            $codigo_validacion = crypt($codigo_validacion,$datos['funcionario_id']);
            
            $datos['codigo_prestamo_fisico'] = $codigo_validacion;
            
            $funcionario_solicitante = Doctrine::getTable('Funcionarios_Funcionario')->find($datos['funcionario_id']);
            
            if($funcionario_solicitante->getEmailValidado()==FALSE) $error_fisico = TRUE;
        }
    
    
    $request->setParameter('archivo_prestamo_archivo',$datos);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    
    if($error_fisico==FALSE)
    {
        if ($form->isValid())
        {
          $notice = $form->getObject()->isNew() ? 'Prestamo generado correctamente.' : 'The item was updated successfully.';

          try {


            $archivo_prestamo_archivo = $form->save();



            // INICIO NOTIFICACIONES INTERNAS
            // INICIO NOTIFICACIONES INTERNAS
            // INICIO NOTIFICACIONES INTERNAS
            // INICIO NOTIFICACIONES INTERNAS

            if(isset($datos['fisico']))
                if($datos['fisico']==TRUE){
                    $email_internos = array();
                    if($funcionario_solicitante->getEmailInstitucional()!='')
                        $email_internos[count($email_internos)] = $funcionario_solicitante->getEmailInstitucional();
                    if($funcionario_solicitante->getEmailPersonal()!='')
                        $email_internos[count($email_internos)] = $funcionario_solicitante->getEmailPersonal();
                    
                    
                    $expediente = Doctrine::getTable('Archivo_Expediente')->find($archivo_prestamo_archivo->getExpedienteId());
                    $unidad_expediente = Doctrine::getTable('Organigrama_Unidad')->find($expediente->getUnidadId());


                    $nombre = $funcionario_solicitante->getPrimerNombre().' '.$funcionario_solicitante->getSegundoNombre().', '.$funcionario_solicitante->getPrimerApellido().' '.$funcionario_solicitante->getSegundoApellido();

                    $funcionario_unidad_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual($funcionario_solicitante->getId());
                    $unidad = $funcionario_unidad_cargo[0]->getUnidad();
                    $cargo = $funcionario_unidad_cargo[0]->getCargoTipo();


                    if(count($email_internos)>0){

                        list($fecha, $hora) = explode(' ', $archivo_prestamo_archivo->getCreatedAt());
                        list($h, $m, $s) = explode(':', $hora);
                        list($year_now, $month_now, $day_now) = explode('-', $fecha);
                        $month_now+=0;

                        $AP = 'am';
                        if ($h > 12) {
                            $h = $h - 12;
                            $AP = 'pm';
                        }
                        if ($h == 12) {
                            $AP = 'M';
                        }

                        $months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
                        $date = $day_now . " de " . $months[$month_now] . " de " . $year_now . " a las " . $h . ":" . $m . " " . $AP;


                        //################################## INICIO DE CORREO ELECTRONICO ##################################

                        if(count($email_internos)>0)
                        {

                            $mensaje['mensaje'] = sfConfig::get('sf_organismo')."<br/>";
                            $mensaje['mensaje'] .= "Sistema Integral para la Gestión Laboral, Administrativa y de Servicios (SIGLAS)<br/><br/><br/>";

                            for($i=0;$i<count($email_internos);$i++){

                                $mensaje['mensaje'] .= "Srs.-<br/>";
                                $mensaje['mensaje'] .= $unidad . "<br/>";
                                $mensaje['mensaje'] .= $nombre . "<br/>";
                                $mensaje['mensaje'] .= $cargo . "<br/><br/><br/>";

                                $mensaje['mensaje'].="Reciba un cordial saludo Bolivariano y Revolucionario, nos dirigimos a usted en la " .
                                        "oportunidad de informarle que se ha aprobado en fecha " . $date . ", un prestamo en fisico " .
                                        "del expediente " .$expediente->getCorrelativo()." ".
                                        "que reposa en ".$unidad_expediente->getNombre().". ";

                                $mensaje['mensaje'] .= "<br/><br/>Con la intención de que se mantenga la seguridad y confiabilidad de dicho expediente, ".
                                        " le sera solicitado al momento de buscar el fisico, un codigo de prestamo el cual podra proporcionar de ser el caso " .
                                        " al funcionario auxiliar o mensajero que realice el retiro." .
                                        "<br/><br/><b>CODIGO: ".$codigo_validacion_email."</b><br/><br/>" .
                                        "Le invitamos a hacer uso del SIGLAS-".sfConfig::get('sf_siglas').".<br/><br/>".
                                        "Reiterándole el compromiso de trabajo colectivo para la construcción de la patria socialista. " .
                                        "se despide. <br/><br/>".sfConfig::get('sf_organismo');

                                $mensaje['emisor'] = 'Archivo';
                                $mensaje['receptor'] = $nombre;

                                Email::notificacion('archivo', $email_internos[$i], $mensaje, 'inmediata');
                            }
                        }


                        //################################## FIN DE CORREO ELECTRONICO ##################################
                    }
                }

            // FIN NOTIFICACIONES INTERNAS
            // FIN NOTIFICACIONES INTERNAS
            // FIN NOTIFICACIONES INTERNAS
            // FIN NOTIFICACIONES INTERNAS


          } catch (Doctrine_Validator_Exception $e) {

            $errorStack = $form->getObject()->getErrorStack();

            $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
            foreach ($errorStack as $field => $errors) {
                $message .= "$field (" . implode(", ", $errors) . "), ";
            }
            $message = trim($message, ', ');

            $this->getUser()->setFlash('error', $message);
            return sfView::SUCCESS;
          }

          $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_prestamo_archivo)));

          if ($request->hasParameter('_save_and_add'))
          {
            $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

            $this->redirect('@archivo_prestamo_archivo_new');
          }
          else
          {
            $this->getUser()->setFlash('notice', $notice);

            $this->redirect(array('sf_route' => 'archivo_prestamo_archivo', 'sf_subject' => $archivo_prestamo_archivo));
          }
        }
        else
        {
          $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
        }
    } else {
        $this->getUser()->setFlash('error', 'Por medidas de seguridad en la entrega del expediente en fisico es necesario que el funcionario receptor 
                                             tenga registrado un correo electronico validado, por lo tanto no se podra realizar el prestamo en forma fisica.
                                             Para solucionar este inconveniente comuniquese con dicho funcionario para que ingrese al SIGLAS y registre un correo electronico valido.', false);
    }
  }

}

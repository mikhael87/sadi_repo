<?php

require_once dirname(__FILE__).'/../lib/mensajesGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/mensajesGeneratorHelper.class.php';

/**
 * mensajes actions.
 *
 * @package    siglas-(institucion)
 * @subpackage mensajes
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mensajesActions extends autoMensajesActions
{
  public function executeGrupos(sfWebRequest $request)
  {
    $this->redirect('mensajes_grupo/index');
  }
  
  public function executeExterno(sfWebRequest $request)
  {
    $this->redirect('externo/index');
  }
  
  public function executeFuncionarioRecibe(sfWebRequest $request)
  { 
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
  }
  
  public function executeLeido(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $mensaje = Doctrine::getTable('Public_Mensajes')->find($id);

    $mensaje->setStatus('L');
    $mensaje->save();
    
    echo 'Leido';
    exit();
    
//    $this->getUser()->setFlash('notice', ' El mensaje se ha eliminado de inicio de sesión');
//
//    $this->redirect('mensajes/index');
  }
  
  public function executeResponder(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $mensaje['mensaje'] = $request->getParameter('mensaje');
    
    $mensaje_old = Doctrine::getTable('Public_Mensajes')->find($id);
    
    if($mensaje_old->getFuncionarioRecibeId() == $this->getUser()->getAttribute('funcionario_id'))
    {
        $mensaje_new = new Public_Mensajes();
        $mensaje_new->setFuncionarioEnviaId($mensaje_old->getFuncionarioRecibeId());
        $mensaje_new->setFuncionarioRecibeId($mensaje_old->getFuncionarioEnviaId());
        $mensaje_new->setContenido($mensaje['mensaje']);
        $mensaje_new->setStatus('A');
        $mensaje_new->save();
        
        if($request->getParameter('mensajes_archivar'))
        {
            $mensaje_old->setStatus('L');
            $mensaje_old->save();
        }

        $emisor = Doctrine::getTable('Funcionarios_Funcionario')->find($mensaje_new->getFuncionarioEnviaId());
        $mensaje['emisor'] = $emisor->getPrimerNombre().' '.$emisor->getPrimerApellido();
        
        $receptor = Doctrine::getTable('Funcionarios_Funcionario')->find($mensaje_new->getFuncionarioRecibeId());
        $mensaje['receptor'] = $receptor->getPrimerNombre().' '.$receptor->getPrimerApellido();
        
        //
        //COMUNICACIONES
        //
        $notificacion = new mensajesNotify();
        $notificacion->notifyDesk($mensaje_old->getFuncionarioEnviaId(), $mensaje_old->getFuncionarioRecibeId(), date('Y-m-d H:i:s'), $mensaje['mensaje'], $mensaje_new->getId());
        
        if ($request->getParameter('mensajes_email')== 't' && $receptor->getEmailInstitucional())
            $notificacion->notifyEmail($receptor->getEmailInstitucional(), $mensaje);

        if ($request->getParameter('mensajes_email')== 't' && $receptor->getEmailPersonal())
            $notificacion->notifyEmail($receptor->getEmailPersonal(), $mensaje);

        if ($request->getParameter('mensajes_sms')== 't' && $receptor->getTelfMovil())
            $notificacion->notifySms($receptor->getTelfMovil(), $mensaje);
            
        //
        //FIN DE COMUNICACIONES
        //
        
        echo 'Su respuesta ha sido enviada.';
        exit();
//        $this->getUser()->setFlash('notice', ' Su respuesta ha sido enviada.');
    }
    else {
//        $this->getUser()->setFlash('error', ' Se ha realizado un evento indebido, por favor intente de nuevo.');
        echo 'Se ha realizado un evento indebido, por favor intente de nuevo.';
        exit();
    }

//    $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
  }
  
  public function executeArchivar(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $mensaje_old = Doctrine::getTable('Public_Mensajes')->find($id);
    
    if($mensaje_old->getFuncionarioRecibeId() == $this->getUser()->getAttribute('funcionario_id'))
    {
        $mensaje_old->setStatus('L');
        $mensaje_old->save();
        
//        $this->getUser()->setFlash('notice', ' Se ha archivado el mensaje.');
    }
//    else
//        $this->getUser()->setFlash('error', ' Se ha realizado un evento indebido, por favor intente de nuevo.');
    exit();
//    $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $public_mensajes = $form->save();
        $public_mensajes->setTipo('I');
        $public_mensajes->save();
        
        $conversacion = Doctrine::getTable('Public_Mensajes')->conversacion($public_mensajes->getFuncionarioEnviaId(),$public_mensajes->getFuncionarioRecibeId()); 
        
        if(count($conversacion)>0) 
            $public_mensajes->setConversacion($conversacion[0]->getConversacion());        
        else 
            $public_mensajes->setConversacion($public_mensajes->getId());
        
        $public_mensajes->save();
            
        $grupo = null;
        if($request->getParameter('mensajes_grupo_id')!=null)
            $grupo = $request->getParameter('mensajes_grupo_id');
        
        $condicion = null;
        if($request->getParameter('mensajes_condicion')!=null)
            $condicion = $request->getParameter('mensajes_condicion');
        
        $tipo = null;
        if($request->getParameter('mensajes_tipo')!=null)
            $tipo = $request->getParameter('mensajes_tipo');
        
        $oficina = null;
        if($request->getParameter('mensajes_oficina')=='U')
            $oficina = $this->getUser()->getAttribute('funcionario_unidad_id');
        
        $participantes = Doctrine::getTable('Public_Mensajes')->receptores($grupo,$oficina,$tipo,$condicion); 
        
        $participantes = array_merge($participantes, array($public_mensajes->getFuncionarioRecibeId()));
        $participantes = array_unique($participantes);

        $emisor = Doctrine::getTable('Funcionarios_Funcionario')->find($public_mensajes->getFuncionarioEnviaId());
        $mensaje['emisor'] = $emisor->getPrimerNombre().' '.$emisor->getPrimerApellido();

        $mensaje['mensaje'] = $public_mensajes->getContenido();

        for ($i = 0; $i < count($participantes); $i++)
        {
            if($participantes[$i] != $public_mensajes->getFuncionarioRecibeId() && $participantes[$i] != 0)
            {
                $mensaje_receptores = new Public_Mensajes();
                $mensaje_receptores->setFuncionarioRecibeId($participantes[$i]);
                $mensaje_receptores->setContenido($public_mensajes->getContenido());
                $mensaje_receptores->setStatus('A');
                $mensaje_receptores->setTipo('I');

                $mensaje_receptores->save();
            }
            
            if($participantes[$i] != 0)
            {                
                
                //
                //COMUNICACIONES
                //
                $notificacion = new mensajesNotify();
                
                $receptor = Doctrine::getTable('Funcionarios_Funcionario')->find($participantes[$i]);
                $mensaje['receptor'] = $receptor->getPrimerNombre().' '.$receptor->getPrimerApellido();
                
                $notificacion->notifyDesk($participantes[$i], $this->getUser()->getAttribute('funcionario_id'), $public_mensajes->getCreatedAt(), $public_mensajes->getContenido(), $public_mensajes->getId());
                
                if ($request->getParameter('mensajes_email') && $receptor->getEmailInstitucional())
                    $notificacion->notifyEmail($receptor->getEmailInstitucional(), $mensaje);
                
                if ($request->getParameter('mensajes_email') && $receptor->getEmailPersonal())
                    $notificacion->notifyEmail($receptor->getEmailPersonal(), $mensaje);
                
                if ($request->getParameter('mensajes_sms') && $receptor->getTelfMovil())
                    $notificacion->notifySms($receptor->getTelfMovil(), $mensaje);
                //
                //FIN DE COMUNICACIONES
                //
            }
        }

        if($public_mensajes->getFuncionarioRecibeId()==null)
                $public_mensajes->delete();
        
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $public_mensajes)));

      $this->redirect('@public_mensajes');
      
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
  
  public function executeCargarMensajes(sfWebRequest $request)
  {
      $id_uno =  $request->getParameter('id_uno');
      $id_dos =  $request->getParameter('id_dos');

      $mensajes_totales = Doctrine::getTable('Public_Mensajes')->mensajesHistorico($id_uno,$id_dos);
      $funcionario_envia_id = '';
        $cierre = '';
        $cadena_conversacion = '';
        $hora = 0;
        foreach ($mensajes_totales as $conversa) {
            $formato = "h:i A";
            if(strtotime($conversa->getCreatedAt()) < strtotime(date('Y-m-d')))
            {
                $formato = "d/m/Y h:i A";
            }
            if ($funcionario_envia_id != $conversa->getfuncionarioEnviaId()) {
                    $cadena_conversacion .= $cierre;
                    $cadena_conversacion .= '
                            <div style="padding-left: 40px; min-height: 40px;">';

                    $funcionario_envia_id = $conversa->getfuncionarioEnviaId();
                    $cierre = '</div>';
                    $hora = date('h:i A', strtotime($conversa->getCreatedAt()));
                }
            elseif($hora != date('h:i', strtotime($conversa->getCreatedAt()))){
                $cadena_conversacion .= $cierre;
                    $cadena_conversacion .= '
                            <div style="padding-left: 40px; min-height: 40px;">';

                    $funcionario_envia_id = $conversa->getfuncionarioEnviaId();
                    $cierre = '</div>';
                    $hora = date('h:i A', strtotime($conversa->getCreatedAt()));
            }
            $align= 'left';
            $back_color= '#fafafa';
            $border_color= '#f2f2f2';
            $arrow_icon= 'sms_arrow_left';
            $shadow= '3px 3px 5px #e5e5e5';
            if($conversa->getfuncionarioEnviaId() == $this->getUser()->getAttribute('funcionario_id')) {
                $align= 'right';
                $back_color= '#e9fce3';
                $border_color= '#dbfbd5';
                $arrow_icon= 'sms_arrow_right';
                $shadow= '-2px 3px 5px #e5e5e5';
            }
            
            $cadena_conversacion .= '<div style="float: '. $align .'; position: relative;
                width: 50%;
                padding: 8px 35px 8px 14px;
                margin-bottom: 18px;
                text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
                box-shadow: '. $shadow .';
                background-color: '. $back_color .';
                border: 1px solid '. $border_color .';
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;">';
            $cadena_conversacion .= '<div style="position: absolute; top: 2px; '. $align .': -16px">
                <img src="/images/icon/' . $arrow_icon . '.png"/></div>';
            
            $cadena_conversacion .= $conversa->getContenido();
            $cadena_conversacion .= '</div>';
            $cadena_conversacion .= '<div class="f10n" style="float: '. $align .'; padding: 0px 8px 0px 8px; width: 50px; text-align: '. $align .'">'. date("$formato", strtotime($conversa->getCreatedAt())) .'</div><br/>';
            $cadena_conversacion .= '<div style="padding-left: 40px; min-height: 40px; float: clear"></div>';
        }
        echo $cadena_conversacion;
        exit();
    }
    
    public function executeMostrarMensajes(sfWebRequest $request)
  {
        $id_recibe =  $request->getParameter('id');
        $mensajes = Doctrine::getTable('Public_Mensajes')->mensajes($this->getUser()->getAttribute('funcionario_id'),$id_recibe);

       $funcionario_envia_id = '';
        $cierre = '';
        $cadena_conversacion = '';
        foreach ($mensajes as $conversa) {
            if ($funcionario_envia_id != $conversa->getfuncionarioEnviaId()) {
                $cadena_conversacion .= $cierre;
                $cadena_conversacion .= '<hr>
                        <div style="position: relative;">
                            <div style="position: absolute;">
                                <img width="30" src="/images/fotos_personal/' . $conversa->getFuncionarioEnviaCi() . '.jpg"/>
                            </div>
                            <div style="position: absolute; right: 10px; top:-5px" class="f10n">
                                ' . date('h:i A', strtotime($conversa->getCreatedAt())) . '
                            </div>
                        </div>
                        <div style="padding-left: 40px; min-height: 40px;">';

                $funcionario_envia_id = $conversa->getfuncionarioEnviaId();
                $cierre = '</div>';
            }
            $cadena_conversacion .= $conversa->getContenido() . '<br/>';
            
        }
        echo $cadena_conversacion;
        exit();
  }
  
  public function executeEliminar(sfWebRequest $request)
  {
      $id_recibe =  $request->getParameter('id_uno');
      $id_envia =  $request->getParameter('id_dos');
      
      if($this->getUser()->getAttribute('funcionario_id') != $id_envia) {
          $id_envia= $request->getParameter('id_uno');
          $id_recibe= $request->getParameter('id_dos');
      }
      
      $mensajes_totales = Doctrine::getTable('Public_Mensajes')->mensajesHistorico($id_envia,$id_recibe);
      
      foreach($mensajes_totales as $mensajes) {
            if ($mensajes->getIdEliminado() != 0 && $mensajes->getIdEliminado() != $id_envia) {
                $mensajes->delete();
            } else {
                $mensajes->setIdEliminado($id_envia);
                $mensajes->save();
            }
        }
     $this->redirect('mensajes/index');
  }
}

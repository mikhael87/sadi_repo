<?php

/**
 * chat actions.
 *
 * @package    siglas
 * @subpackage chat
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chatActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  
  public function executeAmigosActivos(sfWebRequest $request)
  {
    $this->amigos_activos = Doctrine::getTable('Public_Amigo')->buscarAmigo($this->getUser()->getAttribute('funcionario_id'));
    $this->funcionarios = Doctrine::getTable('Funcionarios_Funcionario')->funcionariosActivos();
    
    $amigos = $this->amigos_activos;
    $amigo_agregado = array();
    
    foreach($amigos as $amigo)
    {
        $amigo_agregado[$amigo->getId()] = 1;
    }
    $this->amigo_agregado = $amigo_agregado;
  }
  
  public function executeSaveChat(sfWebRequest $request)
  {
        //LA VARIABLE 'RECEPTOR' SE CAMBIA POR USER_1 Y USER_2
        $receptor= $request->getParameter('user_1');
        $emisor= $request->getParameter('user_2');
        if($this->getUser()->getAttribute('funcionario_id')!= $emisor) {
            $emisor= $request->getParameter('user_1');
            $receptor= $request->getParameter('user_2');
        }

        $mensaje = new Public_Mensajes();
        $mensaje->setFuncionarioEnviaId($emisor);
        $mensaje->setFuncionarioRecibeId($receptor);
        $mensaje->setContenido($request->getParameter('chat'));
        $mensaje->setTipo('I');
        $mensaje->save();

        $conversacion = Doctrine::getTable('Public_Mensajes')->conversacion($mensaje->getFuncionarioEnviaId(),$mensaje->getFuncionarioRecibeId()); 
        
        $last_conver= 0;
        foreach($conversacion as $value) {
            if($value->getConversacion() > $last_conver)
                $last_conver= $value->getConversacion();
        }
        
        if(count($conversacion)>0)
            $mensaje->setConversacion($last_conver);
        else
            $mensaje->setConversacion($mensaje->getId());
        
        $mensaje->save();
        
        $cadena_conversacion= '';
        
        $cadena_conversacion .= '<div style="padding-left: 40px; min-height: 40px;"></div>';
        
        $cadena_conversacion .= '<div style="float: right; position: relative;
                padding: 8px 35px 8px 14px;
                margin-bottom: 18px;
                text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
                box-shadow: -2px 3px 5px #e5e5e5;
                background-color: #e9fce3;
                border: 1px solid #dbfbd5;
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;">';
            $cadena_conversacion .= '<div style="position: absolute; top: 2px; right: -16px">
                <img src="/images/icon/sms_arrow_right.png"/></div>';
            
            $cadena_conversacion .= $request->getParameter('chat');
            $cadena_conversacion .= '</div>';
            $cadena_conversacion .= '<div class="f10n" style="float: right; padding: 0px 8px 0px 8px; width: 50px; text-align: right">'. date("h:i A", strtotime($mensaje->getCreatedAt())) .'</div><br/>';
        
      echo $cadena_conversacion; exit;
        echo $request->getParameter('chat').'<br/>';

        exit();
  }
  
  public function executeCedulaEnvia(sfWebRequest $request)
  {
        $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($request->getParameter('id'));
        echo $funcionario->getCi();
        exit();
  }
  
  public function executeMostrarChat(sfWebRequest $request)
  {
        $mensajes_sesion = Doctrine::getTable('Public_Mensajes')->mensajesChat($this->getUser()->getAttribute('funcionario_id'));

        $funcionario_envia = array();
        foreach($mensajes_sesion as $mensaje_sesion)
        {   
            if(!isset($funcionario_envia[$mensaje_sesion->getFuncionarioEnviaId()])) {
                $funcionario_envia[$mensaje_sesion->getFuncionarioEnviaId()]='';
            }
            
            $funcionario_envia[$mensaje_sesion->getFuncionarioEnviaId()] .= $mensaje_sesion->getContenido()."<br/>";

            $mensaje_sesion->setStatus('L');
            $mensaje_sesion->save();
        }

        $this->mensajes_sesion = $funcionario_envia;
  }
  
  public function executeGuardarId(sfWebRequest $request)
  {
      $chat = array();
      if($this->getUser()->getAttribute('chat'))
        $chat = $this->getUser()->getAttribute('chat');

      $opcion = $request->getParameter('op');
      $id = $request->getParameter('id');
      
      $i = 0;
      if ($opcion == 'N') {
            foreach ($chat as $value) {
                if ($value == $id)
                    $i++;
            }

            if ($i == 0) $chat[count($chat)] = $id;
            
            $conversacion = Doctrine::getTable('Public_Mensajes')->conversacion($this->getUser()->getAttribute('funcionario_id'),$id);
            
            $funcionario_envia_id = ''; $cierre = '';
            $cadena_conversacion = '';
            foreach ($conversacion as $conversa) {
                if($funcionario_envia_id != $conversa->getfuncionarioEnviaId()){
                    
                    $formato = "h:i A";
                    if(strtotime($conversa->getCreatedAt()) < strtotime(date('Y-m-d')))
                    {
                        $formato = "d/m/Y h:i A";
                    }
                    
                    $cadena_conversacion .= $cierre;
                    $cadena_conversacion .= '<hr>
                        <div style="position: relative;">
                            <div style="position: absolute;">
                                <img width="30" src="/images/fotos_personal/'.$conversa->getFuncionarioEnviaCi().'.jpg"/>
                            </div>
                            <div style="position: absolute; right: 10px; top:-5px" class="f10n">
                                '.date("$formato", strtotime($conversa->getCreatedAt())).'
                            </div>
                        </div>
                        <div style="padding-left: 40px; min-height: 40px;">';
                    
                    $funcionario_envia_id = $conversa->getfuncionarioEnviaId();
                    $cierre = '</div>';
                }
                elseif($hora != date('h:i A', strtotime($conversa->getCreatedAt()))){
                $cadena_conversacion .= $cierre;
                    $cadena_conversacion .= '<hr>
                            <div style="position: relative;">
                                <div style="position: absolute;">
                                    <img width="30" src="/images/fotos_personal/' . $conversa->getFuncionarioEnviaCi() . '.jpg"/>
                                </div>
                                <div style="position: absolute; right: 10px; top:-5px" class="f10n">
                                    ' . date("$formato", strtotime($conversa->getCreatedAt())) . '
                                </div>
                            </div>
                            <div style="padding-left: 40px; min-height: 40px;">';

                    $funcionario_envia_id = $conversa->getfuncionarioEnviaId();
                    $cierre = '</div>';
                    $hora = date('h:i A', strtotime($conversa->getCreatedAt()));
            }
                $cadena_conversacion .= $conversa->getContenido().'<br/>';
            }
            
            echo $cadena_conversacion;
            
        } elseif ($opcion == 'D') {
            $chat_tmp = array();
            foreach ($chat as $key => $value) {
                if ($value != $id) {
                    $chat_tmp[$i] = $value;
                    $i++;
                }
            }

            if (isset($chat_tmp)) $chat = $chat_tmp;
            $minimizado = $this->getUser()->getAttribute('minimizados');
            unset($minimizado[$id]);
            $this->getUser()->setAttribute('minimizados',$minimizado);
            
        }
      
      if(count($chat)>0)
          $this->getUser()->setAttribute('chat',$chat);
      else
          $this->getUser()->getAttributeHolder()->remove('chat');
      
      exit();
  }
  
  public function executeGuardarAmigo(sfWebRequest $request)
  {
      $busqueda = Doctrine::getTable('Public_Amigo')->findByFuncionarioIdAndFuncionarioAmigoId($this->getUser()->getAttribute('funcionario_id'),$request->getParameter('id'));
      
      if(count($busqueda)==0){
        $amigo = new Public_Amigo();
        $amigo->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $amigo->setFuncionarioAmigoId($request->getParameter('id'));
        $amigo->setAutorizacion('FALSE');
        $amigo->setFAutorizacion(date('Y-m-d h:i:s'));
        $amigo->setStatus('A');
        $amigo->save();
      }
      
      exit();
  }
  
  public function executeEliminarAmigo(sfWebRequest $request)
  {
      $busqueda = Doctrine::getTable('Public_Amigo')->findByFuncionarioIdAndFuncionarioAmigoId($this->getUser()->getAttribute('funcionario_id'),$request->getParameter('id'));
      
      if(count($busqueda)>0)
          $busqueda->delete();        
      
      exit();
  }
  
  public function executeMinimizar(sfWebRequest $request)
  {
      $id = $request->getParameter('id');
      $min = $request->getParameter('min');
      if($this->getUser()->getAttribute('minimizados')) $minimizado = $this->getUser()->getAttribute('minimizados');

      $minimizado[$id] = $min;
      
      $this->getUser()->setAttribute('minimizados',$minimizado);
      exit();
  }
}

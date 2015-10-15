<?php

/**
 * seguimiento actions.
 *
 * @package    siglas-(institucion)
 * @subpackage seguimiento
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class seguimientoActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    if (!$this->getUser()->getAttribute("pag_seguimiento_atras")) {
        $this->getUser()->setAttribute('pag_seguimiento_atras', $request->getReferer());
    }
    
    $this->anterior = $this->getUser()->getAttribute("pag_seguimiento_atras");
    
    $this->correspondencias = Doctrine::getTable('Correspondencia_Correspondencia')->seguimientoCorrespondencia();

    $correspondencias_recibidas = Doctrine::getTable('Correspondencia_Correspondencia')->nGrupoRecibidaFuncionario($this->getUser()->getAttribute('correspondencia_grupo'),$this->getUser()->getAttribute('funcionario_id'));

    $i=0;
    $recibidas_ids = array();
    foreach ($correspondencias_recibidas as $cr_list)
    {
        $recibidas_ids[$i] = $cr_list->get('id');
        $i++;
    }

    $this->recibidas_ids = $recibidas_ids;
    
    $this->micro_foro = Doctrine::getTable('Correspondencia_MicroForo')->tweetCorrespondencia($this->getUser()->getAttribute('correspondencia_grupo'));
  }
  
  public function executeMicroForo(sfWebRequest $request)
  {
    $contenido=strtolower($request->getParameter('contenido'));

    $micro_foro = new Correspondencia_MicroForo();
    $micro_foro->setCorrespondenciaGrupo($this->getUser()->getAttribute('correspondencia_grupo'));
    $micro_foro->setUnidadId($this->getUser()->getAttribute('funcionario_unidad_id'));
    $micro_foro->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
    $micro_foro->setContenido($contenido);
    $micro_foro->save();
  }
  
  public function executeNotificarList(sfWebRequest $request)
  {
    $all_corresp = Doctrine::getTable('Correspondencia_Correspondencia')->findByGrupoCorrespondencia($this->getUser()->getAttribute('correspondencia_grupo'));
    
    $list_corresp= array(0);
    foreach($all_corresp as $corresp) {
        $list_corresp[] = $corresp->getId();
    }

    $list_funcionarios= array();
    
    $emisor_inicial= Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_grupo'));
    
    foreach($emisor_inicial as $emisor) {
        if($emisor->getFuncionarioId() != $this->getUser()->getAttribute('funcionario_id')) {
            $list_funcionarios[] = $emisor->getFuncionarioId();
        }
    }
    
    $funcionarios_corresp  =Doctrine::getTable('Correspondencia_Receptor')->funcionariosImplicadasGrupo($list_corresp);
    foreach($funcionarios_corresp as $val) {
        $list_funcionarios[] = $val->getFuncionarioId();
    }
    
    $list_funcionarios= array_unique($list_funcionarios);

    $this->funcionarios_corresp_datos  =Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($list_funcionarios);
  }
  
  public function executeNotificar(sfWebRequest $request)
  {
    $noti_funcionarios= $request->getParameter('f_ids');
    $comment= $request->getParameter('comment');
    $funcionarios= explode(',', $noti_funcionarios);
    
    $notificacion = new seguimientoNotify();
    for($cont=0; $cont < count($funcionarios); $cont++) {
        if($funcionarios[$cont]!= '')
            $notificacion->notifyDesk($this->getUser()->getAttribute('funcionario_id'), $funcionarios[$cont], $comment, $this->getUser()->getAttribute('correspondencia_grupo'));
    }
    echo '<font style="font-size: 12px; color: #666">Notificaciones enviadas con exito!</font>'; exit;
  }
}

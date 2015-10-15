<?php

/**
 * notibar actions.
 *
 * @package    siglas
 * @subpackage notibar
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class notibarActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }
  
  public function executeEventos(sfWebRequest $request)
  {
    $events= Doctrine::getTable('Comunicaciones_Notificacion')->groups($this->getUser()->getAttribute('funcionario_id'), 'desk', 'evento');
    $this->eventos= $events;
  }
  
  public function executeSms(sfWebRequest $request)
  {
    $sms= Doctrine::getTable('Comunicaciones_Notificacion')->groups($this->getUser()->getAttribute('funcionario_id'), 'desk', 'sms');
    $this->sms= $sms;
  }
  
  public function executeCorrespondencia(sfWebRequest $request)
  {
    $correspondencia= Doctrine::getTable('Comunicaciones_Notificacion')->groups($this->getUser()->getAttribute('funcionario_id'), 'desk', 'correspondencia');
    $this->correspondencia= $correspondencia;
  }
  
  public function executeGroupsCount(sfWebRequest $request)
  {
    $groups_count= Doctrine::getTable('Comunicaciones_Notificacion')->groupsCount($this->getUser()->getAttribute('funcionario_id'), "desk");
    $this->groups_count= $groups_count;
  }
  
  public function executeBorraIndividual(sfWebRequest $request)
  {
    $noti_delete = Doctrine::getTable('Comunicaciones_Notificacion')
                ->createQuery()
                ->delete()
                ->where('id = ?', $request->getParameter('id_n'))
                ->execute();
    exit();
  }
  
  public function executeBorraTodas(sfWebRequest $request)
  {
    $ids= $request->getParameter('ids_n');
    $exp_ids= explode(',', $ids);
    $ids_array= Array(0);
    for($cont= 0; $cont < count($exp_ids); $cont++) {
        $ids_array[]= $exp_ids[$cont];
    }
    $ids_array= array_values(array_diff($ids_array, array('')));
    
    $noti_delete = Doctrine::getTable('Comunicaciones_Notificacion')
                ->createQuery()
                ->delete()
                ->whereIn('id', $ids_array)
                ->execute();
    exit();
  }
}

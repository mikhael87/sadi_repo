<?php

/**
 * perfil actions.
 *
 * @package    siglas
 * @subpackage perfil
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class perfilActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->datosfuncionario_list = Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($this->getUser()->getAttribute('funcionario_id'));
  }
  
  public function executeSupervisorInmediato(sfWebRequest $request)
  {
  }
}

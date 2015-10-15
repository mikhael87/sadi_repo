<?php

/**
 * directorio_interno actions.
 *
 * @package    siglas-(institucion)
 * @subpackage directorio_interno
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class directorio_internoActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {   
    $this->unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); 
  }
  
  public function executeFuncionarios(sfWebRequest $request)
  {
    $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
    
    $this->amigos_activos = Doctrine::getTable('Public_Amigo')->verificarAmigo($this->getUser()->getAttribute('funcionario_id'));    
    $amigos = $this->amigos_activos;
    $amigos_agregados = array();
    
    foreach($amigos as $amigo)
    {
        $amigos_agregados[$amigo->getId()] = $amigo->getFuncionarioAmigoId();
    }
    $this->amigos_agregados = $amigos_agregados;
  }
  
  public function executeNuevoTelefono(sfWebRequest $request)
  {
    $this->cargo_id = $request->getParameter('cargo');
            
    $telefono_cargo = new Organigrama_TelefonoCargo();
    $telefono_cargo->setCargoId($request->getParameter('cargo'));
    $telefono_cargo->setTelefono($request->getParameter('telefono'));
    @$telefono_cargo->save();
    
    $this->telefonos = Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($request->getParameter('cargo'));
  }
  
  public function executeEditarTelefono(sfWebRequest $request)
  {
    $this->cargo_id = $request->getParameter('cargo');
      
    $telefono_cargo = Doctrine::getTable('Organigrama_TelefonoCargo')->find($request->getParameter('id'));
    $telefono_cargo->setTelefono($request->getParameter('telefono'));
    @$telefono_cargo->save();
    
    $this->telefonos = Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($request->getParameter('cargo'));
  }
  
  public function executeEliminarTelefono(sfWebRequest $request)
  {
    $this->cargo_id = $request->getParameter('cargo');
      
    $telefono_cargo = Doctrine::getTable('Organigrama_TelefonoCargo')->find($request->getParameter('id'));
    $telefono_cargo->delete();
    
    $this->telefonos = Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($request->getParameter('cargo'));
  }
  
  public function executeFuncionariosCalendario(sfWebRequest $request)
  {
    $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
  }
}

<?php

require_once dirname(__FILE__).'/../lib/delegarGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/delegarGeneratorHelper.class.php';

/**
 * delegar actions.
 *
 * @package    siglas
 * @subpackage delegar
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class delegarActions extends autoDelegarActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    // sorting
    if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort')))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }

    // pager
    if ($request->getParameter('page'))
    {
      $this->setPage($request->getParameter('page'));
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();
  }

  public function executeAccion(sfWebRequest $request)
  {
    switch ($request->getParameter('a')) {
        case "fc":
            $accion = "firmar_correspondencia";
            break;
        case "arrhh":
            $accion = "administrar_rrhh";
            break;
        case "calendario":
            $accion = "gestionar_calendario";
            break;
        default:
            $accion = "no_valida";
    }
    
    $this->getUser()->setAttribute('accion_delegada', $accion);
    $this->redirect('delegar/index');
  }
  
  public function executeDeshabilitar(sfWebRequest $request)
  {
        $id = $request->getParameter('id');
        
        $delegada = Doctrine::getTable('Acceso_AccionDelegada')->find($id);

        if($delegada->getUsuarioDelegaId()==$this->getUser()->getAttribute('usuario_id')){
            $delegada->setStatus('D');
            $delegada->save();
            
            $this->getUser()->setFlash('notice', 'Acción deshabilitada con éxito.');
        } else {
            $this->getUser()->setFlash('error', 'Usted no esta autorizado para realizar esta acción.', false);
        }

        $this->redirect('delegar/index');
  }
  
  public function executeFuncionariosDelegados(sfWebRequest $request)
  {
    $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('acceso_accion_delegada');
    $parametros = $request->getParameter('parametros');
    
    $usuario_delegado = Doctrine::getTable('Acceso_Usuario')->findOneByUsuarioEnlaceIdAndEnlaceId($request->getParameter('funcionario_id'),1);
    $datos['usuario_delegado_id']=$usuario_delegado->getId();
    
    if($this->getUser()->getAttribute('accion_delegada'))
        $datos['accion']=$this->getUser()->getAttribute('accion_delegada');

    if(count($parametros)>0){
        $datos['parametros'] = sfYAML::dump($parametros);
    }
    
    $request->setParameter('acceso_accion_delegada',$datos);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $acceso_accion_delegada = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $acceso_accion_delegada)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@acceso_accion_delegada_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'acceso_accion_delegada', 'sf_subject' => $acceso_accion_delegada));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}

<?php

require_once dirname(__FILE__).'/../lib/cuerpo_documentalGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/cuerpo_documentalGeneratorHelper.class.php';

/**
 * cuerpo_documental actions.
 *
 * @package    siglas
 * @subpackage cuerpo_documental
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cuerpo_documentalActions extends autoCuerpo_documentalActions
{
  public function executeRegresarTipologias(sfWebRequest $request)
  {
      $this->redirect(sfConfig::get('sf_app_archivo_url').'tipologia_documental');
  }
  
  public function executeSaveCuerpo(sfWebRequest $request)
  {     
    $cuerpo = new Archivo_CuerpoDocumental();
    $cuerpo->setSerieDocumentalId($request->getParameter('serie_id'));
    $cuerpo->setNombre($request->getParameter('cuerpo'));
    $cuerpo->save();

    echo '<option value="'.$cuerpo->getId().'" selected="selected">'.$cuerpo->getNombre()."</option>";    
    exit();
  }
  
  public function executeOrdenar(sfWebRequest $request)
  {
    $cuerpos = $request->getParameter('cuerpos');
    
      try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
            
            $i=1;
            foreach ($cuerpos as $cuerpo_id) {
                $cuerpo = Doctrine::getTable('Archivo_CuerpoDocumental')->find($cuerpo_id);
                $cuerpo->setOrden($i);
                $cuerpo->save();
                $i++;
            }
            
            $conn->commit();
        
      } catch (Doctrine_Validator_Exception $e) {
          
        $conn->rollBack();
      }

    exit();
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $archivo_cuerpo_documental = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_cuerpo_documental)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_cuerpo_documental_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

//        $this->redirect(array('sf_route' => 'archivo_cuerpo_documental_edit', 'sf_subject' => $archivo_cuerpo_documental));
        $this->redirect('@archivo_cuerpo_documental');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}

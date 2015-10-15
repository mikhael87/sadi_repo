<?php

require_once dirname(__FILE__).'/../lib/funcionario_cargo_certificadoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/funcionario_cargo_certificadoGeneratorHelper.class.php';

/**
 * funcionario_cargo_certificado actions.
 *
 * @package    siglas
 * @subpackage funcionario_cargo_certificado
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class funcionario_cargo_certificadoActions extends autoFuncionario_cargo_certificadoActions
{
  public function executeRegresar(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('certificados_funcionario_cargo_id');
    $this->redirect(sfConfig::get('sf_app_funcionarios_url').'funcionario_cargo');
  }
  
  public function executeAnularCertificado(sfWebRequest $request) {
      $id = $request->getParameter('id');
      $funcionario_cargo_certificado = Doctrine::getTable('Funcionarios_FuncionarioCargoCertificado')->find($id);
      $funcionario_cargo_certificado->setStatus('I');
      $funcionario_cargo_certificado->save();
     
      $this->redirect('funcionario_cargo_certificado/index');
  }
    
  public function executePrepararFirma(sfWebRequest $request)
  {
      echo 'SIGLAS';
      exit();
  }
  
  public function executeProcesarPaquete(sfWebRequest $request)
  {
        $paquete_crypt = $request->getParameter('signature_packet');
        $signature_open = SignSiglas::desempaquetar($paquete_crypt);

        $this->certificado = $signature_open['header']['certificado'];
        $this->configuracion = $signature_open['header']['configuracion'];
        $this->ssl_open = openssl_x509_parse($signature_open['header']['certificado']);        
        
        $certificado_activo = Doctrine::getTable('Funcionarios_FuncionarioCargoCertificado')->findOneByFuncionarioCargoIdAndStatus($this->getUser()->getAttribute('certificados_funcionario_cargo_id'),'A');
        
        if($certificado_activo){
            $certificado_activo_hash_512 = hash('sha512', $certificado_activo->getCertificado());
            $certificado_open_hash_512 = hash('sha512', $signature_open['header']['certificado']);

            if($certificado_activo_hash_512 == $certificado_open_hash_512){
                $this->certificado_existente = 'Actualmente este certificado ya se encuentra activo. por lo tanto no se permitira registrarlo de nuevo';
            } else {
                $this->certificado_existente = 'Actualmente el funcionario tiene registrado otro certificado activo. por lo tanto al guardar este se anularan automaticamente los anteriores.';
            }
        }
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('funcionarios_funcionario_cargo_certificado');
    
    $paquete_crypt = $request->getParameter('signature_packet');
    $signature_open = SignSiglas::desempaquetar($paquete_crypt);
    
    $certificado_activo = Doctrine::getTable('Funcionarios_FuncionarioCargoCertificado')->findOneByFuncionarioCargoIdAndStatus($this->getUser()->getAttribute('certificados_funcionario_cargo_id'),'A');

    if($certificado_activo){
        $certificado_activo_hash_512 = hash('sha512', $certificado_activo->getCertificado());
        $certificado_open_hash_512 = hash('sha512', $signature_open['header']['certificado']);

        if($certificado_activo_hash_512 == $certificado_open_hash_512){
            $this->getUser()->setFlash('error', 'El certificado que intento registrar, anteriormente fue registrado y aun se encuentra activo por lo tanto no se proceso su solicitud.');

            $this->redirect('@funcionarios_funcionario_cargo_certificado');
        }
    }
    
    
    $ssl_open = openssl_x509_parse($signature_open['header']['certificado']);

    if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if( isset( $_SERVER ['HTTP_VIA'] ))  $ip = $_SERVER['HTTP_VIA'];
    else if( isset( $_SERVER ['REMOTE_ADDR'] ))  $ip = $_SERVER['REMOTE_ADDR'];
    $configuracion[0]['ip'] = $ip; 
    $configuracion[0]['configuracion'] = $signature_open['header']['configuracion'].'';
    
    $datos['funcionario_cargo_id']=$this->getUser()->getAttribute('certificados_funcionario_cargo_id');
    $datos['certificado']=$signature_open['header']['certificado'];
    $datos['detalles_tecnicos']=sfYAML::dump($ssl_open);
    $datos['configuraciones']=sfYAML::dump($configuracion);
    $datos['f_valido_desde']=date('Y-m-d', $ssl_open['validFrom_time_t']);
    $datos['f_valido_hasta']=date('Y-m-d', $ssl_open['validTo_time_t']);
    
    $request->setParameter('funcionarios_funcionario_cargo_certificado',$datos);
      
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $conn = Doctrine_Manager::connection();
      $conn->beginTransaction();
      try {
        if($certificado_activo){
            $certificado_activo->setStatus('I');
            $certificado_activo->save();
        }
            
        $funcionarios_funcionario_cargo_certificado = $form->save();
        
        $conn->commit();
      } catch (Doctrine_Validator_Exception $e) {
        $conn->rollBack();
        
        $errorStack = $form->getObject()->getErrorStack();

        $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
        foreach ($errorStack as $field => $errors) {
            $message .= "$field (" . implode(", ", $errors) . "), ";
        }
        $message = trim($message, ', ');

        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $funcionarios_funcionario_cargo_certificado)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', 'El certificado fue registrado con exito.');

        $this->redirect('@funcionarios_funcionario_cargo_certificado');
      }
      else
      {
        $this->getUser()->setFlash('notice', 'El certificado fue registrado con exito.');

        $this->redirect('@funcionarios_funcionario_cargo_certificado');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}

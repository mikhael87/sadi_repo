<?php

require_once dirname(__FILE__).'/../lib/llave_ingresoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/llave_ingresoGeneratorHelper.class.php';

/**
 * llave_ingreso actions.
 *
 * @package    siglas
 * @subpackage llave_ingreso
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class llave_ingresoActions extends autoLlave_ingresoActions
{
  public function executeRegresar(sfWebRequest $request)
  {
    $this->redirect(sfConfig::get('sf_app_seguridad_url').'ingresa');
  }
  
  public function executeGenerarPasesIngreso(sfWebRequest $request)
  {
    $existentes=0;
    $inexistentes=0;
    
    for($i=1;$i<=$request->getParameter('n_pases_nuevos');$i++){
        if($i<10){
            $n_pase = '0'.$i;
        } else {
            $n_pase = $i;
        }
            
        $llave_ingreso = Doctrine_Core::getTable('Seguridad_LlaveIngreso')->findOneByNPase($n_pase);
        
        if($llave_ingreso){
            $existentes++;
        } else {
            $inexistentes++;
            $llave_ingreso = new Seguridad_LlaveIngreso();
            $llave_ingreso->setNPase($n_pase);
            $llave_ingreso->setStatus('D'); // D = Disponible, O = Ocupado
            $llave_ingreso->save();
        }
    }
    
    if($existentes==0){
        $this->getUser()->setFlash('notice', 'Se generaron '.$request->getParameter('n_pases_nuevos').' pases de ingreso con exito.');
    } else if($existentes>0 && $inexistentes>0){
        $this->getUser()->setFlash('notice', 'Anteriormente se generaron '.$existentes.' pases de ingreso, por lo tanto solo se generaron '.$inexistentes.' pases de ingreso.');
    } else if($inexistentes==0){
        $this->getUser()->setFlash('error', 'Anteriormente se generaron los '.$existentes.' pases solicitados.');
    }
    
    $this->redirect(sfConfig::get('sf_app_seguridad_url').'llave_ingreso/index');
  }
}

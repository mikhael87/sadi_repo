<?php

/**
 * documentos actions.
 *
 * @package    siglas
 * @subpackage documentos
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class documentosActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
    $sf_rrhh = sfYaml::load(sfConfig::get('sf_root_dir')."/config/siglas/rrhh.yml");
    
    $this->documentos_activos = $sf_rrhh['documentos'];
    
    $recursos_humanos = Doctrine::getTable('Organigrama_Unidad')->find($sf_oficinasClave['recursos_humanos']['seleccion']);

    $this->nombre_rrhh = Formateo::prefijo_unidad($recursos_humanos->getNombre(),2).' '.$recursos_humanos->getNombre();
  }
  
  public function executeCargarFormulario(sfWebRequest $request)
  {
    $tipo = $request->getParameter('tipo');
    $this->setTemplate('../lib/'.$tipo.'/'.$tipo.'Form');
  }
  
  public function executePdf(sfWebRequest $request)
  {
    $datos = $request->getParameter('datos');
    
    // ################ INIZIALIZAR EL OBJETO DE PDF  #################
    $config = sfTCPDFPluginConfigHandler::loadConfig('pdf_configs.yml');
    // pdf object
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // settings

    // ################ FIN INIZIALIZAR EL OBJETO DE PDF  #################
    eval('$documento = new documento' . ucfirst($datos['tipo']) . '();');
    $documento->executePdf($pdf);

    exit();
  }
}
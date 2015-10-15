<?php

require_once dirname(__FILE__).'/../lib/expedientes_compartidosGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/expedientes_compartidosGeneratorHelper.class.php';

/**
 * expedientes_compartidos actions.
 *
 * @package    siglas
 * @subpackage expedientes_compartidos
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class expedientes_compartidosActions extends autoExpedientes_compartidosActions
{
    public function executeRegresarExpediente(sfWebRequest $request)
    {
        $this->redirect(sfConfig::get('sf_app_archivo_url').'expediente');
    }
    
    public function executeSolicitarFisico(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $carga_edicion = Doctrine::getTable('Archivo_Expediente')->find($id);
        $form = new formatoSolicitudExpediente();

        $correspondencia = array();
        //Vinculo llena campos de correspondencia desde elementro en archivo
        $correspondencia['formato'] = $form->executeTraerDesdeArchivo($carga_edicion);
        $correspondencia['formato']['tipo_formato_id'] = '13';

        $this->getUser()->setAttribute('correspondencia',$correspondencia);
        
        exit();
    }
    
    public function executeExcel(sfWebRequest $request) {
        $tableMethod = $this->configuration->getTableMethod();
        if (null === $this->filters) {
            $this->filters = $this->configuration->getFilterForm($this->getFilters());
        }

        $this->filters->setTableMethod($tableMethod);

        $query = $this->filters->buildQuery($this->getFilters());

        $this->excel = $query->execute();
        $this->setLayout(false);
        $this->getResponse()->clearHttpHeaders();
    }
}

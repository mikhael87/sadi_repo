<?php

require_once dirname(__FILE__).'/../lib/pagoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/pagoGeneratorHelper.class.php';

/**
 * pago actions.
 *
 * @package    siglas
 * @subpackage pago
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pagoActions extends autoPagoActions
{
    public function executeIndex(sfWebRequest $request)
    {
      $this->getUser()->getAttributeHolder()->remove('pae_registro_id');
        
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
    
    public function executePagoDetalle(sfWebRequest $request)
    {
        $registro_id = $request->getParameter('id');

        $this->getUser()->setAttribute('pae_registro_id', $registro_id);

        $this->redirect('pagoDetalle/index?id='.$registro_id);
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

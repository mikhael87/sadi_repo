<?php

require_once dirname(__FILE__).'/../lib/repososGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/repososGeneratorHelper.class.php';

/**
 * reposos actions.
 *
 * @package    siglas
 * @subpackage reposos
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class repososActions extends autoRepososActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
    $this->getUser()->getAttributeHolder()->remove('tercerizado');
    $this->getUser()->getAttributeHolder()->remove('call_module_master');
    $this->getUser()->getAttributeHolder()->remove('call_module_master_reporteGlobal_reposo_tipo');
    $this->getUser()->getAttributeHolder()->remove('call_module_master_reporteGlobal_reposo_unidad');
    
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
  
  public function executeSeguimientoSolicitud(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

    $this->getUser()->setAttribute('correspondencia_grupo', $correspondencia->get('grupo_correspondencia'));

    $this->redirect(sfConfig::get('sf_app_correspondencia_url').'seguimiento/index');
  }
  
  public function executeSolicitar(sfWebRequest $request)
  {
      if($request->getParameter('tercerizado')){
        $this->getUser()->setAttribute('tercerizado',$request->getParameter('tercerizado'));
      }
      
      if($request->getParameter('unidad_seleccionada_id')){
        $this->getUser()->setAttribute('call_module_master_reporteGlobal_reposo_unidad',$request->getParameter('unidad_seleccionada_id'));
      }
  }
  
  public function executeReporteGlobal(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
    $tipo = $request->getParameter('tipo');
    
    if($tipo){
        if($tipo=='global')
            $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); 
        elseif($tipo=='unidad'){
            $session_cargos = $this->getUser()->getAttribute('session_cargos');
            $unidades_good = array(); $organigramas = array(); $unsetear = array();
            
            // BUSQUEDA DE EXTRUCTURAS DIFERENTES PARA DEJAR UNA SOLA EN CASO DE COLISION 
            foreach ($session_cargos as $cargo) {
                if($cargo['cargo_grado_id']==99){
                    $ban=FALSE;
                    
                    // VERIFICAR QUE LA UNIDAD QUE VA A BUSCAR NO PERTENEZCA A UNA EXTRUCTURA YA BUSCADA
                    foreach ($organigramas as $organigrama) {
                        if (array_key_exists($cargo['unidad_id'], $organigrama)) {
                            $ban = TRUE;
                        }
                    }
                    
                    if($ban==FALSE) {
                        // SI LA UNIDAD BUSCADA NO PERTENECE A UNA EXTRUCTURA YA BUSCADA HACER UNA BUSQUEDA EN REVERSA
                        // ES DECIR BUSCAR LAS YA BUSCADAS EN LA EXTRUCTURA DE ESTA ULTIMA UNIDAD 
                        $unidad_analisis_reversa = Doctrine::getTable('Organigrama_Unidad')->combounidad(FALSE,array($cargo['unidad_id']));
                        
                        foreach ($unidades_good as $unidad_good => $id_unset) {
                            if (array_key_exists($id_unset, $unidad_analisis_reversa)) {
                                $unsetear[] = $unidad_good;
                            }
                        }
                        
                        // ELIMINAR LAS UNIDADES QUE SE ENCONTRARON EN LA NUEVA BUSQUEDA
                        foreach ($unsetear as $unset => $id_unset) {
                            unset($unidades_good[$id_unset]);
                        }
                        $unsetear = array();
                        
                        $unidades_good[] = $cargo['unidad_id'];
                        $organigramas[] = $unidad_analisis_reversa;
                    }
                }
            }
            
            if(count($cargo)>0)
                $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(FALSE,$unidades_good); 
            else {
                echo "Error: no tiene los privilegios para ingresar a esta consulta"; exit();
            }
        } else {
            echo "Error."; exit();
        }
        
        $funcionarios = array();
        foreach ($unidades as $unidad_id => $valores) {

            if($unidad_id != ''){
                $funcionarios[$unidad_id] = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($unidad_id));

                foreach ($funcionarios[$unidad_id] as $funcionario) {
                    $reposos[$funcionario->getId()] = Doctrine::getTable('Rrhh_Reposos')->repososPersonal($funcionario->getId());
                }
            }
        }

        $this->getUser()->setAttribute('call_module_master_reporteGlobal_reposo_tipo',$tipo);
        
        $this->unidades = $unidades;
        $this->funcionarios = $funcionarios;
        $this->reposos = $reposos;
    } else {
        echo "Error."; exit();
    }
  }
}

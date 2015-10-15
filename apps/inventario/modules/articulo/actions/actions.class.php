<?php

require_once dirname(__FILE__).'/../lib/articuloGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/articuloGeneratorHelper.class.php';

/**
 * articulo actions.
 *
 * @package    siglas
 * @subpackage articulo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class articuloActions extends autoArticuloActions
{
  public function executeUnidadesMedida(sfWebRequest $request)
  {
    $this->redirect('inventario_unidad_medida');
  }
  
  public function executeComprasRealizadas(sfWebRequest $request)
  {
    $this->redirect('inventario_articulo_ingreso');
  }
  
  public function executeEntregasArticulo(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $articulo = Doctrine::getTable('Inventario_Articulo')->find($id);
    $this->getUser()->setAttribute('header_ruta', $articulo->getNombre());
    
    $this->getUser()->setAttribute('articulo_id', $id);

    $this->redirect('/despacho_articulos');
  }
  
  public function executeEstadisticas(sfWebRequest $request)
  {
      $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad();
      
      $this->unidades = $unidades;
  }
  public function executeEstadisticaSeleccionada(sfWebRequest $request){
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        if(!$request->getParameter('fi'))
        {
            if(!$request->getParameter('ff'))
            {
                $fecha_inicio='2005-12-18 00:00:00';
                $fecha_final= date('Y-m-d H:i:s');
            }
            else
            {
                $fecha_inicio='2005-12-18 00:00:00';
                $fecha_final=$request->getParameter('ff')." 23:59:59";
            }
        }
        elseif(!$request->getParameter('ff'))
        {
            $fecha_inicio=$request->getParameter('fi')." 00:00:00";
            $fecha_final= date('Y-m-d H:i:s');
        }
        else
        {
            $fecha_inicio=$request->getParameter('fi')." 00:00:00";
            $fecha_final=$request->getParameter('ff')." 23:59:59";
        }
        
        $articulo = $request->getParameter('articulo');
        $estadistica_tipo = $request->getParameter('tipo');

        $estadistica = new Inventario_ArticuloStatistic();
            
        eval('$estadistica_datos = $estadistica->'.$estadistica_tipo.'($articulo);');
            
            $this->estadistica_datos = $estadistica_datos;
            $this->setTemplate('estadisticas/'.$estadistica_tipo);
    }
}

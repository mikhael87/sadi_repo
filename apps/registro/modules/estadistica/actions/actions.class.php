<?php

/**
 * estadistica actions.
 *
 * @package    siglas
 * @subpackage estadistica
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class estadisticaActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
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

        $unidad_id = $request->getParameter('unidad_id');
        $estadistica_tipo = $request->getParameter('tipo');

//        $estadistica_tipo = 'totalStatusEnviada';
//        $estadistica_tipo = 'totalStatusEnviadaAOficinas';
//        $estadistica_tipo = 'totalStatusEnviadaAOrganismos';
//        $estadistica_tipo = 'totalEnviadaPorDias';
//        $estadistica_tipo = 'totalEnviadaPorCreador';

        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST

        $autorizado= true;
        if($autorizado==true){
            $estadistica = new Empresas_EmpresaStatistic();

            eval('$estadistica_datos = $estadistica->'.$estadistica_tipo.'($unidad_id, $fecha_inicio,$fecha_final);');

            $this->estadistica_datos = $estadistica_datos;
            $this->fecha = "Estadistica generada desde: ".date('d/m/Y',  strtotime($fecha_inicio))." Hasta: ".date('d/m/Y',  strtotime($fecha_final));
            $this->unidad_id = $unidad_id;

            $this->setTemplate($estadistica_tipo);

        } else {
            echo "No esta autorizado para revisar las estadisticas de esta unidad";
            exit();
        }


    }
}

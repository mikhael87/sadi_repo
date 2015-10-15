<?php

require_once dirname(__FILE__).'/../lib/vehiculoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vehiculoGeneratorHelper.class.php';

/**
 * vehiculo actions.
 *
 * @package    siglas
 * @subpackage vehiculo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vehiculoActions extends autoVehiculoActions
{
  public function executeVolver(sfWebRequest $request)
  {
    $this->redirect('vehiculo/index');
  }
    
  public function executeConductores(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('vehiculo_id',$request->getParameter('id'));
    
    $this->redirect('conductor_vehiculo/index');
  }
  
  public function executeServicios(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('vehiculo_id',$request->getParameter('id'));

    $this->redirect('mantenimiento/index');
  }
  
  public function executeTrack(sfWebRequest $request)
  {
    $this->redirect('tracker/index?id='.$request->getParameter('id'));
  }
  
  public function executeTipoServicio(sfWebRequest $request)
  {
    $this->redirect('mantenimiento_tipo/index');
  }
  
  public function executeTipoVehiculo(sfWebRequest $request)
  {
    $this->redirect('vehiculo_tipo/index');
  }
  
  public function executeTipoVehiculoUso(sfWebRequest $request)
  {
    $this->redirect('vehiculo_tipo_uso/index');
  }
  
  public function executeGps(sfWebRequest $request)
  {
    $gps_vehiculos= Doctrine::getTable('Vehiculos_GpsVehiculo')->findByVehiculoIdAndStatus($request->getParameter('id'), 'A');

    if(count($gps_vehiculos) > 0) {
        $this->getUser()->setAttribute('vehiculo_id',$request->getParameter('id'));
        $this->redirect('gps_vehiculo/index');
    }else{
        $this->redirect('gps_vehiculo/gpsInstaller?id='.$request->getParameter('id'));
    }
    exit;
  }
  
  public function executeConductoresAct(sfWebRequest $request)
  {
      $vehiculo_id= $request->getParameter('id');
      
      $conductores= Doctrine::getTable('Vehiculos_ConductorVehiculo')->conductoresAct();
      $asignados= Doctrine::getTable('Vehiculos_ConductorVehiculo')->findByVehiculoIdAndStatus($vehiculo_id, 'A');
      
      $this->conductores= $conductores;
      $this->vehiculo_id= $vehiculo_id;
      $this->asignados= $asignados;
  }
  
  public function executeGuardarConductores(sfWebRequest $request)
  {
      $vehiculo_id= $request->getParameter('vehiculo_id');

      $cadena= $request->getParameter('cadena');
      $cadena .= 'end';
      $cadena= str_replace('#end', '', $cadena);
      $cadena_exp= explode('#', $cadena);
      
      if($cadena== 'end')
          $cadena_exp= Array();
      
      $asignados= Doctrine::getTable('Vehiculos_ConductorVehiculo')->findByVehiculoIdAndStatus($vehiculo_id, 'A');
      $asignado_array= Array();
      foreach($asignados as $value) {
          $asignado_array[]= $value->getFuncionarioId();
      }
      
      foreach($cadena_exp as $key => $val) {
          if(!in_array($val, $asignado_array)) {
              $registro= new Vehiculos_ConductorVehiculo();
              $registro->setVehiculoId($vehiculo_id);
              $registro->setFuncionarioId($val);
              $registro->setCondicionId(1);
              $registro->setFAsignacion(date('Y-m-d H:i:s'));
              $registro->setStatus('A');
              $registro->save();
          }
      }
      
      foreach($asignado_array as $val) {
          if(!in_array($val, $cadena_exp)) {
              $registro= Doctrine::getTable('Vehiculos_ConductorVehiculo')->findOneByFuncionarioIdAndStatus($val, 'A');
              $registro->setFDesincorporado(date('Y-m-d H:i:s'));
              $registro->setStatus('I');
              $registro->save();
          }
      }
      
      $this->vehiculo_id= $vehiculo_id;
  }
  
  public function executeProcesarServicio(sfWebRequest $request)
  {
        $mantenimiento_id= $request->getParameter('servicio_id');
        $vehiculo_id= $request->getParameter('vehiculo_id');
        
        $registro= Doctrine::getTable('Vehiculos_Mantenimiento')->find($mantenimiento_id);
        
        $registro->setStatus('C');
        $registro->save();
        
        $servicios= Doctrine::getTable('Vehiculos_Mantenimiento')->servicioPorVehiculo($vehiculo_id);
        $this->servicios= $servicios;
        $this->vehiculo_id= $vehiculo_id;
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'El vehículo ha sido agregado con exito.' : 'El vehículo ha sido actualizado con exito.';

      try {
        $vehiculos_vehiculo = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $vehiculos_vehiculo)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' Puede agregar otro seguidamente.');

        $this->redirect('@vehiculos_vehiculo_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect('@vehiculos_vehiculo');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ha ocurrido un error, no se ha agregado el vehículo.', false);
    }
  }
  
  public function executeEstadisticas(sfWebRequest $request){
      
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

    $estadistica_tipo = $request->getParameter('tipo');

//        $estadistica_tipo = 'servicios';
//        $estadistica_tipo = 'usoGps';

    //DATOS QUE VIENEN POR REQUEST
    //DATOS QUE VIENEN POR REQUEST
    //DATOS QUE VIENEN POR REQUEST

    $estadistica = new Vehiculos_VehiculoStatistic();

    eval('$estadistica_datos = $estadistica->'.$estadistica_tipo.'($fecha_inicio,$fecha_final);');

    $this->estadistica_datos = $estadistica_datos;
    $this->fecha = "Estadistica generada desde: ".date('d/m/Y',  strtotime($fecha_inicio))." Hasta: ".date('d/m/Y',  strtotime($fecha_final));

    $this->setTemplate('estadisticas/'.$estadistica_tipo);
  }
}

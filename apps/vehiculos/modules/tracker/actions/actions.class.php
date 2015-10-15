<?php

/**
 * tracker actions.
 *
 * @package    siglas
 * @subpackage tracker
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class trackerActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('vehiculos_track');

    //ESTE ID ES EL DE GPS_VEHICULO Y NO EL DE VEHICULO ID
    if($request->getParameter('id')) {
        $ids= array($request->getParameter('id'));

        $this->getUser()->setAttribute('vehiculos_track',$ids);
    }
  }

  public function executeAddTrack(sfWebRequest $request)
  {
      if($this->getUser()->hasAttribute('vehiculos_track')) {
            $ids= $this->getUser()->getAttribute('vehiculos_track');
            $ids[]= $request->getParameter('id');
        }else
            $ids= array($request->getParameter('id'));

        $this->getUser()->setAttribute('vehiculos_track',$ids);

      exit();
  }

  public function executeRemoveTrack(sfWebRequest $request)
  {
      if($this->getUser()->hasAttribute('vehiculos_track')) {
          $ids= $this->getUser()->getAttribute('vehiculos_track');
          $key= array_search($request->getParameter('id'), $ids);
          unset($ids[$key]);

          if(count($ids) == 0)
            $this->getUser()->getAttributeHolder()->remove('vehiculos_track');
          else
            $this->getUser()->setAttribute('vehiculos_track',$ids);
      }

      exit();
  }

  public function executeCoord(sfWebRequest $request)
  {

      $gps_conf = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/gps.yml");

      if($gps_conf['recuperacion']['status']== false) {
            $current_dir= getcwd();

            chdir(sfConfig::get('sf_root_dir'));

            //LLAMADA AL TASK SYMFONY DE RECUPERACION DE TRACKS
            $task= new cronGpsTrackAllTask($this->dispatcher, new sfFormatter());

            $task->run();

            chdir($current_dir);
            sfContext::switchTo('vehiculos');


            $request_coord[0]['marca']= 'no_session';
            $first= (($this->getUser()->hasAttribute('vehiculos_track')? ((count($this->getUser()->getAttribute('vehiculos_track')) > 0) ? $this->getUser()->getAttribute('vehiculos_track'): Array(0=> '')) : Array(0=> '')));

            if($this->getUser()->hasAttribute('vehiculos_track')) {
                $vehiculos= Doctrine::getTable('Vehiculos_Tracker')->track($this->getUser()->getAttribute('vehiculos_track'));

                $i= 0;
                foreach($vehiculos as $track) {

                    $gps_vehiculo= Doctrine::getTable('Vehiculos_GpsVehiculo')->find($track->getGpsVehiculoId());
                    $vehiculo= Doctrine::getTable('Vehiculos_Vehiculo')->find($gps_vehiculo->getVehiculoId());

                    $request_coord[$i]['vehiculoId']= $vehiculo->getId();
                    $request_coord[$i]['marca']= $vehiculo->getMarca();
                    $request_coord[$i]['modelo']= $vehiculo->getModelo();
                    $request_coord[$i]['placa']= $vehiculo->getPlaca();
                    $request_coord[$i]['icon']= $gps_vehiculo->getIcono();
                    $request_coord[$i]['color_icon']= $gps_vehiculo->getColorIcon();
                    $request_coord[$i]['lat']= $track->getLatitud();
                    $request_coord[$i]['long']= $track->getLongitud();
                    $request_coord[$i]['speed']= $track->getVelocidad();
                    $request_coord[$i]['pwr']= $track->getFuente();
                    $request_coord[$i]['door']= $track->getPuerta();
                    $request_coord[$i]['acc']= $track->getAcc();
                    $i++;
                }

                if($i== 0)
                    $request_coord[0]['marca']= 'no_object';

            }else
                $request_coord[0]['marca']= 'no_session';

            return $this->renderText(json_encode($request_coord));
      }else {
          $request_coord[0]['marca']= 'empty';

          return $this->renderText(json_encode($request_coord));
      }
  }
  
  public function executeShowSites(sfWebRequest $request)
  {
      $sitios= Doctrine::getTable('Public_Sitio')->sitiosActivos();
      
      $i= 0;
      $request_coord[0]['nombre']= 'empty';
      foreach($sitios as $sitio) {
           $icon_or= $sitio->getIcono();
           $icon= str_replace('red', $sitio->getColor(), $icon_or);
          
           $icon= 'http://'.$_SERVER['SERVER_NAME'].'/images/icon/tracker/'.$icon;
          
           $request_coord[$i]['nombre']= $sitio->getNombre();
           $request_coord[$i]['tipo_nombre']= $sitio->getTipoNombre();
           $request_coord[$i]['latitud']= $sitio->getLatitud();
           $request_coord[$i]['longitud']= $sitio->getLongitud();
           $request_coord[$i]['icono']= $icon;
           $request_coord[$i]['direccion']= $sitio->getDireccion();
           
           $i++;
      }
      
      return $this->renderText(json_encode($request_coord));
  }

  protected function tiempo_parada($date1, $date2) {
        $diff = abs(strtotime($date2) - strtotime($date1));

        $years   = floor($diff / (365*60*60*24));
        $months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        $hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
        $minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
        $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));

        $cadena= '';
        if($years != 0)
            $cadena .= ' '.$years.' A&ntilde;os';
        if($months != 0)
            $cadena .= ' '.$months.' Meses';
        if($days != 0)
            $cadena .= ' '.$days.' D&iacute;as';
        if($hours != 0)
            $cadena .= ' '.$hours.' Hrs';
        if($minuts != 0)
            $cadena .= ' '.$minuts.' Min';
        if($seconds != 0)
            $cadena .= ' '.$seconds.' Seg';

        return $cadena;
    }

  public function executeRecorridos(sfWebRequest $request)
  {
    $ids= $this->getUser()->getAttribute('vehiculos_track');

    if(count($ids) > 0) {
        $desde= (($request->getParameter('f_ini'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_ini'))) : date('Y-m-d H:i:s', strtotime('-1 day ' . date('Y-m-d'))));
        $hasta= (($request->getParameter('f_fin'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_fin'))) : date('Y-m-d H:i:s'));

        $rutas= Doctrine::getTable('Vehiculos_Tracker')->recorrido($ids, $desde, $hasta);

        //DEBERIA SER SOLO UN ID (UTILIZAR ESTO AL PRINCIPIO DE LOGICA)
        $id= '';
        foreach($ids as $val)
            $id= $val;
        $datos_vehiculo= Doctrine::getTable('Vehiculos_Vehiculo')->VehiculoPorGpsVehiculoId($id);

        $marca= ''; $modelo= '';
        foreach($datos_vehiculo as $datos) {
            $marca= $datos->getMarca();
            $modelo= $datos->getModelo();
        }

        //NUMERO DE TRACKS LIMITE PARA CONSIDERAR UNA PARADA (DEPENDE DE LA FRECUENCIA DE EMISION GPS)
        $stop_time= 5;
        $path= ''; $cache= '';
        $stop= 0; $i= 0; $k= 0;
        $stop_array= Array();
        $extremos= Array();
        $puntos= Array();
        foreach($rutas as $value) {
            if($value->getVelocidad()== 0 || $value->getVelocidad()== 0.00) {
                $cache .= '['. $value->getLatitud() .','. $value->getLongitud() .'],';
                //ARRAY DE PARADAS
                if($stop== 0) {
                    $stop_array[$i]['latitud']= $value->getLatitud();
                    $stop_array[$i]['longitud']= $value->getLongitud();
                    list($fecha,$hora)= explode(' ', $value->getFRecibido());
                    $stop_array[$i]['fecha']= date('d-m-Y', strtotime($fecha));
                    $stop_array[$i]['hora']= ((date('H', strtotime($hora)) >= 12)? (date('H', strtotime($hora))-12).date(':i:s', strtotime($hora)).' PM' : $hora.' AM');
                    $stop_array[$i]['fecha_unix']= $value->getFRecibido();
                    $stop_array[$i]['acc']= $value->getAcc();
//                    echo $value->getId(); exit;
                    $i++;
                }
                $stop++;
            }else {
                if($stop >= $stop_time) {
                    //CALCULO DE DIFERENCIA ENTRE ACTUAL Y ANTERIOR GUARDADO EN ARRAY
                    $diferencia= $this->tiempo_parada($stop_array[$i-1]['fecha_unix'], $value->getFRecibido());
                    $stop_array[$i-1]['tiempo']= $diferencia;

                    //AGREGA A LA RUTA LEGITIMA EL PRIMER TRACK EN 0 KM/H
                    $path .= '['.$stop_array[$i-1]['latitud'] .','. $stop_array[$i-1]['longitud'] .'],';

                    $stop= 0;
                    $cache= '';
                }else {
                    //BORRA EL TRACK 0KM/H YA QUE NO REPRESENTA UNA PARADA
                    if(count($stop_array) > 0) {
                        if(!isset($stop_array[$i-1]['tiempo'])) {
                            unset($stop_array[$i-1]);
                        }
                    }
                    $path .= $cache;
                    $cache= '';
                    $stop= 0;


                    $puntos[$k]['latitud']= $value->getLatitud();
                    $puntos[$k]['longitud']= $value->getLongitud();
                    list($fecha,$hora)= explode(' ', $value->getFRecibido());
                    $puntos[$k]['fecha']= date('d-m-Y', strtotime($fecha));
                    $puntos[$k]['hora']= ((date('H', strtotime($hora)) >= 12)? (date('H', strtotime($hora))-12).date(':i:s', strtotime($hora)).' PM' : $hora.' AM');
                    $puntos[$k]['fecha_unix']= $value->getFRecibido();
                    $puntos[$k]['acc']= $value->getAcc();
                    $puntos[$k]['marca']= $marca;
                    $puntos[$k]['modelo']= $modelo;

                    $k++;


                    $path .= '['. $value->getLatitud() .','. $value->getLongitud() .'],';
                }
            }
        }

        if($path != '') {
            $rutas_arreglo= explode('],[', $path);
            $rutas_arreglo= str_replace('[', '', $rutas_arreglo);
            $rutas_arreglo= str_replace(']', '', $rutas_arreglo);

            $primero= $rutas_arreglo[0];
            $ultimo= $rutas_arreglo[count($rutas_arreglo)-1];

            $parts_last= explode(',', $ultimo);
            $extremos['ultimo']['latitud']= $parts_last[0];
            $extremos['ultimo']['longitud']= $parts_last[1];

            $parts_first= explode(',', $primero);
            $extremos['primero']['latitud']= $parts_first[0];
            $extremos['primero']['longitud']= $parts_first[1];
        }else {
            $extremos= '';
        }

//        print_r($stop_array); exit;
        //RUTAS LEGITIMAS
        if($path != '') {
            $path .= 'end';
            $path= str_replace(',end', '', $path);
            $path= '['. $path .']';
        }else
            $path= '[]';

        if($request->getParameter('f_ini')) {
//            echo $path;
//            exit();
            $request_coord['coor_array']= $path;
            $request_coord['coor_stop']= $stop_array;
            $request_coord['coor_ext']= $extremos;
            $request_coord['coor_puntos']= $puntos;
            return $this->renderText(json_encode($request_coord));
        }else {
            $this->path= $path;
        }
    }else {

    }
  }

  public function executeShowGeocerca(sfWebRequest $request)
  {
      $gps_vehiculo_id= $request->getParameter('gps_vehiculo_id');

      if($gps_vehiculo_id !== 'none') {
            $datos_gps_vehiculo= Doctrine::getTable('Vehiculos_GpsVehiculo')->find($gps_vehiculo_id);
            $params = sfYaml::load($datos_gps_vehiculo->getAlertaParametro());

            $top_right= $params['geocerca']['top_right'];
            $top_left= $params['geocerca']['top_left'];
            $bottom_right= $params['geocerca']['bottom_right'];
            $bottom_left= $params['geocerca']['bottom_left'];

            $path= '[]';
            if($top_right !== '' && $top_left !== '' && $bottom_right !== '' && $bottom_left !== '') {
                $vert1= explode('#', $top_right);
                $vert2= explode('#', $top_left);
                $vert3= explode('#', $bottom_right);
                $vert4= explode('#', $bottom_left);
                $path = '['. $vert1[0] .','. $vert1[1] .'],';
                $path .= '['. $vert2[0] .','. $vert2[1] .'],';
                $path .= '['. $vert3[0] .','. $vert3[1] .'],';
                $path .= '['. $vert4[0] .','. $vert4[1] .'],';

                if($path != '') {
                  $path .= 'end';
                  $path= str_replace(',end', '', $path);
                  $path= '['. $path .']';
                }else
                  $path= '[]';

                $request_coord['path']= $path;
                return $this->renderText(json_encode($request_coord));
            }else {
                $request_coord['path']= 'undefined';
                return $this->renderText(json_encode($request_coord));
            }
      }else {
            $request_coord['path']= 'none';
            return $this->renderText(json_encode($request_coord));
      }
  }

  public function executeAlertas(sfWebRequest $request)
  {
    $ids= $this->getUser()->getAttribute('vehiculos_track');

    if(count($ids) > 0) {
        $desde= (($request->getParameter('f_ini'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_ini'))) : date('Y-m-d H:i:s', strtotime('-1 day ' . date('Y-m-d'))));
        $hasta= (($request->getParameter('f_fin'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_fin'))) : date('Y-m-d H:i:s'));

        $alertas= Doctrine::getTable('Vehiculos_Tracker')->alerta($ids, $desde, $hasta);

        //DEBERIA SER SOLO UN ID (UTILIZAR ESTO AL PRINCIPIO DE LOGICA)
        $id= '';
        foreach($ids as $val)
            $id= $val;
        $datos_vehiculo= Doctrine::getTable('Vehiculos_Vehiculo')->VehiculoPorGpsVehiculoId($id);

        $marca= ''; $modelo= '';
        foreach($datos_vehiculo as $datos) {
            $marca= $datos->getMarca();
            $modelo= $datos->getModelo();
        }

        $path= '';
        $k= 0;
        $puntos= Array();
        foreach($alertas as $value) {

            //COMANDOS
            $comando= '';
            $pc_com= $value->getComando();
            if($pc_com == 'no gps') {
                $comando= 'Sin conexi&oacute;n GPS';
            }elseif($pc_com == 'move') {
                $comando= 'En veh&iacute;culo se ha movido';
            }elseif($pc_com == 'lowbattery') {
                $comando = 'Bateria interna baja';
            }elseif($pc_com == 'extpower') {
                $comando = 'Bateria desconectada';
            }elseif($pc_com == 'ACC off') {
                $comando = 'Veh&iacute;culo apagado';
            }elseif($pc_com == 'ACC on') {
                $comando = 'Veh&iacute;culo encendido';
            }elseif(preg_match("/speed/", $pc_com)) {
                $vel= str_replace('speed', '', $pc_com);
                $comando = 'Exceso de velocidad ('.$vel.' Km/h establecidos)';
            }else {
                $comando= 'Alerta no identifica';
            }

            $puntos[$k]['latitud']= $value->getLatitud();
            $puntos[$k]['longitud']= $value->getLongitud();
            $puntos[$k]['comando']= $comando;
            $puntos[$k]['velocidad']= $value->getVelocidad();
            list($fecha,$hora)= explode(' ', $value->getFechaGps());
            $puntos[$k]['fecha']= date('d-m-Y', strtotime($fecha));
            $puntos[$k]['hora']= ((date('H', strtotime($hora)) >= 12)? (date('H', strtotime($hora))-12).date(':i:s', strtotime($hora)).' PM' : $hora.' AM');
            $puntos[$k]['marca']= $marca;
            $puntos[$k]['modelo']= $modelo;

            $path .= '['. $value->getLatitud() .','. $value->getLongitud() .'],';

            $k++;
        }

        //RUTAS LEGITIMAS
        if($path != '') {
            $path .= 'end';
            $path= str_replace(',end', '', $path);
            $path= '['. $path .']';
        }else
            $path= '[]';

        if($request->getParameter('f_ini')) {
            $request_coord['coor_array']= $path;
            $request_coord['coor_puntos']= $puntos;
            return $this->renderText(json_encode($request_coord));
        }else {
            $this->path= $path;
        }
    }else {

    }
  }

  public function executeGeocerca(sfWebRequest $request)
  {
  }

  public function executeSitios(sfWebRequest $request)
  {
      $sitios= Doctrine::getTable('Public_Sitio')->sitiosActivos();
      
      $sitios_tipo= Doctrine::getTable('Public_SitioTipo')->findByStatus('A');

      $this->sitios= $sitios;
      $this->sitios_tipo= $sitios_tipo;
  }

  public function executeSaveGeocerca(sfWebRequest $request)
  {
    $gps_vehiculos= $request->getParameter('elegidos');
    $top_r= $request->getParameter('t_r');
    $top_l= $request->getParameter('t_l');
    $bottom_r= $request->getParameter('b_r');
    $bottom_l= $request->getParameter('b_l');

    $parts= explode('#', $gps_vehiculos);

    foreach($parts as $gps_vehiculo_id) {
        if($gps_vehiculo_id != '') {
            $datos_gps_vehiculo= Doctrine::getTable('Vehiculos_GpsVehiculo')->find($gps_vehiculo_id);

            $params = sfYaml::load($datos_gps_vehiculo->getAlertaParametro());

            $params['geocerca']['top_right']= $top_r;
            $params['geocerca']['top_left']= $top_l;
            $params['geocerca']['bottom_right']= $bottom_r;
            $params['geocerca']['bottom_left']= $bottom_l;

            $yaml_new = sfYAML::dump($params);

            $datos_gps_vehiculo->setAlertaParametro($yaml_new);
            $datos_gps_vehiculo->save();
        }
    }
    echo 'Geocerca establecida con exito.';
    exit();
  }
  
  public function executeSaveSite(sfWebRequest $request)
  {
    $lat= $request->getParameter('lat');
    $lng= $request->getParameter('lng');
    $name= $request->getParameter('name');
    $dir= $request->getParameter('dir');
    $sitio_tipo_id= $request->getParameter('sitio_tipo_id');
    $color= $request->getParameter('color');
    
    $sitio= new Public_Sitio();
    $sitio->setSitioTipoId($sitio_tipo_id);
    $sitio->setLatitud($lat);
    $sitio->setLongitud($lng);
    $sitio->setNombre($name);
    $sitio->setStatus('A');
    $sitio->setDireccion($dir);
    $sitio->setMostrar(true);
    $sitio->setColor($color);
    $sitio->save();
    
    echo 'Sitio establecido con exito.';
    exit();
  }

  public function executeExportarRecorridosExcel(sfWebRequest $request)
  {
        $ids= $this->getUser()->getAttribute('vehiculos_track');

        if(count($ids) > 0) {
            $desde= (($request->getParameter('f_ini'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_ini'))) : date('Y-m-d H:i:s', strtotime('-1 day ' . date('Y-m-d'))));
            $hasta= (($request->getParameter('f_fin'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_fin'))) : date('Y-m-d H:i:s'));

            $rutas= Doctrine::getTable('Vehiculos_Tracker')->recorrido($ids, $desde, $hasta);

            //DEBERIA SER SOLO UN ID (UTILIZAR ESTO AL PRINCIPIO DE LOGICA)
            $id= '';
            foreach($ids as $val)
                $id= $val;
            $datos_vehiculo= Doctrine::getTable('Vehiculos_Vehiculo')->VehiculoPorGpsVehiculoId($id);

            $marca= ''; $modelo= '';
            foreach($datos_vehiculo as $datos) {
                $marca= $datos->getMarca();
                $modelo= $datos->getModelo();
            }
        }

        $this->rutas= $rutas;
        $this->marca= $marca;
        $this->modelo= $modelo;
        $this->desde= $desde;
        $this->hasta= $hasta;

        $this->setLayout(false);
        $this->getResponse()->clearHttpHeaders();

  }

  public function executeExportarAlertasExcel(sfWebRequest $request)
  {
        $ids= $this->getUser()->getAttribute('vehiculos_track');

        if(count($ids) > 0) {
            $desde= (($request->getParameter('f_ini'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_ini'))) : date('Y-m-d H:i:s', strtotime('-1 day ' . date('Y-m-d'))));
            $hasta= (($request->getParameter('f_fin'))? date('Y-m-d H:i:s', strtotime($request->getParameter('f_fin'))) : date('Y-m-d H:i:s'));

            $rutas= Doctrine::getTable('Vehiculos_Tracker')->alerta($ids, $desde, $hasta);

            //DEBERIA SER SOLO UN ID (UTILIZAR ESTO AL PRINCIPIO DE LOGICA)
            $id= '';
            foreach($ids as $val)
                $id= $val;
            $datos_vehiculo= Doctrine::getTable('Vehiculos_Vehiculo')->VehiculoPorGpsVehiculoId($id);

            $marca= ''; $modelo= '';
            foreach($datos_vehiculo as $datos) {
                $marca= $datos->getMarca();
                $modelo= $datos->getModelo();
            }
        }

        $this->rutas= $rutas;
        $this->marca= $marca;
        $this->modelo= $modelo;
        $this->desde= $desde;
        $this->hasta= $hasta;

        $this->setLayout(false);
        $this->getResponse()->clearHttpHeaders();

  }
}

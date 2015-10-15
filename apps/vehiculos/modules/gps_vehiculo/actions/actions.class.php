<?php

require_once dirname(__FILE__).'/../lib/gps_vehiculoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/gps_vehiculoGeneratorHelper.class.php';

/**
 * gps_vehiculo actions.
 *
 * @package    siglas
 * @subpackage gps_vehiculo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class gps_vehiculoActions extends autoGps_vehiculoActions
{
    public function executeVolver(sfWebRequest $request)
    {
      $this->redirect('vehiculo/index');
    }

    public function executeAlertas(sfWebRequest $request)
    {
      $gps_vehiculo= $request->getParameter('id');

      $vehiculo_par= Doctrine::getTable('Vehiculos_GpsVehiculo')->find($gps_vehiculo);

      $this->executeActualizacionGpsStatus($vehiculo_par->getSim(), NULL, NULL);

      $vehiculo= Doctrine::getTable('Vehiculos_GpsVehiculo')->find($gps_vehiculo);

      $parametros= sfYaml::load($vehiculo->getAlertaParametro());

      $this->vehiculo_id= $vehiculo->getVehiculoId();
      $this->gps_vehiculo_id= $gps_vehiculo;
      $this->sim= $vehiculo->getSim();
      $this->parametros= $parametros;
    }

    public function executeVolverGpsVehiculo(sfWebRequest $request)
    {
      $this->redirect('gps_vehiculo/index');
    }

    public function executeGpsInstaller(sfWebRequest $request)
    {
      $this->vehiculo_id= $request->getParameter('id');
    }

    public function executeRespuesta(sfWebRequest $request)
    {
        $sf_sms = sfYaml::load(sfConfig::get('sf_root_dir')."/config/siglas/sms.yml");

        $hora= date('Y-m-d H:i:s', strtotime($request->getParameter('hora')));
        $sim= $request->getParameter('sim');
        $ans= $request->getParameter('ans');
        $case= $request->getParameter('case');
        $condi= $request->getParameter('condi');

        if ((!substr($sim, 0, 1 )=='0')) {
            $sim = '0'.$sim;
        }

        $query = '';
        if($sf_sms['conexion_gammu']['version']=='1.28')
            $query = "SELECT * FROM inbox WHERE sendernumber = '". $sim ."' AND updateindb > '". $hora ."'";
        elseif($sf_sms['conexion_gammu']['version']=='1.31')
            $query = "SELECT * FROM inbox WHERE SenderNumber = '". $sim ."' AND UpdatedInDB > '". $hora ."'";

        $manager = Doctrine_Manager::getInstance()
                            ->openConnection(
                            'pgsql' . '://' .
                            $sf_sms['conexion_gammu']['username'] . ':' .
                            $sf_sms['conexion_gammu']['password'] . '@' .
                            $sf_sms['conexion_gammu']['host'] . '/' .
                            $sf_sms['conexion_gammu']['dbname'], 'dbGAMMU');

        $inbox = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);

        Doctrine_Manager::getInstance()->closeConnection($manager);

        $request_json['data']= 'fallo';

        foreach($inbox as $sms) {
            if($ans == 'check') {
                if(is_numeric($sms['TextDecoded']) && strlen($sms['TextDecoded']) == 15) {
                    $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
                    $registro->setImei($sms['TextDecoded']);
                    $parametros_ini= array(
                        'alertas'=> array(
                                'suppress'=> array(
                                    'status'=> false,
                                    'label'=> 'Ahorro de sms',
                                    'help'=> 'Cuando el veh&iacute;culo se encuentre apago o detenido el dispositivo no enviara se&ntilde;ales de tracker.',
                                ),
                                'lowbattery'=> array(
                                    'status'=> false,
                                    'label'=> 'Bateria baja(3.5v)',
                                    'help'=> 'Bateria interna del dispositivo baja.',
                                ),
                                'extpower'=> array(
                                    'status'=> false,
                                    'label'=> 'Bateria desconectada(12v)',
                                    'help'=> 'Bateria de alimentacion principal (vehiculo) desconectada.',
                                ),
                                'gps_signal'=> array(
                                    'status'=> false,
                                    'label'=> 'Perdida de señal GPS',
                                    'help'=> 'Antena GPS no puede establecer conección con ningún satelite.',
                                ),
                                'move'=> array(
                                    'status'=> false,
                                    'label'=> 'Movimiento',
                                    'help'=> 'Vehículo en movimiento luego: de 200 Mts.',
                                ),
                                'speed'=> array(
                                    'status'=> false,
                                    'label'=> 'Limite de velocidad',
                                    'help'=> 'Vehículo a propasado el limite de velocidad establecido',
                                ),
                                'acc'=> array(
                                    'status'=> false,
                                    'label'=> 'Encendido/apagado',
                                    'help'=> 'El motor del Vehículo ha sido encendido o apagado',
                                ),
                                'stockade'=> array(
                                    'status'=> false,
                                    'label'=> 'Geocerca',
                                    'help'=> 'El Vehículo ha salido del perimetro.',
                                )
                        ),
                        'geocerca'=> array(
                                'top_right'=> '',
                                'top_left'=> '',
                                'bottom_right'=> '',
                                'bottom_left'=> '',
                        )
                    );
                    $parametros = sfYAML::dump($parametros_ini);
                    $registro->setAlertaParametro($parametros);
                    $registro->save();
                    $request_json['data']= 'exito';
                    break;
                }else{
                    $request_json['data']= 'fallo';
                }
            }else {
                $texto= strtolower($sms['TextDecoded']);
                if(preg_match("/". $ans ."/", $texto)) {
                    $request_json['data']= 'exito';
                    if($case > 10) {
                        //ACCION QUE RECOJE TODOS LOS SMS RESPUESTA Y ACTUALIZA YML DE ALERTAS
                        $this->executeActualizacionGpsStatus($sim, $case, $condi);
                    }
                    break;
                }else {
                    if($case < 11) {
                        if(preg_match("/password[[:space:]]fail/", $texto))
                            $request_json['data']= 'autenticacion_fallo';
                        else
                            $request_json['data']= 'fallo';
                    }else {
                        $request_json['data']= 'fallo';
                    }
                }
            }
        }
        return $this->renderText(json_encode($request_json));
    }

    public function executeProgramadorGPs(sfWebRequest $request)
    {
      $step= $request->getParameter('op');
      $sim= $request->getParameter('sim');
      $v_id= $request->getParameter('v_id');
      $condi= $request->getParameter('condi');
      $param= $request->getParameter('param');
      $comando= '';
      $stat= '';
      $ans= '';
      switch ($step) {
          case '1':
              //REINICIO (RESET)
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $pass= '123456';
              if($registro) {
                  if($registro->getClave() != '')
                      $pass= $registro->getClave();
              }
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find(31);
              $comando= str_replace('<password>', $pass, $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'reset[[:space:]]ok';
              break;
          case '2':
              //CAMBIO DE CLAVE (PASSWORD)
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $pass_old= '123456';
              if($registro) {
                  if($registro->getClave() != '')
                      $pass_old= $registro->getClave();
              }else {
                  $codigo= substr($sim, 0, 4);
                  $operador= '';
                  switch ($codigo) {
                      case '0412':
                          $operador= 1;
                          break;
                      case '0416':
                          $operador= 2;
                          break;
                      case '0426':
                          $operador= 2;
                          break;
                      case '0414':
                          $operador= 3;
                          break;
                      case '0424':
                          $operador= 3;
                          break;
                  }

                  $registro= new Vehiculos_GpsVehiculo();
//                  $clave= rand(100000,999999);
                  //CLAVE PROVISIONAL
                  $clave= 445577;
                  $registro->setClave($clave);
                  $registro->setVehiculoId($v_id);
                  $registro->setGpsId(1);
                  $registro->setOperadorId($operador);
                  $registro->setStatus('A');
                  $registro->setSim($sim);
                  $registro->save();
              }

              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find(2);
              $comando= str_replace('<password>', $pass_old, $reg_comando->getComando());
              $comando= str_replace('<new_password>', $clave, $comando);

              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'password[[:space:]]ok';
              break;
          case '3':
              //INICIALIZACION (BEGIN)
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find(1);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'begin[[:space:]]ok';
              break;
          case '4':
              //ADMINISTRADORES (ADMIN)
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              //NUMEROS ADMIN DE GAMMU
              $gps_old = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/gps.yml");
              
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find(3);
              
              foreach($gps_old['administradores'] as $value) {
                  $gammu_number= $value;
                  $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
                  $comando= str_replace('<number_authorized>', $gammu_number, $comando);
                  Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              }
              $ans= 'admin[[:space:]]ok';
              break;
          case '5':
              //TRACK AUTOMATICO POR INTERVALOS DE TIEMPO (FIX)
              $gps_conf = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/gps.yml");

              $parametro= explode('#', $gps_conf['recuperacion']['frecuencia_activo']);
              $intervalo= $parametro[0];

              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find(5);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              $comando= str_replace('<interval>0', $intervalo, $comando);
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'without';
              break;
          case '10':
              //CHECKEO Y ASIGNACION (IMEI)
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find(32);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'check';
              break;
          /////////////////////////
          //COMANDOS PARA ALERTAS//
          /////////////////////////
          case '11':
              //SUPRESS
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 8 : 34 ;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              if($condi== 'on')
                $ans= 'suppress[[:space:]]drift[[:space:]]ok';
              else
                $ans= 'nosuppress[[:space:]]ok';
              break;
          case '12':
              //LOWBATTERY
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 10 : 11;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'lowbattery[[:space:]]'. $condi .'[[:space:]]ok';
              break;
          case '13':
              //EXTPOWER
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 12 : 13;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'extpower[[:space:]]'. $condi .'[[:space:]]ok';
              break;
          case '14':
              //GPS_SIGNAL
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 14 : 15;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              $ans= 'gpssignal[[:space:]]'. $condi .'[[:space:]]ok';
              break;
          case '15':
              //MOVE
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 19 : 20;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              if($condi== 'on')
                $ans= 'move[[:space:]]ok';
              else
                $ans= 'nomove[[:space:]]ok';
              break;
          case '16':
              //SPEED
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 21 : 22;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              $comando= str_replace('<velocity>', $param , $comando);
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              if($condi== 'on')
                $ans= 'speed[[:space:]]ok';
              else
                $ans= 'nospeed[[:space:]]ok';
              break;
          case '17':
              //ACC
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 23 : 24;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              if($condi== 'on')
                $ans= 'acc[[:space:]]ok';
              else
                $ans= 'noacc[[:space:]]ok';
              break;
          case '18':
              //STOCKADE
              $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);
              $db_comand= ($condi== 'on')? 17 : 18;
              $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find($db_comand);
              $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
              
              $params = sfYaml::load($registro->getAlertaParametro());
              
              $t_r= $params['geocerca']['top_right'];
              $t_l= $params['geocerca']['top_left'];
              $b_r= $params['geocerca']['bottom_right'];
              $b_l= $params['geocerca']['bottom_left'];
              
              if($t_r !== '' && $t_l !== '' && $b_r !== '' && $b_l !== '') {
                  $t_r_parts= explode('#', $t_r);
                  $t_l_parts= explode('#', $t_l);
                  $b_r_parts= explode('#', $b_r);
                  $b_l_parts= explode('#', $b_l);
                  
                  $comando= str_replace('latitud_1', $t_r_parts[0] , $comando);
                  $comando= str_replace('longitud_1', $t_r_parts[1] , $comando);
                  $comando= str_replace('latitud_2', $b_l_parts[0] , $comando);
                  $comando= str_replace('longitud_2', $b_l_parts[1] , $comando);
                  $comando= str_replace('latitud_3', $b_r_parts[0] , $comando);
                  $comando= str_replace('longitud_3', $b_r_parts[1] , $comando);
                  $comando= str_replace('latitud_4', $t_l_parts[0] , $comando);
                  $comando= str_replace('longitud_4', $t_l_parts[1] , $comando);
              }else {
                  $stat= 'No hay Geocerca asignada a este veh&iacute;culo.';
              }
              
              Sms::sms_at('correspondencia', $sim, $comando, 'auto');
              
              if($condi== 'on')
                $ans= 'stockade[[:space:]]ok';
              else
                $ans= 'nostockade[[:space:]]ok';
              break;
          default:
              break;
      }

      $request_json['data']= 'enviado';
      $request_json['hora']= date("Y-m-d H:i:s");
      $request_json['ans']= $ans;
      $request_json['case']= $step;
      $request_json['condi']= $condi;
      $request_json['stat']= $stat;
      return $this->renderText(json_encode($request_json));
      exit;
    }

    protected function processForm(sfWebRequest $request, sfForm $form)
    {
      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid())
      {
        $notice = $form->getObject()->isNew() ? 'Asignado con exito.' : 'Asignacion actualizada con exito.';

        try {
          $vehiculos_gps_vehiculo = $form->save();
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

        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $vehiculos_gps_vehiculo)));

        if ($request->hasParameter('_save_and_add'))
        {
          $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

          $this->redirect('@vehiculos_gps_vehiculo_new');
        }
        else
        {
          $this->getUser()->setFlash('notice', $notice);

          $this->redirect('@vehiculos_gps_vehiculo');
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'No se han guardado los cambios debido a un error no identificado.', false);
      }
    }

    public function executeActualizacionGpsStatus($sim, $case, $condi)
    {
        if ((!substr($sim, 0, 1 )=='0')) {
            $sim = '0'.$sim;
        }

        $sf_sms = sfYaml::load(sfConfig::get('sf_root_dir')."/config/siglas/sms.yml");

        $del_array= array();

        $respuestas= array(
            11 => array(
                'name' => 'suppress',
                'on' => 'suppress[[:space:]]drift[[:space:]]ok',
                'off' => 'nosuppress[[:space:]]ok'),
            12 => array(
                'name' => 'lowbattery',
                'on' => 'lowbattery[[:space:]]on[[:space:]]ok',
                'off' => 'lowbattery[[:space:]]off[[:space:]]ok'),
            13 => array(
                'name' => 'extpower',
                'on' => 'extpower[[:space:]]on[[:space:]]ok',
                'off' => 'extpower[[:space:]]off[[:space:]]ok'),
            14 => array(
                'name' => 'gps_signal',
                'on' => 'gpssignal[[:space:]]on[[:space:]]ok',
                'off' => 'gpssignal[[:space:]]off[[:space:]]ok'),
            15 => array(
                'name' => 'move',
                'on' => 'move[[:space:]]ok',
                'off' => 'nomove[[:space:]]ok'),
            16 => array(
                'name' => 'speed',
                'on' => 'speed[[:space:]]ok',
                'off' => 'nospeed[[:space:]]ok'),
            17 => array(
                'name' => 'acc',
                'on' => 'acc[[:space:]]ok',
                'off' => 'noacc[[:space:]]ok'),
        );

        $query = '';
        if($sf_sms['conexion_gammu']['version']=='1.28')
            $query = "SELECT * FROM inbox WHERE sendernumber = '". $sim ."'";
        elseif($sf_sms['conexion_gammu']['version']=='1.31')
            $query = "SELECT * FROM inbox WHERE SenderNumber = '". $sim ."'";

        $manager = Doctrine_Manager::getInstance()
                            ->openConnection(
                            'pgsql' . '://' .
                            $sf_sms['conexion_gammu']['username'] . ':' .
                            $sf_sms['conexion_gammu']['password'] . '@' .
                            $sf_sms['conexion_gammu']['host'] . '/' .
                            $sf_sms['conexion_gammu']['dbname'], 'dbGAMMU');

        $inbox = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);

        Doctrine_Manager::getInstance()->closeConnection($manager);

        $vehiculo= Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($sim);

        $parametros= sfYaml::load($vehiculo->getAlertaParametro());

        if($condi && $case) {
            //BUSCA UN SOLO COMANDO PARA NO INTERFERIR EN LA BUSQUEDA DE OTROS COMANDOS AUN ACTIVOS
            $texto= '';
            foreach($inbox as $sms) {
                $texto= strtolower($sms['TextDecoded']);
                if((preg_match("/". $respuestas[$case][$condi] ."/", $texto))) {
                    $parametros['alertas'][$respuestas[$case]['name']]['status']= ($condi== 'on')? TRUE : FALSE;
                    $del_array[]= $sms['ID'];
                }
            }
        }else {
            //BUSCA TODOS LOS COMANDOS CASO AL INGRESAR A ALERTAS PARA ACTUALIZAR YML'S
            $texto= '';
            foreach($inbox as $sms) {
                $texto= strtolower($sms['TextDecoded']);

                foreach($respuestas as $key => $value) {
                    if((preg_match("/". $value['on'] ."/", $texto))) {
                        $parametros['alertas'][$value['name']]['status']= TRUE;
                        $del_array[]= $sms['ID'];
                    }elseif((preg_match("/". $value['off'] ."/", $texto))) {
                        $parametros['alertas'][$value['name']]['status']= FALSE;
                        $del_array[]= $sms['ID'];
                    }
                }
            }
        }

        $parametros_new = sfYAML::dump($parametros);

        $vehiculo->setAlertaParametro($parametros_new);
        $vehiculo->save();


        $ids= "";
        foreach($del_array as $val)
            $ids .= "'".$val."',";
        $ids .= 'end';
        $ids= str_replace(',end', '', $ids);

        if($ids != 'end') {
            $query = "DELETE FROM inbox WHERE ID IN (".$ids.")";

            $manager = Doctrine_Manager::getInstance()
                                ->openConnection(
                                'pgsql' . '://' .
                                $sf_sms['conexion_gammu']['username'] . ':' .
                                $sf_sms['conexion_gammu']['password'] . '@' .
                                $sf_sms['conexion_gammu']['host'] . '/' .
                                $sf_sms['conexion_gammu']['dbname'], 'dbGAMMU');

            $delete_inbox = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);

            Doctrine_Manager::getInstance()->closeConnection($manager);
        }

        return true;
    }
}

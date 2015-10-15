<?php

require_once dirname(__FILE__).'/../lib/vacacionesGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vacacionesGeneratorHelper.class.php';

/**
 * vacaciones actions.
 *
 * @package    siglas
 * @subpackage vacaciones
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vacacionesActions extends autoVacacionesActions
{ 
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->getUser()->getAttributeHolder()->remove('correspondencia');
    $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
        
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
  
  public function executeReporteGlobal(sfWebRequest $request)
  {
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
                    $vacaciones[$funcionario->getId()] = Doctrine::getTable('Rrhh_Vacaciones')->vacacionesPersonal($funcionario->getId());
                }
            }
        }

        $this->unidades = $unidades;
        $this->funcionarios = $funcionarios;
        $this->vacaciones = $vacaciones;
    } else {
        echo "Error."; exit();
    }
  }

  public function executeEditarVacaciones(sfWebRequest $request)
  {
    $funcionario_id = $request->getParameter('funcionario_id');
    
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    
    $this->funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($funcionario_id);
    $this->cargos = Doctrine::getTable('Funcionarios_FuncionarioCargo')->historicoCargosFuncionario($funcionario_id);
    $this->vacaciones = Doctrine::getTable('Rrhh_Vacaciones')->vacacionesPersonal($funcionario_id);
  }
  
  public function executeGuardarVacaciones(sfWebRequest $request)
  {
    $funcionario_id = $request->getParameter('funcionario_id');
    $periodos = $request->getParameter('periodos');
    
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    //VALIDAR QUE EL FUNCIONARIO PUEDA EDITAR
    $conn = Doctrine_Manager::connection();
    $conn->beginTransaction();
    
    foreach ($periodos as $periodo => $dias_pendientes) {
        if($dias_pendientes>0) $status = 'D'; else $status = 'F';
        $q = Doctrine_Query::create($conn);
        $q->update('Rrhh_Vacaciones')
            ->set('status', '?', $status)
            ->set('dias_disfrute_pendientes', '?', $dias_pendientes)
            ->where('funcionario_id = ?', $funcionario_id)
            ->andWhere('periodo_vacacional = ?', $periodo)
            ->execute();  
    }
    $conn->commit();
    
    $this->vacaciones = Doctrine::getTable('Rrhh_Vacaciones')->vacacionesPersonal($funcionario_id);
  }
  
  public function executeSeguimientoSolicitud(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

    $this->getUser()->setAttribute('correspondencia_grupo', $correspondencia->get('grupo_correspondencia'));

    $this->redirect(sfConfig::get('sf_app_correspondencia_url').'seguimiento/index');
  }
  
  public function executeGenerarVacacionesGlobales(sfWebRequest $request)
  {
    $count_vacaciones_generadas = 0;
    $count_funcionarios = 0;
    $count_vacaciones_existentes = 0;
    
    $funcionarios_cargos = Doctrine_Query::create()
            ->select('f.id, f.sexo, fc.id as funcionario_cargo_id, c.id as cargo_id, c.cargo_condicion_id as cargo_condicion_id')
            ->addSelect("(SELECT MIN(fc2.f_ingreso) 
                          FROM Funcionarios_FuncionarioCargo fc2
                          WHERE fc2.funcionario_id = f.id and fc2.f_ingreso <> '1900-01-01') AS f_ingreso")
            ->from('Funcionarios_Funcionario f')
            ->innerJoin('f.Funcionarios_FuncionarioCargo fc')
            ->innerJoin('fc.Organigrama_Cargo c')
            ->where('f.status = ?','A')
            ->andWhere('f.ci = ?',$request->getParameter('ci'))
            ->andWhere('fc.status = ?','A')
            ->andWhere('fc.f_ingreso <> ?','1900-01-01')
            ->orderBy('f.id')
            ->execute();
    
    if(count($funcionarios_cargos)>0){
        foreach ($funcionarios_cargos as $funcionario_cargo) {
            if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$funcionario_cargo->getCi().'.jpg')){
                $foto = "/images/fotos_personal/".$funcionario_cargo->getCi().".jpg";
            } else { 
                $foto = "/images/other/siglas_photo_small_".$funcionario_cargo->getSexo().substr($funcionario_cargo->getCi(), -1).".png";
            } 

            echo '<table width="100%">
                    <tr>
                        <td width="55">
                            <img src="'.$foto.'" width="50"/>
                        </td>
                        <td>
                            <b>CI:'.$funcionario_cargo->getCi().' - '.$funcionario_cargo->getPrimerNombre().' '.$funcionario_cargo->getPrimerApellido().'</b><br/>
                            Fecha de Ingreso: '.date('d-m-Y', strtotime($funcionario_cargo->getFIngreso())).'<br/>
                        </td>
                    </tr>
                  </table>';
            $configuraciones_vacaciones = Doctrine_Query::create()
                                        ->select('ca.*')
                                        ->from('Rrhh_Configuraciones ca')
                                        ->where('ca.modulo = ?', 'vacaciones')
                                        ->andWhere('ca.indexado LIKE ?', '%condicion:['.$funcionario_cargo->getCargoCondicionId().']%')
                                        ->execute();

            list($anio_ingreso,$mes_ingreso,$dia_ingreso) = explode("-",$funcionario_cargo->getFIngreso());

            $anio_actual = date('Y');
            $anio_ingreso++; $anios_laborales=1;

            for($i=$anio_ingreso;$i<=$anio_actual;$i++){
                $f_cumplimiento = $i.'-'.$mes_ingreso.'-'.$dia_ingreso;

                if(date("md") < $mes_ingreso.$dia_ingreso) $edad = $anio_actual-$i-1; else $edad = $anio_actual-$i;
                $periodo = $i-1; $periodo .= '-'.$i;

                if($edad > -1){
                    $configuracion_valida = null;
                    foreach ($configuraciones_vacaciones as $configuracion_vacaciones) {
                        $parametros_vacaciones = sfYaml::load($configuracion_vacaciones->getParametros());

                        if((strtotime($f_cumplimiento) >= strtotime($parametros_vacaciones['f_inicial_configuracion'])) &&
                           (strtotime($f_cumplimiento) <= strtotime($parametros_vacaciones['f_final_configuracion'])))
                                $configuracion_valida = $configuracion_vacaciones; 
                    }

                    if($configuracion_valida!=null){
                        $parametros_validos = sfYaml::load($configuracion_valida->getParametros());

                        $dias_asignados = $parametros_validos['dias_asignados_anio'];

                        $dias_adicionales = 0;
                        if($anios_laborales>$parametros_validos['dias_adicionales_anio_inicio'])
                            $dias_adicionales = $parametros_validos['dias_adicionales_anio']*$anios_laborales;
                        if($anios_laborales>$parametros_validos['dias_adicionales_anio_final'])
                            $dias_adicionales = $parametros_validos['dias_adicionales_anio']*$parametros_validos['dias_adicionales_anio_final'];

                        $dias_totales = $dias_asignados + $dias_adicionales;

                        $vacacion_existente = Doctrine::getTable('Rrhh_Vacaciones')->findOneByFuncionarioIdAndPeriodoVacacional($funcionario_cargo->getId(),$periodo);

                        if(!$vacacion_existente){
                            $vacaciones = new Rrhh_Vacaciones();
                            $vacaciones->setConfiguracionesVacacionesId($configuracion_valida->getId());
                            $vacaciones->setFuncionarioId($funcionario_cargo->getId());
                            $vacaciones->setFCumplimiento($f_cumplimiento);
                            $vacaciones->setPeriodoVacacional($periodo);
                            $vacaciones->setAniosLaborales($anios_laborales);
                            $vacaciones->setDiasDisfruteEstablecidos($dias_asignados);
                            $vacaciones->setDiasDisfruteAdicionales($dias_adicionales);
                            $vacaciones->setDiasDisfruteTotales($dias_totales);
                            $vacaciones->setDiasDisfrutePendientes(0); // SIN DIAS PENDIENTES. ESTO SE ARREGLA AL ACTUALIZAR POR QUERYS
                            $vacaciones->setPagadas(false);
                            $vacaciones->setMontoAbonadoConcepto(0);
                            $vacaciones->setStatus('F'); // CULMINADAS

                            $vacaciones->save();

                            echo "<fond style='color: blue;'>Creado periodo vacacional ".$periodo." cumplido el ".date('d-m-Y', strtotime($f_cumplimiento))."</fond><br/>";

                            $count_vacaciones_generadas++;
                        } else {
                            echo "<fond style='color: red;'>Ya existia el periodo vacacional ".$periodo." cumplido el ".date('d-m-Y', strtotime($f_cumplimiento))."</fond><br/>";
                            $count_vacaciones_existentes++;
                        }


                    }
                }
                $anios_laborales++;
            } 
            $count_funcionarios++;
        }
    } else {
        $funcionario_sin_cargo = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($request->getParameter('ci'));
        
        if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$funcionario_sin_cargo->getCi().'.jpg')){
            $foto = "/images/fotos_personal/".$funcionario_sin_cargo->getCi().".jpg";
        } else { 
            $foto = "/images/other/siglas_photo_small_".$funcionario_sin_cargo->getSexo().substr($funcionario_sin_cargo->getCi(), -1).".png";
        } 
        
        echo '<table width="100%">
                <tr>
                    <td width="55">
                        <img src="'.$foto.'" width="50"/>
                    </td>
                    <td>
                        <b>CI:'.$funcionario_sin_cargo->getCi().' - '.$funcionario_sin_cargo->getPrimerNombre().' '.$funcionario_sin_cargo->getPrimerApellido().'</b><br/>
                        Fecha de Ingreso: <font class="rojo">NO TIENE NINGUN CARGO ACTIVO</font><br/>
                    </td>
                </tr>
              </table>';
    }
    
    echo '<br/>Se crearon '.$count_vacaciones_generadas.' periodos vacacionales. Ya existian '.$count_vacaciones_existentes.' periodos vacacionales creados<hr/>';
    
    exit();
  }
  
  public function executeGenerarPeriodoAdelantado(sfWebRequest $request)
  {
    $funcionario_id = $request->getParameter('funcionario_id');
    $count_vacaciones_generadas = 0;
    $count_funcionarios = 0;
    $count_vacaciones_existentes = 0;
    
    $funcionarios_cargos = Doctrine_Query::create()
            ->select('f.id,fc.id as funcionario_cargo_id, c.id as cargo_id, c.cargo_condicion_id as cargo_condicion_id')
            ->addSelect("(SELECT MIN(fc2.f_ingreso) 
                          FROM Funcionarios_FuncionarioCargo fc2
                          WHERE fc2.funcionario_id = f.id and fc2.f_ingreso <> '1900-01-01') AS f_ingreso")
            ->from('Funcionarios_Funcionario f')
            ->innerJoin('f.Funcionarios_FuncionarioCargo fc')
            ->innerJoin('fc.Organigrama_Cargo c')
            ->where('f.status = ?','A')
            ->andWhere('f.id = ?',$funcionario_id)
            ->andWhere('fc.status = ?','A')
            ->andWhere('fc.f_ingreso <> ?','1900-01-01')
            ->orderBy('f.id')
            ->execute();
    
    foreach ($funcionarios_cargos as $funcionario_cargo) {
        $configuraciones_vacaciones = Doctrine_Query::create()
                                    ->select('ca.*')
                                    ->from('Rrhh_Configuraciones ca')
                                    ->where('ca.modulo = ?', 'vacaciones')
                                    ->andWhere('ca.indexado LIKE ?', '%condicion:['.$funcionario_cargo->getCargoCondicionId().']%')
                                    ->execute();
        
        list($anio_ingreso,$mes_ingreso,$dia_ingreso) = explode("-",$funcionario_cargo->getFIngreso());
        
        $anio_actual = date('Y')+1;
        $anio_ingreso++; $anios_laborales=1;
        
        for($i=$anio_ingreso;$i<=$anio_actual;$i++){
            $f_cumplimiento = $i.'-'.$mes_ingreso.'-'.$dia_ingreso;
            
            if(date("md") < $mes_ingreso.$dia_ingreso) $edad = $anio_actual-$i-1; else $edad = $anio_actual-$i;
            $periodo = $i-1; $periodo .= '-'.$i;
            
            if($edad > -1){
                $configuracion_valida = null;
                foreach ($configuraciones_vacaciones as $configuracion_vacaciones) {
                    $parametros_vacaciones = sfYaml::load($configuracion_vacaciones->getParametros());
                    
                    if((strtotime($f_cumplimiento) >= strtotime($parametros_vacaciones['f_inicial_configuracion'])) &&
                       (strtotime($f_cumplimiento) <= strtotime($parametros_vacaciones['f_final_configuracion'])))
                            $configuracion_valida = $configuracion_vacaciones; 
                }
        
                if($configuracion_valida!=null){
                    $parametros_validos = sfYaml::load($configuracion_valida->getParametros());

                    $dias_asignados = $parametros_validos['dias_asignados_anio'];
                    
                    $dias_adicionales = 0;
                    if($anios_laborales>$parametros_validos['dias_adicionales_anio_inicio'])
                        $dias_adicionales = $parametros_validos['dias_adicionales_anio']*$anios_laborales;
                    if($anios_laborales>$parametros_validos['dias_adicionales_anio_final'])
                        $dias_adicionales = $parametros_validos['dias_adicionales_anio']*$parametros_validos['dias_adicionales_anio_final'];
                    
                    $dias_totales = $dias_asignados + $dias_adicionales;
                    
                    $vacacion_existente = Doctrine::getTable('Rrhh_Vacaciones')->findOneByFuncionarioIdAndPeriodoVacacional($funcionario_cargo->getId(),$periodo);
                    
                    if(!$vacacion_existente){
                        $vacaciones = new Rrhh_Vacaciones();
                        $vacaciones->setConfiguracionesVacacionesId($configuracion_valida->getId());
                        $vacaciones->setFuncionarioId($funcionario_cargo->getId());
                        $vacaciones->setFCumplimiento($f_cumplimiento);
                        $vacaciones->setPeriodoVacacional($periodo);
                        $vacaciones->setAniosLaborales($anios_laborales);
                        $vacaciones->setDiasDisfruteEstablecidos($dias_asignados);
                        $vacaciones->setDiasDisfruteAdicionales($dias_adicionales);
                        $vacaciones->setDiasDisfruteTotales($dias_totales);
                        $vacaciones->setDiasDisfrutePendientes($dias_asignados); // SIN DIAS PENDIENTES. ESTO SE ARREGLA AL ACTUALIZAR POR QUERYS
                        $vacaciones->setPagadas(false);
                        $vacaciones->setMontoAbonadoConcepto(0);
                        $vacaciones->setStatus('D'); // CULMINADAS

                        $vacaciones->save();
                        
                        $count_vacaciones_generadas++;
                    } else {
                        $count_vacaciones_existentes++;
                    }
        
                    
                }
            }
            $anios_laborales++;
        } 
        $count_funcionarios++;
    }
    
    $this->vacaciones = Doctrine::getTable('Rrhh_Vacaciones')->vacacionesPersonal($funcionario_id);
    $this->setTemplate('guardarVacaciones');
  }
  
  public function executeEliminarUltimoPeriodo(sfWebRequest $request)
  {
      $funcionario_id = $request->getParameter('funcionario_id');
      
      $ultimo_periodo = Doctrine::getTable('Rrhh_Vacaciones')->ultimoPeriodo($funcionario_id);

      if(isset($ultimo_periodo[0][0])) {
            $solicitud= Doctrine::getTable('Rrhh_VacacionesDisfrutadas')->findByVacacionesId($ultimo_periodo[0][0]);

            if(count($solicitud)== 0) {
                $delete_periodo = Doctrine::getTable('Rrhh_Vacaciones')
                      ->createQuery()
                      ->delete()
                      ->where('id = ?', $ultimo_periodo[0][0])
                      ->execute();
            }else {
                echo 'error';
                exit;
            }
      }
      
      $this->vacaciones = Doctrine::getTable('Rrhh_Vacaciones')->vacacionesPersonal($funcionario_id);
      $this->setTemplate('guardarVacaciones');
  }
}

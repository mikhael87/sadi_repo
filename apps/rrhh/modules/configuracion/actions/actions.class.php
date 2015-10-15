<?php

/**
 * configuracion actions.
 *
 * @package    siglas
 * @subpackage configuracion
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class configuracionActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      $this->opcion = $request->getParameter('opcion');
  }
  
  public function executeOpciones(sfWebRequest $request)
  {
      $opcion = $request->getParameter('opcion');
      $this->setTemplate($opcion);
  }
  
  public function executeSaveVacaciones(sfWebRequest $request)
  {
    $datos = $request->getParameter('datos');
    $condiciones = $datos['condicion'];
    unset($datos['condicion']);
    
    
    foreach ($condiciones as $condicion) {
        $datos['f_final_configuracion'] = '2038-01-01';
        $parametros = $datos;
        
        
        $configuracion_anterior = Doctrine_Query::create()
                                  ->select('ca.*')
                                  ->from('Rrhh_Configuraciones ca')
                                  ->where('ca.modulo = ?', 'vacaciones')
                                  ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion.']%')
                                  ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                  ->limit(1)
                                  ->execute();
        
        if($configuracion_anterior[0]->getId()){
            $parametros_anteriores = sfYaml::load($configuracion_anterior[0]->getParametros());
            $parametros_anteriores['f_final_configuracion'] = $datos['f_inicial_configuracion'];
            
            $indexado_anterior = 'condicion:['.$parametros_anteriores['condicion'].']; f_inicial_configuracion:['.$parametros_anteriores['f_inicial_configuracion'].']; f_final_configuracion:['.$parametros_anteriores['f_final_configuracion'].']';
                        
            $parametros_anteriores = sfYAML::dump($parametros_anteriores);
            
            $configuracion_anterior[0]->setParametros($parametros_anteriores);
            $configuracion_anterior[0]->setIndexado($indexado_anterior);
            
            $configuracion_anterior[0]->save();
        }

        $indexado = 'condicion:['.$condicion.']; f_inicial_configuracion:['.$datos['f_inicial_configuracion'].']; f_final_configuracion:[2038-01-01]';
        
        $parametros['condicion'] = $condicion;
        $parametros_yml = sfYAML::dump($parametros);
        
        $configuracion_vacaciones = new Rrhh_Configuraciones();
        $configuracion_vacaciones->setModulo('vacaciones');
        $configuracion_vacaciones->setParametros($parametros_yml);
        $configuracion_vacaciones->setIndexado($indexado);
        $configuracion_vacaciones->save();
    }

//    echo "<pre>";
//    echo $cadena;
//    print_r($datos);
//    exit();
    
    $this->getUser()->setFlash('notice', ' Configuración de vacaciones creada con exito.');
    $this->redirect('configuracion/index?opcion=vacaciones');
  }  
  
  public function executeSaveReposos(sfWebRequest $request)
  {
      $datos = $request->getParameter('datos');
      $condiciones = $datos['condicion'];
      unset($datos['condicion']);
      
//      $i=0;
//      foreach ($datos['tipos_reposo'] as $tipo_reposo) {
//          $tipos_reposo[$i] = $tipo_reposo;
//          $i++;
//      }
      
    foreach ($condiciones as $condicion) {
        $datos['f_final_configuracion'] = '2038-01-01';
        $parametros = $datos;
        
        
        $configuracion_anterior = Doctrine_Query::create()
                                  ->select('ca.*')
                                  ->from('Rrhh_Configuraciones ca')
                                  ->where('ca.modulo = ?', 'reposos')
                                  ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion.']%')
                                  ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                  ->limit(1)
                                  ->execute();
        
        if($configuracion_anterior[0]->getId()){
            $parametros_anteriores = sfYaml::load($configuracion_anterior[0]->getParametros());
            $parametros_anteriores['f_final_configuracion'] = $datos['f_inicial_configuracion'];
            
            $indexado_anterior = 'condicion:['.$parametros_anteriores['condicion'].']; f_inicial_configuracion:['.$parametros_anteriores['f_inicial_configuracion'].']; f_final_configuracion:['.$parametros_anteriores['f_final_configuracion'].']';
                        
            $parametros_anteriores = sfYAML::dump($parametros_anteriores);
            
            $configuracion_anterior[0]->setParametros($parametros_anteriores);
            $configuracion_anterior[0]->setIndexado($indexado_anterior);
            
            $configuracion_anterior[0]->save();
        }

        $indexado = 'condicion:['.$condicion.']; f_inicial_configuracion:['.$datos['f_inicial_configuracion'].']; f_final_configuracion:[2038-01-01]';
        
        $parametros['condicion'] = $condicion;
        $parametros_yml = sfYAML::dump($parametros);
        
        $configuracion_vacaciones = new Rrhh_Configuraciones();
        $configuracion_vacaciones->setModulo('reposos');
        $configuracion_vacaciones->setParametros($parametros_yml);
        $configuracion_vacaciones->setIndexado($indexado);
        $configuracion_vacaciones->save();
    }

//    echo "<pre>";
//    echo $cadena;
//    print_r($datos);
//    exit();
    
    $this->getUser()->setFlash('notice', ' Configuración de reposos creada con exito.');
    $this->redirect('configuracion/index?opcion=reposos');
  }
  
  public function executeSavePermisos(sfWebRequest $request)
  {
    $datos = $request->getParameter('datos');
    $condiciones = $datos['condicion'];
    unset($datos['condicion']);
    
//      $i=0;
//      foreach ($datos['tipos_permiso'] as $tipo_permiso) {
//          $tipos_permiso[$i] = $tipo_permiso;
//          $i++;
//      }
      
    foreach ($condiciones as $condicion) {
        $datos['f_final_configuracion'] = '2038-01-01';
        $parametros = $datos;
        
        
        $configuracion_anterior = Doctrine_Query::create()
                                  ->select('ca.*')
                                  ->from('Rrhh_Configuraciones ca')
                                  ->where('ca.modulo = ?', 'permisos')
                                  ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion.']%')
                                  ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                  ->limit(1)
                                  ->execute();
        
        if($configuracion_anterior[0]->getId()){
            $parametros_anteriores = sfYaml::load($configuracion_anterior[0]->getParametros());
            $parametros_anteriores['f_final_configuracion'] = $datos['f_inicial_configuracion'];
            
            $indexado_anterior = 'condicion:['.$parametros_anteriores['condicion'].']; f_inicial_configuracion:['.$parametros_anteriores['f_inicial_configuracion'].']; f_final_configuracion:['.$parametros_anteriores['f_final_configuracion'].']';
                        
            $parametros_anteriores = sfYAML::dump($parametros_anteriores);
            
            $configuracion_anterior[0]->setParametros($parametros_anteriores);
            $configuracion_anterior[0]->setIndexado($indexado_anterior);
            
            $configuracion_anterior[0]->save();
        }

        $indexado = 'condicion:['.$condicion.']; f_inicial_configuracion:['.$datos['f_inicial_configuracion'].']; f_final_configuracion:[2038-01-01]';
        
        $parametros['condicion'] = $condicion;
        $parametros_yml = sfYAML::dump($parametros);
        
        $configuracion_vacaciones = new Rrhh_Configuraciones();
        $configuracion_vacaciones->setModulo('permisos');
        $configuracion_vacaciones->setParametros($parametros_yml);
        $configuracion_vacaciones->setIndexado($indexado);
        $configuracion_vacaciones->save();
    }

//    echo "<pre>";
//    echo $cadena;
//    print_r($datos);
//    exit();
    
    $this->getUser()->setFlash('notice', ' Configuración de permisos creada con exito.');
    $this->redirect('configuracion/index?opcion=permisos');
  }  
  
  public function executeMigrarFIngresoExcel(sfWebRequest $request)
  {
      $this->setTemplate('migracion/migrarFIngresoExcel');
  }
  
  public function executeMigrarFIngresoRevision(sfWebRequest $request)
  {
    foreach ($request->getFiles() as $file)
    $datos='';
        
    if ((!empty($file)) && ($file['error'] == 0)) {
        $userfile_name = $file['name'];
        $userfile_tmp = $file['tmp_name'];
        $userfile_size = $file['size'];
        $userfile_type = $file['type'];
        $filename = basename($file['name']);

        $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
        
        if ($file_ext == 'xls' || $file_ext == 'xlsx'){
            $dir_file = sfConfig::get('sf_upload_dir') . '/excel/migracion_fechas_ingreso_'.date('Y-m-d_h-i-s').'.'.$file_ext;
            move_uploaded_file($userfile_tmp, $dir_file);

            require_once 'PHPExcel/IOFactory.php';
            $objPHPExcel = PHPExcel_IOFactory::load($dir_file, $nocheck = true);
            $datos = array();

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($row = 1; $row <= $highestRow; ++$row) {                    
                    $cell = $worksheet->getCellByColumnAndRow(0, $row);
                    $datos[$row]['cedula'] = trim(str_replace(' ', '', strtoupper($cell->getValue())));
                    $datos[$row]['cedula'] = str_replace('.', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = str_replace('-', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = str_replace('V', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = str_replace('E', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = (int)$datos[$row]['cedula'];

                    $datos[$row]['cedula_error']='';
                    if($datos[$row]['cedula']<100000){
                        $datos[$row]['cedula_error'] = 'parece que la cedula tiene errores';
                    }
                    if($datos[$row]['cedula'] == ''){
                        $datos[$row]['cedula_error'] = 'campo requerido';
                    } else {
                         $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($datos[$row]['cedula']);
                         
                         if($funcionario){
                             $datos[$row]['nombre_apellido'] = $funcionario->getPrimerNombre().' '.$funcionario->getPrimerApellido();
                         } else {
                             $datos[$row]['cedula_error'] = 'cedula no encontrada';
                         }
                        
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(1, $row);
                    $datos[$row]['f_ingreso'] = strtoupper(trim($cell->getValue()));
                    $datos[$row]['f_ingreso'] = str_replace('/', '-', $datos[$row]['f_ingreso']);
                    $datos[$row]['f_ingreso'] = str_replace('.', '-', $datos[$row]['f_ingreso']);
                    
                    $datos[$row]['f_ingreso_error']='';
                    
                    @list($dia,$mes,$anio) = explode('-', $datos[$row]['f_ingreso']);
                    if(!checkdate($mes, $dia, $anio)){
                        $datos[$row]['f_ingreso_error'] = 'La fecha contiene errores';
                    }
                }
                // SOLO LEERA UNA SOLA HOJA DEL ARCHIVO DE CALCULO
                break;
            }
        }else{
            $this->getUser()->setFlash('error', 'El archivo que ha cargado no es de extensión xls o xlsx');
            $this->redirect('configuracion/migrarFIngresoExcel');
        }
    }
    
    if($datos==''){
        $this->getUser()->setFlash('error', 'No se pudo procesar el archivo, por favor reinicie el proceso.');
        $this->redirect('configuracion/migrarFIngresoExcel');
    } else {
        $this->datos = $datos;
    }
    
    $this->setTemplate('migracion/migrarFIngresoRevision');
  }
  
  public function executeMigrarFIngresoPreview(sfWebRequest $request)
  {
    $datos='';
    if($request->getParameter('datos')){
      $datos = $request->getParameter('datos');
    }

    if($datos==''){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('configuracion/migrarFIngresoExcel');
    } else {
        $this->datos = $datos;
        $this->getUser()->setAttribute('f_ingreso_migracion',$datos);
    }

    $this->setTemplate('migracion/migrarFIngresoPreview');
  }
  
  public function executeMigrarFIngresoMigrar(sfWebRequest $request)
  {
    $datos='';
    if($request->getParameter('datos')){
      $datos = $request->getParameter('datos');
    }

    if(!$this->getUser()->getAttribute('f_ingreso_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('configuracion/migrarFIngresoExcel');
    } else {
        $datos = $this->getUser()->getAttribute('f_ingreso_migracion');
    }

    foreach ($datos as $dato) {
        // BUSCAR FUNCIONARIO POR CEDULA
        $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($dato['cedula']);
        
        if($funcionario){
            $conn = Doctrine_Manager::connection();

            try
            {
                $conn->beginTransaction();
                
                $funcionario_cargo = Doctrine_Query::create()
                        ->select('fc.*')
                        ->from('Funcionarios_FuncionarioCargo fc')
                        ->where('fc.funcionario_id = ?',$funcionario->getId())
                        ->orderBy('fc.created_at')
                        ->limit(1)
                        ->execute();
    
                $funcionario_cargo[0]->setFIngreso($dato['f_ingreso']);
                $funcionario_cargo[0]->setStatus($funcionario_cargo[0]->getStatus());
                $funcionario_cargo[0]->save();
    
                
                $conn->commit();
            } catch (Doctrine_Validator_Exception $e) {
                $conn->rollBack();

                $this->getUser()->setFlash('error', $e);
            }
        }
    }
    
    
    
    echo "Fechas de ingreso actualizadas con exito";
    exit();
  }
  
  
  
  
  
  
  
  
  
  
  
  public function executeMigrarDiasDisponiblesExcel(sfWebRequest $request)
  {
      $this->setTemplate('migracion/migrarDiasDisponiblesExcel');
  }
  
  public function executeMigrarDiasDisponiblesRevision(sfWebRequest $request)
  {
    foreach ($request->getFiles() as $file)
    $datos='';
        
    if ((!empty($file)) && ($file['error'] == 0)) {
        $userfile_name = $file['name'];
        $userfile_tmp = $file['tmp_name'];
        $userfile_size = $file['size'];
        $userfile_type = $file['type'];
        $filename = basename($file['name']);

        $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
        
        if ($file_ext == 'xls' || $file_ext == 'xlsx'){
            $dir_file = sfConfig::get('sf_upload_dir') . '/excel/migracion_vacaciones_dias_disponibles_'.date('Y-m-d_h-i-s').'.'.$file_ext;
            move_uploaded_file($userfile_tmp, $dir_file);

            require_once 'PHPExcel/IOFactory.php';
            $objPHPExcel = PHPExcel_IOFactory::load($dir_file, $nocheck = true);
            $datos = array();

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($row = 1; $row <= $highestRow; ++$row) {                    
                    $cell = $worksheet->getCellByColumnAndRow(0, $row);
                    $datos[$row]['cedula'] = trim(str_replace(' ', '', strtoupper($cell->getValue())));
                    $datos[$row]['cedula'] = str_replace('.', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = str_replace('-', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = str_replace('V', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = str_replace('E', '', $datos[$row]['cedula']);
                    $datos[$row]['cedula'] = (int)$datos[$row]['cedula'];

                    $datos[$row]['cedula_error']='';
                    if($datos[$row]['cedula']<100000){
                        $datos[$row]['cedula_error'] = 'parece que la cedula tiene errores';
                    }
                    if($datos[$row]['cedula'] == ''){
                        $datos[$row]['cedula_error'] = 'campo requerido';
                    }
                    
                    
                    
                    $cell = $worksheet->getCellByColumnAndRow(1, $row);
                    $datos[$row]['periodo_vacacional'] = strtoupper(trim($cell->getValue()));
                    $datos[$row]['periodo_vacacional'] = str_replace(' ', '-', $datos[$row]['periodo_vacacional']);
                    $datos[$row]['periodo_vacacional'] = str_replace('/', '-', $datos[$row]['periodo_vacacional']);
                    $datos[$row]['periodo_vacacional'] = str_replace('.', '-', $datos[$row]['periodo_vacacional']);
                    $datos[$row]['periodo_vacacional'] = str_replace('---', '-', $datos[$row]['periodo_vacacional']);
                    $datos[$row]['periodo_vacacional'] = str_replace('--', '-', $datos[$row]['periodo_vacacional']);
                    
                    $datos[$row]['periodo_vacacional_error']='';
                    
                    @list($anio_inicial,$anio_final) = explode('-', $datos[$row]['periodo_vacacional']);
                    $anio_inicial = (int)$anio_inicial;
                    $anio_final = (int)$anio_final;
                    
                    if(!is_int($anio_inicial) || 
                        !is_int($anio_final) || 
                        $anio_inicial < (date('Y')-65) || 
                        $anio_final > date('Y') || 
                        $anio_inicial > date('Y') || 
                        $anio_inicial >= $anio_final || 
                        $anio_final != $anio_inicial+1){
                        $datos[$row]['periodo_vacacional_error'] = 'existen alguno errores en el periodo vacacional';
                    }
                    
                    
                    
                    $cell = $worksheet->getCellByColumnAndRow(2, $row);
                    $datos[$row]['dias_disponibles'] = strtoupper(trim($cell->getValue()));
                    $datos[$row]['dias_disponibles'] = (int)$datos[$row]['dias_disponibles'];
                    
                    $datos[$row]['dias_disponibles_error']='';
                    
                    if($datos[$row]['dias_disponibles']==''){
                        $datos[$row]['dias_disponibles']=0;
                    }
                    
                    if(!is_int($datos[$row]['dias_disponibles'])){
                        $datos[$row]['dias_disponibles_error'] = 'solo se permiten números enteros';
                    }
                }
                // SOLO LEERA UNA SOLA HOJA DEL ARCHIVO DE CALCULO
                break;
            }
        }else{
            $this->getUser()->setFlash('error', 'El archivo que ha cargado no es de extensión xls o xlsx');
            $this->redirect('configuracion/migrarDiasDisponiblesExcel');
        }
    }
    
    if($datos==''){
        $this->getUser()->setFlash('error', 'No se pudo procesar el archivo, por favor reinicie el proceso.');
        $this->redirect('configuracion/migrarDiasDisponiblesExcel');
    } else {
        $this->datos = $datos;
    }
    
    $this->setTemplate('migracion/migrarDiasDisponiblesRevision');
  }
  
  public function executeMigrarDiasDisponiblesPreview(sfWebRequest $request)
  {
    $datos='';
    if($request->getParameter('datos')){
      $datos = $request->getParameter('datos');
    }

    if($datos==''){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('configuracion/migrarDiasDisponiblesExcel');
    } else {
        $this->datos = $datos;
        $this->getUser()->setAttribute('dias_disponibles_migracion',$datos);
    }

    $this->setTemplate('migracion/migrarDiasDisponiblesPreview');
  }
  
  public function executeMigrarDiasDisponiblesMigrar(sfWebRequest $request)
  {
    $datos='';
    if($request->getParameter('datos')){
      $datos = $request->getParameter('datos');
    }

    if(!$this->getUser()->getAttribute('dias_disponibles_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('configuracion/migrarDiasDisponiblesExcel');
    } else {
        $datos = $this->getUser()->getAttribute('dias_disponibles_migracion');
    }
    
    $cedulas = array();
    $funcionarios = Doctrine::getTable('Funcionarios_Funcionario')->findAll();
    foreach ($funcionarios as $funcionario) {
        $cedulas[$funcionario->getCi()] = $funcionario->getId();
    }

    foreach ($datos as $dato) {
        // BUSCAR FUNCIONARIO POR CEDULA
        $vacacion = Doctrine::getTable('Rrhh_Vacaciones')->findOneByFuncionarioIdAndPeriodoVacacional($cedulas[$dato['cedula']],$dato['periodo_vacacional']);
        
        if($vacacion){
            $dias_disponibles = (int)$dato['dias_disponibles'];
            $vacacion->setDiasDisfrutePendientes($dias_disponibles);
            
            if($dias_disponibles==0){
                $vacacion->setStatus('F');
            } else {
                $vacacion->setStatus('D');
            }
            
            $vacacion->save();
        }
    }
    
    
    
    echo "Dias disponibles actualizados con exito";
    exit();
  }
  
  public function executePrecargarDiasDisponiblesExcel(sfWebRequest $request) {
      $vacaciones = Doctrine_Query::create()
            ->select('v.funcionario_id, v.periodo_vacacional, v.dias_disfrute_pendientes')
            ->from('Rrhh_Vacaciones v')
            ->orderBy('v.funcionario_id, v.periodo_vacacional')
            ->execute();
      
      $cedulas = array();
      $funcionarios = Doctrine::getTable('Funcionarios_Funcionario')->findAll();
      foreach ($funcionarios as $funcionario) {
          $cedulas[$funcionario->getId()] = $funcionario->getCi();
      }
      
      $this->vacaciones = $vacaciones;
      $this->cedulas = $cedulas;
      
      
      $this->setLayout(false);
      $this->getResponse()->clearHttpHeaders();
      
      $this->setTemplate('migracion/precargarDiasDisponiblesExcel');
  }
}

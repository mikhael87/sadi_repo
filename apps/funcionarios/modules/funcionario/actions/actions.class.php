<?php

require_once dirname(__FILE__).'/../lib/funcionarioGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/funcionarioGeneratorHelper.class.php';

/**
 * funcionario actions.
 *
 * @package    sigla-(institution)
 * @subpackage funcionario
 * @author     Livio López. liviolopez@gmail.com. (058)426-511.42.50. Venezuela-Caracas
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class funcionarioActions extends autoFuncionarioActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->getUser()->getAttributeHolder()->remove('pae_funcionario_id');
    $this->getUser()->getAttributeHolder()->remove('digifirma_cambio');
    $this->getUser()->getAttributeHolder()->remove('foto_cambio');
    $this->getUser()->getAttributeHolder()->remove('funcionarios_migracion');
    
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
  
  public function executeInactivos(sfWebRequest $request)
  {
    $inactivo = $request->getParameter('inac');

    $this->getUser()->getAttributeHolder()->remove('func_inactivo');
    
    if($inactivo == 'true') {
        $this->getUser()->setAttribute('func_inactivo', TRUE);
    }
    
    $this->redirect('@funcionarios_funcionario');
  }
  
  public function executeAnular(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $cargo = Doctrine::getTable('Funcionarios_Funcionario')->find($id);
    $cargo->setStatus('I');
    $cargo->save();
    
    $this->getUser()->setFlash('notice', 'El funcionario ha sido anulado con exito, para reestablecerlo haga clic sobre "Funcionarios inactivos".');
    $this->redirect('@funcionarios_funcionario');
  }
  
  public function executeReactivar(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $cargo = Doctrine::getTable('Funcionarios_Funcionario')->find($id);
    $cargo->setStatus('A');
    $cargo->save();
    
    $this->getUser()->setFlash('notice', 'El funcionario ha sido reactivado con exito.');
    $this->redirect('@funcionarios_funcionario');
  }

  public function executePasswd(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByEnlaceIdAndUsuarioEnlaceId(1,$id);
    $usuario->setClave(crypt(strtolower($usuario->getNombre()),strtolower($usuario->getNombre())));
    $usuario->setStatus('R');
    $usuario->save();

    $this->getUser()->setFlash('notice', 'Contraseña reiniciada al mismo nombre de usuario con exito.');
    $this->redirect('funcionario/index');
  }
  
  public function executeDigiFirma(sfWebRequest $request)
  {
    $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($request->getParameter('id'));
    $this->getUser()->setAttribute('digifirma_cambio', $funcionario->getCi());

    $this->redirect(sfConfig::get('sf_app_funcionarios_url').'foto?from=digifirma');
  }
  
  public function executeGlobalEnable(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByEnlaceIdAndUsuarioEnlaceId(1,$id);
    $usuario->setAccesoGlobal(TRUE);
    $usuario->save();

    $this->getUser()->setFlash('notice', 'Se activo el acceso global del funcionario.');
    $this->redirect('funcionario/index');
  }
  
  public function executeGlobalDisable(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByEnlaceIdAndUsuarioEnlaceId(1,$id);
    $usuario->setAccesoGlobal(FALSE);
    $usuario->save();

    $this->getUser()->setFlash('notice', 'Se desactivo el acceso global del funcionario.');
    $this->redirect('funcionario/index');
  }

  public function executeActualizarInformacionInicialPersonal(sfWebRequest $request)
  {
    $datos_iniciales = $request->getParameter('datos_iniciales_personales');

    $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($this->getUser()->getAttribute('funcionario_id'));
    $funcionario->setCi($datos_iniciales['cedula']);
    $funcionario->setPrimerNombre($datos_iniciales['primer_nombre']);
    $funcionario->setSegundoNombre($datos_iniciales['segundo_nombre']);
    $funcionario->setPrimerApellido($datos_iniciales['primer_apellido']);
    $funcionario->setSegundoApellido($datos_iniciales['segundo_apellido']);
    $funcionario->setFNacimiento($datos_iniciales['f_nacimiento']['year'].'-'.$datos_iniciales['f_nacimiento']['month'].'-'.$datos_iniciales['f_nacimiento']['day']);
    $funcionario->setEstadoNacimientoId($datos_iniciales['estado_nacimiento']);
    $funcionario->setSexo($datos_iniciales['sexo']);
    $funcionario->setEdoCivil($datos_iniciales['estado_civil']);

    $funcionario->save();
    
    $this->getUser()->setFlash('notice', 'Gracias por actualizar tus datos.');
        
    $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
  }

  public function executeCargosf(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $this->getUser()->setAttribute('pae_funcionario_id', $id);

    $this->redirect('funcionario_cargo/index');
  }
  
  public function executeMigrarFuncionarios(sfWebRequest $request)
  {
      $this->getUser()->getAttributeHolder()->remove('funcionarios_migracion');
      $this->setTemplate('migracion/migrarFuncionarios');
  }
  
  public function executeMigrarFuncionariosDescargarExcel(sfWebRequest $request)
  {   
        $filepath = sfConfig::get("sf_root_dir")."/apps/funcionarios/modules/funcionario/lib/SIGLAS_formulario_migracion_funcionarios.xls";
        $filename = "SIGLAS_formulario_migracion_funcionarios.xls";
        $size = filesize($filepath);

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Content-Length: ".$size);

        $ok = fopen($filepath, 'rb');
        fpassthru($ok);

        exit();
  }
  
  public function executeMigrarFuncionariosExcel(sfWebRequest $request)
  {
      $this->setTemplate('migracion/migrarFuncionariosExcel');
  }
  
  public function executeMigrarFuncionariosRevision(sfWebRequest $request)
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
            $dir_file = sfConfig::get('sf_upload_dir') . '/excel/migracion_funcionarios_'.date('Y-m-d_h-i-s').'.'.$file_ext;
            move_uploaded_file($userfile_tmp, $dir_file);

            require_once 'PHPExcel/IOFactory.php';
            $objPHPExcel = PHPExcel_IOFactory::load($dir_file, $nocheck = true);
            $datos = array();

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($row = 1; $row <= $highestRow; ++$row) {                    
                    $cell = $worksheet->getCellByColumnAndRow(0, $row);
                    $datos[$row]['ubicacion'] = trim($cell->getValue());
                    $datos[$row]['ubicacion_error']='';
                    if($datos[$row]['ubicacion'] == ''){
                        $datos[$row]['ubicacion_error'] = 'campo requerido';
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(1, $row);
                    $datos[$row]['condicion_cargo'] = trim($cell->getValue());
                    $datos[$row]['condicion_cargo_error']='';
                    if($datos[$row]['condicion_cargo'] == ''){
                        $datos[$row]['condicion_cargo_error'] = 'campo requerido';
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(2, $row);
                    $datos[$row]['tipo_cargo'] = trim($cell->getValue());
                    $datos[$row]['tipo_cargo_error']='';
                    if($datos[$row]['tipo_cargo'] == ''){
                        $datos[$row]['tipo_cargo_error'] = 'campo requerido';
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(3, $row);
                    $datos[$row]['grado_cargo'] = (int)trim($cell->getValue());
                    $datos[$row]['grado_cargo_error']='';
                    if($datos[$row]['grado_cargo'] == ''){
                        $datos[$row]['grado_cargo'] = 0;
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(4, $row);
                    $datos[$row]['codigo_empleado'] = (int)trim($cell->getValue());
                    $datos[$row]['codigo_empleado_error']='';
                    if($datos[$row]['codigo_empleado'] == ''){
                        $datos[$row]['codigo_empleado'] = 0;
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(5, $row);
                    $datos[$row]['tipo_contrato'] = trim($cell->getValue());
                    $datos[$row]['tipo_contrato_error']='';
                    if($datos[$row]['tipo_contrato'] == ''){
                        $datos[$row]['tipo_contrato'] = 'Titular del Cargo';
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(6, $row);
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
                    
                    $cell = $worksheet->getCellByColumnAndRow(7, $row);
                    $datos[$row]['sexo'] = strtoupper(trim($cell->getValue()));
                    $datos[$row]['sexo_error']='';
                    if($datos[$row]['sexo']!='M' && $datos[$row]['sexo']!='F' && $datos[$row]['sexo']!=''){
                        $datos[$row]['sexo_error'] = 'el sexo no puede ser '.$datos[$row]['sexo'].' solo puede ser F=femenino o M=masculino';
                        $datos[$row]['sexo']='';
                    }else if($datos[$row]['sexo'] == ''){
                        $datos[$row]['sexo_error'] = 'campo requerido';
                    }
                    
                    $cell = $worksheet->getCellByColumnAndRow(8, $row);
                    $datos[$row]['estado_civil'] = strtoupper(trim($cell->getValue()));
                    $datos[$row]['estado_civil_error']='';
                    if($datos[$row]['estado_civil']!='S' && $datos[$row]['estado_civil']!='C' && $datos[$row]['estado_civil']!='D' && $datos[$row]['estado_civil']!='V'){
                        $datos[$row]['estado_civil']='S';
                    }
                }
                // SOLO LEERA UNA SOLA HOJA DEL ARCHIVO DE CALCULO
                break;
            }
        }else{
            $this->getUser()->setFlash('error', 'El archivo que ha cargado no es de extensión xls o xlsx');
            $this->redirect('funcionario/migrarFuncionariosExcel');
        }
    } else {
        
        if($request->getParameter('datos')){
            $datos = $request->getParameter('datos');
            $this->getUser()->setAttribute('funcionarios_migracion', $datos);
        } else {
            $datos = $this->getUser()->getAttribute('funcionarios_migracion');
        }
    }
    
    if($datos==''){
        $this->getUser()->setFlash('error', 'No se pudo procesar el archivo, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $this->datos = $datos;
    }
    
    $this->setTemplate('migracion/migrarFuncionariosRevision');
  }
  
  public function executeMigrarFuncionariosCotejarUnidades(sfWebRequest $request)
  {
    $datos='';
    if($request->getParameter('datos')){
      $datos = $request->getParameter('datos');
      $this->getUser()->setAttribute('funcionarios_migracion', $datos);
    } else {
      $datos = $this->getUser()->getAttribute('funcionarios_migracion');
    }

    if($datos==''){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $unidades_migradas = array();
        foreach ($datos as $dato) {
            if(!isset($unidades_migradas[$dato['ubicacion']])){
                $unidades_migradas[$dato['ubicacion']]=0;
            }
            $unidades_migradas[$dato['ubicacion']]++;
        }
    
        $this->unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad();
        $this->unidades_migradas = $unidades_migradas;
    }
    
    $this->setTemplate('migracion/migrarFuncionariosCotejarUnidades');
  }
  
  public function executeMigrarFuncionariosCotejarCondicionCargos(sfWebRequest $request)
  {
    $datos='';
    $unidades='';
    if($request->getParameter('unidades')){
      $unidades = $request->getParameter('unidades');
      $this->getUser()->setAttribute('funcionarios_migracion_unidades', $unidades);
    } else {
        if(!$this->getUser()->getAttribute('funcionarios_migracion_unidades')){
            $this->getUser()->setFlash('error', 'Se han perdido las unidades ya procesadas, disculpe el inconveniente, por favor reinicie el proceso de cotejamiento de unidades.');
            $this->redirect('funcionario/migrarFuncionariosCotejarUnidades');
        } else {
            $unidades = $this->getUser()->getAttribute('funcionarios_migracion_unidades');
        }
    }

    if(!$this->getUser()->getAttribute('funcionarios_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $datos = $this->getUser()->getAttribute('funcionarios_migracion');
    }
        
    foreach ($unidades as $unidad) {
        foreach ($datos as $id => $dato) {
            if($unidad['nombre_actual']==$dato['ubicacion']){
                $datos[$id]['unidad_id'] = $unidad['unidad_id'];
            }
        }
    }
    $this->getUser()->setAttribute('funcionarios_migracion', $datos);

    $condiciones_migradas = array();
    foreach ($datos as $dato) {
        if(!isset($condiciones_migradas[$dato['condicion_cargo']])){
            $condiciones_migradas[$dato['condicion_cargo']]=0;
        }
        $condiciones_migradas[$dato['condicion_cargo']]++;
    }

    $this->condiciones = Doctrine::getTable('Organigrama_CargoCondicion')->ordenado();
    $this->condiciones_migradas = $condiciones_migradas;

    $this->setTemplate('migracion/migrarFuncionariosCotejarCondicionCargos');
  }
  
  public function executeMigrarFuncionariosCotejarTipoCargos(sfWebRequest $request)
  {
    $datos='';
    $condiciones='';
    if($request->getParameter('condiciones')){
      $condiciones = $request->getParameter('condiciones');
      $this->getUser()->setAttribute('funcionarios_migracion_condiciones', $condiciones);
    } else {
        if(!$this->getUser()->getAttribute('funcionarios_migracion_condiciones')){
            $this->getUser()->setFlash('error', 'Se han perdido las condiciones del cargos ya procesadas, disculpe el inconveniente, por favor reinicie el proceso de cotejamiento de condiciones.');
            $this->redirect('funcionario/migrarFuncionariosCotejarUnidades');
        } else {
            $condiciones = $this->getUser()->getAttribute('funcionarios_migracion_condiciones');
        }
    }

    if(!$this->getUser()->getAttribute('funcionarios_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $datos = $this->getUser()->getAttribute('funcionarios_migracion');
    }
        
    foreach ($condiciones as $condicion) {
        foreach ($datos as $id => $dato) {
            if($condicion['nombre_actual']==$dato['condicion_cargo']){
                $datos[$id]['condicion_id'] = $condicion['condicion_id'];
            }
        }
    }
    $this->getUser()->setAttribute('funcionarios_migracion', $datos);

    $tipos_migradas = array();
    foreach ($datos as $dato) {
        if(!isset($tipos_migradas[$dato['condicion_id']])){
            $tipos_migradas[$dato['condicion_id']]=array();;
        }
        
        if(!isset($tipos_migradas[$dato['condicion_id']][$dato['tipo_cargo']])){
            $tipos_migradas[$dato['condicion_id']][$dato['tipo_cargo']]=0;
        }
        
        $tipos_migradas[$dato['condicion_id']][$dato['tipo_cargo']]++;
    }

    $condiciones = Doctrine::getTable('Organigrama_CargoCondicion')->ordenado();
    foreach ($condiciones as $condicion) {
        $tipos[$condicion->getId()] = array();
        $condiciones_db[$condicion->getId()] = $condicion->getNombre();
        
        $tipos_pre = Doctrine::getTable('Organigrama_CargoTipo')->filtrarCondicion($condicion->getId());
        foreach ($tipos_pre as $tipo_pre) {
            $tipos[$condicion->getId()][$tipo_pre->getId()] = $tipo_pre->getNombre();
        }
    }
    
    $this->condiciones = $condiciones_db;
    $this->tipos = $tipos;
    $this->tipos_migradas = $tipos_migradas;

    $this->setTemplate('migracion/migrarFuncionariosCotejarTipoCargos');
  }
  
  public function executeMigrarFuncionariosCotejarGradoCargos(sfWebRequest $request)
  {
    $datos='';
    $tipos='';
    if($request->getParameter('tipos')){
      $tipos = $request->getParameter('tipos');
      $this->getUser()->setAttribute('funcionarios_migracion_tipos', $tipos);
    } else {
        if(!$this->getUser()->getAttribute('funcionarios_migracion_tipos')){
            $this->getUser()->setFlash('error', 'Se han perdido los tipos del cargos ya procesadas, disculpe el inconveniente, por favor reinicie el proceso de cotejamiento de tipos.');
            $this->redirect('funcionario/migrarFuncionariosCotejarUnidades');
        } else {
            $tipos = $this->getUser()->getAttribute('funcionarios_migracion_tipos');
        }
    }

    if(!$this->getUser()->getAttribute('funcionarios_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $datos = $this->getUser()->getAttribute('funcionarios_migracion');
    }
     
    foreach ($tipos as $condicion_id => $tipos_pre) {
        foreach ($tipos_pre as $tipo) {
            foreach ($datos as $id => $dato) {
                if($tipo['nombre_actual']==$dato['tipo_cargo'] && $condicion_id==$dato['condicion_id']){
                    $datos[$id]['tipo_id'] = $tipo['tipo_id'];
                }
            }
        }
    }
    $this->getUser()->setAttribute('funcionarios_migracion', $datos);

    $grados_migradas = array();
    foreach ($datos as $dato) {
        if(!isset($grados_migradas[$dato['condicion_id']])){
            $grados_migradas[$dato['condicion_id']]=array();;
        }
        
        if(!isset($grados_migradas[$dato['condicion_id']][$dato['tipo_id']])){
            $grados_migradas[$dato['condicion_id']][$dato['tipo_id']]=array();;
        }
        
        if(!isset($grados_migradas[$dato['condicion_id']][$dato['tipo_id']][$dato['grado_cargo']])){
            $grados_migradas[$dato['condicion_id']][$dato['tipo_id']][$dato['grado_cargo']]=0;
        }
        
        $grados_migradas[$dato['condicion_id']][$dato['tipo_id']][$dato['grado_cargo']]++;
    }

    $condiciones = Doctrine::getTable('Organigrama_CargoCondicion')->ordenado();
    foreach ($condiciones as $condicion) {
        $condiciones_db[$condicion->getId()] = $condicion->getNombre();
        
        $tipos_pre = Doctrine::getTable('Organigrama_CargoTipo')->filtrarCondicion($condicion->getId());
        foreach ($tipos_pre as $tipo_pre) {
            $tipos_db[$tipo_pre->getId()] = $tipo_pre->getNombre();
            
            $grados_pre = Doctrine::getTable('Organigrama_CargoGradoTipo')->findByCargoTipoId($tipo_pre->getId());

            foreach ($grados_pre as $grado_pre) {
                $detalles_pre = Doctrine::getTable('Organigrama_CargoGrado')->find($grado_pre->getCargoGradoId());

                $grados[$condicion->getId()][$tipo_pre->getId()][$detalles_pre->getId()] = $detalles_pre->getNombre();
            }
        }
    }
    
    $this->condiciones = $condiciones_db;
    $this->tipos = $tipos_db;
    
    $this->grados = $grados;
    $this->grados_migradas = $grados_migradas;

    $this->setTemplate('migracion/migrarFuncionariosCotejarGradoCargos');
  }
  
  public function executeMigrarFuncionariosCotejarTipoContrato(sfWebRequest $request)
  {
    $datos='';
    $grados='';
    if($request->getParameter('grados')){
      $grados = $request->getParameter('grados');
      $this->getUser()->setAttribute('funcionarios_migracion_grados', $grados);
    } else {
        if(!$this->getUser()->getAttribute('funcionarios_migracion_grados')){
            $this->getUser()->setFlash('error', 'Se han perdido los grados del cargos ya procesadas, disculpe el inconveniente, por favor reinicie el proceso de cotejamiento de grados.');
            $this->redirect('funcionario/migrarFuncionariosCotejarUnidades');
        } else {
            $grados = $this->getUser()->getAttribute('funcionarios_migracion_grados');
        }
    }

    if(!$this->getUser()->getAttribute('funcionarios_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $datos = $this->getUser()->getAttribute('funcionarios_migracion');
    }
     
    foreach ($grados as $condicion_id => $tipos_pre) {
        foreach ($tipos_pre as $tipo_id => $grados_pre) {
            foreach ($grados_pre as $grado) {
                foreach ($datos as $id => $dato) {
                    if($grado['nombre_actual']==$dato['grado_cargo'] && $condicion_id==$dato['condicion_id'] && $tipo_id==$dato['tipo_id']){
                        $datos[$id]['grado_id'] = $grado['grado_id'];
                    }
                }
            }
        }
    }
    $this->getUser()->setAttribute('funcionarios_migracion', $datos);

    $tipo_contrato_migradas = array();
    foreach ($datos as $dato) {
        if(!isset($tipo_contrato_migradas[$dato['tipo_contrato']])){
            $tipo_contrato_migradas[$dato['tipo_contrato']]=0;
        }
        $tipo_contrato_migradas[$dato['tipo_contrato']]++;
    }

    $this->tipos_contrato = Doctrine::getTable('Funcionarios_FuncionarioCargoCondicion')->ordenado('ingreso');
    $this->tipos_contrato_migradas = $tipo_contrato_migradas;

    $this->setTemplate('migracion/migrarFuncionariosCotejarTipoContrato');
  }
  
  public function executeMigrarFuncionariosPreview(sfWebRequest $request)
  {
    $datos='';
    $tipos_contrato='';
    if($request->getParameter('tipos_contrato')){
      $tipos_contrato = $request->getParameter('tipos_contrato');
      $this->getUser()->setAttribute('funcionarios_migracion_tipos_contrato', $tipos_contrato);
    } else {
        if(!$this->getUser()->getAttribute('funcionarios_migracion_tipos_contrato')){
            $this->getUser()->setFlash('error', 'Se han perdido los tipos de contrato ya procesados, disculpe el inconveniente, por favor reinicie el proceso de cotejamiento de tipos de contrato.');
            $this->redirect('funcionario/migrarFuncionariosCotejarUnidades');
        } else {
            $tipos_contrato = $this->getUser()->getAttribute('funcionarios_migracion_tipos_contrato');
        }
    }

    if(!$this->getUser()->getAttribute('funcionarios_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $datos = $this->getUser()->getAttribute('funcionarios_migracion');
    }
        
    foreach ($tipos_contrato as $tipo_contrato) {
        foreach ($datos as $id => $dato) {
            if($tipo_contrato['nombre_actual']==$dato['tipo_contrato']){
                $datos[$id]['tipo_contrato_id'] = $tipo_contrato['tipo_contrato_id'];
            }
        }
    }
    $this->getUser()->setAttribute('funcionarios_migracion', $datos);
    
    $unidades_db = Doctrine::getTable('Organigrama_Unidad')->findAll();
    foreach ($unidades_db as $unidad_db) {
        $unidades[$unidad_db->getId()]=$unidad_db->getNombre();
    }

    $condiciones_db = Doctrine::getTable('Organigrama_CargoCondicion')->findAll();
    foreach ($condiciones_db as $condicion_db) {
        $condiciones[$condicion_db->getId()]=$condicion_db->getNombre();
    }
    
    $tipos_db = Doctrine::getTable('Organigrama_CargoTipo')->findAll();
    foreach ($tipos_db as $tipo_db) {
        $tipos[$tipo_db->getId()]=$tipo_db->getNombre();
    }
    
    $grados_db = Doctrine::getTable('Organigrama_CargoGrado')->findAll();
    foreach ($grados_db as $grado_db) {
        $grados[$grado_db->getId()]=$grado_db->getNombre();
    }
    
    $tipos_contrato_db = Doctrine::getTable('Funcionarios_FuncionarioCargoCondicion')->findAll();
    foreach ($tipos_contrato_db as $tipo_contrato_db) {
        $tipos_contrato[$tipo_contrato_db->getId()]=$tipo_contrato_db->getNombre();
    }
    
    $this->datos = $datos;

    $this->unidades = $unidades;
    $this->condiciones = $condiciones;
    $this->tipos = $tipos;
    $this->grados = $grados;
    $this->tipos_contrato = $tipos_contrato;

    $this->setTemplate('migracion/migrarFuncionariosPreview');
  }
  
  
  
  
  public function executeMigrarFuncionariosMigrar(sfWebRequest $request)
  {
    $datos='';
    $funcionarios_no_creados = 0;
    $funcionarios_creados = 0;
    $funcionarios_existentes = 0;
    $sf_seguridad = sfYaml::load(sfConfig::get('sf_root_dir') . "/config/siglas/seguridad.yml");

    if(!$this->getUser()->getAttribute('funcionarios_migracion')){
        $this->getUser()->setFlash('error', 'Se han perdido los datos ya procesados, disculpe el inconveniente, por favor reinicie el proceso.');
        $this->redirect('funcionario/migrarFuncionariosExcel');
    } else {
        $datos = $this->getUser()->getAttribute('funcionarios_migracion');
    }
    

    
    $unidades_db = Doctrine::getTable('Organigrama_Unidad')->findAll();
    foreach ($unidades_db as $unidad_db) {
        $unidades[$unidad_db->getId()]=$unidad_db->getNombre();
    }

    $condiciones_db = Doctrine::getTable('Organigrama_CargoCondicion')->findAll();
    foreach ($condiciones_db as $condicion_db) {
        $condiciones[$condicion_db->getId()]=$condicion_db->getNombre();
    }
    
    $tipos_db = Doctrine::getTable('Organigrama_CargoTipo')->findAll();
    foreach ($tipos_db as $tipo_db) {
        $tipos[$tipo_db->getId()]=$tipo_db->getNombre();
    }
    
    $grados_db = Doctrine::getTable('Organigrama_CargoGrado')->findAll();
    foreach ($grados_db as $grado_db) {
        $grados[$grado_db->getId()]=$grado_db->getNombre();
    }
    
    $tipos_contrato_db = Doctrine::getTable('Funcionarios_FuncionarioCargoCondicion')->findAll();
    foreach ($tipos_contrato_db as $tipo_contrato_db) {
        $tipos_contrato[$tipo_contrato_db->getId()]=$tipo_contrato_db->getNombre();
    }
    
    
    
    
    
    
    foreach ($datos as $dato) {
        // BUSCAR FUNCIONARIO POR CEDULA
        $funcionario_db = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($dato['cedula']);
        
        // SI NO EXISTE SE PROCEDE A CREAR EL FUNCIONARIO
        if(!$funcionario_db){
            
            // BUSCAR CEDULA EN LA BD DEL SAIME
            $result=NULL;

            if($sf_seguridad['conexion_saime']['activo']==true){
                try{
                    $manager = Doctrine_Manager::getInstance()
                            ->openConnection(
                            'pgsql' . '://' .
                            $sf_seguridad['conexion_saime']['username'] . ':' .
                            $sf_seguridad['conexion_saime']['password'] . '@' .
                            $sf_seguridad['conexion_saime']['host'] . ':'. $sf_seguridad['conexion_saime']['port'] .'/' .
                            $sf_seguridad['conexion_saime']['dbname'], 'dbSAIME');

                    $query = "SELECT ".$sf_seguridad['conexion_saime']['consulta']['campo_nacionalidad'].", 
                                     ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula'].",
                                     ".$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre'].",
                                     ".$sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre'].",
                                     ".$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido'].",
                                     ".$sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido'].",
                                     ".$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']."
                              FROM ".$sf_seguridad['conexion_saime']['consulta']['tabla']."
                              WHERE ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula']."=" . $dato['cedula'];

                    $result = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
                    Doctrine_Manager::getInstance()->closeConnection($manager);
                } catch (Exception $e) {}
            } else {
                $this->getUser()->setFlash('error', 'No ha podido procesarse la migración debido a que no se encontro activa una conexion con la base de datos SAIME.');
                $this->redirect('funcionario/migrarFuncionariosExcel');
            }

            if($result) {
                $conn = Doctrine_Manager::connection();

                try
                {
                    $conn->beginTransaction();
                    
                    // INICIO CREAR FUNCIONARIO
                    // INICIO CREAR FUNCIONARIO
                    // INICIO CREAR FUNCIONARIO

                    $funcionario_pre['primer_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
                    $funcionario_pre['segundo_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
                    $funcionario_pre['primer_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
                    $funcionario_pre['segundo_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));

                    $funcionario_pre['f_nacimiento_day'] = date("d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
                    $funcionario_pre['f_nacimiento_month'] = date("m", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
                    $funcionario_pre['f_nacimiento_year'] = date("Y", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));

                    $funcionario_pre['f_nacimiento'] = date("Y-m-d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));

                    $funcionario = new Funcionarios_Funcionario();
                    $funcionario->setCi($dato['cedula']);
                    $funcionario->setPrimerNombre($funcionario_pre['primer_nombre']);
                    $funcionario->setSegundoNombre($funcionario_pre['segundo_nombre']);
                    $funcionario->setPrimerApellido($funcionario_pre['primer_apellido']);
                    $funcionario->setSegundoApellido($funcionario_pre['segundo_apellido']);
                    $funcionario->setFNacimiento($funcionario_pre['f_nacimiento']);
                    $funcionario->setSexo($dato['sexo']);
                    $funcionario->setEdoCivil($dato['estado_civil']);
                    $funcionario->save();

                    // FIN CREAR FUNCIONARIO
                    // FIN CREAR FUNCIONARIO
                    // FIN CREAR FUNCIONARIO
                
                    // INICIO CREAR CARGO
                    // INICIO CREAR CARGO
                    // INICIO CREAR CARGO
                    
                    $cargo = new Organigrama_Cargo();
                    $cargo->setUnidadAdministrativaId($dato['unidad_id']);
                    $cargo->setUnidadFuncionalId($dato['unidad_id']);
                    $cargo->setCodigoNomina($dato['codigo_empleado']);
                    $cargo->setCargoCondicionId($dato['condicion_id']);
                    $cargo->setCargoTipoId($dato['tipo_id']);
                    $cargo->setCargoGradoId($dato['grado_id']);
                    $cargo->setDescripcion(' ');
                    $cargo->setFIngreso(date('Y-m-d'));
                    $cargo->setPerfilId(1);
                    $cargo->setStatus('O');
                    $cargo->save();
                    
                    // FIN CREAR CARGO
                    // FIN CREAR CARGO
                    // FIN CREAR CARGO
                    
                    // INICIO CREAR FUNCIONARIO_CARGO
                    // INICIO CREAR FUNCIONARIO_CARGO
                    // INICIO CREAR FUNCIONARIO_CARGO
                    
                    $funcionario_cargo = new Funcionarios_FuncionarioCargo();
                    $funcionario_cargo->setFuncionarioId($funcionario->getId());
                    $funcionario_cargo->setCargoId($cargo->getId());
                    $funcionario_cargo->setFIngreso(date('Y-m-d'));
                    $funcionario_cargo->setObservaciones(' ');
                    $funcionario_cargo->setFuncionarioCargoCondicionId($dato['tipo_contrato_id']);
                    $funcionario_cargo->save();
                    
                    // FIN CREAR FUNCIONARIO_CARGO
                    // FIN CREAR FUNCIONARIO_CARGO
                    // FIN CREAR FUNCIONARIO_CARGO
                    
                    
                    // INICIO CREAR USUARIO
                    // INICIO CREAR USUARIO
                    // INICIO CREAR USUARIO
                    
                    $nombre_usuario = new herramientas();
                    $nombre_usuario = $nombre_usuario->generarUsuario($funcionario->getPrimerNombre(), $funcionario->getSegundoNombre(), $funcionario->getPrimerApellido(), $funcionario->getSegundoApellido());

                    $usuario = new Acceso_Usuario();
                    $usuario->setUsuarioEnlaceId($funcionario->getId());
                    $usuario->setEnlace_id(1);
                    $usuario->setNombre(strtolower($nombre_usuario));
                    $usuario->setClave(crypt(strtolower($nombre_usuario),strtolower($nombre_usuario)));
                    $usuario->setVisitas(0);
                    $usuario->setUltimaconexion(date('Y-m-d h:i:s'));
                    $usuario->setUltimocambioclave(date('Y-m-d h:i:s'));
                    $usuario->setStatus('A');
                    $usuario->setAccesoGlobal(FALSE);
                    $usuario->setTema('estandar');
                    $usuario->save();

                    $enlace = Doctrine::getTable('Acceso_Enlace')->find(1);
                    $enlace->setTotal($enlace->get("total") + 1);
                    $enlace->save();

                    //ASIGNARLE EL PERFIL DEL CARGO AL FUNCIONARIO
                    $usuario_perfil = new Acceso_UsuarioPerfil();
                    $usuario_perfil->setUsuarioId($usuario->getId());
                    $usuario_perfil->setPerfilId($cargo->getPerfilId());
                    $usuario_perfil->setStatus('A');
                    $usuario_perfil->save();
                    
                    // FIN CREAR USUARIO
                    // FIN CREAR USUARIO
                    // FIN CREAR USUARIO

                    $funcionarios_creados++;
                    
                    echo "<b class='azul'>Funcionario creado:</b><br/>";
                    echo "<b>- Cedula:</b> ".$funcionario->getCi()."<br/>";
                    echo "<b>- Nombre:</b> ".$funcionario->getPrimerNombre()." ".$funcionario->getSegundoNombre()." ".$funcionario->getPrimerApellido()." ".$funcionario->getSegundoApellido()."<br/>";
                    echo "<b>- F. Nacimiento:</b> ".date("d-m-Y", strtotime($funcionario->getFNacimiento()))."<br/>";
                    echo "<b>- Sexo:</b> ".$funcionario->getSexo()."<br/>";
                    echo "<b>- Estado Civil:</b> ".$funcionario->getEdoCivil()."<br/>";
                    echo "<b>- Usuario:</b> ".$usuario->getNombre()."<br/><br/>";
                    echo "<b>- Unidad:</b> ".$unidades[$cargo->getUnidadAdministrativaId()]."<br/>";
                    echo "<b>- Cargo:</b> ".$condiciones[$cargo->getCargoCondicionId()]." / ".$tipos[$cargo->getCargoTipoId()]." / ".$grados[$cargo->getCargoGradoId()]."<br/>";
                    echo "<b>- Tipo de Contrato:</b> ".$tipos_contrato[$funcionario_cargo->getFuncionarioCargoCondicionId()]."<br/>";
                    echo "<b>- Codigo de empleado:</b> ".$cargo->getCodigoNomina()."<br/><hr/>";
                
                    $conn->commit();
                } catch (Doctrine_Validator_Exception $e) {
                    $conn->rollBack();

                    $this->getUser()->setFlash('error', $e);
                }
            } else {
                echo "<b class='rojo'>Funcionario no creado (No se encontro en la BD del SAIME):</b><br/>";
                echo "<b>- Cedula:</b> ".$dato['cedula']."<br/>";
                echo "<b>- Sexo:</b> ".$dato['sexo']."<br/>";
                echo "<b>- Estado Civil:</b> ".$dato['estado_civil']."<br/>";
                echo "<b>- Unidad:</b> ".$unidades[$dato['unidad_id']]."<br/>";
                echo "<b>- Cargo:</b> ".$condiciones[$dato['condicion_id']]." / ".$tipos[$dato['tipo_id']]." / ".$grados[$dato['grado_id']]."<br/>";
                echo "<b>- Tipo de Contrato:</b> ".$tipos_contrato[$dato['tipo_contrato_id']]."<br/>";
                echo "<b>- Codigo de empleado:</b> ".$dato['codigo_empleado']."<br/><hr/>";
                
                $funcionarios_no_creados++;
            }
            
        } else {
            echo "<b class='rojo'>Funcionario no creado (Ya existente):</b><br/>";
            echo "<b>- Cedula:</b> ".$funcionario_db->getCi()."<br/>";
            echo "<b>- Nombre:</b> ".$funcionario_db->getPrimerNombre()." ".$funcionario_db->getSegundoNombre()." ".$funcionario_db->getPrimerApellido()." ".$funcionario_db->getSegundoApellido()."<br/>";
            echo "<b>- F. Nacimiento:</b> ".date("d-m-Y", strtotime($funcionario_db->getFNacimiento()))."<br/>";
            echo "<b>- Sexo:</b> ".$funcionario_db->getSexo()."<br/>";
            echo "<b>- Estado Civil:</b> ".$funcionario_db->getEdoCivil()."<br/><hr/>";
                    
            $funcionarios_existentes++;
        }
        
    }
    
    echo "<hr/>";
    echo "Funcionarios creados: ".$funcionarios_creados."<br/>";
    echo "Funcionarios no creados: ".$funcionarios_no_creados."<br/>";
    echo "Funcionarios existentes: ".$funcionarios_existentes."<br/>";
    exit();
//    $this->setTemplate('migracion/migrarFuncionariosResumen');
  }
  
  
  
  
  public function executeChequearCedulaExistente(sfWebRequest $request)
  {
    $cedula = trim($request->getParameter('ci'));

    $existente['status'] = false;
    $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($cedula);
    if($funcionario){
        $existente['status'] = true;
    }

    return $this->renderText(json_encode($existente));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('funcionarios_funcionario');
    $cargo_asignado = $request->getParameter('funcionarios_funcionario_cargo');
    
    if($datos['id']!=''){
        $funcionario_actual = Doctrine::getTable('Funcionarios_Funcionario')->find($datos['id']);

        if(trim($datos['email_personal']) == trim($funcionario_actual->getEmailPersonal()) && $funcionario_actual->getEmailValidado()==TRUE){
            $datos['email_validado'] = TRUE;
        }
    }
    
    $request->setParameter('funcionarios_funcionario',$datos);        

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
	  if($form->getObject()->isNew())
	  {
              $notice = 'El Funcionario se ha registrado correctamente.';

              $conn = Doctrine_Manager::connection();

              try
              {
                  $conn->beginTransaction();
                  $funcionarios_funcionario = $form->save();

                  $nombre_usuario = new herramientas();
                  $nombre_usuario = $nombre_usuario->generarUsuario($funcionarios_funcionario->getPrimerNombre(), $funcionarios_funcionario->getSegundoNombre(), $funcionarios_funcionario->getPrimerApellido(), $funcionarios_funcionario->getSegundoApellido());

                  $usuario = new Acceso_Usuario();
                  $usuario->setUsuarioEnlaceId($funcionarios_funcionario->getId());
                  $usuario->setEnlace_id(1);
                  $usuario->setNombre(strtolower($nombre_usuario));
                  $usuario->setClave(crypt(strtolower($nombre_usuario),strtolower($nombre_usuario)));
                  $usuario->setVisitas(0);
                  $usuario->setUltimaconexion(date('Y-m-d h:i:s'));
                  $usuario->setUltimocambioclave(date('Y-m-d h:i:s'));
                  $usuario->setStatus('I');
                  $usuario->setAccesoGlobal(FALSE);
                  $usuario->setTema('estandar');
                  $usuario->save();

                  $enlace = Doctrine::getTable('Acceso_Enlace')->find(1);
                  $enlace->setTotal($enlace->get("total") + 1);
                  $enlace->save();

                  if($cargo_asignado){ //SI SE ASIGNA EL CARGO DE INMEDIATO A LA CREACION DEL FUNCIONARIO
                      $cargo = Doctrine::getTable('Organigrama_Cargo')->find($cargo_asignado['cargo_id']);

                      //ASIGNAR CARGO AL FUNCIONARIO
                      $funcionario_cargo = new Funcionarios_FuncionarioCargo();
                      $funcionario_cargo->setFuncionarioId($funcionarios_funcionario->getId());
                      $funcionario_cargo->setCargoId($cargo->getId());
                      $funcionario_cargo->setFuncionarioCargoCondicionId($cargo_asignado['funcionario_cargo_condicion_id']);
                      $funcionario_cargo->setFIngreso($cargo_asignado['f_ingreso']);
                      $funcionario_cargo->save();

                      //CAMBIAR STATUS DEL CARGO A OCUPADO
                      $cargo->setStatus('O');
                      $cargo->save();

                      //ACTIVAR EL USUARIO DEL FUNCIONARIO
                      $usuario->setStatus('A');
                      $usuario->save();

                      //ASIGNARLE EL PERFIL DEL CARGO AL FUNCIONARIO
                      $usuario_perfil = new Acceso_UsuarioPerfil();
                      $usuario_perfil->setUsuarioId($usuario->getId());
                      $usuario_perfil->setPerfilId($cargo->getPerfilId());
                      $usuario_perfil->setStatus('A');
                      $usuario_perfil->save();
                  }

                  $conn->commit();
              }
              catch (Doctrine_Validator_Exception $e)
              {
                  $conn->rollBack();
                  $errorStack = $form->getObject()->getErrorStack();

                  $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
                  foreach ($errorStack as $field => $errors) {
                      $message .= "$field (" . implode(", ", $errors) . "), ";
                  }
                  $message = trim($message, ', ');

                  $this->getUser()->setFlash('error', $message);
                  return sfView::SUCCESS;
              }
	  }
	  else
	  {
                try
                {
                    $notice = 'Los datos del Funcionario se han actualizado correctamente.';
                    $funcionarios_funcionario = $form->save();
                }
                catch (Doctrine_Validator_Exception $e)
                {
                    $errorStack = $form->getObject()->getErrorStack();

                    $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
                    foreach ($errorStack as $field => $errors) {
                        $message .= "$field (" . implode(", ", $errors) . "), ";
                    }
                    $message = trim($message, ', ');

                    $this->getUser()->setFlash('error', $message);
                    return sfView::SUCCESS;
                }
	  }



      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $funcionarios_funcionario)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' Puede continuar registrando otro funcionario.');

        $this->redirect('@funcionarios_funcionario_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect('@funcionarios_funcionario');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'Los datos del Funcionario no se han registrado ya que existen algunos errores.', false);
    }
  }
  
  public function executeActivarAsignacionCargo(sfWebRequest $request)
  {
  }

  public function executeFoto(sfWebRequest $request)
  {
    $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($request->getParameter('id'));
    $this->getUser()->setAttribute('foto_cambio', $funcionario->getCi());

    $this->redirect(sfConfig::get('sf_app_funcionarios_url').'foto?from=foto');
  }

  public function executeCheckUser(sfWebRequest $request)
  {
      $id_usr = $request->getParameter('id_usr');
      
      if($request->getParameter('tipo_user')=='siglas'){
          
        $nombre= trim($request->getParameter('ext1')).'.'.trim($request->getParameter('ext2'));
        $usuario = Doctrine::getTable('Acceso_Usuario')->findByNombre($nombre);
        
      } elseif($request->getParameter('tipo_user')=='ldap'){
          
        $nombre = trim($request->getParameter('ext_ldap'));
        $usuario = Doctrine::getTable('Acceso_Usuario')->findByLdap($nombre);
          
      }
      
      $this->usuario= count($usuario);
      $this->id_usr= $id_usr;
      $this->nombre= $nombre;
      $this->tipo_user= $request->getParameter('tipo_user');
  }
  
  public function executeSaveUser(sfWebRequest $request)
  {
      $id_usr = $request->getParameter('id_usr');
      $nombre = trim($request->getParameter('nombre'));
      $tipo_user = $request->getParameter('tipo_user');
      
      $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByEnlaceIdAndId(1, $id_usr);

      $usuario->setClave('por validacion de email');
      $usuario->setStatus('R');
      if($tipo_user=='siglas'){
        $usuario->setNombre(strtolower(trim($nombre)));
      } elseif ($tipo_user=='ldap') {
        $usuario->setLdap(strtolower(trim($nombre)));
      }
      $usuario->save();

      $this->id_usr= $id_usr;
      $this->nombre= $nombre;
      $this->tipo_user= $tipo_user;
      
      $this->getUser()->setFlash('notice', ' El funcionario debe recuperar la nueva contraseña mediante correo electronico.');
  }
  
  public function executeVerificarCedula(sfWebRequest $request)
  {
        $funcionario['persona_saime'] = false;
        
        $funcionario['cedula'] = $request->getParameter('cedula_verificar');
        $sf_seguridad = sfYaml::load(sfConfig::get('sf_root_dir') . "/config/siglas/seguridad.yml");

        $result=NULL;

        if($sf_seguridad['conexion_saime']['activo']==true){
            try{
                $manager = Doctrine_Manager::getInstance()
                        ->openConnection(
                        'pgsql' . '://' .
                        $sf_seguridad['conexion_saime']['username'] . ':' .
                        $sf_seguridad['conexion_saime']['password'] . '@' .
                        $sf_seguridad['conexion_saime']['host'] . ':'. $sf_seguridad['conexion_saime']['port'] .'/' .
                        $sf_seguridad['conexion_saime']['dbname'], 'dbSAIME');

                $query = "SELECT ".$sf_seguridad['conexion_saime']['consulta']['campo_nacionalidad'].", 
                                 ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula'].",
                                 ".$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre'].",
                                 ".$sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre'].",
                                 ".$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido'].",
                                 ".$sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido'].",
                                 ".$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']."
                          FROM ".$sf_seguridad['conexion_saime']['consulta']['tabla']."
                          WHERE ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula']."=" . $request->getParameter('cedula_verificar');

                $result = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
                Doctrine_Manager::getInstance()->closeConnection($manager);
            } catch (Exception $e) {}
        }

        if ($result) {
            $funcionario['persona_saime'] = true;
            $funcionario['primer_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $funcionario['segundo_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $funcionario['primer_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $funcionario['segundo_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            
            $funcionario['f_nacimiento_day'] = date("d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
            $funcionario['f_nacimiento_month'] = date("m", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
            $funcionario['f_nacimiento_year'] = date("Y", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));
            
            $funcionario['f_nacimiento'] = date("Y-m-d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));
        }
        
        $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        sleep(1);
        return $this->renderText(json_encode($funcionario));
  }
  
  public function executeFormularioFirmas() {

        $pdf = new ConPie(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(40, 115, 80);
        $pdf->SetHeaderData('gob_pdf.png', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf->setAutoPageBreak(True, 90);

        $pdf->AliasNbPages();
        $pdf->AddPage();
        

        $tbl = '<table width="470" "center">
    <tr>
        <td width="470">
            <table width="470" "center">
                  <tr>
                    <td colspan="2" align="center"><br/></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center">
                        <h1>HOJA DE FIRMAS</h1>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center"><br/></td>
                  </tr>';
        
        for($i= 0; $i<4; $i++) {
            $tbl .= '<tr><td>
                    <table border="1" width="470" cellpadding="3" >
                        <tr>
                            <td align="left" width="65%">
                                <table cellpadding="5">
                                    <tr><td>Cedula: _____________________________________________</td></tr>
                                    <tr><td>Nombre y Apellido: ____________________________________</td></tr>
                                    <tr><td>Cargo:______________________________________________</td></tr>
                                    <tr><td>Tlf/Ext:______________________________________________</td></tr>
                                    <tr><td>Correo:_____________________________________________</td></tr>
                                </table>
                            </td>
                            <td align="center">
                                <img src="/images/firma_digital/sig_noise.jpg" alt="signature" width="220" height="110" border="0" />
                            </td>
                        </tr>
                    </table>
                </td></tr>';
        }
        
        $tbl .= '</table>
        </td>
    </tr>
</table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output('hojafirma__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }
}
    
class ConPie extends TCPDF {
     public function Footer() {
        $this->Image('http://'.$_SERVER['SERVER_NAME'].'/images/organismo/pdf/gob_footer_pdf.png',0,750,450,70,'','','N','','','C');
    }
}
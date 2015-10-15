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

      if($request->getParameter('prv') == true)
          $this->prv= true;
      else
          $this->prv= false;
  }

  public function executeImportarConfig(sfWebRequest $request){}

  public function executeImportarConfigProcess(sfWebRequest $request)
  {
    if(!empty($_FILES['archivo']['name'])) {
        if (substr($_FILES['archivo']['name'], -4)== '.yml'){
            $dir_file = sfConfig::get('sf_upload_dir') . '/excel/' . $_FILES['archivo']['name'];
            move_uploaded_file($_FILES['archivo']['tmp_name'], $dir_file);

            $got_it = sfYaml::load(sfConfig::get("sf_root_dir")."/web/uploads/excel/".$_FILES['archivo']['name']);
            //RESTAURA CONFIGURACIONDES DESDE BACKUP
            if(count($got_it)== 7){
                if($got_it['unidades_clave']!= NULL){
                    $sf_oficinasClave = sfYAML::dump($got_it['unidades_clave']);
                    file_put_contents('../config/siglas/oficinasClave.yml', $sf_oficinasClave);
                }
                if($got_it['email']!= NULL){
                    $sf_email = sfYAML::dump($got_it['email']);
                    file_put_contents('../config/siglas/email.yml', $sf_email);
                }
                if($got_it['sms']!= NULL){
                    $sf_sms = sfYAML::dump($got_it['sms']);
                    file_put_contents('../config/siglas/sms.yml', $sf_sms);
                }
                if($got_it['datos_basicos']!= NULL){
                    $sf_datosBasicos = sfYAML::dump($got_it['datos_basicos']);
                    file_put_contents('../config/siglas/datosBasicos.yml', $sf_datosBasicos);
                }
                if($got_it['rrhh']!= NULL){
                    $sf_rrhh = sfYAML::dump($got_it['rrhh']);
                    file_put_contents('../config/siglas/rrhh.yml', $sf_rrhh);
                }
                if($got_it['varios']!= NULL){
                    $sf_varios = sfYAML::dump($got_it['varios']);
                    file_put_contents('../config/siglas/varios.yml', $sf_varios);
                }

                unlink(sfConfig::get("sf_root_dir")."/web/uploads/excel/".$_FILES['archivo']['name']);

                $this->getUser()->setFlash('notice', 'Se han restaudado las configuraciones del SIGLAS');
                $this->redirect('configuracion/index');
            }else{
                $this->getUser()->setFlash('error', 'El respaldo no corresponde a SIGLAS '.sfConfig::get('sf_siglas').' o archivo corrupto.');
                $this->redirect('configuracion/importarConfig');
            }
        }else{
            $this->getUser()->setFlash('error', 'Extensión de archivo incorrecta, debe ser YAML (.yml)');
            $this->redirect('configuracion/importarConfig');
        }
    }else{
        $this->getUser()->setFlash('error', 'Debe seleccionar un archivo de respaldo');
        $this->redirect('configuracion/importarConfig');
    }

  }

  public function executeExportarConfig(sfWebRequest $request){}

  public function executeExportarConfigDo(sfWebRequest $request)
  {
    $sf_oficinasClave= $request->getParameter('unidades_clave')? sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml") : NULL;
    $sf_email = $request->getParameter('correo_electronico')? sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/email.yml") : NULL;
    $sf_sms = $request->getParameter('sms')? sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/sms.yml") : NULL;
    $sf_datosBasicos = $request->getParameter('datos_basicos')? sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/datosBasicos.yml") : NULL;
    $sf_rrhh = $request->getParameter('recursos_humanos')? sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/rrhh.yml") : NULL;
    $sf_varios = $request->getParameter('varios')? sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml") : NULL;

    $export_array['fecha_respaldo']= date("d.m.Y H:i:s A");
    $export_array['unidades_clave']= $sf_oficinasClave;
    $export_array['email']= $sf_email;
    $export_array['sms']= $sf_sms;
    $export_array['datos_basicos']= $sf_datosBasicos;
    $export_array['rrhh']= $sf_rrhh;
    $export_array['varios']= $sf_varios;

    $all = sfYAML::dump($export_array);

    file_put_contents('../config/siglas/configRespaldo.yml', $all);

    $file = "configRespaldo";
    $url = sfConfig::get("sf_root_dir") . "/config/siglas/" . $file .".yml";
    header("Content-Disposition: attachment; filename=" . $file . "_" .date("d.m.Y").".yml");
    header("Content-Type: application/x-yml");
    header("Content-Length: " . filesize($url));
    readfile($url);
    exit;
  }

  public function executeSaveAutenticacion(sfWebRequest $request)
  {
    $sf_autenticacion = $request->getParameter('autenticacion');
    $cadena = sfYAML::dump($sf_autenticacion);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/autenticacion.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Parametros de Autenticación actualizados con exito.');
    $this->redirect('configuracion/index?opcion=autenticacion');
  }
  
  public function executeSaveVariablesSistema(sfWebRequest $request)
  {
    $sf_variablesSistema = $request->getParameter('variablesSistema');
    $costo= trim($sf_variablesSistema['costo_disciplina']);
    if($sf_variablesSistema['costo_disciplina'] == '') {
        $costo= 0;
    }else {
        $find = array('/[^a-z0-9\-&lt;&gt;]/', '/[\-]+/', '/&lt;{^&gt;*&gt;/', '/[a-zA-Z]/');
        $costo = preg_replace($find, '', $costo);
    }
    $sf_variablesSistema['costo_disciplina']= ($costo== '')? '0' : $costo;
    
    $cadena = sfYAML::dump($sf_variablesSistema);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/variablesSistema.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Variables actualizadas con exito.');
    $this->redirect('configuracion/index?opcion=variablesSistema');
  }
  
  public function executeSaveSubversion(sfWebRequest $request)
  {
    $sf_subversion = $request->getParameter('subversion');
    $sf_subversion['version_name']='Cunaguaro';
    $sf_subversion['version_number']='5.0';
    
    $cadena = sfYAML::dump($sf_subversion);

    file_put_contents('../config/siglas/subversion.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Parametros de Subvercion se actualizaron con exito.');
    $this->redirect('configuracion/index?opcion=subversion');
  }
  
  public function executeSaveOficinasClave(sfWebRequest $request)
  {
    $oficinas = $request->getParameter('oficinas');
    $sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");

    foreach ($oficinas as $oficina => $seleccion) {
        $sf_oficinasClave[$oficina]['seleccion']=(integer)$seleccion['seleccion'];
    }

    $cadena = sfYAML::dump($sf_oficinasClave);

    file_put_contents('../config/siglas/oficinasClave.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Unidades clave actualizadas con exito.');
    $this->redirect('configuracion/index?opcion=oficinasClave');
  }

  public function executeSaveCorrelativo(sfWebRequest $request)
  {
    $correlativo = $request->getParameter('correlativo');
    
    $sf_nomencladores = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/nomencladores.yml");
    $nomencladores_status = array();

    foreach ($sf_nomencladores['correspondencia'] as $nomenclador => $status) {
        $nomencladores_status[$nomenclador]=false;
    }

    foreach ($correlativo['nomenclador'] as $nomenclador => $status) {
        $nomencladores_status[$nomenclador]=true;
    }
    
    $sf_nomencladores['correspondencia'] = $nomencladores_status;
    
    $cadena = sfYAML::dump($sf_nomencladores);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/nomencladores.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Nomencladores de correlativos de correspondencia seteados con exito.');
    $this->redirect('configuracion/index?opcion=correlativo');
  }
  
  public function executeSaveDatosBasicos(sfWebRequest $request)
  {
    $datos = $request->getParameter('datos');
    $cadena = sfYAML::dump($datos);

    file_put_contents('../config/siglas/datosBasicos.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Datos básicos actualizados con exito.');
    $this->redirect('configuracion/index?opcion=datosBasicos');
  }

  public function executeSaveFirmaElectronica(sfWebRequest $request)
  {
    $sf_firmaElectronica = $request->getParameter('firma_electronica');
    $cadena = sfYAML::dump($sf_firmaElectronica);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/firmaElectronica.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Firma electronica actualizada con exito.');
    $this->redirect('configuracion/index?opcion=firmaElectronica');
  }

  public function executeSaveInteroperabilidad(sfWebRequest $request)
  {
    $sf_interoperabilidad = $request->getParameter('interoperabilidad');
    $cadena = sfYAML::dump($sf_interoperabilidad);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/interoperabilidad.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Parametros de Interoperabilidad actualizados con exito.');
    $this->redirect('configuracion/index?opcion=interoperabilidad');
  }

  public function executeSaveVarios(sfWebRequest $request)
  {
    $datos = $request->getParameter('varios');
    $cadena = sfYAML::dump($datos);

    file_put_contents('../config/siglas/varios.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Varios actualizados con exito.');
    $this->redirect('configuracion/index?opcion=varios');
  }

  public function executeSaveCrontab(sfWebRequest $request)
  {
    $crontab = $request->getParameter('crontab');
    $sf_crontab = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/crontab.yml");

    $sf_crontab['activo'] = $crontab['activo'];
    if($crontab['activo'] == 'false'){
        crontab::delAll();
    }

    foreach ($crontab['aplicaciones'] as $aplicacion => $detalles) {
        foreach ($detalles as $key => $valores) {
            $sf_crontab['aplicaciones'][$aplicacion][$key]['activo']=$valores['activo'];

            if($crontab['activo'] == 'true' && $valores['activo'] == 'true'){
                crontab::add($sf_crontab['aplicaciones'][$aplicacion][$key]['frecuency'], $sf_crontab['aplicaciones'][$aplicacion][$key]['task'], $sf_crontab['aplicaciones'][$aplicacion][$key]['comment']);
            } else {
                crontab::del($sf_crontab['aplicaciones'][$aplicacion][$key]['frecuency'], $sf_crontab['aplicaciones'][$aplicacion][$key]['task'], $sf_crontab['aplicaciones'][$aplicacion][$key]['comment']);
            }
        }
    }

    $cadena = sfYAML::dump($sf_crontab);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/crontab.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Configuraciones de crontab actualizadas y ejecutadas.');
    $this->redirect('configuracion/index?opcion=crontab');
  }

  public function executeSaveEmail(sfWebRequest $request)
  {
    $email = $request->getParameter('email');
    $sf_email = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/email.yml");

    $sf_email['activo'] = $email['activo'];

    $sf_email['cuentas'] = $email['cuentas'];

    foreach ($email['aplicaciones'] as $aplicacion => $detalles) {

        $sf_email['aplicaciones'][$aplicacion]['inmediata']['activo'] = $detalles['inmediata']['activo'];

        $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia']['lunes']=false;
        $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia']['martes']=false;
        $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia']['miercoles']=false;
        $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia']['jueves']=false;
        $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia']['viernes']=false;
        $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia']['sabado']=false;
        $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia']['domingo']=false;


        foreach ($detalles['inmediata']['frecuencia'] as $dia => $valor) {
            $sf_email['aplicaciones'][$aplicacion]['inmediata']['frecuencia'][$dia]=true;
        }

        $sf_email['aplicaciones'][$aplicacion]['reporte']['activo'] = false;
        if($detalles['reporte']['activo']=='true')
            $sf_email['aplicaciones'][$aplicacion]['reporte']['activo'] = true;

        $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia']['lunes']=false;
        $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia']['martes']=false;
        $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia']['miercoles']=false;
        $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia']['jueves']=false;
        $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia']['viernes']=false;
        $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia']['sabado']=false;
        $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia']['domingo']=false;


        foreach ($detalles['reporte']['frecuencia'] as $dia => $valor) {
            $sf_email['aplicaciones'][$aplicacion]['reporte']['frecuencia'][$dia]=true;
        }
    }

    $cadena = sfYAML::dump($sf_email);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/email.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Configuraciones de correo electronico actualizadas.');
    $this->redirect('configuracion/index?opcion=email');
  }

  public function executeSaveSeguridad(sfWebRequest $request)
  {
    $sf_seguridad = $request->getParameter('seguridad');
    $cadena = sfYAML::dump($sf_seguridad);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/seguridad.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Seguridad actualizada con exito.');
    $this->redirect('configuracion/index?opcion=seguridad');
  }

  public function executeTestearConexionSaime(sfWebRequest $request)
  {
      $sf_seguridad = $request->getParameter('seguridad');
      $conexion=NULL;
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
                      LIMIT 1";

            $conexion = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
            Doctrine_Manager::getInstance()->closeConnection($manager);
      } catch (Exception $e) {}

      if($conexion != NULL){
          echo "<font style='color: blue;'>Conexion y consulta satisfactoria</font>";
      } else {
          echo "<font style='color: red;'>Error en conexion o consulta: </font><br/><br/>".$e;
      }
      exit();
  }

  public function executeSaveSms(sfWebRequest $request)
  {
    $sms = $request->getParameter('sms');
    $sf_sms = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/sms.yml");

    $sf_sms['activo'] = $sms['activo'];
    $sf_sms['conexion_gammu'] = $sms['conexion_gammu'];

    foreach ($sms['aplicaciones'] as $aplicacion => $detalles) {
        if($aplicacion != 'mensajes_externos') {
            $sf_sms['aplicaciones'][$aplicacion]['activo'] = $detalles['activo'];
            $horario = $detalles['horario'];

            $sf_sms['aplicaciones'][$aplicacion]['horario']['desde']=$horario['desde']['hora'].':'.$horario['desde']['minuto'].':00';
            $sf_sms['aplicaciones'][$aplicacion]['horario']['hasta']=$horario['hasta']['hora'].':'.$horario['hasta']['minuto'].':00';

            $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['lunes']=false;
            $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['martes']=false;
            $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['miercoles']=false;
            $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['jueves']=false;
            $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['viernes']=false;
            $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['sabado']=false;
            $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['domingo']=false;

            //CARGOS AUTORIZADOS PARA EL USO DE SMS
            if($aplicacion== 'mensajes'){
                $sf_sms['aplicaciones'][$aplicacion]['autorizados']['unico'] = $sms['aplicaciones'][$aplicacion]['autorizados']['unico'];

                if (is_array($sms['aplicaciones'][$aplicacion]['autorizados']['otros']))
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados']['otros'] = $sms['aplicaciones'][$aplicacion]['autorizados']['otros'];
                else
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados']['otros'] = NULL;
            }

            foreach ($detalles['frecuencia'] as $dia => $valor) {
                $sf_sms['aplicaciones'][$aplicacion]['frecuencia'][$dia]=true;
            }
        }else {
            //CARGOS AUTORIZADOS PARA EL USO DE SMS EXTERNO
            if($aplicacion== 'mensajes_externos'){
                $sf_sms['aplicaciones'][$aplicacion]['activo'] = $detalles['activo'];
                if (!$sms['aplicaciones'][$aplicacion]['autorizados']['unico']['dato'] == '') {
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados']['unico'] = $sms['aplicaciones'][$aplicacion]['autorizados']['unico'];
                }else {
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados']['unico']= NULL;
                }

                if (is_array($sms['aplicaciones'][$aplicacion]['autorizados']['otros'])){
                    sort($sms['aplicaciones'][$aplicacion]['autorizados']['otros']);
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados']['otros'] = $sms['aplicaciones'][$aplicacion]['autorizados']['otros'];
                }else{
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados']['otros'] = NULL;
                }

                //USUARIOS ESPECIFICOS AUTORIZADOS PARA USO DE SMS EXTERNO
                if (isset($sms['aplicaciones'][$aplicacion]['autorizados_particulares']['activo'])){
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['activo'] = $sms['aplicaciones'][$aplicacion]['autorizados_particulares']['activo'];

                    if (isset($sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']['unidad']) && $sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']['funcionario'] != ''){
                        $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']['dato'] = $sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']['unidad'].'#'.$sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']['funcionario'];
                        $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']['modems'] = $sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']['modems'];
                    }else
                        $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico'] = NULL;

                    if (is_array($sms['aplicaciones'][$aplicacion]['autorizados_particulares']['otros'])){
                        sort($sms['aplicaciones'][$aplicacion]['autorizados_particulares']['otros']);
                        $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['otros'] = $sms['aplicaciones'][$aplicacion]['autorizados_particulares']['otros'];
                    }else{
                        $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['otros'] = NULL;
                    }
                }else{
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['activo'] = 'false';
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico'] = NULL;
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['otros'] = NULL;
                }
                if ($sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['unico']== NULL && $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['otros']== NULL)
                    $sf_sms['aplicaciones'][$aplicacion]['autorizados_particulares']['activo'] = 'false';
            }
        }

    }

    $cadena = sfYAML::dump($sf_sms);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents('../config/siglas/sms.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Configuraciones de mensajes de texto actualizadas.');
    $this->redirect('configuracion/index?opcion=sms');
  }

  public function executeSaveCache(sfWebRequest $request)
  {
      $cache = $request->getParameter('cache');
      $sf_cache = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/cache.yml");

      $manager = Doctrine_Manager::getInstance();
      $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);

      if($cache=='correspondencia')
      {
          $cacheDriver->deleteByPrefix('correspondencia_');
          $sf_cache['correspondencia']['ultimo_borrado']=date('Y-m-d');
          $this->getUser()->setFlash('notice', ' Cache de correspondencia borrada con exito.');
      }
      else if($cache=='organigrama')
      {
          $cacheDriver->deleteByPrefix('cache_unidad_');
          $sf_cache['organigrama']['ultimo_borrado']=date('Y-m-d');
          $this->getUser()->setFlash('notice', ' Cache de organigrama borrada con exito.');
      }
      else if($cache=='organismos')
      {
          $cacheDriver->deleteByPrefix('organismos_');
          $sf_cache['organismos']['ultimo_borrado']=date('Y-m-d');
          $this->getUser()->setFlash('notice', ' Cache de organismos externos borrada con exito.');
      }

      $cadena = sfYAML::dump($sf_cache);

      file_put_contents('../config/siglas/cache.yml', $cadena);
      $this->redirect('configuracion/index?opcion=cache');
  }


  public function executeSaveOrganismosExternosNew(sfWebRequest $request)
  {
        $datos = $request->getParameter('datos');
        try {
            $organismo = new Organismos_Organismo();
            $organismo->setNombre($datos['organismo']);
            $organismo->setSiglas($datos['siglas']);
            $organismo->setOrganismoTipoId($datos['tipo']);
            @$organismo->save();

            $this->getUser()->setFlash('notice_organismo', 'Organismo agregado con éxito.');
        } catch(Exception $e){
            $conn->rollBack();
            $this->getUser()->setFlash('error_organismo', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnología.');
        }

        $cadena = '<table>
                        <tr>
                            <th></th>
                            <th style="width: 150px;">SIGLAS</th>
                            <th style="min-width: 300px;">Nombre</th>
                        </tr>';

                            $organismos = Doctrine::getTable('Organismos_Organismo')->createQuery('a')->where('status = \'A\'')->orderBy('nombre')->execute();

                            $i=1;
                            foreach ($organismos as $organismo) {
                                $cadena.= '<tr>';
                                $cadena.= '<td><input type="checkbox" name="organismos_ids" value="'.$organismo->getId().'"/></td>';
                                $cadena.= '<td class="organismos_text">'.$organismo->getSiglas().'</td>';
                                $cadena.= '<td class="organismos_text">'.$organismo->getNombre().'</td>';
                                $cadena.= '</tr>';
                                $i++;
                            }
        $cadena.='</table>';

        echo $cadena;
        exit();
  }


  public function executeSaveOrganismosEdit(sfWebRequest $request)
  {
      $id= $request->getParameter('id');
      $nombre= trim($request->getParameter('nombre'));
      $siglas= trim($request->getParameter('siglas'));

      $conn = Doctrine_Manager::connection();
      $pross= 'procede';
        try {
            $conn->beginTransaction();

            //Edita el organismo seleccionado
            $q = Doctrine_Query::create($conn);
            $q->update('Organismos_Organismo')
                ->set('nombre', '?', $nombre)
                ->set('siglas', '?', $siglas)
                ->where('id = ?', $id)
                ->execute();

            $conn->commit();

        } catch(Exception $e){
            $conn->rollBack();
            $pross= 'error';
        }

        $organismo_editado= Doctrine::getTable('Organismos_Organismo')->find($id);
        $organismo_nombre= $organismo_editado->getNombre();
        $organismo_siglas= $organismo_editado->getSiglas();

        $request_list['nombre']= $organismo_nombre;
        $request_list['siglas']= $organismo_siglas;
        $request_list['id']= $id;
        $request_list['pross']= $pross;

        return $this->renderText(json_encode($request_list));
  }


  public function executeSavePersonasEdit(sfWebRequest $request)
  {
      $id= $request->getParameter('id');
      $persona= trim($request->getParameter('persona'));

      $conn = Doctrine_Manager::connection();
      $pross= 'procede';
        try {
            $conn->beginTransaction();

            //Edita el organismo seleccionado
            $q = Doctrine_Query::create($conn);
            $q->update('Organismos_Persona')
                ->set('nombre_simple', '?', $persona)
                ->where('id = ?', $id)
                ->execute();

            $conn->commit();

        } catch(Exception $e){
            $conn->rollBack();
            $pross= 'error';
        }

        $organismo_editado= Doctrine::getTable('Organismos_Persona')->find($id);
        $organismo_persona= $organismo_editado->getNombreSimple();

        $request_list['persona']= $organismo_persona;
        $request_list['id']= $id;
        $request_list['pross']= $pross;

        return $this->renderText(json_encode($request_list));
  }


  public function executeSaveOrganismosExternosCombine(sfWebRequest $request)
  {
        $datos = $request->getParameter('datos');

        $organismos_ids = explode(',',$datos['organismos_ids']);
        sort($organismos_ids);

        $conn = Doctrine_Manager::connection();
        try {
            $conn->beginTransaction();
            
            // BUSCAR SI ALGUNO DE LOS ORGANISMOS A COMBINAR ES UN ORGANISMO DE CONFIANZA PARA INTEROPERABILIDAD
            $organismos_confianza= Doctrine::getTable('Siglas_ServidorConfianza')->organismosConfianzaEn($organismos_ids);

            $notice_nombre_bloqueado = "";
            if(count($organismos_confianza)==0){
                $organismo_id_primario = $organismos_ids[0];
                
                // Actualizar el organismo con el menor ID con los datos corregidos
                $organismo = Doctrine::getTable('Organismos_Organismo')->find($organismo_id_primario);
                $organismo->setNombre($datos['organismo']);
                $organismo->setSiglas($datos['siglas']);
                $organismo->setOrganismoTipoId($datos['tipo']);
                $organismo->setStatus('A');
                @$organismo->save();
            } else if(count($organismos_confianza)==1) {
                $organismo_id_primario = $organismos_confianza[0]->getOrganismoId();
                $organismo = Doctrine::getTable('Organismos_Organismo')->find($organismo_id_primario);
                
                $notice_nombre_bloqueado = "Entre los organismos combinados se encontro uno estalecido como organismo de confianza para interoperabilidad, por lo tanto se dejaron los datos del mismo por defecto.";
            } else {                
                $cadena_script = "<script>";
                $cadena_script .= '$("#error_organismo").html("Entre los organismos que desea combinar se encuentran mas de un organismo de confianza para interoperabilidad. Estos organismos no se podran combinar.");';
                $cadena_script .= '$("#error_organismo").css("display", "block");';
                $cadena_script .= "</script>";
                
                echo $cadena_script;
                exit();
            }

            //Mover personas al organismo de menor ID
            $q = Doctrine_Query::create($conn);
            $q->update('Organismos_Persona')
                ->set('organismo_id', '?', $organismo_id_primario)
                ->whereIn('organismo_id', $organismos_ids)
                ->execute();

            //Mover emisores externos al organismo de menor ID
            $q = Doctrine_Query::create($conn);
            $q->update('Correspondencia_Correspondencia')
                ->set('emisor_organismo_id', '?', $organismo_id_primario)
                ->whereIn('emisor_organismo_id', $organismos_ids)
                ->execute();

            //Mover receptores externos al organismo de menor ID
            $q = Doctrine_Query::create($conn);
            $q->update('Correspondencia_ReceptorOrganismo')
                ->set('organismo_id', '?', $organismo_id_primario)
                ->whereIn('organismo_id', $organismos_ids)
                ->execute();

            //Elimiar todos los organismos seleccionados ecepto el de menor ID
            $q = Doctrine_Query::create($conn);
            Doctrine::getTable('Organismos_Organismo')
                ->createQuery()
                ->delete()
                ->whereIn('id', $organismos_ids)
                ->andWhere('id <> ?', $organismo_id_primario)
                ->execute();

            $conn->commit();

            $manager = Doctrine_Manager::getInstance();
            $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);

            foreach ($organismos_ids as $organismo_id) {
                $cacheDriver->delete('organismos_count_personas_'.$organismo_id);
            }
        } catch(Exception $e){
            $conn->rollBack();
        }

        $cadena_script = "<script>";

        foreach ($organismos_ids as $organismo_id) {
            if($organismo_id != $organismo_id_primario){
                $cadena_script .= '$("#tr_organismo_id_'.$organismo_id.'").remove(); ';
            }
        }

        $cadena_script .= '$("#siglas_'.$organismo_id_primario.'").html("'.$organismo->getSiglas().'");';
        $cadena_script .= '$("#nombre_'.$organismo_id_primario.'").html("'.$organismo->getNombre().'");';
        $cadena_script .= '$("#notice_organismo").html("Organismos combinados correctamente. '.$notice_nombre_bloqueado.'");';
        $cadena_script .= '$("#notice_organismo").css("display", "block");';
        $cadena_script .= '$("#notice_organismo").css("display", "block");';
        $cadena_script .= '$("#check_id_'.$organismo_id_primario.'").attr("checked", false);';
                
        $cadena_script .= "</script>";
        echo $cadena_script;

        exit();
  }




  public function executePrepararPersonasExternasCombineMasivo()
  {
        $organismos = Doctrine_Query::create()
                    ->select("id, nombre")
                    ->from('Organismos_Organismo')
                    ->andWhere('status = ?','A')
                    ->orderBy('nombre')
                    ->execute(array(), Doctrine::HYDRATE_NONE);

        $total_general=0; $total_count=0;
        $cadena = '<a href="'.sfConfig::get('sf_app_acceso_url').'configuracion/savePersonasExternasCombineMasivo?organismo_id=0">Reparar todos los organismos</a>';
        foreach ($organismos as $organismo) {


            $query = "select translate, count(translate) total
                     from (
                          select translate(lower(nombre_simple),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou') as translate
                          from organismos.persona
                          where organismo_id = ".$organismo[0]."
                     ) as tablas_translate
                     group by translate
                     having count(translate) > 1
                     order by translate";

            $personas_repetidas = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);

            if(count($personas_repetidas)>1){
                $cadena .= "<h2>".$organismo[1]."</h2>";
                $cadena .= '<a href="'.sfConfig::get('sf_app_acceso_url').'configuracion/savePersonasExternasCombineMasivo?organismo_id='.$organismo[0].'">Reparar este organismo</a>';


                $cadena .= "<table>
                                <tr>
                                    <th>Nombre comparado</th>
                                    <th>Total repetidos</th>
                                    <th>Nombres reales</th>
                                </tr>";
                foreach ($personas_repetidas as $persona_repetida) {
                    $cadena .= "<tr>
                                    <td>".$persona_repetida['translate']."</td>
                                    <td>".$persona_repetida['total']."</td>
                                    <td> --- </td>
                                </tr>";

                    $total_count++;
                    $total_general += $persona_repetida['total'];
                }

                $cadena .= "</table><hr/>";
            }
        }

        $total_corregir = $total_general-$total_count;
        $cadena = 'Total de nombres repetidos: '.$total_count.'<br/>'.'Total de registros por corregir: '.$total_corregir.'<br/>'.$cadena;
        $this->cadena = $cadena;
  }

  public function executeSavePersonasExternasCombineMasivo(sfWebRequest $request)
  {
        $organismo_id = $request->getParameter('organismo_id');

        if($organismo_id == 0) {
            $organismos = Doctrine_Query::create()
                        ->select("id, nombre")
                        ->from('Organismos_Organismo')
                        ->where('status = ?','A')
                        ->orderBy('nombre')
                        ->execute(array(), Doctrine::HYDRATE_NONE);
        } else {
            $organismos = Doctrine_Query::create()
                        ->select("id, nombre")
                        ->from('Organismos_Organismo')
                        ->where('status = ?','A')
                        ->andWhere('id = ?',$organismo_id)
                        ->orderBy('nombre')
                        ->execute(array(), Doctrine::HYDRATE_NONE);
        }

        foreach ($organismos as $organismo) {


            $query = "select translate, count(translate) total
                     from (
                          select translate(lower(nombre_simple),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou') as translate
                          from organismos.persona
                          where organismo_id = ".$organismo[0]."
                     ) as tablas_translate
                     group by translate
                     having count(translate) > 1
                     order by translate";

            $personas_repetidas = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);


            if(count($personas_repetidas)>1){

                foreach ($personas_repetidas as $persona_repetida) {
                    $personas_ids = array();
                    $query = "select id
                             from organismos.persona
                             where translate(lower(nombre_simple),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou') = '".$persona_repetida['translate']."'
                             and organismo_id = ".$organismo[0];

                    $ids_combinar = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);

                    $i=0;
                    foreach ($ids_combinar as $id_combinar) {
                        $personas_ids[$i] = $id_combinar['id'];
                        $i++;
                    }


                    sort($personas_ids);


                    $conn = Doctrine_Manager::connection();
                    try {
                        $conn->beginTransaction();

                        //Mover cargos a la persona de menor ID
                        $q = Doctrine_Query::create($conn);
                        $q->update('Organismos_PersonaCargo')
                            ->set('persona_id', '?', $personas_ids[0])
                            ->whereIn('persona_id', $personas_ids)
                            ->execute();

                        //Mover telefonos a la persona de menor ID
                        $q = Doctrine_Query::create($conn);
                        $q->update('Organismos_PersonaTelefono')
                            ->set('persona_id', '?', $personas_ids[0])
                            ->whereIn('persona_id', $personas_ids)
                            ->execute();

                        //Mover emisores personas externas a la persona de menor ID
                        $q = Doctrine_Query::create($conn);
                        $q->update('Correspondencia_Correspondencia')
                            ->set('emisor_persona_id', '?', $personas_ids[0])
                            ->whereIn('emisor_persona_id', $personas_ids)
                            ->execute();


                        // Verificar si las personas que se estan moviendo no esten repetidas con nombres diferentes en la recepcion externa de correspondencia
                        $receptores_repetidos = Doctrine_Query::create()
                            ->select("ro.correspondencia_id, COUNT(ro.id) as repetido")
                            ->from('Correspondencia_ReceptorOrganismo ro')
                            ->where('ro.organismo_id = ?',$organismo[0])
                            ->andWhereIn('ro.persona_id',$personas_ids)
                            ->groupBy('ro.correspondencia_id')
                            ->execute(array(), Doctrine::HYDRATE_NONE);

                        foreach ($receptores_repetidos as $receptor_repetido) {
                            if($receptor_repetido[1]>1){
                                //Eliminar todos los receptores externos repetidos
                                $q = Doctrine_Query::create($conn);
                                Doctrine::getTable('Correspondencia_ReceptorOrganismo')
                                    ->createQuery()
                                    ->delete()
                                    ->where('correspondencia_id = ?', $receptor_repetido[0])
                                    ->andWhere('persona_id <> ?', $personas_ids[0])
                                    ->andWhereIn('persona_id', $personas_ids)
                                    ->execute();
                            }
                        }

                        //Mover receptores personas externas a la persona de menor ID
                        $q = Doctrine_Query::create($conn);
                        $q->update('Correspondencia_ReceptorOrganismo')
                            ->set('persona_id', '?', $personas_ids[0])
                            ->whereIn('persona_id', $personas_ids)
                            ->execute();

                        //Eliminar todas las personas seleccionadas ecepto la de menor ID
                        $q = Doctrine_Query::create($conn);
                        Doctrine::getTable('Organismos_Persona')
                            ->createQuery()
                            ->delete()
                            ->whereIn('id', $personas_ids)
                            ->andWhere('id <> ?', $personas_ids[0])
                            ->execute();

                        $conn->commit();

                        $manager = Doctrine_Manager::getInstance();
                        $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);

                        foreach ($personas_ids as $persona_id) {
                            $cacheDriver->delete('organismos_count_cargos_'.$persona_id);
                        }

                        $cacheDriver->delete('organismos_count_personas_'.$organismo[0]);

                        $this->getUser()->setFlash('notice_persona', count($personas_ids).' Personas combinadas con éxito.');
                    } catch(Exception $e){
                        $conn->rollBack();
                        $this->getUser()->setFlash('error_persona', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnología.');
                    }

    //                $cadena = '';
    //                if ($this->getUser()->hasFlash('notice_persona')) {
    //                  $cadena = '<div class="notice">'.$this->getUser()->getFlash('notice_persona').'</div>';
    //                }
    //
    //                if ($this->getUser()->hasFlash('error_persona')){
    //                  $cadena = '<div class="error">'.$this->getUser()->getFlash('error_persona').'</div>';
    //                }
                }
            }
        }

        Doctrine::getTable('Organismos_Persona')
        ->createQuery()
        ->delete()
        ->where('status = ?', 'I')
        ->execute();

        $this->redirect('configuracion/prepararPersonasExternasCombineMasivo');
  }

  public function executeSavePersonasExternasCombine(sfWebRequest $request)
  {
        $datos = $request->getParameter('datos');

        $personas_ids = explode(',',$datos['personas_ids']);
        sort($personas_ids);

        $conn = Doctrine_Manager::connection();
        try {
            $conn->beginTransaction();

            // Actulizar el organismo con el menor ID con los datos corregidos
            $personas = Doctrine::getTable('Organismos_Persona')->find($personas_ids[0]);
            $personas->setNombreSimple($datos['persona']);
            $personas->setStatus('A');
            @$personas->save();

            //Mover cargos a la persona de menor ID
            $q = Doctrine_Query::create($conn);
            $q->update('Organismos_PersonaCargo')
                ->set('persona_id', '?', $personas_ids[0])
                ->whereIn('persona_id', $personas_ids)
                ->execute();

            //Mover telefonos a la persona de menor ID
            $q = Doctrine_Query::create($conn);
            $q->update('Organismos_PersonaTelefono')
                ->set('persona_id', '?', $personas_ids[0])
                ->whereIn('persona_id', $personas_ids)
                ->execute();

            //Mover emisores personas externas a la persona de menor ID
            $q = Doctrine_Query::create($conn);
            $q->update('Correspondencia_Correspondencia')
                ->set('emisor_persona_id', '?', $personas_ids[0])
                ->whereIn('emisor_persona_id', $personas_ids)
                ->execute();

            //Mover receptores personas externas a la persona de menor ID
            $q = Doctrine_Query::create($conn);
            $q->update('Correspondencia_ReceptorOrganismo')
                ->set('persona_id', '?', $personas_ids[0])
                ->whereIn('persona_id', $personas_ids)
                ->execute();

            //Eliminar todas las personas seleccionadas ecepto la de menor ID
            $q = Doctrine_Query::create($conn);
            Doctrine::getTable('Organismos_Persona')
                ->createQuery()
                ->delete()
                ->whereIn('id', $personas_ids)
                ->andWhere('id <> ?', $personas_ids[0])
                ->execute();

            $conn->commit();

            $manager = Doctrine_Manager::getInstance();
            $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);

            foreach ($personas_ids as $persona_id) {
                $cacheDriver->delete('organismos_count_cargos_'.$persona_id);
                $cacheDriver->delete('organismos_count_personas_'.$personas->getOrganismoId());
            }

            $this->getUser()->setFlash('notice_persona', count($personas_ids).' Personas combinadas con éxito.');
        } catch(Exception $e){
            $conn->rollBack();
            $this->getUser()->setFlash('error_persona', $e.'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnología.');
        }

        $cadena = '';
        if ($this->getUser()->hasFlash('notice_persona')) {
          $cadena = '<div class="notice">'.$this->getUser()->getFlash('notice_persona').'</div>';
        }

        if ($this->getUser()->hasFlash('error_persona')){
          $cadena = '<div class="error">'.$this->getUser()->getFlash('error_persona').'</div>';
        }

        $cadena .= $datos['personas_ids'].'<table>
                        <tr>
                            <th></th>
                            <th style="min-width: 250px;">Nombre</th>
                            <th style="min-width: 20px;">Cargos</th>
                            <th style="min-width: 20px;"></th>
                        </tr>';

                            $personas = Doctrine::getTable('Organismos_Persona')->createQuery('a')->where('status = \'A\'')->andWhere('organismo_id = '.$personas->getOrganismoId())->orderBy('nombre_simple')->execute();

                            $i=1; $personas_ids = '';
                            foreach ($personas as $persona) {
                                $personas_ids.=$persona->getId().',';
                                $cadena.= '<tr>';
                                $cadena.= '<td><input type="checkbox" name="personas_ids[]" class="personas_check" value="'.$persona->getId().'"/></td>';
                                $cadena.= '<td class="personas_text">'.$persona->getNombreSimple().'</td>';
                                $cadena.= '<td id="preople_cargos_'.$persona->getId().'"><img src="/images/icon/cargando.gif"/></td>';
                                $cadena.= '<td style="text-align: center;"><li><a href="#"><img src="/images/icon/combine_people.png"/></a></li></td>';
                                $cadena.= '</tr>';
                                $i++;
                            }
        $cadena.='</table>';

        $personas_ids .= '###';
        $personas_ids = str_replace(",###", "", $personas_ids);

        echo $cadena;


        echo  "<script>
                    var personas_ids = new Array(".$personas_ids.");
                    var ACTIVO_PERSONAS = false;
                    var i = 0;
                    function contar_cargos(){
                        if(i<personas_ids.length){
                            if (ACTIVO_PERSONAS == false){
                                ACTIVO_PERSONAS = true;
                                $('#preople_cargos_'+personas_ids[i]).load('".sfConfig::get('sf_app_acceso_url')."configuracion/contarCargosPersonas?persona_id='+personas_ids[i],
                                null, function (){
                                    ACTIVO_PERSONAS = false;
                                    i++;
                                }).fadeIn('slow');
                            }
                        } else {
                            clearInterval(intervalo_contar);
                        }
                    }
                    var intervalo_contar = setInterval('contar_cargos()', 100);
                </script>";



        exit();
  }










  public function executeSaveRrhh(sfWebRequest $request)
  {
    $rrhh = $request->getParameter('rrhh');
    $sf_rrhh = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/rrhh.yml");

    $sf_rrhh = $rrhh;

//    $sf_rrhh['conexion_rrhh'] = $rrhh['conexion_rrhh'];

//    foreach ($sms['aplicaciones'] as $aplicacion => $detalles) {
//
//        $sf_sms['aplicaciones'][$aplicacion]['activo'] = $detalles['activo'];
//        $horario = $detalles['horario'];
//
//        $sf_sms['aplicaciones'][$aplicacion]['horario']['desde']=$horario['desde']['hora'].':'.$horario['desde']['minuto'].':00';
//        $sf_sms['aplicaciones'][$aplicacion]['horario']['hasta']=$horario['hasta']['hora'].':'.$horario['hasta']['minuto'].':00';
//
//        $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['lunes']=false;
//        $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['martes']=false;
//        $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['miercoles']=false;
//        $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['jueves']=false;
//        $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['viernes']=false;
//        $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['sabado']=false;
//        $sf_sms['aplicaciones'][$aplicacion]['frecuencia']['domingo']=false;
//
//
//        foreach ($detalles['frecuencia'] as $dia => $valor) {
//            $sf_sms['aplicaciones'][$aplicacion]['frecuencia'][$dia]=true;
//        }
//    }

    $cadena = sfYAML::dump($sf_rrhh);

    file_put_contents('../config/siglas/rrhh.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Configuraciones de Recursos Humanos actualizadas.');
    $this->redirect('configuracion/index?opcion=rrhh');
  }

  public function executeSaveGps(sfWebRequest $request)
  {
    $gps = $request->getParameter('gps');
    $type= 's';
    if($gps['recuperacion']['frecuencia_activo_selector']== 'm')
        $type= 'm';
    elseif($gps['recuperacion']['frecuencia_activo_selector']== 'h')
        $type= 'h';
    $inter_activo= '';

    $gps_old = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/gps.yml");

    $gps_old['activo']= $gps['activo'];

    $gps_old['administradores']= Array();
    if($gps['tlf']['unico'] != '')
        $gps_old['administradores'][]= $gps['tlf']['unico'];
    foreach($gps['tlf']['otros'] as $key => $value) {
        $gps_old['administradores'][]= $value;
    }

    if($gps_old['recuperacion']['frecuencia_activo'] !== $gps['recuperacion']['frecuencia_activo_'.$type]) {
        $gps_old['recuperacion']['frecuencia_activo']= $gps['recuperacion']['frecuencia_activo_'.$type];
        $inter_activo= $gps['recuperacion']['frecuencia_activo_'.$type];
    }

    if($gps_old['recuperacion']['frecuencia_inactivo'] !== $gps['recuperacion']['frecuencia_inactivo']) {
        $gps_old['recuperacion']['frecuencia_inactivo']= $gps['recuperacion']['frecuencia_inactivo'];
    }

    $gps_old['alertas']['status']= $gps['alertas']['status'];

    if($inter_activo !== '' || $inter_inactivo !== '') {
        $asignados= Doctrine::getTable('Vehiculos_GpsVehiculo')->rastreables();

        if($inter_activo !== '') {
            $parametro= explode('#', $inter_activo);
            $intervalo= $parametro[0];
            $type= $parametro[1];
            if(strlen($intervalo) < 3)
                $intervalo = '0'.$intervalo;
            $intervalo= $intervalo.$type;

            foreach($asignados as $value) {
                if($value->getSim() !== '') {
                    $registro = Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneBySim($value->getSim());
                    $reg_comando= Doctrine::getTable('Vehiculos_Comando')->find(5);
                    $comando= str_replace('<password>', $registro->getClave(), $reg_comando->getComando());
                    $comando= str_replace('<interval>s', $intervalo, $comando);
                    Sms::sms_at('correspondencia', $value->getSim(), $comando, 'auto');
                }
            }
        }
    }


    $cadena = sfYAML::dump($gps_old);
    $cadena = str_replace("'true'", "true", $cadena);
    $cadena = str_replace("'false'", "false", $cadena);

    file_put_contents(sfConfig::get("sf_root_dir").'/config/siglas/gps.yml', $cadena);
    $this->getUser()->setFlash('notice', ' Configuraciones de GPS actualizadas.');
    $this->redirect('configuracion/index?opcion=gps');
  }

  public function executeSaveTablasMaestras(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $value = $request->getParameter('value');
    $table = $request->getParameter('table');

    switch ($table) {
        case 'tipo_educacion_adicional':
            $table = 'Public_TipoEducacionAdicional';
            break;
        case 'parentesco':
            $table = 'Public_Parentesco';
            break;
        default:
            echo '<div class="notice">La opcion seleccionada no esta configurada para administrar.</div>';
            exit();
            break;
    }

    if($id!=''){
        $row_table = Doctrine::getTable($table)->find($id);
    } else {
        $row_table = new $table;
    }

    $row_table->setNombre($value);
    $row_table->save();

    echo '<div class="notice">Informacion guardada exitosamente.</div>';
    exit();
  }

  public function executeParticularesAutorizados(sfWebRequest $request)
  {
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('unidad_id')));
  }

  public function executeOpciones(sfWebRequest $request)
  {
      $opcion = $request->getParameter('opcion');

//      if ($opcion == 'tipoFormato'){
//          $this->redirect(sfConfig::get('sf_app_correspondencia_url').'tipo_formato');
//      }

      eval('$this->sf_'.$opcion.' = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/'.$opcion.'.yml");');
      if ($opcion == 'sms'){
          $this->sf_organigramas = Doctrine_Query::create()
                ->select('id, nombre')
                ->from('Organigrama_CargoTipo')
                ->orderBy('nombre')
                ->execute();

          $this->unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(TRUE);
          $this->device= Sms::count_device();
      } elseif ($opcion == 'organismosExternos'){
          $this->organismos = Doctrine::getTable('Organismos_Organismo')->createQuery('a')->where('status = \'A\'')->orderBy('nombre')->execute();
      } elseif ($opcion == 'grupoCorrespondencia') {
          $organigrama = Doctrine::getTable('Organigrama_Unidad')->comboUnidad();
          $funcionarios= Array();
          $total_redactar=0; $total_leer=0; $total_recibir=0; $total_firmar=0; $total_administrar=0;
          foreach( $organigrama as $unidad_id=>$unidad_nombre ) {
              if($unidad_id!='') {
                    $funcionarios_array= Doctrine::getTable('Correspondencia_FuncionarioUnidad')->AllFuncionarioGrupo($unidad_id);
                    $redactar= 0; $leer= 0; $recibir= 0; $firmar= 0; $administrar= 0;
                    for($i=0; $i< count($funcionarios_array); $i++) {
                        if($funcionarios_array[$i][1]== TRUE) {
                            $redactar++;
                            $total_redactar++;
                        }
                        if($funcionarios_array[$i][2]== TRUE) {
                            $leer++;
                            $total_leer++;
                        }
                        if($funcionarios_array[$i][3]== TRUE) {
                            $recibir++;
                            $total_recibir++;
                        }
                        if($funcionarios_array[$i][4]== TRUE) {
                            $firmar++;
                            $total_firmar++;
                        }
                        if($funcionarios_array[$i][5]== TRUE) {
                            $administrar++;
                            $total_administrar++;
                        }
                    }
                    $funcionarios[$unidad_id]['redactar'] = $redactar;
                    $funcionarios[$unidad_id]['leer'] = $leer;
                    $funcionarios[$unidad_id]['recibir'] = $recibir;
                    $funcionarios[$unidad_id]['firmar'] = $firmar;
                    $funcionarios[$unidad_id]['administrar'] = $administrar;
                    $funcionarios[$unidad_id]['count'] = count($funcionarios_array);
              }
          }
          $funcionarios['total']['redactar'] = $total_redactar;
          $funcionarios['total']['leer'] = $total_leer;
          $funcionarios['total']['recibir'] = $total_recibir;
          $funcionarios['total']['firmar'] = $total_firmar;
          $funcionarios['total']['administrar'] = $total_administrar;
          $this->organigrama= $organigrama;
          $this->funcionarios= $funcionarios;
      } elseif ($opcion == 'grupoArchivo') {
          $organigrama = Doctrine::getTable('Organigrama_Unidad')->comboUnidad();
          $funcionarios= Array();
          $total_archivar=0; $total_leer=0; $total_prestar=0; $total_anular=0; $total_administrar=0;
          foreach( $organigrama as $unidad_id=>$unidad_nombre ) {
              if($unidad_id!='') {
                    $funcionarios_array= Doctrine::getTable('Archivo_FuncionarioUnidad')->AllFuncionarioGrupo($unidad_id);
                    $archivar= 0; $leer= 0; $prestar= 0; $anular= 0; $administrar= 0;
                    for($i=0; $i< count($funcionarios_array); $i++) {
                        if($funcionarios_array[$i][1]== TRUE) {
                            $archivar++;
                            $total_archivar++;
                        }
                        if($funcionarios_array[$i][2]== TRUE) {
                            $leer++;
                            $total_leer++;
                        }
                        if($funcionarios_array[$i][3]== TRUE) {
                            $prestar++;
                            $total_prestar++;
                        }
                        if($funcionarios_array[$i][4]== TRUE) {
                            $anular++;
                            $total_anular++;
                        }
                        if($funcionarios_array[$i][5]== TRUE) {
                            $administrar++;
                            $total_administrar++;
                        }
                    }
                    $funcionarios[$unidad_id]['archivar'] = $archivar;
                    $funcionarios[$unidad_id]['leer'] = $leer;
                    $funcionarios[$unidad_id]['prestar'] = $prestar;
                    $funcionarios[$unidad_id]['anular'] = $anular;
                    $funcionarios[$unidad_id]['administrar'] = $administrar;
                    $funcionarios[$unidad_id]['count'] = count($funcionarios_array);
              }
          }
          $funcionarios['total']['archivar'] = $total_archivar;
          $funcionarios['total']['leer'] = $total_leer;
          $funcionarios['total']['prestar'] = $total_prestar;
          $funcionarios['total']['anular'] = $total_anular;
          $funcionarios['total']['administrar'] = $total_administrar;
          $this->organigrama= $organigrama;
          $this->funcionarios= $funcionarios;
      } elseif ($opcion == 'correlativo') {
          $this->nomencladores = '';
          $this->nomencladores = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/nomencladores.yml");
          
          $organigrama = Doctrine::getTable('Organigrama_Unidad')->comboUnidad();
          $correlativos= Array();
          $total_correlativos=0;
          foreach( $organigrama as $unidad_id=>$unidad_nombre ) {
              if($unidad_id!='') {
                    $correlativos_array= Doctrine::getTable('Correspondencia_UnidadCorrelativo')->AllCorrelativosReport($unidad_id);
                    $correlativos[$unidad_id]['count'] = $correlativos_array[0][0];
                    $total_correlativos+= $correlativos_array[0][0];
              }
          }
          $correlativos['total'] = $total_correlativos;
          $this->organigrama= $organigrama;
          $this->correlativos= $correlativos;
      } elseif ($opcion == 'tipoFormato'){
          $this->redirect(sfConfig::get('sf_app_correspondencia_url').'tipo_formato');
      }

      $this->setTemplate($opcion);
  }

  public function executePersonasExternas(sfWebRequest $request)
  {
      $this->organismo_id = $request->getParameter('organismo_id');
  }

  public function executeEditGroupCorrespondencia(sfWebRequest $request)
  {
      $this->redirect(sfConfig::get('sf_app_correspondencia_url').'grupos/index?id='.$request->getParameter('id'));
  }

  public function executeEditGroupCorrelativo(sfWebRequest $request)
  {
      $this->redirect(sfConfig::get('sf_app_correspondencia_url').'correlativos/index?id='.$request->getParameter('id'));
  }

  public function executeEditGroupArchivo(sfWebRequest $request)
  {
      $this->redirect(sfConfig::get('sf_app_archivo_url').'grupos/index?id='.$request->getParameter('id'));
  }

  public function executeActualizarCapacidadMass(sfWebRequest $request)
  {
      $this->device= Sms::count_device();
  }

  public function executeContarPersonasOrganismos(sfWebRequest $request)
  {
    $organismo_id = $request->getParameter('organismo_id');

    $personas_organismo = Doctrine::getTable('Organismos_Persona')->countPersonasPorOrganismo($organismo_id);

    echo $personas_organismo[0][0];
    exit();
  }

  public function executeContarCargosPersonas(sfWebRequest $request)
  {
    $persona_id = $request->getParameter('persona_id');

    $cargos_persona = Doctrine::getTable('Organismos_PersonaCargo')->countCargosPorPersona($persona_id);

    echo $cargos_persona[0]->getCargos();
    exit();
  }

  public function executeOrganismosExternos(sfWebRequest $request)
  {
        $distancia = $request->getParameter('distancia');
        $array_organismos = Doctrine::getTable('Organismos_Organismo')->arrayTodosNoHidratados();

        $organismos_similares_ids[0] = 0;
        $organismos_ids=array();
        foreach ($array_organismos as $organismo) {
            $organismos_ids[$organismo[0]] = $organismo[0];
        }

        foreach ($array_organismos as $organismo) {
            if(isset($organismos_ids[$organismo[0]])){
                $organismo_buscar = str_replace("'", "''", $organismo[1]);
                $organismos_similares = Doctrine_Query::create()
                    ->select("id")
                    ->from('Organismos_Organismo')
                    ->Where('status = ?','A')
                    ->andWhere("levenshtein(
                                    translate(lower(nombre),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
                                    translate(lower('".$organismo_buscar."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou')) <= ".$distancia)
        //            ->orWhere("levenshtein(
        //                            translate(lower(siglas),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
        //                            translate(lower('".$datos_siglas."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou')) <= 2")
                    ->execute(array(), Doctrine::HYDRATE_NONE);

                if(count($organismos_similares)>1){
                    foreach ($organismos_similares as $organismo_similar) {
                        $organismos_similares_ids[]=$organismo_similar[0];
                        unset($organismos_ids[$organismo_similar[0]]);
                    }
                }
            }
        }

        $this->organismos = Doctrine::getTable('Organismos_Organismo')->todosPorId($organismos_similares_ids);
  }


  public function executeExtraConfig(sfWebRequest $request)
  {
      eval('$classe = new config'.ucfirst($request->getParameter('configuracion')).'();');
      $parametros = $request->getParameter('parametros');
      eval('$resultado = $classe->'.$request->getParameter('funcion').'($parametros);');

      $this->valores = $resultado['this'];
      $this->setTemplate($request->getParameter('configuracion').'/'.$resultado['template']);
  }

   public function executeBorrarIndependiente(sfWebRequest $request) {

      $constantes= $this->getConstantes($request->getParameter('from'), $request->getParameter('classe'));

      if(file_exists($constantes['upload_path'].$constantes['large_image_prefix'].'.png')) {
          unlink($constantes['upload_path'].$constantes['large_image_prefix'].'.png');
          echo '';
      }else {
          echo 'Accion no ejecutada, intenta mas tarde';
      }
      exit();
  }

  public function executeEstablecerImg(sfWebRequest $request) {
        $classe= $request->getParameter('classe');
        $constantes= $this->getConstantes($request->getParameter('from'), $classe);

        $images = new Images();

//Image Locations
        $large_image_location = $constantes['upload_path'] . $constantes['large_image_prefix'];
//
        foreach ($request->getFiles() as $file) {

//Get the file information
            $userfile_name = $file['name'];
            $userfile_tmp = $file['tmp_name'];
            $userfile_size = $file['size'];
            $userfile_type = $file['type'];
            $filename = basename($file['name']);
        }

        $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

//Only process if the file is a JPG, PNG or GIF and below the allowed limit
        if ((!empty($file)) && ($file['error'] == 0)) {

            foreach ($constantes['allowed_image_types'] as $mime_type => $ext) {
                //loop through the specified image types and if they match the extension then break out
                //everything is ok so go and check file size
                if ($file_ext == $ext && $userfile_type == $mime_type) {
                    $error = "";
                    break;
                } else {
                    $error = "Solo son aceptadas las imagenes " . $constantes['image_ext'].'. ';
                }
            }
            //check if the file size is above the allowed limit
            if ($userfile_size > ($constantes['max_file'] * 1048576)) {
                $error.= "La imagen no puede ser mayor a " . $constantes['max_file'] . "MB de peso. ";
            }

            $width = $images->getWidth($userfile_tmp);
            $height = $images->getHeight($userfile_tmp);

            if ($width > $constantes['max_width'] || $width < $constantes['min_width'] ||
                    $height > $constantes['max_height'] || $height < $constantes['min_height']) {
                $error.= "El tamaño de la imagen debera ser: ancho max. = ".$constantes['max_width']."px; ancho min. = ".$constantes['min_width']."px; largo max. = ".$constantes['max_height']."px; largo min. = ".$constantes['min_height']."px. ";
            }
        } else {
            $error = "Seleccione la imagen";
        }

        $request_list['error']= 0;
        $request_list['imagen']= '';

//Everything is ok, so we can upload the image.
        if (strlen($error) == 0) {
            if (isset($file['name'])) {
                //put the file ext in the session so we know what file to look for once its uploaded
                $this->getUser()->setAttribute('user_file_ext', '.' . $file_ext);

                //EN CASO DE QUE SEA HEADER INDEPENDIENTE GUARDA DIRECTO CON EXTENSION, EN CASO CONTRARIO
                //SE GUARDA SIN EXTENSION PARA LUEGO RENOMBRARLO SUSTITUYENDO LA IMAGEN ORIGINAL
                if($request->getParameter('from') != 'header_inde') {
                    move_uploaded_file($userfile_tmp, $large_image_location);
                    chmod($large_image_location, 0777);
                }else {
                    move_uploaded_file($userfile_tmp, $large_image_location.'.' . $file_ext);
                    chmod($large_image_location.'.' . $file_ext, 0777);
                }

                //BUSCA CUAL SERA LA PROXIMA NUMERACION PARA RENOMBRAR LA IMAGEN VIEJA
                $i= 1; $limit= false;
                do {
                    if (!file_exists($large_image_location.'_'.$i.'.'.$file_ext)) {
                        $limit= true;
                    }
                    $i++;
                } while (!$limit); $i--;

                //SI SE TRARA DE UN HEADER INDEPENDIENTE NO HARA FALTA RENOMBRAR YA QUE ESTE NO GUARDA HISTORICO
                //Y AL SUBIR ARCHIVO YA ESTE SE GUARDO CON NOMBRE Y EXTENSION CORRECTO
                if ($request->getParameter('from') != 'header_inde') {
                    //RENOMBRA EL ANTERIOR PARA HISTORICO
                    rename($large_image_location.'.'.$file_ext, $large_image_location.'_'.$i.'.'.$file_ext);

                    //RENOMBRA EL NUEVO CON LA EXTENSION CORRECTA
                    rename($large_image_location, $large_image_location.'.'.$file_ext);
                }
            }

            $request_list['valor']= '<img src="/'.$constantes['upload_path'].$constantes['large_image_prefix'].'.png?'.time().'" style="max-width: 500px; width: '.(($constantes['establecer_width'] == 'auto')? $constantes['establecer_width'] : $constantes['establecer_width'].'px').'; height: auto">';
        }else{
            $request_list['error']= 1;
            $request_list['valor']= $error;
        }

        return $this->renderText(json_encode($request_list));
    }

    public function executeReuseImg(sfWebRequest $request) {
        $constantes= $this->getConstantes($request->getParameter('from'));
        $large_image_location = $constantes['upload_path'] . $constantes['large_image_prefix'];
        $file_ext= trim(strtolower($constantes['image_ext']));

        $route_change= substr(strtolower(substr($request->getParameter('route'), 0, strrpos($request->getParameter('route'), '?'))), 1);

        //RENOMBRA EL ACTUAL A UN NOMBRE TEMPORAL
        rename($large_image_location.'.'.$file_ext, $large_image_location.'_t.'.$file_ext);
        //RENOMBRA LA IMAGEN VIEJA PARA REACTIVAR
        rename($route_change, $large_image_location.'.'.$file_ext);
        //RENOMBRA EL TEMPORAL PARA QUE TOME LA POSICION DEL VIEJO
        rename($large_image_location.'_t.'.$file_ext, $route_change);

        $request_list['valor']= '<img src="/'.$large_image_location.'.png?'.time().'" style="max-width: 500px; width: '.(($constantes['establecer_width'] == 'auto')? $constantes['establecer_width'] : $constantes['establecer_width'].'px').'; height: auto">';
        return $this->renderText(json_encode($request_list));
    }

    public function executeHistorialImg(sfWebRequest $request) {
        $constantes= $this->getConstantes($request->getParameter('from'));

        switch ($request->getParameter('from')) {
            case 'header':
                $width= '300';
                $height= '41';
                break;
            case 'header_cont':
                $width= '300';
                $height= '41';
                break;
            case 'footer':
                $width= '300';
                $height= '41';
                break;
            case 'auth_left':
                $width= 'auto';
                $height= 'auto';
                break;
            case 'auth_right':
                $width= 'auto';
                $height= 'auto';
                break;
            case 'main_session':
                $width= '89';
                $height= '80';
                break;
            default:
                break;
        }

        $file_ext= trim(strtolower($constantes['image_ext']));
        $large_image_location = $constantes['upload_path'] . $constantes['large_image_prefix'];

        $i= 1; $limit= true;
        $images_ar= Array();

        do {
            if (file_exists($large_image_location.'_'.$i.'.'.$file_ext)) {
                $images_ar[]= $large_image_location.'_'.$i.'.'.$file_ext;
            }else {
                $limit= false;
            }
            $i++;
        } while ($limit);

        $request_list['valor']= '';

        if(count($images_ar) > 0) {
            $cadena= ''; $i= count($images_ar);
            $images_ar= array_reverse($images_ar);
            //LOS ID SE ASIGNAN EN REVERSA YA QUE EL ARRAY SERA VOLTEADO PARA SU MUESTREO CRONOLOGICO
            foreach($images_ar as $val) {
                $cadena.= '<table><tr><td><img id="img_'.$request->getParameter('from').'_'.$i.'" src="/'.$val.'?'.time().'" style="width: '.(($width == 'auto')? $width : $width.'px').'; height: '.(($height == 'auto')? $height : $height.'px').'; max-width: 300px"></td>';
                $cadena.= '<td style="vertical-align: middle"><a style="text-decoration: none" href="#" onClick="javascript: reuseImg('.$i.', \''.$request->getParameter('from').'\'); return false;"><img src="/images/icon/reused.png"></a></td></tr>';
                $cadena.= '</table>';
                $i--;
            }
            $request_list['valor']= $cadena;
        }else{
            $request_list['valor']= 'Historico vacio...';
        }

        return $this->renderText(json_encode($request_list));
    }
    
    public function executeAnalizarYml($patron,$parametros) {
//        echo '<pre>';
//        print_r($patron); exit;
            $parametros_listo=array();
            // primer recorrido
            if(is_array($patron)){
                foreach ($patron as $key => $patron_values) {
                    if(!isset($parametros[$key])){
                       $parametros_listo[$key]=$patron_values;
                    } else {
                       $parametros_listo[$key] = $this->executeAnalizarYml($patron[$key],$parametros[$key]);
                    }
                }
            } else {
                $patron_explode = explode('||', $patron);
                
                if(count($patron_explode)>1){
                    if(!in_array($parametros, $patron_explode)) {
                        $parametros=$patron_explode[0];
                    }else {
                        return $parametros;
                    }
                } elseif($patron != 'value_libre') {
                    return $patron;
                }else {
                    return $parametros;
                }
                //FALTA COMPLETAR ESTE CODIGO
                
//                if($patron == 'value_array'){
//                    return $parametros;
//                } else {
//                    if(!is_array($parametros)){
//                        return $parametros;
//                    } else {
//                        return $patron;
//                    }
//                }
                
            }
            
            return $parametros_listo;
        
    }
    
    public function executeActualizarYml(sfWebRequest $request) {
        $tipos_formatos= Doctrine::getTable('Correspondencia_TipoFormato')->tiposFormato();
        $patron= sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/tipoFormatoParams.yml");
        
        foreach($tipos_formatos as $tipo_formato) {
            $parametros= sfYaml::load($tipo_formato->getParametros());
//            $array_final = $this->executeAnalizarYml($patron,$parametros);
            
            //ACCION PROVISIONAL PARA ACTUALIZAR OPTIONS_CREATE DE TIPOS DE FORMATOS PARA PRIVACIDAD SETEADA
            if(isset($parametros['options_create'])) {
                if($parametros['options_create']['privacidad']) {
                    if(!is_array($parametros['options_create']['privacidad'])) {
                        $parametros['options_create']['privacidad']= Array('habilitado'=>'true', 'publico'=>'true');
                    }

                    $array_final_yaml = sfYAML::dump($parametros);

                    $tipo_formato->setParametros($array_final_yaml);
                    $tipo_formato->save();
                }
            }
            //FIN DE CODIGO PROVISIONAL (BORRAR)
            
//            echo 'accion no autorizada...'; exit;
//            FALTA COMPLETAR FUNCION ANALIZAR YML
//            echo '<pre>';
//            print_r($array_final);
//            exit;
            
//            $array_final_yaml = sfYAML::dump($array_final);
            
//            $tipo_formato->setParametros($array_final_yaml);
//            $tipo_formato->save();
        }
        echo 'actualizado...'; exit;
    }

    public function getConstantes($type, $replace1= NULL) {
        switch ($type) {
            case 'header':
                $upload_dir= "images/organismo/pdf";
                $upload_dir_main= "images/organismo/pdf/";
                $max_file= '3';
                $max_width= '665';
                $min_width= '665';
                $max_height= '110';
                $min_height= '90';
                $large_image_prefix= 'gob_pdf';
                $establecer_width= 'auto';
                break;
            case 'header_inde':
                $upload_dir= "images/organismo/pdf";
                $upload_dir_main= "images/organismo/pdf/";
                $max_file= '3';
                $max_width= '665';
                $min_width= '665';
                $max_height= '110';
                $min_height= '90';
                $large_image_prefix= $replace1.'_gob_pdf';
                $establecer_width= 'auto';
                break;
            case 'header_cont':
                $upload_dir= "images/organismo/pdf";
                $upload_dir_main= "images/organismo/pdf/";
                $max_file= '3';
                $max_width= '665';
                $min_width= '665';
                $max_height= '110';
                $min_height= '90';
                $large_image_prefix= 'gob_pdf_contraloria';
                $establecer_width= 'auto';
                break;
            case 'footer':
                $upload_dir= "images/organismo/pdf";
                $upload_dir_main= "images/organismo/pdf/";
                $max_file= '3';
                $max_width= '665';
                $min_width= '665';
                $max_height= '90';
                $min_height= '90';
                $large_image_prefix= 'gob_footer_pdf';
                $establecer_width= 'auto';
                break;
            case 'auth_left':
                $upload_dir= "images/organismo";
                $upload_dir_main= "images/organismo/";
                $max_file= '3';
                $max_width= '660';
                $min_width= '120';
                $max_height= '57';
                $min_height= '57';
                $large_image_prefix= 'banner_izquierdo';
                $establecer_width= 'auto';
                break;
            case 'auth_right':
                $upload_dir= "images/organismo";
                $upload_dir_main= "images/organismo/";
                $max_file= '3';
                $max_width= '280';
                $min_width= '120';
                $max_height= '57';
                $min_height= '57';
                $large_image_prefix= 'banner_derecho';
                $establecer_width= 'auto';
                break;
            case 'main_session':
                $upload_dir= "images/organismo";
                $upload_dir_main= "images/organismo/";
                $max_file= '3';
                $max_width= '338';
                $min_width= '338';
                $max_height= '304';
                $min_height= '304';
                $large_image_prefix= 'logo_session';
                $establecer_width= '120';
                break;
            default:
                break;
        }

        $constantes['upload_dir'] = $upload_dir;
//$upload_dir = "upload_pic"; 				// The directory for the images to be saved in
        $constantes['upload_path'] = $constantes['upload_dir'] . "/";    // The path to where the image will be saved
        $constantes['large_image_prefix'] = $large_image_prefix;    // The prefix name to large image
        $constantes['thumb_image_prefix'] = "thumbnail_";   // The prefix name to the thumb image

        $constantes['max_file'] = $max_file;        // Maximum file size in MB
        $constantes['max_width'] = $max_width;       // Max width allowed for the large image
        $constantes['min_width'] = $min_width;
        $constantes['max_height'] = $max_height;
        $constantes['min_height'] = $min_height;
        $constantes['upload_dir_main'] = $upload_dir_main;
        $constantes['establecer_width'] = $establecer_width;
// Only one of these image types should be allowed for upload
        $constantes['allowed_image_types'] = array('image/png' => "png", 'image/x-png' => "png");
        //$constantes['allowed_image_types'] = array('image/pjpeg' => "jpg", 'image/jpeg' => "jpg", 'image/jpg' => "jpg", 'image/png' => "png", 'image/x-png' => "png", 'image/gif' => "gif");
        $constantes['allowed_image_ext'] = array_unique($constantes['allowed_image_types']); // do not change this
        $image_ext = ""; // initialise variable, do not change this.
        foreach ($constantes['allowed_image_ext'] as $mime_type => $ext) {
            $image_ext.= strtoupper($ext) . " ";
        }

        $constantes['image_ext'] = $image_ext;
        return $constantes;
    }
}

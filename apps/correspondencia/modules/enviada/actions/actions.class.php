<?php

require_once dirname(__FILE__) . '/../lib/enviadaGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/enviadaGeneratorHelper.class.php';

/**
 * enviada actions.
 *
 * @package    siglas-(institucion)
 * @subpackage enviada
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class enviadaActions extends autoEnviadaActions {

    public function executeIndex(sfWebRequest $request) {
        $this->getUser()->getAttributeHolder()->remove('header_ruta');
        $this->getUser()->getAttributeHolder()->remove('correspondencia');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
        $this->getUser()->getAttributeHolder()->remove('seguimiento_externa');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_padre_id');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_formulario');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_grupo');
        $this->getUser()->getAttributeHolder()->remove('pag_seguimiento_atras');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_n_emisor');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_privado');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_emisor_unidad');
        $this->getUser()->getAttributeHolder()->remove('formatos_correlativo');
        $this->getUser()->getAttributeHolder()->remove('formatos_legitimos');
        $this->getUser()->getAttributeHolder()->remove('call_module_master');
        $this->getUser()->getAttributeHolder()->remove('tercerizado');


        $this->getUser()->getAttributeHolder()->remove('nueva_recepcion');

        $ultima_tocada = Doctrine::getTable('Correspondencia_UltimaVista')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        if ($ultima_tocada) {
            $this->getUser()->setAttribute('ultima_vista_enviada', $ultima_tocada->getCorrespondenciaEnviadaId());
        } else {
            $this->getUser()->setAttribute('ultima_vista_enviada', 0);
        }



        $firmas_delegadas = Doctrine::getTable('Acceso_AccionDelegada')->findByUsuarioDelegadoIdAndAccionAndStatus($this->getUser()->getAttribute('usuario_id'), 'firmar_correspondencia', 'A');

        $firmas = array();
        foreach ($firmas_delegadas as $firma_delegada) {
            $usuario = Doctrine::getTable('Acceso_Usuario')->find($firma_delegada->getUsuarioDelegaId());
            $firmas[$firma_delegada->getId()] = $usuario->getUsuarioEnlaceId();
        }

        if (count($firmas) > 0)
            $this->getUser()->setAttribute('firmas_delegadas', $firmas);
        else
            $this->getUser()->getAttributeHolder()->remove('firmas_delegadas');



        // sorting
        if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort'))) {
            $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
        }

        // pager
        if ($request->getParameter('page')) {
            $this->setPage($request->getParameter('page'));
        }

        $this->pager = $this->getPager();
        $this->sort = $this->getSort();
    }
    
    public function executeSetearBandejaDefecto(sfWebRequest $request) {
        $bandeja_defecto = $request->getParameter('status_defecto');
        
        $usuario = Doctrine::getTable('Acceso_Usuario')->find($this->getUser()->getAttribute('usuario_id'));
        $variables_entorno = sfYaml::load($usuario->getVariablesEntorno());
        $variables_entorno['correspondencia']['bandeja_enviada_defecto'] = $bandeja_defecto;
        $usuario->setVariablesEntorno(sfYAML::dump($variables_entorno));
        $usuario->save();
        
        $this->getUser()->setAttribute('sf_variables_entorno', $variables_entorno);
        exit();
    }
    
    public function executeNueva(sfWebRequest $request) {
        $this->getUser()->getAttributeHolder()->remove('header_ruta');
        $this->getUser()->getAttributeHolder()->remove('correspondencia');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
        $this->getUser()->getAttributeHolder()->remove('seguimiento_externa');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_padre_id');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_formulario');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_grupo');
        $this->getUser()->getAttributeHolder()->remove('pag_seguimiento_atras');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_n_emisor');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_privado');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_emisor_unidad');
        $this->getUser()->getAttributeHolder()->remove('formatos_correlativo');
        $this->getUser()->getAttributeHolder()->remove('call_module_master');
        $this->getUser()->getAttributeHolder()->remove('expediente_solicitud_id');

        $this->redirect('formatos/index');
    }

    public function executeFilter(sfWebRequest $request) {
        $this->setPage(1);

        if ($request->hasParameter('_reset')) {
            $this->setFilters($this->configuration->getFilterDefaults());

            $this->redirect('@correspondencia_correspondencia');
        }

        $this->filters = $this->configuration->getFilterForm($this->getFilters());

        $this->filters->bind($request->getParameter($this->filters->getName()));
        if ($this->filters->isValid()) {
            $this->setFilters($this->filters->getValues());

            $this->redirect('@correspondencia_correspondencia');
        }

        $this->pager = $this->getPager();
        $this->sort = $this->getSort();

        $this->setTemplate('index');
    }

    public function executeEdit(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaEnviada($id);
        
        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        if($correspondencia->getStatus()=='C'){
            $this->getUser()->setAttribute('correspondencia_id', $id);

            $this->correspondencia_correspondencia = $this->getRoute()->getObject();
            $this->form = $this->configuration->getForm($this->correspondencia_correspondencia);

            $this->getUser()->setAttribute('correspondencia_n_emisor', $this->correspondencia_correspondencia->getNCorrespondenciaEmisor());
            $this->getUser()->setAttribute('correspondencia_privado', $this->correspondencia_correspondencia->getPrivado());
            $this->getUser()->setAttribute('correspondencia_emisor_unidad', $this->correspondencia_correspondencia->getEmisorUnidadId());

            $this->redirect('formatos/index');
        } else {
            if($correspondencia->getStatus()=='E'){
                $this->getUser()->setFlash('error', 'La correspondencia no se puede editar ya que ha sido firmada y enviada.');
            } else if($correspondencia->getStatus()=='L'){
                $this->getUser()->setFlash('error', 'La correspondencia no se puede editar ya que ha sido firmada, enviada y recibida.');
            } else if($correspondencia->getStatus()=='P'){
                $this->getUser()->setFlash('error', 'La correspondencia no se puede editar, la misma esta pausada, el firmante debe eliminar la firma para poder editar.');
            } else if($correspondencia->getStatus()=='F'){
                $this->getUser()->setFlash('error', 'La correspondencia no se puede editar, la misma esta firmada, el firmante debe eliminar la firma para poder editar.');
            } else {
                $this->getUser()->setFlash('error', 'La correspondencia no se puede editar.');
            }
            
            $this->redirect('enviada/index');
        }
        
        exit();
    }

    public function executeGenerarCorrelativo(sfWebRequest $request) {
        $id = $request->getParameter('id');
        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        $correlativo_activo = new correlativosGenerador();
        $listo = 0;
        while ($listo == 0) {
            $correlativo_final = $correlativo_activo->generarDeUnidad($correspondencia->getUnidadCorrelativoId());
            $correspondencia_find = Doctrine::getTable('Correspondencia_Correspondencia')->findByNCorrespondenciaEmisor($correlativo_final);
            if (!$correspondencia_find[0]['id']) {
                $listo = 1;
            }
        }

        $correspondencia->setNCorrespondenciaEmisor($correlativo_final);
        $correspondencia->save();

        $this->redirect('enviada/index');
    }

    public function executeUltimaVistaEnviada($id) {
        $ultima_tocada = Doctrine::getTable('Correspondencia_UltimaVista')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        if ($ultima_tocada) {
            $ultima_tocada->setCorrespondenciaEnviadaId($id);
            @$ultima_tocada->save();
        } else {
            $ultima_tocada = new Correspondencia_UltimaVista();
            $ultima_tocada->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $ultima_tocada->setCorrespondenciaEnviadaId($id);
            $ultima_tocada->setCorrespondenciaRecibidaId($id);
            $ultima_tocada->setCorrespondenciaExternaId($id);
            $ultima_tocada->save();
        }
    }

    public function executeExcel(sfWebRequest $request) {
        $tableMethod = $this->configuration->getTableMethod();
        if (null === $this->filters) {
            $this->filters = $this->configuration->getFilterForm($this->getFilters());
        }

        $this->filters->setTableMethod($tableMethod);

        $query = $this->filters->buildQuery($this->getFilters());

        $this->excel = $query->execute();
        $this->setLayout(false);
        $this->getResponse()->clearHttpHeaders();
    }

    public function executeFirmar(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $firmante_id = $this->getUser()->getAttribute('funcionario_id');
        if ($request->getParameter('fd')) {
            $firmas_delegadas = $this->getUser()->getAttribute('firmas_delegadas');

            foreach ($firmas_delegadas as $delega_id => $firma_delegada)
                if ($request->getParameter('fd') == $firma_delegada) {
                    $accion_delegada_id = $delega_id;
                    $firmante_id = $firma_delegada;
                }
        }

        $firmante = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findOneByCorrespondenciaIdAndFuncionarioId($id, $firmante_id);

        // SI ESTA FIRMANDO DE FORMA CERTIFICADA SE VERIFICA QUE EL CERTIFICADO ESTA ASIGNADO AL FUNCIONARIO
        if($request->getParameter('signature_packet')){
            $paquete_crypt = $request->getParameter('signature_packet');
            $signature_open = SignSiglas::desempaquetar($paquete_crypt);

            $certificado_activo = Doctrine::getTable('Funcionarios_FuncionarioCargoCertificado')->findOneByFuncionarioCargoIdAndStatus($firmante->getFuncionarioCargoId(),'A');

            if($certificado_activo){
                $certificado_activo_hash_512 = hash('sha512', $certificado_activo->getCertificado());
                $certificado_open_hash_512 = hash('sha512', $signature_open['header']['certificado']);

                if($certificado_activo_hash_512 != $certificado_open_hash_512){
                    $this->getUser()->setFlash('error', 'El certificado usado no es el que se encuentra registrado en el SIGLAS a su nombre, por lo tanto NO puede usar este certificado para firmar su correspondencia.');

                    $this->redirect('enviada/index');
                }
            } else {
                $this->getUser()->setFlash('error', 'Usted no tiene registrados certificados activos en el SIGLAS, por lo tanto NO puede usar este certificado para firmar su correspondencia.');

                $this->redirect('enviada/index');
            }
        }
        // FIN DE VERIFICACION DE CERTIFICADO ASIGNADO

        $this->getUser()->setAttribute('correspondencia_id', $id);


        $this->executeUltimaVistaEnviada($id);

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        if ($correspondencia->get('status') == 'E' ||
                $correspondencia->get('status') == 'P' ||
                $correspondencia->get('status') == 'C' ||
                $correspondencia->get('status') == 'D' ||
                $correspondencia->get('status') == 'A') {

//            $firmante_id = $this->getUser()->getAttribute('funcionario_id');
//            if ($request->getParameter('fd')) {
//                $firmas_delegadas = $this->getUser()->getAttribute('firmas_delegadas');
//
//                foreach ($firmas_delegadas as $delega_id => $firma_delegada)
//                    if ($request->getParameter('fd') == $firma_delegada) {
//                        $accion_delegada_id = $delega_id;
//                        $firmante_id = $firma_delegada;
//                    }
//            }
//
//            $firmante = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
//                    ->findOneByCorrespondenciaIdAndFuncionarioId($id, $firmante_id);

            if ($firmante->getId()) {
                $firmante->setFirma('S');

                if ($request->getParameter('fd')) {
                    $firmante->setAccionDelegadaId($accion_delegada_id);
                    $firmante->setFuncionarioDelegadoId($this->getUser()->getAttribute('funcionario_id'));
                } else {
                    $firmante->setAccionDelegadaId(null);
                    $firmante->setFuncionarioDelegadoId(null);
                }

                if($request->getParameter('signature_packet')){
                    if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    else if( isset( $_SERVER ['HTTP_VIA'] ))  $ip = $_SERVER['HTTP_VIA'];
                    else if( isset( $_SERVER ['REMOTE_ADDR'] ))  $ip = $_SERVER['REMOTE_ADDR'];


                    // INICIO VERIFICACION DE CAMBIO DE CONFIGURACION O IP DE ACCESO
                    $certificado_activo_configuraciones = sfYaml::load($certificado_activo->getConfiguraciones());

                    $config_like=true; $ip_like=false;

                    $config_new = $signature_open['header']['configuracion'].'';

                    for($i=0;$i<count($certificado_activo_configuraciones);$i++){

                        if($certificado_activo_configuraciones[$i]['ip'] == $ip){

                            if($certificado_activo_configuraciones[$i]['configuracion']!=$config_new){
                                $certificado_activo_configuraciones[$i]['configuracion']=$config_new;
                                $config_like=false;
                            }
                            $ip_like=true;
                        }
                    }

                    if($ip_like==false){
                        $certificado_activo_configuraciones[$i]['ip'] = $ip;
                        $certificado_activo_configuraciones[$i]['configuracion'] = $config_new;
                    }

                    if($ip_like==false || $config_like==false){
                        $certificado_activo_configuraciones = sfYAML::dump($certificado_activo_configuraciones);

                        $certificado_activo->setConfiguraciones($certificado_activo_configuraciones);
                        $certificado_activo->save();
                    } else {
//                                echo 'NO CAMBIO NADA';
                    }
                    // FIN VERIFICACION DE CAMBIO DE CONFIGURACION O IP DE ACCESO


                    $proteccion['fecha_firma'] = $signature_open['header']['fecha'].'';
                    $proteccion['certificado_id'] = $certificado_activo->getId();
                    $proteccion['algoritmo_firma'] = $signature_open['header']['algoritmoFirma'].'';
                    $proteccion['resultado_firma'] = $signature_open['signature'][$id]['signature'].'';
                    $proteccion['ultima_verificacion'] = date('Y-m-d H:i:s');
                    $proteccion['status_firma'] = 'TRUE';

                    $proteccion = sfYAML::dump($proteccion);

                    $firmante->setProteccion($proteccion);
                }

                $firmante->save();

                //NOTIFICACIONES POST FIRMA

                if ($firmante_id == $this->getUser()->getAttribute('funcionario_id'))
                    $this->getUser()->setFlash('notice', ' La correspondencia se ha firmado con éxito');
                else {
                    $funcionario_delega = Doctrine::getTable('Funcionarios_Funcionario')->find($firmante_id);

                    $this->getUser()->setFlash('notice', ' La correspondencia se ha firmado por ' .
                            $funcionario_delega->getPrimerNombre() . ' ' . $funcionario_delega->getSegundoNombre() . ', ' .
                            $funcionario_delega->getPrimerApellido() . ' ' . $funcionario_delega->getSegundoApellido() . ' con éxito');
                }
            } else {
                $this->getUser()->setFlash('error', ' Usted no esta autorizado para firmar esta correspondencia');
            }
        } else
            $this->getUser()->setFlash('error', ' No puede firmar esta correspondencia porque ya ha sido leida por uno de sus receptores');

        $this->redirect('enviada/index');
    }

    public function executeAnular(sfWebRequest $request) {
        $id = $request->getParameter('id');
        $batch = $request->getParameter('batch');

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);
        $NCorrespondenciaEmisor_anterior = $correspondencia->getNCorrespondenciaEmisor();

        $firmo = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findOneByCorrespondenciaId($id);

        if (($correspondencia->getStatus() == 'C' || $correspondencia->getStatus() == 'P') && $correspondencia->getIdCreate() == $this->getUser()->getAttribute('usuario_id') || $firmo->getFuncionarioId() == $this->getUser()->getAttribute('usuario_id')) {

            if ($firmo->getFirma() == 'N') {
                try
                {
                    $conn = Doctrine_Manager::connection();
                    $conn->beginTransaction();

                    $correlativo = $correspondencia->getNCorrespondenciaEmisor();

                    $correspondencia->setStatus('X');
                    $correspondencia->setNCorrespondenciaEmisor($correspondencia->getNCorrespondenciaEmisor() . '-ANULADO-' . $correspondencia->getId());
                    $correspondencia->setIdDelete($this->getUser()->getAttribute('usuario_id'));
                    $correspondencia->save();

                    if($correspondencia->getId()!=$correlativo){
                        if ($correspondencia->getUnidadCorrelativoId() != null) {
                            $unidad_correlativo = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->find($correspondencia->getUnidadCorrelativoId());

                            $unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidad_correlativo->getUnidadId());

                            $nomenclador = explode("-", $unidad_correlativo->getNomenclador());

                            $i = 0;
                            foreach ($nomenclador as $value) {
                                if ($value == 'Secuencia')
                                    $pos_secuencia = $i;
                                $i++;
                            }
                            
                            $i = 0; $pos_codigo = null;
                            foreach ($nomenclador as $value) {
                                if ($value == 'Codigo')
                                    $pos_codigo = $i;
                                $i++;
                            }

                            $siglas_tmp = str_replace('-', '%', $unidad->getSiglas());
                            $correlativo = str_replace($unidad->getSiglas(), $siglas_tmp, $correlativo);

                            if($pos_codigo != null){
                                $codigo_unidad_tmp = str_replace('-', '%', $unidad->getCodigoUnidad());
                                $correlativo = str_replace($unidad->getCodigoUnidad(), $codigo_unidad_tmp, $correlativo);
                            }
                            
                            $secuencia = explode("-", $correlativo);

                            if ($unidad_correlativo->getSecuencia() > $secuencia[$pos_secuencia]) {
                                $unidad_correlativo->setSecuencia($secuencia[$pos_secuencia]);
                                $unidad_correlativo->save();
                            }
                        } elseif ($correspondencia->getFuncionarioCorrelativoId() != null) {
                            $funcionario_correlativo = Doctrine::getTable('Correspondencia_FuncionarioCorrelativo')->find($correspondencia->getFuncionarioCorrelativoId());

                            $nomenclador = explode("-", $funcionario_correlativo->getNomenclador());

                            $i = 0;
                            foreach ($nomenclador as $value) {
                                if ($value == 'Secuencia')
                                    $pos_secuencia = $i;
                                $i++;
                            }

                            $secuencia = explode("-", $correlativo);
                            $funcionario_correlativo->setSecuencia($secuencia[$pos_secuencia]);
                            $funcionario_correlativo->save();
                        }
                    }

                    //COMUNICACIONES
                    //NOTIFICA AL CREADOR EN CASO DE QUE SEA EL FIRMANTE QUIEN ANULA
                    if($firmo->getFuncionarioId() == $this->getUser()->getAttribute('usuario_id')) {
                        $formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia->getId());

                        $notificacion= new enviadaNotify();
                        $notificacion->notifyDeskAnulada($correspondencia->getIdCreate(), $this->getUser()->getAttribute('usuario_id'), $formato[0]->getTadnombre(), $NCorrespondenciaEmisor_anterior);
                    }

                    // INICIO LIBERAR CACHE DE CORRESPONDENCIA ANULADA
                    $manager = Doctrine_Manager::getInstance();
                    $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
                    $cacheDriver->delete('correspondencia_enviada_list_funcionario_emisor_'.$correspondencia->getId());
                    $cacheDriver->delete('correspondencia_enviada_list_receptor_'.$correspondencia->getId());
                    $cacheDriver->delete('correspondencia_enviada_list_receptor_externos_'.$correspondencia->getId());
                    // FIN LIBERAR CACHE DE CORRESPONDENCIA ANULADA

                    $conn->commit();

                    // INICIO LLAMADO LIBRERIAS MASTER ANULAR
                    // INICIO LLAMADO LIBRERIAS MASTER ANULAR
                    // INICIO LLAMADO LIBRERIAS MASTER ANULAR
                    // INICIO LLAMADO LIBRERIAS MASTER ANULAR

                    $formato = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($id);
                    $parametros = Doctrine::getTable('Correspondencia_TipoFormato')->find($formato->getTipoFormatoId());
                    $parametros = sfYaml::load($parametros->getParametros());

                    if ($parametros['additional_actions']['anular'] == 'true') {
                        $this->redirect('formatos/additionalLibrerias?correspondencia_id=' . $id . '&libreria=anular');
                    }

                    // FIN LLAMADO LIBRERIAS MASTER ANULAR
                    // FIN LLAMADO LIBRERIAS MASTER ANULAR
                    // FIN LLAMADO LIBRERIAS MASTER ANULAR
                    // FIN LLAMADO LIBRERIAS MASTER ANULAR

                } catch(Exception $e){
                      $conn->rollBack();
                    $this->getUser()->setFlash('error', 'ha ocurrido un error inesperado al anular la correspondencia '.$correlativo.', por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnologia.');

                    $this->redirect(sfConfig::get('sf_app_correspondencia_url').'enviada/index');
                }

                if ($batch)
                    $message = 'notice$%&0$%&' . $NCorrespondenciaEmisor_anterior;
                else
                    $this->getUser()->setFlash('notice', ' Se ha anulado la correspondencia '.$correlativo.' exitosamente y se ha liberado el número para su reutilización');
            } else {
                if ($batch)
                    $message = 'error$%&0$%&' . $NCorrespondenciaEmisor_anterior;
                else
                    $this->getUser()->setFlash('error', ' Solo se pueden anular correspondencias que no esten firmadas');
            }
        } else {
            if ($batch)
                $message = 'error$%&1$%&' . $NCorrespondenciaEmisor_anterior;
            else
                $this->getUser()->setFlash('error', ' Solo se pueden anular correspondencias que esten en estatus de creación o pausado');
        }

        if ($batch)
            return $message;
        else
            $this->redirect('enviada/index');
    }

    protected function executeBatchAnular(sfWebRequest $request) {
        $ids = $request->getParameter('ids');

        $request_single = $request;
        $request_single->setParameter('batch', true);

        $messages = array();
        foreach ($ids as $id) {
            $request_single->setParameter('id', $id);
            $message = $this->executeAnular($request_single);

            $message = explode("$%&", $message);

            if (!isset($messages[$message[0]][$message[1]]))
                $messages[$message[0]][$message[1]] = '';

            $messages[$message[0]][$message[1]] .= $message[2] . '$%&';
        }

        $notice = '';
        $error = '';

        foreach ($messages as $tipo => $categoria) {
            if ($tipo == 'notice') {
                if (isset($categoria[0])) {
                    if (count(explode("$%&", $categoria[0])) - 1 == count($ids))
                        $notice .= '&#x25CF; Se han anulado las correspondencias exitosamente y se han liberado los números para su reutilización.';
                    else {
                        $pre_notice = str_replace('$%&', ', ', $categoria[0]) . '$%&';
                        $pre_notice = str_replace(', $%&', '', $pre_notice);
                        $notice .= '&#x25CF; Se han anulado las correspondencias ' . $pre_notice . ' y se han liberado los números para su reutilización.';
                    }
                }
            } else {
                if (isset($categoria[0]))
                    if (count(explode("$%&", $categoria[0])) - 1 == count($ids))
                        $error .= '&#x25CF; Solo se pueden anular correspondencias que no esten firmadas.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[0]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; Solo se pueden anular correspondencias ' . $pre_error . ' si no estan firmadas.';
                    }

                if (isset($categoria[1])) {
                    if ($error != '')
                        $error .= '<br/>';
                    if (count(explode("$%&", $categoria[1])) - 1 == count($ids))
                        $error .= '&#x25CF; Solo se pueden anular las correspondencias si estan en estatus de creación y han sido creadas por usted.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[1]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; Solo se pueden anular las correspondencias ' . $pre_error . ' si estan en estatus de creación y han sido creadas por usted.';
                    }
                }
            }
        }

        if ($error != '')
            $this->getUser()->setFlash('error', $error);
        if ($notice != '')
            $this->getUser()->setFlash('notice', $notice);
        $this->redirect('enviada/index');
    }

    public function executeFirmarEnviar(sfWebRequest $request) {
        $signature_open=null;
        $id = $request->getParameter('id');
        
        $firmante_id = $this->getUser()->getAttribute('funcionario_id');
        
        if ($request->getParameter('fd')) {
            $firmas_delegadas = $this->getUser()->getAttribute('firmas_delegadas');

            foreach ($firmas_delegadas as $delega_id => $firma_delegada)
                if ($request->getParameter('fd') == $firma_delegada) {
                    $accion_delegada_id = $delega_id;
                    $firmante_id = $firma_delegada;
                }
        }
//echo $firmante_id.'->'.$id; exit();
        $firmante = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
                ->findOneByCorrespondenciaIdAndFuncionarioId($id, $firmante_id);

        // SI ESTA FIRMANDO DE FORMA CERTIFICADA SE VERIFICA QUE EL CERTIFICADO ESTA ASIGNADO AL FUNCIONARIO
        if($request->getParameter('signature_packet') != NULL){     
            
            $paquete_crypt = $request->getParameter('signature_packet');
            $signature_open = SignSiglas::desempaquetar($paquete_crypt);
            
            if($signature_open==''){
                $this->getUser()->setFlash('error', 'No se pudo procesar la firma electrónica, por favor intente de nuevo.');

                $this->redirect('enviada/index');
            }
            
//            echo "<pre>";
//            echo $id;
//            print_r($signature_open['signature']);
//            echo $id.'----><br/>'.$id.'<br/>';
//            print_r($signature_open);
//            exit();

            $certificado_activo = Doctrine::getTable('Funcionarios_FuncionarioCargoCertificado')->findOneByFuncionarioCargoIdAndStatus($firmante->getFuncionarioCargoId(),'A');

            if($certificado_activo){
                $certificado_activo_hash_512 = hash('sha512', $certificado_activo->getCertificado());
                $certificado_open_hash_512 = hash('sha512', $signature_open['header']['certificado']);

                if($certificado_activo_hash_512 != $certificado_open_hash_512){
                    $this->getUser()->setFlash('error', 'El certificado usado no es el que se encuentra registrado en el SIGLAS a su nombre, por lo tanto NO puede usar este certificado para firmar su correspondencia.');

                    $this->redirect('enviada/index');
                }
            } else {
                $this->getUser()->setFlash('error', 'Usted no tiene registrados certificados activos en el SIGLAS, por lo tanto NO puede usar este certificado para firmar su correspondencia.');

                $this->redirect('enviada/index');
            }
        } else {
            $verificacion = 'TRUE';
        }
        // FIN DE VERIFICACION DE CERTIFICADO ASIGNADO

        $batch = $request->getParameter('batch');

        $this->executeUltimaVistaEnviada($id);
        $this->getUser()->setAttribute('correspondencia_id', $id);

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        if ($correspondencia->get('status') == 'P' ||
                $correspondencia->get('status') == 'C' ||
                $correspondencia->get('status') == 'D' ||
                $correspondencia->get('status') == 'A') {




            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();



//            $firmante_id = $this->getUser()->getAttribute('funcionario_id');
//            if ($request->getParameter('fd')) {
//                $firmas_delegadas = $this->getUser()->getAttribute('firmas_delegadas');
//
//                foreach ($firmas_delegadas as $delega_id => $firma_delegada)
//                    if ($request->getParameter('fd') == $firma_delegada) {
//                        $accion_delegada_id = $delega_id;
//                        $firmante_id = $firma_delegada;
//                    }
//            }
//
//            $firmante = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
//                    ->findOneByCorrespondenciaIdAndFuncionarioId($id, $firmante_id);

            if ($firmante->getId()) {
//                echo 'entro en firmante'; exit();
                


                if ($request->getParameter('fd')) {
                    $firmante->setAccionDelegadaId($accion_delegada_id);
                    $firmante->setFuncionarioDelegadoId($this->getUser()->getAttribute('funcionario_id'));
                } else {
                    $firmante->setAccionDelegadaId(null);
                    $firmante->setFuncionarioDelegadoId(null);
                }

                $verificacion = TRUE;
                if($signature_open != NULL){
//                echo 'entro en proteccion'; exit();
                    if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    else if( isset( $_SERVER ['HTTP_VIA'] ))  $ip = $_SERVER['HTTP_VIA'];
                    else if( isset( $_SERVER ['REMOTE_ADDR'] ))  $ip = $_SERVER['REMOTE_ADDR'];
                    
                    ///////////////////////////////////////////////////////////
                    /////////////////////////////////////////////////////////////
                    /////////////////////////////////////////////////////////////
                    /////////////////////////////////////////////////////////////
                    // INICIO VERIFICACION DE CAMBIO DE CONFIGURACION O IP DE ACCESO
                    $certificado_activo_configuraciones = sfYaml::load($certificado_activo->getConfiguraciones());

                    $config_like=true; $ip_like=false;

                    $config_new = $signature_open['header']['configuracion'].'';

                    for($i=0;$i<count($certificado_activo_configuraciones);$i++){

                        if($certificado_activo_configuraciones[$i]['ip'] == $ip){

                            if($certificado_activo_configuraciones[$i]['configuracion']!=$config_new){
                                $certificado_activo_configuraciones[$i]['configuracion']=$config_new;
                                $config_like=false;
                            }
                            $ip_like=true;
                        }
                    }

                    if($ip_like==false){
                        $certificado_activo_configuraciones[$i]['ip'] = $ip;
                        $certificado_activo_configuraciones[$i]['configuracion'] = $config_new;
                    }

                    if($ip_like==false || $config_like==false){
                        $certificado_activo_configuraciones = sfYAML::dump($certificado_activo_configuraciones);

                        $certificado_activo->setConfiguraciones($certificado_activo_configuraciones);
                        $certificado_activo->save();
                    } else {
//                                echo 'NO CAMBIO NADA';
                    }
                    // FIN VERIFICACION DE CAMBIO DE CONFIGURACION O IP DE ACCESO
                    ///////////////////////////////////////////////////////////
                    ///////////////////////////////////////////////////////////
                    ///////////////////////////////////////////////////////////
                    ///////////////////////////////////////////////////////////
                    
                    
                    $proteccion['fecha_firma'] = $signature_open['header']['fecha'].'';
                    $proteccion['certificado_id'] = $certificado_activo->getId();
                    $proteccion['algoritmo_firma'] = $signature_open['header']['algoritmoFirma'].'';
                    $proteccion['resultado_firma'] = $signature_open['signature'][$id]['signature'].'';
                    $proteccion['ultima_verificacion'] = date('Y-m-d H:i:s');
                    
                    // VERIFICAR
                    // // VERIFICAR
                    // // VERIFICAR
                    // // VERIFICAR
                    // // VERIFICAR
//                    $verificacion = $ucaima->verificarFirmaPaquete(dataToSign::concatenar($correspondencia->getId()), $indice_firma);
                    $verificacion = TRUE;
                    $proteccion['status_firma'] = strtoupper($verificacion);
//$conn->rollBack();
//                    print_r($proteccion); exit();
                    $proteccion = sfYAML::dump($proteccion);

                    $firmante->setProteccion($proteccion);
                }

                if($verificacion == TRUE){
//                echo 'entro en firma true'; exit();
                    $firmante->setFirma('S');
                    $firmante->save();
                } else {
                    $conn->rollBack();
                    $message = 'error$%&5$%&' . $correspondencia->getNCorrespondenciaEmisor();
                    
                    return $message;
                }
//echo $firmante->getFirma();
//$conn->rollBack();
//exit();
                $c_firman = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findBy('correspondencia_id', $id);

                $c = 0;
                $cg = 0;
                foreach ($c_firman as $list_firman) {
                    $cg++;

                    if ($list_firman->getFirma() == 'N')
                        $c++;
                }

                if ($c == 0) {
                    if ($cg > 0) {
                        $c_firman = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($id);

                        if (count($c_firman) > 0) {
                            $correspondencia_receptor = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($id);
                            $correspondencia_receptor_organismo = Doctrine::getTable('Correspondencia_ReceptorOrganismo')->findBy('correspondencia_id', $id);
                            $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

                            if ((count($correspondencia_receptor) > 0) || (count($correspondencia_receptor_organismo) > 0)) {
                                $correspondencia->setFEnvio(date('Y-m-d H:i:s'));
                                $correspondencia->setStatus('E');
                                $correspondencia->save();

                                $i = 0;
                                $unidades = '';
                                $email_institucional = array();
                                $email_personal = array();
                                $telf_movil = array();
                                $email_internos = array();
                                $funcionario_receptor_id = array();
                                foreach ($correspondencia_receptor as $receptor) {
                                    $unidades .= $receptor->getUnombre() . ', ';

                                    $funcionario_receptor_id = array_merge($funcionario_receptor_id, array($receptor->getFuncionarioId()));

                                    if ($receptor->getEmailInstitucional())
                                        $email_institucional = array_merge($email_institucional, array($receptor->getPn() . ', ' . $receptor->getPa() . '%%%' . $receptor->getCtnombre() . '%%%' . $receptor->getUnombre() . '%%%' . $receptor->getEmailInstitucional()));
                                    if ($receptor->getEmailPersonal())
                                        $email_personal = array_merge($email_personal, array($receptor->getPn() . ', ' . $receptor->getPa() . '%%%' . $receptor->getCtnombre() . '%%%' . $receptor->getUnombre() . '%%%' . $receptor->getEmailPersonal()));
                                    if ($receptor->getTelfMovil())
                                        $telf_movil = array_merge($telf_movil, array($receptor->getTelfMovil()));

                                    $i++;
                                }

                                $unidades .= '$%&';
                                $unidades = str_replace(', $%&', '', $unidades);

                                $funcionario_receptor_id = array_unique($funcionario_receptor_id);
                                $email_institucional = array_unique($email_institucional);
                                $email_personal = array_unique($email_personal);
                                $email_internos = array_merge($email_institucional, $email_personal);
                                $telf_movil = array_unique($telf_movil);



                                // INICIO NOTIFICACIONES INTERNAS
                                // INICIO NOTIFICACIONES INTERNAS
                                // INICIO NOTIFICACIONES INTERNAS
                                // INICIO NOTIFICACIONES INTERNAS

                                //
                                // COMUNICACIONES
                                //
                                $notificacion= new enviadaNotify();

                                $emisor_actual = Doctrine::getTable('Organigrama_Unidad')->find($this->getUser()->getAttribute('funcionario_unidad_id'));

                                $formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($id);

                                $vb = Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByCorrespondenciaIdAndStatus($id, 'V');

                                $funcios_noti_enviar= Array();
                                foreach($vb as $values)
                                    $funcios_noti_enviar[]= $values->getFuncionarioId();

                                if($this->getUser()->getAttribute('funcionario_id') != $correspondencia->getIdCreate())
                                    $funcios_noti_enviar[] = $correspondencia->getIdCreate();

                                //NOTIFICACION DESK PARA RESUMEN DIARIO
                                foreach($funcionario_receptor_id as $funcionario_id) {
                                    $notificacion->notifyDeskResumenRecibidas($funcionario_id, $this->getUser()->getAttribute('funcionario_id'), $formato[0]->getTadnombre(), $emisor_actual->getNombre(), $correspondencia->getFEnvio(), $correspondencia->getNCorrespondenciaEmisor());
                                }

                                //NOTIFICACION DESK PARA REDACTOR Y VB's
                                foreach($funcios_noti_enviar as $funcionario_id) {
                                    $notificacion->notifyDeskEnviada($funcionario_id, $this->getUser()->getAttribute('funcionario_id'), $formato[0]->getTadnombre(), $correspondencia->getNCorrespondenciaEmisor(), $id);
                                }
                                //NOTIFICACIONES QUE REQUIEREN EMAIL O NUMEROS MOVILES
                                if (count($email_internos) > 0 || count($telf_movil) > 0) {
                                    //NOTIFICACION EMAIL
                                    if (count($email_internos) > 0) {
                                        $sf_email = sfYaml::load(sfConfig::get('sf_root_dir')."/config/siglas/email.yml");
                                        if($sf_email['activo']==true) {
                                            $notificacion->notifyEmail($email_internos, $emisor_actual->getNombre(), $formato[0]->getTadnombre(), $correspondencia->getFEnvio(), $correspondencia->getNCorrespondenciaEmisor());
                                        }
                                    }

                                    //NOTIFICACION SMS
                                    if (count($telf_movil) > 0) {
                                        $sf_sms = sfYaml::load(sfConfig::get('sf_root_dir')."/config/siglas/sms.yml");
                                        if($sf_sms['activo']==true && $sf_sms['aplicaciones']['correspondencia']['activo']==true){
                                            $notificacion->notifySms($telf_movil, $emisor_actual->getNombre(), $formato[0]->getTadnombre(), $correspondencia->getFEnvio(), $correspondencia->getNCorrespondenciaEmisor());
                                        }
                                    }
                                }
                                //
                                // FIN DE COMUNICACIONES
                                //

                                // FIN NOTIFICACIONES INTERNAS
                                // FIN NOTIFICACIONES INTERNAS
                                // FIN NOTIFICACIONES INTERNAS
                                // FIN NOTIFICACIONES INTERNAS

                                // INICIO NOTIFICACIONES EXTERNAS
                                // INICIO NOTIFICACIONES EXTERNAS
                                // INICIO NOTIFICACIONES EXTERNAS
                                // INICIO NOTIFICACIONES EXTERNAS

                                $correspondencia_inicial = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia->getGrupoCorrespondencia());
                                if ($correspondencia_inicial['email_externo'] || $correspondencia_inicial['telf_movil_externo']) {
                                    $ce_para = Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($correspondencia->getId());

                                    $x = 0;
                                    foreach ($ce_para as $list_ce_para) {
                                        $receptores = $list_ce_para->getReceptorOrganismo();

                                        if ($unidades != '$%&')
                                            $receptores .= ' con copia a ' . $unidades;

                                        $x++;
                                    }
                                    if ($x == 0)
                                        $receptores = $unidades;

                                    $rs = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia->getPadreId());
                                    $n_correspondencia_padre = $rs['n_correspondencia_emisor'];
                                    $rs = Doctrine::getTable('Organigrama_Unidad')->find($this->getUser()->getAttribute('funcionario_unidad_id'));
                                    $emisor_actual = $rs['nombre'];

                                    list($fecha, $hora) = explode(' ', $correspondencia['f_envio']);
                                    list($h, $m, $s) = explode(':', $hora);
                                    list($year_now, $month_now, $day_now) = explode('-', $fecha);
                                    $month_now+=0;

                                    $AP = 'am';
                                    if ($h > 12) {
                                        $h = $h - 12;
                                        $AP = 'pm';
                                    }
                                    if ($h == 12) {
                                        $AP = 'M';
                                    }

                                    $months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
                                    $date = $day_now . " de " . $months[$month_now] . " de " . $year_now . " a las " . $h . ":" . $m . " " . $AP;

                                    //################################## INICIO DE CORREO ELECTRONICO ##################################

                                        $organismox = Doctrine::getTable('Organismos_Organismo')->find($correspondencia_inicial['emisor_organismo_id']);
                                        $personax = Doctrine::getTable('Organismos_Persona')->find($correspondencia_inicial['emisor_persona_id']);
                                        $personacargox = Doctrine::getTable('Organismos_PersonaCargo')->find($correspondencia_inicial['emisor_persona_cargo_id']);

                                    if ($correspondencia_inicial['email_externo'] != null) {
                                        $mensaje['mensaje'] = sfConfig::get('sf_organismo') . "<br/>";
                                        $mensaje['mensaje'] .= "Sistema Integral para la Gestión Laboral, Administrativa y de Servicios (SIGLAS)<br/><br/><br/>";

                                        $mensaje['mensaje'] .= "Srs.-<br/>";
                                        $mensaje['mensaje'] .= $organismox['nombre'] . "<br/>";
                                        $mensaje['mensaje'] .= $personax['nombre_simple'] . "<br/>";
                                        $mensaje['mensaje'] .= $personacargox['nombre'] . "<br/><br/><br/>";

                                        $mensaje['mensaje'] .= "Reciba un cordial saludo Bolivariano y Revolucionario, nos dirigimos a usted en la " .
                                                "oportunidad de informarle que la correspondencia que ha enviado a este organismo con el número \"" .
                                                $correspondencia_inicial['n_correspondencia_externa'] . "\" que se encontraba actualmente en " . $emisor_actual .
                                                " con el número \"" . $n_correspondencia_padre .
                                                "\" se ha redireccionado a " . $receptores .
                                                " en fecha " . $date . " con el número \"" .
                                                $correspondencia['n_correspondencia_emisor'] . "\".\n\n";

                                        $mensaje['mensaje'] .= "<br/><br/>Con la intención de atender los planteamientos realizados e informar los resultados obtenidos " .
                                                " y al mismo tiempo reiterándole el compromiso de trabajo colectivo para la construcción de la patria socialista, " .
                                                "se despide. <br/><br/>" . sfConfig::get('sf_organismo');

                                        $mensaje['emisor'] = 'Correspondencia';
                                        $mensaje['receptor'] = $personax['nombre_simple'];

                                        Email::notificacion('correspondencia', $correspondencia_inicial['email_externo'], $mensaje, 'inmediata');
                                    }

                                    //################################## FIN DE CORREO ELECTRONICO ##################################

                                    //################################## INICIO DE SMS ##################################

                                    if ($correspondencia_inicial['telf_movil_externo'] != null) {
                                        $mensaje['emisor'] = 'Correspondencia';
                                        $mensaje['mensaje'] = "Reciba un cordial saludo, nos dirigimos a usted en la " .
                                                "oportunidad de informarle que la correspondencia recibida por " . $emisor_actual . " con el número \"" .
                                                $n_correspondencia_padre .
                                                "\" se ha redireccionado a " . $receptores .
                                                " en fecha " . $date . " con el número \"" .
                                                $correspondencia['n_correspondencia_emisor'] . "\".";

                                        Sms::notificacion_sistema('correspondencia', $correspondencia_inicial['telf_movil_externo'], $mensaje);
                                    }
                                    //################################## FIN DE SMS ##################################
                                }


                                // FIN NOTIFICACIONES EXTERNAS
                                // FIN NOTIFICACIONES EXTERNAS
                                // FIN NOTIFICACIONES EXTERNAS
                                // FIN NOTIFICACIONES EXTERNAS

                                $conn->commit();

                                if ($batch)
                                    $message = 'notice$%&0$%&' . $correspondencia->getNCorrespondenciaEmisor();
                                else {
                                    if ($firmante_id == $this->getUser()->getAttribute('funcionario_id'))
                                        $this->getUser()->setFlash('notice', ' La correspondencia se ha firmado y enviado con éxito');
                                    else {
                                        $funcionario_delega = Doctrine::getTable('Funcionarios_Funcionario')->find($firmante_id);

                                        $this->getUser()->setFlash('notice', ' La correspondencia se ha firmado y enviado por ' .
                                                $funcionario_delega->getPrimerNombre() . ' ' . $funcionario_delega->getSegundoNombre() . ', ' .
                                                $funcionario_delega->getPrimerApellido() . ' ' . $funcionario_delega->getSegundoApellido() . ' con éxito');
                                    }
                                }


                                // INICIO LLAMADO LIBRERIAS MASTER ENVIAR
                                // INICIO LLAMADO LIBRERIAS MASTER ENVIAR
                                // INICIO LLAMADO LIBRERIAS MASTER ENVIAR
                                // INICIO LLAMADO LIBRERIAS MASTER ENVIAR

                                $formato = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($id);
                                $parametros = Doctrine::getTable('Correspondencia_TipoFormato')->find($formato->getTipoFormatoId());
                                $parametros = sfYaml::load($parametros->getParametros());

                                if ($parametros['additional_actions']['enviar'] == 'true') {
                                    $this->redirect('formatos/additionalLibrerias?correspondencia_id=' . $id . '&libreria=enviar');
                                }

                                // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                                // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                                // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                                // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                            } else {
                                $conn->rollBack();

                                if ($batch)
                                    $message = 'error$%&1$%&' . $correspondencia->getNCorrespondenciaEmisor();
                                else
                                    $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia debido a que no se a definido ningún receptor de la misma');
                            }
                        } else {
                            $conn->rollBack();

                            if ($batch)
                                $message = 'error$%&2$%&' . $correspondencia->getNCorrespondenciaEmisor();
                            else
                                $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia sin ningun formato anexo.');
                        }
                    } else {
                        $conn->rollBack();

                        if ($batch)
                            $message = 'error$%&3$%&' . $correspondencia->getNCorrespondenciaEmisor();
                        else
                            $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia sin ninguna firma que lo avale.');
                    }
                } else {
                    $conn->rollBack();

                    if ($batch)
                        $message = 'error$%&4$%&' . $correspondencia->getNCorrespondenciaEmisor();
                    else
                        $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia debido a que falta ' . $c . ' firma(s)');
                }
            } else {
                if ($batch)
                    $message = 'error$%&0$%&' . $correspondencia->getNCorrespondenciaEmisor();
                else
                    $this->getUser()->setFlash('error', ' Usted no esta autorizado para firmar esta correspondencia');
            }

            if ($batch)
                return $message;
            else
                $this->redirect('enviada/index');
        }
        else {
            $this->getUser()->setFlash('error', ' No puede firmar esta correspondencia porque ya ha sido leida por uno de sus receptores');
            $this->redirect('enviada/index');
        }
    }

    public function executeBatchFirmarEnviar(sfWebRequest $request) {
        $ids = $request->getParameter('ids');
        $request_single = $request;
        $request_single->setParameter('batch', true);

        $ucaima = NULL;
//            if($request->getParameter('signature_packet')){
        $ucaima = new UcaimaClient("http://127.0.0.1:8080/ucaima");
        $ucaima->extraerPaquete($request->getParameter('signature_packet'));  
//    }
        
        $messages = array();
        foreach ($ids as $indice => $id) {
            $request_single->setParameter('id', $id);
            $request_single->setParameter('indice', $indice);

//            echo 'indice:'.$indice.'::: correspondencia: '.$id.'<br/>';
//            echo $ucaima->getFirmaPaquete($indice);
//            exit();
            
            $message = $this->executeFirmarEnviar($request_single,$ucaima);

            $message = explode("$%&", $message);

            if (!isset($messages[$message[0]][$message[1]]))
                $messages[$message[0]][$message[1]] = '';

            $messages[$message[0]][$message[1]] .= $message[2] . '$%&';
        }

        $notice = '';
        $error = '';

        foreach ($messages as $tipo => $categoria) {
            if ($tipo == 'notice') {
                if (isset($categoria[0]))
                    if (count(explode("$%&", $categoria[0])) - 1 == count($ids))
                        $notice .= '&#x25CF; Las correspondencias se han firmado y enviado con exito.';
                    else {
                        $pre_notice = str_replace('$%&', ', ', $categoria[0]) . '$%&';
                        $pre_notice = str_replace(', $%&', '', $pre_notice);
                        $notice .= '&#x25CF; Las correspondencias ' . $pre_notice . ' se han firmado y enviado con exito.';
                    }
            } else {
                if (isset($categoria[0]))
                    if (count(explode("$%&", $categoria[0])) - 1 == count($ids))
                        $error .= '&#x25CF; Usted no esta autorizado para firmar estas correspondencias.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[0]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; Usted no esta autorizado para firmar las correspondencias ' . $pre_error . '.';
                    }

                if (isset($categoria[1])) {
                    if ($error != '')
                        $error .= '<br/>';
                    if (count(explode("$%&", $categoria[1])) - 1 == count($ids))
                        $error .= '&#x25CF; No se pueden enviar las correspondencias ya que no se han agregado receptores.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[1]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; No se pueden enviar las correspondencias ' . $pre_error . ' ya que no se han agregado receptores.';
                    }
                }

                if (isset($categoria[2])) {
                    if ($error != '')
                        $error .= '<br/>';
                    if (count(explode("$%&", $categoria[2])) - 1 == count($ids))
                        $error .= '&#x25CF; No se pueden enviar las correspondencias sin ningún formato anexo.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[2]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; No se pueden enviar las correspondencias ' . $pre_error . ' sin ningún formato anexo.';
                    }
                }

                if (isset($categoria[3])) {
                    if ($error != '')
                        $error .= '<br/>';
                    if (count(explode("$%&", $categoria[3])) - 1 == count($ids))
                        $error .= '&#x25CF; No se pueden enviar las correspondencias sin ninguna firma que las avale.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[3]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; No se pueden enviar las correspondencias ' . $pre_error . ' sin ninguna firma que las avale.';
                    }
                }

                if (isset($categoria[4])) {
                    if ($error != '')
                        $error .= '<br/>';
                    if (count(explode("$%&", $categoria[4])) - 1 == count($ids))
                        $error .= '&#x25CF; No se pueden enviar las correspondencias debido a que les falta una o mas firmas.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[4]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; No se pueden enviar las correspondencias ' . $pre_error . ' debido a que les falta una o mas firmas.';
                    }
                }
                
                if (isset($categoria[5])) {
                    if ($error != '')
                        $error .= '<br/>';
                    if (count(explode("$%&", $categoria[5])) - 1 == count($ids))
                        $error .= '&#x25CF; No se firmo y envio la correspondencia por error el la certificacion.';
                    else {
                        $pre_error = str_replace('$%&', ', ', $categoria[5]) . '$%&';
                        $pre_error = str_replace(', $%&', '', $pre_error);
                        $error .= '&#x25CF; No se firmaron y enviaron las correspondencias ' . $pre_error . ' por error el la certificacion.';
                    }
                }
            }
        }

        if ($error != '')
            $this->getUser()->setFlash('error', $error);
        if ($notice != '')
            $this->getUser()->setFlash('notice', $notice);
        $this->redirect('enviada/index');
    }

    public function executeQuitarFirma(sfWebRequest $request) {
        $id = $request->getParameter('id');
        $this->getUser()->setAttribute('correspondencia_id', $id);

        $this->executeUltimaVistaEnviada($id);

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        if ($correspondencia->getStatus() == 'E' || $correspondencia->getStatus() == 'P' || $correspondencia->getStatus() == 'C') {
            $firmante_id = $this->getUser()->getAttribute('funcionario_id');
            if ($request->getParameter('fd')) {
                $firmas_delegadas = $this->getUser()->getAttribute('firmas_delegadas');

                foreach ($firmas_delegadas as $delega_id => $firma_delegada)
                    if ($request->getParameter('fd') == $firma_delegada) {
                        $accion_delegada_id = $delega_id;
                        $firmante_id = $firma_delegada;
                    }
            }

            $firmante = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
                    ->findOneByCorrespondenciaIdAndFuncionarioId($id, $firmante_id);

            if ($firmante->getId()) {
                $firmante->setFirma('N');
                
                $corresp = Doctrine::getTable('Correspondencia_Correspondencia')->findOneById($firmante->getCorrespondenciaId());
                if($corresp) {
                    $corresp->setStatus('C');
                    $corresp->save();
                }

                if ($request->getParameter('fd')) {
                    $firmante->setAccionDelegadaId($accion_delegada_id);
                    $firmante->setFuncionarioDelegadoId($this->getUser()->getAttribute('funcionario_id'));
                } else {
                    $firmante->setAccionDelegadaId(null);
                    $firmante->setFuncionarioDelegadoId(null);
                }

                $firmante->setProteccion('');
                $firmante->save();

                if ($firmante_id == $this->getUser()->getAttribute('funcionario_id'))
                    $this->getUser()->setFlash('notice', ' La firma se ha retirado con éxito');
                else {
                    $funcionario_delega = Doctrine::getTable('Funcionarios_Funcionario')->find($firmante_id);

                    $this->getUser()->setFlash('notice', ' La firma de ' .
                            $funcionario_delega->getPrimerNombre() . ' ' . $funcionario_delega->getSegundoNombre() . ', ' .
                            $funcionario_delega->getPrimerApellido() . ' ' . $funcionario_delega->getSegundoApellido() . ' se ha retirado con éxito');
                }
            } else {
                $this->getUser()->setFlash('error', ' Usted no esta autorizado para quitar esta firma de la correspondencia');
            }
        }
        else
            $this->getUser()->setFlash('error', ' A esta correspondencia no se le puede quitar la firma porque ya ha sido leida por uno de sus receptores');

        $this->redirect('enviada/index');
    }

    public function executeEnviar(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaEnviada($id);

        $c_firman = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findBy('correspondencia_id', $id);

        $c = 0;
        $cg = 0;
        foreach ($c_firman as $list_firman) {
            $cg++;

            if ($list_firman->getFirma() == 'N')
                $c++;
        }

        if ($c == 0) {
            if ($cg > 0) {
                $c_firman = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($id);

                if (count($c_firman) > 0) {
                    $correspondencia_receptor = Doctrine::getTable('Correspondencia_Receptor')->findBy('correspondencia_id', $id);
                    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

                    if ((count($correspondencia_receptor) > 0) || ($correspondencia->getReceptorOrganismoId())) {
                        $correspondencia->setF_envio(date('Y-m-d H:i:s'));
                        $correspondencia->setStatus('E');
                        $correspondencia->save();

                        $this->getUser()->setFlash('notice', ' La correspondencia se ha enviado con exito');

                        // INICIO LLAMADO LIBRERIAS MASTER ENVIAR
                        // INICIO LLAMADO LIBRERIAS MASTER ENVIAR
                        // INICIO LLAMADO LIBRERIAS MASTER ENVIAR
                        // INICIO LLAMADO LIBRERIAS MASTER ENVIAR

                        $formato = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($id);
                        $parametros = Doctrine::getTable('Correspondencia_TipoFormato')->find($formato->getTipoFormatoId());
                        $parametros = sfYaml::load($parametros->getParametros());

                        if ($parametros['additional_actions']['enviar'] == 'true') {
                            $this->redirect('formatos/additionalLibrerias?correspondencia_id=' . $id . '&libreria=enviar');
                        }

                        // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                        // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                        // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                        // FIN LLAMADO LIBRERIAS MASTER ENVIAR
                    }
                    else
                        $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia ya que no se a agregado ningun receptor');
                }
                else
                    $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia sin ningun formato anexo');
            }
            else
                $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia sin ninguna firma que lo avale');
        }
        else
            $this->getUser()->setFlash('error', ' No se puede enviar la correspondencia debido a que falta ' . $c . ' firma(s)');

        $this->redirect('enviada/index');
    }

    public function executePausar(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaEnviada($id);

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        if ($correspondencia->getStatus() == 'L')
            $this->getUser()->setFlash('error', ' No se puede pausar la correspondencia ya ha sido leida por uno de sus receptores');
        else {
            $correspondencia->setStatus('P');
            $correspondencia->setFEnvio(null);
            $correspondencia->save();

            $this->getUser()->setFlash('notice', ' La correspondencia se ha pausado con exito');
        }

        $this->redirect('enviada/index');
    }

    public function executeSeguimiento(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaEnviada($id);

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        $this->getUser()->setAttribute('correspondencia_grupo', $correspondencia->get('grupo_correspondencia'));

        $this->redirect('seguimiento/index');
    }

    public function executeFuncionariosUnidad(sfWebRequest $request) {
        if ($request->getParameter('f_id')) {
            //echo "f_id";
            $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptorSelect(array($request->getParameter('u_id')));
            $this->funcionario_selected = $request->getParameter('f_id');
        } else {
            //echo "u_id";
            $this->funcionario_selected = 0;
            $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
        }
    }

    public function executeHojaRuta(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);
        $correspondenciafuncionarioemisor = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($id);
//    $correspondenciareceptororganismo = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($id);
        $correspondenciareceptor = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($id);
        $correspondenciaformato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($id);
        $correspondenciaanexofisico = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($id);

        // ################ INIZIALIZAR EL OBJETO DE PDF  #################
        $config = sfTCPDFPluginConfigHandler::loadConfig('pdf_configs.yml');
        // pdf object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // settings

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(15, PDF_MARGIN_TOP, 10);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->SetHeaderData('gob_pdf.png', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf-> SetAutoPageBreak(True, 90);

        // init pdf doc
//        $pdf->Image('http://' . $_SERVER['SERVER_NAME'] . '/images/organismo/pdf/gob_footer_pdf2.png', 0, 700, 550, 700, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
//        $pdf->setPageMark();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        if ($correspondencia->getFEnvio() == null) {
            $f_envio = "<b>N O  S E  H A  E N V I A D O</b>";
        } else {
            $f_envio = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));
        }

        $n_envio = $correspondencia->getn_correspondencia_emisor();

        $emisor = '';
        foreach ($correspondenciafuncionarioemisor as $list_correspondenciafuncionarioemisor) {
            $emisor.= $list_correspondenciafuncionarioemisor->getUnombre() . ' / ' .
                    $list_correspondenciafuncionarioemisor->getCtnombre() . ' / ' .
                    ucwords(strtolower($list_correspondenciafuncionarioemisor->getPn())) . ' ' .
                    ucwords(strtolower($list_correspondenciafuncionarioemisor->getPa())) . '<br/>';
        }
        if ($emisor != '') {
            $emisor .= '###';
            $emisor = str_replace('<br/>###', '', $emisor);
        }

        $receptor = '';
        foreach ($correspondenciareceptor as $list_correspondenciareceptor) {
            $receptor.= $list_correspondenciareceptor->getUnombre() . ' / ' .
                    $list_correspondenciareceptor->getCtnombre() . ' / ' .
                    ucwords(strtolower($list_correspondenciareceptor->getPn())) . ' ' .
                    ucwords(strtolower($list_correspondenciareceptor->getPa())) . '<br/>';
        }
        if ($receptor != '') {
            $receptor .= '###';
            $receptor = str_replace('<br/>###', '', $receptor);
        }

        $receptor_externo = '';

        $formatos = '';
        foreach ($correspondenciaformato as $list_correspondenciaformato) {
            $formatos .= $list_correspondenciaformato->getTadnombre() . ', ';

            $enunciado_clave = '';
            if ($list_correspondenciaformato->getTipoFormatoId() == 1) {
                //MEMORANDO
                $enunciado_clave = '<b>Asunto: </b>' . $list_correspondenciaformato->getCampoUno();
            }
        }
        if ($formatos != '') {
            $formatos .= '###';
            $formatos = str_replace(', ###', '', $formatos);
        }

        $fisicos = '<table border="1">';
        foreach ($correspondenciaanexofisico as $list_correspondenciaanexofisico) {
            $fisicos .= '<tr><td width="100">' . $list_correspondenciaanexofisico->gettafnombre() . '</td><td width="300">' .
                    $list_correspondenciaanexofisico->getobservacion() . '</td></tr>';
        }
        $fisicos .= '</table>';

        $tbl = <<<EOD
<table width="560" "center">
       <tr>
        <td width="30"><br/><br/></td>
        <td width="500">
            <table width="500">
                <tr>
                    <td width="500" align="center"><br/><h1>Acuse de Recibo Físico</h1><br/></td>
                </tr>
                <tr>
                    <td width="500">
                        <table border="1" width="500" cellpadding="5">
                            <tr>
                                <td width="90"><b>Fecha de Envio:</b></td>
                                <td width="410">$f_envio</td>
                            </tr>
                            <tr>
                                <td width="90"><b>Nº de Envio:</b></td>
                                <td width="410"><h2>$n_envio</h2></td>
                            </tr>
                            <tr>
                                <td width="90"><b>De:</b></td>
                                <td width="410">$emisor</td>
                            </tr>
                            <tr>
                                <td width="90"><b>Para:</b></td>
                                <td width="410">$receptor $receptor_externo</td>
                            </tr>
                            <tr>
                                <td width="90"><b>$formatos:</b></td>
                                <td width="410">$enunciado_clave</td>
                            </tr>
                            <tr>
                                <td width="90"><b>Fisicos:</b></td>
                                <td width="410">$fisicos</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="500">
                        <br/><br/>
                        <table border="1" width="500" cellpadding="5">
                            <tr>
                                <td width="250" colspan="2" align="center"><b>MENSAJERO</b></td>
                                <td width="250" colspan="2" align="center"><b>RECEPTOR</b></td>
                            </tr>
                            <tr>
                                <td width="60"><b>Unidad:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Unidad:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Nombre:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Nombre:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Cédula:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Cédula:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Firma:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Firma:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Sello:</b></td>
                                <td width="190"><br/><br/><br/><br/><br/></td>
                                <td width="60"><b>Sello:</b></td>
                                <td width="190"><br/><br/><br/><br/><br/></td>
                            </tr>
                            <tr>
                                <td width="60"><b>Fecha Envío:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Fecha Recepción:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td width="30">&nbsp;</td>
    </tr>
</table>
EOD;

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output();
        return sfView::NONE;
    }

    public function executeNuevoFormato(sfWebRequest $request) {
        $this->redirect('formatos/index');
    }

    public function executeComienzo(sfWebRequest $request) {
        $formato_id = $request->getParameter('formato');
        $metodo_correlativo = $request->getParameter('metodo_correlativo');

        $correspondencia = new Correspondencia_Correspondencia();


        $this->redirect('formatos/index?formato=' . $formato_id);
    }

    public function executeOrganismos(sfWebRequest $request) {
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');

        $req = Doctrine::getTable('Organismos_Organismo')->getNombres($string);
        $results = array();
        if (count($req) > 0) {
            foreach ($req as $result)
                $results[$result->getId()] = $result->getNombre();
            return $this->renderText(json_encode($results));
        } else {
            $results[0] = '';
            return $this->renderText(json_encode($results));
        }
    }

    public function executeEstadisticas(sfWebRequest $request){
        $boss= false;
        if($this->getUser()->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
        }

        $funcionario_unidades_redactar = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'),'redactar');

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $redactar_array= array();
        foreach($funcionario_unidades_redactar as $unidades_redactar) {
            $redactar_array[]= $unidades_redactar->getAutorizadaUnidadId();
        }

        $nonrepeat= array_merge($cargo_array, $redactar_array);

        $funcionario_unidades= array();
        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }

        $this->funcionario_unidades = $funcionario_unidades;
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

        //LA UNIDAD_ID VIENE POR REQUEST SIN EMBARGO REALIZAR DOBLE VALIDACION
        $boss= false;
        if($this->getUser()->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
        }

        $funcionario_unidades_redactar = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'),'redactar');

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $redactar_array= array();
        foreach($funcionario_unidades_redactar as $unidades_redactar) {
            $redactar_array[]= $unidades_redactar->getAutorizadaUnidadId();
        }

        $nonrepeat= array_merge($cargo_array, $redactar_array);

        $funcionario_unidades= array();
        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }
        //LA UNIDAD_ID VIENE POR REQUEST SIN EMBARGO REALIZAR DOBLE VALIDACION


        $autorizado=false;
        foreach ($funcionario_unidades as $unidad_autorizada){
            if($unidad_autorizada == $unidad_id)
                $autorizado=true;
        }

        if($autorizado==true){
            $estadistica = new Correspondencia_CorrespondenciaStatistic();

            eval('$estadistica_datos = $estadistica->'.$estadistica_tipo.'($unidad_id, $fecha_inicio,$fecha_final);');

            $this->estadistica_datos = $estadistica_datos;
            $this->fecha = "Estadistica generada desde: ".date('d/m/Y',  strtotime($fecha_inicio))." Hasta: ".date('d/m/Y',  strtotime($fecha_final));
            $this->unidad_id = $unidad_id;

            $this->setTemplate('estadisticas/'.$estadistica_tipo);

        } else {
            echo "No esta autorizado para revisar las estadisticas de esta unidad";
            exit();
        }


    }

    public function executeDarVistobueno(sfWebRequest $request)
    {
        $correspondencia_id= $request->getParameter('id');

        $registro_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findOneByCorrespondenciaIdAndFuncionarioId($correspondencia_id, $this->context->getUser()->getAttribute('usuario_id'));
        $registro_vb->setStatus('V');
        $registro_vb->setTurno(FALSE);
        $registro_vb->save();

        $new_turn= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->vistobuenoCorrespondenciaAsc($correspondencia_id);
        $next= '';
        foreach($new_turn as $val) {
            if($val->getOrden() > $registro_vb->getOrden() && $val->getStatus()== 'E') {
                $next= $val;
                break;
            }
        }
        if($next!= '') {
            $next->setTurno(TRUE);
            $next->save();
        }

        $this->getUser()->setFlash('notice', 'Se ha confirmado el visto bueno');

        $this->redirect('enviada/index');
    }

    public function executeResumenPdf(sfWebRequest $request) {
        $id= $request->getParameter('id');
        $notiy= new enviadaNotify();
        $notiy->notiPdf($id);
        exit();
    }

//    public function executeRevertirVb(sfWebRequest $request) {
//        $correspondencia_id= $request->getParameter('id');
//        $action= $request->getParameter('act');
//
//        $all_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByCorrespondenciaId($correspondencia_id);
//
//        $autorizado= FALSE;
//        $mi_vb= '';
//        $i= 0;
//        foreach($all_vb as $value) {
//            //SOLO SE PUEDE REVERTIR A PARTIR DEL 2 MIEMBRO DE LA RUTA DE VB
//            if($value->getFuncionarioId() == $this->getUser()->getAttribute('funcionario_id') && $value->getOrden() > 1) {
//                $mi_vb= $i;
//                $autorizado= TRUE;
//            }
//            $i++;
//        }
//
//        if($autorizado) {
//            //BIFURCACION, REVIERTE UN VB ATRAS O TODOS LOS VB
//            if($action == 'unpaso') {
//                $procede= FALSE;
//                $bef_turn= $mi_vb- 1;
//                //EVITA QUE EL NUEVO TURNO (ANTERIOR) HAYA SIDO UN DESINCORPORADO
//                if($all_vb[$bef_turn]['status'] == 'V') {
//                    $procede= TRUE;
//                }else {
//                    $bef_turn--;
//                    if($all_vb[$bef_turn]['orden'] > 1 && $all_vb[$bef_turn]['status'] == 'V') {
//                        $procede= TRUE;
//                    }
//                }
//
//                if($procede) {
//                    //SETEA EL NUEVO TURNO (MIEMBRO ANTERIOR EN LA RUTA VB)
//                    $reg_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->find($all_vb[$bef_turn]['id']);
//                    $reg_vb->setTurno(TRUE);
//                    $reg_vb->setStatus('E');
//                    $reg_vb->save();
//                    //SETEA EL ACTUAL TURNO
//                    $reg_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->find($all_vb[$mi_vb]['id']);
//                    $reg_vb->setTurno(FALSE);
//                    $reg_vb->save();
//                }else {
//                    $this->getUser()->setFlash('error', ' No pudo revertirse avance en ruta de visto bueno para este documento.');
//                }
//            }else {
//                //SETEA STATUS EN ESPERA TODA LA RUTA PARA ESTE DOC
//                $clear_reg = Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')
//                    ->createQuery()
//                    ->set('status', '?', 'E')
//                    ->set('turno', '?', FALSE)
//                    ->where('correspondencia_id = ?', $correspondencia_id)
//                    ->andWhereNotIn('status', array('D'))
//                    ->execute();
//                //ASIGNACION DEL TURNO
//                $i= 0;
//                foreach($all_vb as $value) {
//                    $orden= 1;
//                    $new_turn= '';
//                    if($value->getOrden()== 1 && $value->getStatus() != 'D') {
//                        $new_turn= $i;
//                    }
//                    $i++;
//                }
//            }
//        }
//
//        exit;
//    }
    
    public function executeEnviarInteroperabilidad(sfWebRequest $request) {
        $correspondencia_id= $request->getParameter('id');
        
        $param['class'] = 'correspondencia';
        $param['function'] = 'recibirExterna';
        
        $ws = new wsOutputCorrespondencia();
        $array_correspondencia = $ws->generarArray($correspondencia_id);
        
        foreach ($array_correspondencia['receptor_externo'] as $receptor_externo) {
            $servidor_confianza = Doctrine::getTable('Siglas_ServidorConfianza')->findOneByOrganismoId($receptor_externo['organismo_id']);

            if($servidor_confianza){

//                echo "<pre>CORRESPONDENCIA<br/>";
//                print_r($array_correspondencia);

                $ws_array['param'] = $param; 
                $ws_array['content'] = $array_correspondencia;

//                echo "<pre>ARRAY COMPLETObr/>";
//                print_r($ws_array);
//                exit();
                $data_sing_and_crypt = trustedServer::encryptAndSing($servidor_confianza->getDominio(),$ws_array);
                
                if(!isset($PK_public_client['error'])){
                    $PK_public_client = trustedServer::openPublicKey($servidor_confianza->getDominio());

                    $tipo['class']=$param['class'];
                    $tipo['function']=$param['function'];
                    
                    $parametros['correspondencia_enviada_id'] = $correspondencia_id;
                    
                    $interoperabilidad_enviada = new Siglas_InteroperabilidadEnviada();
                    $interoperabilidad_enviada->setServidorConfianzaId($servidor_confianza->getId());
                    $interoperabilidad_enviada->setServidorCertificadoId($PK_public_client['crt_id']);
                    $interoperabilidad_enviada->setTipo(sfYAML::dump($tipo));
                    $interoperabilidad_enviada->setFirma($data_sing_and_crypt['sing']);
                    $interoperabilidad_enviada->setCadena(sfYAML::dump($data_sing_and_crypt['data']));
                    $interoperabilidad_enviada->setPaquete(strtotime(date('Y-m-d H:i:s')));
                    $interoperabilidad_enviada->setPartes(1);
                    $interoperabilidad_enviada->setParte(1);
                    $interoperabilidad_enviada->setStatus('E');
                    $interoperabilidad_enviada->setParametros(sfYAML::dump($parametros));
                    $interoperabilidad_enviada->save();
                    // SETEAR COMO PARAMETRO EL ID DE LA CORRESPONDENCIA QUE ENVIA
                    
                    $interoperabilidad_enviada->setPaquete($interoperabilidad_enviada->getId());
                    $interoperabilidad_enviada->save();
                    
                    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);
                    $correspondencia->setInteroperabilidadEnviadaId($interoperabilidad_enviada->getId());
                    $correspondencia->save();
                    
                    $traza = '';
                    $traza['parametros'] = $parametros;
                    $traza['parametros']['interoperabilidad_envio_solicitud_id'] = $interoperabilidad_enviada->getId();
                    $traza['paquete'] = $interoperabilidad_enviada->getId();
                    $traza['partes'] = 1;
                    $traza['parte'] = 1;
                    
                    // ENCRIPTAMOS TRAZA
                    $traza_crypt = trustedServer::encrypt($servidor_confianza->getDominio(),$traza);

                    // INCORPORAMOS INFORMACION DE LA TRAZA AL ENVIO
                    $data_sing_and_crypt['traza'] = $traza_crypt;
                            
                    require_once(sfConfig::get("sf_root_dir").'/lib/ws/nusoap/nusoap.php');

                    echo "<pre>ENVIO<br/>";
                    print_r($data_sing_and_crypt);
//                    exit();
                    // RUTA DEL SERVIDOR Y SERVICIO
                    $wsdl = $servidor_confianza->getDominio()."/ws.php?wsdl";

                    // INSTANCIAMOS EL WS
                    $client = new nusoap_client($wsdl,'wsdl');

                    $param_ws = array('data'=>$data_sing_and_crypt);


                    // LLAMADO DEl SERVICIO PARA QUE LLENE LA VARIABLE $response
                    $response = $client->call('recibir', $param_ws);

                    echo "<pre>RESPUESTA<br/>";
//                    print_r($response);
//                    exit();
                    
                    $tipo['class']=$param['class'];
                    $tipo['function']='respuesta_recibida';
                    
                    $parametros = '';
                    $traza = trustedServer::decrypt($response['traza']);
                    $parametros = $traza['parametros'];
                    
//                    echo "<pre>";
//                    print_r($parametros);
//                    print_r($traza);
//                    exit();
//                    
                    // REGISTRAMOS TRAZA DE RECEPCION DE DATOS DE INTEROPERABILIDAD
                    $interoperabilidad_recibida = new Siglas_InteroperabilidadRecibida();
                    $interoperabilidad_recibida->setServidorConfianzaId($servidor_confianza->getId());
                    $interoperabilidad_recibida->setServidorCertificadoId($PK_public_client['crt_id']);
                    $interoperabilidad_recibida->setInteroperabilidadEnviadaId($interoperabilidad_enviada->getId());
                    $interoperabilidad_recibida->setTipo(sfYAML::dump($tipo));
                    $interoperabilidad_recibida->setFirma($response['sing']);
                    $interoperabilidad_recibida->setCadena(sfYAML::dump($response['data']));
                    $interoperabilidad_recibida->setPaquete($traza['paquete']);
                    $interoperabilidad_recibida->setPartes($traza['partes']);
                    $interoperabilidad_recibida->setParte($traza['parte']);
                    $interoperabilidad_recibida->setParametros(sfYAML::dump($parametros));
                    $interoperabilidad_recibida->save();
                    
                    $data_responce = trustedServer::verifyAndDecrypt($response);
//                    if($confianza_respuesta){
//                        // SI EL SERVIDOR ES DE CONFIANZA COMIENZA PROCESO DE APERTURA DE DATA
//                        $data_responce = unserialize(urldecode($confianza_respuesta));
//                    }
                    
//                    print_r($confianza_respuesta);
//                    exit();
                    
                    $data_content = $data_responce['content'];
                    if($data_content['status']=='ok'){
                        $interoperabilidad_enviada->setStatus('R');
                        $interoperabilidad_enviada->save();
                    } else {
                        // EL SERVIDOR DEL WS RETORNO UN ERROR
                        // SE DEBE ENVIAR CORREO ELECTRONICO AL ADMINISTRADOR DEL SERVIDOR REMOTO
                    }
                    
                    echo "<pre>";
                    print_r($data_content);
                    exit();
                }
            }
        }

        exit();
    }
}

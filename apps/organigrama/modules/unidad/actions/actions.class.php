<?php

require_once dirname(__FILE__).'/../lib/unidadGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/unidadGeneratorHelper.class.php';

/**
 * unidad actions.
 *
 * @package    siglas-(institucion)
 * @subpackage unidad
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class unidadActions extends autoUnidadActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('unidad_funcional_id');
    
    $unidades_orden = Doctrine::getTable('Organigrama_Unidad')->combounidad();
    $unidad_desordenada = Doctrine_Query::create()
        ->select('u.*')
        ->from('Organigrama_Unidad u')
        ->where('u.status = ?', 'A')
        ->andWhere('u.orden_automatico is NULL');

    $i=1;
    if(count($unidad_desordenada)>0){
        $manager = Doctrine_Manager::getInstance();
        $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
        $cacheDriver->deleteByPrefix('cache_unidad_');
        
        $unidades_orden = Doctrine::getTable('Organigrama_Unidad')->combounidad();
        
        foreach ($unidades_orden as $id => $nombre) {
            if($id != ''){
                Doctrine_Query::create()
                ->update('Organigrama_Unidad')
                ->set('orden_automatico','?', $i)
                ->where('id = ?', $id)
                ->execute();
                $i++;
            }
        }
    }
    
    if($i==1){
        $unidades_orden = Doctrine::getTable('Organigrama_Unidad')->combounidad();
    }
      
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
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

    $this->unidades_orden = $unidades_orden;
    $this->pager = $this->getPager();
    $this->sort = $this->getSort();
  }
  
  public function executeAnular(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $cargo = Doctrine::getTable('Organigrama_Unidad')->find($id);
    $cargo->setStatus('I');
    $cargo->save();

    $this->getUser()->setFlash('notice', 'La unidad ha sido anulada con exito.');
    $this->redirect('@organigrama_unidad');
  }
  
  public function executeUnidadTipo(sfWebRequest $request)
  {
    $this->redirect(sfConfig::get('sf_app_organigrama_url').'unidad_tipo');
  }
  
  public function executeCargos(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $unidad = Doctrine::getTable('Organigrama_Unidad')->find($id);
    $this->getUser()->setAttribute('header_ruta', 'Unidad: '.$unidad->getNombre());
    
    $this->getUser()->setAttribute('unidad_funcional_id', $id);

    $this->redirect('organigrama_cargo');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('organigrama_unidad');
    
    //VERIFICAR SI NO TIENE UN ORDEN PREFERENCIAL
    if($datos['id']!=''){
        $unidad_editada = Doctrine::getTable('Organigrama_Unidad')->find($datos['id']);
        if($unidad_editada->getOrdenPreferencial()!=NULL){
            $datos['orden_preferencial'] = $unidad_editada->getOrdenPreferencial();
        } else {
            $datos['orden_preferencial'] = NULL;
        }
        
        $siglas_old = $unidad_editada->getSiglas();
        $codigo_unidad_old = $unidad_editada->getCodigoUnidad();
    } else {
        $siglas_old =$datos['siglas'];
        $codigo_unidad_old =$datos['codigo_unidad'];
    }
    
    $datos['orden_automatico'] = NULL;
    $request->setParameter('organigrama_unidad',$datos);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      $conn = Doctrine_Manager::connection();
      $conn->beginTransaction();
      try {      
        $organigrama_unidad = $form->save();
        
        // VERIFICAMOS SI LAS SIGLAS CAMBIARON, PARA ADACTAR LOS CORRELATIVOS DE CORRESPONDENCIA A LOS NUEVOS FORMATOS
        if($siglas_old != $organigrama_unidad->getSiglas() || $codigo_unidad_old != $organigrama_unidad->getCodigoUnidad()){
            $unidad_correlativos = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findByUnidadId($organigrama_unidad->getId());
            
            foreach ($unidad_correlativos as $unidad_correlativo) {
                // ELIMINAMOS TODOS LOS CORRELATIVOS SIMULADOS DEL CORRELATIVO GUARDADO
                $delete_simulados = Doctrine::getTable('Correspondencia_Correspondencia')
                  ->createQuery()
                  ->delete()
                  ->where('unidad_correlativo_id = ?', $unidad_correlativo->getId())
                  ->andWhere('status = ?', 'S')
                  ->execute();
                
                // PREARMAMOS VALORES SIGLAS VIEJAS DEL NOMENCLADOR CON DATOS REALES SIN TOMAR EN CUENTA LA SECUENCIA
                $nomenclatura_old = $unidad_correlativo->getNomenclador();
                $nomenclatura_old = str_replace("Siglas", $siglas_old, $nomenclatura_old);
                $nomenclatura_old = str_replace("Codigo", $codigo_unidad_old, $nomenclatura_old);
                $nomenclatura_old = str_replace("Letra", $unidad_correlativo->getLetra(), $nomenclatura_old);
                $nomenclatura_old = str_replace("Año", date('Y'), $nomenclatura_old);
                $nomenclatura_old = str_replace("Mes", date('m'), $nomenclatura_old);
                $nomenclatura_old = str_replace("Día", date('d'), $nomenclatura_old);
                
                // PREARMAMOS VALORES SIGLAS NUEVAS DEL NOMENCLADOR CON DATOS REALES SIN TOMAR EN CUENTA LA SECUENCIA
                $nomenclatura_new = $unidad_correlativo->getNomenclador();
                $nomenclatura_new = str_replace("Siglas", $organigrama_unidad->getSiglas(), $nomenclatura_new);
                $nomenclatura_new = str_replace("Codigo", $organigrama_unidad->getCodigoUnidad(), $nomenclatura_new);
                $nomenclatura_new = str_replace("Letra", $unidad_correlativo->getLetra(), $nomenclatura_new);
                $nomenclatura_new = str_replace("Año", date('Y'), $nomenclatura_new);
                $nomenclatura_new = str_replace("Mes", date('m'), $nomenclatura_new);
                $nomenclatura_new = str_replace("Día", date('d'), $nomenclatura_new);
                
                
                // RECORREMOS DESDE 1 HASTA EL VALOR INICIAL GUARDADO PARA COMPROBAR CORRELATIVOS ACTIVOS ANTERIORES
                for($i=1;$i<$unidad_correlativo->getSecuencia();$i++){

                    // NOMENCLADOR PREARMADO LE SUSTITUIMOS LA SECUENCIA $i
                    $nomenclatura_ok_old = str_replace("Secuencia", $i, $nomenclatura_old);
                    $nomenclatura_ok_new = str_replace("Secuencia", $i, $nomenclatura_new);

                    // BUSCAMOS EL NOMENCLADOR PREARMADO DE LA SECUENCIA $I
                    $verificacion_simulacion = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($nomenclatura_ok_old);

                    $correlativo_simulado = new Correspondencia_Correspondencia();
                    $correlativo_simulado->setNCorrespondenciaEmisor($nomenclatura_ok_new);
                    $correlativo_simulado->setUnidadCorrelativoId($unidad_correlativo->getId());
                    $correlativo_simulado->setPrivado('S');
                    
                    // SI NO EXISTE UN CORRELATIVO ACTIVO DE LA SECUENCIA PROCEDEMOS A CREAR UN CORRELATIVO SIMULADO
                    if($verificacion_simulacion) {
                        $verificacion_simulacion_new = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($nomenclatura_ok_new);
                        if(!$verificacion_simulacion_new) {
                            if($verificacion_simulacion->getStatus()=='M'){
                                $verificacion_simulacion->setNCorrespondenciaEmisor($nomenclatura_ok_new);
                                $verificacion_simulacion->save();
                            } else {
                                $correlativo_simulado->setStatus('M'); // STATUS M: correspondencia simulana de modificacion de correlativo existente
                                $correlativo_simulado->save();
                            }
                        } else {
                            $verificacion_simulacion->delete();
                        }
                    } else {
                        $correlativo_simulado->setStatus('S');
                        $correlativo_simulado->save();
                    }
                }
            }
        }
        $conn->commit();

      } catch (Doctrine_Validator_Exception $e) {
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $organigrama_unidad)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@organigrama_unidad_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'organigrama_unidad_edit', 'sf_subject' => $organigrama_unidad));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
  
  public function executeEnviarEstructura(sfWebRequest $request)
  {
        $servidor_confianza_id = $request->getParameter('servidor_confianza_id');

        // SETEA LA CLASSE DEL CLIENTE QUE LLAMA
        $param['class'] = 'estructura';
        // SETEA LA FUNCION DE LA CLASE SETEADA
        $param['function'] = 'recibirEstructura';

        //ARMADO DE DATA A ENVIAR
        $estructura = new wsOutputEstructsura();
        $array_estructura = $estructura->generarArray();

        // BUSCAR DATOS DE SERVIDOR DE CONFIANZA
        $servidor_confianza = Doctrine::getTable('Siglas_ServidorConfianza')->find($servidor_confianza_id);

//      echo "<pre>Estructura<br/>";
//      print_r($array_estructura);

        // WS_ARRAY ES EL ARRAY DEFINITIVO QUE VOY A ENVIAR
        $ws_array['param'] = $param;
        $ws_array['content'] = $array_estructura;

//      echo "<pre>ARRAY COMPLETObr/>";
//      print_r($ws_array);

        $PK_public_client = trustedServer::openPublicKey($servidor_confianza->getDominio());

        if (!isset($PK_public_client['error'])) {
            $data_sing_and_crypt = trustedServer::encryptAndSing($servidor_confianza->getDominio(), $ws_array);

            // TIPO ES UNA VARIABLE INFORMACITIVA YML PARA SER GUARDADA PARA AUDITORIA, ES DECIR SE GUARDARA QUE CON ESTE ENVIO SE SOLICITO ESTA CLASSE Y ESTA FUNCION
            $tipo['class'] = $param['class'];
            $tipo['function'] = $param['function'];

            // PARAMETROS ES VARIABLE DE AUDITORIA PARA VISUALIZAR QUE DATA O CONTENIDO FINAL SE ENVIO
            $parametros = $array_estructura;

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

            $traza = '';
            //$traza['parametros'] = $parametros;//ANALIZAR QUE PARAMETRO ENVIA LA ESTRUCTURA
            $traza['parametros']['interoperabilidad_envio_solicitud_id'] = $interoperabilidad_enviada->getId();
            $traza['paquete'] = $interoperabilidad_enviada->getId();
            $traza['partes'] = 1;
            $traza['parte'] = 1;

            // ENCRIPTAMOS TRAZA
            $traza_crypt = trustedServer::encrypt($servidor_confianza->getDominio(), $traza);

            // INCORPORAMOS INFORMACION DE LA TRAZA AL ENVIO
            $data_sing_and_crypt['traza'] = $traza_crypt;

            require_once(sfConfig::get("sf_root_dir") . '/lib/ws/nusoap/nusoap.php');

//            echo "<pre>ENVIO<br/>";
//            print_r($data_sing_and_crypt);
//            exit();
            // RUTA DEL SERVIDOR Y SERVICIO
            $wsdl = $servidor_confianza->getDominio() . "/ws.php?wsdl";

            // INSTANCIAMOS EL WS
            $client = new nusoap_client($wsdl, 'wsdl');

            $param = array('data' => $data_sing_and_crypt);


            // LLAMADO DEl SERVICIO PARA QUE LLENE LA VARIABLE $response
            $response = $client->call('recibir', $param);

            $tipo['function'] = 'respuesta_recibida';

            $parametros = '';
            $traza = trustedServer::decrypt($response['traza']);
            $parametros = $traza['parametros'];
//                    $sfprueba = sfYAML::dump($traza);
//                    file_put_contents(sfConfig::get("sf_root_dir").'/config/siglas/ws.yml', $sfprueba);
//                    exit();
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

            $io_basica=array();
            $io_basica = sfYaml::load($servidor_confianza->getIoBasica());
            
            if ($data_responce['notify']['status'] == 'ok') {
                $interoperabilidad_enviada->setStatus('R');
                $interoperabilidad_enviada->save();

                $io_basica['estructura']['status_envio'] = true;
                $io_basica['estructura']['fecha_envio'] = date('Y-m-d h:i:s');
            } else {
                $io_basica['estructura']['status_envio'] = false;
                $io_basica['estructura']['fecha_envio'] = date('Y-m-d h:i:s');
                // EL SERVIDOR DEL WS RETORNO UN ERROR
                // SE DEBE ENVIAR CORREO ELECTRONICO AL ADMINISTRADOR DEL SERVIDOR REMOTO
            }
            $io_basica = sfYaml::dump($io_basica);
            $servidor_confianza->setIoBasica($io_basica);
            $servidor_confianza->save();

            echo $data_responce['notify']['message'];
        } else {
            echo "Error no se encontro la llave pública del servidor de confianza para enviar la estructura.";
        }
        exit();
    }
    
  public function executeCopiarDireccion(sfWebRequest $request){
      $padre_id = $request->getParameter('padre_id');

      $unidad_padre = Doctrine_Core::getTable('Organigrama_Unidad')->find($padre_id);
      
      $direccion['geografico']['estado_id'] = $unidad_padre->getEstadoId();
      $direccion['geografico']['municipio_id'] = $unidad_padre->getMunicipioId();
      $direccion['geografico']['parroquia_id'] = $unidad_padre->getParroquiaId();
      $direccion['detalle']['organigrama_unidad_dir_av_calle_esq'] = $unidad_padre->getDirAvCalleEsq();
      $direccion['detalle']['organigrama_unidad_dir_edf_torre_anexo'] = $unidad_padre->getDirEdfTorreAnexo();
      $direccion['detalle']['organigrama_unidad_dir_piso'] = $unidad_padre->getDirPiso();
      $direccion['detalle']['organigrama_unidad_dir_oficina'] = $unidad_padre->getDirOficina();
      $direccion['detalle']['organigrama_unidad_dir_urbanizacion'] = $unidad_padre->getDirUrbanizacion();
      $direccion['detalle']['organigrama_unidad_dir_ciudad'] = $unidad_padre->getDirCiudad();
      $direccion['detalle']['organigrama_unidad_dir_punto_referencia'] = $unidad_padre->getDirPuntoReferencia();
      
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      sleep(1);
      return $this->renderText(json_encode($direccion));
  }

}

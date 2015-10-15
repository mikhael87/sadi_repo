<?php

require_once dirname(__FILE__).'/../lib/documentoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/documentoGeneratorHelper.class.php';

/**
 * documento actions.
 *
 * @package    siglas
 * @subpackage documento
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class documentoActions extends autoDocumentoActions
{ 
  public function executeRegresarExpediente(sfWebRequest $request)
  {
      $this->getUser()->getAttributeHolder()->remove('expediente_id');
      $this->redirect(sfConfig::get('sf_app_archivo_url').'expediente');
  }
  
  public function executeListarValores(sfWebRequest $request)
  {     
      $this->etiquetas = Doctrine::getTable('Archivo_Etiqueta')->detallesEtiquetaciones($request->getParameter('t_id'));
  }
  
  public function executeArchivarCorrespondencia(sfWebRequest $request)
  {     
        $archivo_documento_datos = $request->getParameter('archivo_documento');

        $conn = Doctrine_Manager::connection();

        try
        {
            $conn->beginTransaction();
            
            $expediente = Doctrine::getTable('Archivo_Expediente')->find($this->getUser()->getAttribute('expediente_id'));
            $estante = Doctrine::getTable('Archivo_Estante')->find($expediente->getEstanteId());
            $unidad = Doctrine::getTable('Organigrama_Unidad')->find($estante->getUnidadFisicaId());

            // CREAR CORRELATIVO
            $unidad_correlativos = Doctrine::getTable('Archivo_UnidadCorrelativos')->find($expediente->getUnidadCorrelativosId());

            $secuencia_tmp = $unidad_correlativos->getSecuenciaAnexoDocumento();

            $ban=0;
            while($ban==0){
                $correlativo_actual = $unidad->getSiglas().'-AD-'.date('Y').'-'.$secuencia_tmp;

                $correlativo_unique = Doctrine::getTable('Archivo_Documento')->findByCorrelativo($correlativo_actual);

                if(count($correlativo_unique)>0){
                    $secuencia_tmp++;
                } else {
                    $secuencia_tmp++;
                    $unidad_correlativos->setSecuenciaAnexoDocumento($secuencia_tmp);
                    $unidad_correlativos->save(); 

                    $ban=1;
                }
            }

            $archivo_documento = new Archivo_Documento();
            $archivo_documento->setCorrespondenciaId($this->getUser()->getAttribute('correspondencia_archivar_id'));
            $archivo_documento->setUnidadId($unidad_correlativos->getUnidadId());
            $archivo_documento->setUnidadCorrelativosId($unidad_correlativos->getId());
            $archivo_documento->setCorrelativo($correlativo_actual);
            $archivo_documento->setTipologiaDocumentalId($archivo_documento_datos['tipologia_documental_id']);
            $archivo_documento->setCopiaDigital(FALSE);
            $archivo_documento->setCopiaFisica(FALSE);
            $archivo_documento->setNombreOriginal(NULL);
            $archivo_documento->setRuta(NULL);
            $archivo_documento->setTipoArchivo(NULL);
            $archivo_documento->setContenidoAutomatico(NULL);
            $archivo_documento->save();

            $valores = $request->getParameter('valores');

            foreach ($valores as $key => $valor) {
                if(preg_match('/#f/', $key)){
                    list($key,$tmp) = explode('#',$key);
                    $valor = $valor['day'].'-'.$valor['month'].'-'.$valor['year'];
                }

                if(trim($valor)!=''){
                    $documento_etiqueta = new Archivo_DocumentoEtiqueta();
                    $documento_etiqueta->setEtiquetaId($key);
                    $documento_etiqueta->setDocumentoId($archivo_documento->getId());
                    $documento_etiqueta->setValor($valor);
                    $documento_etiqueta->save();
                }
            }

            $this->getUser()->getAttributeHolder()->remove('expediente_id');
            $this->getUser()->getAttributeHolder()->remove('correspondencia_archivar_id');
            
            $conn->commit();
            echo '<div class="notice">Correspondencia archivada exitosamente con el correlativo '.$correlativo_actual.'.</div>';
        }
        catch (Doctrine_Validator_Exception $e)
        {
            $conn->rollBack();
            echo '<div class="error">Ocurrio un error al archivar la correspondencia, por favor intente de nuevo.</div>';
        }
        
        exit();
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {     
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $archivo_documento = $form->save();
                
        if(!$form->isNew()){
            Doctrine::getTable('Archivo_DocumentoEtiqueta')
            ->createQuery()
            ->delete()
            ->where('documento_id = ?', $archivo_documento->getId())
            ->execute();
        }
        
        
        $valores = $request->getParameter('valores');

        foreach ($valores as $key => $valor) {
            if(preg_match('/#f/', $key)){
                list($key,$tmp) = explode('#',$key);
                $valor = $valor['day'].'-'.$valor['month'].'-'.$valor['year'];
            }
            
            if(trim($valor)!=''){
                $documento_etiqueta = new Archivo_DocumentoEtiqueta();
                $documento_etiqueta->setEtiquetaId($key);
                $documento_etiqueta->setDocumentoId($archivo_documento->getId());
                $documento_etiqueta->setValor($valor);
                $documento_etiqueta->save();
            }
        }
        
        if(!$form->isNew()){
            $documento_id = $request->getParameter('file_change');

            if($documento_id!='.'){ 
                $archivo_documento = Doctrine::getTable('Archivo_Documento')->find($documento_id);
                $ruta_old = $archivo_documento->getRuta();

                $ruta = explode('/',$archivo_documento->getRuta());

                $ruta[count($ruta)-1] = 'MODIFICADO_'.date('d-m-Y').'__'.$ruta[count($ruta)-1];
                $ruta_new = null;
                for($i=0;$i<count($ruta);$i++)
                    $ruta_new .= $ruta[$i].'/';
                $ruta_new .= '$%&';
                $ruta_new = str_replace('/$%&', '', $ruta_new);

                rename('uploads/archivo/'.$ruta_old, 'uploads/archivo/'.$ruta_new);
            }
        }
        
        $expediente = Doctrine::getTable('Archivo_Expediente')->find($archivo_documento->getExpedienteId());
        $estante = Doctrine::getTable('Archivo_Estante')->find($expediente->getEstanteId());
        $caja = null; if($expediente->getCajaId()!=null) $caja = Doctrine::getTable('Archivo_Caja')->find($expediente->getCajaId());
        $unidad = Doctrine::getTable('Organigrama_Unidad')->find($estante->getUnidadFisicaId());
        
        $ruta_adsoluta = sfConfig::get('sf_upload_dir').'/archivo/';
        if(!is_dir($ruta_adsoluta)){ mkdir($ruta_adsoluta, 0777); chmod($ruta_adsoluta, 0777); }
        $ruta_adsoluta .= 'ARCHIVO__'.str_replace('/','-',$unidad->getSiglas()).'/';
        if(!is_dir($ruta_adsoluta)){ mkdir($ruta_adsoluta, 0777); chmod($ruta_adsoluta, 0777); }
        $ruta_adsoluta .= 'ESTANTE__'.$estante->getIdentificador().'/';
        if(!is_dir($ruta_adsoluta)){ mkdir($ruta_adsoluta, 0777); chmod($ruta_adsoluta, 0777); }
        $ruta_adsoluta .= 'TRAMO__'.$expediente->getTramo().'/';
        if(!is_dir($ruta_adsoluta)){ mkdir($ruta_adsoluta, 0777); chmod($ruta_adsoluta, 0777); }
        if($caja!=null) {
            $ruta_adsoluta .= 'CAJA__'.$caja->getCorrelativo().'/';
            if(!is_dir($ruta_adsoluta)){ mkdir($ruta_adsoluta, 0777); chmod($ruta_adsoluta, 0777); }
        }
        $ruta_adsoluta .= 'EXPEDIENTE__'.$expediente->getCorrelativo().'/';
        if(!is_dir($ruta_adsoluta)){ mkdir($ruta_adsoluta, 0777); chmod($ruta_adsoluta, 0777); }

        $texto_puro = new herramientas();
        
        if($form->isNew()){
            $unidad_correlativos = Doctrine::getTable('Archivo_UnidadCorrelativos')->find($expediente->getUnidadCorrelativosId());
            
            $secuencia_tmp = $unidad_correlativos->getSecuenciaAnexoDocumento();

            $ban=0;
            while($ban==0){
                $correlativo_actual = $unidad->getSiglas().'-AD-'.date('Y').'-'.$secuencia_tmp;
                
                $correlativo_unique = Doctrine::getTable('Archivo_Documento')->findByCorrelativo($correlativo_actual);

                if(count($correlativo_unique)>0){
                    $secuencia_tmp++;
                } else {
                    $secuencia_tmp++;
                    $unidad_correlativos->setSecuenciaAnexoDocumento($secuencia_tmp);
                    $unidad_correlativos->save(); 
                    
                    $ban=1;
                }
            }
        } else {
            $correlativo_actual = $archivo_documento->getCorrelativo();
        }
                
        foreach ($request->getFiles() as $file) {
            $theFileName = $correlativo_actual.'__'.str_replace(' ','_',$texto_puro->limpiar_metas($file['name']));
            $theFileName = preg_replace('([\(\)])','-',$theFileName);
            
            if (file_exists($ruta_adsoluta.$theFileName)) { 
                $theFileName = date('d-m-Y_h-i-s').'_'.$theFileName; 
                $file['name'] = $file['name'].' ('.date('d-m-Y h:i:s').')';
            }
            
            if (move_uploaded_file($file['tmp_name'], $ruta_adsoluta.$theFileName)){
                // El archivo ha sido cargado correctamente
                
                if(preg_match('/pdf/', $file['type'])){
                    system('pdftotext -enc UTF-8 '.$ruta_adsoluta.$theFileName);
                    $contenido_automatico = file_get_contents($ruta_adsoluta.str_replace('.pdf','.txt',$theFileName));
                    unlink($ruta_adsoluta.str_replace('.pdf','.txt',$theFileName));
                } else {
                    $contenido_automatico = null;
                }

                $ruta_web = str_replace(sfConfig::get('sf_upload_dir').'/archivo/','',$ruta_adsoluta);

                $archivo_documento->setCorrelativo($correlativo_actual);
                $archivo_documento->setNombreOriginal($file['name']);
                $archivo_documento->setRuta($ruta_web.$theFileName);
                $archivo_documento->setTipoArchivo($file['type']);
                $archivo_documento->setContenidoAutomatico($contenido_automatico);               
            }else{
                echo "Ocurrió algún error al subir el archivo. No pudo guardarse.";
            } 
        }
        
        if($archivo_documento->getCopiaDigital() == 0){
            $archivo_documento->setCorrelativo($correlativo_actual);
            $archivo_documento->setNombreOriginal(NULL);
            $archivo_documento->setRuta(NULL);
            $archivo_documento->setTipoArchivo(NULL);
            $archivo_documento->setContenidoAutomatico(NULL);
        }
        
        if($form->isNew()){
            $archivo_documento->setUnidadId($unidad_correlativos->getUnidadId());
            $archivo_documento->setUnidadCorrelativosId($unidad_correlativos->getId());
        }
        
        $archivo_documento->save();
        
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_documento)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_documento_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'archivo_documento_edit', 'sf_subject' => $archivo_documento));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}

<?php

require_once dirname(__FILE__).'/../lib/tipo_formatoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/tipo_formatoGeneratorHelper.class.php';

/**
 * tipo_formato actions.
 *
 * @package    siglas
 * @subpackage tipo_formato
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tipo_formatoActions extends autoTipo_formatoActions
{
  public function executeIndex(sfWebRequest $request)
  {
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
    
    if($this->getUser()->hasAttribute('parametros_formato'))
        $this->getUser()->getAttributeHolder()->remove('parametros_formato');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->correspondencia_tipo_formato = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this->correspondencia_tipo_formato);
    $formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($request->getParameter('id'));
    $parametros = sfYaml::load($formato->getParametros());
    if(!empty($parametros)) {
        $parametros['formato_id']= $request->getParameter('id');
        $this->getUser()->setAttribute('parametros_formato', $parametros);
    }
  }
  
  public function executePreSaveRuta(sfWebRequest $request)
  {
    $this->nombre= $request->getParameter('nombre');
    $this->descripcion= $request->getParameter('descripcion');
    $cadena_vb= $request->getParameter('cadena_vb').'end';
    
    $cadena_vb= str_replace('$$end', '', $cadena_vb);
    
    $parts_cadena= explode('$$', $cadena_vb);
    
    //SI NO SE ESTA EDITANDO LAS RUTAS SON GUARDADAS DESDE AQUI, EN CASO CONTRARIO DESDE EL PROCCES FORM
    if($request->getParameter('formato_id') != 'new') {
        $vistobueno_general_config= new Correspondencia_VistobuenoGeneralConfig();
        $vistobueno_general_config->setNombre($request->getParameter('nombre'));
        $vistobueno_general_config->setDescripcion($request->getParameter('descripcion'));
        $vistobueno_general_config->setTipoFormatoId($request->getParameter('formato_id'));
        $vistobueno_general_config->setStatus('A');
        $vistobueno_general_config->save();
        
        $this->vb_general_config= $vistobueno_general_config->getId();
        
        $orden= count($parts_cadena);
        foreach($parts_cadena as $values) {
            $value= explode('#', $values);
            
            $vistobueno_general= new Correspondencia_VistobuenoGeneral();
            $vistobueno_general->setVistobuenoGeneralConfigId($vistobueno_general_config->getId());
            $vistobueno_general->setFuncionarioId($value[0]);
            $vistobueno_general->setFuncionarioCargoId($value[1]);
            $vistobueno_general->setStatus($value[2]);
            $vistobueno_general->setOrden($orden);
            $vistobueno_general->save();
            $orden--;
        }
    }
    $this->parts_cadena= $parts_cadena;
  }
  
  public function executeVistobuenoWindow(sfWebRequest $request)
  {
    if($request->getParameter('visto_bueno_general_config_id') !== 'empty') {
        $datos_vb_config= Doctrine::getTable('Correspondencia_VistobuenoGeneralConfig')->find($request->getParameter('visto_bueno_general_config_id'));
        $this->nombre= $datos_vb_config->getNombre();
        $this->descripcion= $datos_vb_config->getDescripcion();
        $this->visto_bueno_general_config_id= $request->getParameter('visto_bueno_general_config_id');
    }
  }
  
  public function executeEliminaRuta(sfWebRequest $request)
  {
    $vb_general_config_id= $request->getParameter('vb_general_config_id');
    
    $update_vb = Doctrine_Query::create()
        ->update('Correspondencia_VistobuenoGeneral vg')
        ->set('vg.status', '?', 'I')
        ->where('vg.vistobueno_general_config_id = ?', $vb_general_config_id)
        ->execute();
    
    $update_vb_config = Doctrine_Query::create()
        ->update('Correspondencia_VistobuenoGeneralConfig vgc')
        ->set('vgc.status', '?', 'I')
        ->where('vgc.id = ?', $vb_general_config_id)
        ->execute();
    
    exit;
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $tipo_formato = $request->getParameter('correspondencia_tipo_formato');
    $parametros_contenido = $tipo_formato['parametros_contenido'];
    
    
    
    // DEPURACION DE UNIDADES EMISORAS
        if(!isset($parametros_contenido['emisores']['unidades']['todas']))
            $parametros_contenido['emisores']['unidades']['todas'] = 'false';
        
        if(!isset($parametros_contenido['emisores']['unidades']['especificas']))
            $parametros_contenido['emisores']['unidades']['especificas'] = 'false';
        
        if(!isset($parametros_contenido['emisores']['unidades']['tipos']))
            $parametros_contenido['emisores']['unidades']['tipos'] = 'false';
    // DEPURACION DE UNIDADES EMISORAS
    
    // DEPURACION DE UNIDADES RECEPTORAS
        if(!isset($parametros_contenido['receptores']['unidades']['seteada']))
            $parametros_contenido['receptores']['unidades']['seteada'] = 'false';
        
        if(!isset($parametros_contenido['receptores']['unidades']['especificas']))
            $parametros_contenido['receptores']['unidades']['especificas'] = 'false';
        
        if(!isset($parametros_contenido['receptores']['unidades']['tipos']))
            $parametros_contenido['receptores']['unidades']['tipos'] = 'false';
    // DEPURACION DE UNIDADES RECEPTORAS
        
    
    // DEPURACION DE CAMPOS DEL FORMULARIO
        $i=0;
        foreach ($parametros_contenido['formulario'] as $valores_campos) {
            $valores_campos_tmp[$i] = $valores_campos;
            $i++;
        }

        unset($parametros_contenido['formulario']);
        $parametros_contenido['formulario'] = $valores_campos_tmp;
    // DEPURACION DE CAMPOS DEL FORMULARIO
    
    // DEPURACION DE TIPOS DE DESCARGAS
        
        if(!isset($parametros_contenido['options_object']['descargas'])){
            $parametros_contenido['options_object']['descargas'] = array('pdf'=>'false','odt'=>'false','doc'=>'false');
        } else{
            $decargas['pdf']='false'; 
            $decargas['odt']='false'; 
            $decargas['doc']='false';
            
            foreach ($parametros_contenido['options_object']['descargas'] as $i => $tipo_descarga) {
                if($tipo_descarga == 'pdf') $decargas['pdf']='true';
                else if($tipo_descarga == 'odt') $decargas['odt']='true';
                else if($tipo_descarga == 'doc') $decargas['doc']='true';
            }
            
            unset($parametros_contenido['options_object']['descargas']);
            $parametros_contenido['options_object']['descargas'] = $decargas;
        }
        
        $i=0;
        foreach ($parametros_contenido['formulario'] as $valores_campos) {
            $valores_campos_tmp[$i] = $valores_campos;
            $i++;
        }

        unset($parametros_contenido['formulario']);
        $parametros_contenido['formulario'] = $valores_campos_tmp;
    // DEPURACION DE TIPOS DE DESCARGAS
        
//    $cadena = sfYAML::dump($parametros_contenido);
//    echo "<pre>";
//    echo $cadena."<br><br><br>";
//    print_r($parametros_contenido);    
//    exit();
      
    unset($tipo_formato['parametros_contenido']);
    $tipo_formato['parametros'] = sfYAML::dump($parametros_contenido);
    $request->setParameter('correspondencia_tipo_formato',$tipo_formato);
      
      
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $correspondencia_tipo_formato = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $correspondencia_tipo_formato)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@correspondencia_tipo_formato_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'correspondencia_tipo_formato_edit', 'sf_subject' => $correspondencia_tipo_formato));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
  
  public function executeCheckOdtDown(sfWebRequest $request) {
    $classe = $request->getParameter('ft');
    $consult = $request->getParameter('cl');

    if($request->getParameter('cl')== 'general') {
        $consult= '';
    }else {
        $consult= '_consult';
    }
    // Lee la plantilla
    if(file_exists(sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/".$classe."/plantillas/".$classe.$consult."_odt.rtf")) {
        echo '';
    }else {
        echo 'No existe el archivo.';
    }
    exit();
  }
  
  public function executeOdtDown(sfWebRequest $request) {
    $classe = $request->getParameter('ft');
    $consult = $request->getParameter('cl');
    
    if($request->getParameter('cl')== 'general') {
        $consult= '';
    }else {
        $consult= '_consult';
    }
    // Lee la plantilla
    $plantilla = file_get_contents(sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/".$classe."/plantillas/".$classe.$consult."_odt.rtf");

    $plantilla = addslashes($plantilla);
    $plantilla = str_replace('\r','\\r',$plantilla);
    $plantilla = str_replace('\t','\\t',$plantilla);

eval( '$rtf = <<<EOF_RTF
' . $plantilla . '
EOF_RTF;
');

    //OBTENIENDO EL NOMBRE
    $nombre_def = "plantilla_".$classe."_".date('Y-m-d-s').".rtf";

    file_put_contents($nombre_def,$rtf);
    if (!file_exists ($nombre_def)){
        echo "Ocurrio un error al generar el documento de word correspondiente";
    }else{
        $aleatorio = rand(1, 10000000);
        $hash_is = md5($aleatorio);
        $carpeta = $hash_is;
        mkdir(sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/".$classe."/plantillas/tmp/".$carpeta,0777);
        $nuevoarchivo = sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/".$classe."/plantillas/tmp/".$carpeta."/".$nombre_def;
        if (!copy($nombre_def, $nuevoarchivo)) {
            echo "Ocurrio un error al generar el documento de word Temporal correspondiente";
        }
        $path_a_tu_doc = sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/".$classe."/plantillas/tmp/".$carpeta;

        $enlace = $path_a_tu_doc."/".$nombre_def;
        header ("Content-Disposition: attachment; filename=".$nombre_def."\n\n");
        header( "Content-Charset: utf-8");
        header ("Content-Type: application/octet-stream");
        header ("Content-Length: ".filesize($enlace));

    //header( "Content-Type" content="text/html; charset=ISO-8859-1")

        readfile($enlace);
    }
    //ELIMINA ARCHIVO
    unlink($enlace);
    unlink($nombre_def);
    //ELIMINA DIRECTORIO
    rmdir($path_a_tu_doc);
  }
  
  public function executePlantillaUp(sfWebRequest $request) {
    $classe = $request->getParameter('class');
    $consult = ($request->getParameter('consult') == 'general')? '' : '_consult';
    
    foreach ($request->getFiles() as $file) {
        $userfile_name = $file['name'];
        $userfile_tmp = $file['tmp_name'];
        $userfile_size = $file['size'];
        $userfile_type = $file['type'];
        $filename = basename($file['name']);
    }
    
    $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
    $large_image_location= sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/".$classe."/plantillas/".$classe.$consult."_odt.rtf";
    $error='';
    if ($file_ext == 'rtf') {
        unlink($large_image_location);
        if(!move_uploaded_file($userfile_tmp, $large_image_location)){
            $error= 'Ha ocurrido un error al internar subir el archivo, por favor intenta mas tarde';
        }
        chmod($large_image_location, 0777);
    }else {
        $error='La plantilla solo puede ser formato .rtf (Rich Text Format)';
    }
    
    if($error== '') {
        $request_list['valor']= 'La plantilla ha sido sustituida con exito.';
    }else {
        $request_list['valor']= $error;
    }
    
    return $this->renderText(json_encode($request_list));
  }
}

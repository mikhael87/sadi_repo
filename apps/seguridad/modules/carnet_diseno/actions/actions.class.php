<?php

require_once dirname(__FILE__).'/../lib/carnet_disenoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/carnet_disenoGeneratorHelper.class.php';

/**
 * carnet_diseno actions.
 *
 * @package    siglas
 * @subpackage carnet_diseno
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class carnet_disenoActions extends autoCarnet_disenoActions
{
    public function unidadAdscripcion($unidad_id){
        $unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidad_id);
        if($unidad->getAdscripcion()==false){
            $unidad = $this->unidadAdscripcion($unidad->getPadreId());
        }

        return $unidad;
    }

    public function executeBuscarFuncionarioPrueba(sfWebRequest $request) {
        $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($request->getParameter('cedula'));
        
        if($funcionario){
            $funcionario_datos = Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($funcionario->getId());
            
            if(count($funcionario_datos)>0){
                if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$funcionario_datos[0]->getCi().'.jpg')){
                    $array_datos['foto']='/images/fotos_personal/'.$funcionario_datos[0]->getCi().'.jpg';
                } else {
                    $array_datos['foto']= '/images/other/siglas_photo_small_'.$funcionario_datos[0]->getSexo().substr($funcionario_datos[0]->getCi(), -1).'.png';
                }
                
                $array_datos['cedula'] = $funcionario_datos[0]->getCi();
                $array_datos['primer_nombre'] = $funcionario_datos[0]->getPrimerNombre();
                $array_datos['segundo_nombre'] = $funcionario_datos[0]->getSegundoNombre();
                $array_datos['primer_apellido'] = $funcionario_datos[0]->getPrimerApellido();
                $array_datos['segundo_apellido'] = $funcionario_datos[0]->getSegundoApellido();
                $array_datos['unidad_funcional_completo'] = $funcionario_datos[0]->getUnombre();
                $array_datos['unidad_funcional_reducido'] = $funcionario_datos[0]->getUreducido();
                $array_datos['unidad_funcional_siglas'] = $funcionario_datos[0]->getUsiglas();
                $array_datos['cargo_condicion'] = $funcionario_datos[0]->getCcnombre();
                $array_datos['cargo_tipo'] = $funcionario_datos[0]->getCtnombre();
                
                $unidad_adscripcion = $this->unidadAdscripcion($funcionario_datos[0]->getUnidadId());
                $array_datos['unidad_adscripcion_completo'] = $unidad_adscripcion->getNombre();
                $array_datos['unidad_adscripcion_reducido'] = $unidad_adscripcion->getNombreReducido();
                $array_datos['unidad_adscripcion_siglas'] = $unidad_adscripcion->getSiglas();
            }
        }
        
        $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        sleep(1);
        return $this->renderText(json_encode($array_datos));
    }
    
    public function getConstantes() {
        $constantes['upload_dir'] = "images/carnet/temporal";
//$upload_dir = "upload_pic"; 				// The directory for the images to be saved in
        $constantes['upload_path'] = $constantes['upload_dir'] . "/";    // The path to where the image will be saved
        $constantes['large_image_prefix'] = "resize_";    // The prefix name to large image
        $constantes['thumb_image_prefix'] = "thumbnail_";   // The prefix name to the thumb image

        $constantes['max_file'] = "3";        // Maximum file size in MB
        $constantes['max_width'] = "500";       // Max width allowed for the large image
        $constantes['thumb_width'] = "204";      // Width of thumbnail image
        $constantes['thumb_height'] = "325";      // Height of thumbnail image
// Only one of these image types should be allowed for upload
        $constantes['allowed_image_types'] = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg");
        //$constantes['allowed_image_types'] = array('image/pjpeg' => "jpg", 'image/jpeg' => "jpg", 'image/jpg' => "jpg", 'image/png' => "png", 'image/x-png' => "png", 'image/gif' => "gif");
        $constantes['allowed_image_ext'] = array_unique($constantes['allowed_image_types']); // do not change this
        $image_ext = ""; // initialise variable, do not change this.
        foreach ($constantes['allowed_image_ext'] as $mime_type => $ext) {
            $image_ext.= strtoupper($ext) . " ";
        }

        $constantes['image_ext'] = $image_ext;
        return $constantes;
    }
    
  public function executeNew(sfWebRequest $request)
  {
    $constantes = $this->getConstantes();

    if(!is_dir($constantes['upload_dir'])){
        mkdir($constantes['upload_dir'], 0777, true);
        chmod($constantes['upload_dir'], 0777);
    }

    $this->getUser()->setAttribute('random_key', strtotime(date('Y-m-d H:i:s')));
    $this->getUser()->setAttribute('user_file_ext', '');
        
    $this->form = $this->configuration->getForm();
    $this->seguridad_carnet_diseno = $this->form->getObject();
  }
  
    public function executeEstablecerFondo(sfWebRequest $request) {
        $carnet_id= $request->getParameter('carnet_id');
        $tipo_fondo= $request->getParameter('tipo_fondo');

        $constantes = $this->getConstantes();
        $images = new Images();

        $large_image_name = $constantes['large_image_prefix'] . $this->getUser()->getAttribute('random_key');
        $thumb_image_name = $constantes['thumb_image_prefix'] . $this->getUser()->getAttribute('random_key');
//Image Locations
        $large_image_location = $constantes['upload_path'] . $large_image_name . $this->getUser()->getAttribute('user_file_ext');
        $thumb_image_location = $constantes['upload_path'] . $thumb_image_name . $this->getUser()->getAttribute('user_file_ext');

        $this->getUser()->setAttribute('large_image_location', $large_image_location);
        $this->getUser()->setAttribute('thumb_image_location', $thumb_image_location);

        foreach ($request->getFiles() as $file)

//Get the file information
        $userfile_name = $file['name'];
        $userfile_tmp = $file['tmp_name'];
        $userfile_size = $file['size'];
        $userfile_type = $file['type'];
        $filename = basename($file['name']);

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
                    $error = "Solo son aceptadas las imagenes <strong>" . $constantes['image_ext'] . "</strong><br />";
                }
            }
            //check if the file size is above the allowed limit
            if ($userfile_size > ($constantes['max_file'] * 1048576)) {
                $error.= "La imagen no puede ser mayor a " . $constantes['max_file'] . "MB de peso";
            }
        } else {
            $error = "Seleccione la foto";
        }
//Everything is ok, so we can upload the image.
        if (strlen($error) == 0) {

            if (isset($file['name'])) {
                //put the file ext in the session so we know what file to look for once its uploaded
                $this->getUser()->setAttribute('user_file_ext', '.' . $file_ext);

                move_uploaded_file($userfile_tmp, $large_image_location);
                chmod($large_image_location, 0777);

                $width = $images->getWidth($large_image_location);
                $height = $images->getHeight($large_image_location);
                //Scale the image if it is greater than the width set above

                if ($width > $constantes['max_width']) {
                    $scale = $constantes['max_width'] / $width;
                    $uploaded = $images->resizeImage($large_image_location, $width, $height, $scale);
                } else {
                    $scale = 1;
                    $uploaded = $images->resizeImage($large_image_location, $width, $height, $scale);
                }
                //Delete the thumbnail file so the user can create a new one
                if (file_exists($thumb_image_location)) {
                    unlink($thumb_image_location);
                }
            }
        }else {
            echo $error; exit;
        }

        $this->large_image_location = $large_image_location;
        $this->thumb_width = $constantes['thumb_width'];
        $this->thumb_height = $constantes['thumb_height'];
        $this->current_large_image_width = $width;
        $this->current_large_image_height = $height;
        $this->carnet_id= $carnet_id;
        $this->tipo_fondo= $tipo_fondo;
    }

    public function executeDisenar(sfWebRequest $request) {
        $constantes = $this->getConstantes();
        $images = new Images();
        //Get the new coordinates to crop the image.
        $x1 = $request->getParameter('x1');
        $y1 = $request->getParameter('y1');
        $x2 = $request->getParameter('x2');
        $y2 = $request->getParameter('y2');
        $w = $request->getParameter('w');
        $h = $request->getParameter('h');
        $add = $request->getParameter('id');
        $tipo_fondo = $request->getParameter('tipo_fondo');

        //Scale the image to the thumb_width set above
        $scale = $constantes['thumb_width'] / $w;
        //NO ESCALA LA IMAGEN PARA NO REDUCIR SU CALIDAD
        $scale= 1;
        $cropped = $images->resizeThumbnailImage($this->getUser()->getAttribute('thumb_image_location'), $this->getUser()->getAttribute('large_image_location'), $w, $h, $x1, $y1, $scale);
        //Reload the page again to view the thumbnail

//        if (file_exists('images/carnet/'.$this->getUser()->getAttribute('foto_cambio').'.jpg')) {
//            unlink('images/carnet/'.$this->getUser()->getAttribute('foto_cambio').'.jpg');
//        }
        
        $nombre_final_fondo = date('Y-m-d_H-i-s').'.jpg';
        copy($this->getUser()->getAttribute('thumb_image_location'), 'images/carnet/'.$nombre_final_fondo);
        unlink($this->getUser()->getAttribute('large_image_location'));
        unlink($this->getUser()->getAttribute('thumb_image_location'));

        //GUARDA FONDOS DE CARNET EN CAMPO TEXTO YAML DE DB
        if($add) {
            $carnet= Doctrine::getTable('Seguridad_CarnetDiseno')->find($add);
            
            $imagen_fondo= sfYAML::load($carnet->getImagenFondo());
            if($tipo_fondo== 'front') {
                $imagen_fondo['frontal'][]= $nombre_final_fondo;
            }else {
                if($imagen_fondo['trasero'][0] == '') {
                    $imagen_fondo['trasero'][0]= $nombre_final_fondo;
                }else {
                    $imagen_fondo['trasero'][]= $nombre_final_fondo;
                }
            }
            $carnet->setImagenFondo(sfYAML::dump($imagen_fondo));
            $carnet->save();
        }
        
        $this->getUser()->getAttributeHolder()->remove('random_key');
        $this->getUser()->getAttributeHolder()->remove('user_file_ext');
        $this->getUser()->getAttributeHolder()->remove('large_image_location');
        $this->getUser()->getAttributeHolder()->remove('thumb_image_location');
//        $this->getUser()->getAttributeHolder()->remove('foto_cambio');
        
        $this->nombre_final_fondo = $nombre_final_fondo;
    }
    
    public function executeCargarParametros(sfWebRequest $request) {
        $carnet_tipo = $request->getParameter('carnet_tipo');

        switch ($carnet_tipo){
              case '1000': 
                  $this->setTemplate('parametrosFuncionarios');
                  break;
              case '1001': 
                  $this->setTemplate('parametrosVisitantes');
                  break;
              default:
                  echo '<div style="position: absolute; width: 400px; left: 215px;">No existen parametros para este tipo de carnet</div>';
                  exit();
        }
    }
    
    public function executeAddBackground(sfWebRequest $request) {
        $id = $request->getParameter('id');
        $this->id= $id;
    }
    
    public function executeInactivar(sfWebRequest $request) {
        $id = $request->getParameter('id');
              
        $carnet= Doctrine::getTable('Seguridad_CarnetDiseno')->find($id);
        
        $carnet->setStatus('I');
        $carnet->save();
        $this->redirect('@seguridad_carnet_diseno');
    }
    
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $indices_array = $request->getParameter('indices');
    $indices = sfYAML::dump($indices_array);
    
    $parametros = $request->getParameter('parametros');
    $parametros = sfYAML::dump($parametros);
    
    $datos = $request->getParameter('seguridad_carnet_diseno');
    $datos['indices'] = $indices;
    $datos['parametros'] = $parametros;
    $request->setParameter('seguridad_carnet_diseno',$datos);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'El diseño se ha guardado correctamente.' : 'The item was updated successfully.';

      try {
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();
        
        foreach ($indices_array as $key => $value) {
            $condicion_id = str_replace('cargo_condicion_', '', $key);
            echo $condicion_id.'-';
            $condicion_disenos_activos = Doctrine::getTable('Seguridad_CarnetDiseno')->disenoActivoCargoCondicion($condicion_id);
            echo 'dise acti: '.count($condicion_disenos_activos);
            foreach ($condicion_disenos_activos as $condicion_diseno_activo) {
                $indices_activos = sfYAML::load($condicion_diseno_activo->getIndices()); 
                $indices_activos['cargo_condicion_'.$condicion_id] = 'I';    
                $indices_activos = sfYAML::dump($indices_activos);
                
                $condicion_diseno_activo->setIndices($indices_activos);
                $condicion_diseno_activo->save();
            }
        }
        
        $seguridad_carnet_diseno = $form->save();
        
        $fondos['frontal'][]= $seguridad_carnet_diseno->getImagenFondo();
        $fondos['trasero'][]= '';
        
        $seguridad_carnet_diseno->setImagenFondo(sfYAML::dump($fondos));
        $seguridad_carnet_diseno->save();
        
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $seguridad_carnet_diseno)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@seguridad_carnet_diseno_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'seguridad_carnet_diseno', 'sf_subject' => $seguridad_carnet_diseno));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}

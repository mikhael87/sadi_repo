<?php

/**
 * foto actions.
 *
 * @package    siglas
 * @subpackage foto
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class fotoActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */

    public function getConstantes($from) {
        switch ($from) {
            case 'digifirma':
                $upload_dir= "images/firma_digital/temporal";
                $upload_dir_main= "images/firma_digital/";
                $max_file= '3';
                $max_width= '500';
                $thumb_width= '325';
                $thumb_height= '150';
                break;
            case 'foto':
                $upload_dir= "images/fotos_personal/temporal";
                $upload_dir_main= "images/fotos_personal/";
                $max_file= '3';
                $max_width= '500';
                $thumb_width= '188';
                $thumb_height= '223';
                break;
            default:
                break;
        }
        $constantes['upload_dir'] = $upload_dir;
//$upload_dir = "upload_pic"; 				// The directory for the images to be saved in
        $constantes['upload_path'] = $constantes['upload_dir'] . "/";    // The path to where the image will be saved
        $constantes['large_image_prefix'] = "resize_";    // The prefix name to large image
        $constantes['thumb_image_prefix'] = "thumbnail_";   // The prefix name to the thumb image

        $constantes['max_file'] = $max_file;        // Maximum file size in MB
        $constantes['max_width'] = $max_width;       // Max width allowed for the large image
        $constantes['thumb_width'] = $thumb_width;      // Width of thumbnail image
        $constantes['thumb_height'] = $thumb_height;      // Height of thumbnail image
        $constantes['upload_dir_main'] = $upload_dir_main;
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
    
    public function executeIndex(sfWebRequest $request) {
        $constantes = $this->getConstantes($request->getParameter('from'));
        
        if(!is_dir($constantes['upload_dir'])){
            mkdir($constantes['upload_dir'], 0777, true);
            chmod($constantes['upload_dir'], 0777);
        }
        
        $this->from= $request->getParameter('from');
        
        $this->getUser()->setAttribute('random_key', strtotime(date('Y-m-d H:i:s')));
        $this->getUser()->setAttribute('user_file_ext', '');
    }
    
    public function executeMetodoCarga(sfWebRequest $request) {
        $metodo = $request->getParameter('metodo');
        
        if($metodo=='cargar'){
            $this->setTemplate('prepararFoto');
        } else {
            $this->setTemplate('tomarFoto');
        }
    }

    public function executeEstablecerFoto(sfWebRequest $request) {
        if($this->context->getUser()->hasAttribute('foto_cambio')) {
            $constantes = $this->getConstantes('foto');
        }elseif($this->context->getUser()->hasAttribute('digifirma_cambio')) {
            $constantes = $this->getConstantes('digifirma');
        }
        
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
                    $scale = 1;
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
            echo $error;
            exit();
        }
        
        $this->large_image_location = $large_image_location;
        $this->thumb_width = $constantes['thumb_width'];
        $this->thumb_height = $constantes['thumb_height'];
        $this->current_large_image_width = $width;
        $this->current_large_image_height = $height;
    }
    
    public function executeGuardarFotoCargada(sfWebRequest $request) {
        $images = new Images();
        //Get the new coordinates to crop the image.
        $x1 = $request->getParameter('x1');
        $y1 = $request->getParameter('y1');
        $x2 = $request->getParameter('x2');
        $y2 = $request->getParameter('y2');
        $w = $request->getParameter('w');
        $h = $request->getParameter('h');

        
        if($this->context->getUser()->hasAttribute('foto_cambio')) {
            $constantes = $this->getConstantes('foto');
            $name= $this->getUser()->getAttribute('foto_cambio');
            
            //cambiamos la resolucion de la imagen para que las fotos de los funcionarios no queden tan grandes
            $scale = $constantes['thumb_width'] / $w;
        }elseif($this->context->getUser()->hasAttribute('digifirma_cambio')) {
            $constantes = $this->getConstantes('digifirma');
            $name= $this->getUser()->getAttribute('digifirma_cambio');
            
            //NO CAMBIAMOS LA RESOLUCION DE LA IMAGEN SI ES UNA FIRMA
            $scale=1;
        }
        
        $cropped = $images->resizeThumbnailImage($this->getUser()->getAttribute('thumb_image_location'), $this->getUser()->getAttribute('large_image_location'), $w, $h, $x1, $y1, $scale);
        //Reload the page again to view the thumbnail

        if (file_exists($constantes['upload_dir_main'].$name.'.jpg')) {
            rename($constantes['upload_dir_main'].$name.'.jpg', $constantes['upload_dir_main'].$name.'_'.strtotime(date('Y-m-d h:i:s')).'.jpg');
        }
        
        copy($this->getUser()->getAttribute('thumb_image_location'), $constantes['upload_dir_main'].$name.'.jpg');
        unlink($this->getUser()->getAttribute('large_image_location'));
        unlink($this->getUser()->getAttribute('thumb_image_location'));

        $this->getUser()->getAttributeHolder()->remove('random_key');
        $this->getUser()->getAttributeHolder()->remove('user_file_ext');
        $this->getUser()->getAttributeHolder()->remove('large_image_location');
        $this->getUser()->getAttributeHolder()->remove('thumb_image_location');
        $this->getUser()->getAttributeHolder()->remove('foto_cambio');
        $this->getUser()->getAttributeHolder()->remove('digifirma');
        
        $this->redirect(sfConfig::get('sf_app_funcionarios_url').'funcionario');
    }
    
    public function executeGuardarFotoTomada(sfWebRequest $request) {
        if (file_exists('images/fotos_personal/'.$this->getUser()->getAttribute('foto_cambio').'.jpg')) {
            rename('images/fotos_personal/'.$this->getUser()->getAttribute('foto_cambio').'.jpg', 'images/fotos_personal/'.$this->getUser()->getAttribute('foto_cambio').'_'.strtotime(date('Y-m-d h:i:s')).'.jpg');
        }
        
        rename('uploads/fotos_temporales/'.$request->getParameter('foto'), 'images/fotos_personal/'.$this->getUser()->getAttribute('foto_cambio').'.jpg');
        exit();
    }
}

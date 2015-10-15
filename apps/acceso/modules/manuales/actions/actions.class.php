<?php

/**
 * manuales actions.
 *
 * @package    siglas-(institucion)
 * @subpackage manuales
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class manualesActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      $this->man = $request->getParameter('man');

      switch ($this->man){
            case '1': 
                $this->setTemplate('antesDeComenzar');
                break;
            case '2': 
                $this->setTemplate('conoceElEscritorioDeTrabajo');
                break;
            case '2.1': 
                $this->setTemplate('conoceElEscritorioDeTrabajo');
                $this->man = 'area_contenido';
                break;
            case '2.2': 
                $this->setTemplate('conoceElEscritorioDeTrabajo');
                $this->man = 'barra_inicio';
                break;
            case '2.2.1': 
                $this->setTemplate('conoceElEscritorioDeTrabajo');
                $this->man = 'temas';
                break;
            case '2.2.2': 
                $this->setTemplate('conoceElEscritorioDeTrabajo');
                $this->man = 'contrasena';
                break;
            case '2.3': 
                $this->setTemplate('conoceElEscritorioDeTrabajo');
                $this->man = 'menu_herramientas';
                break;
            case '3': 
                $this->setTemplate('correspondencia');
                break;
            case '3.1': 
                $this->setTemplate('correspondencia');
                $this->man = 'configurar_correspondencia';
                break;
            case '3.1.1': 
                $this->setTemplate('correspondencia');
                $this->man = 'grupos';
                break;
            case '3.1.2': 
                $this->setTemplate('correspondencia');
                $this->man = 'correlativos';
                break;
            case '3.2': 
                $this->setTemplate('correspondencia');
                $this->man = 'enviar_correspondencia';
                break;
            case '3.3': 
                $this->setTemplate('correspondencia');
                $this->man = 'recibir_correspondencia';
                break;
            case '3.4': 
                $this->setTemplate('correspondencia');
                $this->man = 'externa_correspondencia';
                break;
            case '4.1': 
                $this->setTemplate('drivers/gemalto');
                $this->man = 'inicio';
                break;
            case '4.2': 
                $this->setTemplate('drivers/epass3000');
                $this->man = 'inicio';
                break;
            default:
                $this->setTemplate('index');
      }
  }
  
  public function executeDescargar(sfWebRequest $request)
  {   
        $filepath = sfConfig::get("sf_root_dir")."/data/".$request->getParameter('archivo');
        $filename = explode("/", $filepath);
        $filename = $filename[count($filename)-1];
        //$filepath = '/home/me/folder/file.zip';
        $size = filesize($filepath);

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Content-Length: ".$size);

        $ok = fopen($filepath, 'rb');
        fpassthru($ok);

        exit();
  }
  
  public function executeDiccionarioDB(sfWebRequest $request)
  {   
      include sfConfig::get("sf_root_dir").'/data/documentacion/SIGLAS_diccionario_BD.html';
      exit();
  }  
}
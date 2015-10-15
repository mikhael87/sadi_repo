<?php

/**
 * reportes actions.
 *
 * @package    siglas
 * @subpackage reportes
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class reportesActions extends sfActions
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
      
      $tecnologias = Doctrine_Query::create()
            ->select('u.ip as id, u.so, u.agente, MAX(u.updated_at) as updated_at, u.usuario_enlace_id')
            ->from('Acceso_Usuario u')
            ->where('u.so <> ?','')
            ->groupBy('id, u.ip, u.so, u.agente, u.usuario_enlace_id')
            ->orderBy('u.ip')
            ->execute();
      /********************************************SISTEMAS OPERATIVOS***************************************/
      
      if($opcion == "sistemas")
      {
        $so=array();
        foreach ($tecnologias as $tecnologia) {
            if(!isset($so[$tecnologia->getSo()]))
                $so[$tecnologia->getSo()] = 0;   
            
            $so[$tecnologia->getSo()]++;
        }
        $this->so = $so;
      }
    /********************************************NAVEGADORES***************************************/
      
      elseif($opcion == "browsers")
      {
          $browser = array();
          $datos = array();
        foreach ($tecnologias as $tecnologia) {
            if(preg_match("/Firefox\//", $tecnologia->getAgente())){
                $versiones = explode(' ', $tecnologia->getAgente());
                foreach ($versiones as $version) {
                    if(preg_match("/Firefox/", $version))
                        $agente_tmp = trim($version);
                }

                $version = trim(str_replace('Firefox/', '', $agente_tmp));
                $version = explode('.', $version);
                
                $actualizar = '';                    
                if($version[0]<18) $actualizar = '<fond class="rojo">&nbsp;&nbsp;&nbsp;Actualizar a la version 18</fond>';
                if($version[0]<10) $version[0] = '0'.$version[0];
                
                $agente = 'Firefox '.$version[0].'.'.$version[1];
                $agenteUpdate = 'Firefox '.$version[0].'.'.$version[1].$actualizar;
            }
            else if(preg_match("/MSIE/", $tecnologia->getAgente())){
                $versiones = explode(';', $tecnologia->getAgente());
                foreach ($versiones as $version) {
                    if(preg_match("/MSIE/", $version))
                        $agente_tmp = trim($version);
                }
                
                $version = trim(str_replace('MSIE', '', $agente_tmp));
                $version = explode('.', $version);
                
                $actualizar = '';                    
                if($version[0]<9) $actualizar = '<fond class="rojo">&nbsp;&nbsp;&nbsp;Actualizar a la version 9</fond>';
                if($version[0]<10) $version[0] = '0'.$version[0];
                
                $agente = 'Internet Explorer '.$version[0].'.'.$version[1];
                $agenteUpdate = 'Internet Explorer '.$version[0].'.'.$version[1].$actualizar;
            }
            else if(preg_match("/Chrome/", $tecnologia->getAgente())){
                $versiones = explode(' ', $tecnologia->getAgente());
                foreach ($versiones as $version) {
                    if(preg_match("/Chrome/", $version))
                        $agente_tmp = trim($version);
                }
                
                
                $version = trim(str_replace('Chrome/', '', $agente_tmp));
                $version = explode('.', $version);
                
                $actualizar = '';                    
                if($version[0]<24) $actualizar = '<fond class="rojo">&nbsp;&nbsp;&nbsp;Actualizar a la version 24</fond>';
                if($version[0]<10) $version[0] = '0'.$version[0];
                
                $agente = 'Chrome '.$version[0].'.'.$version[1];
                $agenteUpdate = 'Chrome '.$version[0].'.'.$version[1].$actualizar;
            }
            else if(preg_match("/Safari/", $tecnologia->getAgente())){
                $versiones = explode(' ', $tecnologia->getAgente());
                foreach ($versiones as $version) {
                    if(preg_match("/Safari/", $version))
                        $agente_tmp = trim($version);
                }
                
                $version = trim(str_replace('Safari/', '', $agente_tmp));
                $version = explode('.', $version);
                
                $actualizar = '';                    
                if($version[0]<533) $actualizar = '<fond class="rojo">&nbsp;&nbsp;&nbsp;Actualizar a la version 5</fond>';
                if($version[0]<533) { $version[0] = '04'; $version[1] = '0'; }
                
                $agente = 'Safari '.$version[0].'.'.$version[1];
                $agenteUpdate = 'Safari '.$version[0].'.'.$version[1].$actualizar;
            }
            else if(preg_match("/Opera/", $tecnologia->getAgente())){
                $versiones = explode(' ', $tecnologia->getAgente());
                foreach ($versiones as $version) {
                    if(preg_match("/Opera/", $version))
                        $agente_tmp = trim($version);
                }
                
                $version = trim(str_replace('Opera/', '', $agente_tmp));
                $version = explode('.', $version);
                
                $actualizar = '';                    
                if($version[0]<12) $actualizar = '<fond class="rojo">&nbsp;&nbsp;&nbsp;Actualizar a la version 12</fond>';
                if($version[0]<10) $version[0] = '0'.$version[0];
                
                $agente = 'Opera '.$version[0].'.'.$version[1];
                $agenteUpdate = 'Opera '.$version[0].'.'.$version[1].$actualizar;
            }
            else
                $agente = $tecnologia->getAgente();
            if(!isset($browser[$agente])){
                $browser[$agente] = 0;   
                $browserUpdate[$agenteUpdate] = 0;   
            }
            $browser[$agente]++;
            $browserUpdate[$agenteUpdate]++;
            
            
            $funcionario_datos =  Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($tecnologia->getUsuarioEnlaceId());
            foreach($funcionario_datos as $funcionario_dato)
            {
                if(!isset($datos[$agenteUpdate][$funcionario_dato->getUnidadId()]))
                {
                    $datos[$agenteUpdate][$funcionario_dato->getUnidadId()] = '';
                }
                $datos[$agenteUpdate][$funcionario_dato->getUnidadId()] .= 
                        $tecnologia->getId()." ".
                        $funcionario_dato->getPrimerNombre()." ".$funcionario_dato->getSegundoNombre()." ".
                        $funcionario_dato->getPrimerApellido()." ".$funcionario_dato->getSegundoApellido().";".
                        $tecnologia->getSo()." ".$funcionario_dato->getUnombre().";-";
            }
            
        }
        
        ksort($browser);
        ksort($browserUpdate);
        ksort($datos);
        $this->browser = $browser;
        $this->browserUpdate = $browserUpdate;
        $this->datos = $datos;
      }
      
      /********************************************ORGANIGRAMA***************************************/
      
      elseif($opcion == "organigrama")
      {
        $organigrama = Doctrine::getTable('Organigrama_Unidad')->comboUnidad();

        $funcionarios=array();
        
        foreach( $organigrama as $unidad_id=>$unidad_nombre ) {
            if($unidad_id!=''){
                $funcionarios[$unidad_id] = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($unidad_id));
            }
        }
        
        $this->organigrama = $organigrama;
        $this->funcionarios = $funcionarios;
      }
      
      /********************************************USUARIOS***************************************/
      
      elseif($opcion == "usuarios")
      {
        $usuarios_perfiles = Doctrine::getTable('Acceso_UsuarioPerfil')->usuariosPerfiles();

        $this->usuarios_perfiles = $usuarios_perfiles;
      }
      
      
      /********************************************CORRESPONDENCIA***************************************/
      
      elseif($opcion == "correspondencia")
      {
          $organigrama = Doctrine::getTable('Organigrama_Unidad')->comboUnidad();
          $estadistica = new Correspondencia_CorrespondenciaStatistic();
          
          $estadistica_enviada = array();
          $estadistica_recibida = array();
          
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
                  
          foreach( $organigrama as $unidad_id=>$unidad_nombre ) {
              if($unidad_id!=''){
                $funcionarios[$unidad_id] = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($unidad_id));
                    $estadistica_enviada[$unidad_id] = $estadistica->totalStatusEnviada($unidad_id, $fecha_inicio,$fecha_final);
                    $estadistica_recibida[$unidad_id] = $estadistica->totalStatusRecibida($unidad_id, $fecha_inicio,$fecha_final);
              }
          }
          
          $this->organigrama = $organigrama;
          $this->estadistica_enviada = $estadistica_enviada;
          $this->estadistica_recibida = $estadistica_recibida;
          $this->fecha = "Estadistica generada desde: ".date('d/m/Y',  strtotime($fecha_inicio))." Hasta: ".date('d/m/Y',  strtotime($fecha_final));
          $this->unidad_id = $unidad_id;
      }
      
      $this->setTemplate($opcion);
  }
  
}

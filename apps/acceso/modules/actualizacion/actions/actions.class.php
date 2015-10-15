<?php

/**
 * actualizacion actions.
 *
 * @package    siglas
 * @subpackage actualizacion
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class actualizacionActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
 static function parametrosSVN(){
     return sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/subversion.yml");
 }   
 
 static function conexionDirectaDB(){
     $db_siglas = sfYaml::load(sfConfig::get("sf_root_dir")."/config/databases.yml");
     
     $username = $db_siglas['all']['doctrine']['param']['username'];
     $password = $db_siglas['all']['doctrine']['param']['password'];
     list($host,$dbname) = explode(';',str_replace(array('pgsql:','host=','dbname='), '', $db_siglas['all']['doctrine']['param']['dsn']));
     
     return pg_connect("dbname=".$dbname." user=".$username." host=".$host." password=".$password);
 }
 
 private function autenticarSVN(){
      $sf_subversion = $this->parametrosSVN();
      svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $sf_subversion['user']);
      svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $sf_subversion['password']);
 }

 public function executeIndex(sfWebRequest $request)
  {
      // LIMPIAR SESSIONES DE SQL POR EJECUTAR
      $this->getUser()->getAttributeHolder()->remove('sql_files_update');
      
      // BUSCAR PARAMETROS DEL SUBVERSION
      $sf_subversion = $this->parametrosSVN();

      //VERIFICAR QUE VERSION DE LIBRERIAS SVN TIENE EL CLIENTE, 
      //EN CASO DE QUE SEA MENOR QUE LA 1.5 BLOQUEAR PROCESO Y MANDAR MENSAJES DE QUE ACTUALICE EL PAQUETE
      //svn_client_version(); //sacar la version del paquete subversion del servidor
      
      // VERIFICAR QUE VERSION FUE LA ULTIMA QUE ACTUALIZAMOS
      $ultima_version_descargada = 1;
      $actualizaciones_svn = Doctrine::getTable('Siglas_ActualizacionSvn')->versionesDescargadas();
      
      if(count($actualizaciones_svn)>0)
          $ultima_version_descargada = $actualizaciones_svn[0]->getRevisionSvn();
      
      // SETEAMOS LOS PARAMETROS DE AUTENTICACION SVN Y URL DEL SERVIDOR SVN SIGLAS
      $this->autenticarSVN();
      $server_svn = $sf_subversion['dominio'].'/'.$sf_subversion['svn'].'/';

      // VERIFICAR LA ULTIMA VERSION DEL SVN
      $info_remoto = svn_ls($server_svn);
      $ultima_version_disponible = $info_remoto[$sf_subversion['project']]['created_rev'];

      // PREPARAR VARIABLES
      $this->actualizaciones_svn = $actualizaciones_svn;
      $this->ultima_version_descargada = $ultima_version_descargada;
      $this->ultima_version_disponible = $ultima_version_disponible;
  }
  
  public function executePrepareSqlUpdate(sfWebRequest $request)
  {
      // LIMPIAR SESSIONES DE SQL POR EJECUTAR
      $this->getUser()->getAttributeHolder()->remove('sql_files_update');
      $filenames = array();
      
      $this->autenticarSVN();
      svn_update(sfConfig::get("sf_root_dir").'/data/cambiosBD');

      $directorio_sql=opendir(sfConfig::get("sf_root_dir").'/data/cambiosBD'); 
      while ($archivo = readdir($directorio_sql)){ 
            if($archivo!='.' && $archivo!='..' && $archivo != 'historico'){
                $sql_ejecutado = Doctrine::getTable('Siglas_ActualizacionSql')->findOneByArchivo($archivo);
                
                if(!$sql_ejecutado)
                    $filenames[] = $archivo;
            } 
      } 
      closedir($directorio_sql);   
      
      sort($filenames);
      
      $this->getUser()->setAttribute('sql_files_update',$filenames);
      
      $this->filenames = $filenames;
  }
  
  public function executePlaySqlUpdate(sfWebRequest $request)
  {
        // LOS QUERYES EN ESTA FUNCION SE EJECUTAN DE MANERA BASICA
        // ES DECIR CON CONEXION DIRECTA A LA BD USANDO COMANDOS DE PHP Y NO DE DOCTRINE
        // DEVIDO A QUE LOS ARCHIVOS GUARDAN MULTIPLES QUERYS
      
        $sql_id = $request->getParameter('sql_id');
        $sql_id_next = $sql_id+1;
        $filenames = $this->getUser()->getAttribute('sql_files_update');

        $siglas = $this->conexionDirectaDB();
        $query = file_get_contents(sfConfig::get("sf_root_dir") . '/data/cambiosBD/' . $filenames[$sql_id]);
        
        if (!$result = @pg_exec($siglas, $query)) {
            echo '<div class="error">Error: ' . pg_errormessage($siglas) . '</div>';
            echo "<script>$('#sql_actions_cancel_".$sql_id."').show();</script>";
            pg_close($siglas);
        } else {
            echo '<div class="notice">Query ejecutado con exito</div>';
            echo "<script>$('#tr_sql_".$sql_id."').fadeOut(6000); $('#action_orden_".$sql_id_next."').show(); sql_procesados++;</script>";
            pg_close($siglas);
         
            $actualizacon_sql = new Siglas_ActualizacionSql();
            $actualizacon_sql->setArchivo($filenames[$sql_id]);
            $actualizacon_sql->setDetallesSql($query);
            $actualizacon_sql->setFActualizacion(date('Y-m-d h:i:s'));
            $actualizacon_sql->save();
            
            if (!(move_uploaded_file(sfConfig::get("sf_root_dir") . '/data/cambiosBD/' . $filenames[$sql_id], sfConfig::get("sf_root_dir") . '/data/cambiosBD/historico' . $filenames[$sql_id]))){
                echo '<div class="error">El query se ejecuto correctamente sin embargo el archivo que lo contiene no fue movido.</div>';
            }
        }

        exit();
  }
  
  public function executeCancelSqlUpdate(sfWebRequest $request)
  {
        $sql_id = $request->getParameter('sql_id');
        $sql_id_next = $sql_id+1;
        $filenames = $this->getUser()->getAttribute('sql_files_update');
        
        $query = file_get_contents(sfConfig::get("sf_root_dir") . '/data/cambiosBD/' . $filenames[$sql_id]);

        $actualizacon_sql = new Siglas_ActualizacionSql();
        $actualizacon_sql->setArchivo($filenames[$sql_id]);
        $actualizacon_sql->setDetallesSql($query);
        $actualizacon_sql->setFActualizacion(date('Y-m-d h:i:s'));
        $actualizacon_sql->save();
        
//        chmod(sfConfig::get("sf_root_dir") . '/data/cambiosBD/historico', 0777);
        if (!(move_uploaded_file(sfConfig::get("sf_root_dir") . '/data/cambiosBD/' . $filenames[$sql_id], sfConfig::get("sf_root_dir") . '/data/cambiosBD/historico' . $filenames[$sql_id]))){
            echo '<div class="error">El query se cancelo correctamente sin embargo el archivo que lo contiene no fue movido.</div>';
        }
        
        echo '<div class="error">La actualizacion de la base de datos con este query ha sido cancelada</div>';
        echo "<script>$('#tr_sql_".$sql_id."').fadeOut(6000); $('#action_orden_".$sql_id_next."').show(); sql_procesados++;</script>";        

        exit();
  }
  
  public function executePrepareSvnUpdate(sfWebRequest $request)
  {
      // LIMPIAR SESSIONES DE SQL POR EJECUTAR
      $this->getUser()->getAttributeHolder()->remove('sql_files_update');

      // BUSCAR PARAMETROS DEL SUBVERSION
      $sf_subversion = $this->parametrosSVN();
      
      // VERIFICAR QUE VERSION FUE LA ULTIMA QUE ACTUALIZAMOS
      $ultima_version_descargada = 1;
      $actualizaciones_svn = Doctrine::getTable('Siglas_ActualizacionSvn')->versionesDescargadas();
      
      if(count($actualizaciones_svn)>0)
          $ultima_version_descargada = $actualizaciones_svn[0]->getRevisionSvn();
      
      // SETEAMOS LOS PARAMETROS DE AUTENTICACION SVN Y URL DEL SERVIDOR SVN SIGLAS
      $this->autenticarSVN();
      $server_svn = $sf_subversion['dominio'].'/'.$sf_subversion['svn'].'/';

      // VERIFICAR LA ULTIMA VERSION DEL SVN
      $info_remoto = svn_ls($server_svn);
      $ultima_version_disponible = $info_remoto[$sf_subversion['project']]['created_rev'];
      
      // TRAER LISTA DE CAMBIOS DESDE LA ULTIMA VERSION DESCARGADA HASTA LA ULTIMA VERSION DISPONIBLE
      $this->lista_cambios = svn_log($server_svn, $ultima_version_descargada+1, $ultima_version_disponible);
  }
  
  public function executePlaySvnUpdate(sfWebRequest $request)
  {
      // BUSCAR PARAMETROS DEL SUBVERSION
      $sf_subversion = $this->parametrosSVN();
      
      // VERIFICAR QUE VERSION FUE LA ULTIMA QUE ACTUALIZAMOS
      $ultima_version_descargada = 1;
      $actualizaciones_svn = Doctrine::getTable('Siglas_ActualizacionSvn')->versionesDescargadas();
      
      if(count($actualizaciones_svn)>0)
          $ultima_version_descargada = $actualizaciones_svn[0]->getRevisionSvn();
      
      // SETEAMOS LOS PARAMETROS DE AUTENTICACION SVN Y URL DEL SERVIDOR SVN SIGLAS
      $this->autenticarSVN();
      $server_svn = $sf_subversion['dominio'].'/'.$sf_subversion['svn'].'/';

      // VERIFICAR LA ULTIMA VERSION DEL SVN
      $info_remoto = svn_ls($server_svn);
      $ultima_version_disponible = $info_remoto[$sf_subversion['project']]['created_rev'];
      
      echo '<hr/>2.1 Sincronizando con el servidor SVN (<i>svn update</i>)<br/>';
      $svn_update = svn_update(sfConfig::get("sf_root_dir"));
      
      if($svn_update!=FALSE){
          $log_cambios = '';
          if($ultima_version_descargada==1){
              $log_cambios = 'Version Inicial';
          } else {
              $lista_cambios = svn_log($server_svn, $ultima_version_descargada+1, $ultima_version_disponible);

              echo '<div class="f14n" style="max-height: 200px; width: 900px; overflow: auto; background-color: #000; color: white;">';
              $i=0;
              foreach($lista_cambios as $cambios) { 
                  $log_cambios[$i]['revision']=$cambios['rev'];
                  $log_cambios[$i]['f_disponible']=$cambios['date'];
                  $log_cambios[$i]['mensaje']=$cambios['msg'];
                  
                  echo '<b style="color: #1c94c4;">Revision: '.$cambios['rev'].'</b><br/>'; 
                  echo 'fecha: '.date('d-m-Y h:m a', strtotime($cambios['date'])).'<br/>'; 
                  echo '<i style="color: #939090;">'.$cambios['msg'].'</i>'; 
                  if($cambios['msg']!='')
                      echo '<br/>';

                  $j=0;
                  foreach($cambios['paths'] as $cambio) { 
                     $log_cambios[$i]['cambios'][$j]['tipo']=$cambio['action'];
                     $log_cambios[$i]['cambios'][$j]['archivo']=$cambio['path'];
                     $j++;
                     
                     
                     switch ($cambio['action']) {
                         case 'A':
                             $color = 'green';
                             break;
                         case 'M':
                             $color = 'blue';
                             break;
                         case 'D':
                             $color = 'red';
                             break;
                     }
                     echo '<b style="color: '.$color.';">'.$cambio['action'].'</b> -> '; 
                     echo $cambio['path'].'<br/>'; 
                  }  
                  echo '<br/><br/>';
                  
                  $i++;
              }
              echo '</div>';
              $log_cambios = sfYAML::dump($log_cambios);
          }

          $actualizacion_svn = new Siglas_ActualizacionSvn();
          $actualizacion_svn->setVersionSiglas($sf_subversion['version_name'].'('.$sf_subversion['version_number'].')');
          $actualizacion_svn->setFActualizacion(date('Y-m-d h:i:s'));
          $actualizacion_svn->setRevisionSvn($ultima_version_disponible);
          $actualizacion_svn->setLogCambios($log_cambios);
          $actualizacion_svn->save();
          
          echo '<hr/><div class="notice">Actualizacion descargada con exito</div>';
          echo "<script>
                    $('#div_svn_update').prepend('<div id=\"div_svn_update_ok\" style=\"display: none;\"><img src=\"/images/other/gracias_siglas.png\" width=\"900\"/></div>');
                    $('#div_svn_update_ok').fadeIn(10000);
                </script>"; 
          
          echo '<hr/>2.2 Limpiando cache de symfony (<i>symfony cc</i>)<br/>';
          ob_start();
          passthru('cd '.sfConfig::get("sf_root_dir").'; symfony cc;',$symfony_cc);
          $details = ob_get_contents();
          ob_end_clean();
          
          if($symfony_cc==0){
              echo '<div class="f14n" style="max-height: 200px; width: 900px; overflow: auto; background-color: #000; color: white;">';
              $details = str_replace('>> cache ', '>> <b style="color: green;">cache</b> ', $details);
              $details = str_replace('>> file- ', '>> <b style="color: green;">file-</b> ', $details);
              $details = str_replace('>> file+ ', '>> <b style="color: green;">file+</b> ', $details);
              $details = str_replace('>> chmod 777 ', '>> <b class="f14b" style="color: green;">chmod 777</b> ', $details);
              $details = str_replace('>>', '<br/>>>', $details);
              echo html_entity_decode($details);
              echo '</div>';
          }
          
          echo '<hr/>2.3 Reactivando plugins de symfony (<i>symfony plugin:publish-asset</i>)<br/>';
          ob_start();
          passthru('cd '.sfConfig::get("sf_root_dir").'; symfony plugin:publish-asset;',$symfony_plugin);
          $details = ob_get_contents();
          ob_end_clean();
          
          if($symfony_plugin==0){
              echo '<div class="f14n" style="max-height: 200px; width: 900px; overflow: auto; background-color: #000; color: white;">';
              $details = str_replace('>> plugin ', '>> <b style="color: green;">plugin</b> ', $details);
              $details = str_replace('>> link+ ', '>> <b style="color: green;">link+</b> ', $details);
              $details = str_replace('>>', '<br/>>>', $details);
              echo html_entity_decode($details);
              echo '</div>';
          }
      } else {
          echo '<div class="error">Ocurrio un error al descargar la actualizacion, por favor comunicate con soporte SIGLAS.</div>';
      }
      
      exit();
  }
}

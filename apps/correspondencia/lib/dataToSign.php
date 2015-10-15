<?php

class dataToSign {
    
  static function concatenar($correspondencia_id)
  {
      $id = $correspondencia_id;

      $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

      $rango_fecha_inicio = '2013-01-01 00:00:00';
      $rango_fecha_final = '';

      $cadena_visual = '';

      if($correspondencia->getCreatedAt()>$rango_fecha_inicio) // PARA CUANDO SE CAMBIEN LOS CAMPOS EN UN FUTURO $correspondencia->getCreatedAt()<$rango_fecha_final
      {
          $cadena_correspondencia = $correspondencia->getId();
          $cadena_correspondencia .= '+'.$correspondencia->getPadreId();
          $cadena_correspondencia .= '+'.$correspondencia->getGrupoCorrespondencia();
          $cadena_correspondencia .= '+'.$correspondencia->getNCorrespondenciaEmisor();
          $cadena_correspondencia .= '+'.$correspondencia->getNCorrespondenciaExterna();
          $cadena_correspondencia .= '+'.$correspondencia->getEmisorUnidadId();
          $cadena_correspondencia .= '+'.$correspondencia->getEmisorOrganismoId();
          $cadena_correspondencia .= '+'.$correspondencia->getEmisorPersonaId();
          $cadena_correspondencia .= '+'.$correspondencia->getEmisorPersonaCargoId();
//          $cadena_correspondencia .= '+'.$correspondencia->getFEnvio(); // LA FECHA DE ENVIO NO SE FIRMA DEBIDO A QUE CUANDO EXISTEN MUCHOS FIRMANTES LOS PRIMEROS TIENEN NULL EL ULTIMO CAMBIA LA FECHA E INVALIDAD LAS PRIMERAS FIRMAS
          $cadena_correspondencia .= '+'.$correspondencia->getPrivado();
          $cadena_correspondencia .= '+'.$correspondencia->getUnidadCorrelativoId();
          $cadena_correspondencia .= '+'.$correspondencia->getFuncionarioCorrelativoId();
          $cadena_correspondencia .= '+'.$correspondencia->getCreatedAt();

          $cadena_correspondencia_hash = hash('sha256', $cadena_correspondencia);
          $cadena_visual .= '<fond class="rojo"><br/><br/>######### CORRESPONDENCIA #########<br/><br/></fond>';
          $cadena_visual .= 'cadena: '.$cadena_correspondencia.'<br/>';
          $cadena_visual .= 'hash: '.$cadena_correspondencia_hash.'<br/>';


          $formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($id);
          $formato = $formato[0];

          $cadena_formato = $formato->getId();
          $cadena_formato .= '+'.$formato->getCorrespondenciaId();
          $cadena_formato .= '+'.$formato->getTipoFormatoId();
          $cadena_formato .= '+'.$formato->getCampoUno();
          $cadena_formato .= '+'.$formato->getCampoDos();
          $cadena_formato .= '+'.$formato->getCampoTres();
          $cadena_formato .= '+'.$formato->getCampoCuatro();
          $cadena_formato .= '+'.$formato->getCampoCinco();
          $cadena_formato .= '+'.$formato->getCampoSeis();
          $cadena_formato .= '+'.$formato->getCampoSiete();
          $cadena_formato .= '+'.$formato->getCampoOcho();
          $cadena_formato .= '+'.$formato->getCampoNueve();
          $cadena_formato .= '+'.$formato->getCampoDiez();
          $cadena_formato .= '+'.$formato->getCampoOnce();
          $cadena_formato .= '+'.$formato->getCampoDoce();
          $cadena_formato .= '+'.$formato->getCampoTrece();
          $cadena_formato .= '+'.$formato->getCampoCatorce();
          $cadena_formato .= '+'.$formato->getCampoQuince();
          $cadena_formato .= '+'.$formato->getCreatedAt();

          $cadena_formato_hash = hash('sha256', $cadena_formato);
          $cadena_visual .= '<fond class="rojo"><br/><br/>######### FORMATO #########<br/><br/></fond>';
          $cadena_visual .= 'cadena: '.$cadena_formato.'<br/>';
          $cadena_visual .= 'hash: '.$cadena_formato_hash.'<br/>';


          $emisores = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($id);

          $cadena_emisores = '';
          foreach ($emisores as $emisor) {
              $cadena_emisores .= $emisor->getId();
              $cadena_emisores .= '+'.$emisor->getCorrespondenciaId();
              $cadena_emisores .= '+'.$emisor->getFuncionarioId();
              $cadena_emisores .= '+'.$emisor->getFuncionarioCargoId();
              $cadena_emisores .= '+'.$emisor->getAccionDelegadaId();
              $cadena_emisores .= '+'.$emisor->getFuncionarioDelegadoId();
              $cadena_emisores .= '+'.$emisor->getFuncionarioDelegadoCargoId();
              $cadena_emisores .= '+'.$emisor->getCreatedAt();
          }

          $cadena_emisores_hash = hash('sha256', $cadena_emisores);
          $cadena_visual .= '<fond class="rojo"><br/><br/>######### EMISORES #########<br/><br/></fond>';
          $cadena_visual .= 'cadena: '.$cadena_emisores.'<br/>';
          $cadena_visual .= 'hash: '.$cadena_emisores_hash.'<br/>';

          $receptores_internos = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($id);

          $cadena_receptores_internos = '';
          if(count($receptores_internos)){
            foreach ($receptores_internos as $receptor_interno) {
                if($receptor_interno->getEstablecido()=='S'){
                    $cadena_receptores_internos .= $receptor_interno->getId();
                    $cadena_receptores_internos .= '+'.$receptor_interno->getCorrespondenciaId();
                    $cadena_receptores_internos .= '+'.$receptor_interno->getUnidadId();
                    $cadena_receptores_internos .= '+'.$receptor_interno->getFuncionarioId();
                    $cadena_receptores_internos .= '+'.$receptor_interno->getCopia();
                    $cadena_receptores_internos .= '+'.$receptor_interno->getPrivado();
                    $cadena_receptores_internos .= '+'.$receptor_interno->getCreatedAt();
                }
            }
          }

          $cadena_receptores_internos_hash = hash('sha256', $cadena_receptores_internos);
          $cadena_visual .= '<fond class="rojo"><br/><br/>######### RECEPTORES INTERNOS #########<br/><br/></fond>';
          $cadena_visual .= 'cadena: '.$cadena_receptores_internos.'<br/>';
          $cadena_visual .= 'hash: '.$cadena_receptores_internos_hash.'<br/>';


          $receptores_externos = Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($id);

          $cadena_receptores_externos = '';
          if(count($receptores_externos)){
            foreach ($receptores_externos as $receptor_externo) {
                $cadena_receptores_externos .= $receptor_externo->getId();
                $cadena_receptores_externos .= '+'.$receptor_externo->getCorrespondenciaId();
                $cadena_receptores_externos .= '+'.$receptor_externo->getOrganismoId();
                $cadena_receptores_externos .= '+'.$receptor_externo->getPersonaId();
                $cadena_receptores_externos .= '+'.$receptor_externo->getPersonaCargoId();
                $cadena_receptores_externos .= '+'.$receptor_externo->getCreatedAt();
            }

            // SE DEBE ESTUDIAR EL IMPACTO QUE EXISTE AL ORGANIZAR ORGANISMOS EXTERNOS CON LA HERRAMIENTA
            // CREAR CAMPO NUEVO EN RECEPTOR EXTERNO PARA IDENTIFICAR LOS ID ANTERIORES EXTERNOS EN CASO DE
            // ORGANIZARSE ESOS ID DEJAR UN RESPALDO DE QUE IDS ERAN PARA VALIDAR LA FIRMA CON ESOS ANTERIORES
            // ESTE PROCESO SOLO GUARDARA LOS IDS INICIALES DE LA PRIMERA CORRECCION
          }

          $cadena_receptores_externos_hash = hash('sha256', $cadena_receptores_externos);
          $cadena_visual .= '<fond class="rojo"><br/><br/>######### RECEPTORES EXTERNOS #########<br/><br/></fond>';
          $cadena_visual .= 'cadena: '.$cadena_receptores_externos.'<br/>';
          $cadena_visual .= 'hash: '.$cadena_receptores_externos_hash.'<br/>';


          $anexos_archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($id);

          $cadena_anexos_archivos = '';
          if(count($anexos_archivos)){
            foreach ($anexos_archivos as $anexo_archivo) {
                $cadena_anexos_archivos .= $anexo_archivo->getId();
                $cadena_anexos_archivos .= '+'.$anexo_archivo->getCorrespondenciaId();
                $cadena_anexos_archivos .= '+'.$anexo_archivo->getNombreOriginal();
                $cadena_anexos_archivos .= '+'.$anexo_archivo->getRuta();
                $cadena_anexos_archivos .= '+'.$anexo_archivo->getTipoAnexoArchivo();
                $cadena_anexos_archivos .= '+'.$anexo_archivo->getCreatedAt();

                $anexo_archivo_file = file_get_contents(sfConfig::get('sf_upload_dir').'/correspondencia/'.$anexo_archivo->getRuta());
                $cadena_anexos_archivos .= '+'.hash('sha256', $anexo_archivo_file);
            }
          }

          $cadena_anexos_archivos_hash = hash('sha256', $cadena_anexos_archivos);
          $cadena_visual .= '<fond class="rojo"><br/><br/>######### ANEXOS ARCHIVO #########<br/><br/></fond>';
          $cadena_visual .= 'cadena: '.$cadena_anexos_archivos.'<br/>';
          $cadena_visual .= 'hash: '.$cadena_anexos_archivos_hash.'<br/>';


          $anexos_fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($id);

          $cadena_anexos_fisicos = '';
          if(count($anexos_fisicos)){
            foreach ($anexos_fisicos as $anexo_fisico) {
                $cadena_anexos_fisicos .= $anexo_fisico->getId();
                $cadena_anexos_fisicos .= '+'.$anexo_fisico->getCorrespondenciaId();
                $cadena_anexos_fisicos .= '+'.$anexo_fisico->getTipoAnexoFisicoId();
                $cadena_anexos_fisicos .= '+'.$anexo_fisico->getObservacion();
                $cadena_anexos_fisicos .= '+'.$anexo_fisico->getCreatedAt();
            }
          }

          $cadena_anexos_fisicos_hash = hash('sha256', $cadena_anexos_fisicos);
          $cadena_visual .= '<fond class="rojo"><br/><br/>######### ANEXOS FISICOS #########<br/><br/></fond>';
          $cadena_visual .= 'cadena: '.$cadena_anexos_fisicos.'<br/>';
          $cadena_visual .= 'hash: '.$cadena_anexos_fisicos_hash.'<br/>';


          $cadena_visual .= '<fond class="rojo"><br/>HASH 512 DE CADENAS COMPLETO: <br/></fond>';
          $correspondencia_hash_512_cadenas = hash('sha512',
                                           $cadena_correspondencia.
                                           $cadena_formato.
                                           $cadena_emisores.
                                           $cadena_receptores_internos.
                                           $cadena_receptores_externos.
                                           $cadena_anexos_archivos.
                                           $cadena_anexos_fisicos);
          $cadena_visual .= $correspondencia_hash_512_cadenas.'<br/>';

          $cadena_visual .= '<fond class="rojo"><br/>CONCATENACION DE CADENAS HASH COMPLETO: <br/></fond>';
          $correspondencia_concatenacion_cadenas_hash = $cadena_correspondencia_hash.
                                           $cadena_formato_hash.
                                           $cadena_emisores_hash.
                                           $cadena_receptores_internos_hash.
                                           $cadena_receptores_externos_hash.
                                           $cadena_anexos_archivos_hash.
                                           $cadena_anexos_fisicos_hash;
          $cadena_visual .= $correspondencia_concatenacion_cadenas_hash.'<br/>';

//          echo $cadena_visual;

            return $correspondencia_hash_512_cadenas;
//          return $correspondencia_concatenacion_cadenas_hash;

//          echo '<input type="text" id="signature_packet" name="signature_packet" value="'.$correspondencia_hash_512_cadenas.'">';
//
//
//          $firmante_id = $this->getUser()->getAttribute('funcionario_id');
//          $firmante = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
//                      ->findOneByCorrespondenciaIdAndFuncionarioId($id, $firmante_id);
//
//          $centificado_conf = Doctrine::getTable('Funcionarios_FuncionarioCargoCertificado')
//                      ->findOneByFuncionarioCargoIdAndStatus($firmante->getFuncionarioCargoId(),'A');
//
//          $configuracion_good = '';
//          if(count($centificado_conf)>0){
//
//              if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//              else if( isset( $_SERVER ['HTTP_VIA'] ))  $ip = $_SERVER['HTTP_VIA'];
//              else if( isset( $_SERVER ['REMOTE_ADDR'] ))  $ip = $_SERVER['REMOTE_ADDR'];
//
//              $configuraciones = sfYaml::load($centificado_conf->getConfiguraciones());
//
////              print_r($configuraciones); exit();
//
//              foreach ($configuraciones as $configuracion) {
//                  echo $ip.'-->'.$configuracion['ip'].'<br>';
//                  if($configuracion['ip']==$ip){
//                      $configuracion_good = $configuracion['configuracion'];
//                  }
//              }
//          }
//          echo '<input type="text" id="signature_conf" name="signature_conf" value="'.$configuracion_good.'">';
      }

//      exit();
  }
}

?>

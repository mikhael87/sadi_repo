<?php

class wsOutputCorrespondencia {
    
    public function generarArray($correspondencia_id) {
        $cor_array= Array();
        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);
        
        if($correspondencia) {
            $cor_array['correspondencia']['id']= $correspondencia->getId();
            $cor_array['correspondencia']['padre']= $correspondencia->getPadreId();
            $cor_array['correspondencia']['grupo']= $correspondencia->getGrupoCorrespondencia();
            $cor_array['correspondencia']['correlativo']= sfConfig::get('sf_siglas').'('.$correspondencia->getNCorrespondenciaEmisor().')';
            $cor_array['correspondencia']['prioridad']= $correspondencia->getPrioridad();
            $cor_array['correspondencia']['privado']= $correspondencia->getPrivado();
            $cor_array['correspondencia']['f_envio']= $correspondencia->getFEnvio();
            
            
            $emisores = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_id);
            $i= 0;
            foreach ($emisores as $emisor) {
                $cor_array['emisor'][$i]['cedula']= $emisor->getCi();
                $cor_array['emisor'][$i]['primer_nombre']= $emisor->getPn();
                $cor_array['emisor'][$i]['segundo_nombre']= $emisor->getSn();
                $cor_array['emisor'][$i]['primer_apellido']= $emisor->getPa();
                $cor_array['emisor'][$i]['segundo_apellido']= $emisor->getSa();
                $cor_array['emisor'][$i]['sexo']= $emisor->getSexo();
                $cor_array['emisor'][$i]['email_principal']= $emisor->getEmailInstitucional();
                $cor_array['emisor'][$i]['email_secundario']= $emisor->getEmailPersonal();
                $cor_array['emisor'][$i]['unidad']= $emisor->getUnombre();
                $cor_array['emisor'][$i]['cargo_id']= $emisor->getCargoId();
                $cor_array['emisor'][$i]['cargo']= Formateo::cargo($emisor->getUnombre(), $emisor->getCtnombre());
                $cor_array['emisor'][$i]['proteccion']= sfYaml::load($emisor->getProteccion());
                
                
                $cor_array['emisor'][$i]['telefonos'][0]['numero']=$emisor->getTelfMovil();
                $cor_array['emisor'][$i]['telefonos'][0]['tipo']='movil';
                    
                $telf_fijo_emisor = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_id);
                
                $telefono_cargo= Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($emisor->getCargoId());
                $telefono_i=1;
                foreach ($telefono_cargo as $telefono_cargo) {
                    $cor_array['emisor'][$i]['telefonos'][$telefono_i]['numero']=$telefono_cargo->getTelefono();
                    $cor_array['emisor'][$i]['telefonos'][$telefono_i]['tipo']='fijo';
                    $telefono_i++;
                }
                
                $i++;
            }
            
            if($correspondencia->getEmisorOrganismoId() != '') {
                $emisor_externo= Doctrine::getTable('Organismos_Organismo')->find($correspondencia->getEmisorOrganismoId());
            
                $cor_array['emisor_externo']['id']= $emisor_externo->getId();
                $cor_array['emisor_externo']['organismo_nombre']= $emisor_externo->getNombre();
                
                $emisor_persona= Doctrine::getTable('Organismos_Persona')->find($correspondencia->getEmisorPersonaId());
                
                $cor_array['emisor_externo']['persona_nombre']= $emisor_persona->getNombreSimple();
                
                $emisor_persona_cargo= Doctrine::getTable('Organismos_PersonaCargo')->find($correspondencia->getEmisorPersonaCargoId());
                
                $cor_array['emisor_externo']['persona_cargo']= $emisor_persona_cargo->getNombre();
            }
            
            ///////////
            
            $formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_id);

            foreach($formatos as $formato) {
                $cor_array['formato']['id']= $formato->getId();
                $cor_array['formato']['tipo_formato_id']= $formato->getTipoFormatoId();
                $cor_array['formato']['tipo_formato_nombre']= $formato->getTadnombre();
                $cor_array['formato']['campo_uno']= $formato->getCampoUno();
                $cor_array['formato']['campo_dos']= $formato->getCampoDos();
                $cor_array['formato']['campo_tres']= $formato->getCampoTres();
                $cor_array['formato']['campo_cuatro']= $formato->getCampoCuatro();
                $cor_array['formato']['campo_cinco']= $formato->getCampoCinco();
                $cor_array['formato']['campo_seis']= $formato->getCampoSeis();
                $cor_array['formato']['campo_siete']= $formato->getCampoSiete();
                $cor_array['formato']['campo_ocho']= $formato->getCampoOcho();
                $cor_array['formato']['campo_nueve']= $formato->getCampoNueve();
                $cor_array['formato']['campo_diez']= $formato->getCampoDiez();
                $cor_array['formato']['campo_once']= $formato->getCampoOnce();
                $cor_array['formato']['campo_doce']= $formato->getCampoDoce();
                $cor_array['formato']['campo_trece']= $formato->getCampoTrece();
                $cor_array['formato']['campo_catorce']= $formato->getCampoCatorce();
                $cor_array['formato']['campo_quince']= $formato->getCampoQuince();
            }
            
            //////////
            
            $receptores_internos = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($correspondencia_id);

            if(count($receptores_internos) > 0){
              $i= 0;
              foreach ($receptores_internos as $receptor) {
                  if($receptor->getEstablecido()=='S'){
                      $cor_array['receptor'][$i]['primer_nombre']= $receptor->getPn();
                      $cor_array['receptor'][$i]['segundo_nombre']= $receptor->getSn();
                      $cor_array['receptor'][$i]['primer_apellido']= $receptor->getPa();
                      $cor_array['receptor'][$i]['segundo_apellido']= $receptor->getSa();
                      $cor_array['receptor'][$i]['unidad']= $receptor->getUnombre();
                      $cor_array['receptor'][$i]['cargo']= $receptor->getCtnombre();
                      $i++;
                  }
              }
            }
            
            ////////
            
            $receptores_externos = Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($correspondencia_id);
            
            if(count($receptores_externos) > 0){
              $i= 0;
              foreach ($receptores_externos as $receptor) {
                  
                $cor_array['receptor_externo'][$i]['organismo_id']= $receptor->getOrganismoId();
                $cor_array['receptor_externo'][$i]['organismo_nombre']= $receptor->getReceptorOrganismo();
                $cor_array['receptor_externo'][$i]['persona_ci']= $receptor->getReceptorPersonaCedula();
                $cor_array['receptor_externo'][$i]['persona_nombre']= $receptor->getReceptorPersona();
                $cor_array['receptor_externo'][$i]['persona_cargo']= $receptor->getReceptorPersonaCargo();
                $i++;
              }
            }
            
            ////////
            
            $anexos_archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($correspondencia_id);

            $i= 0;
            if(count($anexos_archivos)){
              foreach ($anexos_archivos as $anexo_archivo) {
                  $cor_array['adjunto'][$i]['nombre']= $anexo_archivo->getNombreOriginal();
                  $cor_array['adjunto'][$i]['ruta']= $anexo_archivo->getRuta();
                  $cor_array['adjunto'][$i]['tipo']= $anexo_archivo->getTipoAnexoArchivo();
                  $cor_array['adjunto'][$i]['fecha_creacion']= $anexo_archivo->getCreatedAt();
                  $i++;
              }
            }
            
            /////////
            
            $anexos_fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($correspondencia_id);

            $i= 0;
            if(count($anexos_fisicos) > 0){
              foreach ($anexos_fisicos as $anexo_fisico) {
                  $cor_array['fisico'][$i]['observacion']= $anexo_fisico->getObservacion();
                  $cor_array['fisico'][$i]['tipo']= $anexo_fisico->getTafnombre();
                  $cor_array['fisico'][$i]['fecha_creacion']= $anexo_fisico->getCreatedAt();
                  $i++;
              }
            }
        }
        
//        print_r($cor_array); exit;
        
        if(count($cor_array) > 0) {
//            $ready_ar= urlencode(serialize($cor_array));
            return $cor_array;
        }else {
            return 'ERROR, NO EXISTE LA CORRESPONDENCIA SOLICITADA';
        }
    }
}
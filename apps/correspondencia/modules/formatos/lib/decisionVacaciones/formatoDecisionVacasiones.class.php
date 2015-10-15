<?php

class formatoDecisionVacaciones {
    public function executeValidar($formulario, &$messages, &$formato) {
        // campo_uno = Asunto
        if (!$formulario["decisionVacaciones_resultado"]) {
            $messages = array_merge($messages, array("decisionVacaciones_resultado" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["decisionVacaciones_resultado"]);
        }

        // campo_dos = Contenido
        if (!$formulario["decisionVacaciones_observaciones"]) {
            $messages = array_merge($messages, array("decisionVacaciones_observaciones" => "Campo requerido"));
        } else {
            $formato->setCampoDos($formulario["decisionVacaciones_observaciones"]);
        }
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["decisionVacaciones_resultado"] = $datos["campo_uno"];
        $formulario["decisionVacaciones_observaciones"] = $datos["campo_dos"];

        return $formulario;
    }
    
    
    public function executeAdditionalCrear($correspondencia_id)
    {
    }
    
    public function executeAdditionalEmisor($correspondencia_id) {}
    
    public function executeAdditionalEnviar($correspondencia_id)
    {
        $correspondencia_respuesta_formato = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($correspondencia_id);
        $correspondencia_solicitud = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);
        $vacaciones_disfrutadas = Doctrine::getTable('Rrhh_VacacionesDisfrutadas')->findByCorrespondenciaSolicitudId($correspondencia_solicitud->getGrupoCorrespondencia());

        $valores = $this->executeTraer($correspondencia_respuesta_formato);
        
        if($valores["decisionVacaciones_resultado"]=="Aprobadas"){        
            foreach ($vacaciones_disfrutadas as $vacacion_disfrutada) {
                $vacacion_disfrutada->setStatus('A');
                $vacacion_disfrutada->save();
                
                $vacacion = Doctrine::getTable('Rrhh_Vacaciones')->find($vacacion_disfrutada->getVacacionesId()); 
                
                $dias_pendientes = $vacacion->getDiasDisfrutePendientes()-$vacacion_disfrutada->getDiasSolicitados();
                
                $vacacion->setDiasDisfrutePendientes($dias_pendientes);
                $vacacion->setStatus('E');
                $vacacion->save();
            }
        } else {
            foreach ($vacaciones_disfrutadas as $vacacion_disfrutada) {
                $vacacion_disfrutada->setStatus('N');
                $vacacion_disfrutada->save();
                
                $vacacion = Doctrine::getTable('Rrhh_Vacaciones')->find($vacacion_disfrutada->getVacacionesId()); 

                $vacacion->setStatus('D');
                $vacacion->save();
            }
        }
    }
}
<?php

class formatoRedireccion {
    public function executeValidar($formulario, &$messages, &$formato) {

        // campo_uno = Asunto
        if (!$formulario["redireccion_instruccion"]) {
            $messages = array_merge($messages, array("redireccion_instruccion" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["redireccion_instruccion"]);
        }

        // campo_dos = Observaciones
        $formato->setCampoDos($formulario["redireccion_observaciones"]);
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["redireccion_instruccion"] = $datos["campo_uno"];
        $formulario["redireccion_observaciones"] = $datos["campo_dos"];

        return $formulario;
    }
    
    public function executeAdditionalEmisor($correspondencia_id) {}
}
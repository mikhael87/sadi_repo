<?php

class formatoResumenExterno
{
        public function executeTraer($datos)
        {
            $formulario["id"] = $datos["id"];
            $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
            $formulario["resumen_externo_contenido"] = $datos["campo_uno"];

            return $formulario;
        }

}
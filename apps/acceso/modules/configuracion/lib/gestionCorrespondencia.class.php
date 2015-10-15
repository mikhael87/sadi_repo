<?php

class configGestionCorrespondencia {
    
    public function correlativos($parametros) {
        $correlativos = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findByUnidadIdAndStatus($parametros['unidad_id'],'A');
        
        $retorno['this']['correlativos'] = $correlativos;
        $retorno['template'] = 'correlativos';
        
        return $retorno;
    }
}

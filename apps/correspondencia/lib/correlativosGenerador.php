<?php

class correlativosGenerador {
    
    public function generarDeUnidad($unidad_correlativo_id) {
        $correlativo = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->find($unidad_correlativo_id);
        $unidad = Doctrine::getTable('Organigrama_Unidad')->find($correlativo->getUnidadId());
        $siglas_unidad = str_replace("/", "-", $unidad->getSiglas());
        $codigo_unidad = str_replace("/", "-", $unidad->getCodigoUnidad());
        
        $nomenclatura = $correlativo->getNomenclador();

        $nomenclatura = str_replace("Siglas", $siglas_unidad, $nomenclatura);
        $nomenclatura = str_replace("Codigo", $codigo_unidad, $nomenclatura);
        $nomenclatura = str_replace("Letra", $correlativo->getLetra(), $nomenclatura);
        $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
        $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
        $nomenclatura = str_replace("Día", date('d'), $nomenclatura);
        $nomenclatura_reinicio = str_replace("Secuencia", "1", $nomenclatura);
        $nomenclatura = str_replace("Secuencia", $correlativo->getSecuencia(), $nomenclatura);

        
        $verificar_reinicio = Doctrine::getTable('Correspondencia_Correspondencia')->findByNCorrespondenciaEmisor($nomenclatura_reinicio);

        if(!$verificar_reinicio[0]['id']){
            $nomenclatura = $nomenclatura_reinicio;

            $correlativos_unidad = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneById($correlativo->getId());
            $correlativos_unidad->setUltimoCorrelativo('REINICIADO');
            $correlativos_unidad->setSecuencia(1);
            $correlativos_unidad->save();
        } else {
            $listo=0;
            $i=0;
            $secuencia_replace = $correlativo->getSecuencia();

            while($listo == 0) {
                $verificar_consecutivo = Doctrine::getTable('Correspondencia_Correspondencia')->findByNCorrespondenciaEmisor($nomenclatura);

                if($verificar_consecutivo[0]['id']) {
                    $i++;

                    $nomenclatura = $correlativo->getNomenclador();

                    $nomenclatura = str_replace("Siglas", $siglas_unidad, $nomenclatura);
                    $nomenclatura = str_replace("Codigo", $codigo_unidad, $nomenclatura);
                    $nomenclatura = str_replace("Letra", $correlativo->getLetra(), $nomenclatura);
                    $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
                    $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
                    $nomenclatura = str_replace("Día", date('d'), $nomenclatura);
                    $nomenclatura = str_replace("Secuencia", $secuencia_replace++, $nomenclatura);
                } else {
                    $listo = 1;

                    if($i>0) $secuencia_replace--; else $secuencia_replace++;

                    $correlativos_unidad_edit = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneById($correlativo->getId());
                    $correlativos_unidad_edit->setUltimoCorrelativo('RESTABLECIDO');
                    $correlativos_unidad_edit->setSecuencia($secuencia_replace-1);
                    $correlativos_unidad_edit->save();
                }
            }
        }

        return $nomenclatura;
    }
    
    
    public function generarDeFuncionario() {
        $session_funcionario = sfContext::getInstance()->getUser()->getAttribute('session_funcionario');
        $funcionario_id = sfContext::getInstance()->getUser()->getAttribute('funcionario_id');

        $correlativos_funcionario = Doctrine::getTable('Correspondencia_FuncionarioCorrelativo')->findOneByFuncionarioId($funcionario_id);

        $secuencia_replace=1;
        if ($correlativos_funcionario)
        {
            $listo=0;
            $secuencia_replace = $correlativos_funcionario->getSecuencia();

            while($listo == 0)
            {
                $correlativo = $session_funcionario['cedula'].'-'.$secuencia_replace;
                $correspondencia_find = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($correlativo);

                if($correspondencia_find)
                    $secuencia_replace++;
                else
                    $listo = 1;

                $correlativos_funcionario->setUltimoCorrelativo('RESTABLECIDO');
                $correlativos_funcionario->setSecuencia($secuencia_replace);

                $correlativos_funcionario->save();
            }
        }
        else
        {
            $correlativos_funcionario = new Correspondencia_FuncionarioCorrelativo();
            $correlativos_funcionario->setFuncionarioId($funcionario_id);
            $correlativos_funcionario->setNomenclador('Cedula-Secuencia');
            $correlativos_funcionario->setUltimoCorrelativo('INICIAL');
            $correlativos_funcionario->setSecuencia($secuencia_replace);

            $correlativos_funcionario->save();

            $correlativo = $funcionario_ci.'-1';
        }

        return $correlativo;  
    }
}
<?php

class wsOutputEstructura {
    
    public function generarArray() {
        $estruc_array= Array();
        $organigrama = Doctrine::getTable('Organigrama_Unidad')->traerPrincipales();
        
        foreach ($organigrama as $unidad) {
            $estruc_array[$unidad->getId()]['unidad']=$unidad->getNombre();
            
            $funcionarios_principales = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionariosPrincipales($unidad->getId());
            $funcionario_i=0;
            foreach($funcionarios_principales as $funcionario_principal) {
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['id']=$funcionario_principal->getId();
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['ci']=$funcionario_principal->getCi();
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['primer_nombre']=$funcionario_principal->getPrimerNombre();
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['segundo_nombre']=$funcionario_principal->getSegundoNombre();
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['primer_apellido']=$funcionario_principal->getPrimerApellido();
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['segundo_apellido']=$funcionario_principal->getSegundoApellido();
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['email_institucional']=$funcionario_principal->getEmailInstitucional();
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['sexo']=$funcionario_principal->getSexo();
                
                $telefono_cargo= Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($funcionario_principal->getCargoId());
                $telefono_i=0;
                foreach ($telefono_cargo as $telefono_cargo) {
                    $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['telefonos'][$telefono_i]['numero']=$telefono_cargo->getTelefono();
                    $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['telefonos'][$telefono_i]['tipo']='fijo';
                    $telefono_i++;
                }
                
                $cargo = Formateo::cargo($unidad->getNombre(), $funcionario_principal->getCargoTipo());
                $estruc_array[$unidad->getId()]['funcionarios'][$funcionario_i]['cargo']=$cargo;
                
                $funcionario_i++;
            }
            
            if($funcionario_i == 0){
                unset($estruc_array[$unidad->getId()]);
            }

        }
        
        if(count($estruc_array) > 0) {
//            $ready_ar= urlencode(serialize($estruc_array));
            return $estruc_array;
        }else {
            return 'ERROR, NO SE LOGRO PROCESAR LA ESTRUCTURA ORGANIZATIVA';
        }
    }
}
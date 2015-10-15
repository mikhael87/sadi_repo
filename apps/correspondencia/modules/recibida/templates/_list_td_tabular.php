<?php

// INICIO BUSCANDO LA UNIDAD RECEPTORA REAL DEL FUNCIONARIO LOGEADO
        // BUSCAR UNIDADES DE RECEPTORES ESTABLECIDOS
        $unidades_receptoras = array();
        $unidad_recibe_id = '';
        $receptores_establecidos = Doctrine::getTable('Correspondencia_Receptor')->findByCorrespondenciaId($correspondencia_correspondencia->getId());
        
        $parametros_correspondencia['privado'] = 'N';
        $parametros_correspondencia['accesible'] = 'N';
        foreach ($receptores_establecidos as $receptor_establecido) {
            if($receptor_establecido->getEstablecido()=='S'){
                $unidades_receptoras[] = $receptor_establecido->getUnidadId();
                if($receptor_establecido->getFuncionarioId() == $sf_user->getAttribute('funcionario_id')){
                    // SI EL FUNCIONARIO LOGUEADO ES ESTABLECIDO COMO RECEPTOR SE SELECCIONA LA UNIDAD POR LA CUAL RECIBE
                    $unidad_recibe_id = $receptor_establecido->getUnidadId();
                }
            }
            
            if($receptor_establecido->getFuncionarioId() == $sf_user->getAttribute('funcionario_id')){
                $parametros_correspondencia['accesible'] = 'S';
            }
            
            if($receptor_establecido->getPrivado() == 'S'){
                $parametros_correspondencia['privado'] = $receptor_establecido->getPrivado();
            }
        }
        
        if($parametros_correspondencia['privado'] == 'N'){
            $parametros_correspondencia['accesible'] = 'S';
        }
        
        if($unidad_recibe_id==''){
            // EN CASO DE NO ENCONTRAR LA UNIDAD, BUSCAR SI LE FUE ASIGNADA LA CORRESPONDENCIA COMO UNA TAREA
            $receptor_asignado = Doctrine::getTable('Correspondencia_Receptor')->findOneByCorrespondenciaIdAndFuncionarioIdAndEstablecido($correspondencia_correspondencia->getId(), $sf_user->getAttribute('funcionario_id'), 'A');

            if($receptor_asignado){
                $unidad_recibe_id = $receptor_asignado->getUnidadId();
            }
        }
        
        if($unidad_recibe_id==''){
            // BUSCAR LAS UNIDADES A LA QUE PERTENECE EL FUNCIONARIO CON PERMISO DE LEER
            $unidades_receptoras = array_unique($unidades_receptoras);
            
            $funcionario_unidades_leer = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($sf_user->getAttribute('funcionario_id'),'leer');

            foreach($funcionario_unidades_leer as $unidad_leer) {
                if(array_search($unidad_leer->getAutorizadaUnidadId(), $unidades_receptoras)>=0){
                    $unidad_recibe_id = $unidad_leer->getAutorizadaUnidadId();
                }
            }
        }
        
        $parametros_correspondencia['unidad_recibe_id'] = $unidad_recibe_id;
// FIN BUSCANDO LA UNIDAD RECEPTORA REAL DEL FUNCIONARIO LOGEADO

$asignados=Doctrine::getTable('Correspondencia_Receptor')->receptoresPorCorrespondenciaUnidadEstablecido($correspondencia_correspondencia->getId(),$unidad_recibe_id,array('A'));

$reasignados=Doctrine::getTable('Correspondencia_Correspondencia')->respuestaActiva($correspondencia_correspondencia->getId(),$sf_user->getAttribute('funcionario_unidad_id'));
$parametros_correspondencia['reasignado']=0; foreach ($reasignados as $reasignado) $parametros_correspondencia['reasignado']++;

if($parametros_correspondencia['reasignado']==0){ 
    if($correspondencia_correspondencia->getStatus() == 'E') { $parametros_correspondencia['color'] ='silver'; $parametros_correspondencia['texto']='SIN LEER';} 
    elseif($correspondencia_correspondencia->getStatus() == 'D') { $parametros_correspondencia['color'] ='#D7DF01'; $parametros_correspondencia['texto']='DEVUELTA';}
    elseif($correspondencia_correspondencia->getStatus() == 'L') { 
        $parametros_correspondencia['color'] ='#2E9AFE'; $parametros_correspondencia['texto']='LEÍDA';
        if(count($asignados)>0){
           $parametros_correspondencia['color'] ='#0404B4'; $parametros_correspondencia['texto']='ASIGNADA'; 
        }
    }
} else { $parametros_correspondencia['color'] ='#04B404'; $parametros_correspondencia['texto']='PROCESADA';} 
?>

<td></td>
<td class="sf_admin_text sf_admin_list_td_identificacion">
  <?php echo get_partial('recibida/identificacion', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_documento">
  <?php echo get_partial('recibida/documento', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_detalles">
  <?php echo get_partial('recibida/detalles', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_acciones">
  <?php echo get_partial('recibida/acciones', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia)) ?>
</td>

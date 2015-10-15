<?php 
$emisores=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId(), TRUE);
$vistos_buenos = Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->funcionarios_vistobueno($correspondencia_correspondencia->getId());
$receptores_organismo=Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
$receptores_unidad=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondenciaActual($correspondencia_correspondencia->getId());
$formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
$tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($formato[0]->getTipoFormatoId());
$parametros_tipo_formato = sfYaml::load($tipo_formato[0]->getParametros());
$tipo_formato_nombre = $tipo_formato[0]->getNombre();

$parametros_correspondencia['privado'] = $correspondencia_correspondencia->getPrivado();

$parametros_correspondencia['accesible'] = 'S';
if($correspondencia_correspondencia->getPrivado()=='S'){
    $parametros_correspondencia['accesible'] = 'N';
    
    if($correspondencia_correspondencia->getIdCreate()==$sf_user->getAttribute('usuario_id')){
        $parametros_correspondencia['accesible'] = 'S';
    } else {
        foreach ($emisores as $emisor) {
            if($emisor->getFuncionarioId()==$sf_user->getAttribute('usuario_id') && $parametros_correspondencia['accesible'] == 'N'){
                $parametros_correspondencia['accesible'] = 'S';
            }
        }
    }
    
    if($parametros_correspondencia['accesible']=='N'){
        foreach ($vistos_buenos as $visto_bueno) {
            if($visto_bueno->getFuncionarioId()==$sf_user->getAttribute('usuario_id') && $parametros_correspondencia['accesible'] == 'N'){
                $parametros_correspondencia['accesible'] = 'S';
            }
        }
    }
}

//echo $correspondencia_correspondencia->getIdCreate().'->'.$sf_user->getAttribute('usuario_id');
if($correspondencia_correspondencia->getStatus() == 'C') { $parametros_correspondencia['color'] ='silver'; $parametros_correspondencia['texto']='EN CREACIÓN';} 
elseif($correspondencia_correspondencia->getStatus() == 'P') { $parametros_correspondencia['color'] ='#B40404'; $parametros_correspondencia['texto']='PAUSADO';} 
elseif($correspondencia_correspondencia->getStatus() == 'E') { $parametros_correspondencia['color'] ='#04B404'; $parametros_correspondencia['texto']='ENVIADA';} 
elseif($correspondencia_correspondencia->getStatus() == 'L') { $parametros_correspondencia['color'] ='#2E9AFE'; $parametros_correspondencia['texto']='RECIBIDO';} 
elseif($correspondencia_correspondencia->getStatus() == 'A') { $parametros_correspondencia['color'] ='#0404B4'; $parametros_correspondencia['texto']='ASIGNADA';} 
elseif($correspondencia_correspondencia->getStatus() == 'D') { $parametros_correspondencia['color'] ='#D7DF01'; $parametros_correspondencia['texto']='DEVUELTA';} 
elseif($correspondencia_correspondencia->getStatus() == 'F') { $parametros_correspondencia['color'] ='#0B0B61'; $parametros_correspondencia['texto']='FINALIZADO';} 
elseif($correspondencia_correspondencia->getStatus() == 'X') { $parametros_correspondencia['color'] ='#000000'; $parametros_correspondencia['texto']='ANULADA';} 
elseif($correspondencia_correspondencia->getStatus() == 'M') { $parametros_correspondencia['color'] ='#9A2EFE'; $parametros_correspondencia['texto']='SIGLAS MODIFICADAS';} // ESTOS SON DATOS DE CORRESPONDENCIAS SIMULADAS QUE LUEGO SE MODIFICO LAS SIGLAS DE LA UNIDAD
elseif($correspondencia_correspondencia->getStatus() == 'S') { $parametros_correspondencia['color'] ='#FA58D0'; $parametros_correspondencia['texto']='SIMULADO';} // ESTOS SON DATOS DE CORRESPONDENCIAS SIMULADAS

?>

<td class="sf_admin_text sf_admin_list_td_identificacion">
  <?php echo get_partial('enviada/identificacion', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia, 'emisores' => $emisores, 'vistos_buenos' => $vistos_buenos, 'receptores_organismo' => $receptores_organismo, 'receptores_unidad' => $receptores_unidad)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_documento">
  <?php echo get_partial('enviada/documento', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia, 'formato' => $formato, 'parametros_tipo_formato' => $parametros_tipo_formato, 'tipo_formato_nombre' => $tipo_formato_nombre)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_detalles">
  <?php echo get_partial('enviada/detalles', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_acciones">
  <?php echo get_partial('enviada/acciones', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia, 'vistos_buenos' => $vistos_buenos, 'receptores_organismo' => $receptores_organismo, 'formato' => $formato, 'parametros_tipo_formato' => $parametros_tipo_formato)) ?>
</td>

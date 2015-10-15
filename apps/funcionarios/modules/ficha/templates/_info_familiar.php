<?php if ($sf_user->hasFlash('error_familiar')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error_familiar') ?></div>
<?php endif ?>
<?php 
    $fechat="";
    $estudia="";
    $trabaja="";
    $depende="";
    $sexo = "";
   
    $genero = array('m' => 'Masculino', 'f' => 'Femenino'); 
    $cantfamiliar = Doctrine::getTable('Funcionarios_Familiar')->findByFuncionarioIdAndStatus(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'),'A');
    if ($cantfamiliar[0]!=''){
    foreach($cantfamiliar as $familiar): 

        $parentesco  = Doctrine::getTable('Public_Parentesco')->findById($familiar->getParentescoId());
        $nivelacademico  = Doctrine::getTable('Public_NivelAcademico')->findById($familiar->getNivelAcademicoId());
        $id = $familiar->getId();

        if ($familiar->getFNacimiento()!=""){
            $fecha=explode('-',$familiar->getFNacimiento(),3); 
            $fechat = $fecha[2]."-".$fecha[1]."-".$fecha[0];
        }
        if ($familiar->getEstudia()==1){ $estudia = "SI";}else{$estudia = "NO";}
        if ($familiar->getTrabaja()==1){ $trabaja = "SI";}else{$trabaja = "NO";}
        if ($familiar->getDependencia()==1){ $depende = "SI";}else{$depende = "NO";}
        if ($familiar->getSexo()!=""){        
            $sexo = $genero[$familiar->getSexo()];
        }
?>
<div align="right"  style="position: relative; right: 5px; top: 5px;">
    <a style="right: 0px;" title="Agregar Información Corporal" href="#" onclick="javascript:abrir_notificacion_derecha('infoFamiliarcorporal?familiarcorporal_accion=editar&familiarcorporal_id=<?php echo $id; ?>'); return false;">
        <?php echo image_tag('icon/corporal16.png'); ?>
    </a>
    &nbsp;
    <a style="right: 0px;" title="Agregar Discapacidad"  href="#" onclick="javascript:abrir_notificacion_derecha('infoFamiliardiscapacidad?familiardiscapacidad_accion=editar&familiardiscapacidad_id=<?php echo $id; ?>'); return false;">
        <?php echo image_tag('icon/discapacidad1.png'); ?>
    </a>
    &nbsp;
    <a style="right: 0px;" title="Editar" href="#" onclick="javascript:abrir_notificacion_derecha('infoFamiliar?familiar_accion=editar&familiar_id=<?php echo $id; ?>'); return false;">
        <?php echo image_tag('icon/edit.png'); ?>
    </a>
    &nbsp;
    <a style="right: 0px;" title="Eliminar" href="<?php echo url_for('ficha/eliminarFamiliar?familiar_id='.$id) ?>" onclick="javascript:if(confirm('Esta seguro de eliminar?')){return true}else{return false};">
        <?php echo image_tag('icon/delete.png'); ?>
    </a>
</div>


<div class="sf_admin_form_row sf_admin_text">
    <div>
        <span class="spanFichaInicial">Parentesco:</span> <?php echo $parentesco[0]; ?>&nbsp;&nbsp;
        <span class="spanFicha">Cédula:</span> <?php echo $familiar->getNacionalidad()."-".$familiar->getCi(); ?>&nbsp;&nbsp;        
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>        
        <span class="spanFichaInicial">Nombre:</span> <?php echo $familiar->getPrimerNombre()." ".$familiar->getSegundoNombre()." ".$familiar->getPrimerApellido()." ".$familiar->getSegundoApellido(); ?>&nbsp;
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <span class="spanFichaInicial">Fecha nac: </span> <?php echo $fechat; ?>&nbsp;
        <span class="spanFicha">Género: </span><?php echo $sexo; ?>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <span class="spanFichaInicial">Nivel Academico: </span><?php echo $nivelacademico[0]; ?>&nbsp;&nbsp;
        
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>      
        <span class="spanFichaInicial">Estudia act. </span><?php echo $estudia; ?>&nbsp;
        <span class="spanFicha">Trabaja: </span><?php echo $trabaja; ?>&nbsp;&nbsp;
        <span class="spanFicha">Depende del funcionario:</span><?php echo $depende; ?>
    </div>
</div>

<hr>

<?php endforeach; ?>
<?php } else { ?>
    <div class="sf_admin_form_row sf_admin_text" style="min-height: 70px;">
        <div class="f16n gris_medio" style="text-align: justify;">
            Tu información familiar es importante, ...............
        </div>
    </div>
<?php } ?>




<?php   
    $fechat="";
    $fechag="";
    $estudia="";

    $canteduuniversitaria = Doctrine::getTable('Funcionarios_EducacionUniversitaria')->datosFuncionarioEducacionUniversitaria(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
    $cantedumedia = Doctrine::getTable('Funcionarios_EducacionMedia')->datosFuncionarioEducacionMedia(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
    $canteduadicional = Doctrine::getTable('Funcionarios_EducacionAdicional')->datosFuncionarioEducacionAdicional(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
    
    if (($canteduuniversitaria[0]!='') or ($cantedumedia[0]!='') or ($canteduadicional[0]!='')){
?>
<fieldset>
    <label style="width: 9em;margin-top:7px;">Nivel Universitario</label>
<?php    
    if ($canteduuniversitaria[0]!=''){
    foreach($canteduuniversitaria as $eduuniversitaria): 
        $pais  = Doctrine::getTable('Public_Pais')->findById($eduuniversitaria->getPaisId());
        $organismo_educativo = Doctrine::getTable('Organismos_Organismo')->findById($eduuniversitaria->getOrganismoEducativoId());
        $carrera = Doctrine::getTable('Public_CarreraUniversitaria')->findById($eduuniversitaria->getCarreraId());
        $nivelacademico  = Doctrine::getTable('Public_NivelAcademico')->findById($eduuniversitaria->getNivelAcademicoId());
        $id = $eduuniversitaria->getId();

        if ($eduuniversitaria->getFIngreso()!=""){
            $fecha=explode('-',$eduuniversitaria->getFIngreso(),3); 
            $fechat = $fecha[2]."-".$fecha[1]."-".$fecha[0];
        }
        if ($eduuniversitaria->getFGraduado()!=""){
            $fecha=explode('-',$eduuniversitaria->getFGraduado(),3); 
            $fechag = $fecha[2]."-".$fecha[1]."-".$fecha[0];
        }
        if ($eduuniversitaria->getEstudiandoActualmente()==1){ $estudia = "SI";}else{$estudia = "NO";}
        
?>
<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>

    <div align="right" style="position: relative; right: 5px; top: 5px;">
        <a  href="#"  style="right: 0px;" title="Editar"  onclick="javascript:abrir_notificacion_derecha('infoEduuniversitaria?eduuniversitaria_accion=editar&eduuniversitaria_id=<?php echo $id; ?>'); return false;">
            <?php echo image_tag('icon/edit.png'); ?>
        </a>
        &nbsp;&nbsp;
        <a style="right: 0px;" title="Eliminar"  href="<?php echo url_for('ficha/eliminarEduuniversitaria?eduuniversitaria_id='.$id) ?>" onclick="javascript:if(confirm('Esta seguro de eliminar?')){return true}else{return false};">
            <?php echo image_tag('icon/delete.png'); ?>
        </a>
    </div>
   
<div class="sf_admin_form_row sf_admin_text">
    <div>
       
        <div class="">
            <?php echo $organismo_educativo[0]; ?> - <?php echo $pais[0]; ?>&nbsp;
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>       
        <div class="">
            <?php echo $carrera[0]; ?>&nbsp; - <?php echo $nivelacademico[0]; ?>&nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <div class="">
            <?php echo $fechat; ?>&nbsp; / <?php echo $fechag; ?>&nbsp;
        </div>
       
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
        Estudia actualmente: <?php echo $estudia; ?> - Segmento: <?php echo $eduuniversitaria->getSegmento(); ?>
</div>
........................................................................................................................

<?php endforeach; } 
if ($cantedumedia[0]!=''){
?>
    
</fieldset>

<!-- Educacion media -->
<fieldset>
    <label style="width: 9em;margin-top:7px;">Nivel Medio</label>
<?php 

 $especialidad = array('0' => 'ninguna', '1' => 'Ciencias', '2' => 'Humanidades','3' => 'Tecnico medio'); 
    foreach($cantedumedia as $edumedia): 
        $pais  = Doctrine::getTable('Public_Pais')->findById($edumedia->getPaisId());
        $organismo_educativo = Doctrine::getTable('Organismos_Organismo')->findById($edumedia->getOrganismoEducativoId());        
        $nivelacademico  = Doctrine::getTable('Public_NivelAcademico')->findById($edumedia->getNivelAcademicoId());
        $id = $edumedia->getId();

        if ($edumedia->getFIngreso()!=""){
            $fecha=explode('-',$edumedia->getFIngreso(),3); 
            $fechat = $fecha[2]."-".$fecha[1]."-".$fecha[0];
        }
        if ($edumedia->getFGraduado()!=""){
            $fecha=explode('-',$edumedia->getFGraduado(),3); 
            $fechag = $fecha[2]."-".$fecha[1]."-".$fecha[0];
        }
        if ($edumedia->getEstudiandoActualmente()==1){ $estudia = "SI";}else{$estudia = "NO";}
        
?>

    <div align="right"  style="position: relative; right: 5px; top: 5px;">
        <a   style="right: 0px;" title="Editar"  href="#" onclick="javascript:abrir_notificacion_derecha('infoEdumedia?edumedia_accion=editar&edumedia_id=<?php echo $id; ?>'); return false;">
            <?php echo image_tag('icon/edit.png'); ?>
        </a>
        &nbsp;&nbsp;&nbsp;
        <a   style="right: 0px;" title="Eliminar"  href="<?php echo url_for('ficha/eliminarEdumedia?edumedia_id='.$id) ?>" onclick="javascript:if(confirm('Esta seguro de eliminar?')){return true}else{return false};">
            <?php echo image_tag('icon/delete.png'); ?>
        </a>
    </div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        
        <div class="">
            <?php echo $organismo_educativo[0]; ?>&nbsp; - <?php echo $pais[0]; ?>&nbsp;
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        
        <div class="">
            <?php echo $especialidad[$edumedia->getEspecialidadId()]; ?> - <?php echo $nivelacademico[0]; ?>&nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>        
        <div class="">
            <?php echo $fechat; ?>&nbsp; / <?php echo $fechag; ?>&nbsp;
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        Estudia actualmente: <?php echo $estudia; ?>&nbsp;
       
    </div>
</div>
........................................................................................................................

<?php endforeach; }
if ($canteduadicional[0]!=''){
?>


<!-- Educacion adicional -->
<fieldset>
    <label style="width: 11em;margin-top:7px;">Estudios Adicionales</label>
<?php 


    foreach($canteduadicional as $eduadicional): 
        $pais  = Doctrine::getTable('Public_Pais')->findById($eduadicional->getPaisId());
        $organismo_educativo = Doctrine::getTable('Organismos_Organismo')->findById($eduadicional->getOrganismoEducativoId());        
        $tipo_educacion = Doctrine::getTable('Public_TipoEducacionAdicional')->findById($eduadicional->getTipoEducacionAdicionalId());        
        $id = $eduadicional->getId();

        if ($eduadicional->getFIngreso()!=""){
            $fecha=explode('-',$eduadicional->getFIngreso(),3); 
            $fechat = $fecha[2]."-".$fecha[1]."-".$fecha[0];
        }      
        
?>
<div align="right"  style="position: relative; right: 5px; top: 5px;">
    <a style="right: 0px;" title="Editar" href="#" onclick="javascript:abrir_notificacion_derecha('infoEduadicional?eduadicional_accion=editar&eduadicional_id=<?php echo $id; ?>'); return false;">
        <?php echo image_tag('icon/edit.png'); ?>
    </a>
    &nbsp;&nbsp;&nbsp;
    <a style="right: 0px;" title="Eliminar"  href="<?php echo url_for('ficha/eliminarEduadicional?eduadicional_id='.$id) ?>" onclick="javascript:if(confirm('Esta seguro de eliminar?')){return true}else{return false};">
        <?php echo image_tag('icon/delete.png'); ?>
    </a>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
     
        <div class="">
            <?php echo $organismo_educativo[0]; ?>&nbsp;- <?php echo $pais[0]; ?>&nbsp;
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <div class="">
            <?php echo $eduadicional->getNombre(); ?> - Tipo: <?php echo $tipo_educacion[0]; ?>
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <div class="">
            <?php echo $fechat; ?>&nbsp; - Horas: <?php echo $eduadicional->getHoras(); ?>
        </div>
    </div>
</div>
........................................................................................................................

<?php endforeach; }?>
<?php } else { ?>
    <div class="sf_admin_form_row sf_admin_text" style="min-height: 70px;">
        <div class="f16n gris_medio" style="text-align: justify;">
            Solicitamos tu información de contacto con la intencion de recibir y compartir 
            información contigo, como notificarte cuando puedes disfrutar de tus vacaciones, 
            cuando exista un evento de tu interés, enviarte respaldos de tu información, etc.<br/><br/>
            Para nosotros es importante resguardar tu privacidad de forma 
            segura por lo tanto únicamente tendrán acceso a ella personal de Recursos Humanos. 
        </div>
    </div>
<?php } ?>

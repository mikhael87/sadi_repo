<?php 
    $fechat="";
    $estado_civil="";
    $sexo = "";
    $edo_civil = array('s' => 'Soltero','c'=> 'Casado','v'=> 'Viudo','d'=> 'Divorciado');
    $genero = array('m' => 'Masculino', 'f' => 'Femenino');
    $estados = Doctrine::getTable('Public_Estado')->findAll();
    
    if ($basica->getFNacimiento()!=""){
        $fecha=explode('-',$basica->getFNacimiento(),3); 
        $fechat = $fecha[2]."-".$fecha[1]."-".$fecha[0];
    }
    if ($basica->getEdoCivil()!=""){        
        $estado_civil = $edo_civil[$basica->getEdoCivil()];
    }
    if ($basica->getSexo()!=""){        
        $sexo = $genero[$basica->getSexo()];
    }
?>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label style="width: 15em;" for="">Fecha de nacimiento</label>
        <div class="content">
            <?php echo $fechat;?>&nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label style="width: 15em;" for="">Estado de nacimiento</label>
        <div class="content">
            <?php echo $estados[$basica->getEstadoNacimientoId() - 1]; ?>&nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label style="width: 15em;" for="">Sexo</label>
        <div class="content">
            <?php echo $sexo; ?>&nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label style="width: 15em;" for="">Estado civil</label>
        <div class="content">
            <?php echo $estado_civil; ?>&nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label style="width: 15em;" style="width: 15em;" for="">Licencia de conducir grado</label>
        <div class="content">
            <?php echo (($basica->getLicenciaConducirUnoGrado()!='')? $basica->getLicenciaConducirUnoGrado()." º grado" : "N/A"); ?>&nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label style="width: 15em;" style="width: 15em;" for="">Otra Licencia de conducir grado</label>
        <div class="content">
            <?php echo (($basica->getLicenciaConducirDosGrado()!='')? $basica->getLicenciaConducirDosGrado()." º grado" : "N/A"); ?>&nbsp;
        </div>
    </div>
</div>

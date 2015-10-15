<?php $funcionario=Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDelCargo($organigrama_cargo->getId()); ?>

<div style="text-align: center; height: 100px;">
    <?php 
    if(count($funcionario)>0){ 
        echo $funcionario[0]['persona'];  ?>
        <br/>
        <?php if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$funcionario[0]['ci'].'.jpg')){ ?>
            <img src="/images/fotos_personal/<?php echo $funcionario[0]['ci']; ?>.jpg" width="70"/>
        <?php } else { ?>
            <img src="/images/other/siglas_photo_small_<?php echo $funcionario[0]['sexo'].substr($funcionario[0]['ci'], -1); ?>.png" width="70"/>
        <?php } ?>        
    <?php } else { 
        echo "<br/><br/><b>DISPONIBLE</b>"; } 
    ?>    
</div>
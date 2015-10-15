<?php 
    $cantgrupos = Doctrine::getTable('Funcionarios_GrupoSocial')->datosGrupoFuncionario(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
   
    
    foreach($cantgrupos as $grupo):     
        $id = $grupo->getId();    
    
        $tipoGrupo = Doctrine::getTable('Public_TipoGrupoSocial')->findById($grupo->getTipoGrupoSocialId());
?>
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label style="width: 10em;" for=""><?php echo $tipoGrupo[0]; ?>:</label>
            <div class="content">
            <?php echo $grupo->getNombre(); ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>



<?php 
    $tipos = array('1' => image_tag('icon/phone.png'), '2' => image_tag('icon/movil.png'), '3' => image_tag('icon/mail_new.png'));
    $cantcontactos = Doctrine::getTable('Funcionarios_Contacto')->datosContatoFuncionario(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
    
        
    if(count($cantcontactos)>0){
     $id = 0;
     echo '  <div  class="sf_admin_form_row sf_admin_text">';
    foreach($cantcontactos as $contacto){       
            
?>      
    <?php if($contacto->getTipo()!=$id){?>
        </div>
        <div class="sf_admin_form_row sf_admin_text">
                <label style="width: 2em;" for=""><?php echo $tipos[$contacto->getTipo()]; ?></label>

            <?php echo $contacto->getValor(); ?>


    <?php }else{ ?>
           , <?php echo $contacto->getValor(); ?>
    <?php } 
           $id=$contacto->getTipo();         
        ?>

<?php } echo "</div>"; } else { ?>
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




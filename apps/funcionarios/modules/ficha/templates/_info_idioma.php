<?php 
    $cantidiomas = Doctrine::getTable('Funcionarios_IdiomaManejado')->datosIdiomaFuncionario(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
    if ($cantidiomas[0]!=''){
        $valores = array('0' => 'No','1'=> 'Si'); 
        $valores = array('0' => image_tag('icon/fail.png'), '1' => image_tag('icon/ok.png'));
?>
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <div class="">                
                <table width="100%" class="sf_admin_form_row sf_admin_text" style="font-size: 11.5px;">
                    <tr>
                        <th >Idioma</th>
                        <th >Principal</th>
                        <th >Habla</th>
                        <th >Lee</th>
                        <th >Escribe</th>
                    </tr>               

                <?php

                    foreach($cantidiomas as $idioma):          
                        $id = $idioma->getId();        
                        $idiomaDes= Doctrine::getTable('Public_Idioma')->findById($idioma->getIdiomaId());
                ?>    
       
                    <tr>
                        <td ><?php echo $idiomaDes[0]; ?> </td>
                        <td ><?php echo $valores[$idioma->getPrincipal()]; ?> </td>
                        <td ><?php echo $valores[$idioma->getHabla()]; ?></td>
                        <td ><?php echo $valores[$idioma->getLee()]; ?></td>
                        <td ><?php echo $valores[$idioma->getEscribe()]; ?></td>
                    </tr>
        <?php endforeach; ?>
                </table>        
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="sf_admin_form_row sf_admin_text" style="min-height: 70px;">
        <div class="f16n gris_medio" style="text-align: justify;">
            Tu información de idiomas es importante, ...............
        </div>
    </div>
<?php } ?>


<?php if($parametros_correspondencia['accesible'] == 'S') { ?>
    <div style="min-width: 130px;">

    <!--<a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/enviarInteroperabilidad'; ?>" style="text-decoration: none" title="INTEROPERABILIDAD">
    <?php echo image_tag('icon/combine2.png'); ?>
    </a>-->



    <?php
    $all_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByCorrespondenciaId($correspondencia_correspondencia->getId());
    $nonfirm= 0;
    foreach($all_vb as $val) {
        if($val->getStatus() == 'E')
            $nonfirm++;
    }

    if(count($formato)>0){
        if($correspondencia_correspondencia->getStatus()!='X'){
    ?>

        <?php
        $ban_send = -1;
        if($correspondencia_correspondencia->getStatus()!='L' && $correspondencia_correspondencia->getStatus()!='F')
        {  ?>

        <?php if($correspondencia_correspondencia->getFirmasListas() == 0) { ?>
            <?php 
            
            $mensaje_edit_vb = '';
            if(count($vistos_buenos) > 0) { 
                $mensaje_edit_vb = '- Se eliminar&aacute; todo el progreso sobre la ruta de Visto bueno. \n- Si usted no es el primero en la ruta de Visto bueno, dejar&aacute; de ver este documento.\n¿Desea continuar?';
            }
            ?>
            <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/edit'; ?>" style="text-decoration: none" title="Editar"
               <?php if ($correspondencia_correspondencia->getIdCreate()!=$sf_user->getAttribute('usuario_id')) { ?>
                   onclick="return confirm('- Esta editanto la correspondencia de otro usuario.\n<?php echo $mensaje_edit_vb; ?>');"
               <?php }else { if($mensaje_edit_vb != '') { ?>
                   onclick="return confirm('<?php echo $mensaje_edit_vb; ?>');"
               <?php }} ?>>
                <?php echo image_tag('icon/edit.png'); ?>
            </a>

            <?php
            foreach($all_vb as $value) {
                if($value->getFuncionarioId() == $sf_user->getAttribute('usuario_id')) {
                    if($value->getTurno())
                        echo '<a href="'. sfConfig::get('sf_app_correspondencia_url') .'enviada/'. $correspondencia_correspondencia->getId() .'/darVistobueno" style="text-decoration: none" title="Dar visto bueno" onclick="return confirm("Confirma el visto bueno, ¿Continuar?");">'. image_tag('icon/ok.png') .'</a>';
                    elseif($value->getTurno()== FALSE && $value->getStatus()== 'E') {
                        echo image_tag('icon/ok_wait.png', array('style'=> 'cursor: pointer', 'title'=> 'Faltan vistos buenos anteriores'));
                    }
                }
            }
    //        echo '<a href="javascript: revertir_vb(\''. $correspondencia_correspondencia->getId() .'\');">'.  image_tag('icon/ruta.png') .'</a>';
            ?>
        <?php } ?>


        <?php if($correspondencia_correspondencia->getNCorrespondenciaEmisor()!=$correspondencia_correspondencia->getId()) { ?>

        <?php if ($correspondencia_correspondencia->getFirmaPropiaLista()=='S') {
                if ($correspondencia_correspondencia->getStatus()!='E') { ?>
                    <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/quitarFirma'; ?>" style="text-decoration: none" title="Quitar Firma">
                    <?php echo image_tag('icon/tick_del.png'); ?>
                    </a>
        <?php } } elseif ($correspondencia_correspondencia->getFirmaPropiaLista()=='N') {
                    if(($correspondencia_correspondencia->getEmisores()>1) && (($correspondencia_correspondencia->getEmisores()-1) > $correspondencia_correspondencia->getFirmasListas())) {
                    $ban_send = 0; ?>


                    <?php
                    $sf_firmaElectronica = sfConfig::get('sf_firmaElectronica');
                    if($sf_firmaElectronica['correspondencia']['activo']==true && $sf_user->getAttribute('config_pkcsc')!=''){
                    ?>
                        <a href="#" onclick="prepare_signature(<?php echo $correspondencia_correspondencia->getId(); ?>,'formatos/prepararFirma','enviada/firmar'); return false;" style="text-decoration: none" title="Firmar (Firma Certificada)">
                        <?php echo image_tag('icon/tick_secure.png'); ?>
                        </a>
                    <?php } else { ?>
                        <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/firmar'; ?>" style="text-decoration: none" title="Firmar">
                        <?php echo image_tag('icon/tick.png'); ?>
                        </a>
                    <?php } ?>

            <?php } else {
                    $ban_send = 1;
                    if($nonfirm > 0) {
                        echo image_tag('icon/tick_send_inactive.png', array('style'=> 'cursor: pointer', 'title'=> 'En espera por Visto bueno'));
                    }else {
                        ?>

                        <?php
                        $sf_firmaElectronica = sfConfig::get('sf_firmaElectronica');
                        if($sf_firmaElectronica['correspondencia']['activo']==true && $sf_user->getAttribute('config_pkcsc')!=''){
                        ?>
                            <a href="#" onclick="prepare_signature(<?php echo $correspondencia_correspondencia->getId(); ?>,'formatos/prepararFirma','enviada/firmarEnviar'); return false;" style="text-decoration: none" title="Firmar y Enviar (Firma Certificada)">
                            <?php echo image_tag('icon/tick_secure_send.png'); ?>
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/firmarEnviar'; ?>" style="text-decoration: none" title="Firmar y Enviar">
                            <?php echo image_tag('icon/tick_send.png'); ?>
                            </a>
                        <?php } ?>

            <?php } } }
         // si no es si ni no, es que el funcionario no esta autorizado a firmar por ende no aparece el icono


        if ($correspondencia_correspondencia->getStatus()!='A' && $correspondencia_correspondencia->getStatus()!='L')
        {
            if ($correspondencia_correspondencia->getStatus()!='E')
            {
                if ($correspondencia_correspondencia->getStatus()!='F')
                {
                    if(count($correspondencia_correspondencia->getReceptores()>0) || count($receptores_organismo>0))
                    {
                        if($correspondencia_correspondencia->getEmisores()>0)
                        {
                            if(count($formato)>0)
                            {
                                if($ban_send == 0 || $ban_send == -1)
                                {
                                ?>
                                    <?php

                                    $ban_send = 0;
                                    if($sf_user->getAttribute('firmas_delegadas')){
                                        $firmas_delegadas = $sf_user->getAttribute('firmas_delegadas');

                                        $i=0; $firmas=array();
                                        foreach ($firmas_delegadas as $firma_delegada) {
                                            $firmas[$i]=$firma_delegada;
                                            $i++;
                                        }

                                        $firmates_delengan = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
                                                ->createQuery('a')
                                                ->where('a.correspondencia_id = ?',$correspondencia_correspondencia->getId())
                                                ->whereIn('a.funcionario_id',$firmas)
                                                ->orderBy('a.id')
                                                ->execute();

                                        foreach ($firmates_delengan as $firma_delegada) {
                                            $funcionario_delega = Doctrine::getTable('Funcionarios_Funcionario')->find($firma_delegada->getFuncionarioId());
                                            ?>

                                            <?php if($firma_delegada->getFirma()=='N') { $ban_send = 1; ?>
                                                <?php if(($correspondencia_correspondencia->getFirmasListas()+1)==$correspondencia_correspondencia->getEmisores()) {
                                                        if($nonfirm > 0) {
                                                            echo image_tag('icon/tick_send_other_inactive.png', array('style'=> 'cursor: pointer', 'title'=> 'En espera por Visto bueno'));
                                                        }else { ?>
                                                            <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/firmarEnviar?fd='.$funcionario_delega->getId(); ?>"
                                                            style="text-decoration: none" title="Firmar y Enviar por <?php echo $funcionario_delega->getPrimerNombre().' '.$funcionario_delega->getSegundoNombre().' '.
                                                                    $funcionario_delega->getPrimerApellido().' '.$funcionario_delega->getSegundoApellido(); ?>">
                                                                <?php echo image_tag('icon/tick_send_other.png'); ?>
                                                            </a>
                                                        <?php } ?>
                                                <?php } else { ?>
                                                    <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/firmar?fd='.$funcionario_delega->getId(); ?>"
                                                    style="text-decoration: none" title="Firmar por <?php echo $funcionario_delega->getPrimerNombre().' '.$funcionario_delega->getSegundoNombre().', '.
                                                            $funcionario_delega->getPrimerApellido().' '.$funcionario_delega->getSegundoApellido(); ?>">
                                                        <?php echo image_tag('icon/tick_other.png'); ?>
                                                    </a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/quitarFirma?fd='.$funcionario_delega->getId(); ?>"
                                                style="text-decoration: none" title="Quitar firma de <?php echo $funcionario_delega->getPrimerNombre().' '.$funcionario_delega->getSegundoNombre().' '.
                                                        $funcionario_delega->getPrimerApellido().' '.$funcionario_delega->getSegundoApellido(); ?>">
                                                    <?php echo image_tag('icon/tick_del_other.png'); ?>
                                                </a>
                                            <?php
                                            }
                                        }

                                    }

                                    if($ban_send == 0) { ?>
                                        <a style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/enviar'; ?>" title="Enviar">
                                            <?php echo image_tag('icon/send.png'); ?>
                                        </a>
                                    <?php } ?>
                                <?php
                                }
                            }
                        }
                    }
                }
            } else { ?>
                <a style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/pausar'; ?>" title="Pausar Envio">
                    <?php echo image_tag('icon/pause_hand.png'); ?>
                </a>
            <?php } }
            }
            else { ?>
                <a style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/generarCorrelativo'; ?>" title="Generar Correlativo" onclick="return confirm('¿Estas seguro de generar el correlativo en este momento?');">
                    <?php echo image_tag('icon/run.png'); ?>
                </a>
            <?php }
            }

        ?>
            <a style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/seguimiento'; ?>" title="Seguimiento">
                <?php echo image_tag('icon/goto.png'); ?>
            </a>
        <?php

        if ($correspondencia_correspondencia->getStatus()=='E' ||
            $correspondencia_correspondencia->getStatus()=='L') {

            //VERIFICAR SI ES DELEGADO Y ACTIVO O FIRMANTE LEGAL

            $firmates_delengan=array();
            if($sf_user->getAttribute('firmas_delegadas')){
                $firmas_delegadas = $sf_user->getAttribute('firmas_delegadas');

                $i=0; $firmas=array();
                foreach ($firmas_delegadas as $firma_delegada) {
                    $firmas[$i]=$firma_delegada;
                    $i++;
                }

                $firmates_delengan = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
                        ->createQuery('a')
                        ->where('a.correspondencia_id = ?',$correspondencia_correspondencia->getId())
                        ->whereIn('a.funcionario_id',$firmas)
                        ->orderBy('a.id')
                        ->execute();
            }

            if((count($firmates_delengan)>0) || $correspondencia_correspondencia->getFirmaPropiaLista()=='S'){
                if($parametros_tipo_formato['options_object']['email_externo']=='true'){
        ?>
                    <a style="text-decoration: none" href="#" onclick="javascript:open_form_copia_email(<?php echo $correspondencia_correspondencia->getId(); ?>); return false;" title="Enviar copia por email">
                        <?php echo image_tag('icon/arroba_send.png'); ?>
                    </a>
                    <div style="position: relative; width: 10px; height: 10px;" id="div_copia_email_<?php echo $correspondencia_correspondencia->getId(); ?>"></div>

        <?php } } }

        $fecha_actualizacion = strtotime('2011-06-27 12:00:00');
        $fecha_correspondencia = strtotime($correspondencia_correspondencia->getCreatedAt());

        if($fecha_actualizacion < $fecha_correspondencia){

            if ($correspondencia_correspondencia->getIdCreate()==$sf_user->getAttribute('usuario_id') &&
                  ($correspondencia_correspondencia->getStatus()=='C' ||
                  $correspondencia_correspondencia->getStatus()=='P') ||
                  $correspondencia_correspondencia->getFirmaPropiaLista()=='N') {

                if ($correspondencia_correspondencia->getFirmasListas()==0) {?>
                    <a style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/anular'; ?>" title="Anular" onclick="return confirm('<?php echo (($correspondencia_correspondencia->getIdCreate()!= $sf_user->getAttribute('usuario_id'))? 'Eliminará permanentemente el documento de otro usuario ¿Desea continuar?' : 'Eliminará permanentemente este documento ¿Desea continuar?'); ?>');" >
                        <?php echo image_tag('icon/delete.png'); ?>
                    </a>
        <?php }}} ?>


        <?php }} else { ?>
        <font class="f10n rojo">Error: no existe el tipo de documento.</font>
    <?php } ?>

    </div>
<?php } ?>

<br/><br/>

<div class="" style="position: relative;">
    <font class="f10n">Hecho por:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo $correspondencia_correspondencia->getUserUpdate(); ?><br/></font>
    <font class="f10n">Fecha:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($correspondencia_correspondencia->getCreatedAt())); ?><br/></font>
    <font class="f10n">Hora:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($correspondencia_correspondencia->getCreatedAt())); ?></font>
</div>

<?php 
    if($correspondencia_correspondencia->getStatus()=='X'){ 
        if($correspondencia_correspondencia->getIdDelete()!=''){ 
            $usuario_anulo= Doctrine::getTable('Acceso_Usuario')->find($correspondencia_correspondencia->getIdDelete());
?>
            <hr/>
            <div class="" style="position: relative;">
                <font class="f10n">Anulada por:</font><br/>
                <font class="f16n rojo">&nbsp;&nbsp;<?php echo $usuario_anulo->getNombre(); ?><br/></font>
                <font class="f10n">Fecha:</font><br/>
                <font class="f16n rojo">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($correspondencia_correspondencia->getUpdatedAt())); ?><br/></font>
                <font class="f10n">Hora:</font><br/>
                <font class="f16n rojo">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($correspondencia_correspondencia->getUpdatedAt())); ?></font>
            </div>

<?php }} ?>



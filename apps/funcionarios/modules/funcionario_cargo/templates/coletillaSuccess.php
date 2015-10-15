<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>

<div id="sf_admin_container">
    <?php
    if($from=='inicio'):
        echo '<h2>Edici&oacute;n de Mi Coletilla</h2>';
    else:
        echo '<h2>Coletilla del Funcionario en el cargo</h2>';
    endif; ?>
    
<div id="sf_admin_header"></div>
    <form action="<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo/saveColetilla" method="post">
        <fieldset id="sf_fieldset_none">

            <?php foreach( $funcionario_cargos as $funcionario_cargo ) {   ?>

                <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_coletilla">

                    <?php //include_partial('formatos/sessionFlashes', array('error_namen' => 'memorandum_contenido')) ?>

                    <div>
                        <label for="funcionarios_funcionario_cargo_observaciones">Coletilla para <?php echo $funcionario_cargo->getUnidad(); ?></label>
                        <div class="content" style="width: 650px;">
                            <!--<input id="funcionarios_funcionario_cargo_id" type="hidden" value="<?php echo $funcionario_cargo['id'];   ?>" name="funcionarios_funcionario_cargo[id]"/>-->
                            <textarea rows="4" cols="30" name="coletillas[<?php echo $funcionario_cargo->getId(); ?>]" id="coletilla_<?php echo $funcionario_cargo->getId().'_'.$semilla; ?>"><?php echo $funcionario_cargo->getColetilla(); ?></textarea>
                        </div>
                    </div>

                    <div class="help">Escriba de existir la coletilla de firma para el cargo de <?php echo $funcionario_cargo->getCargoTipo(); ?>.</div>

                </div>
                
            <?php } ?>
        </fieldset>

        <ul class="sf_admin_actions trans">
            <li class="sf_admin_action_list">
                <?php
                if($from=='inicio'):
                    echo link_to('Regresar al perfil', sfConfig::get('sf_app_funcionarios_url').'perfil');
                else:
                    echo link_to('Regresar al listado', sfConfig::get('sf_app_funcionarios_url').'funcionario_cargo');
                endif; ?>
            </li>
            <input type="hidden" value="<?php echo $from?>" name="from" />
            <li class="sf_admin_action_save">
                <input type="submit" value="Guardar">
            </li>
        </ul>
    </form>
</div>

<script type="text/javascript">
    CKEDITOR.config.scayt_autoStartup = true;
    CKEDITOR.config.scayt_sLang ="es_ES";
    <?php foreach( $funcionario_cargos as $funcionario_cargo ) { ?>
        CKEDITOR.replace('coletilla_<?php echo $funcionario_cargo->getId().'_'.$semilla; ?>');
    <?php } ?>
</script>





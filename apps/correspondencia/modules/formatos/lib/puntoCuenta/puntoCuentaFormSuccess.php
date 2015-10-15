<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_cuenta">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_cuenta_asunto')) ?>

    <div>
        <label for="punto_cuenta_asunto">Asunto</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][punto_cuenta_asunto]" id="punto_cuenta_asunto_<?php echo $semilla; ?>"><?php if(isset($formulario['punto_cuenta_asunto'])) echo $formulario['punto_cuenta_asunto']; ?></textarea>
        </div>
    </div>

    <div class="help">Asunto del punto de cuenta.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_cuenta">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_cuenta_sintesis')) ?>

    <div>
        <label for="punto_cuenta_sintesis">Sintesis</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][punto_cuenta_sintesis]" id="punto_cuenta_sintesis_<?php echo $semilla; ?>"><?php if(isset($formulario['punto_cuenta_sintesis'])) echo $formulario['punto_cuenta_sintesis']; ?></textarea>
        </div>
    </div>

    <div class="help">Sintesis del punto de cuenta.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_cuenta">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_cuenta_recomendaciones')) ?>

    <div>
        <label for="punto_cuenta_recomendaciones">Recomendaciones</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][punto_cuenta_recomendaciones]" id="punto_cuenta_recomendaciones_<?php echo $semilla; ?>"><?php if(isset($formulario['punto_cuenta_recomendaciones'])) echo $formulario['punto_cuenta_recomendaciones']; ?></textarea>
        </div>
    </div>

    <div class="help">Agregue las recomendaciones.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_cuenta">
    <script>
        function toogle_partida(){
            $('#punto_cuenta_partida_form').toggle();
            $('#punto_cuenta_partida_help').toggle();
        }
    </script>
    
    <div>
        <label for="punto_cuenta_recomendaciones">Partida presupuestaria</label>
        <div class="content" style="width: 650px;">
            ¿En este Punto de Cuenta solicita el uso de una partida presupuestaria?<br/>
            
            <?php 
                $checked_si = '';
                $checked_no = 'checked';        
                $display_partida = 'none';
                
                if(isset($formulario['punto_cuenta_form_partida'])){
                    if($formulario['punto_cuenta_form_partida']=='S'){
                        $checked_si = 'checked';
                        $checked_no = '';
                        
                        $display_partida = 'block';
                    }
                }
            ?>
            
            <input type="radio" name="correspondencia[formato][punto_cuenta_form_partida]" value="S" onclick="toogle_partida();" <?php echo $checked_si; ?>/> Si&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia[formato][punto_cuenta_form_partida]" value="N" onclick="toogle_partida();" <?php echo $checked_no; ?>/> No
            <br/><br/>
            <div id="punto_cuenta_partida_form" style="display: <?php echo $display_partida; ?>;">
                <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_cuenta_partida')) ?>
                <label for="punto_cuenta_recomendaciones">N° de Partida</label>
                <div class="content">
                    <input type="text" name="correspondencia[formato][punto_cuenta_partida]" value="<?php if(isset($formulario['punto_cuenta_partida'])) echo $formulario['punto_cuenta_partida']; ?>"/>
                </div>

                <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_cuenta_monto')) ?>
                <label for="punto_cuenta_recomendaciones">Monto Solicitado</label>
                <div class="content">
                    <input type="text" name="correspondencia[formato][punto_cuenta_monto]" value="<?php if(isset($formulario['punto_cuenta_monto'])) echo $formulario['punto_cuenta_monto']; ?>"/>
                </div>
            </div>
        </div>
    </div>

    <div class="help" id="punto_cuenta_partida_help" style="display: <?php echo $display_partida; ?>;">Escriba la partida presupuestaria que solicita y el monto a ejecutar</div>

</div>


<script type="text/javascript">
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.scayt_sLang ="es_ES";
        CKEDITOR.replace('punto_cuenta_asunto_<?php echo $semilla; ?>');
        CKEDITOR.replace('punto_cuenta_sintesis_<?php echo $semilla; ?>');
        CKEDITOR.replace('punto_cuenta_recomendaciones_<?php echo $semilla; ?>');
</script>

</fieldset>


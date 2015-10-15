<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'nombre_etiqueta')) ?>

    <div>
        <label for="nombre_etiqueta">Nombre de Etiqueta</label>
        <div class="content">
            <input type="" name="" value="" checked/>
        </div>
    </div>

    <div class="help">Ayuda.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_adjunto">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'cedula')) ?>

    <div>
        <label for="memorandum_adjunto">Cedula</label>
        <div class="content" style="width: 650px;">
            <?php 
                $w = new sfWidgetFormInputFile();
                echo $w->render('correspondencia[adjunto][seteado][cedula]');
            ?>
        </div>
    </div>

    <div class="help"></div>
</div>

</fieldset>
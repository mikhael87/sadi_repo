<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'memorandum_asunto')) ?>

    <div>
        <label for="servicios_generales_servicio">Servicio</label>
        <div class="content">
            
            
            <?php 
                $w = new sfWidgetFormDoctrineChoice(array(
                'model' => 'Extenciones_ServiciosGenerales', 
                'add_empty' => false,
                'order_by'=> array('nombre','asc')),
                array('name'=>'correspondencia[formato][servicios_generales_servicio]',));
                
                if(isset($formulario['servicios_generales_servicio']))
                    echo $w->render('correspondencia[formato][servicios_generales_servicio]',$formulario['servicios_generales_servicio']);
                else
                    echo $w->render('correspondencia[formato][servicios_generales_servicio]');
            ?>

        </div>
    </div>

    <div class="help">Seleccione el Servicio.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_contenido">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'servicios_generales_descripcion')) ?>

    <div>
        <label for="servicios_generales_descripcion">Descripción del Trabajo</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][servicios_generales_descripcion]" id="servicios_generales_descripcion"><?php if(isset($formulario['servicios_generales_descripcion'])) echo $formulario['servicios_generales_descripcion']; ?></textarea>
        </div>
    </div>

    <div class="help">Escriba una descripción detallada del servicio solicitado.</div>

</div>
</fieldset>
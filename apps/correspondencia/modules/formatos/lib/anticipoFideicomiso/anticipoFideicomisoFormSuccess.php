<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'anticipo_fideicomiso_motivo')) ?>

    <div>
        <label for="anticipo_fideicomiso_motivo">Motivo de la Solicitud</label>
        <div class="content">
            <input type="radio" name="correspondencia[formato][anticipo_fideicomiso_motivo]" value="Adquisición de Vivienda" checked/> Adquisición de Vivienda<br/>
            <input type="radio" name="correspondencia[formato][anticipo_fideicomiso_motivo]" value="Construcción de Vivienda" <?php if(isset($formulario['anticipo_fideicomiso_motivo'])) if($formulario['anticipo_fideicomiso_motivo'] == "Construcción de Vivienda") echo "checked"; ?>/> Construcción de Vivienda<br/>
            <input type="radio" name="correspondencia[formato][anticipo_fideicomiso_motivo]" value="Mejoras de Vivienda" <?php if(isset($formulario['anticipo_fideicomiso_motivo'])) if($formulario['anticipo_fideicomiso_motivo'] == "Mejoras de Vivienda") echo "checked"; ?>/> Mejoras de Vivienda<br/>
            <input type="radio" name="correspondencia[formato][anticipo_fideicomiso_motivo]" value="Liberación de Hipoteca" <?php if(isset($formulario['anticipo_fideicomiso_motivo'])) if($formulario['anticipo_fideicomiso_motivo'] == "Liberación de Hipoteca") echo "checked"; ?>/> Liberación de Hipoteca<br/>
            <input type="radio" name="correspondencia[formato][anticipo_fideicomiso_motivo]" value="Pagos Pensiones Escolares" <?php if(isset($formulario['anticipo_fideicomiso_motivo'])) if($formulario['anticipo_fideicomiso_motivo'] == "Pagos Pensiones Escolares") echo "checked"; ?>/> Pagos Pensiones Escolares<br/>
            <input type="radio" name="correspondencia[formato][anticipo_fideicomiso_motivo]" value="Gastos de Atención Médica y Hospitalaria" <?php if(isset($formulario['anticipo_fideicomiso_motivo'])) if($formulario['anticipo_fideicomiso_motivo'] == "Gastos de Atención Médica y Hospitalaria") echo "checked"; ?>/> Gastos de Atención Médica y Hospitalaria<br/>
        </div>
    </div>

    <div class="help">Seleccione un Motivo.</div>

</div>

</fieldset>
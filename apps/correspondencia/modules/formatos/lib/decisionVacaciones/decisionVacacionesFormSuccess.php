<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'decisionVacaciones_resultado')) ?>

    <div>
        <label for="memorandum_asunto">Resultado</label>
        <div class="content">
            <input type="radio" name="correspondencia[formato][decisionVacaciones_resultado]" value="Aprobadas" checked/> Aprobadas&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia[formato][decisionVacaciones_resultado]" value="Negadas"/> Negadas
        </div>
    </div>

    <div class="help">Seleccione el resultado para esta solicitud de vacaciones.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_contenido">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'decisionVacaciones_observaciones')) ?>

    <div>
        <label for="memorandum_contenido">Contenido</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][decisionVacaciones_observaciones]" id="decisionVacaciones_observaciones"><?php if(isset($formulario['decisionVacaciones_observaciones'])) echo $formulario['decisionVacaciones_observaciones']; ?></textarea>
        </div>
    </div>

    <div class="help">Agregue un comentario o instrucción particular de ser necesario.</div>

</div>

</fieldset>
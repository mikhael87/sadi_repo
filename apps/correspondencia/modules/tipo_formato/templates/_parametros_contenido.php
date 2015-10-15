    <div><?php include_partial('tipo_formato/unidades_emisoras',array('unidades_emisoras'=>'xxxx')); ?></div>
    <div><?php include_partial('tipo_formato/funcionarios_emisores',array('funcionarios_emisores'=>'xxxx')); ?></div>
</fieldset>
<fieldset id="sf_fieldset_parametros_receptores">
    <h2>Parametros de receptores</h2>
    <div><?php include_partial('tipo_formato/organismos_receptores',array('organismos_receptores'=>'xxxx')); ?></div>
    <div><?php include_partial('tipo_formato/unidades_receptoras',array('unidades_receptoras'=>'xxxx')); ?></div>
    <div><?php include_partial('tipo_formato/funcionarios_receptores',array('funcionarios_receptores'=>'xxxx')); ?></div>
</fieldset>
<fieldset id="sf_fieldset_parametros_create">
    <h2>Parametros al crear la correspondencia</h2>
    <div><?php include_partial('tipo_formato/options_create',array('options_create'=>'xxxx')); ?></div>
</fieldset>
<fieldset id="sf_fieldset_parametros_object">
    <h2>Parametros antes, mientras y despues de firmar la correspondencia</h2>
    <div><?php include_partial('tipo_formato/options_object',array('options_object'=>'xxxx', 'classe'=>$form["classe"]->getValue())); ?></div>
</fieldset>
<fieldset id="sf_fieldset_parametros_formulario">
    <h2>Parametros para generar la plantilla del formulario</h2>
    <div><?php include_partial('tipo_formato/plantilla_formulario',array('plantilla_formulario'=>'xxxx')); ?></div>
</fieldset>
<fieldset id="sf_fieldset_parametros_librerias">
    <h2>Acciones adicionales mediante librerias</h2>
    <div><?php include_partial('tipo_formato/additional_actions',array('additional_actions'=>'xxxx')); ?></div>
</fieldset>
<fieldset id="sf_fieldset_parametros_hijos">
    <h2>Formatos dependientes de este formato</h2>
    <div><?php include_partial('tipo_formato/formatos_hijos',array('formatos_hijos'=>'xxxx')); ?></div>
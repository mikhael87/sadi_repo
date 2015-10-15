<?php use_helper('jQuery'); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div>
        <label for="archivo">Archivo Excel</label>
        <div class="content">
            <input name="archivo" type="file" id="archivo" onClick="javascript: fn_conExcel();"/>&nbsp;<?php echo image_tag('icon/info', array('class'=> 'tooltip', 'title'=> '[!]Formato de Hoja Excel[/!]Asegurese que el archivo tenga extensión .xls o .xlsx ::Debe contener solo dos (2) columnas, telefono y nombre, :: sin encabezado u otros datos. Puede omitir nombres :: <font style="color: #f8a226">*El tiempo de procesamiento est&aacute; sujeto la la cantidad:: de sms que se env&iacute;en simultaneamente.</font>'));?>
        </div>
        <div class="help">El archivo Excel solo debe contener dos columnas; telefonos y nombres de derecha a izquierda.</div>
    </div>
</div>

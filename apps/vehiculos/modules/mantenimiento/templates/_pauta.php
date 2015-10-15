<script>
    function hab_fecha() {
        $('#div_fecha').show();
        $('#div_km').hide();
    }
    
    function hab_km() {
        $('#div_fecha').hide();
        $('#div_km').show();
    }
</script>
<input type='hidden' name='vehiculos_mantenimiento[vehiculo_id]' value='<?php echo $sf_user->getAttribute('vehiculo_id') ?>'>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_pauta">
    <div>
        <label for="vehiculos_mantenimiento_tipo">Recordarme por:</label>
        <div class="content">
            <input type='radio' onChange='hab_fecha()' checked name='pauta' value='fecha_type'/>&nbsp;Fecha&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='pauta' onChange='hab_km()' value='kilometro_type'/>&nbsp;Kilometraje
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_kilometraje" id='div_km' style='display: none'>
    <div>
        <label for="vehiculos_mantenimiento_kilometraje">Kilometraje</label>
        <div class="content">
            <input id="vehiculos_mantenimiento_kilometraje" type="text" name="vehiculos_mantenimiento[kilometraje]">
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_date sf_admin_form_field_fecha" id='div_fecha' style='display: block'>
    <div>
        <label for="vehiculos_mantenimiento_fecha">Fecha:</label>
        <div class="content">
            <?php echo $form['fecha'] ?>
        </div>
    </div>
</div>

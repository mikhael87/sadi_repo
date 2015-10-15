<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_receptor_persona_cargo">
    <div>
        <label for="correspondencia_correspondencia_receptor_persona_cargo">Nº Envio</label>
        <div class="content">
            <input name="correspondencia_correspondencia[n_correspondencia_emisor]" id="correspondencia_correspondencia_n_correspondencia_emisor" 
                   value="<?php echo $sf_user->getAttribute('correspondencia_n_emisor'); ?>"
                   type="text">
        </div>
        <div class="help">Seleccione el correlativo que utilizara para esta correspondencia</div>
    </div>
</div>
<script type="text/javascript">
        $("#correspondencia_correspondencia_n_correspondencia_emisor").attr('disabled','disabled');
</script>

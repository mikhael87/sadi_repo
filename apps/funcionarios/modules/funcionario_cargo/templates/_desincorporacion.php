<script>
    $(document).ready(function(){
        $("form").submit(function() {
            if ($("#funcionarios_funcionario_cargo_f_retiro_jquery_control").val() == "") {
                $("#error_f_retiro").show();
            } else {
                $("#error_f_retiro").hide();
            }
            
            if ($("#funcionarios_funcionario_cargo_motivo_retiro").val() == "") {
                $("#error_motivo_retiro").show();
            } else {
                $("#error_motivo_retiro").hide();
            }   
            
            if($("#funcionarios_funcionario_cargo_f_retiro_jquery_control").val() == "" ||
               $("#funcionarios_funcionario_cargo_motivo_retiro").val() == ""){ 
                return false;
            } else  {
                return true;
            }
        });
    });
</script>

<div class="sf_admin_form_row sf_admin_date sf_admin_form_field_f_retiro">
    <div class="error" id="error_f_retiro" style="display: none;">Seleccione la fecha de desincorporación.</div>
    <div>
        <label for="funcionarios_funcionario_cargo_f_retiro">Fecha de desincorporación</label>
        <div class="content">
            <?php echo $form['f_retiro'] ?>
        </div>            
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_motivo_retiro">
    <div class="error" id="error_motivo_retiro" style="display: none;">Seleccione un motivo de desincorporación.</div>
    <div>
        <label for="funcionarios_funcionario_cargo_motivo_retiro">Motivo de desincorporación</label>
        <div class="content">
            <?php echo $form['motivo_retiro'] ?>
        </div>

        <div class="help">Seleccione un motivo de desincorporación en el cargo</div>
    </div>
</div>
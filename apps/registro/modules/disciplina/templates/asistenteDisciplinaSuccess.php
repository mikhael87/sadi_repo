<script>
    function guardar_disciplina(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>disciplina/saveDisciplina',
            type:'POST',
            dataType:'html',
            data: $('#form_disciplina').serialize(),
            beforeSend: function(Obj){
                $('#save_disciplina_msj').html('<?php echo image_tag('icon/cargando.gif', array('style'=>'width: 16px; vertical-align: middle')); ?> Guardando disciplinas...');
            },
            success:function(data, textStatus){
                document.location = "<?php echo sfConfig::get('sf_app_registro_url').'empresa/index'; ?>";
            }});
    }
    
    function actualizar_master_check() {
        $("input[name='registro_disciplina[]']").each(function() {
            if(this.checked) {
                $(this).parent().parent().children('input').val('T');
            }
        });
        
        $("input[name='master_check']").each(function() {
            if(this.value !== 'T' || this.value === '') {
                $(this).val('F');
            }
        });
    }
    
    function contar_disciplinas() {
        actualizar_master_check();
    
        seleccionados= '0';
        $("input[name='master_check']").each(function() {
            if(this.value === 'T') {
                seleccionados++;
            }
        });
    
        var add_text= '';
        var color= 'green';
        if(seleccionados > '9') {
            color= 'red';
            add_text= '&nbsp;&nbsp;<font style="font-size: 15px; color: #ff8585">Disculpe, puede escojer hasta un máximo de 9 disciplinas.</font>';
            
            $('#guardar_disciplina_add').attr('disabled', 'disabled');
        }else {
            $('#guardar_disciplina_add').attr('disabled', false);
        }
        
        var content= '<font style="font-size: 25px; font-weight: bold; color:'+ color +'">'+ seleccionados +'</font>';
        $('#discount').html('<font style="font-size: 20px; color: #666">Seleccionados: </font>'+ content +add_text);
    }
    
    function check_obligatoria() {
        if(!$('#maraton_femenino').is(':checked') && !$('#maraton_masculino').is(':checked')) {
            $('#maraton_femenino').attr('checked', true);
        }
        contar_disciplinas();
    }
    
    $(document).ready(function(){
        contar_disciplinas();
        check_obligatoria();
        
        $(".disciplina_in").click(function() {
            if($(this).parent().children("input[name='registro_disciplina[]']:checked").length > '0') {
                $(this).parent().parent().children('input').val('T');
            }else {
                $(this).parent().parent().children('input').val('F');
            }
            
            contar_disciplinas();
        });
        
        $('#maraton_femenino').click(function() {
            if(!$(this).is(':checked')) {
                $('#maraton_masculino').attr('checked', true);
                contar_disciplinas();
            }
        });
        
        $('#maraton_masculino').click(function() {
            if(!$(this).is(':checked')) {
                $('#maraton_femenino').attr('checked', true);
                contar_disciplinas();
            }
        });
    });
</script>

<style>
    #sf_admin_container label {
        width: 15em !important
    }
</style>
<?php if ($sf_user->hasFlash('notice')): ?>
    <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
<?php endif; ?> 
    
<?php if ($sf_user->hasFlash('error')): ?>
    <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>
  
<form id="form_disciplina">
    <fieldset>
        <h2>Disciplinas</h2>
        <div id="main_disciplina_div">
            <?php foreach($disciplinas as $disciplina) : ?>
                <div class="sf_admin_form_row disciplina_row">
                    <div>
                        <label><font style="font-size: 15px; font-weight: bold"><?php echo $disciplina->getNombre(); ?></font></label>
                        <input type="hidden" class="master_check" name="master_check" id="master_check_<?php echo $disciplina->getId(); ?>" />
                        <div class="content">
                            <?php 
                            $variantes= Doctrine::getTable('Operaciones_DisciplinaVariante')->variantesPorDisciplina($disciplina->getId());
                            foreach($variantes as $variante) : ?>
                            <input class='disciplina_in' id="<?php echo strtolower($disciplina->getNombre()).'_'.  strtolower($variante->getNombre()); ?>" <?php echo ((strtolower($disciplina->getNombre()).'_'.  strtolower($variante->getNombre())== 'maraton_femenino')? 'checked':'') ?> name="registro_disciplina[]"type="checkbox" value="<?php echo $variante->getDvi() ?>" />&nbsp;<?php echo $variante->getNombre(); ?>&nbsp;&nbsp;&nbsp;
                            <?php endforeach; ?>
                        </div>
                        <?php if(strtolower($disciplina->getNombre()) == 'maraton') : ?>
                        <div class="help">Es obligatorio la inscripci&oacute;n de esta disciplina en cualquiera de sus modalidades</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="sf_admin_form_row">
                <div>
                    <label></label>
                    <div class="content">
                        <div id="discount"></div>
                        <input type='hidden' name='operaciones_registro[id]' value='<?php echo $registro_id ?>'/>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <div class='to_hid_disciplina' style='display: block'>
        <fieldset>
            <div class="sf_admin_form_row">
                <button id="guardar_disciplina_add" style="height: 35px" onClick='guardar_disciplina(); return false;'>
                    <strong>Guardar y finalizar</strong>&nbsp;<?php echo image_tag('icon/filesave.png', array('style' => 'vertical-align: middle')) ?>
                </button>&nbsp;&nbsp;
                <x id="save_disciplina_msj"></x>
            </div>
        </fieldset>
    </div>
</form>
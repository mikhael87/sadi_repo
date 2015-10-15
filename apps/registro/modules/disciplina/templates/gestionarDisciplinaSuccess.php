<?php use_helper('jQuery'); ?>

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

<div id="sf_admin_container">
  <h1>
      Disciplinas Inscritas
  </h1>

<?php if ($sf_user->hasFlash('notice')): ?>
    <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
<?php endif; ?> 

<?php if ($sf_user->hasFlash('error')): ?>
    <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>
    <div id="sf_admin_header"></div>

    <div id="sf_admin_content">
        <div class="sf_admin_form">
            <form id="form_disciplina">
                <fieldset>
                    <h2>Marque o desmarque para inscribir</h2>
                    <div id="main_disciplina_div">
                        <?php foreach($disciplinas as $disciplina) : ?>
                            <div class="sf_admin_form_row disciplina_row">
                                <div>
                                    <label><font style="font-size: 15px; font-weight: bold"><?php echo $disciplina->getNombre(); ?></font></label>
                                    <input type="hidden" class="master_check" name="master_check" id="master_check_<?php echo $disciplina->getId(); ?>" />
                                    <div class="content">
                                        <?php 
                                        $variantes= Doctrine::getTable('Operaciones_DisciplinaVariante')->variantesPorDisciplina($disciplina->getId());
                                        $cadena= '';
                                        foreach($variantes as $variante) : 
                                            $cadena .= '<input class="disciplina_in" id="'.strtolower($disciplina->getNombre()).'_'.strtolower($variante->getNombre()).'"';
                                            
                                            //BUSCA SI LA DISCIPLINA YA ESTA INSCRITA
                                            $findit= FALSE; $blocked= FALSE;
                                            foreach($disciplinas_inscritas as $value) {
                                                if($variante->getDvi() == $value->getDisciplinaVarianteId()) {
                                                    $findit= TRUE;
                                                    $nomina= Doctrine::getTable('Operaciones_RegistroPersona')->cantidadPorRegistroDisciplina($value->getId());
                                                    //SI YA ESTA INSCRITA BUSCA SI HAY PARTICIPANTES INSCRITOS, EN CUYO CASO NO PODRA EDITAR DISCIPLINA
                                                    if($nomina[0][0] > 0) {
                                                        $blocked= TRUE;
                                                    }
                                                }
                                            }
                                            if($findit) {
                                                $cadena .= ' checked ';
                                            }
                                            if($blocked) {
                                                $cadena .= ' disabled ';
                                                $cadena .= ' name="registro_disciplina[]" type="checkbox" />&nbsp;'.$variante->getNombre().'&nbsp;&nbsp;&nbsp;';
                                                $cadena .= '<input name="registro_disciplina[]" type="hidden" value="'.$variante->getDvi().'" />';
                                            }else {
                                                $cadena .= ' name="registro_disciplina[]" type="checkbox" value="'.$variante->getDvi().'" />&nbsp;'.$variante->getNombre().'&nbsp;&nbsp;&nbsp;';
                                            }
                                        endforeach; echo $cadena; ?>
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
                                <strong>Guardar cambios</strong>&nbsp;<?php echo image_tag('icon/filesave.png', array('style' => 'vertical-align: middle')) ?>
                            </button>&nbsp;&nbsp;
                            <x id="save_disciplina_msj"></x>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
  </div>

  <div id="sf_admin_footer">
    <ul class="sf_admin_actions trans">
        <li class="sf_admin_action_regresar_modulo">
            <a href="<?php echo sfConfig::get('sf_app_registro_url'); ?>empresa">Regresar</a>
        </li>
    </ul>
  </div>
</div>

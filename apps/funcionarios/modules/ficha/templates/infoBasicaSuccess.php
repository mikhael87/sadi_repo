<script>
    function saveInfoBasica() {

      mensajes="";
      
      
      if ((document.getElementById('funcionarios_informacion_basica_basica_f_nacimiento_day').value=='') || 
          (document.getElementById('funcionarios_informacion_basica_basica_f_nacimiento_month').value=='') ||
          (document.getElementById('funcionarios_informacion_basica_basica_f_nacimiento_year').value=='')){
          
          mensajes += "- Fecha de nacimiento \n";
          
      }
      if (document.getElementById('funcionarios_informacion_basica_basica_sexo').value==''){
          mensajes += "- Sexo \n";
      }
      if (document.getElementById('funcionarios_informacion_basica_basica_estado_civil').value==''){
          mensajes += "- Estado civil \n";
      }
      
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoBasica',
            type:'POST',
            dataType:'html',
            data:'&dia='+$("#funcionarios_informacion_basica_basica_f_nacimiento_day").val()+
                '&mes='+$("#funcionarios_informacion_basica_basica_f_nacimiento_month").val()+
                '&ano='+$("#funcionarios_informacion_basica_basica_f_nacimiento_year").val()+
                '&estado_nacimiento_id='+$("#funcionarios_informacion_basica_basica_estado_id").val()+
                '&sexo='+$("#funcionarios_informacion_basica_basica_sexo").val()+
                '&edo_civil='+$("#funcionarios_informacion_basica_basica_estado_civil").val()+
                '&licencia_conducir_uno_grado='+$("#funcionarios_informacion_basica_lincencia_uno").val()+
                '&licencia_conducir_dos_grado='+$("#funcionarios_informacion_basica_lincencia_dos").val(),
            success:function(data, textStatus){
                $('#ficha_info_basica_content').html(data);
                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };
     

        
</script>
<form>
<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Basica</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Fecha Nacimiento</label>
                    <div class="content">
                        
                        <?php echo $form['basica_f_nacimiento'] ?>                        
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Estado nacimiento</label>
                    <div class="content">                       
                        <?php echo $form['basica_estado_id'] ?>
                    </div>
                </div>
            </div>
            
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Sexo</label>
                    <div class="content">
                        
                        <?php echo $form['basica_sexo'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Estado Civil</label>
                    <div class="content">
                        <?php echo $form['basica_estado_civil'] ?>
                        
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Grado licencia de conducir</label>
                    <div class="content">
                        <?php echo $form['lincencia_uno'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Otra Licencia de conducir (grado)</label>
                    <div class="content">
                        <?php echo $form['lincencia_dos'] ?>
                    </div>
                </div>
            </div>
            
        </fieldset> 

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoBasica(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>
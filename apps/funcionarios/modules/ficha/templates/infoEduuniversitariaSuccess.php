<script>
    function saveInfoEduuniversitaria() {

      mensajes="";
         
      if (document.getElementById('funcionarios_educacion_universitaria_pais_id').value==''){
          mensajes += "- Pais \n";
      }
      if (document.getElementById('funcionarios_educacion_universitaria_nivel_academico_id').value==''){
          mensajes += "- Nivel Academico \n";
      }
      if ((document.getElementById('funcionarios_educacion_universitaria_f_ingreso_day').value=='') || 
          (document.getElementById('funcionarios_educacion_universitaria_f_ingreso_month').value=='') ||
          (document.getElementById('funcionarios_educacion_universitaria_f_ingreso_year').value=='')){
          
          mensajes += "- Fecha de ingreso \n";
          
      }              
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoEduuniversitaria',
            type:'POST',
            dataType:'html',
            data:'&pais='+$("#funcionarios_educacion_universitaria_pais_id").val()+
                '&organismo='+$("#funcionarios_educacion_universitaria_organismo_educativo_id").val()+    
                '&carrera='+$("#funcionarios_educacion_universitaria_carrera_id").val()+
                '&nivel='+$("#funcionarios_educacion_universitaria_nivel_academico_id").val()+
                '&diai='+$("#funcionarios_educacion_universitaria_f_ingreso_day").val()+
                '&mesi='+$("#funcionarios_educacion_universitaria_f_ingreso_month").val()+
                '&anoi='+$("#funcionarios_educacion_universitaria_f_ingreso_year").val()+
                '&diag='+$("#funcionarios_educacion_universitaria_f_graduado_day").val()+
                '&mesg='+$("#funcionarios_educacion_universitaria_f_graduado_month").val()+
                '&anog='+$("#funcionarios_educacion_universitaria_f_graduado_year").val()+
                '&estudia='+$("#funcionarios_educacion_universitaria_estudiando_actualmente").val()+
                '&segmento='+$("#funcionarios_educacion_universitaria_segmento").val(),
            success:function(data, textStatus){
                $('#ficha_info_academica_content').html(data);
                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };
     

        
</script>

<?php 
    if (sfContext::getInstance()->getUser()->getAttribute('eduuniversitaria_accion')=='editar'){
        if ($eduuniversitaria->getProteccion()==""){
            $pais       = $eduuniversitaria->getPaisId(); 
            $organismo  = $eduuniversitaria->getOrganismoEducativoId();
            $carrera    = $eduuniversitaria->getCarreraId();
            $nivel      = $eduuniversitaria->getNivelAcademicoId();
            $fingreso   = $eduuniversitaria->getFIngreso();
            $fgraduado  = $eduuniversitaria->getFGraduado();
            $estudia    = $eduuniversitaria->getEstudiandoActualmente();
            $segmento   = $eduuniversitaria->getSegmento();
        }else{ ?>
        <div id="sf_admin_container">
             <div class="error">La información fue procesada por el departamento de RRHH. No puede modificarla.</div>
        </div>            
         <?php
            exit();
        }
       
    } elseif(sfContext::getInstance()->getUser()->getAttribute('eduuniversitaria_accion')=='nuevo'){
        $pais       = "";
        $organismo  = "";
        $carrera    = "";
        $nivel      = "";
        $fingreso   = "";
        $fgraduado  = "";
        $estudia    = "";
        $segmento   = "";
    }
        
 ?>
<form>
<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Educacion Universitaria</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Pais</label>
                    <div class="content">
                        <?php echo $form['pais_id']; ?>                                                   
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Organismo educativo</label>
                    <div class="content">
                        <?php echo $form['organismo_educativo_id']; ?>                                                   
                    </div>
                </div>
            </div>    
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Carrera</label>
                    <div class="content">
                        <?php echo $form['carrera_id']; ?>                                                   
                    </div>
                </div>
            </div>  
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Nivel Academico</label>
                    <div class="content">
                        <?php echo $form['nivel_academico_id']; ?>                                                   
                    </div>
                </div>
            </div>     
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Fecha Ingreso</label>
                    <div class="content">
                        <?php echo $form['f_ingreso']; ?>                                                   
                    </div>
                </div>
            </div> 
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Fecha Graduado</label>
                    <div class="content">
                        <?php echo $form['f_graduado']; ?>                                                   
                    </div>
                </div>
            </div> 
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Estudia actualmente</label>
                    <div class="content">
                        <?php echo $form['estudiando_actualmente']; ?>                                                   
                    </div>
                </div>
            </div> 
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Segmento</label>
                    <div class="content">
                        <?php echo $form['segmento']; ?>                                                   
                    </div>
                </div>
            </div> 
                  
        </fieldset> 

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoEduuniversitaria(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>
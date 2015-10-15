<script>
    function saveInfoFamiliar() {

      mensajes="";
         
      if (document.getElementById('funcionarios_familiar_parentesco_id').value==''){
          mensajes += "- Parentesco \n";
      }
      if (document.getElementById('familiar_primernombre').value==''){
          mensajes += "- Primer Nombre \n";
      }
      if (document.getElementById('familiar_primerapellido').value==''){
          mensajes += "- Primer Apellido \n";
      }      
      if ((document.getElementById('funcionarios_familiar_familiar_f_nacimiento_day').value=='') || 
          (document.getElementById('funcionarios_familiar_familiar_f_nacimiento_month').value=='') ||
          (document.getElementById('funcionarios_familiar_familiar_f_nacimiento_year').value=='')){
          
          mensajes += "- Fecha de nacimiento \n";
          
      }
      if (document.getElementById('funcionarios_familiar_familiar_sexo').value==''){
          mensajes += "- Sexo \n";
      }
      if (document.getElementById('funcionarios_familiar_nivel_academico_id').value==''){
          mensajes += "- Nivel Academico \n";
      }
      if (document.getElementById('funcionarios_familiar_familiar_estudia').value==''){         
          mensajes += "- Estudia \n";
      }   
      
      if (document.getElementById('funcionarios_familiar_familiar_trabaja').value==''){
          mensajes += "- Trabaja \n";
      }
      if (document.getElementById('funcionarios_familiar_familiar_dependencia').value==''){ 
          mensajes += "- Dependencia \n";
      }  
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoFamiliar',
            type:'POST',
            dataType:'html',
            data:'&cedula='+$("#familiar_cedula").val()+
                '&parentesco='+$("#funcionarios_familiar_parentesco_id").val()+
                '&primernombre='+$("#familiar_primernombre").val()+
                '&segundonombre='+$("#familiar_segundonombre").val()+
                '&primerapellido='+$("#familiar_primerapellido").val()+
                '&segundoapellido='+$("#familiar_segundoapellido").val()+
                '&dia='+$("#funcionarios_familiar_familiar_f_nacimiento_day").val()+
                '&mes='+$("#funcionarios_familiar_familiar_f_nacimiento_month").val()+
                '&ano='+$("#funcionarios_familiar_familiar_f_nacimiento_year").val()+
                '&nacionalidad='+$("#funcionarios_familiar_familiar_nacionalidad").val()+
                '&sexo='+$("#funcionarios_familiar_familiar_sexo").val()+
                '&nivel='+$("#funcionarios_familiar_nivel_academico_id").val()+
                '&estudia='+$("#funcionarios_familiar_familiar_estudia").val()+
                '&trabaja='+$("#funcionarios_familiar_familiar_trabaja").val()+
                '&dependencia='+$("#funcionarios_familiar_familiar_dependencia").val(),
            success:function(data, textStatus){
                $('#ficha_info_familiar_content').html(data);
                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };
     

        
</script>

<?php 
    if (sfContext::getInstance()->getUser()->getAttribute('familiar_accion')=='editar'){
      if ($familiar->getProteccion()==""){
        $cedula         = $familiar->getCi(); 
        $parentesco     = $familiar->getParentescoId();
        $primernombre   = $familiar->getPrimerNombre();
        $segundonombre  = $familiar->getSegundoNombre();
        $primerapellido = $familiar->getPrimerApellido();
        $segundoapellido= $familiar->getSegundoApellido();
        $f_nacimiento   = $familiar->getFNacimiento();
        $nacionalidad   = $familiar->getNacionalidad();
        $sexo           = $familiar->getSexo();
        $nivel          = $familiar->getNivelAcademicoId();
        $estudia        = $familiar->getEstudia();
        $trabaja        = $familiar->getTrabaja();
        $dependencia    = $familiar->getDependencia();
      }else{ ?>
        <div id="sf_admin_container">
             <div class="error">La información fue procesada por el departamento de RRHH. No puede modificarla.</div>
        </div>            
         <?php
            exit();
      }
       
    } elseif(sfContext::getInstance()->getUser()->getAttribute('familiar_accion')=='nuevo'){
        $cedula         = "";
        $parentesco     = "";
        $primernombre   = "";
        $segundonombre  = "";
        $primerapellido = "";
        $segundoapellido= "";
        $f_nacimiento   = "";
        $nacionalidad   = "";
        $sexo           = "";
        $nivel          = "";
        $estudia        = "";
        $trabaja        = "";
        $dependencia    = "";
        
      
    }
        
 ?>
<form>
<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Familiar</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Parentesco</label>
                    <div class="content">
                        <?php echo $form['parentesco_id']; ?>    
                                               
                    </div>
                </div>
            </div>
                    
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Cedula</label>
                    <div class="content">
                       <?php echo $form['familiar_nacionalidad'] ?> -
                       <input id="familiar_cedula" class="valores_ficha" type="text" name="familiar_cedula" size="10" maxlength="8" onkeydown="return soloNumeros(event)"  value="<?php echo $cedula ?>"/>                        
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Primer Nombre</label>
                    <div class="content">
                       <input id="familiar_primernombre" class="valores_ficha" type="text" name="familiar_primernombre" size="20"  value="<?php echo $primernombre ?>"/>                        
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Segundo Nombre</label>
                    <div class="content">
                       <input id="familiar_segundonombre" class="valores_ficha" type="text" name="familiar_segundonombre" size="20"  value="<?php echo $segundonombre ?>"/>                        
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Primer Apellido</label>
                    <div class="content">
                       <input id="familiar_primerapellido" class="valores_ficha" type="text" name="familiar_primerapellido" size="20"  value="<?php echo $primerapellido ?>"/>                        
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Segundo Apellido</label>
                    <div class="content">
                       <input id="familiar_segundoapellido" class="valores_ficha" type="text" name="familiar_segundoapellido" size="20"  value="<?php echo $segundoapellido ?>"/>                        
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Fecha Nacimiento</label>
                    <div class="content">
                       <?php echo $form['familiar_f_nacimiento'] ?> 
                    </div>
                </div>
            </div>
   
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Sexo</label>
                    <div class="content">
                       <?php echo $form['familiar_sexo'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Nivel Academico</label>
                    <div class="content">
                        <?php echo $form['nivel_academico_id'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Estudia</label>
                    <div class="content">
                       <?php echo $form['familiar_estudia'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Trabaja</label>
                    <div class="content">
                       <?php echo $form['familiar_trabaja'] ?>
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Dependencia</label>
                    <div class="content">
                       <?php echo $form['familiar_dependencia'] ?>
                    </div>
                </div>
            </div>
        </fieldset> 

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoFamiliar(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>
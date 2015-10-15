<link href="css/default.css" media="screen" type="text/css" rel="stylesheet">
<script>
    function saveInfoEduadicional() {

      mensajes="";
         
      if (document.getElementById('funcionarios_educacion_adicional_pais_id').value==''){
          mensajes += "- Pais \n";
      }
      if (document.getElementById('funcionarios_educacion_adicional_tipo_educacion_adicional_id').value==''){
          mensajes += "- Tipo \n";
      }
      if ((document.getElementById('funcionarios_educacion_adicional_f_ingreso_day').value=='') || 
          (document.getElementById('funcionarios_educacion_adicional_f_ingreso_month').value=='') ||
          (document.getElementById('funcionarios_educacion_adicional_f_ingreso_year').value=='')){
          
          mensajes += "- Fecha de ingreso \n";
          
      }              
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoEduadicional',
            type:'POST',
            dataType:'html',
            data:'&pais='+$("#funcionarios_educacion_adicional_pais_id").val()+
                '&organismo='+$("#funcionarios_educacion_adicional_organismo_educativo_id").val()+    
                '&nombre='+$("#funcionarios_educacion_adicional_nombre").val()+
                '&tipo='+$("#funcionarios_educacion_adicional_tipo_educacion_adicional_id").val()+
                '&diai='+$("#funcionarios_educacion_adicional_f_ingreso_day").val()+
                '&mesi='+$("#funcionarios_educacion_adicional_f_ingreso_month").val()+
                '&anoi='+$("#funcionarios_educacion_adicional_f_ingreso_year").val()+
                '&horas='+$("#horas").val(),
                
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
    if (sfContext::getInstance()->getUser()->getAttribute('eduadicional_accion')=='editar'){
        if ($eduadicional->getProteccion()==""){
            $pais       = $eduadicional->getPaisId(); 
            $organismo  = $eduadicional->getOrganismoEducativoId();
            $nombre    = $eduadicional->getNombre();
            $tipo      = $eduadicional->getTipoEducacionAdicionalId();
            $fingreso   = $eduadicional->getFIngreso();
            $horas    = $eduadicional->getHoras();  
        }else{ ?>
        <div id="sf_admin_container">
             <div class="error">La información fue procesada por el departamento de RRHH. No puede modificarla.</div>
        </div>            
         <?php
            exit();
        }
       
    } elseif(sfContext::getInstance()->getUser()->getAttribute('eduadicional_accion')=='nuevo'){
        $pais       = "";
        $organismo  = "";
        $nombre    = "";
        $tipo      = "";
        $fingreso   = "";
        $horas    = "";
 
    }
        
 ?>
<form>
<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Educacion Adicional</h2>
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
                    <label>Nombre</label>
                    <div class="content">                        
                        <input id="funcionarios_educacion_adicional_nombre" class="valores_ficha"  type="text" name="funcionarios_educacion_adicional_nombre" size="40" value="<?php echo $eduadicional->getNombre() ?>"/>
                    </div>
                </div>
            </div>  
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Tipo</label>
                    <div class="content">
                        <?php echo $form['tipo_educacion_adicional_id']; ?>                                                   
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
                    <label>Horas</label>
                    <div class="content">
                        <input id="horas" class="valores_ficha" onkeydown="return soloNumeros(event)"  type="text" name="horas" size="4" maxlength="4" value="<?php echo $eduadicional->getHoras() ?>"/>                                                  
                    </div>
                </div>
            </div>         
                  
        </fieldset> 

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoEduadicional(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>
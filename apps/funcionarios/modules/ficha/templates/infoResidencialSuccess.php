<script type="text/javascript" src="/sfDependentSelectPlugin/js/SelectDependiente.js"></script>
<script>
    function saveInfoResidencial() {

      mensajes="";
         
      if (document.getElementById('funcionarios_residencia_estado_id').value==''){
          mensajes += "- Estado \n";
      }
      if (document.getElementById('funcionarios_residencia_municipio_id').value==''){
          mensajes += "- Municipio \n";
      }
      if (document.getElementById('funcionarios_residencia_parroquia_id').value==''){
          mensajes += "- Parroquia \n";
      }
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoResidencial',
            type:'POST',
            dataType:'html',
            data:'&estado='+$("#funcionarios_residencia_estado_id").val()+
                '&municipio='+$("#funcionarios_residencia_municipio_id").val()+
                '&parroquia='+$("#funcionarios_residencia_parroquia_id").val()+
                '&avenida='+$("#residencial_avenida").val()+
                '&edificio='+$("#residencial_edificio").val()+
                '&piso='+$("#residencial_piso").val()+
                '&apartamento='+$("#residencial_apto").val()+
                '&urbanizacion='+$("#residencial_urb").val()+
                '&ciudad='+$("#residencial_ciudad").val()+
                '&punto='+$("#residencial_punto").val()+
                '&telefono1='+$("#residencial_telefono1").val()+
                '&telefono2='+$("#residencial_telefono2").val(),
            success:function(data, textStatus){
                $('#ficha_residencial_content').html(data);
                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };
     

        
</script>

<?php 
    if (sfContext::getInstance()->getUser()->getAttribute('residencia_accion')=='editar'){
        $avenida  =  $residencia->getDirAvCalleEsq(); 
        $edificio = $residencia->getDirEdfCasa();
        $piso     = $residencia->getDirPiso();
        $apartamento = $residencia->getDirAptNombre();
        $urbanizacion = $residencia->getDirUrbanizacion();
        $ciudad = $residencia->getDirCiudad();
        $punto = $residencia->getDirPuntoReferencia();
        $telf1 = $residencia->getTelfUno();
        $telf2 = $residencia->getTelfDos();
    } elseif(sfContext::getInstance()->getUser()->getAttribute('residencia_accion')=='nuevo'){
        $avenida = "";
        $edificio = "";
        $piso= "";
        $apartamento = "";
        $urbanizacion = "";
        $ciudad = "";
        $punto = "";
        $telf1 = "";
        $telf2 = "";
    }
        
 ?>
<form>
<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Residencial</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Estado</label>
                    <div class="content">
                        <?php echo $form['estado_id']; ?>    
                                               
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Municipio</label>
                    <div class="content">                       
                        <?php echo $form['municipio_id']; ?> 
                    </div>
                </div>
            </div>
            
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Parroquia</label>
                    <div class="content">
                        <?php echo $form['parroquia_id']; ?> 
                        
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Av/Calle/Esquina</label>
                    <div class="content">
                       <input id="residencial_avenida" class="valores_ficha" type="text" name="residencial_avenida" size="40"  value="<?php echo $avenida ?>"/>                        
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Edifico/Casa</label>
                    <div class="content">
                       <input id="residencial_edificio" class="valores_ficha" type="text" name="residencial_edificio" size="40"  value="<?php echo $edificio ?>"/>
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Piso</label>
                    <div class="content">
                       <input id="residencial_piso" class="valores_ficha" type="text" name="residencial_piso" size="10"  value="<?php echo $piso ?>"/>
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Apartamento/Nombre</label>
                    <div class="content">
                       <input id="residencial_apto" class="valores_ficha" type="text" name="residencial_apto" size="20"  value="<?php echo $apartamento ?>"/>
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Urbanización/Sector</label>
                    <div class="content">
                       <input id="residencial_urb" class="valores_ficha" type="text" name="residencial_urb" size="40"  value="<?php echo $urbanizacion ?>"/>
                    </div>
                </div>
            </div>
             <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Ciudad</label>
                    <div class="content">
                       <input id="residencial_ciudad" class="valores_ficha" type="text" name="residencial_ciudad" size="40"  value="<?php echo $ciudad ?>"/>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Punto de referencia</label>
                    <div class="content">
                       <input id="residencial_punto" class="valores_ficha" type="text" name="residencial_punto" size="40"  value="<?php echo $punto; ?>"/>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Telefono</label>
                    <div class="content">
                       <input id="residencial_telefono1" class="valores_ficha" type="text" name="residencial_telefono1" size="20"  onkeydown="return soloNumeros(event)" maxlength="11"  value="<?php echo $telf1 ?>"/>                        
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Otro Telefono</label>
                    <div class="content">
                       <input id="residencial_telefono2" class="valores_ficha" type="text" name="residencial_telefono2" size="20"  onkeydown="return soloNumeros(event)" maxlength="11" value="<?php echo $telf2 ?>"/>                        
                    </div>
                </div>
            </div>
        </fieldset> 

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoResidencial(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>
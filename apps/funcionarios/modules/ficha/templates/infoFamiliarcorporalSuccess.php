<script>
    function saveInfoFamiliarcorporal() {

      mensajes="";
      if (document.getElementById('familiarcorporal_peso').value==''){
          mensajes += "- Peso \n";
      }
      if (document.getElementById('familiarcorporal_altura').value==''){
          mensajes += "- Altura \n";
      }
      if (document.getElementById('funcionarios_informacion_corporal_familiar_corporal_color_ojos').value==''){
          mensajes += "- Color de ojos \n";
      }
      if (document.getElementById('funcionarios_informacion_corporal_familiar_corporal_color_cabello').value==''){
          mensajes += "- Color de cabello \n";
      }
      if (document.getElementById('funcionarios_informacion_corporal_familiar_corporal_color_piel').value==''){
          mensajes += "- Color de piel \n";
      }  
      if (document.getElementById('funcionarios_informacion_corporal_familiar_familiarcorporal_camisa').value==''){
          mensajes += "- Talla de camisa \n";
      }
      if (document.getElementById('funcionarios_informacion_corporal_familiar_familiarcorporal_pantalon').value==''){
          mensajes += "- Talla de pantalon \n";
      }   
      if (document.getElementById('familiarcorporal_calzado').value==''){
          mensajes += "- Talla de calzado \n";
      } 
      if (document.getElementById('funcionarios_informacion_corporal_familiar_familiarcorporal_gorra').value==''){
          mensajes += "- Talla de gorra \n";
      }       
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoFamiliarcorporal',
            type:'POST',
            dataType:'html',
            data:'peso='+$("#familiarcorporal_peso").val()+
                '&ojos='+$("#funcionarios_informacion_corporal_familiar_corporal_color_ojos").val()+
                '&cabello='+$("#funcionarios_informacion_corporal_familiar_corporal_color_cabello").val()+
                '&piel='+$("#funcionarios_informacion_corporal_familiar_corporal_color_piel").val()+
                '&altura='+$("#familiarcorporal_altura").val()+
                '&camisa='+$("#funcionarios_informacion_corporal_familiar_familiarcorporal_camisa").val()+
                '&pantalon='+$("#funcionarios_informacion_corporal_familiar_familiarcorporal_pantalon").val()+
                '&calzado='+$("#familiarcorporal_calzado").val()+
                '&gorra='+$("#funcionarios_informacion_corporal_familiar_familiarcorporal_gorra").val(),
            success:function(data, textStatus){
                $('#ficha_familiarcorporal_content').html(data);

                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };
     

        
</script>

<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Familiar</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Familiar Corporal</h2>
           
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Peso</label>
                    <div class="content">
                        <input id="familiarcorporal_peso" class="valores_ficha" onkeydown="return soloNumeros(event)"  type="text" name="familiarcorporal_peso" size="4" maxlength="3" value="<?php echo $familiarcorporal->getPeso() ?>"/>
                    </div>
                    <div class="help">Escriba su peso corporal en Kilogramos.</div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Altura</label>
                    <div class="content">
                        <input id="familiarcorporal_altura" class="valores_ficha" onkeydown="return soloNumeros(event)" type="text" name="familiarcorporal_altura" size="4" maxlength="3" value="<?php echo $familiarcorporal->getAltura() ?>"/>
                    </div>
                    <div class="help">Escriba su altura corporal en Centimetros.</div>
                </div>
            </div>    
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Color de ojos</label>
                    <div class="content">
                        <?php echo $form['corporal_color_ojos'] ?> 
                        <script>class_ojos();</script>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Color de cabello</label>
                    <div class="content">
                        <?php echo $form['corporal_color_cabello'] ?> 
                        <script>class_cabello();</script>
                    </div>
                </div>
            </div>            
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Color de piel</label>
                    <div class="content">
                        <?php echo $form['corporal_color_piel'] ?> 
                        <script>class_piel();</script>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de camisa</label>
                    <div class="content">
                        <?php echo $form['familiarcorporal_camisa'] ?> 
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de pantalon</label>
                    <div class="content">
                         <?php echo $form['familiarcorporal_pantalon'] ?> 
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de gorra</label>
                    <div class="content">
                       <?php echo $form['familiarcorporal_gorra'] ?> 
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de calzado</label>
                    <div class="content">
                        <select id="familiarcorporal_calzado" class="valores_ficha" type="text" name="familiarcorporal_calzado">
                            <option value=""></option>
                            <?php for($i=35;$i<54;$i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if($familiarcorporal->getTallaCalzado()==$i): echo "selected='selected '"; endif ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </fieldset> 

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoFamiliarcorporal(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
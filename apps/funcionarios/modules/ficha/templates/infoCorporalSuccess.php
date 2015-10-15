<script>
    function saveInfoCorporal() {

      mensajes="";
      if ($('#corporal_peso').val()==''){
          mensajes += "- Peso \n";
      }
      if ($('#corporal_altura').val()==''){
          mensajes += "- Altura \n";
      }
      if ($('#funcionarios_informacion_corporal_corporal_color_ojos').val()==''){
          mensajes += "- Color de ojos \n";
      }
      if ($('#funcionarios_informacion_corporal_corporal_color_cabello').val()==''){
          mensajes += "- Color de cabello \n";
      }
      if ($('#funcionarios_informacion_corporal_corporal_color_piel').val()==''){
          mensajes += "- Color de piel \n";
      }      
      if ($('#funcionarios_informacion_corporal_corporal_camisa').val()==''){
          mensajes += "- Talla de camisa \n";
      }
      if ($('#funcionarios_informacion_corporal_corporal_pantalon').val()==''){
          mensajes += "- Talla de pantalon \n";
      }   
      if ($('#corporal_calzado').val()==''){
          mensajes += "- Talla de calzado \n";
      } 
      if ($('#funcionarios_informacion_corporal_corporal_gorra').val()==''){
          mensajes += "- Talla de gorra \n";
      }       
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoCorporal',
            type:'POST',
            dataType:'html',
            data:'peso='+$("#corporal_peso").val()+
                '&ojos='+$("#funcionarios_informacion_corporal_corporal_color_ojos").val()+
                '&cabello='+$("#funcionarios_informacion_corporal_corporal_color_cabello").val()+
                '&piel='+$("#funcionarios_informacion_corporal_corporal_color_piel").val()+
                '&altura='+$("#corporal_altura").val()+
                '&sangre='+$("#funcionarios_informacion_corporal_corporal_sangre").val()+
                '&lentes='+'Izq:'+$("#corporal_lentes_izq").val()+' Der:'+$("#corporal_lentes_der").val()+
                '&camisa='+$("#funcionarios_informacion_corporal_corporal_camisa").val()+
                '&pantalon='+$("#funcionarios_informacion_corporal_corporal_pantalon").val()+
                '&calzado='+$("#corporal_calzado").val()+
                '&gorra='+$("#funcionarios_informacion_corporal_corporal_gorra").val(),
            success:function(data, textStatus){
                $('#ficha_corporal_content').html(data);

                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };
     

        
</script>
<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Corporal</h2>

            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Peso</label>
                    <div class="content">
                        <input id="corporal_peso" class="valores_ficha" onkeydown="return soloNumeros(event)"  type="text" name="corporal_peso" size="4" maxlength="3" value="<?php echo $corporal->getPeso() ?>"/>
                    </div>
                    <div class="help">Escriba su peso corporal en Kilogramos.</div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Altura</label>
                    <div class="content">
                        <input id="corporal_altura" class="valores_ficha" onkeydown="return soloNumeros(event)" type="text" name="corporal_altura" size="4" maxlength="3" value="<?php echo $corporal->getAltura() ?>"/>
                    </div>
                    <div class="help">Escriba su altura corporal en Centimetros.</div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Tipo de sangre</label>
                    <div class="content">
                        <?php echo $form['corporal_sangre'] ?> 
                    </div>
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
                    <label>Formula de lentes</label>
                    <div class="content">
                        Izquerdo:&nbsp;<input id="corporal_lentes_izq" class="valores_ficha" onkeydown="return soloNumeros(event)" type="text" name="corporal_lentes_izq" size="4" maxlength="4" value="<?php if($corporal->getLentesFormula()!=''){ $valor = explode("Izq:",$corporal->getLentesFormula()); $valor2 = explode(" Der:",$valor[1]); echo $valor2[0];} ?>"/>&nbsp;&nbsp;&nbsp;&nbsp;
                        Derecho:&nbsp;<input id="corporal_lentes_der" class="valores_ficha" onkeydown="return soloNumeros(event)" type="text" name="corporal_lentes_der" size="4" maxlength="4" value="<?php  if($corporal->getLentesFormula()!=''){ $valor = explode("Izq:",$corporal->getLentesFormula()); $valor2 = explode(" Der:",$valor[1]); echo $valor2[1];} ?>"/>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de camisa</label>
                    <div class="content">
                        <?php echo $form['corporal_camisa'] ?> 
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de pantalon</label>
                    <div class="content">
                         <?php echo $form['corporal_pantalon'] ?> 
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de gorra</label>
                    <div class="content">
                       <?php echo $form['corporal_gorra'] ?> 
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Talla de calzado</label>
                    <div class="content">
                        <select id="corporal_calzado" class="valores_ficha" type="text" name="corporal_calzado">
                            <option value=""></option>
                            <?php for($i=35;$i<54;$i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if($corporal->getTallaCalzado()==$i): echo "selected='selected '"; endif ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </fieldset> 

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoCorporal(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
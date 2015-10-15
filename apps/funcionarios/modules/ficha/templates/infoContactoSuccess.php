
<script>
     fn_dar_eliminar();
                    fn_cantidad();
    var cantidad = 0;
    var agregar = 0;
    
    function fn_cantidad(){
            cantidad = $("#grilla tbody").find("tr").length;
            $("#span_cantidad").html(cantidad);           
    };
    
    var mod = 0;
    function fn_dar_eliminar(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                  $(this).remove(); 
                  modificado = 1;
                  fn_cantidad();
                })
        });
    };
    

    
    function fn_validar_campo(){
         if (($("#funcionarios_contacto_contacto_tipo").val() == 1) || ($("#funcionarios_contacto_contacto_tipo").val() == 2)){


		v_telefono =  $("#contacto_valor").val();
		var er_tlfono = /^([0-9\s\+\-])+$/
		 var num_telefono = v_telefono.length
		 if(!er_tlfono.test(v_telefono)){
		 	alert(v_telefono + " no es formato valido para telefono");
                        $("#contacto_valor").val('');
                        return false
                 }
		 else if(num_telefono < 9)
		 	alert("numero Muy corto")
                 else if(num_telefono > 11)
		 	alert("numero Muy Largo")
		 else{}

         }
         else if ($("#funcionarios_contacto_contacto_tipo").val() == 3){
                v_email = $("#contacto_valor").val()
		 var er_email = /^(.+\@.+\..+)$/
		 if(!er_email.test(v_email)){
		 	alert(v_email+ " no es valido como email");
                        return false;
                 }
		else
		{
			return true;
		}

         }
    };
    
    function fn_agregar(){
        if($("#funcionarios_contacto_contacto_tipo").val() && $("#contacto_valor").val())
        {            
            cadena = "<tr>";  
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#contacto_valor").html()) + "</font><br/>";
            cadena += "<font class='f16n'>" + $("#funcionarios_contacto_contacto_tipo option:selected").text()+ ": " + $("#contacto_valor").val() + "</font>";
            cadena += "<td><input id='contacto[tipo]' name='contacto[tipo]' type='hidden' value='"  + $("#funcionarios_contacto_contacto_tipo").val() + "#" + $("#contacto_valor").val() + "'/>" + "</td>";          
            cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            comp = $("#funcionarios_contacto_contacto_tipo").val()+ "#" + $("#contacto_valor").val();
  
            if(cantidad > 0){
                agregar = 0;
                $("#grilla tbody tr td input[name='contacto[tipo]']").each(function (){
                    if($(this).val() == comp){ agregar = 1; }

                });
                if(agregar == 0)
                {
                    $("#grilla tbody").append(cadena);
                    modificado = 1;
                    fn_dar_eliminar();
                    fn_cantidad();
                    agregar = 0;
                }
            }
            else
            {
                $("#grilla tbody").append(cadena);
                modificado = 1;
                fn_dar_eliminar();
                fn_cantidad();               
            }
             $("#funcionarios_contacto_contacto_tipo").val('');
             $("#contacto_valor").val('');
            
        }
        else
        { alert('Debe colocar un valor al tipo de contacto'); }
    };
    
    
    function saveInfoContacto() {
    
       mensajes="";
       var d = 0;
       var grupoId = '';        

        $("#grilla tbody tr td input[name='contacto[tipo]']").each(function (){
            if(d==0){grupoId = $(this).val(); d = 1;}
            else{grupoId = grupoId+","+$(this).val();}

        });     
    
      if (grupoId   ==''){
          mensajes += "- Tipo de contacto \n";
      }   
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
             
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoContacto',
            type:'POST',
            dataType:'html',
            data:'tipo='+grupoId,
                
            success:function(data, textStatus){
                $('#ficha_contacto_content').html(data);
                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };      
    
   
</script>
  <style type='text/css'>
select#idioma option
{
    background-repeat: no-repeat;
    padding-left: 24px;
}

option#fijo
{
    background-image: url('/images/icon/phone.png');
    background-repeat: no-repeat;
}

option#movil
{
    background-image: url('/images/icon/phone_movil.png');
    background-repeat: no-repeat;
}

option#email
{
    background-image:  url('/images/icon/mail_new.png');
    background-repeat: no-repeat;
}
</style>
<form>       
<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion de Contacto</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Tipo de contacto</label>
                    <div class="content">                  
                        <select id="funcionarios_contacto_contacto_tipo" name="funcionarios_contacto[contacto_tipo]" >
                            <option selected="selected" value="">Seleccione</option>
                            <option value="1" id="fijo" data-image="/images/icon/phone.png">&nbsp;&nbsp;&nbsp;&nbsp;Teléfono Fijo</option>
                            <option value="2" id="movil"  data-image="/images/icon/phone_movil.png">&nbsp;&nbsp;&nbsp;&nbsp;Teléfono Móvil</option>
                            <option value="3" id="email"  data-image="/images/icon/mail_new.png">&nbsp;&nbsp;&nbsp;&nbsp;E-mail</option>
                        </select>
                        <input id="contacto_valor" class="valores_ficha"  type="text" name="contacto_valor"  onblur="javascript: fn_validar_campo();"  size="20"  value="" />
                    </div>
                </div>
            </div>            
                <div>
                    <label></label>
                    <div class="content">   
                    <br/>
                    <table id="grilla" class="lista">
                        <tbody>
                           <?php 
                            $tipos = array('1' => 'Teléfono Fijo', '2' => 'Teléfono Movil', '3' => 'Correo Electrónico');
                            foreach($cantcontactos as $contacto){
                                ?>
                                <tr>
                                    <td>
                                        <font class="f16b"></font><br>
                                        <font class="f16n"><?php echo $tipos[$contacto->getTipo()].": ".$contacto->getValor(); ?></font>
                                    </td>
                                    <td><input id='contacto[tipo]' name='contacto[tipo]' type='hidden' value="<?php echo $contacto->getTipo()."#".$contacto->getValor(); ?>"></td>
                                    <td>
                                        <a style="cursor: pointer;" class="elimina"><img src="/images/icon/delete.png"></a>
                                    </td>
                                </tr>
                           <?php 
                                }
                           ?>
                        </tbody>
                    </table>

                    </div>
                </div>
            <div class='partial_new_view partial'><a href="#" onclick="javascript: fn_agregar(); return false;">Agregar otro</a></div>
        </fieldset> 
        <br>
        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoContacto(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>

<script>
     fn_dar_eliminar();
     fn_cantidad();
     

    var cantidad = 0;
    var agregar = 0;
    
    function fn_cantidad(){
            cantidad = $("#grilla tbody").find("tr").length;
            $("#span_cantidad").html(cantidad);           
    };
    
    function fn_ocultar_option(option){        
         $("#funcionarios_idioma_manejado_idioma_id option[value=" + option + "]").hide();      
    };
    
    var mod = 0;
    function fn_dar_eliminar(option){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                  $(this).remove(); 
                  modificado = 1;
                    $("#funcionarios_idioma_manejado_idioma_id option[value=" + option + "]").show();
                  fn_cantidad();
                  option='';                  
                })
        });
    };
    
    
    function fn_agregar(){
        if($("#funcionarios_idioma_manejado_idioma_id").val())
        {       
                        
            cadena = "<tr>";  
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#idioma").html()) + "</font><br/>";
            cadena += "<font class='f16n'>" + $("#funcionarios_idioma_manejado_idioma_id option:selected").text() + "</font>";
            cadena += "<td><input id='idioma[id]' name='idioma[id]' type='hidden' value='"  + $("#funcionarios_idioma_manejado_idioma_id").val()  
                                                                                            + "#" + $("input[name='funcionarios_idioma_manejado[idioma_principal]']:checked").val()
                                                                                            + "#" + $("input[name='funcionarios_idioma_manejado[idioma_habla]']:checked").val() 
                                                                                            + "#" + $("input[name='funcionarios_idioma_manejado[idioma_lee]']:checked").val()
                                                                                            + "#" + $("input[name='funcionarios_idioma_manejado[idioma_escribe]']:checked").val()
                                                                                            +"'/>" + "</td>";        
            cadena += "<td><a class='elimina' style='cursor: pointer;'  onclick='javascript: fn_dar_eliminar("+$("#funcionarios_idioma_manejado_idioma_id").val()+")' ><img src='/images/icon/delete.png'/></a></td>";
            comp = $("#funcionarios_idioma_manejado_idioma_id").val();
                

            if(cantidad > 0){ 
                agregar = 0;
                $("#grilla tbody tr td input[name='idioma_id']").each(function (){      
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
            
            $("#funcionarios_idioma_manejado_idioma_id option[value=" + comp + "]").hide();
            $("#funcionarios_idioma_manejado_idioma_id").val('');
            
        }
        else
        { alert('Debe seleccionar un idioma'); }
    };
    
    
    function saveInfoIdioma() {
    
       mensajes="";
       var d = 0;
       var grupoId = '';        

        $("#grilla tbody tr td input[name='idioma[id]']").each(function (){
            if(d==0){grupoId = $(this).val(); d = 1;}
            else{grupoId = grupoId+","+$(this).val();}

        });     
    
      if (grupoId   ==''){
          mensajes += "- Idioma \n";
      }   
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
             
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoIdioma',
            type:'POST',
            dataType:'html',
            data:'idiomaId='+grupoId,
                
            success:function(data, textStatus){
                $('#ficha_idioma_content').html(data);
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

            <h2>Informacion de Idiomas manejados</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Idioma manejado</label>
                    <div class="content">                  
                        <?php echo $form['idioma_id'] ?>                       
                    </div>
                </div>                    
            </div>    
            <div id="opciones" >
                    <div class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label>Idioma principal</label>
                            <div class="content">                  
                                <?php echo $form['idioma_principal'] ?>  
                            </div>
                        </div>                    
                    </div>  
                    <div class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label>Habla</label>
                            <div class="content">                  
                                <?php echo $form['idioma_habla'] ?>                       
                            </div>
                        </div>                    
                    </div>  
                    <div class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label>Lee</label>
                            <div class="content">                  
                                <?php echo $form['idioma_lee'] ?>                       
                            </div>
                        </div>                    
                    </div>  
                    <div class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label>Escribe</label>
                            <div class="content">                  
                                <?php echo $form['idioma_escribe'] ?>                       
                            </div>
                        </div>                    
                    </div>  
            </div>
                <div>
                    <br/>
                    <table id="grilla" class="lista">
                        <tbody>
                           <?php 
                             $valores = array('0' => '0','1'=> '1');
                            foreach($cantidiomas as $idioma){
                                  $idiomaDes = Doctrine::getTable('Public_Idioma')->findById($idioma->getIdiomaId());
                                 
                                ?>
                                <script>fn_ocultar_option(<?php echo $idioma->getIdiomaId()?>);</script>
                                <tr>
                                    <td>
                                        <font class="f16b"></font><br>
                                        <font class="f16n"><?php echo $idiomaDes[0]; ?></font>
                                    </td>
                                    <td>
                                        <input id='idioma_id' name='idioma_id' type='hidden' value="<?php echo $idioma->getIdiomaId()?>">
                                        <input id='idioma[id]' name='idioma[id]' type='hidden' value="<?php echo $idioma->getIdiomaId()."#".$valores[$idioma->getPrincipal()]."#".$valores[$idioma->getHabla()]."#".$valores[$idioma->getLee()]."#".$valores[$idioma->getEscribe()]; ?>"></td>
                                    <td>
                                        <a style="cursor: pointer;" onclick="javascript: fn_dar_eliminar(<?php echo $idioma->getIdiomaId()?>)" class="elimina"><img src="/images/icon/delete.png"></a>
                                    </td>
                                </tr>
                           <?php 
                                }
                           ?>
                        </tbody>
                    </table>                
                </div>
            <div class='partial_new_view partial'><a href="#" onclick="javascript: fn_agregar(); return false;">Agregar otro</a></div>
        </fieldset> 
        <br>
        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoIdioma(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>
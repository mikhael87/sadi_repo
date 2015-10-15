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
    
    
    function fn_agregar(){
        if($("#funcionarios_funcionario_discapacidad_discapacidad_id").val() )
        {       
                        
            cadena = "<tr>";  
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#grupo").html()) + "</font><br/>";
            cadena += "<font class='f16n'>" + $("#funcionarios_funcionario_discapacidad_discapacidad_id option:selected").text() + "</font>";
            cadena += "<td><input id='discapacidad[id]' name='discapacidad[id]' type='hidden' value='"  + $("#funcionarios_funcionario_discapacidad_discapacidad_id").val() + "'/>" + "</td>";        
            cadena += "<td><a class='elimina' style='cursor: pointer;' ><img src='/images/icon/delete.png'/></a></td>";
            comp = $("#funcionarios_funcionario_discapacidad_discapacidad_id").val();

            if(cantidad > 0){ 
                agregar = 0;
                $("#grilla tbody tr td input[name='discapacidad[id]']").each(function (){    
                    
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
            
            $("#funcionarios_funcionario_discapacidad_discapacidad_id").val('');
            
        }
        else
        { alert('Debe seleccionar un tipo de discapacidad'); }
    };
    
    function saveInfoFuncionariodiscapacidad() {

      mensajes="";
      var d = 0;
       var grupoId = '';        

        $("#grilla tbody tr td input[name='discapacidad[id]']").each(function (){
            if(d==0){grupoId = $(this).val(); d = 1;}
            else{grupoId = grupoId+","+$(this).val();}

        });     
    
      if (grupoId   ==''){
          mensajes += "- Tipo de discapacidad \n";
      }     
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoFuncionariodiscapacidad',
            type:'POST',
            dataType:'html',
            data:'grupoId='+grupoId,
            success:function(data, textStatus){
                $('#ficha_funcionariodiscapacidad_content').html(data);

                $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
                $("#div_espera_documento").hide();
                
                $('#content_notificacion_derecha').html('');
            }});
        }
    };
     

        
</script>


<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Funcionario</h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_email">

            <h2>Informacion Discapacidad</h2>
           
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Tipo de discapacidad</label>
                    <div class="content">
                        <?php echo $form['discapacidad_id'] ?> 
                    </div>
                </div>
            </div>    
            <div>
                    <br/>
                    <table id="grilla" class="lista">
                        <tbody>
                           <?php 
                            foreach($cantdiscapacidad as $discapacidad){

                            if ($discapacidad->getProteccion()!=""){ 
                            ?>
                                <div id="sf_admin_container">
                                    <div class="error">La información fue procesada por el departamento de RRHH. No puede modificarla.</div>
                                </div>            
                            <?php 
                                    exit();
                            }                              

                                
                                  $tipoDiscapacidad = Doctrine::getTable('Public_Discapacidad')->datosDiscapacidadesActivas($discapacidad->getDiscapacidadId());
                                 
                                ?>                               
                                <tr>
                                    <td>
                                        <font class="f16b"></font><br>
                                        <font class="f16n"><?php echo $tipoDiscapacidad[0]; ?></font>
                                    </td>
                                    <td>
                                        <input id='discapacidad[id]' name='discapacidad[id]' type='hidden' value="<?php echo $discapacidad->getDiscapacidadId(); ?>"></td>
                                    <td>
                                        <a style="cursor: pointer;"  class="elimina"><img src="/images/icon/delete.png"></a>
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

        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoFuncionariodiscapacidad(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
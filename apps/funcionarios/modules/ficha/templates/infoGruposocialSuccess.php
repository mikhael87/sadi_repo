
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
        if($("#funcionarios_grupo_social_tipo_grupo_social_id").val() && $("#grupo_nombre").val() )
        {       
                        
            cadena = "<tr>";  
            cadena += "<td><font class='f16b'>" + jQuery.trim($("#grupo").html()) + "</font><br/>";
            cadena += "<font class='f16n'>" + $("#funcionarios_grupo_social_tipo_grupo_social_id option:selected").text() + ' - ' + $("#grupo_nombre").val() + "</font>";
            cadena += "<td><input id='grupo[id]' name='grupo[id]' type='hidden' value='"  + $("#funcionarios_grupo_social_tipo_grupo_social_id").val() + "#" 
                                                                                          + $("#grupo_nombre").val() + "#"
                                                                                          + $("#grupo_descripcion").val() 
                                                                                          +"'/>" + "</td>";        
            cadena += "<td><a class='elimina' style='cursor: pointer;' ><img src='/images/icon/delete.png'/></a></td>";
            comp = $("#funcionarios_grupo_social_tipo_grupo_social_id").val()+ "#" + $("#grupo_nombre").val() + "#" + $("#grupo_descripcion").val();

            if(cantidad > 0){ 
                agregar = 0;
                $("#grilla tbody tr td input[name='grupo[id]']").each(function (){      
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
            
            $("#funcionarios_grupo_social_tipo_grupo_social_id").val('');
             $("#grupo_nombre").val('');
             $("#grupo_descripcion").val('');
            
        }
        else
        { alert('Debe seleccionar un grupo social y asignarle un nombre'); }
    };
    
    
    function saveInfoGrupo() {
    
       mensajes="";
       var d = 0;
       var grupoId = '';        

        $("#grilla tbody tr td input[name='grupo[id]']").each(function (){
            if(d==0){grupoId = $(this).val(); d = 1;}
            else{grupoId = grupoId+","+$(this).val();}

        });     
    
      if (grupoId   ==''){
          mensajes += "- Tipo de Grupo \n";
      }     
      if (mensajes!=''){
          alert("Los siguientes campos no pueden quedar vacios: \n" +mensajes);
      }else{
             
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/saveInfoGrupo',
            type:'POST',
            dataType:'html',
            data:'grupoId='+grupoId,
                
            success:function(data, textStatus){
                $('#ficha_grupo_content').html(data);
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

            <h2>Informacion de Grupos Sociales</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Tipo de grupo</label>
                    <div class="content">                  
                        <?php echo $form['tipo_grupo_social_id'] ?>                       
                    </div>
                </div>                    
            </div>    

            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Nombre del grupo</label>
                    <div class="content">                  
                        <input id="grupo_nombre" class="valores_ficha"  type="text" name="grupo_nombre"   size="40"  value="" />
                    </div>
                </div>                    
            </div>  
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label>Descripción</label>
                    <div class="content">                  
                        <textarea id ="grupo_descripcion" class="valores_ficha" ></textarea>
                    </div>
                </div>                    
            </div>             
                <div>
                    <br/>
                    <table id="grilla" class="lista">
                        <tbody>
                           <?php 
                            foreach($cantgrupos as $grupos){
                                  $tipoGrupo = Doctrine::getTable('Public_TipoGrupoSocial')->findById($grupos->getTipoGrupoSocialId());
                                 
                                ?>                               
                                <tr>
                                    <td>
                                        <font class="f16b"></font><br>
                                        <font class="f16n"><?php echo $tipoGrupo[0]. '-'. $grupos->getNombre(); ?></font>
                                    </td>
                                    <td>
                                        <input id='grupo[id]' name='grupo[id]' type='hidden' value="<?php echo $grupos->getTipoGrupoSocialId().'#'.$grupos->getNombre().'#'.$grupos->getDescripcion(); ?>"></td>
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
        <br>
        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveInfoGrupo(); return false;">
        </li>
        <br/><br/>
    </div>
</div>
</form>
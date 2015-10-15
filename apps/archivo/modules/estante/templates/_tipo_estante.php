<?php use_helper('jQuery'); ?>
<?php
        $estante_modelo = Doctrine::getTable('Archivo_EstanteModelo')
                            ->createQuery('a')
                            ->orderBy('nombre')->execute();
?>


<script>
  $(document).ready(function () {
      $('#flashes_estante_modelo').fadeOut(8000);
  });
  
  
    function toggleEstanteModelo()
    {
        $("#div_estante_modelo_nombre").toggle("slow");
        $("#div_estante_modelo_button_save").toggle("slow");
        $("#estante_modelo_nombre").val(null);
    }
    
    function saveEstanteModelo()
    {
        var error = null;
        if(!$("#estante_modelo_nombre").val()) {
            error = 'Escriba el nombre del tipo de estante.';
        }

        if(error==null) { 
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>estante/saveEstanteModelo',
                type:'POST',
                dataType:'html',
                data:'nombre='+$("#estante_modelo_nombre").val(),
                success:function(data, textStatus){
                    jQuery('#lista_estante_modelos').html(data);
                }})

            $("#div_estante_modelo_nombre").hide();
            $("#div_estante_modelo_button_save").hide();
        } else {
            alert(error);
        }
    }
</script>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="Cargo">Modelo de Estante</label>
        <div class="content" id="lista_cargos" style="position: relative;">

        <div style="position: absolute; top: -20px;">
            <div style="position: absolute; left: 0px; cursor: pointer;" id="div_estante_modelo_button_new" onclick="javascript:toggleEstanteModelo();"><?php echo image_tag('icon/new.png'); ?></div>
            <div style="position: absolute; left: 20px; display: none;" id="div_estante_modelo_nombre"><input type="text" id="estante_modelo_nombre" size="25"/></div>
            <div style="position: absolute; top: 3px; left: 237px; cursor: pointer; display: none;" id="div_estante_modelo_button_save" onclick="javascript:saveEstanteModelo();"><?php echo image_tag('icon/filesave.png'); ?></div>
        </div>


        <div id="lista_estante_modelos">
            <select name="archivo_estante[estante_modelo_id]">
                <option value=""></option>
                <?php foreach ($estante_modelo as $modelo) { ?>
                    <option value="<?php echo $modelo->getId(); ?>"><?php echo $modelo->getNombre(); ?></option>
                <?php } ?>
            </select>
        </div>

        </div>
    </div>
</div>
<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function() {
        ver_unidad_historicos();
    });
    
    function ver_unidad_historicos()
    {
        var unidad_id= $('#historico_unidad_id').val();
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>grupos/unidadHistorico',
            type:'POST',
            dataType:'html',
            data:'id='+unidad_id,
            beforeSend: function(Obj){
                jQuery('#contenido_historico').html('Espere...');
            },
            success:function(data, textStatus){
                jQuery('#contenido_historico').html(data);
        }});
    }
</script>

<div id="sf_admin_container">
    <h1>Hist&oacute;rico de permisos asignados</h1>
    
    <div id="sf_admin_header"></div>
    
    <div id="sf_admin_content">
        
        <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id trans">
            <div>
                <label for="historico_unidad_id">Unidad</label>
                <div class="content">
                    <select name="historico_unidad_id" id="historico_unidad_id" onchange="ver_unidad_historicos(); return false;">
                        <?php foreach( $funcionario_unidades as $unidad ) :
                            $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($unidad);
                            echo '<option value="'. $unidad->getId() .'">'. $unidad->getNombre() .'</option>';
                        endforeach;
                        ?>
                    </select>

                </div>
                <div class="help">Seleccione la unidad que desea ver.</div>
            </div>
        </div>
        
        <hr/>
        
        <div id="contenido_historico" class="sf_admin_list">
        </div>
    </div>

    <div id="sf_admin_footer">
        <ul class="sf_admin_actions trans">
            <li class="sf_admin_action_regresar_modulo">
                <a href="<?php echo sfConfig::get('sf_app_archivo_url'); ?>grupos/index">Regresar a permisos de grupo</a>
            </li>
        </ul>
    </div>
    
</div>
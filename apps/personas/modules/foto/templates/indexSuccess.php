<?php
$from_j= $from;
?>
<script>
    $(document).ready(function(){
        if("<?php echo $from_j ?>" === 'digifirma') {
            abrir_metodo_carga('cargar');
        }
    });
    
    function abrir_metodo_carga(metodo) {
        $('.sf_admin_form_field_ci').show();
        $('#div_metodo_carga').html('');
        $('#div_metodo_carga').html('<div style="position: absolute; width: 400px; left: 215px;"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando metodo de carga...</div>');

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_personas_url'); ?>foto/metodoCarga',
            data: {metodo: metodo},
            success:function(data, textStatus){
                $('.sf_admin_form_field_ci').show();
                $('#div_metodo_carga').html(data);
            }
        });
    }
</script>

<div id="sf_admin_container">
    
    <?php
    switch ($from) {
        case 'digifirma':
                echo '<h1>Firma digitalizada</h1>';
                break;
            case 'foto':
                echo '<h1>Cambio de Foto del Personal</h1>';
                break;
            default:
                break;
    }
    ?>

    <div id="sf_admin_header"></div>

    <div class="trans" style="width: 100%;">
        <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_ci">
            <div>
                <?php if($from== 'foto') : ?>
                    <label for="">Metodo de carga</label>
                    <div class="content">
                        <a href="#" onclick="abrir_metodo_carga('tomar'); return false;">
                            <img src="/images/icon/camera16.png"/>&nbsp;Tomar fotografia
                        </a>
                        <br/>
                        <a href="#" onclick="abrir_metodo_carga('cargar'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Cargar fotografia
                        </a>
                    </div>
                <?php elseif($from== 'digifirma') : ?>
                    <label for="">Firma manuscrita digitalizada. <font style="color: #888; font-size: 10px; font-style: italic">Esta im&aacute;gen aparecer&aacute; en todos los documentos que firme elecr&oacute;nicamente este funcionario.</font></label>
                    <div class="content">
                        <?php
                        if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $sf_user->getAttribute('digifirma_cambio') .".jpg")) {
                            echo '<img src="/images/firma_digital/'. $sf_user->getAttribute('digifirma_cambio') .'.jpg"/>';
                        }else {
                            echo '<img src="/images/firma_digital/signature_default.png"/>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_ci" style="display: none">
            <div>
                <label for="">&nbsp;</label>
                <div id="div_metodo_carga"></div>
            </div>
        </div>
    </div>

    <ul class="sf_admin_actions trans">
      <li class="sf_admin_action_regresar_modulo">
          <a href="<?php echo sfConfig::get('sf_app_personas_url'); ?>usuario">Regresar</a>
      </li>
    </ul>
</div>
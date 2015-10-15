<?php use_helper('jQuery'); ?>
<script>
    function saveTransferida() {

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>serie_documental/transferida',
            type:'POST',
            dataType:'html',
            data: $('#form_transferida').serialize(),
                
            success:function(data, textStatus){
                $('#div_flashes').html(data);
                $('#tr_serie_documental_<?php echo $sf_user->getAttribute('serie_documental_id'); ?>').remove();
                
                $("#content_window_right").animate({right:"-=892px"},1000);
                $("#header_window_right").animate({right:"-=892px"},1000);
                $("#div_wait_window_right").hide();
                
                $('#content_window_right').html('');
            }});
    };            
</script>

<div id="sf_admin_container" style="width: 100%;">
    <h1>Transferencia de Serie Documental</h1>

    <div id="sf_admin_content">
        <div class="sf_admin_form">

        <form id="form_transferida">
            <input type="hidden" name="serie_documental_id" value="<?php echo $serie_documental_id; ?>"/>
            <?php
            if (count($almacenamientos) > 0) {
                echo '<u>La Serie Documental actualmente se esta almacenando en los siguientes lugares:</u><br/><br/>';
                echo '<table class="trans" style="width: 100%;">';
                foreach ($almacenamientos as $almacenamiento) {

                    echo '<tr><td>';
                    echo '<b>Unidad:</b> ' . $almacenamiento->getArchivo_Estante()->getOrganigrama_UnidadFisica()->getNombre() . '<br/>';
                    echo '<b>Estante:</b> ' . $almacenamiento->getArchivo_Estante()->getIdentificador() . ' - ';
                    echo '<b>Tramos:</b> ' . $almacenamiento->getTramos() . '<br/>';

//                  echo $almacenamiento->getEstanteId().'<br/>';
//                  echo $almacenamiento->getArchivo_Estante()->getOrganigrama_UnidadDuena()->getNombre().'<br/>';
                    echo '</td></tr>';
                }
                echo '</table>';

                echo '<b>¿Desea transferir tambien la administración de estos estantes (<i><u>sin cambiar la ubicación</u></i>) a la unidad destino?</b><br/>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<input type="radio" name="transferir_estantes" value="t"/>Si&nbsp;&nbsp;&nbsp;';
                echo '<input type="radio" name="transferir_estantes" value="f" checked="checked"/>No&nbsp;&nbsp;&nbsp;<hr>';
            } else {
                echo '<div class="error">No se ha definido un un estante donde se almacenara la Serie Documental. Debera asigar un modo de almacenamiento en la unidad destino.</div>';
            }
            ?>

            <div class="sf_admin_form_row sf_admin_text trans">
                <div>
                    <label>Unidad Destino</label>
                    <div class="content">

                        <?php echo $form['padre_id']->renderError() ?>
                        <?php echo $form['padre_id'] ?>                      
                    </div>
                </div>
            </div>

            <br/>
            <ul class="sf_admin_actions trans">
            <li class="sf_admin_action_save">
                <input id="guardar" type="button" value="Transferir" onclick="saveTransferida(); return false;">
            </li>
            </ul>
        </form>
        </div>
    </div>
</div>
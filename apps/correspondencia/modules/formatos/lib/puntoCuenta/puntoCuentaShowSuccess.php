<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Asunto: </font>
        <font class="f16n"><?php if(isset($valores['punto_cuenta_asunto'])) echo html_entity_decode($valores['punto_cuenta_asunto']); ?></font>
    </div>
    <div>
        <font class="f16b">Sintesis: </font>
        <font class="f16n"><?php if(isset($valores['punto_cuenta_sintesis'])) echo html_entity_decode($valores['punto_cuenta_sintesis']); ?></font>
    </div>
    <div>
        <font class="f16b">Recomendaciones: </font>
        <font class="f16n"><?php if(isset($valores['punto_cuenta_recomendaciones'])) echo html_entity_decode($valores['punto_cuenta_recomendaciones']); ?></font>
    </div>
    <hr/>
    <div>
        <font class="f16b">Uso de Partida Presupuestaria: </font>
        <font class="f16n">
            <?php 
                if($valores['punto_cuenta_form_partida'] == 'S'){ ?>
                    Si <br/><br/>
                    <b>N° de Partida:</b> <?php echo $valores['punto_cuenta_partida']; ?><br/>
                    <b>Monto Solicitado:</b> <?php echo (($valores['punto_cuenta_monto'] != '') ? number_format(str_replace(',', '.', $valores['punto_cuenta_monto']), 2, ',', '.').' Bs.' : ' '); ?>
                <?php    
                } else {
                    echo 'No';
                }
            ?>
        </font>
    </div>
</div>

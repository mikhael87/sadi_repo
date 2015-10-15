<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <table width="100%">
            <tr>
                <td><font class="f16b">Materiales</font></td>
                <td><font class="f16b">Cantidad Aprobada</font></td>
                <td><font class="f16b">U. Medida</font></td>
            </tr>
            <?php 
            if(isset($valores['materiales_aprobados'])){
                $copias = $valores['materiales_aprobados']['copias'];
                
                foreach ($copias as $material) {
                    list($material_id, $cantidad) = explode('#', $material);
                    $material_detalle = Doctrine::getTable('Extenciones_Materiales')->find($material_id);
                    $material_medida = Doctrine::getTable('Extenciones_UnidadMedida')->find($material_detalle->getUnidadMedidaId());
                    echo '<tr><td>'.$material_detalle->getNombre().'</td><td width="50">'.$cantidad.'</td><td width="80">'.$material_medida->getNombre().'</td></tr>';
                }
            }
            ?>
            </table>
        </font>
    </div>
    <hr>
    <div>
        <font class="f16n"><?php if(isset($valores['materiales_observacion'])) echo $valores['materiales_observacion']; ?></font>
    </div>
    <div>
         <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$valores['id']; ?>" title="Descargar">
            <?php echo image_tag('icon/pdf.png'); ?>
        </a>
    </div>
</div>
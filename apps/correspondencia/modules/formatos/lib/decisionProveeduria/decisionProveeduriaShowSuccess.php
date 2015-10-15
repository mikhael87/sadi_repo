<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <table width="100%"><tr><th></th><th>Articulos</th><th>Cantidad</th><th>U. Medida</th></tr>
            <?php 
            if(isset($valores['articulos_aprobados'])){
                $articulos = $valores['articulos_aprobados'];
                
                $i=1; $contenido = '';
                foreach ($articulos as $articulo_id => $cantidad) {
                    
                    $articulo = Doctrine::getTable('Inventario_Articulo')->find($articulo_id);
                    $unidad_medida = Doctrine::getTable('Inventario_UnidadMedida')->find($articulo->getUnidadMedidaId());
                    
                    $contenido .=  '<tr>
                                        <td width="10">'.$i.'</td>
                                        <td width="260" align="left">'.$articulo->getNombre().'</td>
                                        <td width="60">'.$cantidad.'</td>
                                        <td width="70">'.$unidad_medida->getNombre().'</td>
                                    </tr>';
                    $i++;
                }
                
                echo $contenido;
            }            
            ?>
        </table>
    </div>
    <hr>
    <div>
        <font class="f16n"><?php if(isset($valores['articulos_observacion'])) echo $valores['articulos_observacion']; ?></font>
    </div>
</div>
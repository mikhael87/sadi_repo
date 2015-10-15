<style>
    .label_v { font-size: 9px; font-weight: bold }
    .content_v { font-size: 10px }
    .name_c { font-size: 13px; color: #2978BB }
    .unidad_c { font-size: 11px; color: #666 }
</style>

<div style="position: relative; min-height: 100px; width: 200px; max-height: 100px;" >
    <div id="list_conductores_refresh_<?php echo $vehiculos_vehiculo->getId() ?>" style="overflow-y: auto; max-height: 100px">
        <?php
        $conductores= Doctrine::getTable('Vehiculos_ConductorVehiculo')->conductorPorVehiculo($vehiculos_vehiculo->getId());

        if(count($conductores) > 0) {
            foreach($conductores as $conductor) {
                echo '<font class="label_v">Asignaci&oacute;n:</font> '.$conductor->getCondicion().'<br/>';
                echo '<font class="name_c">'.$conductor->getNombre().' '.$conductor->getApellido().'</font><br/>';

                $unidad= Doctrine::getTable('Organigrama_Unidad')->datosCargoUnidad($conductor->getCargoId());
                foreach($unidad as $unidad) {
                    echo '<font class="unidad_c">'.$unidad->getUnidadNombre().'</font><br/>';
                }
                echo '<hr/>';
            }
        }else {
            echo '<div style="text-align: center; padding-top: 20px"><font style="color: grey; font-size: 10">Sin conductores</font></div>';
        }
        ?>
    </div>
    
    <div id="div_dinamico_<?php echo $vehiculos_vehiculo->getId() ?>" style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 200px; min-height:92px; left: 50px; top: -17px; display: none; box-shadow: 3px 3px 6px #222222">
            <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:92px; padding: 5px; box-shadow: #777 0.1em 0.2em 0.1em;">
            <div style="top: -15px; left: -15px; position: absolute;">
                    <a href="#" onclick="javascript: conmutar(<?php echo $vehiculos_vehiculo->getId();?>); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
            </div>
            <div id='content_conductores_<?php echo $vehiculos_vehiculo->getId() ?>' style="padding-top: 10px;">
            </div>
        </div>
    </div>
</div>

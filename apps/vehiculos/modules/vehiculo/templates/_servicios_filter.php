<?php use_helper('jQuery'); ?>

<script>
    function show_services() {
        if($('#servicios_list').is(':hidden'))
            $('#servicios_list').show();
        else
            $('#servicios_list').hide();
    }
</script>

<tr class="sf_admin_form_row sf_admin_text sf_admin_filter_field_servicios_pendientes">
    <td>
        <label style="vertical-align: top" for="vehiculos_vehiculo_filters_servicios_pendientes">Servicios Pendientes</label>
    </td>
    <td>
        <input type="checkbox" style="vertical-align: middle" name="vehiculos_vehiculo_filters[serviciosPendientes][check]" id="vehiculos_vehiculo_filters_servicios_pendiente" onClick="show_services()" />&nbsp;<font style="color: #666; font-size: 10px; vertical-align: middle">Marque para mostrar veh&iacute;culos con servicios pendientes</font>
        
        <div id="servicios_list" style="display: none">
            <ul>
                <?php
                $servicios= Doctrine::getTable('Vehiculos_MantenimientoTipo')->findByStatus('A');

                foreach($servicios as $value) {
                    echo '<li style="list-style: none"><input type="checkbox" checked name="vehiculos_vehiculo_filters[serviciosPendientes][list][]" value="'. $value->getId() .'">&nbsp;'. $value->getNombre() .'</li>';
                }
                ?>
            </ul>
        </div>
        
<!--        <select name="vehiculos_vehiculo_filters[kilometraje_actual][param]" id="vehiculos_vehiculo_filters_kilometraje_actual_param">
            <option value=">" selected>mayor a</option>
            <option value="<">menor a</option>
        </select>
        
        <input type="text" name="vehiculos_vehiculo_filters[kilometraje_actual][text]" id="vehiculos_vehiculo_filters_kilometraje_actual" />-->
    </td>
</tr>
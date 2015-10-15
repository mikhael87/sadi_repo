<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label>Equipos anteriormente ingresados</label>
        <div class="content">
            <table>
                <tr>
                    <th style="min-width: 100px;">Tipo</th>
                    <th style="min-width: 100px;">Marca</th>
                    <th style="min-width: 150px;">Serial</th>
                    <th></th>
                </tr>
                <?php foreach ($equipos as $equipo) { ?>
                    <tr id="tr_equipo_anterior_<?php echo $equipo->getId(); ?>">
                        <td><?php echo $equipo->getMarca(); ?></td>
                        <td><?php echo $equipo->getTipo(); ?></td>
                        <td><?php echo $equipo->getSerial(); ?></td>
                        <td><a onclick="agregar_equipo_anterior(<?php echo $equipo->getId(); ?>,'<?php echo $equipo->getMarca(); ?>','<?php echo $equipo->getTipo(); ?>','<?php echo $equipo->getSerial(); ?>');" style="cursor: pointer;"><img src="/images/icon/tick.png"/></a></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
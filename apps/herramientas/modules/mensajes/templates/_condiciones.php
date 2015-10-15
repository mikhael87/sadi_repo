<?php $condiciones=Doctrine::getTable('Organigrama_CargoCondicion')->ordenado(); ?>
<?php $tipos=Doctrine::getTable('Organigrama_CargoTipo')->ordenado(); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_mensajes_condiciones">
    <div>
        <label for="public_mensajes_opciones">Condición del cargo</label>
        <div class="content">
            <select name="mensajes_condicion" id="mensajes_condicion">
                <option value=""></option>
                <?php foreach ($condiciones as $condicion): ?>
                <option value="<?php echo $condicion->getId(); ?>"><?php echo $condicion->getNombre(); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="help">Seleccione la condición de los receptores.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_mensajes_condiciones">
    <div>
        <label for="public_mensajes_opciones">Tipo de cargo</label>
        <div class="content">
            <select name="mensajes_tipo" id="mensajes_tipo">
                <option value=""></option>
                <?php foreach ($tipos as $tipo): ?>
                <option value="<?php echo $tipo->getId(); ?>"><?php echo $tipo->getNombre(); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="help">Seleccione el cargo de los receptores.</div>
    </div>
</div>

<div style="height: 5px; background-color: #939090;"></div>

<?php use_helper('jQuery'); ?>
<div class="error" id="flash_error_id" style="display: none;">No se ha definido un modo de almacenamiento para la serie documental seleccionada.</div>
<div id="cargando_ubicacion_id"></div>
<div id="div_ubicacion_expediente" style="display: none;">
    <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad">
        <div>
            <label for="archivo_expediente_unidad">Ubicación</label>
            <div id="div_unidad" class="content">
                <select id="select_unidad_ubicacion" name="unidad">
                    <option selected="selected" value=""></option>
                </select>
            </div>
            <div class="help">Seleccione la unidad donde se ubicara el expediente</div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_estante_id">
        <div>
            <label for="archivo_expediente_estante_id">Estante</label>
            <div id="div_estante" class="content">
                <select id="archivo_expediente_estante_id" name="archivo_expediente[estante_id]">
                    <option selected="selected" value=""></option>
                </select>
            </div>

            <div class="help"></div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_padre_id">
        <div>
            <label for="archivo_expediente_padre_id">Tramo</label>
            <div id="div_tramo" class="content">
                <select id="archivo_expediente_tramo" name="archivo_expediente[tramo]">
                    <option selected="selected" value=""></option>
                </select>
            </div>

            <div class="help"></div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_padre_id">
        <div>
            <label for="archivo_expediente_padre_id">Caja</label>
            <div id="div_caja" class="content" style="position: relative; height: 23px;">
                <select id="archivo_expediente_caja_id" name="archivo_expediente[caja]">
                    <option selected="selected" value=""></option>
                </select>
            </div>

            <div class="help">Si es archivado dentro de una caja seleccione la caja del estante</div>
        </div>
    </div>
</div>
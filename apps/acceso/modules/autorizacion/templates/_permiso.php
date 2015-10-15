<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_permiso">
    <div>
        <label for="acceso_autorizacion_modulo_permiso">Permiso</label>

        <div class="content">
            <?php if ($sf_user->getAttribute('autorizacion')=='punto_cuenta') { ?>
            <select name="acceso_autorizacion_modulo[permiso]" id="acceso_autorizacion_modulo_permiso">
                <option value="A">Agregar y Consultar</option>
                <option value="C">Consultar</option>
            </select>
            <?php } else { ?>
            <?php $this->getUser()->setFlash('error', 'Error en la asignación del permiso.'); ?>
            <script language='JavaScript'>location.href='acceso.php/index';</script>
            <?php } ?>
        </div>

        <div class="help">Seleccione el permiso que asigna.</div>
    </div>
</div>
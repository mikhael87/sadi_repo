<?php
    $prestamos_activos = Doctrine::getTable('Archivo_PrestamoArchivo')->prestamosActivos($sf_user->getAttribute('expediente_id'));
    $prestamo_fisico = 0;

    foreach ($prestamos_activos as $prestamo_activo) {
        if($prestamo_activo->getFisico()==TRUE){
            $prestamo_fisico++;
            $fecha_expiracion = $prestamo_activo->getFExpiracion();
        }
    }

    $documentos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndStatus($sf_user->getAttribute('expediente_id'),'A');

    $c_fisico= false; $c_digital= false;
    foreach ($documentos as $documento) {
        if($documento->getCopiaDigital()) {
            $c_digital= true;
        }
        if($documento->getCopiaFisica()) {
            $c_fisico= true;
        }
    }
?>

<div class="sf_admin_form_row sf_admin_boolean sf_admin_form_field_tipo_prestamo">
    <div>
        <label for="archivo_prestamo_tipo_prestamo">Tipo de Prestamo</label>
        <div class="content">
            <?php if($c_digital || $c_fisico):
                if($c_digital) :?>
                    Digital
                    <input type="checkbox" id="archivo_prestamo_tipo_prestamo_digital" name="archivo_prestamo_archivo[digital]" <?php echo (($c_digital && !$c_fisico) ? 'checked' : ''); ?>>&nbsp;&nbsp;&nbsp;
                <?php endif;
                if($c_fisico) : ?>
                    Fisico
                    <?php if($prestamo_fisico>0) { ?>
                        <input type="checkbox" id="archivo_prestamo_tipo_prestamo_fisico" name="archivo_prestamo_archivo[fisico]" disabled="disabled"> Disponible desde: <?php echo date('d-m-Y', strtotime($fecha_expiracion)); ?>
                    <?php } else { ?>
                        <input type="checkbox" id="archivo_prestamo_tipo_prestamo_fisico" name="archivo_prestamo_archivo[fisico]" <?php echo ((!$c_digital && $c_fisico) ? 'checked' : ''); ?>>
                    <?php }
                endif; ?>
            <?php else:
                echo image_tag('icon/error.png', array('style'=> 'vertical-align: middle')).' <font style="color: red">El expediente no posee documentos físicos ni digitales.</font>';
            endif; ?>
            <input type="hidden" name="val_tipo_prestamo" />
        </div>
    </div>
</div>
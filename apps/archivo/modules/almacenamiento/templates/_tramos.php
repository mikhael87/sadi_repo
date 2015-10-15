<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Tramos o gavetas del estante</label>
        <div class="content">
            <?php
            $estante = Doctrine::getTable('Archivo_Estante')->find($sf_user->getAttribute('estante_id'));

            for($i=1;$i<=$estante->getTramos();$i++){ ?>
            <input type="checkbox" name="tramos[<?php echo $i; ?>]"/> Tramo <?php echo $i; ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php } ?>
        </div>
        <div class="help">Seleccione los tramos o gavetas donde desea almacenar los expedientes de la serie documental</div>
    </div>
</div>
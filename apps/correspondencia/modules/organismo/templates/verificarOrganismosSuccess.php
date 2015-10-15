<?php use_helper('jQuery'); ?>

<div id="verificacion_organismo">
    <div class="error" style="background-color: transparent; color: red; border: 0px">Se detectaron nombres de organismo parecidos</div>
    <table style="min-width: 300px;">
        <tr>
            <td>
                <a href="javascript: saveOrganismo(0);">
                    <input type="hidden" id="organismo_nombre" value="<?php echo $datos_organismo_nombre; ?>"/>
                    <input type="hidden" id="organismo_siglas" value="<?php echo $datos_organismo_siglas; ?>"/>
                    <input type="hidden" id="organismo_tipo" value="<?php echo $datos_organismo_tipo; ?>"/>
                    <?php echo $datos_organismo_nombre.' - '.$datos_organismo_siglas; ?>
                </a>&nbsp;<font style="color: #ababab">(Nuevo)</font>
            </td>
        </tr>
        <?php foreach ($organismos_verificar as $organismo) { ?>
            <tr>
                <td>
                    <a href="javascript: saveOrganismo(<?php echo $organismo['id']; ?>)" >
                        <?php echo $organismo['nombre'].' - '.$organismo['siglas']; ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
    Seleccione el Organismo que emitio la correspondencia
</div>
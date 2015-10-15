<?php use_helper('jQuery'); ?>

<div>
    <div class="error" style="background-color: transparent; color: red; border: 0px">Se detectaron nombres de organismo parecidos</div>
    <table style="min-width: 300px;">
        <tr>
            <td>
                <a href="javascript: savePersona(0);">
                    <input type="hidden" id="persona_nombre" value="<?php echo $datos_persona; ?>"/>
                    <?php echo $datos_persona; ?>
                </a>
            </td>
        </tr>
        <?php foreach ($personas_verificar as $persona_id => $persona_nombre) { ?>
            <tr>
                <td>
                    <a href="#" onclick="savePersona(<?php echo $persona_id; ?>);">
                        <?php echo $persona_nombre; ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
    Seleccione la persona que emitio la correspondencia
</div>
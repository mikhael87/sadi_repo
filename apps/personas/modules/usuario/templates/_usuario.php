<div style="min-width: 100px;">
    <?php $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByPersonaId($personas_persona->getId()); ?>

    <div id="usuario_siglas_<?php echo $usuario->getId(); ?>" style="position: relative;">
        <table>
            <tr style='border: none'>
                <td style="min-width: 250px; border: none">
                    <font class='label_p'>Usuario: </font>
                    <font class='dato_p'><a href="#" onclick="conmutar_user(<?php echo $usuario->getId();?>,'siglas'); return false;" style="text-decoration: none" id="user_siglas_<?php echo $usuario->getId(); ?>"><?php echo $usuario->getNombre(); ?></a></font>
                </td>
            </tr>
            <tr style='border: none'>
                <td style="min-width: 250px; border: none">
                    <font class='label_p'>Perfil: </font>
                    <font class='dato_p'><?php echo $personas_persona->getPerfil(); ?></font>
                </td>
            </tr>
        </table>

        <div  style="position: relative; width: 10px; height: 10px" >
            <div id="tab_user_siglas_<?php echo $usuario->getId() ?>" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; width: 270px; min-height:92px; left: 0px; top: -17px; display: none">
                <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:92px; padding: 5px; box-shadow: #777 0.1em 0.2em 0.1em;">
                    <div style="top: -15px; left: -15px; position: absolute;">
                        <a href="#" onclick="conmutar_user(<?php echo $usuario->getId();?>,'siglas'); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
                    </div>
                    <table>
                        <tr>
                            <td>
                                <?php
                                $parts= explode('.', $usuario->getNombre());
                                ?>
                                <input size="12" maxlength="15" type="text" name="ext1" id="ext1_<?php echo $usuario->getId(); ?>" value="<?php echo $parts[0]; ?>"/>&nbsp;&nbsp;<b>.</b>&nbsp;
                                <input size="12" maxlength="15" type="text" name="ext2" id="ext2_<?php echo $usuario->getId(); ?>"  value="<?php echo $parts[1]; ?>"/><br/>
                                <font class="helpfont">Cambie o agregue caracteres al Nombre o Apellido</font>
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: right; width: 257px; background-color: #B7B7B7" id="renew_siglas_<?php echo $usuario->getId(); ?>">
                        <a href="#" onClick="checkuserSiglas(<?php echo $usuario->getId(); ?>); return false;" >Comprobar</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input name="aceptar" type="button" value="Aceptar" disabled="disabled" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
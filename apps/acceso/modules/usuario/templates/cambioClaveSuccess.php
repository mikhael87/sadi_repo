<form id="formpassword" name="formpassword" method="post" action="<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/cambioClave">
    <table width="100%">
        <tr>
            <td align="center"><br/><br/>
                <table width="700">
                    <tr>
                        <td align="justify" width="280">
                            <font class="f17n gris_oscuro">
                                <br/>
                                Por favor escriba su contraseña actual, seguidamente ingrese la nueva contraseña 
                                y repítala para verificar que la introdujo correctamente.
                                <br/><br/>
                                <b>Restricciones para la nueva contraseña:</b><br/><br/>
                                &#149;&nbsp;No debe ser el número identificador del usuario (cédula, RIF).<br/><br/>
                                &#149;&nbsp;No podrá repetir contraseñas anteriormente ingresadas.<br/><br/>
                                &#149;&nbsp;No pueden ser el mismo nombre de usuario.<br/><br/>
                                &#149;&nbsp;No pueden ser cadenas como '123456' o 'abcdef'<br/><br/>
                                &#149;&nbsp;Debe tener al menos seis caracteres<br/><br/>
                                <br/>
                            </font>
                            <font class="f17n rojo">
                                Recuerde que el acceso es personalizado e intransferible,
                                cualquier modificación de los datos en el sistema queda bajo su responsabilidad,
                                es por ello que no debe compartir la contraseña que esta ingresando.
                            </font>
                        </td>
                        <td align="center" width="100"><?php echo image_tag('icon/candado.png'); ?></td>
                        <td align="center" valign="middle">
                            <table width="100%">
                                <tr>
                                    <td colspan="2">
                                        <h2>Cambio de Contraseña</h2>
                                    </td>
                                </tr>
                                <?php if ($sf_user->hasFlash('notice')): ?>
                                <tr>
                                    <td colspan="2">
                                        <div class="tr_n gris_oscuro"><?php echo $sf_user->getFlash('notice') ?></div><br/>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($sf_user->hasFlash('error')): ?>
                                <tr>
                                    <td colspan="2">
                                        <div class="tr_e gris_oscuro"><font style="color: #E0DFE3"><?php echo $sf_user->getFlash('error') ?></font></div><br/>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td width="150">Usuario</td>
                                    <td width="157"><input name="user" type="hidden" value="<?php echo $usuario; ?>"><b><?php echo $usuario; ?></b></td>
                                </tr>
                                <tr>
                                    <td>Contraseña actual</td>
                                    <td><input name="actual" type="password"></td>
                                </tr>
                                <tr>
                                    <td>Contraseña nueva</td>
                                    <td><input name="nuevo" type="password"></td>
                                </tr>
                                <tr>
                                    <td>Repita contraseña nueva</td>
                                    <td><input name="repite" type="password"></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td valign="middle">
                                        <input name="cambiar" type = "submit" value="Cambiar"/>
                                        &nbsp;&nbsp;
                                        <?php echo image_tag('icon/delete.png'); ?>
                                        <?php
                                            if ($sf_user->getAttribute('usuario_id')) {
                                            // ############ PARA NO AGREGAR OTRO LAYOUT VALIDO SI ESTA LOGEADO Y CREO EL TD AMPLIO PARA EL MENU  ############
                                        ?>
                                        <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/session" class="vns">Cancelar</a>
                                        <?php
                                            } else {
                                        ?>
                                        <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario" class="vns">Cancelar</a>
                                        <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <br/><br/></td>
        </tr>
    </table>
</form>
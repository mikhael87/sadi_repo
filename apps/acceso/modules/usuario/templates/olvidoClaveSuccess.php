<br/><br/><br/><br/>
<table align="center" border ="0" width="500">
    <tr>
        <td colspan="2">
            <br/><br/><br/>
        </td>
    </tr>
    <tr class="password404">
        <td width="100" align="center">
            <?php echo image_tag('/images/icon/password_64.png', array('alt' => 'Contraseña olvidada')) ?>
        </td>
        <td>
            <div>
                <h1>¿Olvido su contraseña?</h1>
                <h5>Recuperela de forma automatica.</h5>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="justify">
            <br/>
            Se le enviará una contraseña temporal mediante correo electronico o mensaje de texto (SMS).<br/>
            <br/>
            Escriba su número de cédula para confirmar su identidad y haga clic en el boton "Enviar".<br/>
            
            <br/>
                <form name="form" method="post" action="<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/claveTemporal">
                <table width="500">
                    <tr>
                        <td colspan="2" height="5" bgcolor="#939090"></td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Cédula de Identidad&nbsp;&nbsp;</td>
                        <td><input name="cedula" type="text"/></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td valign="top"><input name="enviar" type = "submit" value="Enviar"/><br/><br/></td>
                    </tr>
                    <tr>
                        <td colspan="2" height="5" bgcolor="#939090"></td>
                    </tr>
                </table>
                </form>
            <ul>
                <div class='partial_link partial'>
                <?php echo link_to('Ir al inicio', sfConfig::get('sf_app_acceso_url').'usuario/session') ?>
                </div>
            </ul>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <br/><br/><br/>
        </td>
    </tr>
</table>
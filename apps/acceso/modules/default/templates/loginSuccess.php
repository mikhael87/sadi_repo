<table align="center" border ="0" width="500">
    <tr>
        <td colspan="2">
            <br/><br/><br/>
        </td>
    </tr>
    <tr class="permiso404">
        <td width="100" align="center">
            <?php echo image_tag('/icon/candado.png', array('alt' => 'login required', 'class' => 'sfTMessageIcon')) ?>
        </td>
        <td>
            <div class="sfTMessageWrap">
                <h1>Autentificación Requerida</h1>
                <h5>Esta pagina no es publica.</h5>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="sfTMessageInfo">
            <br/><b>¿Como auntentificarse?</b><br/>
            Usted debe ir a la página de inicio de sesión e introducir su usuario y contraseña.<br/><br/>

            <ul>
                <div class='partial_link partial'>
                <?php echo link_to('Ir al inicio de sesión', sfConfig::get('sf_app_acceso_url').'usuario') ?>
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
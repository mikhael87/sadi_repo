<table align="center" border ="0" width="500">
    <tr>
        <td colspan="2">
            <br/><br/><br/>
        </td>
    </tr>
    <tr class="alerta404">
        <td width="100" align="center">
            <?php echo image_tag('/images/icon/cancel48.png', array('alt' => 'Pagina no encontrada')) ?>
        </td>
        <td>
            <div>
                <h1>Página no encontrada</h1>
                <h5>El servidor retorno un error 404.</h5>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="justify">
            <br/><b>¿Escribio usted la dirección de esta página?</b><br/>
            Es posible que haya escrito la dirección (URL) incorrectamente. Compruebe para asegurarse de que tienes la ortografía correcta, capitalización, etc.<br/>
            <br/><b>¿Ha llegado a esta página desde un enlace desde otro lugar en este sistema?</b><br/>
            Si llegó a esta página desde otra parte de este sistema, por favor envíenos un correo electrónico a info@ondasystems.com para que podamos corregir nuestro error.<br/>
            
            <br/>
            <ul>
                <div class='partial_link partial'>
                <a href="javascript:history.go(-1)">Regresar a la página anterior</a>
                </div>
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
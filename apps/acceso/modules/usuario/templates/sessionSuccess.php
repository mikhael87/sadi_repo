<?php $session_usuario = $sf_user->getAttribute('session_usuario'); ?>
<table width="100%">
    <?php if ($sf_user->hasFlash('notice')): ?>
    <tr>
        <td colspan="2">
            <div class="tr_n"><?php echo $sf_user->getFlash('notice') ?></div><br/>
        </td>
    </tr>
    <?php endif; ?>
    <?php if ($sf_user->hasFlash('error')): ?>
    <tr>
        <td colspan="2">
            <div class="tr_e"><?php echo $sf_user->getFlash('error') ?></div><br/>
        </td>
    </tr>
    <?php endif; ?>

    <tr>
        <td>
            <table>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <?php if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$datospersona_list->getCi().'.jpg')){ ?>
                                        <img src="/images/fotos_personal/<?php echo $datospersona_list->getCi(); ?>.jpg" width="150"/><br/>
                                    <?php } else { ?>
                                        <img src="/images/other/siglas_photo_small_<?php echo $datospersona_list->getSexo().substr($datospersona_list->getCi(), -1); ?>.png" width="150"/><br/>
                                    <?php } ?>
                                </td>
                                <td>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                </td>
                                <td>
                                    <h2>
                                        <?php
                                            echo $datospersona_list->getPrimerNombre().' '.
                                                 $datospersona_list->getSegundoNombre().', '.
                                                 $datospersona_list->getPrimerApellido().' '.
                                                 $datospersona_list->getSegundoApellido().' ';
                                        ?>
                                    </h2>

                                    <b>Cédula</b>&nbsp;&nbsp;<?php echo $datospersona_list->getCi(); ?><br/><br/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td colspan="2"><h2>USUARIO <?php echo strtoupper($session_usuario['usuario_nombre']); ?></h2></td>
                            </tr>
                            <tr>
                                <td><b>Ultima conexión</b></td>
                                <td><?php echo date('d-m-Y g:i:s a', strtotime($session_usuario['ultima_conexion'])); ?></td>
                            </tr>
                            <tr>
                                <td><b>Cantidad de Visitas&nbsp;&nbsp;</b></td>
                                <td><?php echo $session_usuario['visitas']; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td width="271">
            <?php echo image_tag("organismo/logo_session.png"); ?>
        </td>

    </tr>
    <tr>
        <td>

        </td>
    </tr>
</table>
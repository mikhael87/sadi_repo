<table width="100%" cellpadding="10" cellspacing="10">
    <tr>
        <?php
        $dir = "images/temas/temas_mini/";
        $directorio = opendir($dir);
        $i = 0;
        while ($archivo = readdir($directorio)) {
            
            if (!($archivo == '.' or $archivo == '..' or $archivo == '.svn')) {
                $i++;
                $nombre = explode('.', $archivo);
                $nombre = str_replace('_', '<br/>', $nombre[0]);
                $nombre = str_replace('-', ' ', $nombre);
                ?>
                <td  align="center" valign="top">
                    <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>temas/agregar?t=<?php echo $archivo; ?>"><img src="/images/temas/temas_mini/<?php echo $archivo; ?>" width="300" style="border: 5px solid #848484;"/></a><br/>
                    <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>temas/agregar?t=<?php echo $archivo; ?>"><?php echo image_tag('icon/add.png'); ?></a>
                    <?php echo $nombre; ?><br/><br/>
                </td>
            <?php
            }

            if ($i == 3) {
                echo "</tr><tr>";
                $i = 0;
            }
        }
        closedir($directorio);
        ?> 
    </tr>
</table>
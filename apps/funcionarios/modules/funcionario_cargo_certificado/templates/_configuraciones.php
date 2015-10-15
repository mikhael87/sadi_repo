<div style="text-align: justify; overflow-x: no-display; overflow-y: auto; width: 190px; height: 240px;">
    <?php 
    $configuraciones = sfYaml::load($funcionarios_funcionario_cargo_certificado->getConfiguraciones());

    foreach ($configuraciones as $configuracion) {
        echo 'IP de acceso: '.$configuracion['ip'].'<br/>';
        echo '<font class="f11n">'.wordwrap($configuracion['configuracion'],32,' ',1).'</font><hr/>';
    }
    ?>
</div>
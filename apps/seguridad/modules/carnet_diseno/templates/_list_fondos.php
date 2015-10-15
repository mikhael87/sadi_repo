<div><font style="color: #666; font-weight: bold">Delanteros</font></div>
<div style="max-height: 300px">
    <?php
    $imagenes_fondo= sfYAML::load($seguridad_carnet_diseno->getImagenFondo());
    if($imagenes_fondo['frontal'][0] != '') {
        foreach($imagenes_fondo['frontal'] as $key => $val) {
            echo '<img src="/images/carnet/'. $val .'" style="border: 1px solid; width: 63px; height: 100px"/>&nbsp;';
        }
    }else {
        echo '<div style="border: 1px solid; width: 63px; height: 100px"></div>';
    }
    ?>
</div>

<br/><hr/><br/>

<div><font style="color: #666; font-weight: bold">Traseros</font></div>
<div style="max-height: 300px">
    <?php
    if($imagenes_fondo['trasero'][0] != '') {
        foreach($imagenes_fondo['trasero'] as $key => $val) {
            echo '<img src="/images/carnet/'. $val .'" style="border: 1px solid; width: 63px; height: 100px"/>&nbsp;';
        }
    }else {
        echo '<div style="border: 1px solid; width: 63px; height: 100px"></div>';
    }
    ?>
</div>
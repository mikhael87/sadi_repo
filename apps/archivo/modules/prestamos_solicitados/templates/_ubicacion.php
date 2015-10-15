<div style="position: relative; font-size: 13px; width: 220px;">
    <div style="position: relative; left: 0px;">
        <?php echo $archivo_expediente->getArchivo_Estante()->getOrganigrama_UnidadFisica(); ?>
    </div>
    <div style="height: 5px; clear: both;"></div>
    <div style="position: relative;">
        <div style="position: absolute; font-size: 8px; top: 0px; left: 0px;">
            Estante
        </div>
        <div style="position: absolute; top: 10px; left: 0px;">
            <img src="/images/icon/file-manager.png"/>
        </div>
        <div style="position: relative; padding-top: 10px; left: 20px; width: 130px;">
            <?php echo $archivo_expediente->getArchivo_Estante(); ?>
        </div>
        <div style="position: absolute; font-size: 8px; top: 0px; left: 150px;">
            Tramo
        </div>
        <div style="position: absolute; top: 10px; left: 150px;">
            <img src="/images/icon/file-manager-tramo.png"/>
        </div>
        <div style="position: absolute; top: 10px; left: 170px;">
            <?php echo $archivo_expediente->getTramo(); ?>
        </div>
    </div>
    <div style="height: 5px; clear: both;"></div>
    <?php if($archivo_expediente->getCajaId()) { ?>
    <div style="position: relative;">
        <div style="position: absolute; font-size: 8px; left: 0px;">
            Caja
        </div>
        <div style="position: absolute; top: 10px; left: 0px;">
            <img src="/images/icon/package_white.png"/>
        </div>
        <div style="position: relative; padding-top: 10px; left: 20px; width: 130px;">
            <?php echo $archivo_expediente->getArchivoCaja(); ?>
        </div>
    </div>
    <?php } ?>
</div>
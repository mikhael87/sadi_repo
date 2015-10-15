<div style="position: relative; width: 200px;">
    <div style="position: relative; font-size: 8px; left: 0px;">
        Nº de Expediente
    </div>
    <div style="position: relative; font-size: 13px; left: 0px; font-size:14px; color: #00008B; font-weight: bold;">
        <?php echo $archivo_expediente->getCorrelativo(); ?>
    </div>

    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Serie Documental
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo $archivo_expediente->getArchivo_SerieDocumental()->getNombre(); ?>
    </div>

    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Descriptores
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php
        $valores_clasificadores = Doctrine::getTable('Archivo_ExpedienteClasificador')->valoresClasificadores($archivo_expediente->getId());

        foreach ($valores_clasificadores as $valores) {
            echo '<b>'.$valores->getClasificador().'</b>: '.$valores->getValor()."<br/>";    
        } ?>
        <br/>
    </div>
</div>
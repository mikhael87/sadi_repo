<?php use_helper('jQuery'); ?>

<div style="position: relative; font-size: 13px; width: 500px; max-height: 110px;">
    <div style="width: 500px; max-height: 110px; overflow-y: auto; overflow-x: hidden;">
<?php
    $archivo_expediente = Doctrine::getTable('Archivo_Expediente')->find($sf_user->getAttribute('expediente_id'));
    $documentos_ids = ','.$archivo_prestamo_archivo->getDocumentosIds().',';

    $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($archivo_expediente->getSerieDocumentalId(),'null');

    foreach ($tipologias as $tipologia) {
        $adjuntos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndTipologiaDocumentalIdAndStatus($archivo_expediente->getId(),$tipologia->getId(),'A');

        foreach($adjuntos as $adjunto){
            if(preg_match('/,'.$adjunto->getId().',/', $documentos_ids)){
                $valores_etiquetas = Doctrine::getTable('Archivo_DocumentoEtiqueta')->valoresEtiquetas($adjunto->getId());

                $etiquetado = null;
                foreach ($valores_etiquetas as $valores) {
                    $etiquetado .= '<b>'.$valores->getEtiqueta().':</b> '.$valores->getValor()."<br/>";
                }
                ?>
                <div>
                    <a class="tooltip" title="[!]<?php echo $adjunto->getCorrelativo(); ?>[/!]<?php echo $etiquetado; ?>" href="/uploads/archivo/<?php echo $adjunto->getRuta(); ?>">
                        <?php echo $tipologia->getNombre(); ?>
                    </a>
                </div>
                <?php
            }
        }
     }

    $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($archivo_expediente->getSerieDocumentalId(),'permitidos',$archivo_expediente->getId());

    $cuerpo = null; $cadena_completa = null; $count_global = 0;
    foreach ($tipologias as $tipologia) {
        $adjuntos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndTipologiaDocumentalIdAndStatus($archivo_expediente->getId(),$tipologia->getId(),'A');

        if($tipologia->getCuerpo()!=$cuerpo) {
            if($count_global>0){
                if($count_cuerpo>0){
                    $cadena_cuerpo = '<div class="existente_'.$archivo_expediente->getId().'" style="display: block; color: black;"><b>'.$cuerpo.'</b></div>';
                    $cadena_completa .= $cadena_cuerpo.$cadena_documentos;
                }
            }

            $cuerpo = $tipologia->getCuerpo();
            $count_cuerpo = 0;
            $cadena_documentos = null;
        }

        $count=0;
        foreach($adjuntos as $adjunto){
            if(preg_match('/,'.$adjunto->getId().',/', $documentos_ids)){
                $valores_etiquetas = Doctrine::getTable('Archivo_DocumentoEtiqueta')->valoresEtiquetas($adjunto->getId());

                $etiquetado = null;
                foreach ($valores_etiquetas as $valores) {
                    $etiquetado .= '<b>'.$valores->getEtiqueta().':</b> '.$valores->getValor()."<br/>";
                }

                $comentario = '';

                $icon_fisico='<div style="position: absolute; left: -13px; top: 3px;"><img src="/images/icon/tag_good.png"/></div>';
                if($adjunto->getCopiaFisica()==0) {
                    $mensaje = '<b style=\'color: #FF0000;\'>NO SE ARCHIVO EL DOCUMENTO EN FISICO</b>';
                    $comentario .= ' <br/>'.$mensaje;
                    $icon_fisico.='<div style="position: absolute; left: -13px; top: 3px;" class="tooltip" title="'.$mensaje.'"><img src="/images/icon/tag_bad.png"/></div>';
                }

                $icon_digital='<div style="position: absolute; left: -7px; top: 3px;"><img src="/images/icon/tag_good.png"/></div>';
                if($adjunto->getCopiaDigital()==0) {
                    $ruta = '';
                    $mensaje = '<b style=\'color: #FF0000;\'>NO SE ARCHIVO EL DOCUMENTO EN DIGITAL</b>';
                    $comentario .= ' <br/>'.$mensaje;
                    $icon_digital='<div style="position: absolute; left: -7px; top: 3px;" class="tooltip" title="'.$mensaje.'"><img src="/images/icon/tag_bad.png"/></div>';
                } else {
                    $ruta = 'href="/uploads/archivo/'.$adjunto->getRuta().'"';
                }   

                $cadena_documentos .= '
                <div style="position: relative; left: 30px;">
                    <a '.$ruta.' class="tooltip" title="[!]'.$adjunto->getCorrelativo().'[/!]'.$etiquetado.$comentario.'">
                        '.$tipologia->getNombre().'
                    </a>'.$icon_fisico.$icon_digital.'
                </div>';

                $count++;
            }
        }
        $count_cuerpo += $count;

        $count_global++;
    }

    if($count_global>0){
        if($count_cuerpo>0){
            $cadena_cuerpo = '<div class="existente_'.$archivo_expediente->getId().'" style="display: block; color: black;"><b>'.$cuerpo.'</b></div>';
            $cadena_completa .= $cadena_cuerpo.$cadena_documentos;
        }
    }
    echo $cadena_completa;
?>
        </div>
</div>
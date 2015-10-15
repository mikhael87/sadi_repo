<?php use_helper('jQuery'); ?>

<script>
    function faltantes(id){
        $(".faltante_"+id).each(function() {
            if ($(this).is(":hidden")) {
                $(this).show("slow");
            } else {
                $(this).slideUp();
            }
        });
    };
</script>

<div style="position: relative; font-size: 13px; width: 440px; max-height: 140px;">
    <div style="width: 440px; max-height: 140px; overflow-y: auto; overflow-x: hidden;">
<?php
    $doc_faltantes=0;
    $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($archivo_expediente->getSerieDocumentalId(),'null');

    foreach ($tipologias as $tipologia) {
        $adjuntos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndTipologiaDocumentalIdAndStatus($archivo_expediente->getId(),$tipologia->getId(),'A');

        $count=0;
        foreach($adjuntos as $adjunto){
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
            $count++;
        }

        if($count==0){ $doc_faltantes++; ?>
            <div class="faltante_<?php echo $archivo_expediente->getId(); ?>" style="display: none; color: red;">
                <?php echo $tipologia->getNombre() ?>
            </div>
        <?php
        }
     }

    $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($archivo_expediente->getSerieDocumentalId(),'permitidos',$archivo_expediente->getId());

    $cuerpo = null; $cadena_completa = null; $count_global = 0;
    foreach ($tipologias as $tipologia) {
        $adjuntos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndTipologiaDocumentalIdAndStatus($archivo_expediente->getId(),$tipologia->getId(),'A');

        if($tipologia->getCuerpo()!=$cuerpo) {
            if($count_global>0){
                if($count_cuerpo==0)
                    $cadena_cuerpo = '<div class="faltante_'.$archivo_expediente->getId().'" style="display: none; color: FireBrick;"><b>'.$cuerpo.'</b></div>';
                else
                    $cadena_cuerpo = '<div class="existente_'.$archivo_expediente->getId().'" style="display: block; color: black;"><b>'.$cuerpo.'</b></div>';

                $cadena_completa .= $cadena_cuerpo.$cadena_documentos;
            }

            $cuerpo = $tipologia->getCuerpo();
            $count_cuerpo = 0;
            $cadena_documentos = null;
        }

        $count=0;
        $coincide_total = false;
        foreach($adjuntos as $adjunto){
            $valores_etiquetas = Doctrine::getTable('Archivo_DocumentoEtiqueta')->valoresEtiquetas($adjunto->getId());

            $etiquetado = null;
            foreach ($valores_etiquetas as $valores) {
                $etiquetado .= '<b>'.$valores->getEtiqueta().':</b> '.$valores->getValor()."<br/>";
            }

            if($sf_user->getAttribute('expediente_filters')){
                $expediente_filters = $sf_user->getAttribute('expediente_filters');

                if($expediente_filters['tipologia_documental_id']['id'] != '') {
                    if($expediente_filters['tipologia_documental_id']['id'] != $adjunto->getTipologiaDocumentalId()) {
                        $coincide_total = false;
                    } else {
                        $coincide_total = true;
                    }
                }

                if($coincide_total == true && count($expediente_filters['valores_documento']) > 0){
                    $herramientas = new herramientas();

                    $count_valores = count($expediente_filters['valores_documento']);
                    $count_valores_tmp = 0;

                    foreach ($expediente_filters['valores_documento'] as $etiqueta_id => $valor) {
                        $valor = $herramientas->insensitiveSearch($valor);

                        $documento_valor = Doctrine::getTable('Archivo_DocumentoEtiqueta')
                                        ->createQuery('a')
                                        ->where('a.documento_id = ?',$adjunto->getId())
                                        ->andWhere("a.etiqueta_id = ?", $etiqueta_id)
                                        ->andWhere("a.valor ~* ?", $valor)
                                        ->execute();

                        if(count($documento_valor) > 0) $count_valores_tmp++;
                    }

                    if($count_valores != $count_valores_tmp) 
                        $coincide_total = false;
                    else 
                        $coincide_total = true;
                }

                if($coincide_total == true && $expediente_filters['contenido_documento']['text'] != ''){
                    $herramientas = new herramientas();

                    $valor = $herramientas->insensitiveSearch($expediente_filters['contenido_documento']['text']);
                    $contenido_documento = Doctrine::getTable('Archivo_Documento')
                                        ->createQuery('a')
                                        ->where('a.id = ?',$adjunto->getId())
                                        ->andWhere('a.contenido_automatico ~* ?',$valor)
                                        ->execute();

                    if(count($contenido_documento) == 0) 
                        $coincide_total = false;
                    else 
                        $coincide_total = true;
                }
            }

            if($coincide_total == true){
                $u_i = '<u><i>';
                $u_f = '</i></u>';
                $coincide='<div style="position: absolute; left: -30px; top: -2px;"><img src="/images/icon/online.png"/></div>';
            } else {
                $u_i = '';
                $u_f = '';
                $coincide = '';
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
            <div onmouseover="javascript:accion_mostrar('.$adjunto->getId().')" onmouseout ="javascript:accion_ocultar('.$adjunto->getId().')" style="position: relative; left: 30px;">
                <a '.$ruta.' class="tooltip" title="[!]'.$adjunto->getCorrelativo().'[/!]'.$etiquetado.$comentario.'">
                    '.$u_i.$tipologia->getNombre().$u_f.'
                </a>';
            
                if($archivo_expediente->getUnidadId() == $sf_user->getAttribute('funcionario_unidad_id')) {
                    $cadena_documentos .= '<a href="expediente/'.$archivo_expediente->getId().'/editarDocumento?doc='.$adjunto->getId().'" id="edit_'.$adjunto->getId().'" style="display: none;" title="Editar">
                                                <img src="/images/icon/edit11.png"/>
                                            </a>
                                            <a href="expediente/'.$archivo_expediente->getId().'/eliminarDocumento?doc='.$adjunto->getId().'" id="delete_'.$adjunto->getId().'" style="display: none;" title="Eliminar" onclick="return confirm(\'¿Estas seguro de eliminar el documento archivado?\');">
                                                <img src="/images/icon/delete11.png"/>
                                            </a>';
                }else {
                    $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->PermisosUnidadFuncionario($archivo_expediente->getUnidadId(), $sf_user->getAttribute('funcionario_id'));
                    
                    if(count($unidades_autorizadas) > 0) {
                        if($unidades_autorizadas[0]['archivar'] && $unidades_autorizadas[0]['autorizada_unidad_id']== $archivo_expediente->getUnidadId()) {
                            $cadena_documentos .= '<a href="expediente/'.$archivo_expediente->getId().'/editarDocumento?doc='.$adjunto->getId().'" id="edit_'.$adjunto->getId().'" style="display: none;" title="Editar">
                                                        <img src="/images/icon/edit11.png"/>
                                                    </a>';
                        }
                        if($unidades_autorizadas[0]['anular'] && $unidades_autorizadas[0]['autorizada_unidad_id']== $archivo_expediente->getUnidadId()) {
                            $cadena_documentos .= '<a href="expediente/'.$archivo_expediente->getId().'/eliminarDocumento?doc='.$adjunto->getId().'" id="delete_'.$adjunto->getId().'" style="display: none;" title="Eliminar" onclick="return confirm(\'¿Estas seguro de eliminar el documento archivado?\');">
                                                        <img src="/images/icon/delete11.png"/>
                                                    </a>';
                        }
                    }
                }
            
            $cadena_documentos .= $coincide.$icon_fisico.$icon_digital.'</div>';
            
            $count++;
        }
        $count_cuerpo += $count;

        if($count==0){
            $doc_faltantes++;
            $cadena_documentos .= '
            <div class="faltante_'.$archivo_expediente->getId().'" style="display: none; color: red; position: relative; left: 15px;">
                '.$tipologia->getNombre().'
            </div>';
        }

        $count_global++;
    }

    if($count_global>0){
        if($count_cuerpo==0)
            $cadena_cuerpo = '<div class="faltante_'.$archivo_expediente->getId().'" style="display: none; color: FireBrick;"><b>'.$cuerpo.'</b></div>';
        else
            $cadena_cuerpo = '<div class="existente_'.$archivo_expediente->getId().'" style="display: block; color: black;"><b>'.$cuerpo.'</b></div>';

        $cadena_completa .= $cadena_cuerpo.$cadena_documentos;
    }
    echo $cadena_completa;
?>
        </div>
<?php if($doc_faltantes>0) { ?>
    <div onclick="javascript: faltantes(<?php echo $archivo_expediente->getId(); ?>); return false;" style="position: absolute; top: 0px; right: 0px;" title="Ver documentos faltantes">
        <img src="/images/icon/error.png"/>
    </div>
<?php } ?>
</div>

<?php
//echo "<pre>";
//print_r($sf_user->getAttribute('expediente_filters')); exit();

?>
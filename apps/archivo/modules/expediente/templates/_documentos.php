<div class="documents" style="position: relative; font-size: 13px; width: 440px; max-height: 140px;">
    <div style="width: 440px; max-height: 140px; overflow-y: auto; overflow-x: hidden;">
<?php
    $doc_faltantes=0;
    $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($archivo_expediente->getSerieDocumentalId(),'null');

    foreach ($tipologias as $tipologia) {
        $adjuntos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndTipologiaDocumentalIdAndStatus($archivo_expediente->getId(),$tipologia->getId(),'A');

        $count=0;
        foreach($adjuntos as $adjunto){
            $valores_etiquetas = Doctrine::getTable('Archivo_DocumentoEtiqueta')->valoresEtiquetas($adjunto->getId());

            // OJO VERIFICAR CODIGO VALIDADOR
            $etiquetado = null;
            foreach ($valores_etiquetas as $valores) {
                $eti= str_replace("\"", '&#34;', $valores->getEtiqueta());
                $eti= str_replace("\'", '&#39;', $eti);
                $val= str_replace("\"", '&#34;', $valores->getValor());
                $val= str_replace("\'", '&#39;', $val);
                $etiquetado .= '<b>'.$eti.':</b> '.$val."<br/>";
            }
        ?>
            <div onmouseover="javascript:accion_mostrar(<?php echo $adjunto->getId(); ?>)" onmouseout ="javascript:accion_ocultar(<?php echo $adjunto->getId(); ?>)">
                <a class="tooltip" title="[!]<?php echo $adjunto->getCorrelativo(); ?>[/!]<?php echo $etiquetado; ?>" href="/uploads/archivo/<?php echo $adjunto->getRuta(); ?>">
                    <?php echo $tipologia->getNombre(); ?>
                </a>
                <a href="expediente/<?php echo $archivo_expediente->getId(); ?>/editarDocumento?doc=<?php echo $adjunto->getId(); ?>" id="edit_<?php echo $adjunto->getId(); ?>" style="display: none;" title="Editar">
                    <img src="/images/icon/edit11.png"/>
                </a>
                <a href="expediente/<?php echo $archivo_expediente->getId(); ?>/eliminarDocumento?doc=<?php echo $adjunto->getId(); ?>" id="delete_<?php echo $adjunto->getId(); ?>" style="display: none;" title="Eliminar" onclick="return confirm('¿Estas seguro de eliminar el documento archivado?');">
                    <img src="/images/icon/delete11.png"/>
                </a>
                <?php if(strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'jpg' || strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'jpeg' || strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'png' || strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'gif'): ?>
                <a href="/uploads/archivo/<?php echo $adjunto->getRuta(); ?>" target="_blank" class="prev" id="preview_<?php echo $adjunto->getId(); ?>" style="display: none;" >
                    <img src="/images/icon/view13.png"/>
                </a>
                <?php endif; ?>
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

    $cuerpo = 'X'; $cadena_completa = null; $count_global = 0; $i=1;
    foreach ($tipologias as $tipologia) {
        $adjuntos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndTipologiaDocumentalIdAndStatus($archivo_expediente->getId(),$tipologia->getId(),'A');

        if($tipologia->getCuerpo()!=$cuerpo) {
            if($count_global>0){
                if($count_cuerpo==0)
                    $cadena_cuerpo = '<div class="existente_'.$archivo_expediente->getId().' tooltip" title="No se han archivado documentos en este cuerpo" style="display: block; color: FireBrick;"><b>'.$cuerpo.'</b></div>';
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
            
            if($adjunto->getCorrespondenciaId()==NULL){
                $icon_correspondencia='';
                
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
            } else {
                $ruta = '';
                $icon_digital = '';
                $icon_fisico = '';
                $u_i .= '<fond style="color: #0D89EC;">';
                $u_f = '</fond>'.$u_f;
                
                $mensaje = '<b style=\'color: #0D89EC;\'>DOCUMENTO ARCHIVADO MEDIANTE CORRESPONDENCIA</b>';
                
                $comentario .= ' <br/>'.$mensaje."<br/>----------------------------------------------------------------------------------------";
                
                $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($adjunto->getCorrespondenciaId());
                
                $formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($adjunto->getCorrespondenciaId());
                $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($formato[0]->getTipoFormatoId());
                $parametros = sfYaml::load($tipo_formato[0]->getParametros());
                
                if($parametros['options_object']['descargas']['pdf']=='true') {
                    $ruta = sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$formato[0]->getId();
                }
                
                $comentario .= "<br/><b>Documento:</b> ".strtr(strtoupper($tipo_formato[0]->getNombre()),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                $comentario .= "<br/><b>N° de Envio:</b> <fond style='color: #0D89EC;'>".$correspondencia->getNCorrespondenciaEmisor()."</fond>";
                $comentario .= "<br/><b>Fecha de envio:</b> ".date('d-m-Y h:m a', strtotime($correspondencia->getFEnvio()));
                
                
                $icon_correspondencia='<div style="position: absolute; left: -16px; top: 1px;" class="tooltip" title="'.$mensaje.'"><img src="/images/icon/mail12.png"/></div>';
            }
            if($ruta == '') $etiqueta = 'fond'; else $etiqueta = 'a';
                
            $cadena_documentos .= '
            <div onmouseover="javascript:accion_mostrar('.$adjunto->getId().')" onmouseout ="javascript:accion_ocultar('.$adjunto->getId().')" style="position: relative; left: 30px;">
                <'.$etiqueta.' '.$ruta.' class="tooltip" title="[!]'.$adjunto->getCorrelativo().'[/!]'.$etiquetado.$comentario.'">
                    '.$u_i.$tipologia->getNombre().' <i>('.date('d-m-Y h:m a', strtotime($adjunto->getCreatedAt())).')</i>'.$u_f.'
                </'.$etiqueta.'>';
            
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
                    if(strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'jpg' || strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'jpeg' || strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'png' || strtolower(substr(strrchr($adjunto->getRuta(), '.'), 1)) == 'gif') {
                        $cadena_documentos .= '<a href="/uploads/archivo/'.$adjunto->getRuta().'" target="_blank" class="prev" id="preview_'.$adjunto->getId().'" style="display: none;" >
                                                <img src="/images/icon/view13.png"/>
                                               </a>';
                    }
                }
            
            $cadena_documentos .= $coincide.$icon_fisico.$icon_digital.$icon_correspondencia.'</div>';
            
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
            $cadena_cuerpo = '<div class="existente_'.$archivo_expediente->getId().' tooltip" title="No se han archivado documentos en este cuerpo" style="display: block; color: FireBrick;"><b>'.$cuerpo.'</b></div>';
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
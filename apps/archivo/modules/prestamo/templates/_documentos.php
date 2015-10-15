<?php use_helper('jQuery'); ?>

<div class="sf_admin_form_row sf_admin_boolean sf_admin_form_field_documentos_ids">
    <div>
        <label for="archivo_prestamo_tipo_prestamo">Documentos</label>
        <div class="content">
            <table id="documentos_a_prestar_table" name="documentos_a_prestar_table">
            <?php
                $documentos = Doctrine::getTable('Archivo_Documento')->findByExpedienteIdAndStatus($sf_user->getAttribute('expediente_id'),'A');

                foreach ($documentos as $documento) {
                    echo "<tr>";
                        echo "<td><input class='documentos_prest' type='checkbox' value='".$documento->getId()."' name='prestamo_archivo[documentos_ids][]' checked></td>";
                        
                        $tipologia = Doctrine::getTable('Archivo_TipologiaDocumental')->find($documento->getTipologiaDocumentalId());
                        echo "<td>";
                            echo $tipologia->getNombre().' ('.$documento->getCorrelativo().")";
                        echo "</td>";
                    echo "</tr>";
                }
            ?>
            </table>
            <input type='hidden' name='val_doc'/>
        </div>
    </div>
</div>
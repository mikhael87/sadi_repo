<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_adjunto">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'materiales_aprobados')) ?>

    <div>
        <label for="memorandum_adjunto">Materiales Solicitados</label>
        <div class="content" style="width: 650px;">
            <table class="lista">
                <tr>
                    <th>Material</th>
                    <th>Solicitado</th>
                    <th>Disponible</th>
                    <th>Aprobado</th>
                </tr>
                
                <?php 
                    $materiales_solicitados = Doctrine::getTable('Correspondencia_Formato')->
                            findOneByCorrespondenciaId($sf_user->getAttribute('correspondencia_padre_id'));

                
                    $materiales = explode(':',$materiales_solicitados->getCampoUno());

                    foreach ($materiales as $material)
                    {
                        list($material_id,$material_cantidad) = explode ('#',$material);
                        $material_detalle = Doctrine::getTable('Extenciones_Materiales')->find($material_id);

                        $aprobado = $material_cantidad; $style = '';
                        if($material_detalle->getStop()<$material_cantidad){
                            $aprobado = $material_detalle->getStop();
                            $style = "style = 'color:red;'";
                        }
                        
                        $cadena = "<tr>";
                        $cadena .= "<td ".$style.">".$material_detalle->getNombre()."</td>";
                        $cadena .= "<td>".$material_cantidad."</td>";
                        $cadena .= "<td ".$style."><b>".$material_detalle->getStop()."</b></td>";
                        $cadena .= "<td><input name='correspondencia[formato][materiales_aprobados][".$material_id."]' maxlength='4' size='3' type='text' value='".$aprobado."'/></td>";
                        $cadena .= "</tr>";
                        echo $cadena;
                    }
                ?>
                
            </table>
        </div>
    </div>

    <div class="help"></div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'materiales_observacion')) ?>

    <div>
        <label for="nombre_etiqueta">Observación</label>
        <div class="content">
            <textarea name="correspondencia[formato][materiales_observacion]" value=""/>
        </div>
    </div>
</div>

</fieldset>
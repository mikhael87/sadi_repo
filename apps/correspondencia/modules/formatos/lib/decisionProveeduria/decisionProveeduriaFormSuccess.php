<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_articulos_aprobados">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'articulos_aprobados')) ?>

    <div>
        <label for="articulos_aprobados">Articulos Solicitados</label>
        <div class="content" style="width: 650px;">
            <table class="lista">
                <tr>
                    <th></th>
                    <th>Articulo</th>
                    <th>Solicitado</th>
                    <th>Disponible</th>
                    <th>Aprobado</th>
                </tr>
                
                <?php 
                    $articulos_solicitados = Doctrine::getTable('Correspondencia_Formato')->
                            findOneByCorrespondenciaId($sf_user->getAttribute('correspondencia_padre_id'));

                    $articulos_solicitados = sfYaml::load($articulos_solicitados->getCampoUno());
                            
                    foreach ($articulos_solicitados as $articulo_id => $cantidad)
                    {
                        $articulo = Doctrine::getTable('Inventario_Articulo')->cantidadActualArticulos(array($articulo_id));
                        

                        $aprobado = $cantidad; $style = '';
                        if($articulo[0]->getCantidadActual()<$cantidad){
                            $aprobado = $articulo[0]->getCantidadActual();
                            $style = "style = 'color:red;'";
                        }
                        
                        $cadena = "<tr>";
                        $cadena .= "<td></td>";
                        $cadena .= "<td ".$style.">".$articulo[0]->getNombre()."</td>";
                        $cadena .= "<td>".$cantidad."</td>";
                        $cadena .= "<td ".$style."><b>".$articulo[0]->getCantidadActual()."</b></td>";
                        $cadena .= "<td><input name='correspondencia[formato][articulos_aprobados][".$articulo_id."]' maxlength='4' size='3' type='text' value='".$aprobado."'/></td>";
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
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'articulos_observacion')) ?>

    <div>
        <label for="articulos_observacion">Observación</label>
        <div class="content">
            <textarea name="correspondencia[formato][articulos_observacion]" value=""/>
        </div>
    </div>
</div>

</fieldset>
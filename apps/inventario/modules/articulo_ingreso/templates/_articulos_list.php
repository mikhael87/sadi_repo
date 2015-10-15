<?php
$articulos_comprados = Doctrine::getTable('Inventario_Inventario')->articulosComprados($inventario_articulo_ingreso->getId());

echo '<table style="min-width: 300px;">';
foreach ($articulos_comprados as $articulo_comprado) {
    echo '<tr><td>'.$articulo_comprado->getArticulo().'</td>
              <td>'.$articulo_comprado->getUnidadMedida().'</td>
              <td title="Cantidad inicial">'.$articulo_comprado->getCantidadInicial().'</td>
              <td title="Cantidad actual">'.$articulo_comprado->getCantidadActual().'</td>    
          </tr>';
}
echo '</table>';
?>
<table>
    <tr>
        <th>Empresa</th>
        <th>Ganados</th>
        <th>Perdidos</th>
    </tr>
<?php
foreach($estadistica_datos as $values) {
    
    ?>
    <tr>
        <td><?php echo $values['nombre'] ?></td>
        <td style="text-align: center"><?php echo $values['ganados'] ?></td>
        <td style="text-align: center"><?php echo $values['perdidos'] ?></td>
    </tr>
    <?php
}
?>
</table>

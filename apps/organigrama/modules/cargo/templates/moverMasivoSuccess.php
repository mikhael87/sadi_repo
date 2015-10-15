<h1>Movimiento de Cargos de forma masiva</h1>

<form action="movidoMasivo">
<table>
    <tr>
        <th>Unidad Destino</th>
        <td>
            <?php echo $form['padre_id']->renderError() ?>
            <?php echo $form['padre_id'] ?>
        </td>
    </tr>
    <tr>
        <td>
            <input type="submit" value="Guardar" />
        </td>
    </tr>
</table>
</form>
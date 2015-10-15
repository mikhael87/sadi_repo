<h1>Movimiento de Cargo</h1>

<form action="movido">
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
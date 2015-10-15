<form id="form_reciboPago">
    <input type="hidden" name="datos[tipo]" value="reciboPago">
    <div style="position: relative; padding-top: 5px;">
        Desde:
        <div style="position: absolute; top: 0px; left: 50px;">
            <select name="datos[param][recibo_desde]">
                <option value=""><- quincena -></option>
                <option value="1">1° quincena</option>
                <option value="2">2° quincena</option>
            </select>
            <select name="datos[param][mes_desde]">
                <option value=""><-&nbsp;&nbsp;&nbsp;mes&nbsp;&nbsp;&nbsp;-></option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>

            <select name="datos[param][anio_desde]">
                <option value=""><- año -></option>
                <?php
                $anio_ingreso = 2011;
                for($i=$anio_ingreso;$i<=date('Y');$i++){
                ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <br/>
    <div style="position: relative">
        Hasta:
        <div style="position: absolute; top: -5px; left: 50px;">
            <select name="datos[param][recibo_hasta]">
                <option value=""><- quincena -></option>
                <option value="1">1° quincena</option>
                <option value="2">2° quincena</option>
            </select>
            <select name="datos[param][mes_hasta]">
                <option value=""><-&nbsp;&nbsp;&nbsp;mes&nbsp;&nbsp;&nbsp;-></option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>

            <select name="datos[param][anio_hasta]">
                <option value=""><- año -></option>
                <?php
                $anio_ingreso = 2011;
                for($i=$anio_ingreso;$i<=date('Y');$i++){
                ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</form>

<div class="help">Seleccione el rango de recibos de pago que desea descargar.</div>
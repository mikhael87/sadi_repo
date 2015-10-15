<div style="position: relative; min-height: 100px; width: 120px;">
    <div class="f10n">Posible fecha de ingreso</div>
    <font class="f10n">Desde:</font> <?php echo date('d-m-Y', strtotime($seguridad_preingreso->getFIngresoPosibleInicio())); ?><br/>
    <font class="f10n">Hasta:</font> <?php echo date('d-m-Y', strtotime($seguridad_preingreso->getFIngresoPosibleFinal())); ?><br/>
</div>
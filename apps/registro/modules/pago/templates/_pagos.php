<div style="min-width: 300px">
    <?php
    //CALCULO DE PAGO
    $disciplinas= Doctrine::getTable('Operaciones_RegistroDisciplina')->findByRegistroIdAndStatus($empresas_empresa->getRegistro(), 'A');
    
    $dic_pagas= array(); //DISCIPLINAS PAGADAS EN CASO DE PAGO UNICO POR DISCIPLINA
    $dic_to_count= 0; //CANTIDAD DE DISCIPLINAS FINAL PARA CONTABILIZAR MONTO
    foreach($disciplinas as $value) {
        $modo= Doctrine::getTable('Operaciones_Disciplina')->modoCobro($value->getDisciplinaVarianteId());

        if($modo[0][1] == 'U') {
            if(!in_array($modo[0][2], $dic_pagas)) {
                $dic_to_count++;
                $dic_pagas[]= $modo[0][2];
            }
        }elseif($modo[0][1] == 'T') {
            $dic_to_count++;
        }
    }

    $deuda= ($dic_to_count * 3000);
    
    
    $pagos= Doctrine::getTable('Operaciones_RegistroPago')->pagosPorRegistro($empresas_empresa->getRegistro());
    
    $total_pago= 0;
    foreach($pagos as $value) {
        $total_pago+=  $value->getMonto();
    }
    
    ?>
    <font style="font-size: 13px; color: #666">Total pagado:</font><br/>
    &nbsp;&nbsp;&nbsp;&nbsp;<font style="font-size: 17px; font-weight: bold"><?php echo (($total_pago< $deuda)?'<font style="color:red">'.number_format($total_pago, 2, ',', '.').'</font>' : '<font style="color:green">'.$total_pago.'</font>') ?>&nbsp;Bs.</font>
    <?php
    if($total_pago< $deuda) { ?>
        <br/>&nbsp;&nbsp;&nbsp;&nbsp;<font style="font-size: 10px; color: #666"><?php echo number_format(($deuda - $total_pago), 2, ',', '.')  ?>&nbsp;Bs.</font>
    <?php } ?>
    <br/><br/>
    <font style="font-size: 13px; color: #666">Deuda total:</font><br/>
    &nbsp;&nbsp;&nbsp;&nbsp;<font style="font-size: 17px; font-weight: bold"><?php echo number_format($deuda, 2, ',', '.'); ?>&nbsp;Bs.</font>
</div>

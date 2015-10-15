<?php
    $usuario = Doctrine::getTable('Acceso_Usuario')->find($acceso_accion_delegada->getUsuarioDelegadoId());
    $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($usuario->getUsuarioEnlaceId());

    echo $funcionario->getPrimerNombre().' '.
         $funcionario->getSegundoNombre().', '.
         $funcionario->getPrimerApellido().' '.
         $funcionario->getSegundoApellido();
    
    $actual = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual($funcionario->getId());
    
    if(count($actual)>0)
    {
        $actual = $actual[0];
        $unidad = $actual->getUnidad();
        $cargo = $actual->getCargoTipo();
    }
    else
    {
        $unidad = '<font style="color: red;">Sin Asignación</font>';
        $cargo = '<font style="color: red;">Sin Cargo</font>';
    }
?>

<div style="position: relative; height: 100px; width: 430px;">
    <div style="position: absolute; top: 0px; width: 350px;">
        <img src="/images/other/barra_trans.png" width="350" height="1"/>
    </div>
    <div style="position: absolute; top: 15px; width: 430px; color: #9d9d9d;">
        <b>Ubicación actual:</b> <?php echo $unidad; ?><br/>
        <b>Cargo:</b> <?php echo $cargo; ?>
    </div>
</div>
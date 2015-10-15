<div style="min-width: 100px;">
    <u style="color: #60676a;" class="f10b">Correo Insti.:</u><br/><?php echo $funcionarios_funcionario->getEmailInstitucional(); ?><br/>
    <u style="color: #60676a;" class="f10b">Correo Pers.:</u><br/><?php echo $funcionarios_funcionario->getEmailPersonal(); ?><br/>
    <u style="color: #60676a;" class="f10b">Telf. Móvil:</u><br/><?php echo $funcionarios_funcionario->getTelfMovil(); ?><br/>
    <u style="color: #60676a;" class="f10b">Extensiónes:</u>
    <?php 
    $cargos_actuales = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual($funcionarios_funcionario->getId());

    if(count($cargos_actuales)>0) { 
        foreach ($cargos_actuales as $cargo) {
            $extenciones = Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($cargo->getCargoId());

            foreach ($extenciones as $extencion) {
                echo $extencion->getTelefono().'; ';
            }
        }
    }


    ?>
    <br/>
</div>

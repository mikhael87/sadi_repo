<?php
$sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
$funcionario_unidades = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($sf_user->getAttribute('funcionario_id'));
$funcionarios_unidades= Array();
for($i=0; $i< count($funcionario_unidades); $i++)
    $funcionarios_unidades[]= $funcionario_unidades[$i]['unidad_id'];

    $detalles = '';
    if($sf_user->getAttribute('accion_delegada')){
        $parametros = '';
        switch ($sf_user->getAttribute('accion_delegada')) {
            case "firmar_correspondencia":
                $accion = "Firmar Correspondencia";
                $detalles = "Con esta acción delegas la firma de la correspondencia a otra persona, 
                      es decir la persona que selecciones podra firmar todas aquellas correspondencias que salgan a tu nombre.<br/>
                      ¡Cerciorece de que la persona seleccionada sea de su gran confianza!.";
                break;
            case "administrar_rrhh":
                $accion = "Administracion de RRHH";
                $detalles = "Con esta acción delegas la visualizacion y edicion de los modulos de RRHH a otra persona.<br/> 
                        Es decir la persona que selecciones podra visualizar los reportes de RRHH que permitas, asi como editar informacion.<br/>
                        ¡Cerciorece de que la persona seleccionada sea de su equipo de trabajo!.";
                break;
            case "gestionar_calendario":
                $accion = "Gestion del calendario";
                $detalles = "Con esta acción delegas la visualizacion o edicion de tu calendario a otra persona.<br/> 
                        Es decir la persona que selecciones podra visualizar o editar los eventos de tu calendario, asi como invitar funcionarios y personas a los mismos.<br/>
                        ¡Cerciorece de que la persona seleccionada sea de su equipo de trabajo!.";
                break;
            case "OtraCosaMas":
                $accion = "otra";
                break;
            default:
                $accion = "Opción no valida";
        }
        
    } else {
        // AQUI VAN TODAS LAS DELEGACIONES EN FORMA DE COMBO DE ACCIONES DIRECTAMENTE DESDE UN MODULO
    }
?>

<div class="sf_admin_form_row sf_admin_foreignkey">
    <div>
        <label for="">Acción a Delegar</label>
        <div class="content">
            <b class="f19b"><?php echo $accion; ?></b>
        </div>
        <div class="help"><?php echo $detalles; ?></div>
    </div>
</div>

<?php if($sf_user->getAttribute('accion_delegada')=="administrar_rrhh") { ?>
<div class="sf_admin_form_row sf_admin_foreignkey">
    <div>
        <label for="">Opciones</label>
        <div class="content">
            <?php if(in_array($sf_oficinasClave['recursos_humanos']['seleccion'], $funcionarios_unidades)) : ?>
                <input type="checkbox" name="parametros[ver_vacaciones_global]"/>&nbsp;Ver reporte global de vacaciones<br/>
                <input type="checkbox" name="parametros[editar_vacaciones]"/>&nbsp;Editar dias pendientes de vacaciones<br/>
                <input type="checkbox" name="parametros[ver_permisos_global]"/>&nbsp;Ver reporte global de permisos<br/>
                <input type="checkbox" name="parametros[ver_reposos_global]"/>&nbsp;Ver reporte global de reposos<br/>
            <?php else : ?>
                <input type="checkbox" name="parametros[ver_vacaciones_unidad]"/>&nbsp;Ver reporte de <b>vacaciones</b> de la unidad<br/>
                <input type="checkbox" name="parametros[ver_permisos_unidad]"/>&nbsp;Ver reporte de <b>permisos</b> de la unidad<br/>
                <input type="checkbox" name="parametros[ver_reposos_unidad]"/>&nbsp;Ver reporte de <b>reposos</b> de la unidad<br/>
            <?php endif; ?>
        </div>
        <div class="help"></div>
    </div>
</div>
<?php } else if ($sf_user->getAttribute('accion_delegada')=="gestionar_calendario") { ?>
<div class="sf_admin_form_row sf_admin_foreignkey">
    <div>
        <label for="">Opciones</label>
        <div class="content">
                <input type="checkbox" name="parametros[ver_calendario]"/>&nbsp;Ver calendario<br/>
                <input type="checkbox" name="parametros[editar_calendario]"/>&nbsp;Editar calendario<br/>
                <input type="checkbox" name="parametros[invitaciones_calendario]"/>&nbsp;Aceptar invitaciones<br/>
        </div>
        <div class="help"></div>
    </div>
</div>
<?php } ?>
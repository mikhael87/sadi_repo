<script type="text/javascript" src="/js/jqueryTooltip.js"></script>
<style>
    .without_border {
        border-width: 0px !important
    }
    
    .actual {
        background-color: #f2f2f2;
    }
</style>
<div id="tabla_unidad" class="tabla_historico_reporte">
    <table style="width: auto;" class="trans">
        <tr>
            <th style="text-align: left; max-width: 300px" colspan="2"><?php echo html_entity_decode($unidad->getNombre()) ?></th>
        </tr>
        <?php
        $unidad_consulta= $unidad->getId();
        foreach ($grupo as $funcionario) { ?>
            <tr>
                <td style="width: 200px;">
                    <font class='f16n'>
                    <?php echo $funcionario->getPersona(); ?>
                    </font>
                </td>
                <td>
                    <table>
                        <tr class="without_border" style="font-size: 11px">
                            <td class="without_border" style="max-width: 150px; min-width: 150px">Unidad</td>
                            <td class="without_border actual">Archivar</td>
                            <td class="without_border">Leer</td>
                            <td class="without_border actual">Prestar</td>
                            <td class="without_border">Anular</td>
                            <td class="without_border actual">Administrar</td>
                            <td class="without_border" style="max-width: 100px; min-width: 100px">Hecho por</td>
                            <td class="without_border">Desde</td>
                            <td class="without_border">Hasta</td>
                        </tr>
                        <?php
                        $permisos_funcionario= Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioHistorico($funcionario->getId(), $unidad_consulta);

                        foreach ($permisos_funcionario as $permiso) {
                            $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($permiso->getDependenciaUnidadId());
                            ?>
                            <tr class="without_border" style="font-size: 10px">
                                <td class="without_border"><?php echo ((strlen($unidad->getNombre()) > 28) ? substr($unidad->getNombre(), 0, 28).'<x style="cursor: pointer" class="tooltip" title="'. html_entity_decode($unidad->getNombre()) .'">...</x>' : html_entity_decode($unidad->getNombre())); ?></td>
                                <td class="without_border actual" style="text-align: center"><?php echo ($permiso->getArchivar())? image_tag('icon/online.png', array('size'=>'12x12')) : image_tag('icon/offline.png', array('size'=>'12x12')); ?></td>
                                <td class="without_border" style="text-align: center"><?php echo ($permiso->getLeer())? image_tag('icon/online.png', array('size'=>'12x12')) : image_tag('icon/offline.png', array('size'=>'12x12')); ?></td>
                                <td class="without_border actual" style="text-align: center"><?php echo ($permiso->getPrestar())? image_tag('icon/online.png', array('size'=>'12x12')) : image_tag('icon/offline.png', array('size'=>'12x12')); ?></td>
                                <td class="without_border" style="text-align: center"><?php echo ($permiso->getAnular())? image_tag('icon/online.png', array('size'=>'12x12')) : image_tag('icon/offline.png', array('size'=>'12x12')); ?></td>
                                <td class="without_border actual" style="text-align: center"><?php echo ($permiso->getAdministrar())? image_tag('icon/online.png', array('size'=>'12x12')) : image_tag('icon/offline.png', array('size'=>'12x12')); ?></td>
                                <td class="without_border"><?php echo $permiso->getUserUpdate(); ?></td>
                                <td class="without_border" style="text-align: left"><?php echo date('d-m-Y h:m A', strtotime($permiso->getCreatedAt())); ?></td>
                                <td class="without_border" style="text-align: left"><?php echo (($permiso->getStatus()== 'A') ? '<font style="color: green">Actualmente activo</font>' : date('d-m-Y h:m A', strtotime($permiso->getUpdatedAt()))); ?></td>
                            </tr>
                        <?php } ?>
                        </table>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
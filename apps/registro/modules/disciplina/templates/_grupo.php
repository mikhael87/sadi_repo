<?php
$cadena= '<div style="max-width: 150px; text-align: center" id="div_grupo_'.$operaciones_registro_disciplina->getId().'">';
if(count($operaciones_registro_disciplina->getGrupoId()) != '') {
    $grupo= Doctrine::getTable('Operaciones_Grupo')->find($operaciones_registro_disciplina->getGrupoId());
    
    $cadena.= '<font style="font-size: 30px; font-weight: bolder">'.$grupo->getNombre().'</font>';
}else {
    $cadena.= '<b>SIN GRUPO</b><br/><font style="color: #666; font-size: 10px">Recuerde asignar un grupo para que este equipo pueda participar en juegos</font>';
}

$cadena.= '</div>';
echo $cadena;

?>

<div  style="position: relative; width: 10px; height: 10px" >
    <div id="grupo_<?php echo $operaciones_registro_disciplina->getId() ?>" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; width: 180px; min-height:92px; left: 0px; top: -100px; display: none">
        <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:92px; padding: 5px; box-shadow: #777 0.1em 0.2em 0.1em;">
            <div style="top: -15px; left: -15px; position: absolute;">
                <a href="#" onclick="conmutar_grupo(<?php echo $operaciones_registro_disciplina->getId();?>,'siglas'); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
            </div>
            <table>
                <tr>
                    <td>
                        <table>
                            <tr><td colspan="2" style="background-color: #666; text-align: center"><font style="font-weight: bold">Asignar Grupo</font></td></tr>
                            <tr>
                                <td style="width: 30%">Grupos:</td>
                                <?php $grupos= Doctrine::getTable('Operaciones_Grupo')->disponibles(); ?>
                                <td style="width: 70%">
                                    <?php if(count($grupos) > 0) { ?>
                                        <select id="grupo_dato_<?php echo $operaciones_registro_disciplina->getId(); ?>">
                                            <?php
                                            foreach($grupos as $value) {
                                                echo '<option value="'.$value->getId().'">'.$value->getNombre().'</option>';
                                            }
                                            ?>
                                        </select>
                                    <?php }else { ?>
                                        <font style="color: red">No hay grupos creados</font>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div style="text-align: right; width: 170px; background-color: #B7B7B7" id="save_pago_<?php echo $operaciones_registro_disciplina->getId(); ?>">
                <input onClick="javascript: saveGrupo('<?php echo $operaciones_registro_disciplina->getId(); ?>'); return false;" name="guardar" type="button" value="Guardar" />
            </div>
        </div>
    </div>
</div>
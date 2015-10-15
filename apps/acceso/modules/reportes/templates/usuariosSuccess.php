<script>
    function abrir_funcionarios(perfil_id) {
        $('#div_perfil_'+perfil_id).slideDown('slow');
        $('#abrir_'+perfil_id).hide();
        $('#cerrar_'+perfil_id).show();
    }
    
    function cerrar_funcionarios(perfil_id) {
        $('#div_perfil_'+perfil_id).slideUp('slow');
        $('#abrir_'+perfil_id).show();
        $('#cerrar_'+perfil_id).hide();
    }
</script>

<div>   
    <fieldset><h2>DISTRIBUCION DE PERFILES DE USUARIO</h2></fieldset>
        <br/>

        <table style="width: 100%;">

            <tr class="sf_admin_row">
                <th>Perfil</th>
                <th>Funcionarios</th>
            </tr>
            <?php  
                $perfil = '';
                foreach ($usuarios_perfiles as $usuario_perfil) { 
                    if($perfil != $usuario_perfil->getPerfil()){
                        if($perfil!=''){
                        ?>
                                    <a href="#" id="abrir_<?php echo $perfil_id; ?>" onclick="abrir_funcionarios(<?php echo $perfil_id; ?>); return false;">
                                        <?php echo $count_perfil; ?>&nbsp;&nbsp;Ver detalles
                                    </a>
                                    <a href="#" id="cerrar_<?php echo $perfil_id; ?>" onclick="cerrar_funcionarios(<?php echo $perfil_id; ?>); return false;" style="display: none;">
                                        <?php echo $count_perfil; ?>&nbsp;&nbsp;Ocultar detalles
                                    </a>
                                    <?php echo $cadena_funcionarios.'</div>'; ?>
                                </td>
                            </tr>
                        <?php
                        }
                        
                        $perfil = $usuario_perfil->getPerfil();
                        $perfil_id = $usuario_perfil->getPerfilId();
                        $count_perfil=1;
                        $cadena_funcionarios = '<div id="div_perfil_'.$perfil_id.'" style="display:none;"><hr/>';
                        
                        $funcionario = Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($usuario_perfil->getUsuarioEnlaceId()); 
                        
                        if(count($funcionario)>0){
                            $cadena_funcionarios .= $funcionario[0]->getFnombre().' '.$funcionario[0]->getFapellido().'<br/>'.
                                                    '<b>'.$funcionario[0]->getUnombre().'</b><br/>'.
                                                    $funcionario[0]->getCtnombre().'<hr/>';
                        }
                        ?>
                            <tr class="sf_admin_row">
                                <td style="width: 200px;">
                                    <?php echo $perfil; ?>
                                </td>
                                <td>
                        <?php
                    } else {
                        $count_perfil++;
                        
                        $funcionario = Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($usuario_perfil->getUsuarioEnlaceId()); 
                        
                        if(count($funcionario)>0){
                            $cadena_funcionarios .= $funcionario[0]->getFnombre().' '.$funcionario[0]->getFapellido().'<br/>'.
                                                    '<b>'.$funcionario[0]->getUnombre().'</b><br/>'.
                                                    $funcionario[0]->getCtnombre().'<hr/>';
                        }
                    }
                } 
            ?>
                    <a href="#" id="abrir_<?php echo $perfil_id; ?>" onclick="abrir_funcionarios(<?php echo $perfil_id; ?>); return false;">
                        <?php echo $count_perfil; ?>&nbsp;&nbsp;Ver detalles
                    </a>
                    <a href="#" id="cerrar_<?php echo $perfil_id; ?>" onclick="cerrar_funcionarios(<?php echo $perfil_id; ?>); return false;" style="display: none;">
                        <?php echo $count_perfil; ?>&nbsp;&nbsp;Ocultar detalles
                    </a>
                    <br/>
                    <?php echo $cadena_funcionarios.'</div>'; ?>
                </td>
            </tr>
        </table>
    <div id="sf_admin_footer"> </div>
</div>
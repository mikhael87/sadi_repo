<div>
        <table>
            <tr>
                <th>Condición del trabajador</th>
                <th style="width: 140px;">Dias configurados</th>
                <th style="width: 150px;">Frecuencia de alertas</th>
                <th style="width: 170px;">Forma de pago</th>
                <th style="width: 150px;">Tiempo de validez</th>
                <th>Acciones</th>
            </tr>                    
            <?php 
            $configuraciones_vacaciones = Doctrine_Query::create()
                                          ->select('c.*')
                                          ->addSelect('(SELECT us.nombre FROM Acceso_Usuario us WHERE us.id = c.id_update LIMIT 1) as user_update')
                                          ->from('Rrhh_Configuraciones c')
                                          ->where('c.modulo = ?', 'vacaciones')
                                          ->orderBy('c.indexado, id')
                                          ->execute();

            foreach ($configuraciones_vacaciones as $configuracion_vacaciones) { 
                $parametros_creados = sfYaml::load($configuracion_vacaciones->getParametros());

            ?>
                <tr>
                    <td>
                        <?php 
                            $condicion = Doctrine::getTable('Organigrama_CargoCondicion')->find($parametros_creados['condicion']);
                                echo $condicion->getNombre(); 
                        ?>
                    </td>
                    <td>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Dias asignados por año: </div>
                            <div style="position: absolute; top: 0px; padding-left: 110px;"><?php echo $parametros_creados['dias_asignados_anio']; ?></div>
                        </div>
                        <br/>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Dias adicionales por año: </div>
                            <div style="position: absolute; top: 0px; padding-left: 110px;"><?php echo $parametros_creados['dias_adicionales_anio']; ?></div>
                        </div>
                        <br/>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Dias de disfrute minimo: </div>
                            <div style="position: absolute; top: 0px; padding-left: 110px;"><?php echo $parametros_creados['dias_disfrute_minimo']; ?></div>
                        </div>
                        <br/>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Dias de disfrute maximo: </div>
                            <div style="position: absolute; top: 0px; padding-left: 110px;"><?php echo $parametros_creados['dias_disfrute_maximo']; ?></div>
                        </div>
                        <br/>
                    </td>
                    <td>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Periodos pendientes: </div>
                            <div style="position: absolute; top: 0px; padding-left: 90px;"><?php echo $parametros_creados['alerta_periodos_pendientes']; ?></div>
                        </div>
                        <br/>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Dias aplazados: </div>
                            <div style="position: absolute; top: 0px; padding-left: 90px;"><?php echo $parametros_creados['alerta_dias_aplazados']; ?> dias</div>
                        </div>
                        <br/>
                    </td>
                    <td><?php 
                        if($parametros_creados['forma_abono_concepto']=='anio')
                            echo 'Al cumplir año de servicio'; 
                        else
                            echo 'Al solicitar las vacaciones'; 
                        ?>
                    </td>
                    <td>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Desde: </div>
                            <div style="position: absolute; top: 0px; padding-left: 30px;"><?php echo date('d-m-Y', strtotime($parametros_creados['f_inicial_configuracion'])); ?></div>
                        </div>
                        <br/>
                        <div style="position: relative;">
                            <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Hasta: </div>
                            <div style="position: absolute; top: 0px; padding-left: 30px;">
                                <?php 
                                    if($parametros_creados['f_final_configuracion']!='2038-01-01')
                                        echo date('d-m-Y', strtotime($parametros_creados['f_final_configuracion']));
                                    else
                                        echo '<fond style="color: #04B404;">Activo</fond>'; 
                                ?>
                            </div>
                        </div>
                        <br/>                                
                    </td>
                    <td>
                        <br/><br/>

                        <div class="" style="position: relative;">
                            <font class="f10n">Hecho por:</font><br/>
                            <font class="f16n">&nbsp;&nbsp;<?php echo $configuracion_vacaciones->getUserUpdate(); ?><br/></font>
                            <font class="f10n">Fecha:</font><br/>
                            <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($configuracion_vacaciones->getCreatedAt())); ?><br/></font>
                            <font class="f10n">Hora:</font><br/>
                            <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($configuracion_vacaciones->getCreatedAt())); ?></font>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
</div>
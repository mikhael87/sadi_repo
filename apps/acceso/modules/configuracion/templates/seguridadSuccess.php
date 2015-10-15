<?php use_helper('jQuery'); ?>
<script>
    function testear_conexion(){
        $('#div_test_conexion').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Testeando...');
    
        <?php
        echo jq_remote_function(array('update' => 'div_test_conexion',
        'url'  => 'configuracion/testearConexionSaime',
        'with' => "$('#form_seguridad').serialize()",));
        ?>
    };
</script>

<fieldset id="sf_fieldset_seguridad">
    <form method="post" id="form_seguridad" action="<?php echo sfConfig::get('sf_app_acceso_url').'configuracion/saveSeguridad'; ?>"> 
    <h2>Seguridad</h2>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Tiempo sesi&oacute;n</label>
            <div class="content">
                <select name="seguridad[session][expira]">
                    <option value="6" <?php if($sf_seguridad['session']['expira'] == 6) echo "selected"; ?>>6 minutos</option>
                    <option value="10" <?php if($sf_seguridad['session']['expira'] == 10) echo "selected"; ?>>10 minutos</option>
                    <option value="20" <?php if($sf_seguridad['session']['expira'] == 20) echo "selected"; ?>>20 minutos</option>
                    <option value="30" <?php if($sf_seguridad['session']['expira'] == 30) echo "selected"; ?>>30 minutos</option>
                    <option value="60" <?php if($sf_seguridad['session']['expira'] == 60) echo "selected"; ?>>1 hora</option>
                    <option value="120" <?php if($sf_seguridad['session']['expira'] == 120) echo "selected"; ?>>2 horas</option>
                    <option value="300" <?php if($sf_seguridad['session']['expira'] == 300) echo "selected"; ?>>5 horas</option>
                    <option value="100000" <?php if($sf_seguridad['session']['expira'] == 100000) echo "selected"; ?>>No expirar</option>
                </select>
            </div>
            <div class="help">Seleccione la cantidad de minutos que deben pasar en tiempo inactivo para que expire la sesi&oacute;n de usuairo.</div>
        </div>
    </div>
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Sistema de Visitantes</label>
            <div class="content">
                <table style="width: 100%">
                    <tr>
                        <td style="width: 450px !important">
                            <div style="position: relative; height: 460px;">
                                <div style="position: absolute; top: 0px;">Conexion con<br/>base de datos<br/>SAIME:</div>
                                <div style="position: absolute; top: 20px; left: 100px;">
                                    <input type="radio" name="seguridad[conexion_saime][activo]" value="true" <?php if($sf_seguridad['conexion_saime']['activo'] == true) echo "checked"; ?>/> Activo&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="seguridad[conexion_saime][activo]" value="false" <?php if($sf_seguridad['conexion_saime']['activo'] == false) echo "checked"; ?>/> Inactivo
                                </div>
                                
                                <div style="position: absolute; top: 60px; width: 100%;"><hr/></div>
                                <div style="position: absolute; top: 80px;"><b>Parametros de conexion</b></div>

                                <div style="position: absolute; top: 120px;">Servidor:</div>
                                <div style="position: absolute; top: 120px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][host]" value="<?php echo $sf_seguridad['conexion_saime']['host']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 140px;">Puerto:</div>
                                <div style="position: absolute; top: 140px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][port]" value="<?php echo $sf_seguridad['conexion_saime']['port']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 160px;">Base de Datos:</div>
                                <div style="position: absolute; top: 160px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][dbname]" value="<?php echo $sf_seguridad['conexion_saime']['dbname']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 180px;">Usuario:</div>
                                <div style="position: absolute; top: 180px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][username]" value="<?php echo $sf_seguridad['conexion_saime']['username']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 200px;">Clave:</div>
                                <div style="position: absolute; top: 200px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][password]" value="<?php echo $sf_seguridad['conexion_saime']['password']; ?>"/>
                                </div>
                                
                                <div style="position: absolute; top: 240px; width: 100%;"><hr/></div>
                                <div style="position: absolute; top: 260px;"><b>Parametros de consulta (tabla y campos)</b></div>
                                
                                <div style="position: absolute; top: 300px;">Tabla:</div>
                                <div style="position: absolute; top: 300px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][tabla]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['tabla']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 320px;">Nacionalidad:</div>
                                <div style="position: absolute; top: 320px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][campo_nacionalidad]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['campo_nacionalidad']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 340px;">Cedula:</div>
                                <div style="position: absolute; top: 340px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][campo_cedula]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['campo_cedula']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 360px;">1° Nombre:</div>
                                <div style="position: absolute; top: 360px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][campo_primer_nombre]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 380px;">2° Nombre:</div>
                                <div style="position: absolute; top: 380px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][campo_segundo_nombre]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 400px;">1° Apellido:</div>
                                <div style="position: absolute; top: 400px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][campo_primer_apellido]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 420px;">2° Apellido:</div>
                                <div style="position: absolute; top: 420px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][campo_segundo_apellido]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido']; ?>"/>
                                </div>
                                <div style="position: absolute; top: 440px;">F. Nacimiento:</div>
                                <div style="position: absolute; top: 440px; left: 100px;">
                                    <input type="text" name="seguridad[conexion_saime][consulta][campo_f_nacimiento]" value="<?php echo $sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']; ?>"/>
                                </div>
                            </div>
                            
                            <div style="width: 100%;"><hr/></div>
                            <input type="button" value="Testear conexion" onClick="testear_conexion();">
                            <div id="div_test_conexion"></div>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
    
    <ul class="sf_admin_actions">
        <li class="sf_admin_action_save">
            <button id="guardar_documento" onClick="javascript: this.form.submit();" style="height: 35px; margin-left: 130px">
                <?php echo image_tag('icon/filesave.png', array('style' => 'vertical-align: middle')) ?>&nbsp;<strong>Guardar cambios</strong>
            </button>
        </li>
    </ul>
    
    </form>         
</fieldset>
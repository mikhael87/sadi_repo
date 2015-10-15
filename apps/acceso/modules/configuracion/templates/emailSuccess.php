<?php use_helper('jQuery'); ?>

<script>    
    function inactivar_cuentas(id)
    {
        $(".estatus_inactivo").attr("checked", "checked");
        $("#estatus_activo_"+id).attr("checked", "checked");
    }
</script>

<fieldset id="sf_fieldset_email">
    <form method="post" action="<?php echo sfConfig::get('sf_app_acceso_url').'configuracion/saveEmail'; ?>"> 
    <h2>Correo Electronico</h2>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="correspondencia_n_correspondencia_emisor">Estatus</label>
            <div class="content">
                <input type="radio" name="email[activo]" value=true <?php if($sf_email['activo'] == true) echo "checked"; ?>/> Activo&nbsp;&nbsp;&nbsp;
                <input type="radio" name="email[activo]" value=false <?php if($sf_email['activo'] == false) echo "checked"; ?>/> Inactivo
            </div>
        </div>
    </div>
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="correspondencia_n_correspondencia_emisor">Cuentas de correo</label>
            <div class="content">
                <?php $i=0; foreach ($sf_email['cuentas'] as $cuenta => $detalles) { $i++; ?>
                    <table width="600">
                        <tr>
                            <td width="110"><b>Nombre</b></td>
                            <td><input type="text" name="email[cuentas][<?php echo $cuenta; ?>]" value="<?php echo $cuenta; ?>"/></td>
                        </tr>
                        <tr>
                            <td><b>Estatus</b></td>
                            <td>
                                <input id="estatus_activo_<?php echo $i; ?>" onclick="javascript:inactivar_cuentas(<?php echo $i; ?>)" type="radio" name="email[cuentas][<?php echo $cuenta; ?>][activo]" value="true" <?php if($detalles['activo'] == TRUE) echo "checked"; ?>/> Activa&nbsp;&nbsp;&nbsp;
                                <input class="estatus_inactivo" type="radio" name="email[cuentas][<?php echo $cuenta; ?>][activo]" value="false" <?php if($detalles['activo'] == FALSE) echo "checked"; ?>/> Inactiva
                                <div style="position: relative;"><div style="position: absolute; left: 70px; top: -20px; width: 20px; height: 20px;"></div></div>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Conector</b></td>
                            <td>
                                <div style="position: relative; height: 60px;">
                                    <div style="position: absolute; top: 0px;">SMTP:</div>
                                    <div style="position: absolute; top: 0px; left: 55px;">
                                        <input type="text" name="email[cuentas][<?php echo $cuenta; ?>][conector][smtp]" value="<?php echo $detalles['conector']['smtp']; ?>"/>
                                    </div>
                                    <div style="position: absolute; top: 20px;">Puerto:</div>
                                    <div style="position: absolute; top: 20px; left: 55px;">
                                        <input type="text" name="email[cuentas][<?php echo $cuenta; ?>][conector][port]" value="<?php echo $detalles['conector']['port']; ?>"/>
                                    </div>
                                    <div style="position: absolute; top: 40px;">Cifrado:</div>
                                    <div style="position: absolute; top: 40px; left: 55px;">
                                        <input type="text" name="email[cuentas][<?php echo $cuenta; ?>][conector][cifrado]" value="<?php echo $detalles['conector']['cifrado']; ?>"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Acceso</b></td>
                            <td>
                                <div style="position: relative; height: 40px;">
                                    <div style="position: absolute; top: 0px;">Usuario:</div>
                                    <div style="position: absolute; top: 0px; left: 55px;">
                                        <input type="text" name="email[cuentas][<?php echo $cuenta; ?>][acceso][usuario]" value="<?php echo $detalles['acceso']['usuario']; ?>"/>
                                    </div>
                                    <div style="position: absolute; top: 20px;">Clave:</div>
                                    <div style="position: absolute; top: 20px; left: 55px;">
                                        <input type="text" name="email[cuentas][<?php echo $cuenta; ?>][acceso][clave]" value="<?php echo $detalles['acceso']['clave']; ?>"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    
                    <br/>
                <?php } ?>
            </div>
        </div>
    </div>

    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="correspondencia_n_correspondencia_emisor">Aplicaciones</label>
            <div class="content">
                <?php foreach ($sf_email['aplicaciones'] as $aplicacion => $detalles) { ?>
                    <table width="600">
                        <tr>
                            <td colspan="2"><b><?php echo $aplicacion; ?></b></td>
                        </tr>
                        <tr>
                            <td width="110"><b>Alerta Inmediata</b></td>
                            <td>
                                <input type="radio" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][activo]" value="true" <?php if($detalles['inmediata']['activo'] == TRUE) echo "checked"; ?>/> Activa&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][activo]" value="false" <?php if($detalles['inmediata']['activo'] == FALSE) echo "checked"; ?>/> Inactiva&nbsp;&nbsp;&nbsp;<br/>
                                Frecuencia: &nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][frecuencia][lunes]" <?php if($detalles['inmediata']['frecuencia']['lunes'] == TRUE) echo "checked"; ?>/> Lun&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][frecuencia][martes]" <?php if($detalles['inmediata']['frecuencia']['martes'] == TRUE) echo "checked"; ?>/> Mar&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][frecuencia][miercoles]" <?php if($detalles['inmediata']['frecuencia']['miercoles'] == TRUE) echo "checked"; ?>/> Mie&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][frecuencia][jueves]" <?php if($detalles['inmediata']['frecuencia']['jueves'] == TRUE) echo "checked"; ?>/> Jue&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][frecuencia][viernes]" <?php if($detalles['inmediata']['frecuencia']['viernes'] == TRUE) echo "checked"; ?>/> Vie&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][frecuencia][sabado]" <?php if($detalles['inmediata']['frecuencia']['sabado'] == TRUE) echo "checked"; ?>/> Sab&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][inmediata][frecuencia][domingo]" <?php if($detalles['inmediata']['frecuencia']['domingo'] == TRUE) echo "checked"; ?>/> Dom
                            </td>
                        </tr>
                        <tr>
                            <td width="110"><b>Reportes</b></td>
                            <td>
                                <input type="radio" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][activo]" value="true" <?php if($detalles['reporte']['activo'] == TRUE) echo "checked"; ?>/> Activa&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][activo]" value="false" <?php if($detalles['reporte']['activo'] == FALSE) echo "checked"; ?>/> Inactiva&nbsp;&nbsp;&nbsp;<br/>
                                Frecuencia: &nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][frecuencia][lunes]" <?php if($detalles['reporte']['frecuencia']['lunes'] == TRUE) echo "checked"; ?>/> Lun&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][frecuencia][martes]" <?php if($detalles['reporte']['frecuencia']['martes'] == TRUE) echo "checked"; ?>/> Mar&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][frecuencia][miercoles]" <?php if($detalles['reporte']['frecuencia']['miercoles'] == TRUE) echo "checked"; ?>/> Mie&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][frecuencia][jueves]" <?php if($detalles['reporte']['frecuencia']['jueves'] == TRUE) echo "checked"; ?>/> Jue&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][frecuencia][viernes]" <?php if($detalles['reporte']['frecuencia']['viernes'] == TRUE) echo "checked"; ?>/> Vie&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][frecuencia][sabado]" <?php if($detalles['reporte']['frecuencia']['sabado'] == TRUE) echo "checked"; ?>/> Sab&nbsp;&nbsp;
                                <input type="checkbox" name="email[aplicaciones][<?php echo $aplicacion; ?>][reporte][frecuencia][domingo]" <?php if($detalles['reporte']['frecuencia']['domingo'] == TRUE) echo "checked"; ?>/> Dom
                            </td>
                        </tr>
                    </table>
                    
                    <br/>
                <?php } ?>
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
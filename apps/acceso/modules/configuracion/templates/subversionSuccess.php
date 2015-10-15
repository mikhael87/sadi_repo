<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_subversion">
    <form method="post" action="<?php echo sfConfig::get('sf_app_acceso_url').'configuracion/saveSubversion'; ?>"> 
    <h2>Conexión Subversion</h2>
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Parametros SVN</label>
            <div class="content">
                    <table width="600">
                        <tr>
                            <td width="110"><b>Ubicación</b></td>
                            <td>
                                <input type="hidden" name="subversion[dominio]" value="<?php echo $sf_subversion['dominio']; ?>"/>
                                <div style="position: relative; height: 60px;">
                                    <div style="position: absolute; top: 0px;">SVN:</div>
                                    <div style="position: absolute; top: 0px; left: 65px;">
                                        <input type="text" name="subversion[svn]" value="<?php echo $sf_subversion['svn']; ?>"/>
                                    </div>
                                    <div style="position: absolute; top: 20px;">Proyecto:</div>
                                    <div style="position: absolute; top: 20px; left: 65px;">
                                        <input type="text" name="subversion[project]" value="<?php echo $sf_subversion['project']; ?>"/>
                                    </div>
                                    <div style="position: absolute; top: 40px;">Usuario:</div>
                                    <div style="position: absolute; top: 40px; left: 65px;">
                                        <input type="text" name="subversion[user]" value="<?php echo $sf_subversion['user']; ?>"/>
                                    </div>
                                    <div style="position: absolute; top: 60px;">Password:</div>
                                    <div style="position: absolute; top: 60px; left: 65px;">
                                        <input type="text" name="subversion[password]" value="<?php echo $sf_subversion['password']; ?>"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    
                    <br/>
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
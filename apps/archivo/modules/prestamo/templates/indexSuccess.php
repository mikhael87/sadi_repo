<?php use_helper('I18N', 'Date') ?>
<?php include_partial('prestamo/assets') ?>
<?php use_helper('jQuery'); ?>

<div id="sf_admin_container">
  <h1><?php echo __('Prestamos del expediente', array(), 'messages') ?></h1>

  <?php include_partial('prestamo/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('prestamo/list_header', array('pager' => $pager)) ?>
  </div>


  <div id="sf_admin_content">
    <?php include_partial('prestamo/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('prestamo/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('prestamo/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('prestamo/list_footer', array('pager' => $pager)) ?>
  </div>
</div>




<script>
    function open_registro_retiro(id){
        $("#div_registro_retiro").show();
        $("#windows_prestamo").val(id);
    };
    
    function close_registro_retiro(){
        $("#div_registro_retiro").hide();
        $("#windows_prestamo").val('');
    };
</script>




<div id="div_registro_retiro" class="caja" style="padding: 3px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: fixed; width: 450px; height:250px; left: 35%; top: 35%; box-shadow: #777 0.1em 0.2em 0.3em; display: none;">
    <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; height:250px;">
        <div style="top: -15px; left: -15px; position: absolute;">
            <a href="#" onclick="javascript: close_registro_retiro(); return false;"><?php echo image_tag('icon/icon_close.png'); ?></a>
        </div>
        <div style="top: 10px; left: 10px; width: 430px; position: absolute; text-align: left">
            <table width="430">
                <tr>
                    <td colspan="3" class="f19b" aling="center">
                        Retiro fisico del expediente
                        <hr>
                    </td>
                </tr>                
                <form id="formlogin" name="form_retiro_prestamo" method="post" action="<?php echo sfConfig::get('sf_app_archivo_url'); ?>prestamo/registroRetiroFisico">
                    <tr>
                        <td rowspan="4" width="75" align="center"><?php echo image_tag("icon/candado.png"); ?></td>
                        <td width="120">Usuario que retira</td>
                        <td ><input name="retiro[usuario_retira]" type="text" style="width: 190px;"/></td>
                    </tr>
                    <tr>
                        <td>Contraseña</td>
                        <td><input name="retiro[contrasena_retira]" type="password" style="width: 190px;"/></td>
                    </tr>
                    <tr>
                        <td>Codigo de Prestamo</td>
                        <td><input name="retiro[codigo_prestamo]" type="text" style="width: 190px;"/></td>
                    </tr>
                    <tr>
                        <td>&nbsp;<input id="windows_prestamo" name="retiro[prestamo]" type="hidden" value=""/></td>
                        <td valign="top"><input name="entrar" type = "submit" value="Registrar Prestamo"/></td>
                    </tr>
                </form>

                <tr>
                    <td colspan="3">
                        <hr>
                        <div class="pie" align="justify">
                            <br/>
                            Por favor escriba su nombre de usuario, contraseña y codigo de prestamo para registrar el retiro.
                            <br/><br/>
                            Recuerde que el usuario y contraseña es personalizado e intransferible,
                            al registrar el prestamo en el sistema queda bajo su responsabilidad la integridad de los documentos retirados.
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/registro/modules/empresa/lib/assets_asistente.php'); ?>

<div id="sf_admin_container">
  <h1>
      Asistente para Inscripci&oacute;n de Empresas
  </h1>


    <div id="sf_admin_header"></div>

    <div id="sf_admin_content">
        <div class="sf_admin_form">
            <fieldset style="border-top: none; border-left: none; border-right: none">
                <div style="position: relative; height: 50px">
                    <div id="div_paso_1" class="pasos" style="position: absolute; left: 0px; background-color: #CCCCFF; font-weight: bold; color: #000; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 1</div>
                            <div style="position: absolute; font-size: 12px; width: 120px; text-align: center; top: 30px;">Empresa</div>
                        </div>
                        <div id="div_vinculo_1" class="vinculo" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                    <div id="div_paso_2" class="pasos" style="position: absolute; left: 120px; background-color: ''; font-weight: normal; color: #aaa; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 2</div>
                            <div style="position: absolute; font-size: 12px; width: 120px; text-align: center; top: 30px;">Encargados</div>
                        </div>
                        <div id="div_vinculo_2" class="vinculo" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                    <div id="div_paso_3" class="pasos" style="position: absolute; left: 240px; background-color: ''; font-weight: normal; color: #aaa; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 3</div>
                            <div style="position: absolute; font-size: 12px; width: 120px; text-align: center; top: 30px;">Delegados</div>
                        </div>
                        <div id="div_vinculo_3" class="vinculo" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                    <div id="div_paso_4" class="pasos" style="position: absolute; left: 360px; background-color: ''; font-weight: normal; color: #aaa; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 4</div>
                            <div style="position: absolute; font-size: 12px; width: 120px; text-align: center; top: 30px;">Disciplinas</div>
                        </div>
                        <div id="div_vinculo_4" class="vinculo" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                </div>
            </fieldset>
            <div id="div_prosesar"><script>rastrear('<?php echo $paso; ?>', '<?php echo $regid; ?>');</script></div>
        </div>
  </div>

  <div id="sf_admin_footer">
    <ul class="sf_admin_actions trans">
        <li class="sf_admin_action_regresar_modulo">
            <a href="<?php echo sfConfig::get('sf_app_registro_url'); ?>empresa">Regresar</a>
        </li>
    </ul>
  </div>
</div>

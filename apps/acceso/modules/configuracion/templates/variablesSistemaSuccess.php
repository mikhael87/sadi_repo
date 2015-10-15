<?php use_helper('jQuery'); ?>
<link rel="stylesheet" type="text/css" media="screen" href="/css/chosen.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/jquery.stepper.css"/>
<script type="text/javascript" src="/js/chosen.jquery.js"></script>
<script type="text/javascript" src="/js/docsupport/prism.js"></script>
<script type="text/javascript" src="/js/jquery.stepper.js"></script>
<script type="text/javascript">
    $(document).ready(function (){
        $('#stepperA').stepper({
            limit: [0, 80],
            type: 'float',
            floatPrecission: 2,
            wheelStep: .1,
            arrowStep: .1
        });
    });
</script>

<fieldset id="sf_fieldset_autenticacion">
    <form method="post" action="<?php echo sfConfig::get('sf_app_acceso_url').'configuracion/saveVariablesSistema'; ?>"> 
    <h2>Variables de Sistema</h2>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Costo por disciplina</label>
            <div class="content">
                <input type="text" size="5" name="variablesSistema[costo_disciplina]" value="<?php echo $sf_variablesSistema['costo_disciplina']; ?>" />
            </div>
        </div>
    </div>
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Moneda</label>
            <div class="content">
                <select name="variablesSistema[moneda]">
                    <option value="Bs" <?php echo (($sf_variablesSistema['moneda']== 'Bs')? 'selected': ''); ?>>Bolivares</option>
                    <option value="USD" <?php echo (($sf_variablesSistema['moneda']== 'USD')? 'selected': ''); ?>>Dolares</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">I.V.A.</label>
            <div class="content">
                <input type="text" id="stepperA" readonly="true" size="5" name="variablesSistema[iva]" value="<?php echo $sf_variablesSistema['iva']; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%
            </div>
        </div>
    </div>
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">El IVA deber&aacute;:</label>
            <div class="content">
                <select name="variablesSistema[iva_operacion]">
                    <option value="restar" <?php echo (($sf_variablesSistema['iva_operacion']== 'restar')? 'selected': ''); ?>>Restarse</option>
                    <option value="sumar" <?php echo (($sf_variablesSistema['iva_operacion']== 'sumar')? 'selected': ''); ?>>Sumarse</option>
                </select>
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
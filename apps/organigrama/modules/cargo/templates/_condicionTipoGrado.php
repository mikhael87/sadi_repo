<?php use_helper('jQuery'); ?>

<script>
    function tiposDeCondicion(){
        condicion_id = $('#organigrama_cargo_cargo_condicion_id').val();
        
        <?php
        echo jq_remote_function(array('update' => 'organigrama_cargo_cargo_tipo_id',
        'url' => 'cargo/tiposDeCondicion',
        'with'     => "'condicion_id=' +condicion_id",)); 
        ?>

        <?php
        echo jq_remote_function(array('update' => 'organigrama_cargo_cargo_grado_id',
        'url' => 'cargo/gradosDeTipos',
        'with'     => "tipo_id=0",)); 
        ?>
    };
</script>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_cargo_condicion_id">
    <div>
        <label for="organigrama_cargo_cargo_condicion_id">Condición</label>
        <div class="content">
            <select id="organigrama_cargo_cargo_condicion_id" name="organigrama_cargo[cargo_condicion_id]" onchange="tiposDeCondicion();">
                <option value=""></option>
                <?php 
                    $condiciones = Doctrine::getTable('Organigrama_CargoCondicion')->createQuery('cc')->orderBy('nombre')->execute();
                    
                    foreach( $condiciones as $condicion ) { ?>
                    <option value="<?php echo $condicion->getId(); ?>" <?php if(!$form->isNew()) { if($form['cargo_condicion_id']->getValue() == $condicion->getId()) { echo "selected"; } } ?>>
                        <?php echo $condicion->getNombre(); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="help">seleccione la condición del cargo</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_cargo_tipo_id">
    <div>
        <label for="organigrama_cargo_cargo_tipo_id">Tipo</label>
        <div class="content">
            <select id="organigrama_cargo_cargo_tipo_id" name="organigrama_cargo[cargo_tipo_id]" onchange="
                <?php
                    echo jq_remote_function(array('update' => 'organigrama_cargo_cargo_grado_id',
                    'url' => 'cargo/gradosDeTipos',
                    'with'     => "'tipo_id=' +this.value",)) ?>">
                <option value=""></option>
                
                <?php 
                if(!$form->isNew()){
                    $tipos = Doctrine::getTable('Organigrama_CargoTipo')
                            ->createQuery('ct')
                            ->where('ct.cargo_condicion_id = ?',$form['cargo_condicion_id']->getValue())
                            ->orderBy('nombre')
                            ->execute();
                    
                    foreach( $tipos as $tipo ) { ?>
                    <option value="<?php echo $tipo->getId(); ?>" <?php if($form['cargo_tipo_id']->getValue() == $tipo->getId()) echo "selected"; ?>>
                        <?php echo $tipo->getNombre(); ?>
                    </option>
                <?php } } ?>
                
            </select>
        </div>
        <div class="help">seleccione el tipo de cargo</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_cargo_grado_id">
    <div>
        <label for="organigrama_cargo_cargo_grado_id">Grado</label>
        <div class="content" id="div_tipos">
            <select id="organigrama_cargo_cargo_grado_id" name="organigrama_cargo[cargo_grado_id]">
                <option value=""></option>
                
                <?php 
                if(!$form->isNew()){
                    $grados = Doctrine::getTable('Organigrama_CargoGrado')
                            ->createQuery('cg')
                            ->innerJoin('cg.Organigrama_CargoGradoTipo cgt')
                            ->where('cgt.cargo_tipo_id = ?',$form['cargo_tipo_id']->getValue())
                            ->orderBy('cg.nombre')
                            ->execute();
                    
                    foreach( $grados as $grado ) { ?>
                    <option value="<?php echo $grado->getId(); ?>" <?php if($form['cargo_grado_id']->getValue() == $grado->getId()) echo "selected"; ?>>
                        <?php echo $grado->getNombre(); ?>
                    </option>
                <?php } } ?>
                
            </select>
        </div>
        <div class="help">seleccione el grado de cargo</div>
    </div>
</div>
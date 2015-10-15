<?php use_helper('jQuery'); ?>
<script>    
    function mostrar_cargos_vacios(unidad_id) {
        if(unidad_id != ''){
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo/cargosVacios',
                type:'POST',
                dataType:'html',
                data: 'unidad_id='+unidad_id,
                beforeSend: function(Obj){
                    $('#cargos_vacios_id').html('<img src="/images/icon/cargando.gif"/>&nbsp;<font style="color: #666; font-size: 12px">Cargando cargos disponibles...</font>');
                },
                success:function(data, textStatus){
                    $('#cargos_vacios_id').html(data);
                }});
        } else {
            $('#asignacion_cargo').html('<option value=""></option>');
        }
    }
</script>

<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div>
        <label for="autorizacion_unidad_id">Unidad</label>
        <div class="content">
            <select name="unidad_id" id="unidad_id" onchange="mostrar_cargos_vacios($(this).val()); return false;">
                <option value=""></option>

                <?php 
                $count_vacios=0;
                foreach( $unidades as $unidad_id=>$unidad_nombre ) { ?>
                    <?php if($unidad_id!='') { ?>
                        <?php 
                        $cargos_vacios = Doctrine::getTable('Organigrama_Cargo')->cargosVacios($unidad_id); 
                        if(count($cargos_vacios)>0){ $color = 'blue'; } else { $color = 'black'; }
                        
                        ?>
                        <option value="<?php echo $unidad_id; ?>" style="color: <?php echo $color; ?>;">
                            <?php 
                            echo $unidad_nombre; 
                            if(count($cargos_vacios)>0){
                                $count_vacios++;
                                echo ' / CARGOS DISPONIBLES: '.count($cargos_vacios); 
                            }
                            ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
            <?php if($count_vacios==0){ ?>
                <script>
                    $('#activar_cargo_false').attr('checked',true);
                    asignacion_cargo = false;
                    $('#div_asignacion_cargo').html('<div class="sf_admin_form_row" style="color: #666; font-size: 12px;">No se encontraron cargos disponibles.</div>');
                </script>
            <?php exit(); } ?>
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_cargos_vacios_id">
    <div>
        <label>Cargo</label>
        <div class="content" id="cargos_vacios_id">
            <select id="asignacion_cargo" name="funcionarios_funcionario_cargo[cargo_id]">
                <option value=""></option>
            </select>
        </div>
    </div>
</div>

<?php $funcionario_cargo_condiciones = Doctrine::getTable('Funcionarios_FuncionarioCargoCondicion')->findAll(); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_funcionario_cargo_condicion_id">
    <div>
        <label>Condición en el Cargo</label>
        <div class="content" id="cargos_vacios_id">
            <select id="asignacion_cargo_condicion" name="funcionarios_funcionario_cargo[funcionario_cargo_condicion_id]">
                <?php foreach( $funcionario_cargo_condiciones as $funcionario_cargo_condicion ) { ?>
                    <option value="<?php echo $funcionario_cargo_condicion->getId(); ?>">
                        <?php echo $funcionario_cargo_condicion->getNombre(); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>


<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_funcionario_cargo_condicion_id">
    <div>
        <label>Fecha de Asignación</label>
        <div class="content" id="cargos_vacios_id">

                <?php 
                    $years = range(date('Y'), date('Y')+1);
                
                    $w = new sfWidgetFormJQueryDate(array(
                    'image' => '/images/icon/calendar.png',
                    'culture' => 'es',
                        'config' => "{changeYear: true, yearRange: 'c-100:c+100'}",
                    'date_widget' => new sfWidgetFormI18nDate(array(
                                    'format' => '%day%-%month%-%year%',
                                    'culture'=>'es',
                                    'empty_values' => array('day'=>'<- Día ->',
                                    'month'=>'<- Mes ->',
                                    'year'=>'<- Año ->'),
                                    'years' => array_combine($years, $years),
                                    ))
                    ),array('name'=>'funcionarios_funcionario_cargo[f_ingreso]'));

                    echo $w->render('asignacion_f_ingreso');
                
                ?>

        </div>
    </div>
</div>


<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>
<?php use_helper('jQuery'); ?>

<?php 
    $session_cargos = $sf_user->getAttribute('session_cargos');
    $editar_vacaciones = FALSE;
    $sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
    foreach ($session_cargos as $session_cargo) {
        if($session_cargo['cargo_grado_id']==99 && $session_cargo['unidad_id']==$sf_oficinasClave['recursos_humanos']['seleccion']) {
          $editar_vacaciones = TRUE; 
        }
    }
    
    if($editar_vacaciones == FALSE){
        $rrhh_delegados = Doctrine::getTable('Acceso_AccionDelegada')->findByUsuarioDelegadoIdAndAccionAndStatus($sf_user->getAttribute('usuario_id'), 'administrar_rrhh', 'A');

        foreach ($rrhh_delegados as $rrhh_delegado) {
            $parametros = sfYaml::load($rrhh_delegado->getParametros());
            if(isset($parametros['editar_vacaciones'])) $editar_vacaciones = TRUE;
        }
    }
?>
<script>
    $.expr[':'].icontains = function(obj, index, meta, stack){
    return (obj.textContent || obj.innerText || jQuery(obj).text() || '').toLowerCase().indexOf(meta[3].toLowerCase()) >= 0;
    };
    $(document).ready(function(){  
        $('#buscador').keyup(function(){
                     buscar = $(this).val();
                     $('.lista').parent().parent().removeClass('resaltar');
                            if(jQuery.trim(buscar) != ''){
                               $(".lista:icontains('" + buscar + "')").parent().parent().addClass('resaltar');
                               var strAncla= '#'+$(".lista:icontains('" + buscar + "')").attr('id');
                               $('body,html').stop(true,true).animate({
                                    scrollTop: $(strAncla).offset().top - parseInt(80)
                                },1000);
                            }
            });
    });
    
    function ver_unidad_vacaciones()
    {
        if($('#vacaciones_unidad_id').val()=='todas'){
            $('.tabla_vacaciones_reporte').show();
            $('#find_ci').show();
        }else {
            if($('#vacaciones_unidad_id').val()=='')
                $('#find_ci').hide();
            else
                $('#find_ci').show();
            $('.tabla_vacaciones_reporte').hide();
            $('#tabla_unidad_'+$('#vacaciones_unidad_id').val()).show();
        }
    }
        
    <?php if($editar_vacaciones==TRUE) { ?>
        function abrir_edicion_vacacion(funcionario_id){
            $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando vacaciones...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>vacaciones/editarVacaciones=?funcionario_id='+funcionario_id,
                type:'POST',
                dataType:'html',
                success:function(data, textStatus){
                    $('#content_window_right').html(data)
                }});
        };
    <?php } ?>
</script>

<style>
.resaltar{background-color: yellow}
</style> 

<div id="sf_admin_container">
    <h1>Reporte global de Vacaciones</h1>
    
    <div id="sf_admin_header"></div>
    
    <div id="sf_admin_content">
        
        <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id trans">
            <div>
                <label for="vacaciones_unidad_id">Unidad</label>
                <div class="content">
                    <select name="vacaciones_unidad_id" id="vacaciones_unidad_id" onchange="ver_unidad_vacaciones(); return false;">
                        <option value=""></option>
                        <?php if(count($unidades)>1){ ?>
                        <option value="todas">Ver Todas</option>
                        <?php } ?>

                        <?php foreach( $unidades as $clave=>$valor ) { 
                            if($clave != '') { ?>
                            <option value="<?php echo $clave; ?>">
                                <?php echo html_entity_decode($valor); ?>
                            </option>
                        <?php }} ?>
                    </select>

                </div>
                <div class="help">Seleccione la unidad que desea ver.</div>
                
                <div id="find_ci" style="display: none">
                    <label for="buscador">c&eacute;dula</label>
                    <input name="buscador" id="buscador" type="text" value="" />
                </div>
            </div>
        </div>
        
        <hr/>
        
        <div class="sf_admin_list">
            <?php foreach ($unidades as $unidad_id => $nombre) { 
                    if($unidad_id != ''){
            ?>
            <div id="tabla_unidad_<?php echo $unidad_id; ?>" class="tabla_vacaciones_reporte" style="display: none;">
                <table style="width: 1000px;" class="trans">
                    <tr>
                        <th style="text-align: left;"><?php echo html_entity_decode($nombre); ?></th>
                        <th>Periodos Vacacionales</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($funcionarios[$unidad_id] as $funcionario) { ?>
                        <tr>
                            <td style="width: 300px;">
                                <font class='f16n'>
                                <?php echo $funcionario->getPrimer_nombre(); ?>
                                <?php echo $funcionario->getSegundo_nombre(); ?>,
                                <?php echo $funcionario->getPrimer_apellido(); ?>
                                <?php echo $funcionario->getSegundo_apellido(); ?>
                                </font>
                                <br/>
                                <?php echo "<font class='f16b'>" . $funcionario->getCtnombre() . "</font>"; ?>
                                <br/>
                                <?php
                                $cargo= Doctrine::getTable('Funcionarios_FuncionarioCargo')->historicoCargosFuncionario($funcionario->getId());
                                echo "<font class='f11n gris_oscuro'>C&eacute;dula: </font><font id='".$funcionario->getCi()."' class='lista f13b'>".$funcionario->getCi()."</font><br/>";
                                echo "<font class='f11n gris_oscuro'>Ingreso: </font><font class='f13b'>".date('d-m-Y', strtotime($cargo[0]->getFIngreso()))."</font>";
                                ?>
                            </td>
                            <td>
                                <div style="position: relative; font-size: 13px; width: 550px;">
                                    <div style="position: relative;">
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 0px;">
                                            Periodo Vacacional
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 80px;">
                                            Fecha Cumplimiento
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 170px;">
                                            Establecidos
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 230px;">
                                            Adicionales
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 295px;">
                                            Totales
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 350px;">
                                            Pendientes
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 450px;">
                                            Estatus
                                        </div>
                                    </div>
                                </div>
                                <div id="periodos_funcionario_<?php echo $funcionario->getId(); ?>">
                                    <?php foreach ($vacaciones[$funcionario->getId()] as $vacacion) { ?>
                                        <div style="position: relative; font-size: 13px; width: 600px;">
                                            <div style="position: relative;">
                                                <div style="position: absolute; top: 10px; left: 0px;">
                                                    <?php echo $vacacion->getPeriodoVacacional(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 80px;">
                                                    <?php echo date('d-m-Y', strtotime($vacacion->getFCumplimiento())); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 180px;">
                                                    <?php echo $vacacion->getDiasDisfruteEstablecidos(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 240px;">
                                                    <?php echo $vacacion->getDiasDisfruteAdicionales(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 300px;">
                                                    <?php echo $vacacion->getDiasDisfruteTotales(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 360px;">
                                                    <?php echo $vacacion->getDiasDisfrutePendientes(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 420px;">
                                                    <?php 
                                                        if($vacacion->getStatus()=='D')
                                                            echo image_tag('icon/online.png')." Disponible"; 
                                                        elseif($vacacion->getStatus()=='A')
                                                            echo image_tag('icon/online.png')." Disfrutando"; 
                                                        elseif($vacacion->getStatus()=='E')
                                                            echo image_tag('icon/absent.png')." Por Disfrutar"; 
                                                        elseif($vacacion->getStatus()=='S')
                                                            echo image_tag('icon/absent.png')." Solicitada"; 
                                                        elseif($vacacion->getStatus()=='F')
                                                            echo image_tag('icon/offline.png')." Finalizada"; 
                                                    ?>
                                                </div>
                                                <br/>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <br/>
                            </td>
                            <td>
                                <ul class="sf_admin_td_actions">
                                    <?php if($editar_vacaciones==TRUE) { ?>
                                    <li class="sf_admin_action_edit">
                                        <a href="#" onclick="open_window_right(); abrir_edicion_vacacion(<?php echo $funcionario->getId(); ?>); return false;">Editar</a>    
                                    </li>
<!--                                    <li class="sf_admin_action_reload">
                                        <a href="<?php echo sfConfig::get('sf_app_rrhh_url'); ?>vacaciones/index" style="text-decoration: none">Reconstruir periodos vacionales</a>    
                                    </li>-->
                                    <?php } ?>
                                </ul>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <?php }} ?>
        </div>
    </div>

    <div id="sf_admin_footer">
        <ul class="sf_admin_actions trans">
            <li class="sf_admin_action_regresar_modulo">
                <a href="<?php echo sfConfig::get('sf_app_rrhh_url'); ?>vacaciones/index">Regresar a vacaciones personales</a>
            </li>
        </ul>
    </div>
    
</div>
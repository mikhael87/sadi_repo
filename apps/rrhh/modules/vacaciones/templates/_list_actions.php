<?php use_helper('jQuery'); ?>

<?php
    $configuraciones_vacaciones = Doctrine_Query::create()
                                  ->select('c.*')
                                  ->from('Rrhh_Configuraciones c')
                                  ->where('c.modulo = ?', 'vacaciones')
                                  ->execute();

    if(count($configuraciones_vacaciones)>0){
        $correlativos_activos = Doctrine_Query::create()
                ->select('cf.id, uc.id as unidad_correlativo, uc.unidad_id as unidad_id, uc.descripcion as descripcion_correlativo, cf.tipo_formato_id as tipo_formato_id, tf.nombre as tipo_formato')
                ->from('Correspondencia_CorrelativosFormatos cf')
                ->innerJoin('cf.Correspondencia_UnidadCorrelativo uc')
                ->innerJoin('cf.Correspondencia_TipoFormato tf')
                ->where('uc.unidad_id = ?',$sf_user->getAttribute('funcionario_unidad_id'))
                ->andWhere('tf.id = ?',1004) // ID POR DEFECTO DE VACACIONES 1004
                ->andWhere('tf.tipo = ?','M')
                ->andWhere('uc.tipo = ?','E')
                ->andWhere('uc.status = ?','A')
                ->orderBy('tf.nombre')
                ->execute();

                $formatos_legitimos = array();
                $i=0;

                if(count($correlativos_activos)>0){

                    foreach ($correlativos_activos as $correlativo_activo) {
                        $formatos_legitimos[$i]=$correlativo_activo->getTipoFormatoId().'|'.
                                                $correlativo_activo->getUnidadId().'|'.
                                                $correlativo_activo->getUnidadCorrelativoId().'|'.                                   
                                                $correlativo_activo->getId().'|'.
                                                $correlativo_activo->getDescripcionCorrelativo();

                        $i++;
                    }

                } else {

                    $correlativo_vacaciones = new Correspondencia_UnidadCorrelativo();
                    $correlativo_vacaciones->setUnidadId($sf_user->getAttribute('funcionario_unidad_id'));
                    $correlativo_vacaciones->setNomenclador('Siglas-Letra-Año-Secuencia');
                    $correlativo_vacaciones->setLetra('(VA)');
                    $correlativo_vacaciones->setSecuencia(1);
                    $correlativo_vacaciones->setStatus('M');
                    $correlativo_vacaciones->setTipo('E');

                    $correlativo_vacaciones->save();

                    $vacaciones_formato = new Correspondencia_CorrelativosFormatos();
                    $vacaciones_formato->setUnidadCorrelativoId($correlativo_vacaciones->getId());
                    $vacaciones_formato->setTipoFormatoId(1004);
                    $vacaciones_formato->save();

                    $formatos_legitimos[0]='1004|'.
                                           $sf_user->getAttribute('funcionario_unidad_id').'|'.
                                           $correlativo_vacaciones->getId().'|'.                                   
                                           $vacaciones_formato->getId().'|';
                }

            $sf_user->setAttribute('formatos_legitimos',$formatos_legitimos);
            $sf_user->setAttribute('call_module_master',sfConfig::get('sf_app_rrhh_url').'vacaciones');
    }
?>

<script>
    function solicitar_vacaciones(){
        
        $("#header_notificacion_derecha").css('right', '-875px');
        $("#content_notificacion_derecha").css('right', '-892px');
        $("#div_espera_documento").show();
            
    
        $('#div_vacaciones_emisores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando emisores...');
        $('#div_vacaciones_receptores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando receptores...');
        $('#div_vacaciones_contenido').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando contenido...');
        
        <?php if(count($configuraciones_vacaciones)>0){ ?>
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/formato',
            type:'POST',
            dataType:'html',
            data:'tipo_formato_id=0',
            success:function(data, textStatus){
                jQuery('#div_vacaciones_contenido').html(data);
            }})
        
            <?php
            echo jq_remote_function(array('update' => 'div_vacaciones_emisores',
            'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/emisores',
            'with'=> "'tipo_formato_id=0'",))
            ?>

            <?php
            echo jq_remote_function(array('update' => 'div_vacaciones_receptores',
            'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/receptores',
            'with'=> "'tipo_formato_id=0'",))
            ?>
        <?php } ?>

        $("#content_notificacion_derecha").animate({right:"+=892px"},1000);
        $("#header_notificacion_derecha").animate({right:"+=892px"},1000);
    };

    function cerrar_solicitud(){
        $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
        $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
        $("#div_espera_documento").hide();
            
        $('#div_vacaciones_emisores').html('');
        $('#div_vacaciones_receptores').html('');
        $('#div_vacaciones_contenido').html('');
    };
</script>

<li class="sf_admin_action_solicitar_vacaciones">
    <a href="#" onclick="javascript:solicitar_vacaciones(); return false;">Solicitar Vacaciones</a>
</li>

<?php 
  $session_cargos = $sf_user->getAttribute('session_cargos');
  $ver_unidad = FALSE; 
  foreach ($session_cargos as $session_cargo) {
      if($session_cargo['cargo_grado_id']==99) $ver_unidad = TRUE; 
  }

  $ver_global = FALSE;
  $sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
  foreach ($session_cargos as $session_cargo) {
      if($session_cargo['cargo_grado_id']==99 && $session_cargo['unidad_id']==$sf_oficinasClave['recursos_humanos']['seleccion']) {
        $ver_global = TRUE; 
      }
  }
  
  if($ver_global == FALSE || $ver_unidad == FALSE)
      $rrhh_delegados = Doctrine::getTable('Acceso_AccionDelegada')->findByUsuarioDelegadoIdAndAccionAndStatus($sf_user->getAttribute('usuario_id'), 'administrar_rrhh', 'A');
  
  if($ver_global == FALSE){
    foreach ($rrhh_delegados as $rrhh_delegado) {
        $parametros = sfYaml::load($rrhh_delegado->getParametros());
        if(isset($parametros['ver_vacaciones_global'])) $ver_global = TRUE;
    }
  }
  
  if($ver_unidad == FALSE){
    foreach ($rrhh_delegados as $rrhh_delegado) {
        $parametros = sfYaml::load($rrhh_delegado->getParametros());
        if(isset($parametros['ver_vacaciones_unidad'])) $ver_unidad = TRUE;
    }
  }
  
  if($ver_unidad == TRUE) { ?>
      <li class="sf_admin_action_reporte_unidad_vacaciones">
        <?php echo link_to(__('Reporte de la Unidad', array(), 'messages'), 'vacaciones/reporteGlobal?tipo=unidad', array()) ?>
      </li>
  <?php }
  
  if($ver_global == TRUE) { ?>
    <li class="sf_admin_action_reporte_global_vacaciones">
      <?php echo link_to(__('Reporte de Global', array(), 'messages'), 'vacaciones/reporteGlobal?tipo=global', array()) ?>
    </li>
<?php } ?>

<div id="div_espera_documento" 
     style="display: none; position: fixed; 
            left: 0px; top: 0px; width: 100%; 
            height: 100%; background-color: black; 
            opacity: 0.4; filter:alpha(opacity=40); 
            z-index: 999;">&nbsp;
</div>

<div id="header_notificacion_derecha">
    <a title="Cerrar" href="#" onclick="javascript:cerrar_solicitud(); return false;">
        <?php echo image_tag('other/menu_close.png'); ?>
    </a>
</div>

<div id="content_notificacion_derecha">
    <h1>Nueva solicitud de Vacaciones</h1>
    
    <?php if(count($configuraciones_vacaciones)>0){ ?>
        <form id="form_enviada" method="post" action="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/create'; ?>" enctype="multipart/form-data">

        <input type="hidden" name="correspondencia[formato][tipo_formato_id]" id="formato_tipo_formato_id" value="0">    
        <input type="hidden" name="correspondencia[identificacion][prioridad]" value="S">
        <input type="hidden" name="correspondencia[identificacion][metodo_correlativo]" value="I">

        <div class="inner" style="background-color: #ebebeb; z-index: 200001; height:100%;">
            <div id="vacaciones_ver" style="height:100%; width: 835px; top: 40px; left: 10px;">
                <div id="div_vacaciones_emisores"></div>
                <div id="div_vacaciones_receptores"></div>
                <div id="div_vacaciones_contenido"></div>

        <hr/>
        <input type="submit" id="guardar_documento" value="Enviar Solicitud"><br/><br/>
            </div>
        </div>
        </form>
    <?php } else { ?>
        <div class="error">
            No se han definido las configuraciones necesarias para el funcionamiento de la solicitud de vacaciones.
            Por favor comunicate con la unidad de Tecnologia y Recursos humanos para generar dichas configuraciones. 
        </div>
    <?php } ?>
</div>
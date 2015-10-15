<?php use_helper('jQuery'); ?>

<?php
    if(!$sf_user->getAttribute('tercerizado')) {
        $session_funcionario_unidad_id = $sf_user->getAttribute('funcionario_unidad_id');
    } else {
        $tercerizado = $sf_user->getAttribute('tercerizado');
        $session_funcionario_unidad_id = $tercerizado['unidad_id'];
    }

    $configuraciones_reposos = Doctrine_Query::create()
                                  ->select('c.*')
                                  ->from('Rrhh_Configuraciones c')
                                  ->where('c.modulo = ?', 'reposos')
                                  ->execute();

    if(count($configuraciones_reposos)>0){
        $correlativos_activos = Doctrine_Query::create()
                ->select('cf.id, uc.id as unidad_correlativo, uc.unidad_id as unidad_id, uc.descripcion as descripcion_correlativo, cf.tipo_formato_id as tipo_formato_id, tf.nombre as tipo_formato')
                ->from('Correspondencia_CorrelativosFormatos cf')
                ->innerJoin('cf.Correspondencia_UnidadCorrelativo uc')
                ->innerJoin('cf.Correspondencia_TipoFormato tf')
                ->where('uc.unidad_id = ?',$session_funcionario_unidad_id)
                ->andWhere('tf.id = ?',1007) // ID POR DEFECTO DE REPOSOS 1007
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

                    $correlativo_reposos = new Correspondencia_UnidadCorrelativo();
                    $correlativo_reposos->setUnidadId($session_funcionario_unidad_id);
                    $correlativo_reposos->setNomenclador('Siglas-Letra-Año-Secuencia');
                    $correlativo_reposos->setLetra('(RE)');
                    $correlativo_reposos->setSecuencia(1);
                    $correlativo_reposos->setStatus('M');
                    $correlativo_reposos->setTipo('E');

                    $correlativo_reposos->save();

                    $reposos_formato = new Correspondencia_CorrelativosFormatos();
                    $reposos_formato->setUnidadCorrelativoId($correlativo_reposos->getId());
                    $reposos_formato->setTipoFormatoId(1007);
                    $reposos_formato->save();

                    $formatos_legitimos[0]='1007|'.
                                           $session_funcionario_unidad_id.'|'.
                                           $correlativo_reposos->getId().'|'.                                   
                                           $reposos_formato->getId().'|';
                }

            $sf_user->setAttribute('formatos_legitimos',$formatos_legitimos);
            
            if(!$sf_user->getAttribute('tercerizado')) {
                $sf_user->setAttribute('call_module_master',sfConfig::get('sf_app_rrhh_url').'reposos');
            } else {
                $sf_user->setAttribute('call_module_master',sfConfig::get('sf_app_rrhh_url').'reposos/reporteGlobal?tipo='.$sf_user->getAttribute('call_module_master_reporteGlobal_reposo_tipo'));
            }
    }
?>

<div id="sf_admin_container">
    <h1>Nuevo registro de Reposo</h1>

    <?php if(count($configuraciones_reposos)>0){ ?>

        <form id="form_enviada" method="post" action="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/create'; ?>" enctype="multipart/form-data">

        <input type="hidden" name="correspondencia[formato][tipo_formato_id]" id="formato_tipo_formato_id" value="0">    
        <input type="hidden" name="correspondencia[identificacion][prioridad]" value="S">
        <input type="hidden" name="correspondencia[identificacion][metodo_correlativo]" value="I">

        <div class="inner" style="background-color: #ebebeb; z-index: 200001; height:100%;">
            <div id="reposos_ver" style="height:100%; width: 835px; top: 40px; left: 10px;">
                <div id="div_reposos_emisores">ccccc</div>
                <div id="div_reposos_receptores"></div>
                <div id="div_reposos_contenido"></div>
                <div id="div_reposos_adjuntos"></div>
                <div id="div_reposos_fisicos"></div>

        <hr/>
        <input type="submit" id="guardar_documento" value="Enviar Solicitud"><br/><br/>
            </div>
        </div>
        </form>


        <script>
            $('#div_reposos_emisores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando emisores...');
            $('#div_reposos_receptores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando receptores...');
            $('#div_reposos_contenido').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando contenido...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/formato',
                type:'POST',
                dataType:'html',
                data:'tipo_formato_id=0',
                success:function(data, textStatus){
                    $('#div_reposos_contenido').html(data);
                }})

                <?php
                echo jq_remote_function(array('update' => 'div_reposos_emisores',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/emisores',
                'with'=> "'tipo_formato_id=0'",))
                ?>

                <?php
                echo jq_remote_function(array('update' => 'div_reposos_receptores',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/receptores',
                'with'=> "'tipo_formato_id=0'",))
                ?>

                <?php
                echo jq_remote_function(array('update' => 'div_reposos_adjuntos',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/adjuntos',
                'with'=> "'tipo_formato_id=0'",))
                ?>

                <?php
                echo jq_remote_function(array('update' => 'div_reposos_fisicos',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/fisicos',
                'with'=> "'tipo_formato_id=0'",))
                ?>
        </script>
    <?php } else { ?>
        <div class="error">
            No se han definido las configuraciones necesarias para el funcionamiento del registro de reposos.
            Por favor comunicate con la unidad de Tecnologia y Recursos humanos para generar dichas configuraciones. 
        </div>
    <?php } ?>
</div>
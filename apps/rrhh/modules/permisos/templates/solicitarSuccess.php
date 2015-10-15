<?php use_helper('jQuery'); ?>

<?php
    if(!$sf_user->getAttribute('tercerizado')) {
        $session_funcionario_unidad_id = $sf_user->getAttribute('funcionario_unidad_id');
    } else {
        $tercerizado = $sf_user->getAttribute('tercerizado');
        $session_funcionario_unidad_id = $tercerizado['unidad_id'];
    }

    $configuraciones_permisos = Doctrine_Query::create()
                                  ->select('c.*')
                                  ->from('Rrhh_Configuraciones c')
                                  ->where('c.modulo = ?', 'permisos')
                                  ->execute();

    if(count($configuraciones_permisos)>0){
        $correlativos_activos = Doctrine_Query::create()
                ->select('cf.id, uc.id as unidad_correlativo, uc.unidad_id as unidad_id, uc.descripcion as descripcion_correlativo, cf.tipo_formato_id as tipo_formato_id, tf.nombre as tipo_formato')
                ->from('Correspondencia_CorrelativosFormatos cf')
                ->innerJoin('cf.Correspondencia_UnidadCorrelativo uc')
                ->innerJoin('cf.Correspondencia_TipoFormato tf')
                ->where('uc.unidad_id = ?',$session_funcionario_unidad_id)
                ->andWhere('tf.id = ?',1006) // ID POR DEFECTO DE PERMISOS 1006
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

                    $correlativo_permisos = new Correspondencia_UnidadCorrelativo();
                    $correlativo_permisos->setUnidadId($session_funcionario_unidad_id);
                    $correlativo_permisos->setNomenclador('Siglas-Letra-Año-Secuencia');
                    $correlativo_permisos->setLetra('(PE)');
                    $correlativo_permisos->setSecuencia(1);
                    $correlativo_permisos->setStatus('M');
                    $correlativo_permisos->setTipo('E');

                    $correlativo_permisos->save();

                    $permisos_formato = new Correspondencia_CorrelativosFormatos();
                    $permisos_formato->setUnidadCorrelativoId($correlativo_permisos->getId());
                    $permisos_formato->setTipoFormatoId(1006);
                    $permisos_formato->save();

                    $formatos_legitimos[0]='1006|'.
                                           $session_funcionario_unidad_id.'|'.
                                           $correlativo_permisos->getId().'|'.                                   
                                           $permisos_formato->getId().'|';
                }

            $sf_user->setAttribute('formatos_legitimos',$formatos_legitimos);
            
            if(!$sf_user->getAttribute('tercerizado')) {
                $sf_user->setAttribute('call_module_master',sfConfig::get('sf_app_rrhh_url').'permisos');
            } else {
                $sf_user->setAttribute('call_module_master',sfConfig::get('sf_app_rrhh_url').'permisos/reporteGlobal?tipo='.$sf_user->getAttribute('call_module_master_reporteGlobal_permiso_tipo'));
            }
    }
?>

<div id="sf_admin_container">
    <h1>Nueva solicitud de Permiso</h1>

    <?php if(count($configuraciones_permisos)>0){ ?>

        <form id="form_enviada" method="post" action="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/create'; ?>" enctype="multipart/form-data">

        <input type="hidden" name="correspondencia[formato][tipo_formato_id]" id="formato_tipo_formato_id" value="0">    
        <input type="hidden" name="correspondencia[identificacion][prioridad]" value="S">
        <input type="hidden" name="correspondencia[identificacion][metodo_correlativo]" value="I">

        <div class="inner" style="background-color: #ebebeb; z-index: 200001; height:100%;">
            <div id="permisos_ver" style="height:100%; width: 835px; top: 40px; left: 10px;">
                <div id="div_permisos_emisores">ccccc</div>
                <div id="div_permisos_receptores"></div>
                <div id="div_permisos_contenido"></div>
                <div id="div_permisos_adjuntos"></div>
                <div id="div_permisos_fisicos"></div>

        <hr/>
        <input type="submit" id="guardar_documento" value="Enviar Solicitud"><br/><br/>
            </div>
        </div>
        </form>


        <script>
            $('#div_permisos_emisores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando emisores...');
            $('#div_permisos_receptores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando receptores...');
            $('#div_permisos_contenido').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando contenido...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/formato',
                type:'POST',
                dataType:'html',
                data:'tipo_formato_id=0',
                success:function(data, textStatus){
                    $('#div_permisos_contenido').html(data);
                }})

                <?php
                echo jq_remote_function(array('update' => 'div_permisos_emisores',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/emisores',
                'with'=> "'tipo_formato_id=0'",))
                ?>

                <?php
                echo jq_remote_function(array('update' => 'div_permisos_receptores',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/receptores',
                'with'=> "'tipo_formato_id=0'",))
                ?>

                <?php
                echo jq_remote_function(array('update' => 'div_permisos_adjuntos',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/adjuntos',
                'with'=> "'tipo_formato_id=0'",))
                ?>

                <?php
                echo jq_remote_function(array('update' => 'div_permisos_fisicos',
                'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/fisicos',
                'with'=> "'tipo_formato_id=0'",))
                ?>
        </script>
    <?php } else { ?>
        <div class="error">
            No se han definido las configuraciones necesarias para el funcionamiento de la solicitud de permisos.
            Por favor comunicate con la unidad de Tecnologia y Recursos humanos para generar dichas configuraciones. 
        </div>
    <?php } ?>
</div>
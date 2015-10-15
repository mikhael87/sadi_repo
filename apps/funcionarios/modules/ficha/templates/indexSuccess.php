<?php use_helper('I18N', 'Date') ?>
<?php include_partial('ficha/header') ?>
<?php use_helper('jQuery'); ?>
<link rel="stylesheet" type="text/css" media="screen" href="/css/ficha.css" />
<script type="text/javascript" src="/js/ficha.js"></script>


<div id="sf_admin_container" style="width: 100%;">
    <h1>Ficha Personal</h1>

    <div id="sf_admin_content">
        <div style="position: relative;">
            <div style="position: absolute; left: 0px; top: 0px;">
                <div style="position: relative; width: 560px; border: 1px solid; padding: 5px;" class="trans">
                    <div style="background-color: #CCCCFF; padding: 5px; position: relative;">
                        <b>Información Basica</b>
                        <div style="position: absolute; right: 5px; top: 5px;">
                            <a href="#"  style="right: 0px;" title="Editar"  onclick="javascript:abrir_notificacion_derecha('infoBasica'); return false;">
                                <?php echo image_tag('icon/edit.png'); ?>
                            </a>
                        </div>
                    </div>                    
                    
                    <div id="ficha_informacion_basica">
                        <div style="position: absolute; top: 25px;">
                            <img style="padding: 5px;" src="/images/icon/job64.png"/>
                        </div>   
                        <div id="ficha_info_basica_content" style="padding-left: 75px; width: 480px;">
                            <?php include_partial('ficha/info_basica', array('basica' => $basica)); ?> 
                        </div> 
                    </div>
                </div>
                
                <div style="height: 10px;"></div>
                
                <div style="position: relative; width: 560px; border: 1px solid; padding: 5px;" class="trans">
                    <div style="background-color: #CCCCFF; padding: 5px; position: relative;">
                        <b>Información Familiar</b>
                        <div style="position: absolute; right: 5px; top: 5px;">
                            <a href="#" style="right: 0px;" title="Agregar"  onclick="javascript:abrir_notificacion_derecha('infoFamiliar?familiar_accion=nuevo&familiar_id=0'); return false;">
                                <?php echo image_tag('icon/add.png'); ?>
                            </a>
                        </div>
                    </div>
                    <div id="ficha_info_familiar">
                        <div style="position: absolute; top: 25px; ">
                            <img style="padding: 5px;" src="/images/icon/family64.png">
                        </div>   
                        <div id="ficha_info_familiar_content" style="padding-left: 75px; width: 480px;">
                            <?php include_partial('ficha/info_familiar', array('familiar' => $familiar)); ?> 
                        </div>       
                    </div>
                </div>
                
                <div style="height: 10px;"></div>
                
                
                <div style="position: relative; width: 560px; border: 1px solid; padding: 5px;" class="trans">
                    <div  style="background-color: #CCCCFF; padding: 5px;">
                        <b>Información Academica</b>
                           &nbsp;&nbsp;Agregar información: 
                           <a href="#" onclick="javascript:abrir_notificacion_derecha('infoEduuniversitaria?eduuniversitaria_accion=nuevo&eduuniversitaria_id=0'); return false;">Univeristaria&nbsp;<?php echo image_tag('icon/add.png'); ?></a>
                         | <a href="#" onclick="javascript:abrir_notificacion_derecha('infoEdumedia?edumedia_accion=nuevo&edumedia_id=0'); return false;">Media&nbsp;<?php echo image_tag('icon/add.png'); ?></a>
                         | <a href="#" onclick="javascript:abrir_notificacion_derecha('infoEduadicional?eduadicional_accion=nuevo&eduadicional_id=0'); return false;">Adicional&nbsp;<?php echo image_tag('icon/add.png'); ?></a>
                    </div>
                    <div id="ficha_info_academica">
                        <div style="position: absolute; top: 25px;">
                            <img style="padding: 5px;" src="/images/icon/academic64.png"> 
                        </div>   
                        <div id="ficha_info_academica_content" style="padding-left: 75px; width: 480px;">
                            <?php include_partial('ficha/info_academica', array('eduuniveristaria' => $eduuniversitaria, 'edumedia' => $edumedia, 'eduadicional' => $eduadicional)); ?> 
                        </div>    
                    </div>
                </div>
                
                
                <br><br><br>
            </div>
            

            <!--###########################################################-->
            <!--###########################################################-->
            <!--#####################SEGUNDA COLUMNA#######################-->
            <!--###########################################################-->
            <!--###########################################################-->
            

            <div style="position: absolute; left: 580px; top: 0px;">
                <div style="position: relative; width: 350px; border: 1px solid; padding: 5px;" class="trans">

                    <div style="background-color: #CCCCFF; padding: 5px; position: relative;">
                        <b>Información Corporal</b>
                        <div style="position: absolute; right: 5px; top: 5px;">
                            <a href="#" style="right: 0px;" title="Editar" onclick="javascript:abrir_notificacion_derecha('infoCorporal'); return false;">
                                <?php echo image_tag('icon/edit.png'); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div id="ficha_corporal">
                        <div style="position: absolute; top: 25px;">
                            <img style="padding: 5px;" src="/images/icon/corporal64.png"/>
                        </div>   
                        <div id="ficha_corporal_content" style="padding-left: 75px; width: 270px;">
                            <?php include_partial('ficha/info_corporal', array('corporal' => $corporal)); ?>
                        </div>
                    </div>
                </div>
                
                <div style="height: 10px;"></div>
               
                <div style="position: relative; width: 350px; border: 1px solid; padding: 5px;" class="trans">
                    <div style="background-color: #CCCCFF; padding: 5px; position: relative;">
                        <b>Información de Contacto</b>                    
                        <div style="position: absolute; right: 5px; top: 5px;">
                            <a href="#" style="right: 0px;" title="Editar" onclick="javascript:abrir_notificacion_derecha('infoContacto'); return false;">
                                <?php echo image_tag('icon/edit.png'); ?>
                            </a>
                        </div>
                    </div>
                    <div id="ficha_contacto">
                        <div style="position: absolute; top: 25px;">
                            <img style="padding: 5px;" src="/images/icon/contact64.png"/>
                        </div>   
                        <div id="ficha_contacto_content" style="padding-left: 75px; width: 270px;">
                            <?php include_partial('ficha/info_contacto', array('contacto' => $contacto)); ?> 
                        </div>
                    </div>
                </div>
                
                <div style="height: 10px;"></div>
               
                <div style="position: relative; width: 350px; border: 1px solid; padding: 5px;" class="trans">
                    <div style="background-color: #CCCCFF; padding: 5px; position: relative;">
                        <b>Información de Idiomas</b>
                        <div style="position: absolute; right: 5px; top: 5px;">
                            <a href="#" style="right: 0px;" title="Editar" onclick="javascript:abrir_notificacion_derecha('infoIdioma'); return false;">
                                <?php echo image_tag('icon/edit.png'); ?>
                            </a>
                        </div>
                    </div>
                    <div id="ficha_contacto">
                        <div style="position: absolute; top: 25px;">
                            <img style="padding: 5px;" src="/images/icon/helloWorld.png"/>
                        </div>   
                        <div id="ficha_idioma_content" style="padding-left: 75px; width: 270px;">
                            <?php include_partial('ficha/info_idioma', array('idioma' => $idioma)); ?> 
                        </div>
                    </div>
                </div>
                <div style="height: 10px;"></div>
               
                <div style="position: relative; width: 350px; border: 1px solid; padding: 5px;" class="trans">
                    <div style="background-color: #CCCCFF; padding: 5px;  position: relative;">
                        <b>Información de Grupo Social</b>
                        <div style="position: absolute; right: 5px; top: 5px;">
                            <a href="#" style="right: 0px;" title="Editar" onclick="javascript:abrir_notificacion_derecha('infoGruposocial'); return false;">
                                <?php echo image_tag('icon/edit.png'); ?>
                            </a>
                        </div>
                    </div>
                    <div id="ficha_grupo">
                        <div style="position: absolute; top: 25px;">
                            <img style="padding: 5px;" src="/images/icon/group64.png"/>
                        </div>   
                        <div id="ficha_grupo_content" style="padding-left: 75px; width: 270px;">
                            <?php include_partial('ficha/info_gruposocial', array('grupo' => $grupo)); ?> 
                        </div>
                    </div>
                </div>
               <div style="height: 10px;"></div>

                
                <div style="position: relative; width: 350px; border: 1px solid; padding: 5px;" class="trans">
                    <div style="background-color: #CCCCFF; padding: 5px; position: relative;">
                        <b>Información Residencial</b>
                        <div style="position: absolute; right: 5px; top: 5px;">
                            <a href="#" style="right: 0px;" title="Agregar" onclick="javascript:abrir_notificacion_derecha('infoResidencial?residencia_accion=nuevo&res_id=0'); return false;">
                                <?php echo image_tag('icon/add.png'); ?>&nbsp;
                            </a>
                        </div>
                    </div>
                    <div id="ficha_residencial">
                        <div style="position: absolute; top: 25px;">
                            <img style="padding: 5px;" src="/images/icon/home64.png">
                        </div>   
                        <div id="ficha_residencial_content" style="padding-left: 75px; width: 270px;">
                            <?php include_partial('ficha/info_residencial', array('residencia' => $residencia)); ?> 
                        </div>
                    </div>
                </div>   
            </div>
        </div>
    </div>
</div>
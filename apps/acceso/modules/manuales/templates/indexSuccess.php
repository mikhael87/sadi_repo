<h3>Ayuda</h3>
<b><a href="#" onclick="javascript:ver_manual('1'); return false;">Antes de Comenzar</a></b><br/>
<br/>
<b><a href="#" onclick="javascript:ver_manual('2'); return false;">Conoce el escritorio de trabajo del SIGLA</a></b><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('2.1'); return false;">Area de Contenido</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('2.2'); return false;">Barra de Inicio</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('2.3'); return false;">Menu de Herramientas</a><br/>
<br/>
<b><a href="#" onclick="javascript:ver_manual('3'); return false;">Correspondencia</a></b><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('3.1'); return false;">Configurar la correspondencia</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('3.1.1'); return false;">Grupos</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('3.1.2'); return false;">Correlativos</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('3.2'); return false;">Enviar una correspondencia</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('3.3'); return false;">Recibir una correspondencia interna</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('3.4'); return false;">Recibir una correspondencia externa</a><br/>
<hr>

<?php if ($sf_user->hasCredential(array('Administrador','Root'), false)) { ?>
<h3>Documentacion</h3>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=documentacion/SIGLAS_informe.pdf'; ?>">Descripcion del SIGLAS</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=documentacion/SIGLAS_requerimientos.pdf'; ?>">Tecnologias y Requerimientos</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=documentacion/SIGLAS_flujos_correspondencia.pdf'; ?>">Flujos de correspondencia</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diagramas Entidad-Relacion<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_acceso.jpeg'; ?>">Acceso</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_archivo.jpeg'; ?>">Archivo</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_comunicaciones.jpeg'; ?>">Comunicaciones (SMS - Chat)</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_correspondencia.jpeg'; ?>">Correspondencia</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_eventos.jpeg'; ?>">Eventos</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_funcionarios.jpeg'; ?>">Funcionarios</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_organigrama.jpeg'; ?>">Organigrama</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_public.jpeg'; ?>">Public</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_rrhh.jpeg'; ?>">Recursos Humanos</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_seguridad.jpeg'; ?>">Seguridad -Visitantes</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_siglas.jpeg'; ?>">SVN - Interoperabilidad</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/descargar?archivo=diagramasER/jpeg/siglas_vehiculos.jpeg'; ?>">Vehiculos (GPS)</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo sfConfig::get('sf_app_acceso_url').'manuales/diccionarioDB'; ?>" target="_blank">Diccionario de Datos</a><br/>

<br/>
<h3>Drivers de tarjetas y token critográficos</h3>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('2'); return false;">Tarjeta Gemalto</a><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:ver_manual('2'); return false;">Token serie epass3000</a><br/>
<br/>
<hr>
<?php } ?>

<?php if ($sf_user->hasCredential(array('Root'), false)) { ?>
<h3><a href="<?php echo sfConfig::get('sf_app_acceso_url').'actualizacion'; ?>">Actualizaciones</a></h3>
<?php } ?>
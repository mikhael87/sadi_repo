<?php

require_once dirname(__FILE__) . '/../lib/recibidaGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/recibidaGeneratorHelper.class.php';

/**
 * recibida actions.
 *
 * @package    siglas-(institucion)
 * @subpackage recibida
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class recibidaActions extends autoRecibidaActions {

    public function executeIndex(sfWebRequest $request) {
        $this->getUser()->getAttributeHolder()->remove('header_ruta');
        $this->getUser()->getAttributeHolder()->remove('correspondencia');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
        $this->getUser()->getAttributeHolder()->remove('seguimiento_externa');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_padre_id');
        $this->getUser()->getAttributeHolder()->remove('pag_seguimiento_atras');
        $this->getUser()->getAttributeHolder()->remove('call_module_master');
        $this->getUser()->getAttributeHolder()->remove('tercerizado');

        $ultima_tocada = Doctrine::getTable('Correspondencia_UltimaVista')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        if ($ultima_tocada) {
            $this->getUser()->setAttribute('ultima_vista_recibida', $ultima_tocada->getCorrespondenciaRecibidaId());
        } else {
            $this->getUser()->setAttribute('ultima_vista_recibida', 0);
        }

        // sorting
        if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort'))) {
            $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
        }

        // pager
        if ($request->getParameter('page')) {
            $this->setPage($request->getParameter('page'));
        }

        $this->pager = $this->getPager();
        $this->sort = $this->getSort();
    }

    public function unidadReceptoraReal($correspondencia_id) {
        // DETERMINAR LA UNIDAD POR LA CUAL PUEDE LEER LA CORRESPONDENCIA
        
        // BUSCAR UNIDADES DE RECEPTORES ESTABLECIDOS
        $unidades_receptoras = array();
        $unidad_recibe_id = '';
        $receptores_establecidos = Doctrine::getTable('Correspondencia_Receptor')->findByCorrespondenciaIdAndEstablecido($correspondencia_id, 'S');
        foreach ($receptores_establecidos as $receptor_establecido) {
            $unidades_receptoras[] = $receptor_establecido->getUnidadId();
            if($receptor_establecido->getFuncionarioId() == $this->getUser()->getAttribute('funcionario_id')){
                // SI EL FUNCIONARIO LOGUEADO ES ESTABLECIDO COMO RECEPTOR SE SELECCIONA LA UNIDAD POR LA CUAL RECIBE
                $unidad_recibe_id = $receptor_establecido->getUnidadId();
            }
        }
        
        if($unidad_recibe_id==''){
            // EN CASO DE NO ENCONTRAR LA UNIDAD, BUSCAR SI LE FUE ASIGANADA LA CORRESPONDENCIA COMO UNA TAREA
            $receptor_asignado = Doctrine::getTable('Correspondencia_Receptor')->findOneByCorrespondenciaIdAndFuncionarioIdAndEstablecido($correspondencia_id, $this->getUser()->getAttribute('funcionario_id'), 'A');

            if($receptor_asignado){
                $unidad_recibe_id = $receptor_asignado->getUnidadId();
            }
        }
        
        if($unidad_recibe_id==''){
            // BUSCAR LAS UNIDADES A LA QUE PERTENECE EL FUNCIONARIO CON PERMISO DE LEER
            $unidades_receptoras = array_unique($unidades_receptoras);
            
            $funcionario_unidades_leer = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'),'leer');

            foreach($funcionario_unidades_leer as $unidad_leer) {
                if(array_search($unidad_leer->getAutorizadaUnidadId(), $unidades_receptoras)>=0){
                    $unidad_recibe_id = $unidad_leer->getAutorizadaUnidadId();
                }
            }
        }
        
        return $unidad_recibe_id;
    }
    
    public function executeAsignar(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaRecibida($id);
        
        $unidad_recibe_id = $this->unidadReceptoraReal($id);
        
        $this->funcionarios_directos = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($unidad_recibe_id));
        $instrucciones_unidad = Doctrine::getTable('Correspondencia_Instruccion')->instruccionesUnidad($unidad_recibe_id);
        
        if(count($instrucciones_unidad)>0){
            $instrucciones=array();
            foreach ($instrucciones_unidad as $instruccion_unidad) {
                $instrucciones[] = $instruccion_unidad->getDescripcion();
            }
        } else {
            $instrucciones = array ('Archivar', 'Revisar', 'Para su conocimiento',
                                    'Analizar', 'Hacer seguimiento', 'Procesar',
                                    'Tramitar', 'Informarme', 'Traer a cuenta',
                                    'Preparar Informe', 'Preparar Respuesta',
                                    'Contestar al Interesado', 'Atender o darle audiencia',
                                    'Proceder', 'Coordinar');
        }
        
        $this->getUser()->setAttribute('correspondencia_id', $id);
        $this->unidad_asigna_id = $unidad_recibe_id;
        $this->instrucciones = $instrucciones;
    }
    
    public function executeAsignado(sfWebRequest $request) {
        
        $asignacion = $request->getParameter('asignacion');
        $correspondencia_id = $this->getUser()->getAttribute('correspondencia_id');

        $this->executeUltimaVistaRecibida($correspondencia_id);

        try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
            
            $correspondencia_receptor = Doctrine::getTable('Correspondencia_Receptor')->findOneByCorrespondenciaIdAndUnidadIdAndFuncionarioId($correspondencia_id,$asignacion['unidad_id'],$asignacion['funcionario_id']);
            
            if($correspondencia_receptor){
                $correspondencia_receptor->setEstablecido('A');
                $correspondencia_receptor->save();
            } else {
                $correspondencia_receptor = new Correspondencia_Receptor();

                $correspondencia_receptor->setCorrespondenciaId($correspondencia_id);
                $correspondencia_receptor->setUnidadId($asignacion['unidad_id']);
                $correspondencia_receptor->setFuncionarioId($asignacion['funcionario_id']);
                $correspondencia_receptor->setCopia('N');
                $correspondencia_receptor->setEstablecido('A');
                $correspondencia_receptor->setPrivado('S'); // REVISAR PORQUE ESTA PRIVADO
                $correspondencia_receptor->save();
            }
            
            $descripcion = '<b>'.$asignacion['instruccion'].'</b><br/>'.$asignacion['descripcion'];
            $parametros = array('aplicacion'=>'correspondencia','correspondencia_id'=>$correspondencia_id,'receptor_id'=>$correspondencia_receptor->getId());
            $parametros = sfYAML::dump($parametros);
            
            $tarea = new Comunicaciones_Tarea();
            $tarea->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $tarea->setDescripcion($descripcion);
            $tarea->setPrioridad($asignacion['prioridad']);
            $tarea->setParametros($parametros);
            $tarea->save();
            
            $funcionario_funcionario = new Comunicaciones_FuncionarioTarea();
            $funcionario_funcionario->setTareaId($tarea->getId());
            $funcionario_funcionario->setFuncionarioId($asignacion['funcionario_id']);
            $funcionario_funcionario->save();

            $this->getUser()->setFlash('notice', ' La correspondencia se ha asignado con exito');
            
            $conn->commit();
        } catch(Exception $e) {
            $conn->rollBack();
            echo $e;
        }

        exit();
        $this->redirect('recibida/index');
    }
    
    public function executeDevolver(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaRecibida($id);

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        $correspondencia->setStatus('D');
        $correspondencia->save();

        $q = Doctrine_Query::create($conn);
        $q->update('Correspondencia_FuncionarioEmisor')->set('firma', '?', 'N')
                ->where('correspondencia_id = ?', $id)->execute();

        $this->getUser()->setFlash('notice', ' La correspondencia se ha devuelto con exito');

        $this->redirect('recibida/index');
    }

    public function executeUltimaVistaRecibida($id) {
        $ultima_tocada = Doctrine::getTable('Correspondencia_UltimaVista')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        if ($ultima_tocada) {
            $ultima_tocada->setCorrespondenciaRecibidaId($id);
            @$ultima_tocada->save();
        } else {
            $ultima_tocada = new Correspondencia_UltimaVista();
            $ultima_tocada->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $ultima_tocada->setCorrespondenciaEnviadaId($id);
            $ultima_tocada->setCorrespondenciaRecibidaId($id);
            $ultima_tocada->setCorrespondenciaExternaId($id);
            $ultima_tocada->save();
        }
    }

    public function executeResponder(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaRecibida($id);
        $this->getUser()->setAttribute('correspondencia_padre_id', $id);

        $this->redirect('formatos/index');
    }

    public function executeSeguimiento(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $this->executeUltimaVistaRecibida($id);
        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        $this->getUser()->setAttribute('correspondencia_id', $id);
        $this->getUser()->setAttribute('correspondencia_grupo', $correspondencia->getGrupoCorrespondencia());

        $this->redirect('seguimiento/index');
    }

    public function executeAbrirFormato(sfWebRequest $request) {
        $id = $request->getParameter('id');
        
        // BUSCAMOS LA UNIDAD REAL POR LA CUAL RECIBE EL FUNCIONARIO LOGUEADO
        // YA QUE PUEDE RECIBIR PORQUE ES DIRECTA PARA EL, ES DE UNA UNIDAD POR LA CUAL ESTA AUTORIZADO A LEER (QUE PUEDEN SER VARIAS)
        $unidad_recibe_id = $this->unidadReceptoraReal($id);

        $correspondencia_receptor = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondenciayReceptorUnidadFuncionario($id, $unidad_recibe_id, $this->getUser()->getAttribute('funcionario_id'));

        if (count($correspondencia_receptor) > 0) {
            // Es un receptor establecido de la correspondencia o autorizado que ya la leyo
            foreach ($correspondencia_receptor as $correspondencia_receptor_list) {
                if (!$correspondencia_receptor_list->getFRecepcion()) { //no lo ha leido
                    $correspondencia_receptor_list->setFRecepcion(date('Y-m-d H:i:s'));
                    @$correspondencia_receptor_list->save();

                    $this->getUser()->setFlash('notice', ' Se ha registrado la lectura de esta correspondencia exitosamente');
                }
            }
        } else {
            // Es un receptor autorizado que no ha leido
            $correspondencia_receptor = new Correspondencia_Receptor();

            $correspondencia_receptor->setCorrespondenciaId($id);
            $correspondencia_receptor->setUnidadId($unidad_recibe_id);
            $correspondencia_receptor->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $correspondencia_receptor->setFRecepcion(date('Y-m-d H:i:s'));
            $correspondencia_receptor->setCopia('N');
            $correspondencia_receptor->setEstablecido('N');
            $correspondencia_receptor->setPrivado('N');

            @$correspondencia_receptor->save();

            $this->getUser()->setFlash('notice', ' Se ha registrado la lectura de esta correspondencia exitosamente');
        }

        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);

        if ($correspondencia->getStatus() == 'E') {
            $correspondencia->setStatus('L');
            $correspondencia->setIdCreate($correspondencia->getIdCreate());
            $correspondencia->setIdUpdate($correspondencia->getIdUpdate());
            @$correspondencia->save();

            $this->getUser()->setFlash('notice', ' Se ha registrado la recepción de esta correspondencia exitosamente');
        }

        $this->redirect('formatos/show?idc='.$id);
    }

    public function executeAbrirDetalles(sfWebRequest $request) {
        $id = $request->getParameter('id');
        $this->executeUltimaVistaRecibida($id);
        $this->archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($id);
        $this->fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($id);
    }

    public function executeExcel(sfWebRequest $request) {
        $tableMethod = $this->configuration->getTableMethod();
        if (null === $this->filters) {
            $this->filters = $this->configuration->getFilterForm($this->getFilters());
        }

        $this->filters->setTableMethod($tableMethod);

        $query = $this->filters->buildQuery($this->getFilters());

        $this->excel = $query->execute();
        $this->setLayout(false);
        $this->getResponse()->clearHttpHeaders();
    }

    public function executeOrganismos(sfWebRequest $request) {
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');

        $req = Doctrine::getTable('Organismos_Organismo')->getNombres($string);
        $results = array();
        if (count($req) > 0) {
            foreach ($req as $result)
                $results[$result->getId()] = $result->getNombre();
            return $this->renderText(json_encode($results));
        } else {
            $results[0] = '';
            return $this->renderText(json_encode($results));
        }
    }

    public function executeEstadisticas(sfWebRequest $request){
        $boss= false;
        if($this->getUser()->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
        }

        $funcionario_unidades_leer = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'),'leer');

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $leer_array= array();
        foreach($funcionario_unidades_leer as $unidades_leer) {
            $leer_array[]= $unidades_leer->getAutorizadaUnidadId();
        }

        $nonrepeat= array_merge($cargo_array, $leer_array);

        $funcionario_unidades= array();
        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }
        
        $this->funcionario_unidades = $funcionario_unidades;
    }
    
    public function executeEstadisticaSeleccionada(sfWebRequest $request){
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        if(!$request->getParameter('fi'))
        {
            if(!$request->getParameter('ff'))
            {
                $fecha_inicio='2005-12-18 00:00:00';
                $fecha_final= date('Y-m-d H:i:s');
            }
            else
            {
                $fecha_inicio='2005-12-18 00:00:00';
                $fecha_final=$request->getParameter('ff')." 23:59:59";
            }
        }
        elseif(!$request->getParameter('ff'))
        {
            $fecha_inicio=$request->getParameter('fi')." 00:00:00";
            $fecha_final= date('Y-m-d H:i:s');
        }
        else
        {
            $fecha_inicio=$request->getParameter('fi')." 00:00:00";
            $fecha_final=$request->getParameter('ff')." 23:59:59";
        }
        
        $unidad_id = $request->getParameter('unidad_id');
        $estadistica_tipo = $request->getParameter('tipo');
        
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        
        //LA UNIDAD_ID VIENE POR REQUEST SIN EMBARGO REALIZAR DOBLE VALIDACION
        $boss= false;
        if($this->getUser()->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
        }

        $funcionario_unidades_leer = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'),'leer');

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $leer_array= array();
        foreach($funcionario_unidades_leer as $unidades_leer) {
            $leer_array[]= $unidades_leer->getAutorizadaUnidadId();
        }

        $nonrepeat= array_merge($cargo_array, $leer_array);

        $funcionario_unidades= array();
        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }
        //LA UNIDAD_ID VIENE POR REQUEST SIN EMBARGO REALIZAR DOBLE VALIDACION
        

        $autorizado=false;
        foreach ($funcionario_unidades as $unidad_autorizada){
            if($unidad_autorizada == $unidad_id)
                $autorizado=true;
        }
        
        if($autorizado==true){
            $estadistica = new Correspondencia_CorrespondenciaStatistic();
            
            eval('$estadistica_datos = $estadistica->'.$estadistica_tipo.'($unidad_id, $fecha_inicio,$fecha_final);');
            
            $this->estadistica_datos = $estadistica_datos;
            $this->fecha = "Estadistica generada desde: ".date('d/m/Y',  strtotime($fecha_inicio))." Hasta: ".date('d/m/Y',  strtotime($fecha_final));
            $this->unidad_id = $unidad_id;
            
            $this->setTemplate('estadisticas/'.$estadistica_tipo);
            
        } else {
            echo "No esta autorizado para revisar las estadisticas de esta unidad";
            exit();
        }
        
                  
    }
}
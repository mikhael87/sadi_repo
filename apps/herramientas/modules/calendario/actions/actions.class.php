<?php

/**
 * calendario actions.
 *
 * @package    siglas
 * @subpackage calendario
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class calendarioActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        if ($request->getParameter("usuario_delega_id") != 'undefined') {
            $id = $request->getParameter("usuario_delega_id");
            $usuario = Doctrine::getTable('Acceso_Usuario')->find($id);
            $id = $usuario->getUsuarioEnlaceId();
        } else {
            $id = $this->getUser()->getAttribute('funcionario_id');
            $usuario = Doctrine::getTable('Acceso_Usuario')->find($this->getUser()->getAttribute('usuario_id'));
            $sf_variablesEntorno = sfYaml::load($usuario->getVariablesEntorno());
            $sf_variablesEntorno['ultimos_accesos']['calendario'] = date('Y-m-d H:i:s');
            $sf_variablesEntorno = sfYAML::dump($sf_variablesEntorno);
            $usuario->setVariablesEntorno($sf_variablesEntorno);
            $usuario->save();
        }


        $eventos = Doctrine::getTable('Public_Eventos')->buscarEventos($id);
        $eventosInvitado = Doctrine::getTable('Public_Eventos')->buscarEventosInvitado($id);
        $invitaciones = Doctrine::getTable('Public_Eventos')->buscarInvitaciones($id);

        $session_cargos = $this->getUser()->getAttribute('session_cargos');
        foreach ($session_cargos as $value) {
            $unidadNombre = Doctrine::getTable('Organigrama_Unidad')->findOneById($value['unidad_id']);
            $unidad[$value['unidad_id']] = $unidadNombre->getNombre();
        }
        //Buscar calendario delegado
        $modulos_autorizados = Doctrine::getTable('Acceso_AccionDelegada')->findByUsuarioDelegadoIdAndAccion($this->getUser()->getAttribute('usuario_id'), "gestionar_calendario");
        foreach ($modulos_autorizados as $modulo_autorizado) {

            $usuario = Doctrine::getTable('Acceso_Usuario')->find($modulo_autorizado->getUsuarioDelegaId());
            $funcionarioId = $usuario->getUsuarioEnlaceId();
            $funcionario = Doctrine::getTable('Funcionarios_funcionario')->findOneById($funcionarioId);
            $funcionarioNombre = $funcionario->getPrimerNombre() . " " . $funcionario->getPrimerApellido();
            $autorizados[$funcionarioId] = $funcionarioNombre;
            $idModuloAutorizado = $modulo_autorizado->getId();
        }
        //FIN Buscar calendario delegado
        if (isset($autorizados)) {
            $this->autorizados = $autorizados;
            $this->idModuloAutorizado = $idModuloAutorizado;
        }
        $this->id = $id;
        $this->unidad = $unidad;
        $this->eventos = $eventos;
        $this->eventosInvitado = $eventosInvitado;
        $this->invitaciones = $invitaciones;
    }

    public function executeCreateEvento(sfWebRequest $request) {
        if ($request->getParameter("id") != $this->getUser()->getAttribute('funcionario_id')) {
            $id = $request->getParameter("id");
            $usuario = Doctrine::getTable('Acceso_Usuario')->find($id);
            $id = $usuario->getUsuarioEnlaceId();
        } else {
            $id = $this->getUser()->getAttribute('funcionario_id');
        }
        $conn = Doctrine_Manager::connection();

        try {
            $conn->beginTransaction();
            $cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoFuncionario($request->getParameter('unidad'), $id);

            $evento = new Public_Eventos();
            $evento->setTitulo($request->getParameter('eventName'));
            $evento->setFFinal($request->getParameter('endTime'));
            $evento->setFInicio($request->getParameter('startTime'));
            $evento->setDia($request->getParameter('allDay'));
            $evento->setUnidadId($request->getParameter('unidad'));
            $evento->setCargoId($cargo[0]->getCargoId());
            $evento->setFuncionarioId($id);
            $evento->setInstitucional($request->getParameter('institucional'));
            $evento->setMotivoId(100000);
            $evento->save();

            $eventoId['evento'] = $evento->getId();
            $eventoId = sfYAML::dump($eventoId);

            if (count($request->getParameter('invitados')) > 0) {
                foreach ($request->getParameter('invitados') as $key => $value) {
                    list($cargo_id, $unidad_id) = explode("_", $value);
                    $invitado = new Public_EventosInvitados();
                    $invitado->setEventoId($evento->getId());
                    $invitado->setUnidadInvitadoId($unidad_id);
                    $invitado->setCargoInvitadoId($cargo_id);
                    $invitado->setFuncionarioInvitadoId($key);
                    $invitado->setAprobado(0);
                    $invitado->save();
                }
            }

            if (count($request->getParameter('externo')) > 0) {
                foreach ($request->getParameter('externo') as $key => $value) {
                    $seguridad_preingreso = new Seguridad_Preingreso();
                    $seguridad_preingreso->setUnidadId($request->getParameter('unidad'));
                    $seguridad_preingreso->setFuncionarioId($id);
                    $seguridad_preingreso->setFIngresoPosibleInicio($request->getParameter('startTime'));
                    $seguridad_preingreso->setFIngresoPosibleFinal($request->getParameter('endTime'));
                    $seguridad_preingreso->setMotivoId(100000);
                    $seguridad_preingreso->setMotivoVisita($request->getParameter('eventName'));
                    $seguridad_preingreso->setStatus('P');
                    $seguridad_preingreso->setIndices($eventoId);
                    $seguridad_preingreso->save();

                    // INICIO PROCESAR INGRESO
                    $seguridad_ingreso = new Seguridad_Ingreso();
                    $seguridad_ingreso->setPersonaId($key);
                    $seguridad_ingreso->setPreingresoId($seguridad_preingreso->getId());
                    $seguridad_ingreso->setUnidadId($request->getParameter('unidad'));
                    $seguridad_ingreso->setFuncionarioId($id);
                    $seguridad_ingreso->setImagen('');
                    $seguridad_ingreso->setMotivoId(100000);
                    $seguridad_ingreso->setMotivoVisita($request->getParameter('eventName'));
                    $seguridad_ingreso->setLlaveIngresoId(NULL);
                    $seguridad_ingreso->setFIngreso('1900-01-01');
                    $seguridad_ingreso->setStatus('P');
                    $seguridad_ingreso->save();
                    // FIN PROCESAR INGRESO
                }
            }
            $conn->commit();
            
            $array_datos['id'] = $evento->getId();
            $array_datos['title'] = $evento->getTitulo();
            $array_datos['inicio']['Y'] = date("Y", strtotime($evento->getFInicio()));
            $array_datos['inicio']['m'] = date("m", strtotime($evento->getFInicio()));
            $array_datos['inicio']['d'] = date("d", strtotime($evento->getFInicio()));
            $array_datos['inicio']['H'] = date("H", strtotime($evento->getFInicio()));
            $array_datos['inicio']['i'] = date("i", strtotime($evento->getFInicio()));
            $array_datos['final']['Y'] = date("Y", strtotime($evento->getFFinal()));
            $array_datos['final']['m'] = date("m", strtotime($evento->getFFinal()));
            $array_datos['final']['d'] = date("d", strtotime($evento->getFFinal()));
            $array_datos['final']['H'] = date("H", strtotime($evento->getFFinal()));
            $array_datos['final']['i'] = date("i", strtotime($evento->getFFinal()));
            $array_datos['dia'] = $evento->getDia();

            $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
            sleep(1);
            return $this->renderText(json_encode($array_datos));
            
        } catch (Doctrine_Validator_Exception $e) {
            $conn->rollBack();
            $errorStack = $form->getObject()->getErrorStack();

            $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
            foreach ($errorStack as $field => $errors) {
                $message .= "$field (" . implode(", ", $errors) . "), ";
            }
            $message = trim($message, ', ');

            $this->getUser()->setFlash('error', $message);
            return sfView::SUCCESS;
        }
//    exit();
    }

    public function executeUpdateEvento(sfWebRequest $request) {
        if ($request->getParameter("id") != $this->getUser()->getAttribute('funcionario_id')) {
            $id = $request->getParameter("id");
            $usuario = Doctrine::getTable('Acceso_Usuario')->find($id);
            $id = $usuario->getUsuarioEnlaceId();
        } else {
            $id = $this->getUser()->getAttribute('funcionario_id');
        }

        $conn = Doctrine_Manager::connection();

        try {
            $conn->beginTransaction();

            $evento = Doctrine::getTable('Public_Eventos')->findOneById($request->getParameter('id'));

            $evento->setTitulo($request->getParameter('eventName'));
            if ($evento->getFInicio() != $request->getParameter('startTime')) {
                $evento->setFInicio($request->getParameter('startTime'));
            }
            if ($evento->getFFinal() != $request->getParameter('endTime')) {
                $evento->setFFinal($request->getParameter('endTime'));
            }
            $evento->setDia($request->getParameter('allDay'));
            $evento->setUnidadId($request->getParameter('unidad'));
            $evento->setFuncionarioId($id);
            $evento->setMotivoId(100000);
            $evento->save();
            $eventoId['evento'] = $evento->getId();
            $eventoId = sfYAML::dump($eventoId);

            if (count($request->getParameter('invitados')) > 0) {
                foreach ($request->getParameter('invitados') as $key => $value) {
                    if ($value == 1) {
                        $invitado = new Public_EventosInvitados();
                        $invitado->setEventoId($evento->getId());
                        $invitado->setFuncionarioInvitadoId($key);
                        $invitado->setAprobado(0);
                        $invitado->save();
                    }
                }
            }
            $conn->commit();
        } catch (Doctrine_Validator_Exception $e) {
            $conn->rollBack();
            $errorStack = $form->getObject()->getErrorStack();

            $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
            foreach ($errorStack as $field => $errors) {
                $message .= "$field (" . implode(", ", $errors) . "), ";
            }
            $message = trim($message, ', ');

            $this->getUser()->setFlash('error', $message);
            return sfView::SUCCESS;
        }

        if (count($request->getParameter('externo')) > 0) {
            try {
                $conn = Doctrine_Manager::connection();
                $conn->beginTransaction();
                foreach ($request->getParameter('externo') as $key => $value) {
                    $seguridad_preingreso = new Seguridad_Preingreso();
                    $seguridad_preingreso->setUnidadId($request->getParameter('unidad'));
                    $seguridad_preingreso->setFuncionarioId($id);
                    $seguridad_preingreso->setFIngresoPosibleInicio($request->getParameter('startTime'));
                    $seguridad_preingreso->setFIngresoPosibleFinal($request->getParameter('endTime'));
                    $seguridad_preingreso->setMotivoId(100000);
                    $seguridad_preingreso->setMotivoVisita($request->getParameter('eventName'));
                    $seguridad_preingreso->setStatus('P');
                    $seguridad_preingreso->setIndices($eventoId);
                    $seguridad_preingreso->save();

                    // INICIO PROCESAR INGRESO
                    $seguridad_ingreso = new Seguridad_Ingreso();
                    $seguridad_ingreso->setPersonaId($key);
                    $seguridad_ingreso->setPreingresoId($seguridad_preingreso->getId());
                    $seguridad_ingreso->setUnidadId($request->getParameter('unidad'));
                    $seguridad_ingreso->setFuncionarioId($id);
                    $seguridad_ingreso->setImagen('');
                    $seguridad_ingreso->setMotivoId(100000);
                    $seguridad_ingreso->setMotivoVisita($request->getParameter('eventName'));
                    $seguridad_ingreso->setLlaveIngresoId(NULL);
                    $seguridad_ingreso->setFIngreso('1900-01-01');
                    $seguridad_ingreso->setStatus('P');
                    $seguridad_ingreso->save();
                    // FIN PROCESAR INGRESO
                }

                $conn->commit();
            } catch (Doctrine_Validator_Exception $e) {
                $conn->rollBack();
                $errorStack = $form->getObject()->getErrorStack();

                $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
                foreach ($errorStack as $field => $errors) {
                    $message .= "$field (" . implode(", ", $errors) . "), ";
                }
                $message = trim($message, ', ');

                $this->getUser()->setFlash('error', $message);
                return sfView::SUCCESS;
            }
        }
        exit();
    }

    public function executeUpdateEventoDrag(sfWebRequest $request) {
        $id = $request->getParameter("id");
        $dias = $request->getParameter("dias");
        $minutos = $request->getParameter("minutos");
        $evento = Doctrine::getTable('Public_Eventos')->findOneById($id);

        if ($dias > 0) {
            echo $inicioNuevo = date("Y-m-d h:m:s", strtotime($evento->getFInicio() . ' + ' . $dias . ' day'));
            echo "<br/>";
            echo $finalNuevo = date("Y-m-d h:m:s", strtotime($evento->getFFinal() . ' + ' . $dias . ' day'));
        } else {
            echo $inicioNuevo = date("Y-m-d h:m:s", strtotime($evento->getFInicio() . ' ' . $dias . ' day'));
            echo "<br/>";
            echo $finalNuevo = date("Y-m-d h:m:s", strtotime($evento->getFFinal() . ' ' . $dias . ' day'));
        }

        $evento->setFInicio($inicioNuevo);
        $evento->setFFinal($finalNuevo);
        $evento->save();
        exit();
    }

    public function executeEventoInvitado(sfWebRequest $request) {
        $decision = $request->getParameter("decision");
        $evento = $request->getParameter("evento");
        $invitacion = $request->getParameter("invitacion");
        $externas = $request->getParameter("externas");
        $eventoInvitacion = Doctrine::getTable('Public_EventosInvitados')->findOneById($invitacion);
        $eventoInvitacion->setAprobado($decision);
        $eventoInvitacion->save();

        exit();
    }

    public function executeInterno() {
        $this->unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad();
    }

    public function executeExterno() {
        
    }

    public function executeBuscarInvitadosInternos(sfWebRequest $request) {
        $cadena = '';
        $eventos = Doctrine::getTable("Public_EventosInvitados")->findByEventoId($request->getParameter("id"));
        echo $request->getParameter("id");
        foreach ($eventos as $evento) {
            if ($evento->getFuncionarioInvitadoId() != $this->getUser()->getAttribute('funcionario_id')) {
                $funcionario = Doctrine::getTable("Funcionarios_Funcionario")->findOneById($evento->getFuncionarioInvitadoId());

                if ($evento->getAprobado() == -1) {
                    $aprobado = '<span style="color: #E80000;">Rechazado</span>';
                }
                if ($evento->getAprobado() == 0) {
                    $aprobado = 'No aprobado aún';
                }
                if ($evento->getAprobado() == 1) {
                    $aprobado = '<span style="color: #27AE60;">Aprobado</span>';
                }
                if ($evento->getAprobado() == 2) {
                    $aprobado = '<span style="color: #E80000;">Ya no asistirá</span>';
                }

                $cadena .= '<span id="' . $evento->getFuncionarioInvitadoId() . '" style="font-weight: bold;">&emsp;&emsp;&bull;' . $funcionario->getPrimerNombre() . ' ' . $funcionario->getPrimerApellido() . ' (' . $aprobado . ') <a href="#" style="cursor: pointer; text-decoration: none; float: right;" onclick="eliminarInvitacion(' . $request->getParameter("id") . ',' . $evento->getFuncionarioInvitadoId() . ')" title="Eliminar Invitacion">&nbsp;&nbsp;&nbsp;&nbsp;</a></span><br/>';
            }
        }
        echo $cadena;
        exit();
    }

    public function executeBuscarInvitadosExternos(sfWebRequest $request) {
        $cadena = '';
        $invitados = Doctrine::getTable('Seguridad_Preingreso')->findByMotivoId(100000);
        foreach ($invitados as $invitado) {
            $sf_evento = sfYaml::load($invitado->getIndices());
            if ($sf_evento['evento'] == $request->getParameter("id")) {
                $datosPersonas = Doctrine_Query::create()
                        ->select('i.id, p.primer_nombre, p.primer_apellido')
                        ->from('Seguridad_Persona p')
                        ->innerjoin('p.Seguridad_Ingreso i')
                        ->where('i.preingreso_id = ?', $invitado->getId())
                        ->execute();
                foreach ($datosPersonas as $datoPersona) {
                    $cadena .= '<span style="font-weight: bold;">&emsp;&emsp;&bull;' . $datoPersona->getPrimerNombre() . ' ' . $datoPersona->getPrimerApellido() . '</span><br/>';
                }
            }
        }

        echo $cadena;
        exit();
    }

    public function executeEliminarInvitacion(sfWebRequest $request) {
        $invitacion = Doctrine::getTable("Public_EventosInvitados")->findByEventoIdAndFuncionarioInvitadoId($request->getParameter("evento"), $request->getParameter("funcionario"));
        $invitacion->delete();
        exit();
    }

    public function executeVerificar(sfWebRequest $request) {
        $id = $request->getParameter("evento");
        $eventos = Doctrine::getTable("Public_Eventos")->findById($id);
        foreach ($eventos as $evento) {
            if ($evento->getFuncionarioId() != $this->getUser()->getAttribute('funcionario_id')) {
                echo "false";
            }
        }
        exit();
    }

    public function executeNoAsistira(sfWebRequest $request) {
        $invitaciones = Doctrine::getTable("Public_EventosInvitados")->findByEventoIdAndFuncionarioInvitadoId($request->getParameter("evento"), $request->getParameter("funcionario"));
        foreach ($invitaciones as $invitacion) {
            $invitacion->setAprobado(2);
            $invitacion->save();
        }
        exit();
    }

}

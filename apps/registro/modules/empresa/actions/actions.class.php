<?php

require_once dirname(__FILE__).'/../lib/empresaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/empresaGeneratorHelper.class.php';

/**
 * empresa actions.
 *
 * @package    siglas
 * @subpackage empresa
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class empresaActions extends autoEmpresaActions
{
    public function executeIndex(sfWebRequest $request)
    {
      $this->getUser()->getAttributeHolder()->remove('pae_registro_id');
        
      // sorting
      if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort')))
      {
        $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
      }

      // pager
      if ($request->getParameter('page'))
      {
        $this->setPage($request->getParameter('page'));
      }

      $this->pager = $this->getPager();
      $this->sort = $this->getSort();
    }
    
    public function executeAsistente(sfWebRequest $request)
    {
        $paso= $request->getParameter('paso');
        $regid= ($request->getParameter('regid')) ? $request->getParameter('regid') : 'none';
        
        $this->paso= $paso;
        $this->regid= $regid;
    }
    
    public function executeChangeRecaudo(sfWebRequest $request)
    {
        $id= $request->getParameter('id');
        $status= $request->getParameter('status');
        
        if($request->getParameter('status') == 'true') {
            $status= 'C';
        }else {
            $status= 'P';
        }

        $recaudo= Doctrine::getTable('Operaciones_RegistroEmpresaRequisito')->find($id);

        if($recaudo) {
            if($status != $recaudo->getStatus()) {
                $recaudo->setStatus($status);
                $recaudo->save();
            }
            
            echo $recaudo->getStatus();
        }else {
            echo 'error';
        }
        exit();
    }
    
    public function executeEncargados(sfWebRequest $request)
    {
      $registro_id = $request->getParameter('id');

      $this->getUser()->setAttribute('pae_registro_id', $registro_id);

      $this->redirect('encargado/index');
    }
    
    public function executeEquipos(sfWebRequest $request)
    {
      $registro_id = $request->getParameter('id');

      $this->getUser()->setAttribute('pae_registro_id', $registro_id);

      $this->redirect('disciplina/index?id='.$registro_id);
    }
    
    public function executeDelegados(sfWebRequest $request)
    {
      $registro_id = $request->getParameter('id');

      $this->getUser()->setAttribute('pae_registro_id', $registro_id);

      $this->redirect('delegado/index');
    }
    
    public function executeDisciplinas(sfWebRequest $request)
    {
      $registro_id = $request->getParameter('id');

      $this->redirect('disciplina/gestionarDisciplina?id='.$registro_id);
    }
    
    public function executeEncargadoAdd(sfWebRequest $request)
    {
//        $this->redirect('encargado/asistenteEncargado?regid='.$operaciones_registro->getId());
        $this->redirect('encargado/asistenteEncargado?regid=1');
    }
    
    public function executeAsistenteEmpresa(sfWebRequest $request)
    {
        $this->form = $this->configuration->getForm();
        $this->empresas_empresa = $this->form->getObject();
    }
    
    public function executeSavePago(sfWebRequest $request)
    {
        $banco = $request->getParameter('banco');
        $referencia= $request->getParameter('referencia');
        $tipo_pago = $request->getParameter('tipo_pago');
        $monto = str_replace(',', '.', $request->getParameter('monto'));
        $regid= $request->getParameter('regid');

        $registro_pago= new Operaciones_RegistroPago();
        
        $registro_pago->setRegistroId($regid);
        $registro_pago->setBanco($banco);
        $registro_pago->setReferencia($referencia);
        $registro_pago->setTipoPagoId($tipo_pago);
        $registro_pago->setMonto($monto);
        
        $registro_pago->save();
        
        $this->getUser()->setFlash('notice', 'El pago ha sido registrado con exito.');
        exit();
    }
    
    public function executeSaveEmpresa(sfWebRequest $request)
    {
        $form_datos= $request->getParameter('empresas_empresa');
//        echo '<pre>';
//        print_r($form_datos); exit;
        //LIMPIADO DE DATOS
        $form_datos['rif']= trim($form_datos['rif']);
        $form_datos['nombre']= trim(ucfirst($form_datos['nombre']));
        $form_datos['siglas']= trim(strtoupper($form_datos['siglas']));
        $form_datos['empresa_tipo_id']= trim($form_datos['empresa_tipo_id']);
        $form_datos['dir_av_calle_esq']= trim(ucfirst($form_datos['dir_av_calle_esq']));
        $form_datos['dir_edf_torre_anexo']= trim(ucfirst($form_datos['dir_edf_torre_anexo']));
        $form_datos['dir_piso']= trim(ucfirst($form_datos['dir_piso']));
        $form_datos['dir_urbanizacion']= trim(ucfirst($form_datos['dir_urbanizacion']));
        $form_datos['dir_ciudad']= trim(ucfirst($form_datos['dir_ciudad']));
        $form_datos['telf_uno']= trim($form_datos['telf_uno']);
        $form_datos['telf_dos']= trim($form_datos['telf_dos']);
        $form_datos['email_principal']= trim(strtolower($form_datos['email_principal']));
        
        $procede= FALSE;
        if($form_datos['rif'] != '' && $form_datos['nombre'] != '' && $form_datos['empresa_tipo_id'] != '') {
            $procede= TRUE;
        }

        //SI EXISTE ESTE PARAMETRO ES PORQUE LA EMPRESA EXISTE
        $new= TRUE;
        if($form_datos['id'] != '') {
            $empresas_empresa= Doctrine::getTable('Empresas_Empresa')->find($form_datos['id']);
            $new= FALSE;
        }else {
            $empresas_empresa= new Empresas_Empresa();
        }

        $conn = Doctrine_Manager::connection();
        if($procede) {
            try {
                $conn->beginTransaction();
                
                if($new) {
                    $empresas_empresa->setNombre($form_datos['nombre']);
                    $empresas_empresa->setRif($form_datos['rif']);
                    $empresas_empresa->setSiglas($form_datos['siglas']);
                    $empresas_empresa->setEmpresaTipoId($form_datos['empresa_tipo_id']);
                    if($form_datos['estado_id'] != '') {
                        $empresas_empresa->setEstadoId($form_datos['estado_id']);
                    }
                    if($form_datos['municipio_id'] != '') {
                        $empresas_empresa->setMunicipioId($form_datos['municipio_id']);
                    }
                    if($form_datos['parroquia_id'] != '') {
                        $empresas_empresa->setParroquiaId($form_datos['parroquia_id']);
                    }
                    $empresas_empresa->setDirAvCalleEsq($form_datos['dir_av_calle_esq']);
                    $empresas_empresa->setDirEdfTorreAnexo($form_datos['dir_edf_torre_anexo']);
                    $empresas_empresa->setDirPiso($form_datos['dir_piso']);
                    $empresas_empresa->setDirUrbanizacion($form_datos['dir_urbanizacino']);
                    $empresas_empresa->setDirCiudad($form_datos['dir_ciudad']);
                    $empresas_empresa->setTelfUno($form_datos['telf_uno']);
                    $empresas_empresa->setTelfDos($form_datos['telf_dos']);
                }else {
                    //SI HAY ALGUN CAMBIO EN LOS SIGUIENTES CAMPOS ES QUE ACTUALIZA
                    if($form_datos['siglas'] != $empresas_empresa->getSiglas()) { $empresas_empresa->setSiglas($form_datos['siglas']); }
                    if($form_datos['estado_id'] != $empresas_empresa->getEstadoId() && $form_datos['estado_id'] != '') { $empresas_empresa->setEstadoId($form_datos['estado_id']); }
                    if($form_datos['municipio_id'] != $empresas_empresa->getMunicipioId() && $form_datos['municipio_id'] != '') { $empresas_empresa->setMunicipioId($form_datos['municipio_id']); }
                    if($form_datos['parroquia_id'] != $empresas_empresa->getParroquiaId() && $form_datos['parroquia_id'] != '') { $empresas_empresa->setParroquiaId($form_datos['parroquia_id']); }
                    if($form_datos['dir_av_calle_esq'] != $empresas_empresa->getDirAvCalleEsq()) { $empresas_empresa->setDirAvCalleEsq($form_datos['dir_av_calle_esq']); }
                    if($form_datos['dir_edf_torre_anexo'] != $empresas_empresa->getDirEdfTorreAnexo()) { $empresas_empresa->setDirEdfTorreAnexo($form_datos['dir_edf_torre_anexo']); }
                    if($form_datos['dir_piso'] != $empresas_empresa->getDirPiso()) { $empresas_empresa->setDirPiso($form_datos['dir_piso']); }
                    if($form_datos['dir_urbanizacion'] != $empresas_empresa->getDirUrbanizacion()) { $empresas_empresa->setDirUrbanizacion($form_datos['dir_urbanizacion']); }
                    if($form_datos['dir_ciudad'] != $empresas_empresa->getDirCiudad()) { $empresas_empresa->setDirCiudad($form_datos['dir_ciudad']); }
                    if($form_datos['telf_uno'] != $empresas_empresa->getTelfUno()) { $empresas_empresa->setTelfUno($form_datos['telf_uno']); }
                    if($form_datos['telf_dos'] != $empresas_empresa->getTelfDos()) { $empresas_empresa->setTelfDos($form_datos['telf_dos']); }
                }

                $empresas_empresa->save();
                
                //BUSCA LA LIGA ACTUAL
                $liga= Doctrine::getTable('Operaciones_Liga')->ligaActual();
                $liga_actual= '';
                foreach($liga as $value) {
                    $liga_actual = $value->getLiga();
                }
                
                //CREACION DE REGISTRO O INSCRIPCION
                $operaciones_registro= new Operaciones_Registro();
                
                $operaciones_registro->setEmpresaId($empresas_empresa->getId());
                $operaciones_registro->setLigaId($liga_actual);
                $operaciones_registro->setStatus('R');
                
                $operaciones_registro->save();
                
                //CREACION DE REQUISITOS ACTIVOS PARA INSCRIPCION DE EMPRESAS
                $requisitos= Doctrine::getTable('Operaciones_Requisito')->findByStatusAndTipo('A', 'E');
                
                foreach($requisitos as $requisito) {
                    $operaciones_empresa_requisito= new Operaciones_RegistroEmpresaRequisito();
                
                    $operaciones_empresa_requisito->setRegistroId($operaciones_registro->getId());
                    $operaciones_empresa_requisito->setRequisitoId($requisito->getId());
                    $operaciones_empresa_requisito->setStatus('P');

                    $operaciones_empresa_requisito->save();
                }
                
                $conn->commit();
                
                $this->getUser()->setFlash('notice', 'La empresa ha si inscrita con exito. Ahora puede agregar encargados de actividades deportivas de esta empresa.');
//                $this->registro_id= $operaciones_registro->getId();
                //SUSTITUIR IDS VERDADEROS
                $this->redirect('encargado/asistenteEncargado?regid='.$operaciones_registro->getId());
            } catch (Doctrine_Validator_Exception $e) {
                $conn->rollBack();
                //MANIPULAR ERROR
                $this->getUser()->setFlash('error', 'No pudimos registrar los datos, por favor si la situacion persiste notifica al departamente de tecnología.');
                $this->redirect('empresa/asistente?paso=1');
            }
        }else {
            //MANIPULAR ERROR
            $this->getUser()->setFlash('error', 'No pudimos registrar los datos, por favor si la situacion persiste notifica al departamente de tecnología.');
            $this->redirect('empresa/asistente?paso=1');
        }
    }
    
    public function executeBuscarEmpresa(sfWebRequest $request) {
        $rif1= trim($request->getParameter('rif1'));
        $rif2= trim($request->getParameter('rif2'));
        $rif3= trim($request->getParameter('rif3'));
        $rif= $rif1.'-'.$rif2.'-'.$rif3;

        $empresa= Doctrine::getTable('Empresas_Empresa')->findOneByRif($rif);

        $empresa_ar= array();
        if(count($empresa) > 1) {
            //BUSCA LA LIGA ACTUAL
            $liga= Doctrine::getTable('Operaciones_Liga')->ligaActual();
            $liga_actual= '';
            foreach($liga as $value) {
                $liga_actual = $value->getLiga();
            }
            
            $existente= Doctrine::getTable('Operaciones_Registro')->findOneByEmpresaIdAndLigaId($empresa->getId(), $liga_actual);

            if(count($existente) <= 1) {
                //SIGNIFICA QUE EXISTE LA EMPRESA PERO NO ESTA INSCRITA
                $empresa_ar['status']= 'ok';
                $empresa_ar['content']['id']= $empresa->getId();
                $empresa_ar['content']['nombre']= $empresa->getNombre();
                $empresa_ar['content']['siglas']= $empresa->getSiglas();
                $empresa_ar['content']['empresaTipoId']= $empresa->getEmpresaTipoId();
                $empresa_ar['content']['estadoId']= $empresa->getEstadoId();
                $empresa_ar['content']['municipioId']= $empresa->getMunicipioId();
                $empresa_ar['content']['parroquiaId']= $empresa->getParroquiaId();
                $empresa_ar['content']['dirAvCalleEsq']= $empresa->getDirAvCalleEsq();
                $empresa_ar['content']['dirEdfTorreAnexo']= $empresa->getDirEdfTorreAnexo();
                $empresa_ar['content']['dirPiso']= $empresa->getDirPiso();
                $empresa_ar['content']['dirUrbanizacion']= $empresa->getDirUrbanizacion();
                $empresa_ar['content']['dirCiudad']= $empresa->getDirCiudad();
                $empresa_ar['content']['telfUno']= $empresa->getTelfUno();
                $empresa_ar['content']['telfDos']= $empresa->getTelfDos();
                $empresa_ar['content']['emailPrincipal']= $empresa->getEmailPrincipal();
            }else {
                //LA EMPRESA EXISTE Y ADEMAS YA ESTA INSCRITA
                $empresa_ar['status']= 'existe';
            }
        }else {
            $empresa_ar['status']= 'empty';
        }
        
        return $this->renderText(json_encode($empresa_ar));
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
    
    public function executePlanillaInscripcionTodas(sfWebRequest $request) {

        $pdf = new ConPie(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(60, 115, 80);
        $pdf->SetHeaderData('gob_pdf.png', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf->setAutoPageBreak(True, 90);

        $pdf->AliasNbPages();
        
        $tbl= '';
        
//        $registros= Doctrine::getTable('Operaciones_Registro')->findAll();
        $registros= Doctrine::getTable('Operaciones_Registro')->todosLimitado();
        
        foreach($registros as $registro) {
            $empresa= Doctrine::getTable('Empresas_Empresa')->find($registro->getEmpresaId());
            $encargados= Doctrine::getTable('Operaciones_RegistroEncargado')->findByRegistroIdAndStatus($registro->getId(), 'A');
            $delegados= Doctrine::getTable('Operaciones_RegistroDelegado')->findByRegistroIdAndStatus($registro->getId(), 'A');
            $disciplinas= Doctrine::getTable('Operaciones_RegistroDisciplina')->findByRegistroIdAndStatus($registro->getId(), 'A');

            //ORDENAR INFORMACION ESTADO
            $direccion= '';
            if($empresa->getEstadoId() != '' && $empresa->getEstadoId() != NULL) {
                $estado_ar= Doctrine::getTable('Public_Estado')->find($empresa->getEstadoId());
                $direccion.= $estado_ar->getNombre().', ';
            }
            //ORDENAR INFORMACION MUNICIPIO
            $municipio= '';
            if($empresa->getMunicipioId() != '' && $empresa->getMunicipioId() != NULL) {
                $municipio_ar= Doctrine::getTable('Public_Municipio')->find($empresa->getMunicipioId());
                $direccion.= $municipio_ar->getNombre().', ';
            }
            //ORDENAR INFORMACION PARROQUIA
            $parroquia= '';
            if($empresa->getParroquiaId() != '' && $empresa->getParroquiaId() != NULL) {
                $parroquia_ar= Doctrine::getTable('Public_Parroquia')->find($empresa->getParroquiaId());
                $direccion.= $parroquia_ar->getNombre().', ';
            }

            $direccion.= ($empresa->getDirAvCalleEsq() !== '' && $empresa->getDirAvCalleEsq() != NULL)? $empresa->getDirAvCalleEsq().', ' : '';
            $direccion.= ($empresa->getDirEdfTorreAnexo() !== '' && $empresa->getDirEdfTorreAnexo() != NULL)? $empresa->getDirEdfTorreAnexo().', ' : '';
            $direccion.= ($empresa->getDirPiso() !== '' && $empresa->getDirPiso() != NULL)? $empresa->getDirPiso().', ' : '';
            $direccion.= ($empresa->getDirUrbanizacion() !== '' && $empresa->getDirUrbanizacion() != NULL)? $empresa->getDirUrbanizacion().', ' : '';
            $direccion.= ($empresa->getDirCiudad() !== '' && $empresa->getDirCiudad() != NULL)? $empresa->getDirCiudad().', ' : '';

            if($direccion != '') {
                $direccion = substr($direccion, 0, -2);
            }

            //ORDENAR TELEFONOS
            $telefonos= '';
            $telefonos.= ($empresa->getTelfUno() !== '' && $empresa->getTelfUno() != NULL)? $empresa->getTelfUno() : '';
            $telefonos.= ($empresa->getTelfDos() !== '' && $empresa->getTelfDos() != NULL)? '<br/>'.$empresa->getTelfDos() : '';

            $delegados_info= '';
            //DELEGADOS DE PREVENCION
            foreach($delegados as $delegado) {
                $persona= Doctrine::getTable('Personas_Persona')->find($delegado->getPersonaId());

                $delegados_info.= '<font style="color: #666">Nombre: </font>'.$persona->getPrimerNombre().' '.$persona->getPrimerApellido();
                $delegados_info.= ($persona->getTelfMovil() != '' && $persona->getTelfMovil() != NULL)? '<br/><font style="color: #666">Telf.: </font>'.$persona->getTelfMovil() : '';
                $delegados_info.= ($persona->getEmailPersonal() != '' && $persona->getEmailPersonal() != NULL)? '<br/><font style="color: #666">Email: </font>'.$persona->getEmailPersonal() : '';
                $delegados_info.= '<hr/>';
            }

            $delegados_base= '';
            if(count($delegados) > 0) {
                $delegados_base= '<tr><td colspan="2"></td></tr><tr>
                        <td style="width: 30%">
                            Delegados de prevención:
                        </td>
                        <td style="width: 35%">
                            '. $delegados_info .'
                        </td>
                        <td  style="width: 35%"></td>
                    </tr>';
            }

            //DISCIPLINAS
            $table_disciplinas= '';
            $disciplinas_ar= array();
            foreach($disciplinas as $value) {
                $disciplinas_ar[] = $value->getDisciplinaVarianteId();
            }
            $disciplinas_inscritas= Doctrine::getTable('Operaciones_RegistroDisciplina')->disciplinasInscritas($disciplinas_ar);

            $table_disciplinas.= '<table border="1" cellpadding="5"><tr><td colspan="3" style="background-color: #666"><font style="color: white">DISCIPLINAS INSCRITAS EN LA LIGA INCRET DEPORTE LABORAL</font></td></tr>';
            foreach($disciplinas_inscritas as $val) {
                $table_disciplinas.= '<tr><td colspan="1" style="width: 25%">';
                $disciplina_data= Doctrine::getTable('Operaciones_Disciplina')->find($val->getDisciplinas());
                $table_disciplinas.= $disciplina_data->getNombre();
                $table_disciplinas.= '</td><td colspan="2" style="width: 75%">';

                $variante_data= Doctrine::getTable('Operaciones_DisciplinaVariante')->variantesPorDisciplina($val->getDisciplinas());
                foreach($variante_data as $variante) {
                    $checked= FALSE;
                    if(in_array($variante->getDvi(), $disciplinas_ar)) {
                        $checked= TRUE;
                    }
                    $table_disciplinas.= '<img width="10" src="/images/icon/'. (($checked) ? 'check_lleno.png' : 'check_vacio.png') .'"/>&nbsp;'.$variante->getNombre();
                }

                $table_disciplinas.= '</td></tr>';
            }
            $table_disciplinas.= '</table>';

            $encargados_info= '';
            //ENCARGADOS DE PREVENCION
            foreach($encargados as $encargado) {
                $persona= Doctrine::getTable('Personas_Persona')->find($encargado->getPersonaId());

                $encargados_info.= '<font style="color: #666">Nombre: </font>'.$persona->getPrimerNombre().' '.$persona->getPrimerApellido();
                $encargados_info.= ($encargado->getCargo() != '' && $encargado->getCargo() != NULL)? '<br/><font style="color: #666">Cargo: </font>'.$encargado->getCargo() : '';
                $encargados_info.= ($persona->getTelfMovil() != '' && $persona->getTelfMovil() != NULL)? '<br/><font style="color: #666">Telf.: </font>'.$persona->getTelfMovil() : '';
                $encargados_info.= ($persona->getEmailPersonal() != '' && $persona->getEmailPersonal() != NULL)? '<br/><font style="color: #666">Email: </font>'.$persona->getEmailPersonal() : '';
                $encargados_info.= '<hr/>';
            }

            $encargados_base= '';
            if(count($encargados) > 0) {
                $encargados_base= '<tr><td colspan="2"></td></tr><tr>
                        <td style="width: 30%">
                            Encargados de actividades deportivas:
                        </td>
                        <td style="width: 35%">
                            '. $encargados_info .'
                        </td>
                        <td  style="width: 35%"></td>
                    </tr>';
            }


            $suscriptor_info= '';
            //SUSCRIPTOR

            $persona= Doctrine::getTable('Personas_Persona')->find($this->getUser()->getAttribute('persona_id'));

            $suscriptor_info.= '<font style="color: #666">Nombre: </font>'.$persona->getPrimerNombre().' '.$persona->getPrimerApellido();
            $suscriptor_info.= ($persona->getTelfMovil() != '' && $persona->getTelfMovil() != NULL)? '<br/><font style="color: #666">Telf.: </font>'.$persona->getTelfMovil() : '';
            $suscriptor_info.= ($persona->getEmailPersonal() != '' && $persona->getEmailPersonal() != NULL)? '<br/><font style="color: #666">Email: </font>'.$persona->getEmailPersonal() : '';

            $suscriptor_base= '';
            $suscriptor_base= '<tr><td colspan="2"></td></tr><tr>
                    <td style="width: 30%">
                        Quien suscribe esta planilla:
                    </td>
                    <td style="width: 35%">
                        '. $suscriptor_info .'
                    </td>
                    <td  style="width: 35%"></td>
                </tr>';

            //CALCULO DE PAGO
            $dic_pagas= array(); //DISCIPLINAS PAGADAS EN CASO DE PAGO UNICO POR DISCIPLINA
            $dic_to_count= 0; //CANTIDAD DE DISCIPLINAS FINAL PARA CONTABILIZAR MONTO
            foreach($disciplinas as $value) {
                $modo= Doctrine::getTable('Operaciones_Disciplina')->modoCobro($value->getDisciplinaVarianteId());

                if($modo[0][1] == 'U') {
                    if(!in_array($modo[0][2], $dic_pagas)) {
                        $dic_to_count++;
                        $dic_pagas[]= $modo[0][2];
                    }
                }elseif($modo[0][1] == 'T') {
                    $dic_to_count++;
                }
            }

            $pago= number_format(($dic_to_count * 3000), 2, ',', '.');
            
            
            $tbl .= '<table width="470" "center">
    <tr>
        <td width="470">
            <table width="470" "center">
                  <tr>
                    <td colspan="3" align="center">
                        <h3>PLANILLA DE INSCRIPCIÓN DE ENTIDAD DE TRABAJO EN LIGA INCRET</h3>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" align="center"><br/></td>
                  </tr>';
        
        $tbl .= '<tr>
                    <td style="width: 25%">
                        Fecha:
                    </td>
                    <td colspan="2" style="width: 75%">
                        '. date('d-m-Y', strtotime($registro->getCreatedAt())) .'
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%">
                        Entidad de trabajo:
                    </td>
                    <td colspan="2" style="width: 75%">
                        <font style="font-size: 12; font-weight: bold">'. $empresa->getNombre(). '</font> <font style="color: #666">' . $empresa->getRif() .'</font>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%">
                        Dirección:
                    </td>
                    <td colspan="2" style="width: 75%">
                        '. $direccion .'
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%">
                        Teléfonos:
                    </td>
                    <td colspan="2" style="width: 75%">
                        '. $telefonos .'
                    </td>
                </tr>';
        
        $tbl .= $delegados_base;
        
        $tbl .= '<tr>
                    <td colspan="3">
                        '. $table_disciplinas .'
                    </td>
                </tr>';
        $tbl .= '<tr><td colspan="3"></td></tr>
                <tr>
                    <td colspan="3">
                        Monto calculado a pagar en inscripción ('. (count($disciplinas)- 1) .' equipos): <font style="font-weight: bold">'. $pago .' Bs.</font>
                    </td>
                </tr>';
        
        $tbl .= $encargados_base;
        
        $tbl .= $suscriptor_base;
        
        $tbl .= '<tr><td colspan="3"></td></tr><tr><td colspan="3"></td></tr><tr><td colspan="3"></td></tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="left"><hr/></td>
                </tr>
                <tr><td></td><td></td><td align="center">FIRMA</td></tr>';
        
        $tbl .= '</table>
        </td>
    </tr>
</table>';
            
            $pdf->AddPage();
            $pdf->writeHTML($tbl, true, false, false, false, '');
            $tbl= '';
        }
        
        $pdf->Output('planillas__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }
    
    public function executePlanillaInscripcion(sfWebRequest $request) {

        $variables_sistema= sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/variablesSistema.yml");
        
        $registro_id= $request->getParameter('id');
        $registro= Doctrine::getTable('Operaciones_Registro')->find($registro_id);

        $empresa= Doctrine::getTable('Empresas_Empresa')->find($registro->getEmpresaId());
        $encargados= Doctrine::getTable('Operaciones_RegistroEncargado')->findByRegistroIdAndStatus($registro_id, 'A');
        $delegados= Doctrine::getTable('Operaciones_RegistroDelegado')->findByRegistroIdAndStatus($registro_id, 'A');
        $disciplinas= Doctrine::getTable('Operaciones_RegistroDisciplina')->findByRegistroIdAndStatus($registro_id, 'A');
        
        //ORDENAR INFORMACION ESTADO
        $direccion= '';
        if($empresa->getEstadoId() != '' && $empresa->getEstadoId() != NULL) {
            $estado_ar= Doctrine::getTable('Public_Estado')->find($empresa->getEstadoId());
            $direccion.= $estado_ar->getNombre().', ';
        }
        //ORDENAR INFORMACION MUNICIPIO
        $municipio= '';
        if($empresa->getMunicipioId() != '' && $empresa->getMunicipioId() != NULL) {
            $municipio_ar= Doctrine::getTable('Public_Municipio')->find($empresa->getMunicipioId());
            $direccion.= $municipio_ar->getNombre().', ';
        }
        //ORDENAR INFORMACION PARROQUIA
        $parroquia= '';
        if($empresa->getParroquiaId() != '' && $empresa->getParroquiaId() != NULL) {
            $parroquia_ar= Doctrine::getTable('Public_Parroquia')->find($empresa->getParroquiaId());
            $direccion.= $parroquia_ar->getNombre().', ';
        }
        
        $direccion.= ($empresa->getDirAvCalleEsq() !== '' && $empresa->getDirAvCalleEsq() != NULL)? $empresa->getDirAvCalleEsq().', ' : '';
        $direccion.= ($empresa->getDirEdfTorreAnexo() !== '' && $empresa->getDirEdfTorreAnexo() != NULL)? $empresa->getDirEdfTorreAnexo().', ' : '';
        $direccion.= ($empresa->getDirPiso() !== '' && $empresa->getDirPiso() != NULL)? $empresa->getDirPiso().', ' : '';
        $direccion.= ($empresa->getDirUrbanizacion() !== '' && $empresa->getDirUrbanizacion() != NULL)? $empresa->getDirUrbanizacion().', ' : '';
        $direccion.= ($empresa->getDirCiudad() !== '' && $empresa->getDirCiudad() != NULL)? $empresa->getDirCiudad().', ' : '';
        
        if($direccion != '') {
            $direccion = substr($direccion, 0, -2);
        }
        
        //ORDENAR TELEFONOS
        $telefonos= '';
        $telefonos.= ($empresa->getTelfUno() !== '' && $empresa->getTelfUno() != NULL)? $empresa->getTelfUno() : '';
        $telefonos.= ($empresa->getTelfDos() !== '' && $empresa->getTelfDos() != NULL)? '<br/>'.$empresa->getTelfDos() : '';
        
        $delegados_info= '';
        //DELEGADOS DE PREVENCION
        foreach($delegados as $delegado) {
            $persona= Doctrine::getTable('Personas_Persona')->find($delegado->getPersonaId());
            
            $delegados_info.= '<font style="color: #666">Nombre: </font>'.$persona->getPrimerNombre().' '.$persona->getPrimerApellido();
            $delegados_info.= ($persona->getTelfMovil() != '' && $persona->getTelfMovil() != NULL)? '<br/><font style="color: #666">Telf.: </font>'.$persona->getTelfMovil() : '';
            $delegados_info.= ($persona->getEmailPersonal() != '' && $persona->getEmailPersonal() != NULL)? '<br/><font style="color: #666">Email: </font>'.$persona->getEmailPersonal() : '';
            $delegados_info.= '<hr/>';
        }
        
        $delegados_base= '';
        if(count($delegados) > 0) {
            $delegados_base= '<tr><td colspan="2"></td></tr><tr>
                    <td style="width: 30%">
                        Delegados de prevención:
                    </td>
                    <td style="width: 35%">
                        '. $delegados_info .'
                    </td>
                    <td  style="width: 35%"></td>
                </tr>';
        }
        
        //DISCIPLINAS
        $table_disciplinas= '';
        $disciplinas_ar= array();
        foreach($disciplinas as $value) {
            $disciplinas_ar[] = $value->getDisciplinaVarianteId();
        }
        $disciplinas_inscritas= Doctrine::getTable('Operaciones_RegistroDisciplina')->disciplinasInscritas($disciplinas_ar);
        
        $table_disciplinas.= '<table border="1" cellpadding="5"><tr><td colspan="3" style="background-color: #666"><font style="color: white">DISCIPLINAS INSCRITAS EN LA LIGA INCRET DEPORTE LABORAL</font></td></tr>';
        foreach($disciplinas_inscritas as $val) {
            $table_disciplinas.= '<tr><td colspan="1" style="width: 25%">';
            $disciplina_data= Doctrine::getTable('Operaciones_Disciplina')->find($val->getDisciplinas());
            $table_disciplinas.= $disciplina_data->getNombre();
            $table_disciplinas.= '</td><td colspan="2" style="width: 75%">';
            
            $variante_data= Doctrine::getTable('Operaciones_DisciplinaVariante')->variantesPorDisciplina($val->getDisciplinas());
            foreach($variante_data as $variante) {
                $checked= FALSE;
                if(in_array($variante->getDvi(), $disciplinas_ar)) {
                    $checked= TRUE;
                }
                $table_disciplinas.= '<img width="10" src="/images/icon/'. (($checked) ? 'check_lleno.png' : 'check_vacio.png') .'"/>&nbsp;'.$variante->getNombre();
            }
            
            $table_disciplinas.= '</td></tr>';
        }
        $table_disciplinas.= '</table>';
        
        $encargados_info= '';
        //ENCARGADOS DE PREVENCION
        foreach($encargados as $encargado) {
            $persona= Doctrine::getTable('Personas_Persona')->find($encargado->getPersonaId());
            
            $encargados_info.= '<font style="color: #666">Nombre: </font>'.$persona->getPrimerNombre().' '.$persona->getPrimerApellido();
            $encargados_info.= ($encargado->getCargo() != '' && $encargado->getCargo() != NULL)? '<br/><font style="color: #666">Cargo: </font>'.$encargado->getCargo() : '';
            $encargados_info.= ($persona->getTelfMovil() != '' && $persona->getTelfMovil() != NULL)? '<br/><font style="color: #666">Telf.: </font>'.$persona->getTelfMovil() : '';
            $encargados_info.= ($persona->getEmailPersonal() != '' && $persona->getEmailPersonal() != NULL)? '<br/><font style="color: #666">Email: </font>'.$persona->getEmailPersonal() : '';
            $encargados_info.= '<hr/>';
        }
        
        $encargados_base= '';
        if(count($encargados) > 0) {
            $encargados_base= '<tr><td colspan="2"></td></tr><tr>
                    <td style="width: 30%">
                        Encargados de actividades deportivas:
                    </td>
                    <td style="width: 35%">
                        '. $encargados_info .'
                    </td>
                    <td  style="width: 35%"></td>
                </tr>';
        }
        
        
        $suscriptor_info= '';
        //SUSCRIPTOR
        
        $persona= Doctrine::getTable('Personas_Persona')->find($this->getUser()->getAttribute('persona_id'));

        $suscriptor_info.= '<font style="color: #666">Nombre: </font>'.$persona->getPrimerNombre().' '.$persona->getPrimerApellido();
        $suscriptor_info.= ($persona->getTelfMovil() != '' && $persona->getTelfMovil() != NULL)? '<br/><font style="color: #666">Telf.: </font>'.$persona->getTelfMovil() : '';
        $suscriptor_info.= ($persona->getEmailPersonal() != '' && $persona->getEmailPersonal() != NULL)? '<br/><font style="color: #666">Email: </font>'.$persona->getEmailPersonal() : '';
        
        $suscriptor_base= '';
        $suscriptor_base= '<tr><td colspan="2"></td></tr><tr>
                <td style="width: 30%">
                    Quien suscribe esta planilla:
                </td>
                <td style="width: 35%">
                    '. $suscriptor_info .'
                </td>
                <td  style="width: 35%"></td>
            </tr>';
        
        //CALCULO DE PAGO
        $dic_pagas= array(); //DISCIPLINAS PAGADAS EN CASO DE PAGO UNICO POR DISCIPLINA
        $dic_to_count= 0; //CANTIDAD DE DISCIPLINAS FINAL PARA CONTABILIZAR MONTO
        foreach($disciplinas as $value) {
            $modo= Doctrine::getTable('Operaciones_Disciplina')->modoCobro($value->getDisciplinaVarianteId());
            
            if($modo[0][1] == 'U') {
                if(!in_array($modo[0][2], $dic_pagas)) {
                    $dic_to_count++;
                    $dic_pagas[]= $modo[0][2];
                }
            }elseif($modo[0][1] == 'T') {
                $dic_to_count++;
            }
        }
        
        if($variables_sistema['iva_operacion'] == 'restar') {
            $pago= number_format(($dic_to_count * $variables_sistema['costo_disciplina']), 2, ',', '.');
            $pago_cal= $dic_to_count * $variables_sistema['costo_disciplina'];
            
            $iva_cal= $pago_cal * ($variables_sistema['iva'] / 100);
            $iva= number_format($iva_cal, 2, ',', '.');

            $subtotal= number_format(($pago_cal - $iva_cal), 2, ',', '.');
        }else {
            $subtotal= number_format(($dic_to_count * $variables_sistema['costo_disciplina']), 2, ',', '.');
            $subtotal_cal= $dic_to_count * $variables_sistema['costo_disciplina'];
            
            $iva_cal= $subtotal_cal * ($variables_sistema['iva'] / 100);
            $iva= number_format($iva_cal, 2, ',', '.');
            
            $pago= number_format(($subtotal_cal + $iva_cal), 2, ',', '.');
        }
        
        $pdf = new ConPie(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(60, 115, 80);
        $pdf->SetHeaderData('gob_pdf.png', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf->setAutoPageBreak(True, 90);

        $pdf->AliasNbPages();
        $pdf->AddPage();
        

        $tbl = '<table width="470" "center">
    <tr>
        <td width="470">
            <table width="470" "center">
                  <tr>
                    <td colspan="3" align="center">
                        <h3>PLANILLA DE INSCRIPCIÓN DE ENTIDAD DE TRABAJO EN LIGA INCRET</h3>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" align="center"><br/></td>
                  </tr>';
        
        $tbl .= '<tr>
                    <td style="width: 25%">
                        Fecha:
                    </td>
                    <td colspan="2" style="width: 75%">
                        '. date('d-m-Y', strtotime($registro->getCreatedAt())) .'
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%">
                        Entidad de trabajo:
                    </td>
                    <td colspan="2" style="width: 75%">
                        <font style="font-size: 12; font-weight: bold">'. $empresa->getNombre(). '</font> <font style="color: #666">' . $empresa->getRif() .'</font>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%">
                        Dirección:
                    </td>
                    <td colspan="2" style="width: 75%">
                        '. $direccion .'
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%">
                        Teléfonos:
                    </td>
                    <td colspan="2" style="width: 75%">
                        '. $telefonos .'
                    </td>
                </tr>';
        
        $tbl .= $delegados_base;
        
        $tbl .= '<tr>
                    <td colspan="3">
                        '. $table_disciplinas .'
                    </td>
                </tr>';
        $tbl .= $encargados_base;
        
        $tbl .= $suscriptor_base;
        
        $tbl .= '<tr><td colspan="3"></td></tr>
                <tr>
                    <td colspan="3">
                        Monto calculado a pagar en inscripción ('. (count($disciplinas)- 1) .' equipos):<br/>
                            <table style="text-align: right; width: 150px;">
                                <tr>
                                    <td style="font-size: 8px">Sub-total:</td>
                                    <td style="font-size: 8px">'.$subtotal.'</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 8px"><i>IVA '.$variables_sistema['iva'].' %:</i></td>
                                    <td style="font-size: 8px">'.$iva.'</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 8px"><b>TOTAL:</b></td>
                                    <td style="font-size: 8px"><b>'.$pago.'</b></td>
                                </tr>
                            </table>
                    </td>
                </tr>';
        
        $tbl .= '<tr><td colspan="3"></td></tr><tr><td colspan="3"></td></tr><tr><td colspan="3"></td></tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="left"><hr/></td>
                </tr>
                <tr><td></td><td></td><td align="center">FIRMA</td></tr>';
        
        $tbl .= '</table>
        </td>
    </tr>
</table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output('planilla__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }
}

class ConPie extends TCPDF {
     public function Footer() {
        $this->Image('http://'.$_SERVER['SERVER_NAME'].'/images/organismo/pdf/gob_footer_pdf.png',0,750,450,70,'','','N','','','C');
    }
}

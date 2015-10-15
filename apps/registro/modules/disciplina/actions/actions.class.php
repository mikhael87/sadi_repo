<?php

require_once dirname(__FILE__).'/../lib/disciplinaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/disciplinaGeneratorHelper.class.php';

/**
 * delegado actions.
 *
 * @package    siglas
 * @subpackage delegado
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class disciplinaActions extends autoDisciplinaActions
{   
    public function executeIndex(sfWebRequest $request)
    {
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

      $this->registro_id= $request->getParameter('id');
      $this->pager = $this->getPager();
      $this->sort = $this->getSort();
    }
    
    public function executeVolver(sfWebRequest $request)
    {
        $this->redirect('@empresas_empresa_empresa');
    }
    
    public function executeGrupo(sfWebRequest $request)
    {
        $this->redirect('@operaciones_grupo');
    }
    
    public function executeNomina(sfWebRequest $request)
    {
        $registro_disciplina_id = $request->getParameter('id');

        $this->getUser()->setAttribute('pae_registro_disciplina_id', $registro_disciplina_id);

        $this->redirect('nomina/index');
    }
    
    public function executeDirectores(sfWebRequest $request)
    {
      $registro_disciplina_id = $request->getParameter('id');

      $this->getUser()->setAttribute('pae_registro_disciplina_id', $registro_disciplina_id);

      $this->redirect('director/index');
    }
    
    public function executeAsistenteDisciplina(sfWebRequest $request)
    {
        $this->registro_id= $request->getParameter('regid');

        $disciplinas= Doctrine::getTable('Operaciones_Disciplina')->findByStatus('A');
        
        $this->disciplinas= $disciplinas;
    }
    
    public function executeGestionarDisciplina(sfWebRequest $request)
    {
        $this->registro_id= $request->getParameter('id');

        $disciplinas= Doctrine::getTable('Operaciones_Disciplina')->findByStatus('A');
        $disciplinas_inscritas= Doctrine::getTable('Operaciones_RegistroDisciplina')->findByRegistroIdAndStatus($request->getParameter('id'), 'A');
        
        $this->disciplinas= $disciplinas;
        $this->disciplinas_inscritas= $disciplinas_inscritas;
    }
    
    public function executeSaveDisciplina(sfWebRequest $request)
    {
        $form_operaciones_registro= $request->getParameter('operaciones_registro');
        $form_operaciones_registro_disciplina= $request->getParameter('registro_disciplina');

//echo '<pre>';
//print_r($form_operaciones_registro_disciplina);
//exit;

        $procede= FALSE;
        if(count($form_operaciones_registro_disciplina) > 0) {
            $procede= TRUE;
        }

        if($procede) {
            $conn = Doctrine_Manager::connection();
            try {
                $conn->beginTransaction();
                
                foreach($form_operaciones_registro_disciplina as $disciplina_variante) {
                    $existe = Doctrine::getTable('Operaciones_RegistroDisciplina')->findByRegistroIdAndDisciplinaVarianteIdAndStatus($form_operaciones_registro['id'], $disciplina_variante, 'A');
                    if (count($existe) == 0) {
                        $operaciones_registro_disciplina= new Operaciones_RegistroDisciplina();

                        $operaciones_registro_disciplina->setRegistroId($form_operaciones_registro['id']);
                        $operaciones_registro_disciplina->setDisciplinaVarianteId($disciplina_variante);
                        $operaciones_registro_disciplina->setStatus('A');
                        
                        $operaciones_registro_disciplina->save();
                    }
                }

                //INACTIVAR LAS NO SELECCIONADAS
                $inscritas= Doctrine::getTable('Operaciones_RegistroDisciplina')->findByRegistroIdAndStatus($form_operaciones_registro['id'], 'A');
                
                foreach($inscritas as $value) {
                    if(!in_array($value->getDisciplinaVarianteId(), $form_operaciones_registro_disciplina)) {
                        $to_anular= Doctrine::getTable('Operaciones_RegistroDisciplina')->find($value->getId());
                        $to_anular->setStatus('I');
                        $to_anular->save();
                    }
                }
                
                $conn->commit();

                $this->getUser()->setFlash('notice', '¡La inscripción ha sido completada con exito!.');
                exit();
            } catch (Doctrine_Validator_Exception $e) {
                $conn->rollBack();
                //MANIPULAR ERROR
                $this->getUser()->setFlash('error', 'No pudimos registrar los datos, por favor si la situacion persiste notifica al departamente de tecnología.');
                $this->redirect('empresa/asistente?paso=4&regid='.$form_operaciones_registro['id']);
            }
        } else {
            //MANIPULAR ERROR
            $this->getUser()->setFlash('error', 'No pudimos registrar los datos, por favor si la situacion persiste notifica al departamente de tecnología.');
            $this->redirect('empresa/asistente?paso=4&regid='.$form_operaciones_registro['id']);
        }
    }
    
    public function executeNominaPdf(sfWebRequest $request) {

        $registro_disciplina_id= $request->getParameter('id');

        $registro_disciplina= Doctrine::getTable('Operaciones_RegistroDisciplina')->find($registro_disciplina_id);
        $registro_id= $registro_disciplina->getRegistroId();
        
        $registro= Doctrine::getTable('Operaciones_Registro')->find($registro_id);

        $empresa= Doctrine::getTable('Empresas_Empresa')->find($registro->getEmpresaId());
        
        $disciplina_variante= Doctrine::getTable('Operaciones_DisciplinaVariante')->find($registro_disciplina->getDisciplinaVarianteId());
        $table_disciplina= Doctrine::getTable('Operaciones_Disciplina')->find($disciplina_variante->getDisciplinaId());
        $table_variante= Doctrine::getTable('Operaciones_Variante')->find($disciplina_variante->getVarianteId());
        
        $disciplina= $table_disciplina->getNombre();
        $variante= $table_variante->getNombre();
        
        $table_registro_director= Doctrine::getTable('Operaciones_RegistroDirectorTecnico')->findOneByRegistroDisciplinaId($registro_disciplina_id);
        $director= '';
        if(count($table_registro_director) > 1) {
            $director= Doctrine::getTable('Personas_Persona')->find($table_registro_director->getPersonaId());
        }
        
        $registro_persona= Doctrine::getTable('Operaciones_RegistroPersona')->findByRegistroDisciplinaIdAndStatus($registro_disciplina_id, 'A');
        $cadena_personas= '';
        
        if(count($registro_persona) > 0) {
            $cadena_personas.= '<tr>';
            $round= 1;
            foreach($registro_persona as $value) {
                $personas_persona= Doctrine::getTable('Personas_Persona')->find($value->getPersonaId());

                if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$personas_persona->getCi().'.jpg')){ 
                    $foto= '<img src="/images/fotos_personal/'.$personas_persona->getCi().'.jpg" width="40"/><br/>';
                } else { 
                    $foto= '<img src="/images/other/siglas_photo_small_'.$personas_persona->getSexo().substr($personas_persona->getCi(), -1).'.png" width="40"/>';
                } 

                $cadena_personas.= '<td>';
                $cadena_personas.= '<table border="0">';
                $cadena_personas.= '<tr><td rowspan="3" style="width: 20%">'.$foto.'</td><td><font style="font-size: 12px">'.$personas_persona->getPrimerNombre().' '.$personas_persona->getPrimerApellido().'</font></td></tr>';
                $cadena_personas.= '<tr><td>C.I.: '.$personas_persona->getCi().'</td></tr>';

                list($Y,$m,$d) = explode("-",$personas_persona->getFNacimiento());
                $edad = (date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y);

                $cadena_personas.= '<tr><td>Edad: '.(($edad > 1)? $edad : '').'</td></tr>';
                $cadena_personas.= '</table>';
                $cadena_personas.= '</td>';

                if($round== 2) {
                    $cadena_personas.= '</tr><tr><td colspan="2"></td></tr><tr>';

                    $round= 0;
                }

                $round++;
            }
            if($round != 1) {
                $cadena_personas.= '<td></td>';
            }else {
                $cadena_personas.= '<td colspan="2"></td>';
            }
            $cadena_personas.= '</tr>';
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
                <td colpan="2">
                </td>
                <td align="right"><font style="color: #666">'.date('d-m-Y h:i A').'</font></td>
            </tr>
            <tr>
                <td colpan="3">
                    <font style="font-size: 12; font-weight: bold">'. $empresa->getNombre(). '</font> <font style="color: #666">' . $empresa->getRif() .'</font>
                </td>
            </tr>
            <tr>
                <td colspan="3"><font style="font-style: italic; font-size:15">'.$disciplina.'</font>&nbsp;<font style="color: #666; font-style: italic">('.$variante.')</font></td>
            </tr>
            <tr>
                <td colspan="3"><font style="color: #666">Director t&eacute;cnico: '.(($director== '')? 'N/A':$director->getPrimerNombre().'&nbsp;'.$director->getPrimerApellido()).'</font></td>
            </tr>
        </table><br/><br/>';

        $tbl.= '<table width="470" "center" border="0">
            '. $cadena_personas .'
        </table>';
        
        
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output('Nomina_'.$disciplina.'_'.$variante.'_'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }
    
    public function executeAsignarGrupo(sfWebRequest $request) {
        $grupo_id= $request->getParameter('grupo');
        $id= $request->getParameter('id');

        $registro= Doctrine::getTable('Operaciones_RegistroDisciplina')->find($id);
        
        $registro->setGrupoId($grupo_id);
        $registro->save();
        
        $grupo= Doctrine::getTable('Operaciones_Grupo')->find($registro->getGrupoId());
        
        echo $grupo->getNombre();
        exit();
    }
}

class ConPie extends TCPDF {
     public function Footer() {
        $this->Image('http://'.$_SERVER['SERVER_NAME'].'/images/organismo/pdf/gob_footer_pdf.png',0,750,450,70,'','','N','','','C');
    }
}

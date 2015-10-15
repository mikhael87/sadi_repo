<?php

require_once dirname(__FILE__).'/../lib/juegoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/juegoGeneratorHelper.class.php';

/**
 * juego actions.
 *
 * @package    siglas
 * @subpackage juego
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class juegoActions extends autoJuegoActions
{
    public function executeActualizarVariante(sfWebRequest $request) {
        $disciplina_id= $request->getParameter('disc');
        $old_variante= $request->getParameter('vari');
        
        $variantes= Doctrine::getTable('Operaciones_DisciplinaVariante')->variantesPorDisciplina($disciplina_id);
        
        $this->old_variante= $old_variante;
        $this->variantes= $variantes;
    }
    
    public function executeMatch(sfWebRequest $request) {
        $disciplina_id= $request->getParameter('disc');
        $disciplina_variante_id= $request->getParameter('disc_v');
        $mod= $request->getParameter('mod');
        $grupo= $request->getParameter('grupo');

        $datos_disciplina= Doctrine::getTable('Operaciones_Disciplina')->find($disciplina_id);

        if($grupo == '') {
            $empresas= Doctrine::getTable('Empresas_Empresa')->empresasRegistradas($disciplina_variante_id);
        }else {
            $empresas= Doctrine::getTable('Empresas_Empresa')->empresasRegistradasPorGrupo($disciplina_variante_id, $grupo);
        }
        
        $registros= '';
        if($mod!= '') {
            $registros= Doctrine::getTable('Operaciones_Resultado')->findByJuegoId($mod);
        }
        
        $grupos= Doctrine::getTable('Operaciones_Grupo')->disponibles();
        
        $this->grupos= $grupos;
        $this->registros= $registros;
        $this->empresas= $empresas;
        $this->tipo_juego= $datos_disciplina->getTipoJuego();
        $this->tipo_marcador= $datos_disciplina->getTipoMarcador();
    }
    
    public function executeAddTeam(sfWebRequest $request) {
        $disciplina_variante_id= $request->getParameter('disc_v');
        $empresas= Doctrine::getTable('Empresas_Empresa')->empresasRegistradas($disciplina_variante_id);
        
        $this->empresas= $empresas;
        $this->pos= $request->getParameter('pos');
    }
    
    protected function processForm(sfWebRequest $request, sfForm $form)
    {
        $datos = $request->getParameter('operaciones_juego');
        $tipo = $request->getParameter('tipo');

        $datos['liga_id']= sfContext::getInstance()->getUser()->getAttribute('liga_actual');
        $datos['tipo_juego']= substr($tipo['op'], 0, 1);
        $datos['tipo_marcador']= substr($tipo['op'], -1, 1);
        $request->setParameter('operaciones_juego',$datos);
        
      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid())
      {
        $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

        $conn = Doctrine_Manager::connection();
        try {
          $conn->beginTransaction();  
            
          $operaciones_juego = $form->save();
          
          //BORRADO DE REGISTROS DE RESULTADOS ANTERIORES
          $resultados = Doctrine::getTable('Operaciones_Resultado')
                    ->createQuery()
                    ->delete()
                    ->where('juego_id = ?', $operaciones_juego->getId())
                    ->andWhere('status = ?', 'A')
                    ->execute();
          
          switch ($tipo['op']) {
              case 'VP':
                    //VERSUS CON PUNTAJE
                    if($tipo['equipos'][0]['id'] != '' && $tipo['equipos'][1]['id'] !== '') {
                        //CALCULO DE OPERADOR
                        //SOLO SI LA FECHA ES PASADA
                        if(strtotime($operaciones_juego->getFecha()) <= strtotime(date('Y-m-d'))) {
                            //CONSULTAR TABULADOR
                            $tabulador= Doctrine::getTable('Operaciones_Tabulador')->tabulardorPorDisciplinaVariante($operaciones_juego->getDisciplinaVarianteId());
                            $w= 0; $l=0; $d= 0; $f= 0;
                            foreach($tabulador as $value) {
                                $w= $value->getWin();
                                $l= $value->getLose();
                                $d= $value->getDraw();
                                $f= $value->getForfait();
                            }

                            if($tipo['equipos'][0]['marcador'] > $tipo['equipos'][1]['marcador']) {
                                $tipo['equipos'][0]['operador']= '+'.$w;
                                $tipo['equipos'][1]['operador']= '+'.$l;
                            }elseif($tipo['equipos'][0]['marcador'] < $tipo['equipos'][1]['marcador']) {
                                $tipo['equipos'][0]['operador']= '+'.$l;
                                $tipo['equipos'][1]['operador']= '+'.$w;
                            }elseif($tipo['equipos'][0]['marcador'] == $tipo['equipos'][1]['marcador']) {
                                $tipo['equipos'][0]['operador']= '+'.$d;
                                $tipo['equipos'][1]['operador']= '+'.$d;
                            }
                        }
                        //FIN DE CALCULO DE OPERADOR
                        
                        //REVERSA PUNTAJES GUARDADOS
                            
                        //GUARDA RESULTADOS
                        foreach($tipo['equipos'] as $equipo) {
                            //EQUIPO DONDE SE GUARDARA PUNTAJE (ANTES SE REVERSARA PUNTAJE EN CASO DE EDITAR)
//                            $equipo_puntaje= Doctrine::getTable('Operaciones_RegistroDisciplina')->findOneByRegistroIdAndDisciplinaVarianteId($equipo['id'], $operaciones_juego->getDisciplinaVarianteId());
//
//                            if(!$form->getObject()->isNew() && $datos['id']) {
//                                $resultados_ver= Doctrine::getTable('Operaciones_Resultado')->findByJuegoId($datos['id']);
//
//                                foreach($resultados_ver as $value) {
////                                    echo $value->getId().'--';
//                                    if($value->getOperador() != '' && $value->getOperador() != '+0' && $equipo_puntaje->getPuntaje() != 0) {
//                                        //REVERSAR SOLO EL EQUIPO DE LA EMPRESA QUE ESTA CORRIENDO
//                                        if($value->getRegistroId() == $equipo['id']) {
//                                            if(substr($value->getOperador(), 0, 1) == '+') {
//                                                $result= $equipo_puntaje->getPuntaje() - substr($value->getOperador(), -1);
//                                            }elseif(substr($value->getOperador(), 0, 1) == '-') {
//                                                $result= $equipo_puntaje->getPuntaje() + substr($value->getOperador(), -1);
//                                            }
//                                        }
//
//                                        $equipo_puntaje->setPuntaje($result);
//                                        $equipo_puntaje->save();
//                                    }
//                                }
//                            }
                            
                            //FIN DE REVERSA PUNTAJES GUARDADOS
                            
                            $operaciones_resultado= new Operaciones_Resultado();
                        
                            $operaciones_resultado->setJuegoId($operaciones_juego->getId());
                            $operaciones_resultado->setRegistroId($equipo['id']);
                            $operaciones_resultado->setMarcador($equipo['marcador']);
                            if(isset($equipo['operador'])) {
                                $operaciones_resultado->setOperador($equipo['operador']);
                            }
                            $operaciones_resultado->setStatus('A');
                            
                            $operaciones_resultado->save();
                            
                            if(isset($equipo['operador'])) {
                                //GUARDA PUNTAJE
                                $equipo_puntaje= Doctrine::getTable('Operaciones_RegistroDisciplina')->findOneByRegistroIdAndDisciplinaVarianteId($equipo['id'], $operaciones_juego->getDisciplinaVarianteId());
                                $result= 0;

                                if(substr($equipo['operador'], 0, 1) == '+') {
                                    $result= $equipo_puntaje->getPuntaje() + substr($equipo['operador'], -1);
                                }elseif(substr($equipo['operador'], 0, 1) == '-') {
                                    $result= $equipo_puntaje->getPuntaje() - substr($equipo['operador'], -1);
                                }
                                
                                $equipo_puntaje->setPuntaje($result);
                                $equipo_puntaje->save();
                            }
                        }
                    }
                    break;
              case 'VS':
                  //VERSUS SIMPLE
                  if($tipo['equipos'][0]['id'] != '' && $tipo['equipos'][1]['id'] !== '') {
                        //CALCULO DE OPERADOR
                        //SOLO SI LA FECHA ES PASADA
                        if(strtotime($operaciones_juego->getFecha()) <= strtotime(date('Y-m-d'))) {
                            //CONSULTAR TABULADOR
                            $tabulador= Doctrine::getTable('Operaciones_Tabulador')->tabulardorPorDisciplinaVariante($operaciones_juego->getDisciplinaVarianteId());
                            $w= 0; $l=0; $d= 0; $f= 0;
                            foreach($tabulador as $value) {
                                $w= $value->getWin();
                                $l= $value->getLose();
                                $d= $value->getDraw();
                                $f= $value->getForfait();
                            }

                            if($tipo['equipos'][0]['marcador'] > $tipo['equipos'][1]['marcador']) {
                                $tipo['equipos'][0]['operador']= '+'.$w;
                                $tipo['equipos'][1]['operador']= '+'.$l;
                            }elseif($tipo['equipos'][0]['marcador'] < $tipo['equipos'][1]['marcador']) {
                                $tipo['equipos'][0]['operador']= '+'.$l;
                                $tipo['equipos'][1]['operador']= '+'.$w;
                            }elseif($tipo['equipos'][0]['marcador'] == $tipo['equipos'][1]['marcador']) {
                                $tipo['equipos'][0]['operador']= '+'.$d;
                                $tipo['equipos'][1]['operador']= '+'.$d;
                            }
                        }
                        //FIN DE CALCULO DE OPERADOR
                      
                        //GUARDA RESULTADOS
                        if($tipo['equipos'][0]['marcador'] == 0) {
                            $tipo['equipos'][0]['marcador']= 1;
                            $tipo['equipos'][1]['marcador']= 0;
                        }else {
                            $tipo['equipos'][0]['marcador']= 0;
                            $tipo['equipos'][1]['marcador']= 1;
                        }
                      
                        foreach($tipo['equipos'] as $equipo) {
                            $operaciones_resultado= new Operaciones_Resultado();
                        
                            $operaciones_resultado->setJuegoId($operaciones_juego->getId());
                            $operaciones_resultado->setRegistroId($equipo['id']);
                            $operaciones_resultado->setMarcador($equipo['marcador']);
                            if(isset($equipo['operador'])) {
                                $operaciones_resultado->setOperador($equipo['operador']);
                            }
                            $operaciones_resultado->setStatus('A');
                            
                            $operaciones_resultado->save();
                            
                            if(isset($equipo['operador'])) {
                                //GUARDA PUNTAJE
                                $equipo_puntaje= Doctrine::getTable('Operaciones_RegistroDisciplina')->findOneByRegistroIdAndDisciplinaVarianteId($equipo['id'], $operaciones_juego->getDisciplinaVarianteId());
                                $result= 0;
                                if(substr($equipo['operador'], 0, 1) == '+') {
                                    $result= $equipo_puntaje->getPuntaje() + substr($equipo['operador'], -1);
                                }elseif(substr($equipo['operador'], 0, 1) == '-') {
                                    $result= $equipo_puntaje->getPuntaje() - substr($equipo['operador'], -1);
                                }
                                $equipo_puntaje->setPuntaje($result);
                                $equipo_puntaje->save();
                            }
                        }
                    }
                  break;
              case 'VO':
                  //VERSUS POR ORDEN

                  break;
              case 'MP':
                  //MULTIPLES POR PUNTAJE

                  break;
              case 'MO':
                  //MULTIPLES POR ORDEN
                  if(count($tipo['equipos'] > 0)) {
                      $empty= FALSE;
                      foreach($tipo['equipos'] as $equipo) {
                          if($equipo['id'] == '') {
                              $empty= TRUE;
                          }
                      }
                      
                      if(!$empty) {
                            $pos= 1;
                            foreach($tipo['equipos'] as $equipo) {
                                $operaciones_resultado= new Operaciones_Resultado();

                                $operaciones_resultado->setJuegoId($operaciones_juego->getId());
                                $operaciones_resultado->setRegistroId($equipo['id']);
                                $operaciones_resultado->setMarcador($pos);
                                $operaciones_resultado->setStatus('A');

                                $operaciones_resultado->save();
                                $pos++;
                            }
                      }
                  }
                  break;
              default:
                  break;
          }
          
          $conn->commit();
        } catch (Doctrine_Validator_Exception $e) {
          $conn->rollBack();
            
          $errorStack = $form->getObject()->getErrorStack();

          $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
          foreach ($errorStack as $field => $errors) {
              $message .= "$field (" . implode(", ", $errors) . "), ";
          }
          $message = trim($message, ', ');

          $this->getUser()->setFlash('error', $message);
          return sfView::SUCCESS;
        }

        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $operaciones_juego)));

        if ($request->hasParameter('_save_and_add'))
        {
          $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

          $this->redirect('@operaciones_juego_new');
        }
        else
        {
//          $this->redirect(array('sf_route' => 'operaciones_juego_edit', 'sf_subject' => $operaciones_juego));
            $this->getUser()->setFlash('notice', 'El juego ha sido registrado con éxito.');
            $this->redirect('@operaciones_juego');
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      }
    }
    
    public function executeAnular(sfWebRequest $request) {
        $juego_id = $request->getParameter('id');
        
        $juego_datos= Doctrine::getTable('Operaciones_Juego')->find($juego_id);
        
        $juego_datos->setStatus('I');
        $juego_datos->save();
        
        $this->getUser()->setFlash('notice', 'El juego ha sido anulado con exito.');
        $this->redirect('@operaciones_juego');
    }
    
    public function executeReporte(sfWebRequest $request) {
    }
}

<?php

require_once dirname(__FILE__).'/../lib/externoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/externoGeneratorHelper.class.php';

/**
 * externo actions.
 *
 * @package    siglas
 * @subpackage externo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class externoActions extends autoExternoActions
{
    public function executeIndex(sfWebRequest $request)
    {
        sfContext::getInstance()->getUser()->getAttributeHolder()->remove('sms_masivo_query');
        sfContext::getInstance()->getUser()->getAttributeHolder()->remove('sms_masivo_count');

        sfContext::getInstance()->getUser()->getAttributeHolder()->remove('sms_reutilizado');

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

        //SETEO DE ATRIBUTO PARA BARRA DE PROGRESO GENERAL
        $pendientes_general= Sms::process_sms('outbox', NULL, $this->context->getUser()->getAttribute('funcionario_id'));
        if($pendientes_general == 'no_conn' || $pendientes_general == '')
            $pendientes_general= '-1';
        else
            $pendientes_general= $pendientes_general[0]['cant'];
        $this->getUser()->setAttribute('sms_pendientes', $pendientes_general);
    }

    public function executeMensajes(sfWebRequest $request)
    {
        $this->redirect('mensajes/index');
    }

    public function executeMostrarDestinatarios(sfWebRequest $request)
    {
        $mensajes_id = $request->getParameter('id');
        $yml_variables = Doctrine::getTable('Public_MensajesMasivos')->findByMensajesId($mensajes_id);

        $td = ''; $th = ''; $i = 0;

        foreach ($yml_variables as $yml_variable){
            $variables = sfYaml::load($yml_variable->getVariables());

            foreach ($variables as $variable) {
                $td .= "<tr>";
                foreach ($variable as $key => $value) {
                    if($i==0) $th .= "<th>".$key."</th>";
                    $td .= "<td>".$value."</td>";
                }
                $td .= "</tr>";
                $i++;
            }
        }

        echo "<table><tr>".$th."</tr>".$td."</table>";
        exit();
    }

    public function executeCargarProgreso(sfWebRequest $request)
    {
        $mensajes_ids_array = $request->getParameter('ids');
        $mensajes_ids = explode("-", $mensajes_ids_array);

        $script_progreso = '<script>';
        foreach ($mensajes_ids as $mensajes_datos) {
            list($mensajes_id,$mensaje_status) = explode("_", $mensajes_datos);
            $total_mensajes = Doctrine::getTable('Public_MensajesMasivos')->mensajesTotalEnvio($mensajes_id);
            $total_mensajes = $total_mensajes[0]->getTotal();

            $procesados= Sms::process_sms('sentitems', $mensajes_id);
            
            if ($procesados != 'no_conn' && $procesados != '') {
                $total_procesados = $procesados[0]['cant'];

                $total_cola = $total_mensajes - $total_procesados;

                if($mensaje_status == 'C' || $mensaje_status == 'P'){
                    $script_progreso .= "$('#calcular_progreso_".$mensajes_id."').removeClass('calcular_progreso');";

                    if($mensaje_status == 'P'){
                        $script_progreso .= "$('#cola_".$mensajes_id."').html('".$total_cola."');";
                    }
                } else {
                    if($total_cola == 0){
                        $mensajes = Doctrine::getTable('Public_Mensajes')->find($mensajes_id);
                        if($mensajes->getStatus()=='A'){
                            $mensajes->setStatus('F');
                            $mensajes->save();
                        }

                        $q = Doctrine_Query::create();
                        $q->update('Public_MensajesMasivos')
                        ->set('procesados', '?', $total_procesados)
                        ->set('cola', '?', $total_cola)
                        ->set('status', '?', 'F')
                        ->where('mensajes_id = ?', $mensajes_id)
                        ->execute();

                        $script_progreso .= "$('#calcular_progreso_".$mensajes_id."').removeClass('calcular_progreso');";
                        $script_progreso .= "$('#action_cancelar_".$mensajes_id."').remove();";
                        $script_progreso .= "$('#action_pausar_".$mensajes_id."').remove();";

                        $total_cola = '<fond style="color: #04B404;">Finalizado</fond>';
                    }

                    $script_progreso .= "$('#cola_".$mensajes_id."').html('".$total_cola."');";
                }

                $script_progreso .= "$('#procesados_".$mensajes_id."').html('".$total_procesados."');";
            }else {
                $script_progreso .= "$('#cola_".$mensajes_id."').html('<font style=\'font-size: 9px; color: #666\'>Sin conexión<br/>Gammu</font>');$('#procesados_".$mensajes_id."').html('<font style=\'font-size: 9px; color: #666\'>Sin conexión<br/>Gammu</font>');";
            }
        }
        $script_progreso .= '</script>';

        echo $script_progreso;
        exit();
    }

    //Funcion no utilizada
    public function executeProcesadosSms(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $procesados= Sms::process_sms('sentitems', $id);
        echo trim($procesados[0]['cant']);
        exit();
    }

    public function executeProcesadosSmsTotal(sfWebRequest $request)
    {
        $procesados= Sms::process_sms('outbox', NULL, $this->context->getUser()->getAttribute('funcionario_id'));
        $pendientes= $this->getUser()->getAttribute('sms_pendientes');
        if($procesados != 'no_conn' && $procesados != '') {
            if($pendientes > 0) {
                $inverso= $pendientes- $procesados[0]['cant'];
                $porcentajeToBar= ($inverso* 100)/ $pendientes;
                $porcentajeToBar= number_format($porcentajeToBar, 2, ".", "");     
            }else {
                //si la conexion de gammu se reestablece hay que actualizar navegador
                $porcentajeToBar= '0';
            }
        }else {
            $porcentajeToBar= '0';
        }
        
        echo $porcentajeToBar;
        exit();
    }

    public function executeProcesadosSmsTotalNumero(sfWebRequest $request)
    {
        $procesados= Sms::process_sms('outbox', NULL, $this->context->getUser()->getAttribute('funcionario_id'));
        $pendientes= $this->getUser()->getAttribute('sms_pendientes');
        if($procesados != 'no_conn' && $procesados != '') {
            if($pendientes > 0)
                $enviados= $pendientes- $procesados[0]['cant'].' de '.$pendientes;
            else
                //si la conexion de gammu se reestablece hay que actualizar navegador
                $enviados= 'Sin conexión gammu';
        }else {
            $enviados= 'Sin conexión gammu';
        }
        echo $enviados;
        exit();
    }

    public function executeColaSms(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $cola= Sms::process_sms('outbox', $id);
        echo trim($cola[0]['cant']);
        exit();
    }

    public function executeLimpiarOutbox(sfWebRequest $request)
    {
        $q = Doctrine_Query::create()
            ->update('Public_Mensajes m')
            ->set('m.status', '?', 'C')
            ->whereIn('m.status', array('A','P'))
            ->andwhere('m.funcionario_envia_id = ?', $this->context->getUser()->getAttribute('funcionario_id'));
            $q->execute();

        Sms::clean_outbox($this->context->getUser()->getAttribute('funcionario_id'));
        $this->redirect('externo/index');
    }

    public function executeReutilizar(sfWebRequest $request)
    {
        $this->getUser()->setAttribute('sms_reutilizado', $request->getParameter('id'));
        $this->redirect('externo/new');
    }

    public function executeCancelar(sfWebRequest $request)
    {
        $mensajes = Doctrine::getTable('Public_Mensajes')->find($request->getParameter('id'));
        $mensajes->setStatus('C');
        $mensajes->save();
        
        $mensajes_masivos = Doctrine::getTable('Public_MensajesMasivos')->findOneByMensajesId($request->getParameter('id'));
        $mensajes_masivos->setStatus('C');
        $mensajes_masivos->save();

        Sms::cancelar('mensajes', $request->getParameter('id'));

        $this->redirect('externo/index');
    }

    public function executePausar(sfWebRequest $request)
    {
        $mensajes = Doctrine::getTable('Public_Mensajes')->find($request->getParameter('id'));
        $mensajes->setStatus('P');
        $mensajes->save();

        Sms::pausar('mensajes', $request->getParameter('id'));

        $this->redirect('externo/index');
    }

    public function executeContinuar(sfWebRequest $request)
    {
        $mensajes = Doctrine::getTable('Public_Mensajes')->find($request->getParameter('id'));
        $mensajes->setStatus('A');
        $mensajes->save();

        Sms::continuar('mensajes', $request->getParameter('id'));

        $this->redirect('externo/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form)
    {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        $excel_permitido= null;
        $error_excel_detail= array();

        $prioridad = $request->getParameter('prioridad_masivos');
        $modem_masivos = $request->getParameter('modem_masivos');
        if($modem_masivos== 'auto') {
            $modem_masivos= Sms::available_modem();
        }

        if(!empty($_FILES['archivo']['name'])) {
            if (substr($_FILES['archivo']['name'], -4)== '.xls' || substr($_FILES['archivo']['name'], -4)== 'xlsx'){
                $dir_file = sfConfig::get('sf_upload_dir') . '/excel/' . $_FILES['archivo']['name'];
                move_uploaded_file($_FILES['archivo']['tmp_name'], $dir_file);

                //VERIFICACION ANTERIOR DE EXCEL
                $result= Sms::count_device();
                $per_page = 200;
                $row_limit = count($result)* $per_page;
                $row_limit = 50000;
                $telf_tipo = 'n';
                $highestRow = 0;
                $highestColumn = 0;
                $highestColumnIndex = 0;
                $dato_full = true;

                require_once 'PHPExcel/IOFactory.php';
                $objPHPExcel = PHPExcel_IOFactory::load($dir_file, $nocheck = true);

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $i= 0;
                    for ($row = 1; $row <= $highestRow; ++$row) {
                        $cell = $worksheet->getCellByColumnAndRow(0, $row);
                        $val = $cell->getValue();
                        $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                        if (!$dataType == 'n')
                            $telf_tipo = $dataType;
                        //EN EL SUPUESTO 41236413538
                        if (substr($val, 1) == 0 && strlen($val) != 11) {
                            $dato_full = false;
                            $error_excel_detail[$i]['fila']= $row;
                            $error_excel_detail[$i]['numero']= $val;
                            $i++;
                        }
                        if (substr($val, 1) != 0 && strlen($val) != 10) {
                            $dato_full = false;
                            $error_excel_detail[$i]['fila']= $row;
                            $error_excel_detail[$i]['numero']= $val;
                            $i++;
                        }
                    }
                    break;
                }
                //VERIFICA QUE EL ARCHIVO CUMPLA LOS PARAMETROS
                if ($highestRow <= $row_limit && $telf_tipo == 'n' && $dato_full == true) {
                    $excel_permitido = 'si';
                } else {
                    $excel_permitido = 'no';
                }

            }else{
                $excel_permitido = 'extension';
            }
        }

        if ($form->isValid() && $excel_permitido == null || $excel_permitido == 'si')
        {
        $notice = $form->getObject()->isNew() ? 'EL mensaje se ha enviado con exito.' : 'The item was updated successfully.';
        try {
            $public_mensajes = $form->save();

            $public_mensajes->setTipo('E');
            $public_mensajes->save();

            $telf= $request->getParameter("telf");
            $telf_unico= $request->getParameter("codigo_movil").$telf['unico']['tlf'];
            $nombre_unico= $telf['unico']['nombre'];

            //ARRAY PARA BDD GAMMU
            $emisor = Doctrine::getTable('Funcionarios_Funcionario')->find($public_mensajes->getFuncionarioEnviaId());
            $mensaje['emisor'] = $emisor->getPrimerNombre().' '.$emisor->getPrimerApellido();
            $mensaje['mensaje_original'] = $public_mensajes->getContenido();
            //PARA GUARDADO UNICO
            $mensaje['mensaje'] = str_replace('%%NOMBRE%%',trim($nombre_unico),$mensaje['mensaje_original']);

            //BIFURCACION PARA IMPORTACION EXCEL
            if($excel_permitido == 'si') {
                    //VERIFICA QUE EL ARCHIVO CUMPLA LOS PARAMETROS
                    $name= '';
                    $tlf= '';

                    if($public_mensajes->getNInformeProgreso()!=''){
                        $supervisores = explode(';', $public_mensajes->getNInformeProgreso());

                        $this->getUser()->setAttribute('sms_masivo_query',' ');
                        $this->getUser()->setAttribute('sms_masivo_count',count($supervisores));
                        $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                        $this->getUser()->setAttribute('sms_masivo_count_160',1);

                        $mensaje['mensaje'] = 'Iniciado envio "'.$public_mensajes->getNombreExterno().'" - total de destinatarios: '.$highestRow;
                        foreach ($supervisores as $supervisor_tlf) {
                            Sms::notificacion_personal('mensajes', $supervisor_tlf, $mensaje, $modem_masivos, $public_mensajes->getId().'-informe');
                        }
                    }

                    $this->getUser()->setAttribute('sms_masivo_query',' ');
                    $this->getUser()->setAttribute('sms_masivo_count',$highestRow);
                    $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                    $this->getUser()->setAttribute('sms_masivo_count_160',1);

                    $variables = array(); $j=0;
                    for ($row = 1; $row <= $highestRow; ++$row) {
                        $i = 0;
                        for ($col = 0; $col < $highestColumnIndex; ++$col) {
                            $cell = $worksheet->getCellByColumnAndRow($col, $row);
                            if ($i == 0)
                                $tlf = $cell->getValue();
                            if ($i == 1)
                                $name = $cell->getValue();
                            $i++;
                        }
                        //GUARDO MENSAJE BDD GAMMU
                        $mensaje['mensaje'] = str_replace('%%NOMBRE%%', trim($name), $mensaje['mensaje_original']);
                        Sms::notificacion_personal('mensajes', $tlf, $mensaje, $modem_masivos, $public_mensajes->getId());


                        $variables[$j] = array('TELF' => trim($tlf),'NOMBRE' => $name);
                        $j++;

                        if($j==100){
                            $yml_variables = sfYAML::dump($variables);
                            //GUARDO MENSAJE BDD SIGLAS MASIVO
                            $mensaje_masivo = new Public_MensajesMasivos();
                            $mensaje_masivo->setVariables($yml_variables);
                            $mensaje_masivo->setMensajesId($public_mensajes->getId());
                            $mensaje_masivo->setDestinatarios($j);
                            $mensaje_masivo->setPrioridad($prioridad);
                            $mensaje_masivo->setModemEmisor($modem_masivos);
                            $mensaje_masivo->setTotal($this->getUser()->getAttribute('sms_masivo_count_progreso')*$this->getUser()->getAttribute('sms_masivo_count_160'));
                            $mensaje_masivo->setProcesados(0);
                            $mensaje_masivo->setCola($this->getUser()->getAttribute('sms_masivo_count_progreso'));
                            $mensaje_masivo->save();


                            if($public_mensajes->getNInformeProgreso()!=''){
                                $supervisores = explode(';', $public_mensajes->getNInformeProgreso());

                                $this->getUser()->setAttribute('sms_masivo_query',' ');
                                $this->getUser()->setAttribute('sms_masivo_count',count($supervisores));
                                $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                                $this->getUser()->setAttribute('sms_masivo_count_160',1);

                                $mensaje['mensaje'] = 'Estatus envio "'.$public_mensajes->getNombreExterno().'" - enviando al destinatario '.$row.' de '.$highestRow;
                                foreach ($supervisores as $supervisor_tlf) {
                                    Sms::notificacion_personal('mensajes', $supervisor_tlf, $mensaje, $modem_masivos, $public_mensajes->getId().'-informe');
                                }
                            }


                            $variables = array(); $j=0;
                            $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                            $this->getUser()->setAttribute('sms_masivo_count_160',1);
                        }
                    }

                    if($j>0){
                        $yml_variables = sfYAML::dump($variables);
                        //GUARDO MENSAJE BDD SIGLAS MASIVO
                        $mensaje_masivo = new Public_MensajesMasivos();
                        $mensaje_masivo->setVariables($yml_variables);
                        $mensaje_masivo->setMensajesId($public_mensajes->getId());
                        $mensaje_masivo->setDestinatarios($j);
                        $mensaje_masivo->setPrioridad($prioridad);
                        $mensaje_masivo->setModemEmisor($modem_masivos);
                        $mensaje_masivo->setTotal($this->getUser()->getAttribute('sms_masivo_count_progreso')*$this->getUser()->getAttribute('sms_masivo_count_160'));
                        $mensaje_masivo->setProcesados(0);
                        $mensaje_masivo->setCola($this->getUser()->getAttribute('sms_masivo_count_progreso'));
                        $mensaje_masivo->save();

                        $variables = array(); $j=0;
                        $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                        $this->getUser()->setAttribute('sms_masivo_count_160',1);
                    }

                    if($public_mensajes->getNInformeProgreso()!=''){
                        $supervisores = explode(';', $public_mensajes->getNInformeProgreso());

                        $this->getUser()->setAttribute('sms_masivo_query',' ');
                        $this->getUser()->setAttribute('sms_masivo_count',count($supervisores));
                        $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                        $this->getUser()->setAttribute('sms_masivo_count_160',1);

                        $mensaje['mensaje'] = 'Finalizado envio "'.$public_mensajes->getNombreExterno().'" - total de destinatarios: '.$highestRow;
                        foreach ($supervisores as $supervisor_tlf) {
                            Sms::notificacion_personal('mensajes', $supervisor_tlf, $mensaje, $modem_masivos, $public_mensajes->getId().'-informe');
                        }
                    }

            }else {
                if (isset($telf['otros'])) {
                    if($public_mensajes->getNInformeProgreso()!=''){
                        $supervisores = explode(';', $public_mensajes->getNInformeProgreso());

                        $this->getUser()->setAttribute('sms_masivo_query',' ');
                        $this->getUser()->setAttribute('sms_masivo_count',count($supervisores));
                        $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                        $this->getUser()->setAttribute('sms_masivo_count_160',1);

                        $mensaje['mensaje'] = 'Iniciado envio "'.$public_mensajes->getNombreExterno().'" - total de destinatarios: '.count($telf['otros']);
                        foreach ($supervisores as $supervisor_tlf) {
                            Sms::notificacion_personal('mensajes', $supervisor_tlf, $mensaje, $modem_masivos, $public_mensajes->getId().'-informe');
                        }
                    }



                    $this->getUser()->setAttribute('sms_masivo_query',' ');
                    $this->getUser()->setAttribute('sms_masivo_count',count($telf['otros']));
                    $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                    $this->getUser()->setAttribute('sms_masivo_count_160',1);

                    $variables = array(); $j=0; $des=0;
                    foreach ($telf['otros'] as $value) {
                        $des++;
                        $mensaje['mensaje'] = str_replace('%%NOMBRE%%',trim($value['nombre']),$mensaje['mensaje_original']);
                        Sms::notificacion_personal('mensajes', $value['tlf'], $mensaje, $modem_masivos, $public_mensajes->getId());

                        $variables[$j] = array('TELF' => trim($value['tlf']),'NOMBRE' => trim($value['nombre']));
                        $j++;

                        if($j==100){
                            $yml_variables = sfYAML::dump($variables);
                            //GUARDO MENSAJE BDD SIGLAS MASIVO
                            $mensaje_masivo = new Public_MensajesMasivos();
                            $mensaje_masivo->setVariables($yml_variables);
                            $mensaje_masivo->setMensajesId($public_mensajes->getId());
                            $mensaje_masivo->setDestinatarios($j);
                            $mensaje_masivo->setPrioridad($prioridad);
                            $mensaje_masivo->setModemEmisor($modem_masivos);
                            $mensaje_masivo->setTotal($this->getUser()->getAttribute('sms_masivo_count_progreso')*$this->getUser()->getAttribute('sms_masivo_count_160'));
                            $mensaje_masivo->setProcesados(0);
                            $mensaje_masivo->setCola($this->getUser()->getAttribute('sms_masivo_count_progreso'));
                            $mensaje_masivo->save();


                            if($public_mensajes->getNInformeProgreso()!=''){
                                $supervisores = explode(';', $public_mensajes->getNInformeProgreso());

                                $this->getUser()->setAttribute('sms_masivo_query',' ');
                                $this->getUser()->setAttribute('sms_masivo_count',count($supervisores));
                                $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                                $this->getUser()->setAttribute('sms_masivo_count_160',1);

                                $mensaje['mensaje'] = 'Estatus envio "'.$public_mensajes->getNombreExterno().'" - enviando al destinatario '.$des.' de '.count($telf['otros']);
                                foreach ($supervisores as $supervisor_tlf) {
                                    Sms::notificacion_personal('mensajes', $supervisor_tlf, $mensaje, $modem_masivos, $public_mensajes->getId().'-informe');
                                }
                            }


                            $variables = array(); $j=0;
                            $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                            $this->getUser()->setAttribute('sms_masivo_count_160',1);
                        }
                    }

                    if($j>0){
                        $yml_variables = sfYAML::dump($variables);
                        //GUARDO MENSAJE BDD SIGLAS MASIVO
                        $mensaje_masivo = new Public_MensajesMasivos();
                        $mensaje_masivo->setVariables($yml_variables);
                        $mensaje_masivo->setMensajesId($public_mensajes->getId());
                        $mensaje_masivo->setDestinatarios($j);
                        $mensaje_masivo->setPrioridad($prioridad);
                        $mensaje_masivo->setModemEmisor($modem_masivos);
                        $mensaje_masivo->setTotal($this->getUser()->getAttribute('sms_masivo_count_progreso')*$this->getUser()->getAttribute('sms_masivo_count_160'));
                        $mensaje_masivo->setProcesados(0);
                        $mensaje_masivo->setCola($this->getUser()->getAttribute('sms_masivo_count_progreso'));
                        $mensaje_masivo->save();

                        $variables = array(); $j=0;
                        $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                        $this->getUser()->setAttribute('sms_masivo_count_160',1);
                    }

                    if($public_mensajes->getNInformeProgreso()!=''){
                        $supervisores = explode(';', $public_mensajes->getNInformeProgreso());

                        $this->getUser()->setAttribute('sms_masivo_query',' ');
                        $this->getUser()->setAttribute('sms_masivo_count',count($supervisores));
                        $this->getUser()->setAttribute('sms_masivo_count_progreso',0);
                        $this->getUser()->setAttribute('sms_masivo_count_160',1);

                        $mensaje['mensaje'] = 'Finalizado envio "'.$public_mensajes->getNombreExterno().'" - total de destinatarios: '.count($telf['otros']);
                        foreach ($supervisores as $supervisor_tlf) {
                            Sms::notificacion_personal('mensajes', $supervisor_tlf, $mensaje, $modem_masivos, $public_mensajes->getId().'-informe');
                        }
                    }
                }
            }
        } catch (Doctrine_Validator_Exception $e) {

            $errorStack = $form->getObject()->getErrorStack();

            $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
            foreach ($errorStack as $field => $errors) {
                $message .= "$field (" . implode(", ", $errors) . "), ";
            }
            $message = trim($message, ', ');

            $this->getUser()->setFlash('error', $message);
            return sfView::SUCCESS;
        }

        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $public_mensajes)));

        if ($request->hasParameter('_save_and_add'))
        {
            $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

            $this->redirect('@public_mensajes_externo_new');
        }
        else
        {
            $this->getUser()->setFlash('notice', $notice);

            $this->redirect('@public_mensajes_externo');
        }
        }
        else
        {
        if ($excel_permitido == 'no'){
            $texto_error= '';
            if(sizeof($error_excel_detail) > 0){
                foreach($error_excel_detail as $value){
                    $num= ($value['numero']== '')? '<vacio>': $value['numero'];
                    $texto_error.= '>> dato: '.$num.', en fila: '.$value['fila'].' ';
                }
            }
            if($texto_error!= '')
                $text= 'El archivo Excel no es correcto, verifique los siguientes errores: '.$texto_error;
            else{
                if($highestRow > $row_limit)
                    $text= 'El archivo Excel no es correcto, posee mas de '.$row_limit.' filas.';
                else{
                    if($telf_tipo != 'n')
                        $text= 'El archivo Excel no es correcto, todos los telefonos deben ser numéricos.';
                    else
                        $text= 'El archivo Excel no es correcto.';
                }
            }
            $this->getUser()->setFlash('error', $text, false);
        }
        else{
            $text= 'Error al guardar el archivo';
            if($excel_permitido == 'extension')
                $text= 'Archivo con extensión no permitida. Asegurese de que este sea .xls(Excel 97) o .xlsx(Excel 2007)';
            $this->getUser()->setFlash('error', $text, false);
        }

        }
    }
}

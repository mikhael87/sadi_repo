<?php
use_helper('Date');

//NUEVO CODIGO PARA GENERACION DE EXCEL5
$objPHPExcel = new sfPhpExcel;
// set syles
//$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
//$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
// writer will create the first sheet for us, let's get it
$objSheet = $objPHPExcel->getActiveSheet();
$objSheet->setTitle('Reportes');


$default_border = array(
    'style' => PHPExcel_Style_Border::BORDER_THIN,
    'color' => array('rgb' => '1006A3')
);

$style_header = array(
    'borders' => array(
        'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'E1E0F7'),
    ),
    'font' => array(
        'bold' => true,
    )
);

function correspondenciaHijos($correspondencia_id, $cadena='') {
    $hijos=Doctrine::getTable('Correspondencia_Correspondencia')->findByPadreId($correspondencia_id);

    foreach ($hijos as $hijo) {
        $cadena .= $hijo->getNCorrespondenciaEmisor()." > ";
//        $cadena = correspondenciaHijos($hijo->getId(),$cadena);
    }
    return $cadena;
}

if($excel)
{
        //ASIGNACION DE FORMATOS DE HEADER
        $objSheet->getStyle('A1')->applyFromArray($style_header);
        $objSheet->getStyle('B1')->applyFromArray($style_header);
        $objSheet->getStyle('C1')->applyFromArray($style_header);
        $objSheet->getStyle('D1')->applyFromArray($style_header);
        $objSheet->getStyle('E1')->applyFromArray($style_header);
        $objSheet->getStyle('F1')->applyFromArray($style_header);
        $objSheet->getStyle('G1')->applyFromArray($style_header);
        $objSheet->getStyle('H1')->applyFromArray($style_header);
        $objSheet->getStyle('I1')->applyFromArray($style_header);
        $objSheet->getStyle('J1')->applyFromArray($style_header);

        //ANCHO DE COLUMNAS
        $objSheet->getColumnDimensionByColumn('0')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('1')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('2')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('3')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('4')->setWidth(15);
        $objSheet->getColumnDimensionByColumn('5')->setWidth(15);
        $objSheet->getColumnDimensionByColumn('6')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('7')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('8')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('9')->setWidth(30);

        //ALINEACION DE HEADER
        $objSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //DATOS PARA HEADER
        $objSheet->getCell('A1')->setValue('Nº de Correspondencia');
        $objSheet->getCell('B1')->setValue('Firman');
        $objSheet->getCell('C1')->setValue('Formato');
        $objSheet->getCell('D1')->setValue('Asunto');
        $objSheet->getCell('E1')->setValue('Para');
        $objSheet->getCell('F1')->setValue('Fecha de Envío');
        $objSheet->getCell('G1')->setValue('Estatus');
        $objSheet->getCell('H1')->setValue('Respuestas');
        $objSheet->getCell('I1')->setValue('Fecha de Creación');
        $objSheet->getCell('J1')->setValue('Hecho por');

    $row_index= 1;
//    $conmutador= 1;
    foreach ($excel as $datos)
    {
        $field1= $datos->getNCorrespondenciaEmisor();

        //Segundo campo firmantes
        $c_firman = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($datos->getId());
        foreach ($c_firman as $list_firman) {
            $field2 = $list_firman->getCtnombre().': ';
            $field2.= ucwords(strtolower($list_firman->getpn())).' '.ucwords(strtolower($list_firman->getpa())).' / ';
        }
        $field2.= '$%&';
        $field2 = str_replace(' / $%&', '', $field2);
        
        //Tercer campo formatos
        //Cuarto campo asunto
        $c_formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($datos->getId());
        
        
        $parametros_correspondencia['privado'] = $datos->getPrivado();

        $accesible = 'S';
        if($datos->getPrivado()=='S'){
            $accesible = 'N';

            if($datos->getIdCreate()==$sf_user->getAttribute('usuario_id')){
                $accesible = 'S';
            } else {
                foreach ($c_firman as $emisor) {
                    if($emisor->getFuncionarioId()==$sf_user->getAttribute('usuario_id') && $accesible == 'N'){
                        $accesible = 'S';
                    }
                }
            }

            if($accesible=='N'){
                $vistos_buenos = Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->funcionarios_vistobueno($datos->getId());
                foreach ($vistos_buenos as $visto_bueno) {
                    if($visto_bueno->getFuncionarioId()==$sf_user->getAttribute('usuario_id') && $accesible == 'N'){
                        $accesible = 'S';
                    }
                }
            }
        }



        foreach ($c_formatos as $list_formatos) {
            $field3 = $list_formatos->getTadnombre() . " / ";
            $field3.= "; ";
            if($accesible=='S') {
                $field4 = $list_formatos->getCampoUno();
            } else {
                $field4 = 'PRIVADO';
            }
            //LIMPIA EL CAMPO DE ETIQUETAS
            $field4= preg_replace("/<p(.*?)>/i","<br/>",$field4);
            $field4= preg_replace("[</p>]","",$field4);
            $field4= preg_replace("/<!--(.*?)-->/","",$field4);
            $html_limpio = new herramientas();
            $field4=$html_limpio->limpiar_metas($field4);
//            $field4= preg_replace("/<p(.*?)>/i","<br/>",$field4);
//            $field4= preg_replace("[</p>]","",$field4);
//            $field4= preg_replace("/<!--(.*?)-->/","",$field4);
//            $field4 = preg_replace("/<br(.*?)\/>/i", "", $field4);
//            $field4 = preg_replace("/<span(.*?)\/>/i", "", $field4);
//            $field4= preg_replace("[</span>]","",$field4);
        }
        $field3 .= '$%&';
        $field3 = str_replace(' / ; $%&', '', $field3);
        
        //Quinto campo para
        $ce_para = Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($datos->getId());
        $field5_externo= '';
        foreach ($ce_para as $list_ce_para) {
            $field5_externo.= "Externo: " . $list_ce_para->getReceptorOrganismoSiglas()." (" . $list_ce_para->getReceptorOrganismo() . ") / ".$list_ce_para->getReceptorPersonaCargo() . ": ".ucwords(strtolower($list_ce_para->getReceptorPersona()));
        }

        $ci_para = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($datos->getId());
        $field5_interno= '';
        foreach ($ci_para as $list_ci_para) {
            $unidad_tipo = Doctrine::getTable('Organigrama_UnidadTipo')->find($list_ci_para->getUnidadTipoId());
            
            $padre_unidad = '';
            if($unidad_tipo->getPrincipal() != TRUE){
                $padre_unidad = ucwords(strtolower($list_ci_para->getPadreUnidad()))." > ";
            }
            $field5_interno.= $padre_unidad.ucwords(strtolower($list_ci_para->getUnombre()))." / ".ucwords(strtolower($list_ci_para->getctnombre())).": ".ucwords(strtolower($list_ci_para->getpn())).' '.ucwords(strtolower($list_ci_para->getpa()))."; ";
        }
        
        $field5_todos= '';
        if ($datos->getStatus() == 'F') {
            $field5_todos.= "TODOS LOS INVOLUCRADOS EN ESTA CORRESPONDENCIA";
        }
        //Sexto campo fecha de envio
        $field6= false !== strtotime($datos->getFEnvio()) ? format_date($datos->getFEnvio(), "f", "es") : 'SIN ENVIAR';

        //Septimo campo estatus
        switch ($datos->getStatus()){
            case 'C':
                $field7= 'CREACION';
                break;
            case 'P':
                $field7= 'PAUSADO';
                break;
            case 'E':
                $field7= 'ENVIADO';
                break;
            case 'L':
                $field7= 'RECIBIDO';
                break;
            case 'A':
                $field7= 'ASIGNADA';
                break;
            case 'F':
                $field7= 'FINALIZADO';
                break;
            default:
                $field7= '';
        }

        $row_index++;
        $objSheet->getCell('A'.$row_index)->setValue($field1);
        $objSheet->getCell('B'.$row_index)->setValue($field2);
        $objSheet->getCell('C'.$row_index)->setValue($field3);
        $objSheet->getCell('D'.$row_index)->setValue($field4);
        $objSheet->getCell('E'.$row_index)->setValue($field5_externo.$field5_interno.$field5_todos);
        $objSheet->getCell('F'.$row_index)->setValue($field6);
        $objSheet->getCell('G'.$row_index)->setValue($field7);
        if($accesible=='S') {
            $objSheet->getCell('H'.$row_index)->setValue(correspondenciaHijos($datos->getId()));
        } else {
            $objSheet->getCell('H'.$row_index)->setValue('PRIVADO');
        }
        $objSheet->getCell('I'.$row_index)->setValue($datos->getCreatedAt());
        $objSheet->getCell('J'.$row_index)->setValue($datos->getUserUpdate());

//        $objSheet->getStyle('F'.$row_index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //Estilos conmutados para datos
//        if($conmutador%2!= 0){
//            $objSheet->getStyle('A'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('B'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('C'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('D'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('E'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('F'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('G'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('H'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('I'.$row_index)->applyFromArray($style_alter);
//        }

//        $row_index++;
//        $objSheet->getCell('A'.$row_index)->setValue();
//        $objSheet->getCell('B'.$row_index)->setValue();
//        $objSheet->getCell('C'.$row_index)->setValue();
//        $objSheet->getCell('D'.$row_index)->setValue();
//        $objSheet->getCell('E'.$row_index)->setValue();
//        $objSheet->getCell('F'.$row_index)->setValue();
//        $objSheet->getCell('G'.$row_index)->setValue();
//        $objSheet->getCell('H'.$row_index)->setValue();
//        $objSheet->getCell('I'.$row_index)->setValue();

//        $conmutador+= 2;
    }
}

    //$objWriter->save('pruebaexcel2.xls');
    $nombre= 'correspondencias_enviadas_al_'.date('d-m-Y');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$nombre.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

?>
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

$style_alter = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'E4E4E4'),
    ),
);

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

        //ANCHO DE COLUMNAS
        $objSheet->getColumnDimensionByColumn('0')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('1')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('2')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('3')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('4')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('5')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('6')->setWidth(15);
        $objSheet->getColumnDimensionByColumn('7')->setWidth(30);

        //ALINEACION DE HEADER
        $objSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //DATOS PARA HEADER
        $objSheet->getCell('A1')->setValue('Fecha de envio');
        $objSheet->getCell('B1')->setValue('N° de envio');
        $objSheet->getCell('C1')->setValue('De');
        $objSheet->getCell('D1')->setValue('Para');
        $objSheet->getCell('E1')->setValue('Adjuntos');
        $objSheet->getCell('F1')->setValue('Fisicos');
        $objSheet->getCell('G1')->setValue('Estatus');
        $objSheet->getCell('H1')->setValue('Asunto');


    $row_index= 1;
//    $conmutador= 1;
    foreach ($excel as $datos)
    {
        //Campo 3 De
        $c_firman = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($datos->getId());
        foreach ($c_firman as $list_firman) {
            $field3 = $list_firman->getCtnombre() . '/ ';
            $field3.= ucwords(strtolower($list_firman->getpn()));
            $field3.= ucwords(strtolower($list_firman->getpa()));
        }
        $field3_externo= '';
        if ($datos->getEmisorOrganismoId()) {
            $field3_externo=  "Externo: ".$datos->getOrganismos_Organismo()->getSiglas().
            " (".$datos->getOrganismos_Organismo().") / ".
            $datos->getOrganismos_PersonaCargo().": ".
            ucwords(strtolower($datos->getOrganismos_Persona()));                    
        }
        
        //Campo 4 Para
        $receptores = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($datos->getId());
        $field4= '';
        foreach ($receptores as $receptor) {
            $unidad_tipo = Doctrine::getTable('Organigrama_UnidadTipo')->find($receptor->getUnidadTipoId());
            
            $padre_unidad = '';
            if($unidad_tipo->getPrincipal() != TRUE){
                $padre_unidad = ucwords(strtolower($receptor->getPadreUnidad()))." > ";
            }
            $field4.= $padre_unidad.ucwords(strtolower($receptor->getUnombre()))." / ".ucwords(strtolower($receptor->getctnombre())).": ".ucwords(strtolower($receptor->getpn())).' '.ucwords(strtolower($receptor->getpa()))."; ";
        }
        
        //CONFIDENCIAL
        $receptores_establecidos = Doctrine::getTable('Correspondencia_Receptor')->findByCorrespondenciaId($datos->getId());
        
        $privado = 'N';
        $accesible = 'N';
        
        foreach ($receptores_establecidos as $receptor_establecido) {
            if($receptor_establecido->getFuncionarioId() == $sf_user->getAttribute('funcionario_id')){
                $accesible = 'S';
            }
            if($receptor_establecido->getPrivado() == 'S'){
                $privado = $receptor_establecido->getPrivado();
            }
        }
        
        if($privado == 'N'){
            $accesible = 'S';
        }
        
        //Campo 5 Adjuntos
        $field5= '';
        if($accesible=='S'){
            $archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($datos->getId());
            foreach ($archivos as $archivo){
                $field5 .= $archivo->getNombreOriginal().' / ';
            }
            if($field5==''){ $field5='~'; } else { $field5 .= '$%&'; $field5 = str_replace(' / $%&', '', $field5); }
        } else {
            $field5 = 'PRIVADO';
        }
        
        //Campo 6 Fisicos
        $field6= '';
        if($accesible=='S'){
            $fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($datos->getId());
            foreach ($fisicos as $fisico){
                $field6 .= $fisico->getObservacion().' / ';
            }
            if($field6==''){ $field6='~'; } else { $field6 .= '$%&'; $field6 = str_replace(' / $%&', '', $field6); }
        } else {
            $field6 = 'PRIVADO';
        }
        
        
        //Campo 7 Status
        switch ($datos->getStatus()){
            case 'E':
                $field7= 'SIN LEER';
                break;
            case 'D':
                $field7= 'DEVUELTA';
                break;
            case 'L':
                $field7= 'LEIDO';
                break;
            default:
                $field7= '';
        }
        
        if($accesible=='S'){
            $c_formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($datos->getId());
            foreach ($c_formatos as $list_formatos) {
                $field8 = $list_formatos->getCampoUno();
                //LIMPIA EL CAMPO DE ETIQUETAS
                $field8 = preg_replace("/<br(.*?)\/>/i", "", $field8);
                $field8 = preg_replace("/<p(.*?)>/i", "", $field8);
                $field8 = preg_replace("/<\/p>/i", "", $field8);

                $field8 = preg_replace("/<strong(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/strong>/", "", $field8);
                $field8 = preg_replace("/<h1(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/h1>/", "", $field8);
                $field8 = preg_replace("/<h2(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/h2>/", "", $field8);
                $field8 = preg_replace("/<h3(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/h3>/", "", $field8);
                $field8 = preg_replace("/<b>/", "", $field8);
                $field8 = preg_replace("/<\/b>/", "", $field8);

                $field8 = preg_replace("/<em(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/em>/", "", $field8);
                $field8 = preg_replace("/<i>/", "", $field8);
                $field8 = preg_replace("/<\/i>/", "", $field8);

                $field8 = preg_replace("/<ul(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/ul>/", "", $field8);
                $field8 = preg_replace("/<li(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/li>/", "", $field8);

                $field8 = preg_replace("/<u(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/u>/", "", $field8);

                $field8 = preg_replace("/<a(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/a>/", "", $field8);

                $field8 = preg_replace("/<colgroup(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/colgroup>/", "", $field8);
                $field8 = preg_replace("/<col (.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/col>/", "", $field8);
                $field8 = preg_replace("/<font(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/font>/", "", $field8);
                $field8 = preg_replace("/<style(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/style>/", "", $field8);
                $field8 = preg_replace("/<tbody(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/tbody>/", "", $field8);
                $field8 = preg_replace("/<span(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/span>/", "", $field8);
                $field8 = preg_replace("/<div(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/div>/", "", $field8);

                $field8 = preg_replace("/<!--(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/-->/", "", $field8);

                $field8 = preg_replace("/<img(.*?)>/", "", $field8);

                $field8 = preg_replace("/<tbody(.*?)>/", "", $field8);
                $field8 = preg_replace("/<\/tbody>/", "", $field8);
                $field8 = preg_replace("/<&nbsp;/", "", $field8);
                $field8= trim($field8);
                $field8= html_entity_decode($field8);
            }
        } else {
            $field8 = 'PRIVADO';
        }
        
        $row_index++;
        $objSheet->getCell('A'.$row_index)->setValue($datos->getFEnvio());
        $objSheet->getCell('B'.$row_index)->setValue($datos->getNCorrespondenciaEmisor());
        $objSheet->getCell('C'.$row_index)->setValue($field3.$field3_externo);
        $objSheet->getCell('D'.$row_index)->setValue($field4);
        $objSheet->getCell('E'.$row_index)->setValue($field5);
        $objSheet->getCell('F'.$row_index)->setValue($field6);
        $objSheet->getCell('G'.$row_index)->setValue($field7);
        $objSheet->getCell('H'.$row_index)->setValue($field8);
        
        //Estilos conmutados para datos
//        if($conmutador%2!= 0){
//            $objSheet->getStyle('A'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('B'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('C'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('D'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('E'.$row_index)->applyFromArray($style_alter);
//            $objSheet->getStyle('F'.$row_index)->applyFromArray($style_alter);
//        }
        
//        $row_index++;
//        $objSheet->getCell('A'.$row_index)->setValue();
//        $objSheet->getCell('B'.$row_index)->setValue();
//        $objSheet->getCell('C'.$row_index)->setValue();
//        $objSheet->getCell('D'.$row_index)->setValue();
//        $objSheet->getCell('E'.$row_index)->setValue();
//        $objSheet->getCell('F'.$row_index)->setValue();
        
//        $conmutador+= 2;
    }
}

    //$objWriter->save('pruebaexcel2.xls');
    $nombre= 'correspondencias_recibidas_al_'.date('d-m-Y');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$nombre.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

?>
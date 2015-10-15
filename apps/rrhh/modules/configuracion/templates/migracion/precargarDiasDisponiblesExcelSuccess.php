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

if($vacaciones)
{
        //ASIGNACION DE FORMATOS DE HEADER
        $objSheet->getStyle('A1')->applyFromArray($style_header);
        $objSheet->getStyle('B1')->applyFromArray($style_header);
        $objSheet->getStyle('C1')->applyFromArray($style_header);

        //ANCHO DE COLUMNAS
        $objSheet->getColumnDimensionByColumn('0')->setWidth(10);
        $objSheet->getColumnDimensionByColumn('1')->setWidth(20);
        $objSheet->getColumnDimensionByColumn('2')->setWidth(20);

        //ALINEACION DE HEADER
        $objSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //DATOS PARA HEADER
        $objSheet->getCell('A1')->setValue('Cédula');
        $objSheet->getCell('B1')->setValue('Período Vacacional');
        $objSheet->getCell('C1')->setValue('Días Disponibles');

    $row_index= 1;
//    $conmutador= 1;
    foreach ($vacaciones as $vacacion)
    {
        $field1 = $cedulas[$vacacion->getFuncionarioId()];
        $field2 = $vacacion->getPeriodoVacacional();
        $field3 = $vacacion->getDiasDisfrutePendientes();

        $row_index++;
        $objSheet->getCell('A'.$row_index)->setValue($field1);
        $objSheet->getCell('B'.$row_index)->setValue($field2);
        $objSheet->getCell('C'.$row_index)->setValue($field3);
    }
}

    //$objWriter->save('pruebaexcel2.xls');
    $nombre= 'dias_disponibles_precargados_periodos_vacacionales_'.date('d-m-Y');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$nombre.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

?>
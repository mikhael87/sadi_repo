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

if($rutas)
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
        $objSheet->getColumnDimensionByColumn('0')->setWidth(10);
        $objSheet->getColumnDimensionByColumn('1')->setWidth(10);
        $objSheet->getColumnDimensionByColumn('2')->setWidth(40);
        $objSheet->getColumnDimensionByColumn('3')->setWidth(12);
        $objSheet->getColumnDimensionByColumn('4')->setWidth(15);
        $objSheet->getColumnDimensionByColumn('5')->setWidth(15);
        $objSheet->getColumnDimensionByColumn('6')->setWidth(15);
        $objSheet->getColumnDimensionByColumn('7')->setWidth(15);

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
        $objSheet->getCell('A1')->setValue('MARCA');
        $objSheet->getCell('B1')->setValue('MODELO');
        $objSheet->getCell('C1')->setValue('ALERTA');
        $objSheet->getCell('D1')->setValue('FECHA');
        $objSheet->getCell('E1')->setValue('HORA');
        $objSheet->getCell('F1')->setValue('LATITUD');
        $objSheet->getCell('G1')->setValue('LONGITUD');
        $objSheet->getCell('H1')->setValue('VELOCIDAD');

    $row_index= 2;
    foreach ($rutas as $datos)
    {
        //COMANDOS
        $comando= '';
        $pc_com= $datos->getComando();
        if($pc_com == 'no gps') {
            $comando= 'Sin conexiónn GPS';
        }elseif($pc_com == 'move') {
            $comando= 'En vehículo se ha movido';
        }elseif($pc_com == 'lowbattery') {
            $comando = 'Bateria interna baja';
        }elseif($pc_com == 'extpower') {
            $comando = 'Bateria desconectada';
        }elseif($pc_com == 'ACC off') {
            $comando = 'Veh&iacute;culo apagado';
        }elseif($pc_com == 'ACC on') {
            $comando = 'Vehículo encendido';
        }elseif(preg_match("/speed/", $pc_com)) {
            $vel= str_replace('speed', '', $pc_com);
            $comando = 'Exceso de velocidad ('.$vel.' Km/h establecidos)';
        }else {
            $comando= 'Alerta no identifica';
        }
        
        $objSheet->getCell('A'.$row_index)->setValue($marca);
        $objSheet->getCell('B'.$row_index)->setValue($modelo);
        $objSheet->getCell('C'.$row_index)->setValue($comando);
        $objSheet->getCell('D'.$row_index)->setValue(date('d-m-Y', strtotime($datos->getFechaGps())));
        $objSheet->getCell('E'.$row_index)->setValue(((date('H', strtotime($datos->getFechaGps())) >= 12)? ((date('H', strtotime($datos->getFechaGps())) == 12)? '12' : date('H', strtotime($datos->getFechaGps()))-12).date(':i:s', strtotime($datos->getFechaGps())).' PM' : date('H:i:s', strtotime($datos->getFechaGps())).' AM'));
        $objSheet->getCell('F'.$row_index)->setValue($datos->getLatitud());
        $objSheet->getCell('G'.$row_index)->setValue($datos->getLongitud());
        $objSheet->getCell('H'.$row_index)->setValue($datos->getVelocidad().' Km/h');
        
        $objSheet->getStyle('G'.$row_index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objSheet->getStyle('H'.$row_index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $row_index++;
    }
}

    //$objWriter->save('pruebaexcel2.xls');
    $nombre= 'Historico_de_recorido_'.date('Y-m-d', strtotime($desde));
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$nombre.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

?>

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

        //ANCHO DE COLUMNAS
        $objSheet->getColumnDimensionByColumn('0')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('1')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('2')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('3')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('4')->setWidth(15);
        $objSheet->getColumnDimensionByColumn('5')->setWidth(20);
        $objSheet->getColumnDimensionByColumn('6')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('7')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('8')->setWidth(25);
        $objSheet->getColumnDimensionByColumn('9')->setWidth(25);

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

        //DATOS PARA HEADER
        $objSheet->getCell('A1')->setValue('Nº de Expediente');
        $objSheet->getCell('B1')->setValue('Serie documental');
        $objSheet->getCell('C1')->setValue('Ubicacion');
        $objSheet->getCell('D1')->setValue('Estante');
        $objSheet->getCell('E1')->setValue('Tramo');
        $objSheet->getCell('F1')->setValue('Cant. de Documentos');
        $objSheet->getCell('G1')->setValue('Disponibilidad');
        $objSheet->getCell('H1')->setValue('Archivado por');
        $objSheet->getCell('I1')->setValue('Fecha de Creacion');

    $row_index= 1;
//    $conmutador= 1;
    foreach ($excel as $datos)
    {
        //Primer campo n° de expediente
        $field1= $datos->getCorrelativo();

        //Segundo campo serie documental
        $field2 = $datos->getArchivo_SerieDocumental()->getNombre();

        $c_unidad_correlativos_id = Doctrine::getTable('Archivo_UnidadCorrelativos')->find($datos->getUnidadCorrelativosId());
        
        //Tercer campo ubicacion
        $c_unidad = Doctrine::getTable('Organigrama_Unidad')->find($c_unidad_correlativos_id->getUnidadId());
        $field3= $c_unidad->getNombre();
        
        //Cuarto campo estante
        $c_estante = Doctrine::getTable('Archivo_Estante')->find($datos->getEstanteId());
        $field4= $c_estante->getIdentificador();
        
        //Quinto campo tramo
        $field5= $datos->getTramo();

        //Sexto campo cantidad de documentos
        $field6= Doctrine_Query::create()
            ->select('d.id as ids')
            ->from('Archivo_Documento d')
            ->where('d.expediente_id = ?', $datos->getId())
            ->execute();

        $field6=  count($field6);
        
        //Septimo campo disponibilidad
        $prestamos_activos = Doctrine::getTable('Archivo_PrestamoArchivo')->prestamosActivos($datos->getId());
        if(count($prestamos_activos)> 0)
            $field7= 'Prestado';
        else
            $field7= 'Disponible';
        
        //Octavo campo archivado por
        $field8= $datos->getUserUpdate();
        
        //Noveno campo fecha de creacion
        $field9= date('d-m-Y h:i:s A', strtotime($datos->getCreatedAt()));
        
        $row_index++;
        $objSheet->getCell('A'.$row_index)->setValue($field1);
        $objSheet->getCell('B'.$row_index)->setValue($field2);
        $objSheet->getCell('C'.$row_index)->setValue($field3);
        $objSheet->getCell('D'.$row_index)->setValue($field4);
        $objSheet->getCell('E'.$row_index)->setValue($field5);
        $objSheet->getCell('F'.$row_index)->setValue($field6);
        $objSheet->getCell('G'.$row_index)->setValue($field7);
        $objSheet->getCell('H'.$row_index)->setValue($field8);
        $objSheet->getCell('I'.$row_index)->setValue($field9);

        $objSheet->getStyle('F'.$row_index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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

        $row_index++;
        $objSheet->getCell('A'.$row_index)->setValue();
        $objSheet->getCell('B'.$row_index)->setValue();
        $objSheet->getCell('C'.$row_index)->setValue();
        $objSheet->getCell('D'.$row_index)->setValue();
        $objSheet->getCell('E'.$row_index)->setValue();
        $objSheet->getCell('F'.$row_index)->setValue();
        $objSheet->getCell('G'.$row_index)->setValue();
        $objSheet->getCell('H'.$row_index)->setValue();
        $objSheet->getCell('I'.$row_index)->setValue();

//        $conmutador+= 2;
    }
}

    //$objWriter->save('pruebaexcel2.xls');
    $nombre= 'expedientes_archivados_al_'.date('d-m-Y');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$nombre.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

?>
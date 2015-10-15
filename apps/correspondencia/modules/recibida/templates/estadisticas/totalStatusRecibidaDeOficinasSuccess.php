<?php 
 $totales = '';
 $sin_leer = '';
 $leidas = '';
 $devueltas = '';
 $nombre = '';
 $nombreOficina = '';
 
    foreach ($estadistica_datos as $estadistica_dato){
        $nombre .=  "'".$estadistica_dato['unidad_siglas']."',";
        $nombreOficina .=  "'".$estadistica_dato['unidad_nombre']."',";
        $totales .= $estadistica_dato['total'].",";
        $sin_leer .= $estadistica_dato['sin_leer'].",";
        $leidas .= $estadistica_dato['leidas'].",";
        $devueltas .= $estadistica_dato['devueltas'].",";
    }
$unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidad_id);
?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        
    Highcharts.setOptions({
	lang: {
		loading: 'Cargando...',
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
			'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                shortMonths: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Nov','Dic'],
		weekdays: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                exportButtonTitle: "Exportar",
                printButtonTitle: "Imprimir",
                rangeSelectorFrom: "De",
                rangeSelectorTo: "Hasta",
                rangeSelectorZoom: "Periodo",
                downloadPNG: 'Descargar imagen PNG',
                downloadJPEG: 'Descargar imagen JPEG',
                downloadPDF: 'Descargar documento PDF',
                downloadSVG: 'Descargar imagen SVG',
                resetZoom: 'Restablecer',
                resetZoomTitle: 'Restablecer',
                printButtonTitle: 'Imprimir'
	}
        });
        
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'containerBars',
                type: 'column'
            },
            colors: [
                '#D0D0D0',
                '#2E9AFE',
                '#FFD700'
             ],
             credits: {
                enabled: true,
                position: {
                            align: 'right',
                            x: -10,
                            verticalAlign: 'bottom',
                            y: -5
                    },
               text: "<?php echo "Generado el dia: ".date('d/m/Y h:i:s a') ?> / SIGLAS - <?php echo sfConfig::get('sf_siglas'); ?>"
            },
            title: {
                text: 'Total de correspondendia recibida de oficinas'
            },
            subtitle: {
                text: '<?php echo $unidad->getNombre()."<br/>".$fecha; ?>'
            },
            xAxis: {
                categories: [<?php echo $nombre; ?>],
                labels: {
                    rotation: -45,
                    align: 'right',
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Correspondencia'
                }, 
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                layout: 'vertical',
                backgroundColor: '#FFFFFF',
                align: 'right',
                verticalAlign: 'top',
                x: 0,
                y: 70,
                shadow: true
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                    }
                }
            },
                series: [
                    <?php
                        echo "{ name: 'Sin Leer', data: [$sin_leer] },\n";
                        echo "{ name: 'Leidas', data: [$leidas] },\n";
                        echo "{ name: 'Devueltas', data: [$devueltas] },\n";
                ?>]
        },
        function(chart){
        chart.renderer.image('/images/other/logo_siglas_watermark_small.png',0,0,89,89).add();
     });
    });
    
});
		</script>
<div id="containerBars" class="highchartsContainer"></div>
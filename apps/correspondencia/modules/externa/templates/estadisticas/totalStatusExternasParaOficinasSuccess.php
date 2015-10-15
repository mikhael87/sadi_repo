<?php 
 $totales = '';
 $enviadas = '';
 $entregadas = '';
 $devueltas = '';
 $nombre = '';
 
    foreach ($estadistica_datos as $estadistica_dato){
        $nombre .=  "'".$estadistica_dato['unidad_siglas']."',";
        $totales .= $estadistica_dato['total'].",";
        $enviadas .= $estadistica_dato['enviadas'].",";
        $entregadas .= $estadistica_dato['entregadas'].",";
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
                '#04B404', 
                '#2E9AFE', 
                '#FFFF33', 
                '#B40404', 
                '#3D96AE'
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
                text: 'Total de correspondencia externa para oficinas'
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
                        echo "{ name: 'Enviadas', data: [$enviadas] },\n";
                        echo "{ name: 'Entregadas', data: [$entregadas] },\n";
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
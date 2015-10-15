<?php $nombre = '';

    $chart["Recibida y enviada"] = 0;
    $chart["Devuelta por la oficina"] = 0;
    $chart["Recibida y entregada"] = 0;

            foreach($estadistica_datos as $key => $value)
            {
                if($key == 'E') { $chart["Recibida y enviada"] += $value; } 
                elseif($key == 'D') { $chart["Devuelta por la oficina"] += $value; } 
                elseif($key == 'L') { $chart["Recibida y entregada"] += $value; } 
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
        
		// Build the chart
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'containerPie',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            colors: [
                '#04B404', 
                '#FFD700', 
                '#2E9AFE', 
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
               text: "<?php echo "Generado el dia: ".date('d/m/Y h:i:s a') ?> / SIGLAS <?php echo sfConfig::get('sf_siglas'); ?>"
            },
            title: {
                text: 'Total de correspondencia externa por estatus'
            },
            subtitle: {
                text: '<?php echo $unidad->getNombre()."<br/>".$fecha; ?>'
            },
            tooltip: {
        	    pointFormat: '<b>{point.percentage}%</b>',
            	percentageDecimals: 1
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.y +'';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: ' ',
                data: [<?php foreach($chart as $key => $value){echo "['$key', $value],";}?>]
            }]
        },
        function(chart){
        chart.renderer.image('/images/other/logo_siglas_watermark_small.png',0,0,89,89).add();
     });
    });
});
</script>
<div id="containerPie" class="highchartsContainer"></div>
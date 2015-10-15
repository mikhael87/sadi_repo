<?php 
$totalGlobal = 0;
foreach ($estadistica_datos as $estadistica_dato){
    $totalGlobal += $estadistica_dato['total'];
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
            title: {
                text: 'Total de correspondencia enviada por creador'
            },
            subtitle: {
                text: '<?php echo $unidad->getNombre()."<br/>".$fecha; ?>'
            },
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
            xAxis: {
                categories: ['Enviadas']
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Correspondencia'
                },
                labels: {
                    formatter: function() {
                        return this.value;
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
                floating: true,
                shadow: true
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.series.name+', '+this.x +': '+ this.y +'';
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [
                <?php 
                foreach ($estadistica_datos as $estadistica_dato){
                $name = $estadistica_dato['nombre'];
                $data = $estadistica_dato['total'];
                echo "{ name: '$name', data: [$data] },\n";
            }?>]
        },
        function(chart){
        chart.renderer.image('/images/other/logo_siglas_watermark_small.png',0,0,89,89).add();
     });
             
    });
    
});
		</script>
                <div id="containerBars" class="highchartsContainer"></div>
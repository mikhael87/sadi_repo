<?php 
$totalGlobal = 0;
foreach ($motivos as $nombre => $total){
    $totalGlobal += $total;
}
?>

<div style="position: relative;">
    <div id="sf_admin_content">
        <script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Porcentajes de vivistas por motivos'
            },
            subtitle: {
                text: 'cantidad total: <?php echo $totalGlobal; ?>'
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
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.y}</b>',
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
                            return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 2, ',') +' %';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Motivos',
                data: [
                    <?php  foreach ($motivos as $nombre => $total) { 
                        echo "['$nombre',   $total],";
                        } ?>
                ]
            }]
        },
        function(chart){
        chart.renderer.image('/images/other/logo_siglas_watermark_small.png',0,0,89,89).add();
     });
    });
    
});
		</script>
<div id="container" class="highchartsContainer"></div>
        
        <fieldset><h2>MOTIVOS</h2></fieldset>
        <br/>

        <table style="width: 100%;">

            <tr class="sf_admin_row">
                <th>Motivos</th>
                <th>Total de visitantes</th>
            </tr>

            <?php  foreach ($motivos as $nombre => $total) { ?>
            <tr class="sf_admin_row">
                <td><?php echo $nombre; ?> </td>
                <td><?php echo $total; ?> </td>
            </tr>
            <?php } ?>
      </table>

    </div>


    <div id="sf_admin_footer"> </div>

</div>
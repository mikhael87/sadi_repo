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
                text: 'Uso de sistemas operativos'
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
                name: 'Sistemas Operativos',
                data: [
                    <?php  foreach ($so as $nombre => $total) { 
                        echo "['$nombre',   $total],";
                        } ?>
                ]
            }]
        });
    });
    
});
		</script>
<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
        
        <fieldset><h2>TECNOLOGIAS</h2></fieldset>
        <br/>

        <table style="width: 100%;">

            <tr class="sf_admin_row">
                <th>Sistemas Operativos</th>
                <th>Total de computadores</th>
            </tr>

            <?php  foreach ($so as $nombre => $total) { ?>
            <tr class="sf_admin_row">
                <td><?php echo $nombre; ?> </td>
                <td><?php echo $total; ?> </td>
            </tr>
            <?php } ?>
      </table>

    </div>


    <div id="sf_admin_footer"> </div>

</div>
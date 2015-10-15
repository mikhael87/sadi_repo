<?php
$cn = '';
$cv = '';
$ct = 0;

$fn = '';
$fv = '';
$ft = 0;

$in = '';
$iv = '';
$it = 0;

$on = '';
$ov = '';
$ot = 0;

$sn = '';
$sv = '';
$st = 0;

foreach($browser as $nombre => $valor)
{
    if(stripos($nombre, "Chrome") !==FALSE)
    {
        $cn .= "'".$nombre."',";
        $cv .= $valor.",";
        $ct += $valor;
    }
    elseif(stripos($nombre, "Firefox") !==FALSE)
    {
        $fn .= "'".$nombre."',";
        $fv .= $valor.",";
        $ft += $valor;
    }
    elseif(stripos($nombre, "Internet") !==FALSE)
    {
        $in .= "'".$nombre."',";
        $iv .= $valor.",";
        $it += $valor;
    }
    elseif(stripos($nombre, "Opera") !==FALSE)
    {
        $on .= "'".$nombre."',";
        $ov .= $valor.",";
        $ot += $valor;


    }
    elseif(stripos($nombre, "Safari") !==FALSE)
    {
        $sn .= "'".$nombre."',";
        $st += $valor;
    }
}

$i = 0;

use_helper('jQuery'); ?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
    
        var colors = Highcharts.getOptions().colors,
            categories = ['Internet Explorer', 'Firefox', 'Chrome', 'Safari', 'Opera'],
            name = 'Navegadores',
            data = [{
                    y: <?php echo $it; ?>,
                    color: colors[0],
                    drilldown: {
                        name: 'IE',
                        categories: [<?php echo $in; ?>],
                        data: [<?php echo $iv; ?>],
                        color: colors[0]
                    }
                }, {
                    y: <?php echo $ft; ?>,
                    color: colors[1],
                    drilldown: {
                        name: 'Firefox versions',
                        categories: [<?php echo $fn; ?>],
                        data: [<?php echo $fv; ?>],
                        color: colors[1]
                    }
                }, {
                    y: <?php echo $ct; ?>,
                    color: colors[2],
                    drilldown: {
                        name: 'Chrome versions',
                        categories: [<?php echo $cn; ?>],
                        data: [<?php echo $cv; ?>],
                        color: colors[2]
                    }
                }, {
                    y: <?php echo $st; ?>,
                    color: colors[3],
                    drilldown: {
                        name: 'Safari versions',
                        categories: [<?php echo $sn; ?>],
                        data: [<?php echo $sv; ?>],
                        color: colors[3]
                    }
                }, {
                    y: <?php echo $ot; ?>,
                    color: colors[4],
                    drilldown: {
                        name: 'Opera versions',
                        categories: [<?php echo $on; ?>],
                        data: [<?php echo $ov; ?>],
                        color: colors[4]
                    }
                }];
    
    
        // Build the data arrays
        var browserData = [];
        var versionsData = [];
        for (var i = 0; i < data.length; i++) {
    
            // add browser data
            browserData.push({
                name: categories[i],
                y: data[i].y,
                color: data[i].color
            });
    
            // add version data
            for (var j = 0; j < data[i].drilldown.data.length; j++) {
                var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
                versionsData.push({
                    name: data[i].drilldown.categories[j],
                    y: data[i].drilldown.data[j],
                    color: Highcharts.Color(data[i].color).brighten(brightness).get()
                });
            }
        }
    
        // Create the chart
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'pie'
            },
            title: {
                text: 'Navegadores utilizados en SIGLAS'
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
            yAxis: {
                title: {
                    text: 'Porcentaje total'
                }
            },
            plotOptions: {
                pie: {
                    shadow: false
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.point.name +': '+ Highcharts.numberFormat(this.percentage, 2, ',')+'%';
                }
            },
            series: [{
                name: 'Navegadores',
                data: browserData,
                size: '60%',
                dataLabels: {
                    formatter: function() {
                        return this.point.name;
                    },
                    color: 'white',
                    distance: -30
                }
            }, {
                name: 'Versiones',
                data: versionsData,
                innerSize: '60%',
                dataLabels: {
                    formatter: function() {
                        // display only if larger than 1
                        return this.percentage > 1 ? '<b>'+ this.point.name +':</b> '+ Highcharts.numberFormat(this.percentage, 2, ',') +'%'  : null;
                    }
                }
            }]
        });
    });
    
});

function mostrarDatos(id)
{
   $("#datosPersonasNavegadores_"+id).slideToggle("slow");
}
</script>

<div id="sf_admin_container">
    
<h1>Reporte de uso del SIGLAS</h1>
<div style="position: relative;">
<!--<div id="sf_admin_header"> </div>-->

<div id="sf_admin_content">
    
    <div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
    <br/>
    <fieldset><h2>TECNOLOGIAS</h2></fieldset>
    <br/>
    
    <table style="width: 100%;">
        <tr class="sf_admin_row">
            <th>Navegadores</th>
            <th>Total de computadores</th>
            <th>Personas</th>
        </tr>
        
        <?php  foreach ($browserUpdate as $nombre => $total) { ?>
        <tr class="sf_admin_row">
            <td style="width: 339px;"><?php echo $nombre; ?> </td>
            <td style="width: 180px;"><?php 
                if(preg_match("/Actualizar/", $nombre)){
                    echo '<fond class="rojo">'.$total.'</fond>';  
            } else { echo $total; } 
            ?>
            </td>
            <td id="verPersonasNavegadores" style="max-width: 600px;">
                <a onclick="<?php echo "mostrarDatos($i)"; ?>" style="cursor: pointer;" title="Ver Personas">&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <div id="<?php echo "datosPersonasNavegadores_".$i; ?>" style="display: none; max-width: 600px;">
                    <?php
                    
                    foreach($datos[$nombre] as $key => $value)
                    {
                        $dato = str_replace(";", "<br/>", $value);
                        $dato = str_replace("-", "<hr/>", $dato);
                    
                        echo $dato;
                    }
                    $i++;
                 ?>
                </div>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<div id="sf_admin_footer"> </div>
</div>
<?php
$egresos = '';
$ingresos = '';
$meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
//echo "<pre>";
//print_r($estadistica_datos);
//echo "</pre>";

if(count($estadistica_datos)>1){
foreach($estadistica_datos as $clave => $valor)
{
    foreach($valor as $key => $value)
    {
        if($key == 'nombre') $nombre = $value;
        if($clave != 'datos')
        {
            $numMes = explode("-",$key);
            $numMes = $numMes[1];
            $categorias[$key] = $meses[$numMes-1];
        }
    }
}
ksort($categorias);
foreach($categorias as $key => $value)
{
   foreach($estadistica_datos as $clave => $valor)
   {
       if($clave == 'egresos' || $clave == 'ingresos')
       {
            if(isset($valor[$key]))
            {
                if($clave == 'egresos'){ $egresos[$key] = $valor[$key]; }
                elseif($clave == 'ingresos'){ $ingresos[$key] = $valor[$key]; }
            }
            else
            {
                if($clave == 'egresos'){ $egresos[$key] = 0; }
                elseif($clave == 'ingresos'){ $ingresos[$key] = 0; } 
            }
       }
   }
}
?>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'containerLiner',
                type: 'line',
                marginRight: 130,
                marginBottom: 25
            },
            title: {
                text: 'Grafico de ingresos y egresos para el articulo: <?php echo $nombre; ?>',
                x: -20 //center
            },
            xAxis: {
                categories: [<?php
                foreach($categorias as $categoria){ echo "'".$categoria."',"; }
                ?>]
            },
            yAxis: {
                title: {
                    text: 'Cantidad'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y +'';
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [<?php
                        if($ingresos != '') { 
                            echo "{ name: 'Ingresos', data: [";
                        foreach($ingresos as $key => $ingreso){ echo $ingreso.","; }
                        echo "] },";
                        }
                        if($egresos != '') { 
                            echo "{ name: 'Egresos', data: [";
                        foreach($egresos as $key => $egreso){ echo $egreso.","; }
                        echo "] },";
                        }
                        ?>]
        });
    });
    
});
		</script>
	</head>
	<body>

<div id="containerLiner" class="highchartsContainer"></div>
<?php } else { echo "No existen entradas para el articulo seleccionado, por favor eliga otro e intente de nuevo"; } ?>
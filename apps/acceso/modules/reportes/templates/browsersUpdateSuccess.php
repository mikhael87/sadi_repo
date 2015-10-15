<?php use_helper('jQuery'); 
$i = 0;
?>

<script type="text/javascript">
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
    
    <fieldset><h2>TECNOLOGIAS</h2></fieldset>
    <br/>
    
    <table style="width: 100%;">
        <tr class="sf_admin_row">
            <th>Navegadores</th>
            <th>Total de computadores</th>
            <th>Personas</th>
        </tr>
        
        <?php  foreach ($browser as $nombre => $total) { ?>
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
                    $dato = str_replace(";", "<br/>", $datos[$nombre]);
                    $dato = str_replace("-", "<hr/>", $dato);
                    
                    echo $dato;
                    $i++;
                 ?>
                </div>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<pre>
<?php // print_r($datos); ?>
</pre>
<div id="sf_admin_footer"> </div>
</div>
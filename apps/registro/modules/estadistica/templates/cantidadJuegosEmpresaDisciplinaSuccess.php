<script type="text/javascript" src="/js/jqueryTooltip.js"></script>
<style>
    .thx
    {
        background-color: grey;
        color: black;
        /*text-align: top;*/
        vertical-align: bottom !important;
        height: 160px;
        padding-bottom: 3px;
        padding-left: 5px;
        padding-right: 5px;
    }

    .verticalText
    {
        text-align: center;
        vertical-align: middle;
        font-size: 10px;
        width: 20px;
        margin: 0px;
        padding: 0px;
        padding-left: 3px;
        padding-right: 3px;
        padding-top: 10px;
        white-space: nowrap;
        -webkit-transform: rotate(-90deg); 
        -moz-transform: rotate(-90deg);                 
    };
</style>
<?php
//echo '   sdfasdfasdfasdfasd '.count($estadistica_datos);
//echo $estadistica_datos[0]['datos'];
//echo '<pre>';
//print_r($estadistica_datos); echo '</pre>';

//echo print_r($estadistica_datos[2]['datos']);
?>
<table><tr><th></th>
<?php

for($i= 0; $i< 1; $i++) {
    for($j= 0; $j< count($estadistica_datos[$i]['datos']); $j++) {
        echo '<th class="thx"><div class="verticalText">'.$estadistica_datos[$i]['datos'][$j]['variante'].'</div></th>';
    }
}


?></tr><?php

for($i= 0; $i< count($estadistica_datos); $i++) {
    $largo= strlen($estadistica_datos[$i]['nombre']);
    $parte= $estadistica_datos[$i]['nombre'];
    if($largo > 30) {
        $parte= utf8_encode(substr(utf8_decode($parte), 0, 30));
    }
    
    echo '<tr><th><font style="font-size: 10px">'.strtoupper($estadistica_datos[$i]['siglas']).'<br/><font '.(($largo != strlen($parte))? 'class="tooltip" title="[!]'.$estadistica_datos[$i]['nombre'].'[/!]"':'').' style="font-size: 9px; color: #666">'.(($largo != strlen($parte))? $parte.'...': $parte).'</font></th>';
    
    for($j= 0; $j< count($estadistica_datos[$i]['datos']); $j++) {
        echo '<td><font style="color: green">'.$estadistica_datos[$i]['datos'][$j]['ganados'].'</font> <font style="color: red">'.$estadistica_datos[$i]['datos'][$j]['perdidos'].'</font></td>';
    }
    echo '</tr>';
}



?>
</table>
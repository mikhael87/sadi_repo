<?php use_helper('jQuery'); ?>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js"></script>

<style>
    #stop_div {
        overflow-y: hidden
    }
    #point_div {
        overflow-y: hidden
    }
</style>

<script type="text/javascript">
  var map2;
  $(document).ready(function(){
    map2 = new GMaps({
      div: '#map_alert',
      lat: 10.490850150899991,
      lng: -66.8789291381836,
//      lat: -12.044012922866312,
//      lng: -77.02470665341184,
      zoom: 12,
      zoomControl : true,
      zoomControlOpt: {
          style : 'SMALL',
          position: 'TOP_RIGHT'
      },
      panControl : true,
      streetViewControl : true,
      mapTypeControl: true
    });
  });
  
  function active_button(act) {
      var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>/images/icon/';
      if(act=== 'ON')
        $('#generar_reporte_img').attr('src', icon+'ok.png');
      else
        $('#generar_reporte_img').attr('src', icon+'ok_wait.png');
  }
  
  function generate_report(){
      $('#listado').hide();
      var f_ini= 'FALSE';
      if($('#rango_fecha_inicio_day').val() !== '' || $('#rango_fecha_inicio_month').val() !== '' || $('#rango_fecha_inicio_year').val() !== '') {
          f_ini= $('#rango_fecha_inicio_year').val()+'-'+$('#rango_fecha_inicio_month').val()+'-'+$('#rango_fecha_inicio_day').val();
          
          if($('#rango_tiempo_inicio_hour').val() === '')
              f_ini = f_ini+' 00:00';
          else {
              f_ini = f_ini+' '+$('#rango_tiempo_inicio_hour').val();
              f_ini = (($('#rango_tiempo_inicio_minute').val() === '')? f_ini+':00' : f_ini+':'+$('#rango_tiempo_inicio_minute').val());
                  
          }
      }
      var f_fin= 'FALSE';
      if($('#rango_fecha_fin_day').val() !== '' || $('#rango_fecha_fin_month').val() !== '' || $('#rango_fecha_fin_year').val() !== '') {
          f_fin= $('#rango_fecha_fin_year').val()+'-'+$('#rango_fecha_fin_month').val()+'-'+$('#rango_fecha_fin_day').val();
          
          if($('#rango_tiempo_fin_hour').val() === '')
              f_fin = f_fin+' 00:00';
          else {
              f_fin = f_fin+' '+$('#rango_tiempo_fin_hour').val();
              f_fin = (($('#rango_tiempo_fin_minute').val() === '')? f_fin+':00' : f_fin+':'+$('#rango_tiempo_fin_minute').val());
                  
          }
      }
      
      if(f_ini !== 'FALSE') {
          $.ajax({
                url:'tracker/alertas',
                type:'POST',
                dataType:'json',
                data: 'f_ini='+f_ini+'&f_fin='+f_fin,
                success:function(json, textStatus){
                    map2.removeMarkers();
                    //PUNTOS
                    if(json.coor_puntos !== '') {
                        $.each(json.coor_puntos,function(key,value){
                            map2.addMarker({
                                lat: value.latitud,
                                lng: value.longitud,
                                title: value.hora,
                                infoWindow: {
                                  content: '<div id="point_div"><font style="font-size: 13px; font-weight: bold">'+value.comando+'</font><hr/><font style="font-size: 12px; font-weight: bold">'+value.marca+'</font><font style="font-size: 14px; color: #666">'+value.modelo+'</font><br/><b>Fecha: </b><font style="font-size: 10px">'+value.fecha+'</font><br/><b>Hora: </b><font style="font-size: 10px">'+value.hora+'</font></div>'
                                }
                              });
                              
                            //LLENA TABLA PARA LISTADO
                            $('#listado_table').append('<tr><td><font style="font-size: 12px; font-weight: bold">'+value.marca+'</font><font style="font-size: 14px; color: #666">'+value.modelo+'</font></td><td>'+value.comando+'</td><td>'+value.fecha+'</td><td>'+value.hora+'</td><td>'+value.velocidad+'</td></tr>');
                        });
                    }
                }});
      }else {
            alert('Verifique los rangos de fechas');
      }
  }
  
  function exportar(act) {
        var f_ini= 'FALSE';
        if($('#rango_fecha_inicio_day').val() !== '' || $('#rango_fecha_inicio_month').val() !== '' || $('#rango_fecha_inicio_year').val() !== '') {
            f_ini= $('#rango_fecha_inicio_year').val()+'-'+$('#rango_fecha_inicio_month').val()+'-'+$('#rango_fecha_inicio_day').val();

            if($('#rango_tiempo_inicio_hour').val() === '')
                f_ini = f_ini+' 00:00';
            else {
                f_ini = f_ini+' '+$('#rango_tiempo_inicio_hour').val();
                f_ini = (($('#rango_tiempo_inicio_minute').val() === '')? f_ini+':00' : f_ini+':'+$('#rango_tiempo_inicio_minute').val());

            }
        }
        var f_fin= 'FALSE';
        if($('#rango_fecha_fin_day').val() !== '' || $('#rango_fecha_fin_month').val() !== '' || $('#rango_fecha_fin_year').val() !== '') {
            f_fin= $('#rango_fecha_fin_year').val()+'-'+$('#rango_fecha_fin_month').val()+'-'+$('#rango_fecha_fin_day').val();

            if($('#rango_tiempo_fin_hour').val() === '')
                f_fin = f_fin+' 00:00';
            else {
                f_fin = f_fin+' '+$('#rango_tiempo_fin_hour').val();
                f_fin = (($('#rango_tiempo_fin_minute').val() === '')? f_fin+':00' : f_fin+':'+$('#rango_tiempo_fin_minute').val());

            }
        }
        
        if(act === 'excel')
            window.location='<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>tracker/exportarAlertasExcel?f_ini='+f_ini+'&f_fin='+f_fin;
        else {
            if(act === 'listado') {
                if ($('#listado').is(':hidden'))
                    $('#listado').show();
                else
                    $('#listado').hide();
            }
            //AGREGAR MAS ACCIONES
        }
  }
</script>

<style>
    #map_alert{
        display: block;
        width: 90%;
        height: 520px;
        margin: 1em 1em;
        -moz-box-shadow: 0px 5px 20px #ccc;
        -webkit-box-shadow: 0px 5px 20px #ccc;
        box-shadow: 0px 5px 20px #ccc;
    }
    
    #actions_div{
        position: relative;
        text-align: right;
        height: 30px;
        width: 760px
    }
    
    #table_map_op{
        width: 800px
    }
    
    #action_gps_exportar_excel {
        padding-left: 17px;
        background-image: url('../images/icon/page_calc.png');
        background-repeat: no-repeat
    }
</style>

<div id="header_map">
    <h1>Hist&oacute;rico de Alertas</h1>

    <table id="table_map_op">
        <tr>
            <td style="width: 60px"><label>Desde:</label></td>
            <td colspan="2">
                <?php
                $ayer = date('Y-m-d', strtotime('-1 day ' . date('Y-m-d')));
                $years = range(date('Y')-1, date('Y'));

                $w = new sfWidgetFormJQueryDate(array(
                    'image' => '/images/icon/calendar.png',
                    'culture' => 'es',
                    'config' => "{changeYear: true}",
                    'date_widget' => new sfWidgetFormI18nDate(array(
                                    'format' => '%day%-%month%-%year%',
                                    'culture'=>'es',
                                    'empty_values' => array('day'=>'<- Día ->',
                                    'month'=>'<- Mes ->',
                                    'year'=>'<- Año ->'),
                                    'years' => array_combine($years, $years),
                                    ))
                    ),array('name'=>'rango_fecha_inicio', 'value'=> $ayer));

                echo $w->render('rango_fecha_inicio', $ayer);


                $minutes = array();
                for($i = 0; $i < 60; ++$i)
                {
                    $minutes[$i] = sprintf('%02d', $i);
                }
                $t= new sfWidgetFormTime(array(
                    'format_without_seconds' => '%hour%:%minute%',
                    'empty_values' => array('hour'=>' Hora', 'minute'=>' Minutos'),
                    'minutes' => $minutes));
                echo $t->render('rango_tiempo_inicio');
                ?>
            </td>
        </tr>
        <tr>
            <td style="width: 60px"><label>Hasta:</label></td>
            <td colspan="2">
                <?php
                $hoy = date('Y-m-d H:i:s');
                $years = range(date('Y')-1, date('Y'));

                $w = new sfWidgetFormJQueryDate(array(
                    'image' => '/images/icon/calendar.png',
                    'culture' => 'es',
                    'config' => "{changeYear: true}",
                    'date_widget' => new sfWidgetFormI18nDate(array(
                                    'format' => '%day%-%month%-%year%',
                                    'culture'=>'es',
                                    'empty_values' => array('day'=>'<- Día ->',
                                    'month'=>'<- Mes ->',
                                    'year'=>'<- Año ->'),
                                    'years' => array_combine($years, $years),
                                    ))
                    ),array('name'=>'rango_fecha_fin','value'=> $hoy));

                echo $w->render('rango_fecha_fin', $hoy);


                $minutes = array();
                for($i = 0; $i < 60; ++$i)
                {
                    $minutes[$i] = sprintf('%02d', $i);
                }
                $t= new sfWidgetFormTime(array(
                    'format_without_seconds' => '%hour%:%minute%',
                    'empty_values' => array('hour'=>' Hora', 'minute'=>' Minutos'),
                    'minutes' => $minutes));
                echo $t->render('rango_tiempo_fin');
                ?>
            </td>
        </tr>
        <tr>
            <td style="width: 60px"></td>
            <td>
                <button id="generar_reporte_button" onClick="javascript: generate_report();" onMouseover="active_button('ON')" onMouseout="active_button('OFF')" style="height: 30px">
                    <?php echo image_tag('icon/ok_wait.png', array('style' => 'vertical-align: middle', 'id'=>'generar_reporte_img')) ?>&nbsp;<strong>Generar reporte</strong>
                </button>
            </td>
            <td>
                <!--Errors-->
            </td>
        </tr>
    </table>
    <div id="actions_div">
        <table style="width: 100%">
            <tr>
                <td style="width: 50%; text-align: left"><a id="action_gps_exportar_excel" href="javascript: exportar('listado');">Ver listado</a></td>
                <td style="width: 50%; text-align: right"><a id="action_gps_exportar_excel" href="javascript: exportar('excel');">Exportar</a></td>
            </tr>
        </table>
        
        <div id="listado" style="display: none; position: relative; width: 100%">
            <table id='listado_table'>
                <tr>
                    <th>Veh&iacute;culo</th>
                    <th>Alerta</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Velocidad</th>
                </tr>
            </table>
        </div>
    </div>
</div>

<div id="content_map">
    <div id="map_alert">Mapa sin conexi&oacute;n a Internet</div>
</div>
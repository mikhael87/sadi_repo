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
      div: '#map_rec',
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
                url:'tracker/recorridos',
                type:'POST',
                dataType:'json',
                data: 'f_ini='+f_ini+'&f_fin='+f_fin,
                success:function(json, textStatus){
                    map2.removePolylines();
                    if(json.coor_array !== '[]') {
                        var path= JSON.parse(json.coor_array);
                        map2.drawPolyline({
                          path: path,
                          strokeColor: '#1200bd',
                          strokeOpacity: 0.7,
                          strokeWeight: 4
                        });
                        
                        //PARADAS
                        if(json.coor_stop !== '') {
                            $.each(json.coor_stop,function(key,value){
//                                var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>/images/icon/tracker/red/'+value.icon;


                                map2.addMarker({
                                    lat: value.latitud,
                                    lng: value.longitud,
                                    title: value.hora,
                                    infoWindow: {
                                      content: '<div id="stop_div"><font style="font-size: 12px; font-weight: bold">Parada</font><br/><b>Fecha: </b><font style="font-size: 10px">'+value.fecha+'</font><br/><b>Hora: </b><font style="font-size: 10px">'+value.hora+'</font><br/><b>Duración: </b><font style="font-size: 10px">'+value.tiempo+'</font><br/><b>Velocidad: </b><font style="font-size: 10px">0 Km/h</font><br/><b>Status: </b><font style="font-size: 10px">'+((value.acc)? "<font style='color: green'>Encencido</font>" : "<font style='color: red'>Apagado</font>")+'</font></div>'
                                    },
                                    mouseover: function(e) {
//                                        alert('You clicked in this marker');
//                                         infoWindow: {'<span style="padding: 0px; text-align:left" align="left"><h5>XXX&nbsp; &nbsp; XXXX</h5><p>XXXX<br />XXXX<br />' +
//                                            '<a target="_blank" href=XXXX</a></p>' };
//                                            alert('evento');
//                                           infoWindow.open(map2, marker);
//                                             marker.infoWindow.open(self.map2, marker);
                                      },
//                                    icon : {
//                                          size : new google.maps.Size(32, 37),
//                                          url : icon
//                                        },
//                                    infoWindow: {
//                                      content: '<font style="font-size: 12px; font-weight: bold">Parada</font><br/><b>Fecha: </b><font style="font-size: 10px">'+value.fecha+'</font><br/><b>Hora: </b><font style="font-size: 10px">'+value.hora+'</font><br/><b>Duración: </b><font style="font-size: 10px">'+value.tiempo+'</font><br/><b>Velocidad: </b><font style="font-size: 10px">0 Km/h</font><br/><b>Status: </b><font style="font-size: 10px">'+((value.acc)? "<font style='color: green'>Encencido</font>" : "<font style='color: red'>Apagado</font>")+'</font>'
//                                    }
                                  });
                            });
                        }
                        
                        //EXTREMOS
                        if(json.coor_ext !== '') {
                        var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>/images/icon/tracker/';
                        var icon_start= icon+'route_start.png';
                        var icon_finish= icon+'route_finish.png';

                        map2.addMarker({
                            lat: json.coor_ext['primero']['latitud'],
                            lng: json.coor_ext['primero']['longitud'],
                            title: 'Comienzo',
                            icon : {
                                size : new google.maps.Size(25, 37),
                                url : icon_start
                              }
                          });
                          
                        map2.addMarker({
                            lat: json.coor_ext['ultimo']['latitud'],
                            lng: json.coor_ext['ultimo']['longitud'],
                            title: 'Final',
                            icon : {
                                size : new google.maps.Size(25, 37),
                                url : icon_finish
                              }
                          });
                        }
                        
                        //PUNTOS
                        if(json.coor_puntos !== '') {
                            var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>/images/icon/tracker/';
                            var icon_point= icon+'point_track.png';

                            $.each(json.coor_puntos,function(key,value){
                                map2.addMarker({
                                    lat: value.latitud,
                                    lng: value.longitud,
                                    title: value.hora,
                                    icon : {
                                          size : new google.maps.Size(10, 10),
                                          url : icon_point
                                        },
                                    infoWindow: {
                                      content: '<div id="point_div"><font style="font-size: 12px; font-weight: bold">'+value.marca+'</font><font style="font-size: 14px; color: #666">'+value.modelo+'</font><hr/><b>Fecha: </b><font style="font-size: 10px">'+value.fecha+'</font><br/><b>Hora: </b><font style="font-size: 10px">'+value.hora+'</font><br/><b>Status: </b><font style="font-size: 10px">'+((value.acc)? "<font style='color: green'>Encencido</font>" : "<font style='color: red'>Apagado</font>")+'</font></div>'
                                    }
                                  });
                            });
                        }
                        
                        
                        
                    }else {
                        alert('Sin rutas')
                    }
                }});
      }else {
            alert('Verifique los rangos de fechas');
      }
  }
  
  function exportarExcel() {
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
        
        
  
        window.location='<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>tracker/exportarRecorridosExcel?f_ini='+f_ini+'&f_fin='+f_fin;
  }
</script>

<style>
    #map_rec{
        display: block;
        width: 90%;
        height: 520px;
        margin: 1em 1em;
        -moz-box-shadow: 0px 5px 20px #ccc;
        -webkit-box-shadow: 0px 5px 20px #ccc;
        box-shadow: 0px 5px 20px #ccc;
    }
    
/*    #content_map{
        position: relative;
        overflow-y: hidden
    }
    
    #actions_div{
        position: relative;
        height: 30px;
        width: 760px
    }
    
    #header_map{
        position: relative;
        z-index: 100;
        background-color: #f2f1f0;
        top: 0px;
    }
    
    #table_map_op{
        width: 800px
    }*/
    
    #action_gps_exportar_excel {
        padding-left: 17px;
        background-image: url('../images/icon/page_calc.png');
        background-repeat: no-repeat
    }
</style>

<div id="header_map">
    <h1>Hist&oacute;rico de recorridos</h1>

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
        <a id="action_gps_exportar_excel" href="javascript: exportarExcel();">Exportar</a>
    </div>
</div>

<div id="content_map">
    <div id="map_rec">Mapa sin conexi&oacute;n a Internet</div>
</div>

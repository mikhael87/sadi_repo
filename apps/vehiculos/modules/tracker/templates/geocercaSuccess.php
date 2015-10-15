<?php use_helper('jQuery'); ?>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
      map3 = new GMaps({
        div: '#map_geo',
        lat: 10.490850150899991,
        lng: -66.8789291381836,
    //        zoom: 12,
        zoomControl : true,
        zoomControlOpt: {
            style : 'SMALL',
            position: 'TOP_RIGHT'
        },
        panControl : true,
        streetViewControl : true,
        mapTypeControl: true
      });

      GMaps.on('click', map3.map, function(event) {
        var index = map3.markers.length;
        var lat = event.latLng.lat();
        var lng = event.latLng.lng();

        map3.removePolygons();
        $('#vertx_top_right').val('');
        $('#vertx_bottom_left').val('');
        $('#vertx_top_right').val('');
        $('#vertx_bottom_left').val('');

        map3.addMarker({
          lat: lat,
          lng: lng,
          title: 'Vertice N°' + index
        });

        if(index === 3) {
            var path = [[map3.markers[0].getPosition().lat(), map3.markers[0].getPosition().lng()],
                [map3.markers[1].getPosition().lat(), map3.markers[1].getPosition().lng()],
                [map3.markers[2].getPosition().lat(), map3.markers[2].getPosition().lng()],
                [map3.markers[3].getPosition().lat(), map3.markers[3].getPosition().lng()]];

            polygon = map3.drawPolygon({
              paths: path,
              strokeColor: '#BBD8E9',
              strokeOpacity: 1,
              strokeWeight: 3,
              fillColor: '#BBD8E9',
              fillOpacity: 0.6
            });

            $('#vertx_top_right').val(map3.markers[0].getPosition().lat()+'#'+map3.markers[0].getPosition().lng());
            $('#vertx_top_left').val(map3.markers[1].getPosition().lat()+'#'+map3.markers[1].getPosition().lng());
            $('#vertx_bottom_right').val(map3.markers[2].getPosition().lat()+'#'+map3.markers[2].getPosition().lng());
            $('#vertx_bottom_left').val(map3.markers[3].getPosition().lat()+'#'+map3.markers[3].getPosition().lng());
            
            map3.removeMarkers();
        }
      });
    });
  
  function guardar_geo() {
      if($('#vertx_top_right').val() !== '' && $('#vertx_bottom_left').val() !== '' && $('#vertx_top_right').val() !== '' && $('#vertx_bottom_left').val() !== '') {
          var check= false;
          $('input[name=gps_vehiculos]').each(function() {
            if($(this).is(':checked')) {
              check= true;
            }
          });
          
          if(check) {
              var elegs= '';
              $('input[name=gps_vehiculos]').each(function() {
                if($(this).is(':checked')) {
                  elegs = elegs+$(this).val()+'#';
                }
              });
              
              var t_r= $('#vertx_top_right').val();
              var t_l= $('#vertx_top_left').val();
              var b_r= $('#vertx_bottom_right').val();
              var b_l= $('#vertx_bottom_left').val();
              
              if(elegs !== '') {
                  $.ajax({
                    url:'tracker/saveGeocerca',
                    type:'POST',
                    dataType:'html',
                    data: 'elegidos='+elegs+'&t_r='+t_r+'&t_l='+t_l+'&b_r='+b_r+'&b_l='+b_l,
                    beforeSend: function(Obj){
                        $('#guardar_boton').attr("disabled", "disabled");
                    },
                    success:function(html, textStatus){
                        alert(html);
                        close_window_right();
                    }});
              }
          }else {
              alert('No hay vehículos seleccionados para asignar geocerca.');
          }
      }else {
          alert('Por favor, asegurese de haber seleccionado cuatro (4) vertices.');
      }
  }
  
  function show_geocerca() {
    var gps_vehiculo_id= $('#vehiculo_show').val();
    
    $.ajax({
        url:'tracker/showGeocerca',
        type:'POST',
        dataType:'json',
        data: 'gps_vehiculo_id='+gps_vehiculo_id,
        success:function(json, textStatus){
            
            if(json.path !== 'undefined' && json.path !== '[]') {
                if(json.path !== 'none') {
                    var path= JSON.parse(json.path);
                    map3.removePolygons();
                    map3.removeMarkers();

                    polygon = map3.drawPolygon({
                      paths: path,
                      strokeColor: '#BBD8E9',
                      strokeOpacity: 1,
                      strokeWeight: 3,
                      fillColor: '#BBD8E9',
                      fillOpacity: 0.6
                    });
                }else {
                    map3.removePolygons();
                    map3.removeMarkers();
                }
            }else {
                map3.removePolygons();
                map3.removeMarkers();
                alert('El vehículo no posee una geocerca registrada, puede crearla desde aquí.');
            }
        }});
  }
</script>

<style>
    #map_geo{
        display: block;
        width: 90%;
        height: 360px;
        margin: 1em 1em;
        -moz-box-shadow: 0px 5px 20px #ccc;
        -webkit-box-shadow: 0px 5px 20px #ccc;
        box-shadow: 0px 5px 20px #ccc;
    }
    
    .help { color: #aaa; }
</style>

<div id="header_map">
    <h1>Configuraci&oacute;n Perimetros Geogr&aacute;ficos</h1>

    <div id="actions_div">
        <table style="width: 100%">
            <tr>
                <td style="width: 50%; text-align: left">
                    <select id="vehiculo_show" onChange="javascript: show_geocerca()">
                        <option value="none">Ninguno</option>
                        <?php
                        $vehiculos= Doctrine::getTable('Vehiculos_GpsVehiculo')->rastreables();

                        $cadena_show= '';
                        foreach ($vehiculos as $value) {
                            $cadena_show.= '<option value="'. $value->getGpsVehiculoId() .'">'.$value->getMarca().' '.$value->getModelo().' ['. $value->getPlaca() .']</option>'; 
                        }
                        echo $cadena_show;
                        ?>
                    </select>
                    <?php echo image_tag('icon/info.png', array('style'=>'vertical-align: middle','class'=>'tooltip', 'title'=>'Seleccione un veh&iacute;culo para ver su geocerca, solo si este tiene una asignada previamente.')); ?>
                </td>
                <td style="width: 50%; text-align: right"></td>
            </tr>
        </table>
    </div>
</div>

<div id="content_map">
    <div id="map_geo">Mapa sin conexi&oacute;n a Internet</div>
</div>


<div id="config_geocerca">
    <input type="hidden" id="vertx_top_right" name="vertx_top_right" value=""/>
    <input type="hidden" id="vertx_top_left" name="vertx_top_left" value=""/>
    <input type="hidden" id="vertx_bottom_right" name="vertx_bottom_right" value=""/>
    <input type="hidden" id="vertx_bottom_left" name="vertx_bottom_left" value=""/>
    <br/>
    <hr/>
    <h3>Parametros de configuraci&oacute;n de Geocerca</h3><br/>
    <font style="font-size: 14px">Veh&iacute;culos activos:</font><br/>
    <?php
    $cadena_v= '';
    foreach ($vehiculos as $value) {
        $cadena_v .= '<input type="checkbox" name="gps_vehiculos" value="'. $value->getGpsVehiculoId() .'"/>&nbsp;<b>'.$value->getMarca().'</b>&nbsp;<font style="color: #494949">'.$value->getModelo().'</font>&nbsp;<font style="color: #9f9f9f; font-size: 10px">['. $value->getPlaca() .']</font><br/>';
    }
    $cadena_v .= '<p class="help">Seleccione los veh&iacute;culos a los cuales se asignar&aacute; esta Geocerca.</p>';
    echo $cadena_v;
    ?>
    <button id="guardar_boton" onClick="javascript: guardar_geo();" style="height: 35px;">
        <?php echo image_tag('icon/filesave.png', array('style' => 'vertical-align: middle')) ?>&nbsp;<strong>Guardar configuraci&oacute;n</strong>
    </button>
</div>
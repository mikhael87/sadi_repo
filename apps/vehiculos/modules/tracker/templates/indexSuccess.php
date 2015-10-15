<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="/js/gmaps.js"></script>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>
<script type="text/javascript">
  var map;
  $(document).ready(function(){
    map = new GMaps({
      div: '#map',
      lat: 10.490850150899991,
      lng: -66.8789291381836,
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
    
    var v_track= '<?php echo ($sf_user->getAttribute('vehiculos_track')? '1': '') ?>';
    if(v_track !== '') {
        track();
    }
  });



    var ACTIVO_UPDATE = false;
    function update_track(){
        if (ACTIVO_UPDATE == false){
            track();
        }
    }
    setInterval("track()", 20000);
    
    function track(id) {
        var status_old= '';
        $.ajax({
            url:'tracker/coord',
            type:'POST',
            dataType:'json',
//                  data: 'sim='+id,
                beforeSend: function(Obj){
                    if(id) {
                        status_old= $('#list_vehiculos_table_'+id).attr('src');
                        load= setInterval('track_click('+id+', "TRUE")',150);
                    }
                },
            success:function(json, textStatus){
                if(json[0].marca !== 'empty')
                    map.removeMarkers();
                site_show_check_fun(true);
                if(json[0].marca !== 'no_session' && json[0].marca !== 'no_object' && json[0].marca !== 'empty') {
                    $.each(json,function(key,value){
                        if(status_old !== '')
                            map.setCenter(value.lat, value.long);
                        if(value.icon === 'default.png')
                            var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>/images/icon/tracker/'+value.icon;
                        else
                            var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>/images/icon/tracker/'+value.color_icon+'/'+value.icon;

                        var acc= (value.acc)? '<font style="color: green">Encendido</font>' : '<font style="color: red">Apagado</font>';
                        var speed= '<font style="font-size: 10px">'+value.speed+' Km/h</font>';
                        var pwr= (value.pwr)? '<font style="font-size: 10px">Bateria conectada</font>' : '<font style="font-size: 10px; color: red">Bateria desconectada</font>';


                        map.addMarker({
                            lat: value.lat,
                            lng: value.long,
                            title: value.placa,
                            icon : {
                                  size : new google.maps.Size(32, 37),
                                  url : icon
                                },
                            infoWindow: {
                              content: '<font style="font-size: 12px; font-weight: bold">'+value.marca+'</font><font style="font-size: 14px; color: #666">'+value.modelo+'</font><br/>'+acc+'<br/>'+speed+'<br/>'+pwr
                            }
                          });
                    });
                }else {
                    if(json[0].marca === 'no_object') {
                        if(id) {
                            clearInterval(load);
                            status_old= '/images/icon/tracker/target_off.png';
                            $('#noti_'+id).html('<font class="error" style="color: red">Sin rastro</font>');
                            $(".error").delay(3000).fadeOut("fast");
                            removeTrack(id);
                        }
                    }
                }
                if(status_old !== '') {
                    clearInterval(load);
                    $('#list_vehiculos_table_'+id).attr('src', status_old);
                }
                ACTIVO_UPDATE = false;
            }});
    }
      
    function addTrack(id) {
        $.ajax({
            url:'tracker/addTrack',
            type:'POST',
            dataType:'html',
                  data: 'id='+id,
            success:function(html, textStatus){
                track(id);
            }});
    }
    
    function removeTrack(id) {
        $.ajax({
            url:'tracker/removeTrack',
            type:'POST',
            dataType:'html',
                  data: 'id='+id,
            success:function(html, textStatus){
                track(id);
            }});
    }
    
    function track_click(id, load) {
        if($('#list_vehiculos_table_'+id).attr('src') === '/images/icon/tracker/target_off.png') {
            $('#list_vehiculos_table_'+id).attr('src', '/images/icon/tracker/target_on.png');
            if(load === "FALSE")
                addTrack(id);
        } else {
            $('#list_vehiculos_table_'+id).attr('src', '/images/icon/tracker/target_off.png');
            if(load === "FALSE")
                removeTrack(id);
        }
    }
    
    function recorridos(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando información...');
    
        $.ajax({
            url:'tracker/recorridos',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }}); 
    }
    
    function alertas(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando información...');
    
        $.ajax({
            url:'tracker/alertas',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }}); 
    }
    
    function geocerca(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando información...');
    
        $.ajax({
            url:'tracker/geocerca',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }}); 
    }
    
    function sitios(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando información...');
    
        $.ajax({
            url:'tracker/sitios',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }}); 
    }
    
    function site_show_check_fun(auto) {
        if($('#site_show_check').is(':checked')) {
            $.ajax({
            url:'tracker/showSites',
            type:'POST',
            dataType:'json',
            success:function(json, textStatus){
                if(json[0].nombre !== 'empty') {
                    $.each(json,function(key,value){
                        
                        var tipo_nombre= '<font style="font-weight: bold">'+ value.tipo_nombre +'</font>';
                        var nombre= '<font style="font-size: 10px">'+value.nombre+'</font>';
                        var direccion= (value.direccion !== '')? '<font style="font-size: 10px">'+ value.direccion +'</font>' : '<font style="font-size: 10px">Sin direccion</font>';


                        map.addMarker({
                            lat: value.latitud,
                            lng: value.longitud,
                            title: value.nombre,
                            icon : {
                                  size : new google.maps.Size(32, 37),
                                  url : value.icono
                                },
                            infoWindow: {
                              content: tipo_nombre+'<br/>'+nombre+'<br/>'+direccion
                            }
                          });
                    });
                }
            }});
        }else {
            map.removeMarkers();
            if(auto === false)
                track();
        }
    }
</script>


<style>
    #map{
        display: block;
        width: 90%;
        height: 520px;
        margin: 1em 1em;
        -moz-box-shadow: 0px 5px 20px #ccc;
        -webkit-box-shadow: 0px 5px 20px #ccc;
        box-shadow: 0px 5px 20px #ccc;
    }
    
    #list_vehiculos_table tr td {
        padding-top: 8px;
        padding-left: 4px;
        padding-bottom: 4px;
        padding-right: 4px
    }
    
    #main_table_track { width: 100% }
    
    #list_vehiculos_table tr td a { text-decoration: none }
    
    #main_table_track .reportes {
        padding-left: 20px;
        background-image: url('../images/icon/chart_pie.png');
        background-repeat: no-repeat;
        background-position: 0px 6px
    }
    
    #main_table_track .herramientas {
        padding-left: 20px;
        background-image: url('../images/icon/tracker/servicio.png');
        background-repeat: no-repeat;
        background-position: 0px 6px
    }
    
    #main_table_track .vehiculos {
        padding-left: 20px;
        background-image: url('../images/icon/tracker/car.png');
        background-repeat: no-repeat;
        background-position: 0px 6px
    }
    
    #main_table_track .geocerca {
        padding-left: 20px;
        background-image: url('../images/icon/tracker/geocerca.png');
        background-repeat: no-repeat;
        background-position: 0px 6px
    }
    
    #main_table_track .configuracion {
        padding-left: 20px;
        background-image: url('../images/icon/run.png');
        background-repeat: no-repeat;
        background-position: 0px 6px
    }
    
    .noti {
        padding-top: 5px
    }
        
</style>
<br/>
<table id="main_table_track" class="trans_claro">
    <tr>
        <td></td>
        <td style="padding-top: 20px">
            <ul class="mi-menu-track">
                <li>
                    <a class="herramientas" href="javascript: return false;">Herramientas</a>
                    <ul>
                    <!--<li><a href="URL_enlace_2.1">Sitios interes</a></li>-->
                    <li><a href="javascript: open_window_right(); sitios()">Editar sitios</a></li>
                  </ul>
                <li><a class="vehiculos" href="<?php echo sfConfig::get('sf_app_vehiculos_url') ?>vehiculo/index">Veh&iacute;culos</a></li>
                <li>
                  <a class="reportes" href="javascript: return false;">Reportes</a>
                  <ul>
                    <li><a href="javascript: open_window_right(); recorridos()">Recorrido</a></li>
                    <li><a href="javascript: open_window_right(); alertas()">Alertas</a></li>
                  </ul>
                </li>
                <li><a class="geocerca" href="javascript: open_window_right(); geocerca()">Geocerca</a></li>
                <li><a class="configuracion" href="<?php echo sfConfig::get('sf_app_acceso_url') ?>configuracion?opcion=gps&prv=true">Configuraci&oacute;n</a></li>
<!--                <li><a href="URL_enlace_3">Gps</a></li>
                <li><a href="URL_enlace_3">Comandos AT</a></li>-->
              </ul>
        </td>
    </tr>
    <tr>
        <td style="width: 12%; vertical-align: top" style="position: relative">
            <div id="vehiculos_div" style="position: relative; top: 0px; padding-left: 5px; padding-right: 5px">
                    <table id="list_vehiculos_table">
                        <tr>
                            <td colspan="2">
                                <font style="font-size: 15px; font-weight: bolder">Veh&iacute;culos</font>
                            </td>
                        </tr>
                        <?php
                        $vehiculos= Doctrine::getTable('Vehiculos_GpsVehiculo')->rastreables();
                        //echo $sf_user->getAttribute('vehiculos_track');
                        foreach($vehiculos as $vehiculo) {
                            $cadena = '<tr class="trans_claro"><td>';
                            $cadena .= '<a id="'. $vehiculo->getGpsVehiculoId() .'" href="javascript: changeTrack('. $vehiculo->getGpsVehiculoId() .')"><strong>'. $vehiculo->getMarca().'</strong> '. $vehiculo->getModelo() .'</a><br/><font style="color: #666; font-size: 10px">'. $vehiculo->getPlaca() .'</font>';
                            $cadena .= '<div class="noti" id="noti_'. $vehiculo->getGpsVehiculoId() .'" style="display: block"></div>';
                            $cadena .= '</td><td style="padding-left: 10px; padding-top: 2px">'.image_tag('icon/tracker/target_'. ((sfContext::getInstance()->getUser()->hasAttribute('vehiculos_track')) ? ((in_array($vehiculo->getGpsVehiculoId(), sfContext::getInstance()->getUser()->getAttribute('vehiculos_track'))) ? 'on' : 'off') : 'off')  .'.png', array('style' => 'cursor: pointer', 'id' => 'list_vehiculos_table_'.$vehiculo->getGpsVehiculoId(), 'onClick' => 'javascript: track_click('. $vehiculo->getGpsVehiculoId() .', "FALSE")'));
                            $cadena .= '</td></tr>';
                            $cadena .= '<tr ><td colspan="2" id="noti_'. $vehiculo->getGpsVehiculoId() .'"></td></tr>';
                            echo $cadena;
                        }
                        ?>
                    </table>
            </div>
            <div id="sitios_div" style="position: relative; padding-left: 5px; padding-right: 5px">
                <input type="checkbox" style="vertical-align: middle" name="site_show_check" id="site_show_check" onClick="site_show_check_fun(false)" />&nbsp;<font style="font-size: 11px; color: #000">Mostrar sitios de interes</font>
            </div>
        </td>
        <td style="width: 88%"><div id="map">Mapa sin conexi&oacute;n a Internet</div></td>
    </tr>
</table>

<style type="text/css">
  /* si es necesario, evitamos que Blogger de problemas con los saltos de línea cuando escribimos el HTML */
  .mi-menu-track  br { display:none; }

  /* cada item del menu */
  .mi-menu-track  li {
    display: block;
    float: left; /* la lista se ve horizontal */
    height: 40px;
    list-style: none;
    margin: 0;
    padding: 0;
    position: relative;
  }
  .mi-menu-track li a {
/*    border-left: 1px solid #000;
    border-right: 1px solid #666;*/
    color: black;
    display: block;
    font-family: Arial;
    font-size: 13px;
    font-weight: bold;
    line-height: 28px;
    padding: 0 14px;
    margin: 6px 0;
    text-decoration: none;
    /* animamos el cambio de color de los textos */
    -webkit-transition: color .2s ease-in-out;
    -moz-transition: color .2s ease-in-out;
    -o-transition: color .2s ease-in-out;
    -ms-transition: color .2s ease-in-out;
    transition: color .2s ease-in-out;
  }
  /* eliminamos los bordes del primer y el último */
  .mi-menu-track li:first-child a { border-left: none; }
  .mi-menu li:last-child a{ border-right: none; }
  /* efecto hover cambia el color */
  .mi-menu-track li:hover > a { color: black; }

  /* los submenús */
  .mi-menu-track ul {
    border: 1px;
    border-radius: 0 0 5px 5px;
    z-index: 10;
    left: 0;
    margin: 0;
    opacity: 0; /* no son visibles */
    position: absolute;
    top: 30px; /* se ubican debajo del enlace principal */
    /* el color de fondo */
    background: #efefef;
    background: -moz-linear-gradient(#efefef,#fefefe);
    background: -webkit-linear-gradient(#22,#fefefe);
    background: -o-linear-gradient(#efefef,#fefefe);
    background: -ms-linear-gradient(#efefef,#fefefe);
    background: linear-gradient(#efefef,#fefefe);
    /* animamos su visibildiad */
    -moz-transition: opacity .25s ease .1s;
    -webkit-transition: opacity .25s ease .1s;
    -o-transition: opacity .25s ease .1s;
    -ms-transition: opacity .25s ease .1s;
    transition: opacity .25s ease .1s;
  }
  /* son visibes al poner el cursor encima */
  .mi-menu-track li:hover > ul { opacity: 1; }

   /* cada un ode los items de los submenús */
  .mi-menu-track ul li {
    height: 0; /* no son visibles */
    overflow: hidden;
    padding: 0;
    /* animamos su visibildiad */
    -moz-transition: height .25s ease .1s;
    -webkit-transition: height .25s ease .1s;
    -o-transition: height .25s ease .1s;
    -ms-transition: height .25s ease .1s;
    transition: height .25s ease .1s;
  }
  .mi-menu-track li:hover > ul li {
    height: 36px; /* los mostramos */
    overflow: visible;
    padding: 0;
  }
  .mi-menu-track ul li a {
    border: none;
    border-bottom: 1px solid #a4a4a4;
    margin: 0;
    /* el ancho dependerá de los textos a utilizar */
    padding: 5px 20px;
    width: 100px;
  }
  /* el último n otiene un borde */
  .mi-menu-track ul li:last-child a { border: none; }

</style>
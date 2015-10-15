<?php use_helper('jQuery'); ?>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js"></script>
<script type="text/javascript">
    var map4;
    $(document).ready(function(){
      map4 = new GMaps({
        div: '#map_site',
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

      GMaps.on('click', map4.map, function(event) {
        var index = map4.markers.length;
        var lat = event.latLng.lat();
        var lng = event.latLng.lng();

        map4.removeMarkers();
        $('#site_latitud').val('');
        $('#site_longitud').val('');

        var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>'+$('#icon_selected').attr('src');

        map4.addMarker({
          lat: lat,
          lng: lng,
          title: $('#sitios_tipo option:selected').html(),
          icon : {
                size : new google.maps.Size(32, 37),
                url : icon
              }
        });
        
        $('#site_latitud').val(map4.markers[0].getPosition().lat());
        $('#site_longitud').val(map4.markers[0].getPosition().lng());
      });
    });
    
    
  function sitios_tipo_select() {
    var icon_selected_ar= $('#sitios_tipo').val();
    icon_selected_ar= icon_selected_ar.split('#');
    var icon_selected= icon_selected_ar[0];
    var color_selected= 'red';
    $('input[name=color_select]').each(function() {
        if($(this).is(':checked')) {
          color_selected= $(this).val();
        }
      });
      
    icon_selected = icon_selected.replace("red",color_selected);

    $('#icon_selected').attr('src', '/images/icon/tracker/'+icon_selected);
    
    if($('#site_latitud').val() !== '' && $('#site_longitud').val() !== '') {
        map4.removeMarkers();
        var lat= $('#site_latitud').val();
        var lng= $('#site_longitud').val();

        var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>'+$('#icon_selected').attr('src');

        map4.addMarker({
          lat: lat,
          lng: lng,
          title: $('#sitios_tipo option:selected').html(),
          icon : {
                size : new google.maps.Size(32, 37),
                url : icon
              }
        });
    }
  }
  
  function save_site() {
      if($('#sitio_nombre').val() !== '') {
          if($('#site_latitud').val() !== '' && $('#site_longitud').val() !== '') {
                var lat= $('#site_latitud').val();
                var lng= $('#site_longitud').val();
                var name= $('#sitio_nombre').val();
                var dir= $('#sitio_direccion').val();
                
                var icon_selected_ar= $('#sitios_tipo').val();
                icon_selected_ar= icon_selected_ar.split('#');
                var sitio_tipo_id= icon_selected_ar[1];
                
                var src= $('#icon_selected').attr('src');
                src = src.replace("/images/icon/tracker/", '');
                src= src.split('/');
                
                $.ajax({
                    url:'tracker/saveSite',
                    type:'POST',
                    dataType:'html',
                    data: 'lat='+lat+'&lng='+lng+'&name='+name+'&dir='+dir+'&sitio_tipo_id='+sitio_tipo_id+'&color='+src[0],
                    beforeSend: function(Obj){
                        $('#guardar_boton').attr("disabled", "disabled");
                    },
                    success:function(html, textStatus){
                        alert(html);
                        document.getElementById("sitios_tipo").selectedIndex = 0;
                        $('input[name=color_select][value=red]').prop("checked",true);
                        sitios_tipo_select();
                        map4.removeMarkers();
                        $('#site_latitud').val('');
                        $('#site_longitud').val('');
                        $('#sitio_nombre').val('');
                        $('#sitio_direccion').val('');
                    }});
          }else {
              alert('Seleccione una ubicación para el sitio en el mapa.');
          }
      }else {
          alert('Indique Nombre del sitio creado.');
      }
  }
</script>

<style>
    #map_site{
        display: block;
        width: 90%;
        height: 360px;
        margin: 2em 1em;
        -moz-box-shadow: 0px 5px 20px #ccc;
        -webkit-box-shadow: 0px 5px 20px #ccc;
        box-shadow: 0px 5px 20px #ccc;
    }
    
    .help { color: #aaa; }
</style>

<div id="header_map">
    <h1>Edici&oacute;n de Sitios de Interes</h1>

    <div id="actions_div">
        <table style="width: 250px">
            <tr>
                <td style="width: 20%; text-align: left">
                    <input type="hidden" name="sitio_tipo_id" value=""/>
                    <select id="sitios_tipo" onChange="javascript: sitios_tipo_select()">
                        <?php
                        $cadena_show= '';
                        foreach ($sitios_tipo as $value) {
                            $cadena_show.= '<option value="'. $value->getIcono() .'#'. $value->getId() .'">'. $value->getNombre() .'</option>'; 
                        }
                        echo $cadena_show;
                        ?>
                    </select>
                </td>
                <td style="width: 20%; text-align: left">
                    <?php echo image_tag('icon/tracker/red/site.png', array('style'=>'vertical-align: middle','id'=>'icon_selected')); ?>
                </td>
                <td style="width: 60%; text-align: left">
                    <table style='width: 92px; margin-top: 10px'>
                        <tr>
                            <td style='text-align: center'><input type="radio" name="color_select" class='color_select' value='red' checked onClick='javascript: sitios_tipo_select()'/></td>
                            <td style='text-align: center'><input type="radio" name="color_select" class='color_select' value='green' onClick='javascript: sitios_tipo_select()'/></td>
                            <td style='text-align: center'><input type="radio" name="color_select" class='color_select' value='blue' onClick='javascript: sitios_tipo_select()'/></td>
                            <td style='text-align: center'><input type="radio" name="color_select" class='color_select' value='pink' onClick='javascript: sitios_tipo_select()'/></td>
                        </tr>
                        <tr>
                            <td colspan='4'><?php echo image_tag('icon/tracker/color_selector.png', array('style'=>'padding-top: 10px')); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>

<div id="content_map">
    <div id="map_site">Mapa sin conexi&oacute;n a Internet</div>
</div>


<div id="config_sites">
    <input type="hidden" id="site_latitud" name="site_latitud" value=""/>
    <input type="hidden" id="site_longitud" name="site_longitud" value=""/>
    <br/>
    <hr/>
    <table>
        <tr>
            <td style="vertical-align: top"><font style="font-size: 14px">Nombre del Sitio:&nbsp;</font></td>
            <td><input type="text" id="sitio_nombre" value="" /><br/><br/></td>
        </tr>
        <tr>
            <td style="vertical-align: top"><font style="font-size: 14px">Direcci&oacute;n:</font></td>
            <td><textarea id="sitio_direccion" cols="30" rows="4"></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td><br/>
                <button id="guardar_boton" style="height: 30px;" onClick="javascript: save_site()">
                    <?php echo image_tag('icon/filesave.png', array('style' => 'vertical-align: middle')) ?>&nbsp;<strong>Guardar sitio</strong>
                </button>
            </td>
        </tr>
    </table>
</div>
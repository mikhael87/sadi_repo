<style>
    #icon_table tr td {
        border-width: 0px;
    }
    #icon_table tr {
        border-width: 0px;
    }
    #icon_table tr td img {
        cursor: pointer
    }
    .help {
        padding-left: 0px !important
    }
    .stick {
        position: absolute;
        margin-top: 20px;
        margin-left: -15px
    }
</style>

<script>
    $(document).ready(function() {
        $( "#icon_table tr td img" ).click(function() {
            $('.stick').remove();
            $(this).parent().append('<img class="stick" id="stick_'+ $(this).attr('id') +'" name="" src="/images/icon/ok.png"/>');
            $('#vehiculos_gps_vehiculo_icono').val($(this).attr('name')+'.png');
        });
    });
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_icono">
    <div>
        <label for="vehiculos_gps_vehiculo_icono">Icono</label>
        <div class="content">
            <input id="vehiculos_gps_vehiculo_icono" type="hidden" value="default.png" name="vehiculos_gps_vehiculo[icono]">
            <table id="icon_table">
                <tr>
                    <td><img id="icon_1" name="ambulance" src="/images/icon/tracker/red/ambulance.png"/></td>
                    <td><img id="icon_2" name="bulldozer" src="/images/icon/tracker/red/bulldozer.png"/></td>
                    <td><img id="icon_3" name="bus" src="/images/icon/tracker/red/bus.png"/></td>
                    <td><img id="icon_4" name="car" src="/images/icon/tracker/red/car.png"/></td>
                    <td><img id="icon_5" name="convertible" src="/images/icon/tracker/red/convertible.png"/></td>
                </tr>
                <tr>
                    <td><img id="icon_6" name="ducati-diavel" src="/images/icon/tracker/red/ducati-diavel.png"/></td>
                    <td><img id="icon_7" name="fourbyfour" src="/images/icon/tracker/red/fourbyfour.png"/></td>
                    <td><img id="icon_8" name="jeep" src="/images/icon/tracker/red/jeep.png"/></td>
                    <td><img id="icon_9" name="motorcycle" src="/images/icon/tracker/red/motorcycle.png"/></td>
                    <td><img id="icon_10" name="pickup" src="/images/icon/tracker/red/pickup.png"/></td>
                </tr>
                <tr>
                    <td><img id="icon_11" name="pickup_camper" src="/images/icon/tracker/red/pickup_camper.png"/></td>
                    <td><img id="icon_12" name="sportutilityvehicle" src="/images/icon/tracker/red/sportutilityvehicle.png"/></td>
                    <td><img id="icon_13" name="taxi" src="/images/icon/tracker/red/taxi.png"/></td>
                    <td><img id="icon_14" name="truck3" src="/images/icon/tracker/red/truck3.png"/></td>
                    <td><img id="icon_15" name="van" src="/images/icon/tracker/red/van.png"/></td>
                </tr>
            </table>
            <p class="help">Seleccione una imagen para mostrar en el mapa</p>
        </div>
    </div>
</div>

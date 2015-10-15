<style>
    #color_table tr td {
        border-width: 0px;
    }
    #color_table tr {
        border-width: 0px;
    }
    #color_table tr td img {
        cursor: pointer
    }
    .help {
        padding-left: 0px !important
    }
    #blue {
        border-top-right-radius: 8px;
        border-bottom-left-radius: 8px;
        width: 20px;
        height: 20px;
        background-color: blue
    }
    #green {
        border-top-right-radius: 8px;
        border-bottom-left-radius: 8px;
        width: 20px;
        height: 20px;
        background-color: green
    }
    #pink {
        border-top-right-radius: 8px;
        border-bottom-left-radius: 8px;
        width: 20px;
        height: 20px;
        background-color: pink
    }
    #red {
        border-top-right-radius: 8px;
        border-bottom-left-radius: 8px;
        width: 20px;
        height: 20px;
        background-color: red
    }
</style>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_color_icon">
    <div>
        <label for="vehiculos_gps_vehiculo_color_icon">Color del Icono</label>
        <div class="content">
            <!--<input id="vehiculos_gps_vehiculo_color_icon" type="text" name="vehiculos_gps_vehiculo[color_icon]">-->
            <table id="color_table">
                <tr>
                    <td><input type="radio" name="vehiculos_gps_vehiculo[color_icon]" value="blue"/></td>
                    <td><div id="blue"></div></td>
                    <td><input type="radio" name="vehiculos_gps_vehiculo[color_icon]" value="green"></td>
                    <td><div id="green"></div></td>
                    <td><input type="radio" name="vehiculos_gps_vehiculo[color_icon]" value="pink"></td>
                    <td><div id="pink"></div></td>
                    <td><input type="radio" name="vehiculos_gps_vehiculo[color_icon]" value="red" checked></td>
                    <td><div id="red"></div></td>
                </tr>
            </table>
        </div>
    </div>
</div>

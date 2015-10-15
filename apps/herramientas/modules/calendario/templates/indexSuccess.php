<?php use_helper('jQuery'); ?>
<?php
//VERIFICAR PERMISOS DE MODIFICACION DE EVENTOS
if ($id == $sf_user->getAttribute('funcionario_id')) {
    $editarCalendario = "true";
    $aceptarEventos = "true";
} elseif (isset($idModuloAutorizado)) {
    $modulo_autorizado = Doctrine::getTable('Acceso_AccionDelegada')->findOneById($idModuloAutorizado);
    $parametros = sfYaml::load($modulo_autorizado->getParametros());
    if (isset($parametros['editar_calendario'])) {
        $editarCalendario = "true";
    } else {
        $editarCalendario = "false";
    }
    if (isset($parametros['invitaciones_calendario'])) {
        $aceptarEventos = "true";
    } else {
        $aceptarEventos = "false";
    }
}
//FIN DE VERIFICAR PERMISOS DE MODIFICACION DE EVENTOS
//OBTENER EVENTOS DEL CALENDARIO
$eventosLista = "";
foreach ($eventos as $evento) {

    $eventoInicio = date("Y,(m-1),d,H,i", strtotime($evento[2]));
    $eventoFinal = date("Y,(m-1),d,H,i", strtotime($evento[3]));
    if ($evento[4] == "TRUE") {
        $allDay = "allDay: true";
    } else {
        $allDay = '';
    }
    $eventosLista .= "
        {
            id: " . $evento[0] . ",
            title: '" . $evento[1] . "',
            start: new Date(" . $eventoInicio . "),
            end: new Date(" . $eventoFinal . "),
            " . $allDay . "
        },";
}
foreach ($eventosInvitado as $evento) {

    $eventoInicio = date("Y,(m-1),d,H,i", strtotime($evento[2]));
    $eventoFinal = date("Y,(m-1),d,H,i", strtotime($evento[3]));

    $eventosLista .= "
        {
            id: " . $evento[0] . ",
            title: '" . $evento[1] . "',
            start: new Date(" . $eventoInicio . "),
            end: new Date(" . $eventoFinal . ")
        },";
}
//FIN DE OBTENER EVENTOS DEL CALENDARIO
?>
<style>
    .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
    .ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

    .ui-timepicker-rtl{ direction: rtl; }
    .ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
    .ui-timepicker-rtl dl dt{ float: right; clear: right; }
    .ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }

    .btn
    {
        -moz-user-select: none;
        border: 1px solid rgba(0, 0, 0, 0);
        border-radius: 4px 4px 4px 4px;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        font-weight: normal;
        line-height: 1.42857;
        margin-bottom: 0;
        padding: 6px 12px;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    .btn-default {
        background-color: #FFFFFF;
        border-color: #CCCCCC;
        color: #333333;
    }

    #calendar {
        width: 100%;
        margin: 0 auto;
        text-align:left;
    }
    #popup, .popup{
        width: 70%;
        position: absolute;
        background-color: #F2F1F0;
        border-color: #CCCCCC;
        border-radius: 8px;
        border-style: solid;
        display:block;
        left: 12%;
        top: 50px;
        display: none;
        cursor: pointer;
        z-index: 9999;
    }
    #detail{
        background-color: #000;
    }
    #popup input{
        width:100%;
        border-radius: 5px;
    } 
    #popup table label{
        font-size: 100px;
    }
    #startTime{
        background-position:right center;
        background-repeat:no-repeat;
    }
    #endTime{
        background-position:right center;
        background-repeat:no-repeat;
    }
</style>
<script type="text/javascript">
    var dataString = {};
    dataString['id'] = <?php echo $id; ?>;
    dataString['invitados'] = {};
    dataString['externo'] = {};
    var eventoModificado;
    var update = 1;
    var startDay = '';

    $(document).ready(function() {

        var calendar = $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            selectable: <?php echo $editarCalendario; ?>,
            selectHelper: true,
            editable: <?php echo $editarCalendario; ?>,
            events: [
<?php echo $eventosLista; ?>
            ],
            select: function(start, end, allDay) {
                i = 0;
                startTime = $.fullCalendar.formatDate(start, "yyyy-MM-dd H:mm tt");
                endTime = $.fullCalendar.formatDate(start, "yyyy-MM-dd H:mm tt");

                startDay = "<b>Dia: " + $.fullCalendar.formatDate(start, "dd") + " de " + $.fullCalendar.formatDate(start, "MMMM") + "</b>";
                $("#startDay").html(startDay);

                $("#startTime").val(startTime);
                $("#endTime").val(endTime);
                $("#allDay").attr("checked", "checked");
                $("#noAllDay").hide();
                $("#startDay").show();
                $("#popup").show();
                $("#eventName").focus();

                $("#submit").one('click', function() {
                    $("#editarInvitacion").hide();
                    type = "Create";
                    var title = $("#eventName").val() + " (" + $("#cargos input[type='radio']:checked").attr('id') + ")";
                    //TOMAR FECHA INICIO
                    var dataExplode = $('#startTime').val();
                    año = dataExplode[0] + dataExplode[1] + dataExplode[2] + dataExplode[3];
                    mes = dataExplode[5] + dataExplode[6];
                    dia = dataExplode[8] + dataExplode[9];
                    hora = parseInt(dataExplode[11] + dataExplode[12]);
                    min = dataExplode[14] + dataExplode[15];
                    meridiem = dataExplode[17] + dataExplode[18];
                    if (meridiem === "pm") {
                        hora += 12;
                    }
                    start = new Date(año, (mes - 1), dia, hora, min);
                    startCompare = start.getTime();

                    //TOMAR FECHA FINAL
                    var dataExplode = $('#endTime').val();
                    año = dataExplode[0] + dataExplode[1] + dataExplode[2] + dataExplode[3];
                    mes = dataExplode[5] + dataExplode[6];
                    dia = dataExplode[8] + dataExplode[9];
                    hora = parseInt(dataExplode[11] + dataExplode[12]);
                    min = dataExplode[14] + dataExplode[15];
                    meridiem = dataExplode[17] + dataExplode[18];
                    if (meridiem === "pm") {
                        hora += 12;
                    }
                    end = new Date(año, (mes - 1), dia, hora, min);
                    endCompare = end.getTime();

                    if ($("#allDay").attr("checked") === "checked") {
                        dataString['allDay'] = "TRUE";
                        allDay = true;
                    }
                    else {
                        dataString['allDay'] = "FALSE";
                        allDay = false;
                    }
                    if ($("#institucional").attr("checked") === "checked") {
                        dataString['institucional'] = "TRUE";
                    }
                    else {
                        dataString['institucional'] = "FALSE";
                    }
                    dataString['eventName'] = title;
                    dataString['unidad'] = $("#cargos input[type='radio']:checked").val();
                    dataString['startTime'] = $.fullCalendar.formatDate(start, "yyyy-MM-dd H:mm:ss");
                    dataString['endTime'] = $.fullCalendar.formatDate(end, "yyyy-MM-dd H:mm:ss");
                    $("input[name='preingreso_persona[]']").each(function() {
                        dataString['externo'][$(this).val()] = $(this).val();
                    });

                    if (startCompare > endCompare) {
                        alert('Lo sentimos, no puedes crear un evento que finaliza antes de que empiece.');
                    }
                    else {

                        if (title && start && end) {
                            if (type === "Create" && i === 0)
                            {
                                i = 1;
                                $.ajax({
                                    type: 'POST',
                                    dataType: 'json',
                                    url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/createEvento',
                                    data: dataString,
                                    success: function(json) {
                                        inicio = new Date(json['inicio']['Y'], (json['inicio']['m'] - 1), json['inicio']['d'], json['inicio']['H'], json['inicio']['i']);
                                        final = new Date(json['final']['Y'], (json['final']['m'] - 1), json['final']['d'], json['final']['H'], json['final']['i']);
                                        dataString['invitados'] = {};
                                        dataString['externo'] = {};
                                        calendar.fullCalendar('renderEvent',
                                                {
                                                    id: json['id'],
                                                    title: json['title'],
                                                    start: inicio,
                                                    end: final,
                                                    allDay: allDay
                                                },
                                        true
                                                );
                                    }
                                });
                            }
                            calendar.fullCalendar('unselect');
                            calendar.fullCalendar('refetchEvents');
                            //LIMPIO LOS TEXTOS
                            $("#popup").hide();
                            $("#invitarFuncionariosEvento").html('');
                            $("#eventName").val('');
                            $("#startTime").val('');
                            $("#endTime").val('');
                        }
                    }
                });
            },
            eventClick: function(calEvent) {
                $("#editarInvitacion").hide();
                id = calEvent.id;
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/verificar',
                    data: "evento=" + id,
                    success: function(data) {
                        if (data !== "false")
                        {
                            if (calEvent.allDay === true)
                            {
                                $("#allDay").attr("checked", "checked");
                                $("#noAllDay").hide();
                                $("#startDay").show();
                            }
                            else
                            {
                                $("#allDay").removeAttr("checked");
                                $("#noAllDay").show();
                                $("#startDay").hide();
                            }
                            type = "Update";
                            startDay = "<b>Dia: " + $.fullCalendar.formatDate(calEvent.start, "dd") + " de " + $.fullCalendar.formatDate(calEvent.start, "MMMM") + "</b>";
                            $("#startDay").html(startDay);

                            var startFirst = calEvent.start;
                            var endFirst = calEvent.end || startFirst;

                            startTime = $.fullCalendar.formatDate(startFirst, "yyyy-MM-dd H:mm tt");
                            endTime = $.fullCalendar.formatDate(endFirst, "yyyy-MM-dd H:mm tt");

                            $("#startTime").val(startTime);
                            $("#endTime").val(endTime);

                            title = calEvent.title.split("(");
                            title = title[0];
                            $("#eventName").val(title);
                            $('#invitadosInternos').html('<?php echo image_tag('icon/cargando.gif', array('size' => '25x25')); ?> Cargando invitados internos...');
                            $.ajax({
                                type: 'POST',
                                dataType: 'html',
                                url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/buscarInvitadosInternos',
                                data: {id: calEvent.id},
                                success: function(data, textStatus) {
                                    if (data !== '')
                                        $("#invitadosInternos").html("Invitados Internos a este evento: <br/>" + data);
                                    else
                                        $('#invitadosInternos').html('');

                                    $('#invitadosExternos').html('<?php echo image_tag('icon/cargando.gif', array('size' => '25x25')); ?> Cargando invitados externos...');
                                    $.ajax({
                                        type: 'POST',
                                        dataType: 'html',
                                        url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/buscarInvitadosExternos',
                                        data: {id: calEvent.id},
                                        success: function(data, textStatus) {
                                            if (data !== '')
                                                $("#invitadosExternos").html("Invitados Externos a este evento: <br/>" + data);
                                            else
                                                $('#invitadosExternos').html('');
                                        }
                                    });
                                }
                            });

                            $("#popup").show();
                            eventoModificado = calEvent.id;
                            $("#eventName").focus();

                            $("#submit").one('click', function() {
                                if (calEvent.id === id && type === "Update") {
                                    i = 0;
                                    var title = $("#eventName").val() + " (" + $("#cargos input[type='radio']:checked").attr('id') + ")";
                                    //TOMAR FECHA INICIO
                                    if ($('#startTime').val() !== '')
                                    {
                                        var dataExplode = $('#startTime').val();
                                        año = dataExplode[0] + dataExplode[1] + dataExplode[2] + dataExplode[3];
                                        mes = dataExplode[5] + dataExplode[6];
                                        dia = dataExplode[8] + dataExplode[9];
                                        hora = parseInt(dataExplode[11] + dataExplode[12]);
                                        min = dataExplode[14] + dataExplode[15];
                                        meridiem = dataExplode[17] + dataExplode[18];
                                        if (meridiem === "pm") {
                                            hora += 12;
                                        }
                                        start = new Date(año, (mes - 1), dia, hora, min);
                                        startCompare = start.getTime();
                                    } else {
                                        start = startFirst;
                                    }
                                    //TOMAR FECHA FINAL
                                    if ($('#endTime').val() !== '')
                                    {
                                        var dataExplode = $('#endTime').val();
                                        año = dataExplode[0] + dataExplode[1] + dataExplode[2] + dataExplode[3];
                                        mes = dataExplode[5] + dataExplode[6];
                                        dia = dataExplode[8] + dataExplode[9];
                                        hora = parseInt(dataExplode[11] + dataExplode[12]);
                                        min = dataExplode[14] + dataExplode[15];
                                        meridiem = dataExplode[17] + dataExplode[18];
                                        if (meridiem === "pm") {
                                            hora += 12;
                                        }
                                        end = new Date(año, (mes - 1), dia, hora, min);
                                        endCompare = end.getTime();
                                    } else {
                                        end = endFirst;
                                    }

                                    if ($("#allDay").attr("checked") === "checked") {
                                        dataString['allDay'] = "TRUE";
                                        allDay = true;
                                    }
                                    else {
                                        dataString['allDay'] = "FALSE";
                                        allDay = false;
                                    }
                                    
                                    if ($("#institucional").attr("checked") === "checked") {
                                        dataString['institucional'] = "TRUE";
                                    }
                                    else {
                                        dataString['institucional'] = "FALSE";
                                    }

                                    dataString['id'] = calEvent.id;
                                    dataString['eventName'] = title;
                                    dataString['unidad'] = $("#cargos input[type='radio']:checked").val();
                                    dataString['startTime'] = $.fullCalendar.formatDate(start, "yyyy-MM-dd H:mm:ss");
                                    dataString['endTime'] = $.fullCalendar.formatDate(end, "yyyy-MM-dd H:mm:ss");

                                    if (startCompare > endCompare) {
                                        alert('Lo sentimos, no puedes crear un evento que finaliza antes de que empiece.');
                                    }
                                    else {
                                        calEvent.title = title;
                                        if (startTime !== $("#startTime").val()) {
                                            calEvent.start = start;
                                        }
                                        if (endTime !== $("#endTime").val()) {
                                            calEvent.end = end;
                                        }
                                        if (calEvent.allDay !== allDay) {
                                            calEvent.allDay = allDay;
                                        }
                                        if (i === 0)
                                        {
                                            i = 1;
                                            $.ajax({
                                                type: 'POST',
                                                dataType: 'html',
                                                url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/updateEvento',
                                                data: dataString,
                                                success: function(data, textStatus) {
                                                    dataString['invitados'] = {};
                                                    type = "";
                                                    calendar.fullCalendar('updateEvent', calEvent);
                                                }
                                            });

                                        }
                                        calendar.fullCalendar('unselect');
//                        calendar.fullCalendar('refetchEvents');
                                        //LIMPIO LOS TEXTOS
                                        $("#popup").hide();
                                        $("#invitarFuncionariosEvento").html('');
                                        $("#eventName").val('');
                                        $("#startTime").val('');
                                        $("#endTime").val('');
                                        $("#invitadosInternos").html("");
                                        $("#invitadosExternos").html("");
                                    }
                                }
                            });
                        }
                        else{
                            $("#editarInvitacion").show();
                            $("#deleteAsist").attr('onClick','javascript: noAsistira('+id+','+dataString['id']+');return false;');
                        }
                    }
                });
            },
            /*******************************************************************************************************/
            eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
                $("#editarInvitacion").hide();
                id = event.id;
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/verificar',
                    data: "evento=" + id,
                    success: function(data) {
                        if (data !== "false")
                        {
                            if (confirm("Haz movido el evento: " + event.title + " \r\n" +
                                    +dayDelta + " dias y " +
                                    minuteDelta + " minutos.\n\¿Está seguro del cambio?")) {
//                if(editar !== "false"){
                                $.ajax({
                                    type: 'POST',
                                    dataType: 'html',
                                    url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/updateEventoDrag',
                                    data: "id=" + event.id + "&dias=" + dayDelta + "&minutos=" + minuteDelta
                                });
//                }
                            }
                            else {
                                revertFunc();
                            }
                        }
                        else {
                            revertFunc();
                        }
                    }
                });
            }
        });

        $("#allDay").click(function() {
            $("#noAllDay").toggle();
            $("#startDay").toggle();
        });

        $("#calendariosDelegados").change(function() {
            if ($("#calendariosDelegados").val() !== '') {
                id = $("#calendariosDelegados").val();
            }
            else {
                id = undefined;
            }
            cargar_calendario(id);
        });
    });

    function calendario(invitacion, evento, decision)
    {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/eventoInvitado',
            data: "evento=" + evento + "&decision=" + decision + "&invitacion=" + invitacion,
            success: function(data, textStatus) {
                cargar_calendario();
                $("#" + invitacion).remove();
            }
        });
    }

    function interno()
    {
        $('#invitarFuncionariosEvento').html('<?php echo image_tag('icon/cargando.gif', array('size' => '25x25')); ?> Cargando...');
        $("#invitarFuncionariosEvento").load('<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/interno');
    }

    function externo()
    {
        $('#invitarFuncionariosEvento').html('<?php echo image_tag('icon/cargando.gif', array('size' => '25x25')); ?> Cargando...');
        $("#invitarFuncionariosEvento").load('<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/externo');
    }

    function eliminarInvitacion(evento, funcionario)
    {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/eliminarInvitacion',
            data: "evento=" + evento + "&funcionario=" + funcionario,
            success: function(data, textStatus) {
                $("#" + funcionario).remove();
            }
        });
    }
    
    function noAsistira(evento,funcionario)
    {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_herramientas_url'); ?>calendario/eliminarInvitacion',
            data: "evento=" + evento + "&funcionario=" + funcionario,
            success: function(data, textStatus) {
                $('#calendar').fullCalendar('removeEvents', evento);
                $("#editarInvitacion").hide();
            }
        });
    }

    function cancelar(cancelar)
    {
        if (cancelar == 1)
        {
            $("#popup").hide();
            type = "";
        }
        $("#editarInvitacion").hide();
        $("#invitarFuncionariosEvento").html('');
        $("#eventName").val('');
        $("#startTime").val('');
        $("#endTime").val('');
        $("#startDay").hide();
        $("#invitadosInternos").html("");
        $("#invitadosExternos").html("");

    }
</script>
<div class="wrapper">
    <!-- DELEGACIONES Y CONFIGURACIONES -->

    <table style="width: 99%;">
        <tr>
            <td><a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>delegar/accion?a=calendario"><img src="/images/icon/config_black.png">Configuraciones</a></td>
            <td style="text-align: right;">
                <?php if (isset($autorizados)) { ?>
                    <div id="delegaciones">
                        Calendarios Delegados: 
                        <select id="calendariosDelegados" name="calendariosDelegados">
                            <option value=""></option>
                            <?php foreach ($autorizados as $key => $value) { ?>
                                <option value="<?php echo $key; ?>" <?php
                                if ($key == $id) {
                                    echo "selected";
                                }
                                ?>><?php echo $value; ?></option>
                                    <?php } ?>
                        </select>
                    </div>
                <?php } ?>
            </td>
        </tr>
    </table>
    <!-- FIN DELEGACIONES Y CONFIGURACIONES -->
    <!-- INVITACIONES -->
    <?php if (count($invitaciones) > 0 && $aceptarEventos == "true") {
        ?>
        <div id="invitacionesCalendario">
            <?php
            foreach ($invitaciones as $invitacion) {
                if ($invitacion[3] == "TRUE") {
                    $fInicio = date("d/m/Y", strtotime($invitacion[1]));
                    $mensaje = "<b>" . $invitacion[3] . " " . $invitacion[4] . "</b> te ha invitado al evento: <b>" . $invitacion[0] . "</b>
                <br/>
                &emsp;&emsp;&ensp;Empieza: " . $fInicio . "<br/>
                &emsp;&emsp;&ensp;Este evento es todo el dia.";
                } else {
                    $fInicio = date("d/m/Y h:m A", strtotime($invitacion[1]));
                    $fFinal = date("d/m/Y h:m A", strtotime($invitacion[2]));
                    $mensaje = "<b>" . $invitacion[3] . " " . $invitacion[4] . "</b> te ha invitado al evento: <b>" . $invitacion[0] . "</b>
                <br/>
                &emsp;&emsp;&ensp;Empieza: " . $fInicio . "<br/>
                &emsp;&emsp;&ensp;Finaliza: " . $fFinal;
                }
                ?>
                <div id="<?php echo $invitacion[5]; ?>" style="background-color: #fff; border-bottom: 1px solid #000; border-radius: 10px; padding-left: 10px; padding-right: 10px;">
                    <img src="/images/icon/1day.png" style="position: relative; top: 2px;">
                    <?php
                    echo $mensaje;
                    ?>
                    <div style="position: relative; text-align: right; float: right; top: -30px;">
                        <span style="cursor: pointer;" onclick="calendario(<?php echo $invitacion[5]; ?>,<?php echo $invitacion[6]; ?>, 1);">Aceptar: <img src="/images/icon/tick.png" title="Aceptar"></span>
                        <br/>
                        <span style="cursor: pointer;" onclick="calendario(<?php echo $invitacion[5]; ?>,<?php echo $invitacion[6]; ?>, -1);">Rechazar: <img src="/images/icon/delete.png" title="Rechazar"></span>
                    </div>
                </div>
            <?php } ?>
        </div>
        <br/>
    <?php } ?>
    <!-- FIN INVITACIONES -->
    <div id='calendar'></div>
    <div id="detail"></div>
    <div id="popup"> 
        <table style="width: 90%; margin: auto;">
            <thead>
            <th colspan="4" style="background-color: #d0cece; width: 100%;">Agregar Evento</th>
            </thead>
            <tr>
                <td><br/>Descripción:</td>
                <td id="calendarDescription" colspan="2"><input name="eventName" style="width: 100%" id="eventName"></td>
            </tr>
            <tr id="noAllDay" style="display: none;">
                <td>Inicio:</td>
                <td><input type="text" id="startTime" name="startTime" readonly="readonly" /></td>
                <td><span style="margin-left: 20px;">Final: </span></td>
                <td><input type="text" id="endTime" name="endTime" readonly="readonly" /></td>
            </tr>
            <tr>
                <td colspan="2" id="startDay"></td>
            </tr>
            <tr>
                <td colspan="2"><input type="checkbox" name="allDay" value="allDay" id="allDay" checked>Todo el dia</td>
            </tr>
            <tr>
                <td colspan="2"><input type="checkbox" name="institucional" value="institucional" id="institucional" checked>Evento institucional</td>
            </tr>
            <?php
            if (count($unidad) > 1) {
                $i = 0;
                ?>
                <tr>
                    <td id="cargos" colspan="4">
                        <br/>
                        El <span class="gris_oscuro">SIGLAS</span> ha detectado que actualmente posee <?php echo count($unidad); ?> cargos. Por favor seleccione
                        la unidad funcional en la cual este evento va a ser asignado: <br/>
                        <?php foreach ($unidad as $key => $value) { ?>
                            <input id="<?php echo $value; ?>" type="radio" name="unidad" <?php if ($i == 0) echo "checked"; ?> value="<?php echo $key; ?>" ><?php echo $value; ?><br/>
                            <?php
                            $i++;
                        }
                        ?>
                    </td>
                </tr>
                <?php
            } else {
                foreach ($unidad as $key => $value) {
                    ?>
                    <tr><td id="cargos" style="display: none;"><input id="<?php echo $value; ?>" type="radio" name="unidad" checked value="<?php echo $key; ?>" ><?php echo $value; ?></td></tr>
                <?php
                }
            }
            ?>
            <tr>
                <td align="center"colspan="4">
                    <br/>
                    <button class="btn btn-default" name="submit" id="submit">Guardar</button>
                    <button class="btn btn-default" name="reset"  id="reset" onclick='cancelar();'>Resetear</button>
                    <button class="btn btn-default" name="cancel" id="cancel" onclick='cancelar(1);'>Cancelar</button>
                    <br/>
                    <button class="btn btn-default" onclick='interno();'>
                        <span><img style="position: relative; top: 2px;" src="/images/icon/group_add.png" /></span>
                        Invitar a personas internas
                    </button>
                    <button class="btn btn-default" onclick='externo();'>
                        <span><img style="position: relative; top: 2px;" src="/images/icon/group_go.png" /></span>
                        Invitar a personas externas
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div id="invitadosInternos" style="width: 100%;">

                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div id="invitadosExternos" style="width: 100%;">

                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" id="invitarFuncionariosEvento">

                </td>
            </tr>
        </table>
        <p><tt id="results"></tt></p>
    </div>
    <div id="editarInvitacion" class="popup">
        <table style="width: 90%; margin: auto;">
            <thead>
            <th style="background-color: #d0cece; width: 100%;">Editar Invitación</th>
            </thead>
            <tbody>
                <tr>
                    <td><br/><br/></td>
                </tr>
                <tr>
                    <td style="width: 90%; text-align: center;">
                        <button id="deleteAsist" name="submit" class="btn btn-default">Ya no Asistiré al Evento</button> &ensp;&ensp;&ensp;
                        <button class="btn btn-default" name="cancel" id="cancel" onclick='cancelar(1);'>Cancelar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<br/>
<br/>
<br/>
<br/>
<script type="text/javascript">
    $(function() {
        $('#startTime').datetimepicker({
            closeText: 'Hecho',
            currentText: 'Ahora',
            hourText: 'Hora:',
            minuteText: 'Minutos:',
            timeText: 'Hora:',
            controlType: 'select',
            timeFormat: 'hh:mm tt',
            dateFormat: 'yy-mm-dd',
            onClose: function(dateText, inst) {
                //INICIO
                dateTextS = $("#startTime").val();
                añoS = dateText[0] + dateText[1] + dateText[2] + dateText[3];
                mesS = dateText[5] + dateText[6];
                diaS = dateText[8] + dateText[9];
                horaS = parseInt(dateText[11] + dateText[12]);
                minS = dateText[14] + dateText[15];
                meridiemS = dateText[17] + dateText[18];
                if (meridiemS === "pm") {
                    horaS += 12;
                }
                startDatePicker = new Date(añoS, (mesS - 1), diaS, horaS, minS);
                startDatePicker = startDatePicker.getTime();

                dateTextE = $("#endTime").val();
                añoE = dateTextE[0] + dateTextE[1] + dateTextE[2] + dateTextE[3];
                mesE = dateTextE[5] + dateTextE[6];
                diaE = dateTextE[8] + dateTextE[9];
                horaE = parseInt(dateTextE[11] + dateTextE[12]);
                minE = dateTextE[14] + dateTextE[15];
                meridiemE = dateTextE[17] + dateTextE[18];
                if (meridiemE === "pm") {
                    horaE += 12;
                }
                endDatePicker = new Date(añoE, (mesE - 1), diaE, horaE, minE);
                endDatePicker = endDatePicker.getTime();
                if (startDatePicker > endDatePicker) {
                    $("#endTime").attr("style", "background-color: #FFF0F0;");
                }
                else {
                    $("#endTime").removeAttr("style");
                }
            }
        });
        $('#endTime').datetimepicker({
            closeText: 'Hecho',
            currentText: 'Ahora',
            hourText: 'Hora:',
            minuteText: 'Minutos:',
            timeText: 'Hora:',
            controlType: 'select',
            timeFormat: 'hh:mm tt',
            dateFormat: 'yy-mm-dd',
            onClose: function(dateText, inst) {
                //INICIO
                dateTextS = $("#startTime").val();
                añoS = dateTextS[0] + dateTextS[1] + dateTextS[2] + dateTextS[3];
                mesS = dateTextS[5] + dateTextS[6];
                diaS = dateTextS[8] + dateTextS[9];
                horaS = parseInt(dateTextS[11] + dateTextS[12]);
                minS = dateTextS[14] + dateTextS[15];
                meridiemS = dateText[17] + dateTextS[18];
                if (meridiemS === "pm") {
                    horaS += 12;
                }
                startDatePicker = new Date(añoS, (mesS - 1), diaS, horaS, minS);
                startDatePicker = startDatePicker.getTime();


                añoE = dateText[0] + dateText[1] + dateText[2] + dateText[3];
                mesE = dateText[5] + dateText[6];
                diaE = dateText[8] + dateText[9];
                horaE = parseInt(dateText[11] + dateText[12]);
                minE = dateText[14] + dateText[15];
                meridiemE = dateText[17] + dateText[18];
                if (meridiemE === "pm") {
                    horaE += 12;
                }
                endDatePicker = new Date(añoE, (mesE - 1), diaE, horaE, minE);
                endDatePicker = endDatePicker.getTime();
                if (startDatePicker > endDatePicker) {
                    $("#endTime").attr("style", "background-color: #FFF0F0;");
                }
                else {
                    $("#endTime").removeAttr("style");
                }
            }
        });
    });
</script>
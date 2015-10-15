<?php use_helper('jQuery'); ?>
<script>
    $(document).ready(function(){
        $('#archivo_serie_documental_nombre').val('<?php echo $serie->getNombre(); ?> Duplicado');
    });
    
    function saveDuplicada() {
        var pass= false;
        if ($('#archivo_serie_documental_nombre').val() === '<?php echo $serie->getNombre(); ?> Duplicado') {
            if(confirm('¿Esta seguro de no renombrar la serie documental?')) {
                pass= true;
            }
        }else {
            pass= true;
        }

        if(pass) {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>serie_documental/duplicada',
                type:'POST',
                dataType:'html',
                data: $('#form_duplicada').serialize(),

                success:function(data, textStatus){
                    $('#div_flashes').html(data);
                    $('#tr_serie_documental_<?php echo $sf_user->getAttribute('serie_documental_id'); ?>').remove();

                    $("#content_window_right").animate({right:"-=892px"},1000);
                    $("#header_window_right").animate({right:"-=892px"},1000);
                    $("#div_wait_window_right").hide();

                    $('#content_window_right').html('');
                }});
        }
    };
</script>

<div id="sf_admin_container" style="width: 100%;">
    <h1>Duplicado de Serie Documental</h1>

    <div id="sf_admin_content">
        <div class="sf_admin_form">

        <form id="form_duplicada">
            <input type="hidden" name="serie_documental_id" value="<?php echo $serie_documental_id; ?>"/>

            <font style="color: #202020; font-weight: 500">Su nueva serie documental, que será un duplicado de esta, debería tener un nombre distinto, por lo que sugerimos escriba uno diferente</font><br/><br/>

            <div class="sf_admin_form_row sf_admin_text trans">
                <div>
                    <label>Nombre de Serie</label>
                    <div class="content">
                        <?php echo $form['nombre']->renderError(); ?>
                        <?php echo $form['nombre']; ?>
                    </div>
                </div>
            </div>
            <font style="color: red; font-size: 11px">*</font>&nbsp;<font style="font-size: 11px; color: #666">La serie documental será duplicada incluyendo sus descriptores, tipolog&iacute;as, etiquetas y secciones.<br/>Sus metodos de almacenamientos y estantes asignados <b>NO</b> ser&aacute;n duplicados.</font>

            <br/>
            <ul class="sf_admin_actions trans">
            <li class="sf_admin_action_save">
                <input id="guardar" type="button" value="Duplicar" onclick="saveDuplicada(); return false;">
            </li>
            </ul>
        </form>
        </div>
    </div>
</div>
<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){ 
        $("form").submit(function() {

            if($("input[name='archivo_documento[copia_digital]']:checked").val()=='f') {
                $("#documento_cambiado").val($("a.elimina").attr("id"));
            } else {
               if($('#archivo_documento_ruta').val()==''){
                   alert('Seleccione el archivo que desea agregar');
                   return false;
               }
            }
        });
    });

    function fn_cambiar_fil(){
        $("#documento_cambiado").val($("a.elimina").attr("id"));

        $("a.elimina").parent().parent().fadeOut(600, function(){
            $(this).remove();
        })

        setTimeout(function(){
            cadena = '<input type="file" id="archivo_documento_ruta" name="archivo_adjunto"/>';
            $("#div_file").append(cadena);
        },601);
    };
    
    function fn_open_fil(){
        $('#div_input_file').slideDown('slow');
    };
    function fn_close_fil(){
        $('#div_input_file').slideUp('slow');
    };
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_archivo_adjunto">
    <div>
        <label for="archivo_documento_archivo_adjunto">Documento</label>
        <div id="div_file" class="content">
            ¿Archivará el documento fisico?&nbsp;&nbsp;
            <input type="radio" name="archivo_documento[copia_fisica]" value="t" checked/> Si
            <input type="radio" name="archivo_documento[copia_fisica]" value="f" <?php if(!$form->isNew()) { if($form['copia_fisica']->getValue()==FALSE) echo 'checked'; } ?>/> No
            <br/>
            ¿Archivará el documento digital?&nbsp;
            <input type="radio" name="archivo_documento[copia_digital]" value="t" onclick="javascript:fn_open_fil()" checked/> Si
            <input type="radio" name="archivo_documento[copia_digital]" value="f" onclick="javascript:fn_close_fil()" <?php if(!$form->isNew()) { if($form['copia_digital']->getValue()==FALSE) echo 'checked'; } ?>/> No
            <div id="div_input_file" <?php if(!$form->isNew()) { if($form['copia_digital']->getValue()==FALSE) echo 'style="display: none;"'; } ?>>
                <br/>
                <?php if($form->isNew()) { ?>
                    <input type="file" id="archivo_documento_ruta" name="archivo_adjunto"/>
                <?php } else { 
                    $documento_original = Doctrine::getTable('Archivo_Documento')->find($form['id']->getValue());
                    
                    if($documento_original->getRuta()==NULL) {
                ?>
                    <input type="file" id="archivo_documento_ruta" name="archivo_adjunto"/>
                <?php } else { ?>
                    <table>
                        <tr>
                            <td>
                                <a href="/uploads/archivo/<?php echo $documento_original->getRuta(); ?>"><?php echo $documento_original->getNombreOriginal(); ?></a>
                            </td>
                            <td>
                                <a class='elimina' onclick="javascript:fn_cambiar_fil()" style='cursor: pointer;' id="<?php echo $form['id']->getValue(); ?>">
                                    <img src='/images/icon/delete.png'/>
                                </a>
                            </td>
                        </tr>
                    </table>
                <?php } } ?>
            </div>
        </div>
        <input id="documento_cambiado" name="file_change" type="hidden" value="."/>
        <div>
            <div class="help">Seleccione el archivo que desea agregar</div>
        </div>
    </div>
</div>
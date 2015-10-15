<script type="text/javascript" src="/sfDependentSelectPlugin/js/SelectDependiente.js"></script>
<?php include(sfConfig::get("sf_root_dir").'/apps/registro/modules/empresa/lib/validator_empresa.php'); ?>

<script>
    function guardar_empresa(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>empresa/saveEmpresa',
            type:'POST',
            dataType:'html',
            data: $('#form_empresa').serialize(),
            beforeSend: function(Obj){
                $('#save_empresa_msj').html('<?php echo image_tag('icon/cargando.gif', array('style'=>'width: 16px; vertical-align: middle')); ?> Guardando empresa...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(2);
            }});
    }
    
    function buscarEmpresa() {
        var rif1= $('#rif_1').val();
        var rif2= $('#rif_2').val();
        var rif3= $('#rif_3').val();

        if(rif1 === '' || rif2 === '' || rif3 === '') {
            alert('Debe introducir el Rif');
        }else {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>empresa/buscarEmpresa',
                type:'POST',
                dataType:'json',
                data:'rif1='+rif1+'&rif2='+rif2+'&rif3='+rif3,
                beforeSend: function(Obj){
                    $('#buscar_empresa_msj').html('<?php echo image_tag('icon/cargando.gif', array('style'=>'width: 16px; vertical-align: middle')); ?> Buscando empresa ...');
                },
                success:function(json, textStatus){
                    if(json.status === 'ok') {
                        $('#buscar_empresa_msj').html('');
                        var rif_completo= $('#rif_1').val()+'-'+$('#rif_2').val()+'-'+$('#rif_3').val();
                        $('#empresas_empresa_rif').val(rif_completo);
                        $('#empresas_empresa_id').val(json.content.id);
                        
                        $('#empresas_empresa_nombre').val(json.content.nombre);
                        $('#empresas_empresa_siglas').val(json.content.siglas);
                        $('#empresas_empresa_empresa_tipo_id option[value='+json.content.empresaTipoId+']').attr('selected', 'selected');
                        
                        $('#empresas_empresa_estado_id option[value='+json.content.estadoId+']').attr('selected', 'selected');
                        $('#empresas_empresa_estado_id option').trigger('change', function() { });

                        $('#empresas_empresa_municipio_id option[value='+json.content.municipioId+']').attr('selected', 'selected');
                        $('#empresas_empresa_municipio_id option').trigger('change', function() { });
                        $('#empresas_empresa_parroquia_id option[value='+json.content.parroquiaId+']').attr('selected', 'selected');
                        
                        $('#empresas_empresa_dir_av_calle_esq').val(json.content.dirAvCalleEsq);
                        $('#empresas_empresa_dir_edf_torre_anexo').val(json.content.dirEdfTorreAnexo);
                        $('#empresas_empresa_dir_piso').val(json.content.dirPiso);
                        $('#empresas_empresa_dir_urbanizacion').val(json.content.dirUrbanizacion);
                        $('#empresas_empresa_ciudad').val(json.content.dirCiudad);
                        $('#empresas_empresa_telf_uno').val(json.content.telfUno);
                        $('#empresas_empresa_telf_dos').val(json.content.telfDos);
                        $('#empresas_empresa_email_principal').val(json.content.emailPrincipal);
                        
                        $('.to_hid_empresa').show('fast');
                        
                        //BLOQUEO DE CAMPOS
                        $('#empresas_empresa_nombre').attr('readonly', 'readonly');
                        $('#empresas_empresa_empresa_tipo_id').attr('readonly', 'readonly');
                    }else {
                        if(json.status === 'existe') {
                            $("#buscar_empresa_msj").html('<x id="inner_msj"><font style="color: red">La empresa ya esta inscrita, puede continuar cargando la información desde el lista de empresas</font></x>');
                            setTimeout(function(){
                                $("#inner_msj").fadeOut(1000);
                            }, 6000);
                            
                            $('.to_hid_empresa').hide('fast');
                        }else {
                            $('.to_hid_empresa').show('fast');
                            var rif_completo= $('#rif_1').val()+'-'+$('#rif_2').val()+'-'+$('#rif_3').val();
                            $('#empresas_empresa_rif').val(rif_completo);
                            //REINICIO DE FORM
                            $('#empresas_empresa_id').val('');

                            $('#empresas_empresa_nombre').val('');
                            $('#empresas_empresa_siglas').val('');
                            $('#empresas_empresa_dir_av_calle_esq').val('');
                            $('#empresas_empresa_dir_edf_torre_anexo').val('');
                            $('#empresas_empresa_dir_piso').val('');
                            $('#empresas_empresa_dir_urbanizacion').val('');
                            $('#empresas_empresa_ciudad').val('');
                            $('#empresas_empresa_telf_uno').val('');
                            $('#empresas_empresa_telf_dos').val('');
                            $('#empresas_empresa_email_principal').val('');
                            
                            //DESBLOQUEO DE CAMPOS
                            $('#empresas_empresa_nombre').attr('readonly', false);
                            $('#empresas_empresa_empresa_tipo_id').attr('readonly', false);

                            $("#buscar_empresa_msj").html('<x id="inner_msj"><font style="color: green">La empresa no esta registrada aún</font></x>');
                            setTimeout(function(){
                                $("#inner_msj").fadeOut(1000);
                            }, 3000);
                        }
                    }
                }
            });
        }
    }
</script>
    
<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>
<form id="form_empresa">
    <fieldset>
        <h2>Datos</h2>
        <div class="sf_admin_form_row">
            <div>
                <label>R.I.F.</label>
                <div class="content">
                    <select id="rif_1" name="rif_1">
                        <option value="J">J</option>
                        <option value="G">G</option>
                    </select>-
                    <input id="rif_2" name="rif_2" type="text" size="8" maxlength="8"/>-
                    <input id="rif_3" name="rif_3" type="text" size="1" maxlength="1"/>&nbsp;&nbsp;&nbsp;
                    <a href="#" onClick="javascript: buscarEmpresa(); return false;"><?php echo image_tag('icon/2execute.png', array('style'=>'vertical-align: middle')) ?></a>
                    <x id="buscar_empresa_msj"></x>
                    <input id="empresas_empresa_rif" name="empresas_empresa[rif]" type="hidden" value="" />
                    <input id="empresas_empresa_id" name="empresas_empresa[id]" type="hidden" value="" />
                </div>
                <div class="help">Indique el n&uacute;mero de registro de informaci&oacute;n fiscal</div>
            </div>
        </div>
        <div class="sf_admin_form_row to_hid_empresa" style='display: none'>
            <div>
                <label>Entidad de trabajo</label>
                <div class="content">
                    <input id="empresas_empresa_nombre" name="empresas_empresa[nombre]" type="text" size="50"/>
                </div>
                <div class="help">Nombre o razon social de la empresa a inscribir</div>
            </div>
        </div>
        <div class='to_hid_empresa' style='display: none'>
            <div class="sf_admin_form_row">
                <div>
                    <label>Siglas</label>
                    <div class="content">
                        <?php echo $form['siglas'] ?>
                    </div>
                    <div class="help">En caso de no tener, use un nombre reducido</div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Tipo de empresa</label>
                    <div class="content">
                        <?php echo $form['empresa_tipo_id'] ?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <div class='to_hid_empresa' style='display: none'>
        <fieldset>
            <h2>Direcci&oacute;n</h2>
            <div class="sf_admin_form_row">
                <div>
                    <label>Estado</label>
                    <div class="content">
                        <?php echo $form['estado_id']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Municipio</label>
                    <div class="content">
                        <?php echo $form['municipio_id']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Parroquia</label>
                    <div class="content">
                        <?php echo $form['parroquia_id']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Av/Calle/Esq</label>
                    <div class="content">
                        <?php echo $form['dir_av_calle_esq']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Edif/Torre/Anexo</label>
                    <div class="content">
                        <?php echo $form['dir_edf_torre_anexo']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Piso</label>
                    <div class="content">
                        <?php echo $form['dir_piso']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Urbanizaci&oacute;n</label>
                    <div class="content">
                        <?php echo $form['dir_urbanizacion']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Ciudad</label>
                    <div class="content">
                        <?php echo $form['dir_ciudad']; ?>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <h2>Contacto</h2>
            <div class="sf_admin_form_row">
                <div>
                    <label>Telf. principal</label>
                    <div class="content">
                        <?php echo $form['telf_uno']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Telf. secundario</label>
                    <div class="content">
                        <?php echo $form['telf_dos']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Correo</label>
                    <div class="content">
                        <?php echo $form['email_principal']; ?>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <div class="sf_admin_form_row">
                <button id="guardar_empresa" style="height: 35px">
                    <strong>Guardar y continuar</strong>&nbsp;<?php echo image_tag('icon/execute.png', array('style' => 'vertical-align: middle')) ?>
                </button>
                <x id="save_empresa_msj"></x>
            </div>
        </fieldset>
    </div>
</form>
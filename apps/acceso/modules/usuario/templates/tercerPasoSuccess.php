<?php use_helper('I18N', 'Date') ?>

<?php use_helper('jQuery'); ?>

<script>
    function submitform(ci, old_email, old_telf) {
        var pross= true;
        if(old_email != '') {
            if($.trim($('#email').val()) != old_email) {
                if(saveEmail(ci)!= false)
                    pross= true;
                else
                    pross= false;
            }
        }else {
            if(saveEmail(ci)!= false)
                pross= true;
            else
                pross= false;
        }
        if ($('#celphone').attr('checked')){
            if(old_telf != '') {
                if($('#codigo').val()+$('#telf_num').val() != old_telf) {
                    if(saveTelf(ci)!= false)
                        pross= true;
                    else
                        pross= false;
                }
            }else {
                if(saveTelf(ci)!= false)
                    pross= true;
                else
                    pross= false;
            }
        }
        
        if(pross){
            document.forms.SignupForm.submit();
            jQuery('#SaveAccount').html('Espere...');
        }
    }
    
    function apperSave(op) {
        $('#filesave_'+op).attr("src", "/images/icon/filesave.png");
        $('#filesave_'+op).show();
    }

    function saveEmail(ci) {
        var filter= /^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,4}$/i;
        email= $.trim($('#email').val());
        if(filter.test(email)){
            $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/changeEmailorTelf',
                    type:'POST',
                    dataType:'html',
                    data:'email='+email+'&cedula='+ci+'&act=email',
                    success:function(data, textStatus){
                        $('#email').val(data);
                        $('#email').blur();
                        document.SignupForm.email.disabled= true;
                        $(function(){
                            $('#filesave_email').attr("src", "/images/icon/save.png");
                            setTimeout(
                                function(){
                                    $('#filesave_email').fadeOut(1000);
                                    document.SignupForm.email.disabled= false;
                                    $('#SaveAccount').show();
                                }, 2000);
                        });
                }});
        }else {
            alert('El email no es correcto');
            return false;
        }
    }

    function saveTelf(ci) {
        telf= $('#codigo').val()+$('#telf_num').val();
        if(telf.length == 11){
            $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/changeEmailorTelf',
                    type:'POST',
                    dataType:'html',
                    data:'telf='+telf+'&cedula='+ci+'&act=telefono',
                    success:function(data, textStatus){
                        $('#telf_num').blur();
                        document.SignupForm.telf_num.disabled= true;
                        $(function(){
                            $('#filesave_tlf').attr("src", "/images/icon/save.png");
                            setTimeout(
                                function(){
                                    $('#filesave_tlf').fadeOut(1000);
                                    document.SignupForm.telf_num.disabled= false;
                                }, 2000);
                        });
                }});
        }else {
            alert('El número de telefono no es correcto');
            return false;
        }
    }

    function fn_conmutar_sms(){
        if ($('#celphone').attr('checked')){
            $("#div_celphone").show();
        }else{
            $("#div_celphone").hide();
        }
    }
</script>

<p class="help">Su usuario es: <font style="font-weight: bold; font-size: 16px"><?php echo $usuario_clavetemporal->getNombre(); ?></font>&nbsp;<font style="color: red; vertical-align: top">*</font></p>
<input name="email" id="email" type="text" value="<?php echo $funcionario->getEmailPersonal(); ?>" onFocus="javascript: apperSave('email')" <?php echo ($funcionario->getEmailValidado()? 'disabled':'' ) ?>/>&nbsp;
<img id="filesave_email" src="/images/icon/filesave.png" onClick="javascript:saveEmail(<?php echo $funcionario->getCi() ?>)" />
<p class="help"><br/><?php
    if($funcionario->getEmailValidado()) {
        echo 'Este correo ya fue validado, no puede modificarlo.';
    }else {
        if($funcionario->getEmailPersonal()== '') {
            echo 'Indique correo electrónico personal. Su contraseña temporal de ingreso será enviada a este correo';
        }else {
            echo 'Modifique su correo si no es correcto. Su contraseña temporal de ingreso será enviada a este correo';
        }
    } ?></p>
<?php
$num= '';
$sf_sms = sfYaml::load(sfConfig::get("sf_root_dir") . "/config/siglas/sms.yml");
if ($sf_sms['activo'] == true && $sf_sms['aplicaciones']['mensajes']['activo'] == true) {
    if ($funcionario->getTelfMovil()!= '') {
        $cod= substr($funcionario->getTelfMovil(), 0, 4);
        $num= substr($funcionario->getTelfMovil(), -7);
    }
    ?><input type="checkbox" id="celphone" name="celphone" onClick="javascript:fn_conmutar_sms();" />&nbsp;<font style="font-size: 12px; color: #333">Enviar tambi&eacute;n a mi tel&eacute;fono celular</font>
    <br/>
    <div id="div_celphone" style="display: none; width: 294px; padding-left: 17px">
        <select name="codigo" id="codigo" onChange="javascript: apperSave('tlf')" >
            <option value="0412" <?php echo ($cod== '0412') ? 'selected':''; ?>>0412</option>
            <option value="0416" <?php echo ($cod== '0416') ? 'selected':''; ?>>0416</option>
            <option value="0426" <?php echo ($cod== '0426') ? 'selected':''; ?>>0426</option>
            <option value="0414" <?php echo ($cod== '0414') ? 'selected':''; ?>>0414</option>
            <option value="0424" <?php echo ($cod== '0424') ? 'selected':''; ?>>0424</option>
        </select>&nbsp;
        <input type="text" name="telf_num" id="telf_num" maxlength="7" size="7" style="width: 60px !important; height: 10px" value="<?php echo $num ?>" onFocus="javascript: apperSave('tlf')" />&nbsp;
        <img id="filesave_tlf" src="/images/icon/filesave.png" onClick="javascript:saveTelf(<?php echo $funcionario->getCi() ?>)" />
    </div><br/>
<?php } ?>
    <p class="help">(<font style="color: red">*</font>)Se enviar&aacute; una clave temporal a su correo para ingresar en el SIGLAS, use su Usuario y Clave temporal en la pantalla de Autenticaci&oacute;n del sistema.</p>
    <input type="hidden" name="act" value="primera_vez" />
    <a id="SaveAccount" class="next" href="javascript: submitform('<?php echo $funcionario->getCi()?>','<?php echo $funcionario->getEmailPersonal()?>','<?php echo $funcionario->getTelfMovil() ?>')" style="cursor: pointer; <?php echo (($funcionario->getEmailPersonal() != '')? 'display: block' : 'display: none'); ?>" >Enviar</a>
<?php
$sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
$informatica = Doctrine::getTable('Organigrama_Unidad')
        ->find($sf_oficinasClave['informatica']['seleccion']);
?>

<?php 
    $app_call_firma = 'funcionarios'; // VARIABLE QUE VERIFICA SI ESTA ACTIVA LA FIRMA PARA LA APLICACION. VERIFICAR PERMISOS EN config/siglas/firmaElectronica.yml
    include(sfConfig::get("sf_root_dir").'/lib/partial/certified_signature.php'); 
?>

<script>
    var PROCESAR_PAQUETE = false;
    var PAUSAR_RECEPCION = false;

    
    function recibir_paquete(){
        if(PAUSAR_RECEPCION == false){
            if($('#signature_packet').val()!=''){
                PROCESAR_PAQUETE = true;
                PAUSAR_RECEPCION = true;
            }
        }
    }
    setInterval("recibir_paquete()", 10000);
    
    function procesar_paquete(){
        if (PROCESAR_PAQUETE == true){
            PROCESAR_PAQUETE = false;
            $('#div_preparar_firma').html('<?php echo image_tag('other/siglas_wait.gif', array('size'=>'64x64')); ?> <br/> Procesando...');

            $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: '<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo_certificado/procesarPaquete',
                    data: {signature_packet: $('#signature_packet').val()},
                    success:function(data, textStatus){
                        $("#div_preparar_firma").html(data);
                        $('#div_form_actions').show();
                    }
                })
        }
    }
    setInterval("procesar_paquete()", 10000);
</script>

<div style="width: 780px; padding: 20px; text-align: justify;">
    Registrar un certificado electronico, es una labor que debe realizarse por funcionarios con perfil "root" y
    debe estar presente el funcionario a quien se le registra el mismo, ya que se le sera solicitado
    asi como su "pin" o clave de uso.<br/><br/>
    Tenga presente que la verificacion de este certificado queda bajo la resposabilidad del funcionario que lo registra;
    este evento sera notificado mediante correo electronico, sms y SIGLAS a la maxima autoridad de 
    <?php echo $informatica->getNombre(); ?>, al funcionario que registra y al funcionario que pertenece el certificado.
</div>
<hr/>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_certificado">
    <div>
        <label for="funcionarios_funcionario_cargo_certificado_certificado">Certificado<br/>Electronico</label>
        <div class="content" id="div_preparar_firma">
            <a href="#" onclick="prepare_signature('SIGLAS','funcionario_cargo_certificado/prepararFirma','no_submit'); recibir_paquete(); return false;" style="text-decoration: none">
            <?php echo image_tag('icon/key_new.gif'); ?><br/>Buscar y abrir
            </a>
        </div>
    </div>
</div>
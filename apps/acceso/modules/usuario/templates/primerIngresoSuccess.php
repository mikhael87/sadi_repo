<?php use_helper('jQuery'); ?>

<style type="text/css">
    #mainx { width:460px; margin: 0px auto; border:solid 1px #b2b3b5; -moz-border-radius:10px; padding:20px; background-color:#f6f6f6;}
    #header_in { width:460px; text-align:center; margin:0 auto 0 auto }
    fieldset { border:none; width:320px;}
    legend { font-size:18px; margin:0px; padding:10px 0px; color:#b0232a; font-weight:bold;}
    label { display:block; margin:15px 0 5px;}
    input[type=text], input[type=password] { width:300px; padding:5px; border:solid 1px #000;}
    .prev, .next { background-color:#b0232a; padding:5px 10px; color:#fff; text-decoration:none;}
    .prev:hover, .next:hover { background-color:#000; text-decoration:none;}
    .prev { float:left;}
    .next { float:right;}
    #steps { list-style:none; width:460px; overflow:hidden; margin:0px; padding:0px; margin:0 auto 0 auto }
    #steps li {font-size:24px; float:left; padding:10px; color:#b0b1b3; list-style:none}
    #steps li span {font-size:11px; display:block;}
    #steps li.current { color:#000;}
    #makeWizard { background-color:#b0232a; color:#fff; padding:5px 10px; text-decoration:none; font-size:18px;}
    #makeWizard:hover { background-color:#000;}
    .help {font-size: 11px; color: #666; text-align: justify}
    .SignupForm fieldset {margin:0 auto 0 auto}
    .welcome_logo { margin-left: 30px}
    #text_welcome { padding-left: 45px; padding-top: 35px}
    .cedula { width: 280px !important; }
    #email { width: 280px !important; }
    #filesave_email { display: none; vertical-align: middle; cursor: pointer }
    #filesave_tlf { display: none; vertical-align: middle; cursor: pointer }
    .find_icon { vertical-align: middle; cursor: pointer }
    .user_table_paso_dos td { padding: 5px }
    .user_off { color: #CCCCFF; }
    .user_on_true { color: #666; font-weight: bold; background-image: url("../../images/icon/tick.png"); background-repeat: no-repeat; background-position: left }
    .user_on_false {color: #666; background-image: url("../../images/icon/delete_old.png"); background-repeat: no-repeat; background-position: left}
</style>
<script type="text/javascript">
    $(document).ready(function(){
//        $("#SignupForm").validate({
//        });
//        
//        $("#email").addClass('required');
//        $("#email").addClass('email_valid');
//
//        $.validator.addMethod(
//            "email_valid",
//            function(value, element) {
//                return value.match(/^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,4}$/i);
//            },
//            "Por favor, introduzca la fecha con este formato dd/mm/aaaa"
//        );
        document.SignupForm.onkeypress = acceptNum;
        document.onkeypress = stopRKey;
        $("#SignupForm").formToWizard({ submitButton: 'SaveAccount' });
        $('#step0Next').hide();
        $('#step1Next').click(function () {
            cedula_hid= $('#cedula_hid').val();
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/tercerPaso',
                type:'POST',
                dataType:'html',
                data:'cedula='+cedula_hid,
                beforeSend: function(Obj){
                              $('#spinner').show();
                        },
                success:function(data, textStatus){
                    jQuery('#div_tercer_paso').html('');
                    jQuery('#div_tercer_paso').append(data);
            }});
        });
    });

    function stopRKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
    }

    function find_user() {

        var re= /^[0-9]*$/;
        cedula= $.trim($('#cedula').val());
        if (cedula != '' && cedula.length <= '10' ) {
            if(re.test(cedula)){
                $.ajax({
                        url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/findUser',
                        type:'POST',
                        dataType:'html',
                        data:'cedula='+cedula,
                        success:function(data, textStatus){
                            $('#exist').addClass('user_off');
                            $('#noexist').addClass('user_off');
                            $('#exist').removeClass('user_on_true');
                            $('#noexist').removeClass('user_on_false');
                            $('#non_exist_msj').hide();
                            if(data != '') {
                                $('#exist').removeClass('user_off');
                                $('#exist').addClass('user_on_true');
                                $('#step0Next').show();
                                $('#non_exist_msj').hide();
                            }else {
                                $('#noexist').removeClass('user_off');
                                $('#noexist').addClass('user_on_false');
                                $('#non_exist_msj').show();
                                $('#step0Next').hide();
                            }
                            jQuery('#div_segundo_paso').html('');
                            jQuery('#div_segundo_paso').append(data);
                        }});
            }else{
                alert('Use solo Números (sin puntos)');
            }
        }
    }
    
    function acceptNum(evt){
        var nav4 = window.Event ? true : false;
        var key = nav4 ? evt.which : evt.keyCode;
        if(key==13){
            find_user();
        }
    }
    
    (function($) {
        $.fn.formToWizard = function(options) {
            options = $.extend({
                submitButton: ""
            }, options);

            var element = this;

            var steps = $(element).find("fieldset");
            var count = steps.size();
            var submmitButtonName = "#" + options.submitButton;
            $(submmitButtonName).hide();

            // 2
            $(element).before("<ul id='steps'></ul>");

            steps.each(function(i) {
                $(this).wrap("<div id='step" + i + "'></div>");
                $(this).append("<p id='step" + i + "commands'></p>");

                // 2
                var name = $(this).find("legend").html();
                $("#steps").append("<li id='stepDesc" + i + "'>Paso " + (i + 1) + "<span>" + name + "</span></li>");

                if (i == 0) {
                    createNextButton(i);
                    selectStep(i);
                }
                else if (i == count - 1) {
                    $("#step" + i).hide();
                    createPrevButton(i);
                }
                else {
                    $("#step" + i).hide();
                    createPrevButton(i);
                    createNextButton(i);
                }
            });

            function createPrevButton(i) {
                var stepName = "step" + i;
                $("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Prev' class='prev'>< Anterior</a>");

                $("#" + stepName + "Prev").bind("click", function(e) {
                    $("#" + stepName).hide();
                    $("#step" + (i - 1)).show();
                    $(submmitButtonName).hide();
                    selectStep(i - 1);
                });
            }

            function createNextButton(i) {
                var stepName = "step" + i;
                $("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Next' class='next'>Siguiente ></a>");

                $("#" + stepName + "Next").bind("click", function(e) {
                    $("#" + stepName).hide();
                    $("#step" + (i + 1)).show();
                    if (i + 2 == count)
                        $(submmitButtonName).show();
                    selectStep(i + 1);
                });
            }

            function selectStep(i) {
                $("#steps li").removeClass("current");
                $("#stepDesc" + i).addClass("current");
            }

        }
    })(jQuery);
</script>

<br/><br/><br/><br/>

    <div id="main">
        <div id="header_in" style="position: relative; text-align: left; height: 100px;">
            <div style="position: absolute; top: 0px; left: 0px; z-index: 0;">
                <?php echo image_tag('/images/other/welcome_fondo.png', array('style'=>'height: 100px; width: 460px;')) ?>
            </div>
            <div style="position: absolute; top: 0px; left: 0px; z-index: 1;">
                <table style="width: 460px; border: 0px;">
                    <tr>
                        <td style="width: 120px;">
                            <div style="position: relative;">
                                <div style="position: absolute; top: -30px;">
                                    <?php echo image_tag('/images/other/welcome_logo.png', array('height' => '120')) ?>
                                </div>
                                <div style="position: absolute; right: -340px; top: 55px;">
                                    <?php echo link_to('Ir al inicio', sfConfig::get('sf_app_acceso_url').'usuario/session') ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div id="text_welcome">
                                <font style="font-size: 30px; font-weight: bolder">BIENVENIDOS</font><br/>
                                <font style="font-size: 17px; font-weight: bolder">3 Sencillos pasos para ingresar.</font>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br/><br/>
        <form id="SignupForm" class="SignupForm" method="post" name="SignupForm" action="<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/claveTemporal">
        <fieldset>
            <legend>Comprobaci&oacute;n&nbsp;&nbsp;</legend>
            <label for="cedula">N&uacute;mero de c&eacute;dula</label>
            <input id="cedula" class="cedula" name="cedula" type="text" size="8" />
            <img src="/images/icon/find24.png" class="find_icon" onClick="javascript: find_user()" />
            <p class="help"><br/>Indique n&uacute;mero de c&eacute;dula para verificar su existencia en el SIGLAS. Ejm.: 24423423</p>
            <div style="text-align: left">
                <p id="exist" class="user_off">&nbsp;&nbsp;&nbsp;&nbsp;Usuario existente</p>
                <p id="noexist" class="user_off">&nbsp;&nbsp;&nbsp;&nbsp;Usuario no existente</p>
                <p class="help" id="non_exist_msj" style="padding-left: 19px; display: none">Pongase en contacto con el administrador SIGLAS para registrarse.</p>
            </div>
        </fieldset>
        <fieldset>
            <legend>¿Es usted este Usuario?</legend>
            <div id="div_segundo_paso"></div>
        </fieldset>
        <fieldset>
            <legend>Env&iacute;o de Clave</legend>
            <div id="div_tercer_paso">
                <div id="spiner" style="text-align: center"><?php echo image_tag('/images/icon/spinner.gif'); ?></div>
            </div>
        </fieldset>
        </form>
    </div>

<?php use_helper('jQuery'); ?>


<?php
$datos_iniciales = Doctrine::getTable('Funcionarios_Funcionario')->find($sf_user->getAttribute('funcionario_id'));
$estados = Doctrine::getTable('Public_Estado')->findAll();
?>


<script>   
    $(document).ready(function()
    {   
        $('.caja').css({
           position:'absolute',
           left: ($(window).width() - $('.caja').outerWidth())/2,
           top: ($(window).height() - $('.caja').outerHeight())/2
        });

        $("#actualizar_datos_div").slideDown();
        
        //VALIDACIONES JQUERY
          $('#formactu').validate({
           rules: {
           'datos_iniciales_personales[cedula]': { required: true, minlength: 6, maxlength: 8, digits: true},
           'datos_iniciales_personales[primer_nombre]': { required: true,  sololetras: true },
           'datos_iniciales_personales[segundo_nombre]': { sololetras: true },
           'datos_iniciales_personales[primer_apellido]': { required: true,  sololetras: true },
           'datos_iniciales_personales[segundo_apellido]': { sololetras: true },
           'datos_iniciales_personales[f_nacimiento][day]': 'required',
           'datos_iniciales_personales[f_nacimiento][month]': 'required',
           'datos_iniciales_personales[f_nacimiento][year]': 'required',
           'datos_iniciales_personales[estado_nacimiento]': 'required',
           'datos_iniciales_personales[sexo]': 'required',
           'datos_iniciales_personales[estado_civil]': 'required'
           },
       messages: {
           'datos_iniciales_personales[cedula]': { required: 'Requerido', minlength: 'No menor a 6 dígitos', maxlength: 'No mayor a 8 dígitos', digits: 'Ingrese solo números' },
           'datos_iniciales_personales[primer_nombre]': { required: 'Requerido' },
           'datos_iniciales_personales[primer_apellido]': { required: 'Requerido' },
           'datos_iniciales_personales[f_nacimiento][day]': '<font style="font-weight: bold; font-size: 15px">*</font>',
           'datos_iniciales_personales[f_nacimiento][month]': '<font style="font-weight: bold; font-size: 15px">*</font>',
           'datos_iniciales_personales[f_nacimiento][year]': '<font style="font-weight: bold; font-size: 15px">*</font>',
           'datos_iniciales_personales[estado_nacimiento]': 'Requerido',
           'datos_iniciales_personales[sexo]': 'Requerido',
           'datos_iniciales_personales[estado_civil]': 'Requerido'
       }
    });

    jQuery.validator.addMethod("sololetras", function(value, element) {
        return this.optional(element) || /^[a-zA-Z áéíóúAÉÍÓÚÑñ]+$/i.test(value);
    }, "Ingrese solo letras");

    });
//
//    function cerrar_actualizar_datos(){
//        $("#actualizar_datos_div").slideUp();
//    };

</script>

<style>
    label.error {
        margin-left: 10px;
        color: #DD3333;
    }
</style>

<div style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background-color: black; opacity: 0.4; filter:alpha(opacity=40); z-index: 2000;"></div>

<div id="actualizar_datos_div" class="caja" style="padding: 4px; border-radius: 10px 10px 10px 10px; background-color: #000; z-index: 2001; position: absolute; width: 550px; height:450px; display: none;">
    <div class="inner" style="border-radius: 8px 8px 8px 8px; background-color: #ebebeb; z-index: 2001; height:450px;">
<!--        <div style="top: -15px; left: -15px; position: absolute;">
            <a href="#" onclick="javascript:cerrar_actualizar_datos(); return false;"><?php echo image_tag('icon/icon_close.png'); ?></a>
        </div>-->
        <div id="actualizar_datos_ver" style="overflow: auto; height:450px; width: 540px; top: 10px; left: 10px; position: absolute;">
            <h2>¡Bienvenido al SIGLAS!.</h2>
            <div style="position: relative; width: 540px; text-align: justify;">
                Antes de usar el SIGLAS por primera vez es necesario que nos ayudes a cargar cierta información de gran importancia para los procesos internos de la institución.
                <br/><br/>
                Por este motivo actualiza o rellena el formulario de datos básicos a continuación.
            </div><br/>
            <div style="position: relative; width: 540px;">
                <form id="formactu" name="formactu" method="post" action="<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/actualizarInformacionInicialPersonal">
                    <table border="0" align="center" width="540">
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr>                        
                        <tr>
                            <td align="right">Cédula:</td>
                            <td><input name="datos_iniciales_personales[cedula]" type="text" value="<?php echo $datos_iniciales->getCi(); ?>"/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr>
                        <tr>
                            <td align="right">1º Nombre:</td>
                            <td><input name="datos_iniciales_personales[primer_nombre]" type="text" value="<?php echo $datos_iniciales->getPrimerNombre(); ?>"/></td>
                        </tr>
                        <tr>
                            <td align="right">2º Nombre:</td>
                            <td><input name="datos_iniciales_personales[segundo_nombre]" type="text" value="<?php echo $datos_iniciales->getSegundoNombre(); ?>"/></td>
                        </tr>
                        <tr>
                            <td align="right">1º Apellido:</td>
                            <td><input name="datos_iniciales_personales[primer_apellido]" type="text" value="<?php echo $datos_iniciales->getPrimerApellido(); ?>"/></td>
                        </tr>
                        <tr>
                            <td align="right">2º Apellido:</td>
                            <td><input name="datos_iniciales_personales[segundo_apellido]" type="text" value="<?php echo $datos_iniciales->getSegundoApellido(); ?>"/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr>
                        <tr>
                            <td align="right">Fecha de Nacimiento:</td>
                            <td>
                                
                                <?php 
                                    $anio_inicio = date('Y')-80;
                                    $anio_final = date('Y')-16;
                                    
                                    $years = range($anio_inicio, $anio_final);
                                    
                                    $w = new sfWidgetFormJQueryDate(array(
                                    'image' => '/images/icon/calendar.png',
                                    'culture' => 'es',
                                    'config' => '{changeYear: true, yearRange: \'c-100:c+100\'}',
                                    'date_widget' => new sfWidgetFormI18nDate(array(
                                                    'format' => '%day%-%month%-%year%',
                                                    'culture'=>'es',
                                                    'empty_values' => array('day'=>'<- Día ->',
                                                    'month'=>'<- Mes ->',
                                                    'year'=>'<- Año ->'),
                                                    'years' => array_combine($years, $years)))
                                    ),array('name'=>'datos_iniciales_personales[f_nacimiento]',));

                                    if($datos_iniciales->getFNacimiento())
                                        echo $w->render('datos_iniciales_personales[f_nacimiento]',$datos_iniciales->getFNacimiento());
                                    else
                                        echo $w->render('datos_iniciales_personales[f_nacimiento]');
                                ?>                                
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Lugar de Nacimiento:</td>
                            <td>
                                <select name="datos_iniciales_personales[estado_nacimiento]">
                                    <option value=""></option>
                                <?php
                                foreach ($estados as $estado) {
                                    if($estado->getId()==$datos_iniciales->getEstadoNacimientoId())
                                        $selected = ' selected="selected" ';
                                    else 
                                        $selected = '';
                                    
                                    echo "<option value='".$estado->getId()."' ".$selected.">".$estado->getNombre()."</option>";
                                }
                                ?>
                                </select>
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr>    
                        <tr>
                            <td align="right">Sexo:</td>
                            <td>
                                <select name="datos_iniciales_personales[sexo]">
                                    <option value=""></option>
                                    <option value="M" <?php if('M'==$datos_iniciales->getSexo()) echo 'selected="selected"'; ?>>Masculino</option>
                                    <option value="F" <?php if('F'==$datos_iniciales->getSexo()) echo 'selected="selected"'; ?>>Femenino</option>
                                </select>
                                
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Estado Civil:</td>
                            <td>
                                <select name="datos_iniciales_personales[estado_civil]">
                                    <option value=""></option>
                                    <option value="S" <?php if('S'==$datos_iniciales->getEdoCivil()) echo 'selected="selected"'; ?>>Soltero(a)</option>
                                    <option value="C" <?php if('C'==$datos_iniciales->getEdoCivil()) echo 'selected="selected"'; ?>>Casado(a)</option>
                                    <option value="D" <?php if('D'==$datos_iniciales->getEdoCivil()) echo 'selected="selected"'; ?>>Divorciado(a)</option>
                                    <option value="V" <?php if('V'==$datos_iniciales->getEdoCivil()) echo 'selected="selected"'; ?>>Viudo(a)</option>
                                </select>
                                
                            </td>
                        </tr>                        
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr> 
                        <tr>
                            <td>&nbsp;</td>
                            <td valign="top"><input id="submit" name="submit" type = "submit" value="Actualizar"/></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
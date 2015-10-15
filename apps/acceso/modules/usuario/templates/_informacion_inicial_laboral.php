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
        $('#formactu2').validate({
            rules: {
                'datos_iniciales_laborales[unidad_id]': 'required',
                'datos_iniciales_laborales[cargo_supervisor_id]': 'required'
            },
            messages: {
                'datos_iniciales_laborales[unidad_id]': 'Seleccione la unidad a la que pertenece',
                'datos_iniciales_laborales[cargo_supervisor_id]': 'Seleccione su supervisor inmediato'
            }
        });
    });
    
    $("#actualizar_datos_div").slideDown();
        
    
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
            <h2><?php echo (isset($supervisor_cambiado) ? '¡El cargo del supervisor inmediato ha sido cambiado!.' : '¡Este es el ultimo paso!.') ?></h2>
            <div style="position: relative; width: 540px; text-align: justify;">
                Adicional a la informacion anteriormente cargada, necesitados saber algunos datos laborales basicos ya que seran usados para que puedas 
                consultar y solicitar las vacaciones que te correspondan por derecho, asi como tambien consultar y solicitar permisos y reposos de ser necesarios.
                <br/><br/>
                Por este motivo actualiza o rellena el formulario de datos laborales del cargo a continuación.
            </div><br/>
            <div>
                <?php 
                $cargo_actual = Doctrine::getTable('Organigrama_Cargo')->datosDeCargo($supervisor_cargo_id);
                echo "<b>Unidad:</b> ".$cargo_actual[0]->getUnidad()."<br/>".
                     "<b>Condicion:</b> ".$cargo_actual[0]->getCondicion()."<br/>".
                     "<b>Tipo:</b> ".$cargo_actual[0]->getTipo()."<br/>";
                ?>
                
            </div>
            <div style="position: relative; width: 540px;">
                <form id="formactu2" name="formactu2" method="post" action="<?php echo sfConfig::get('sf_app_organigrama_url'); ?>cargo/actualizarInformacionInicialLaboral">
                    <table border="0" align="center" width="540">
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="datos_iniciales_laborales[cargo_id]" value="<?php echo $supervisor_cargo_id; ?>"/>
                                <hr/>
                            </td>
                        </tr>                        
                        <tr>
                            <td align="right">Supervisor Inmediato:</td>
                            <td>
                                
                                <?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>
                                <select name="datos_iniciales_laborales[unidad_id]" id="unidad_id" onchange="<?php
                                        echo jq_remote_function(array('update' => 'funidad',
                                        'url' => 'usuario/funcionarioUnidad',
                                        'with'     => "'u_id=' +this.value",)) ?>">

                                        <option value=""></option>

                                    <?php foreach( $unidades as $clave=>$valor ) { ?>
                                        <option value="<?php echo $clave; ?>">
                                            <?php echo $valor; ?>
                                        </option>
                                    <?php } ?>
                                </select>

                                <div id="funidad"></div> 
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
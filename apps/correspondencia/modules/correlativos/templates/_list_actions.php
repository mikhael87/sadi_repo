<?php
use_helper('jQuery');

$boss= false;
if($sf_user->getAttribute('funcionario_gr') == 99) {
    $boss= true;
    $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($sf_user->getAttribute('funcionario_id'));
}

$funcionario_unidades_admin = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->adminFuncionarioGrupo($sf_user->getAttribute('funcionario_id'));

$cargo_array= array();
if($boss) {
    foreach($funcionario_unidades_cargo as $unidades_cargo) {
        $cargo_array[]= $unidades_cargo->getUnidadId();
    }
}

$admin_array= array();
for($i= 0; $i< count($funcionario_unidades_admin); $i++) {
    $admin_array[]= $funcionario_unidades_admin[$i][0];
}

$nonrepeat= array_merge($cargo_array, $admin_array);

$funcionario_unidades= array();
foreach ($nonrepeat as $valor){
    if (!in_array($valor, $funcionario_unidades)){
        $funcionario_unidades[]= $valor;
    }
}

$formatos_legitimos = array();
$formatos_unidad_count = 0;
$i=0;
foreach ($funcionario_unidades as $unidades) {

    $unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidades);
    $unidad_tipo = $unidad->getUnidadTipoId();
    $unidad_id = $unidad->getId();

    $formatos = Doctrine::getTable('Correspondencia_TipoFormato')->findByStatusAndPrivadoAndTipo('A','N','C');   

    foreach ($formatos as $formato) {

    if($formato->getParametros() != null){
            $parametros = sfYaml::load($formato->getParametros());        

            if($parametros['emisores']['unidades']['todas']=='true') { 
                $formatos_legitimos[$i.'_'.$formato->getId()]=$formato->getNombre();
            } else if($parametros['emisores']['unidades']['tipos']!='false') {
                foreach ($parametros['emisores']['unidades']['tipos'] as $unidad_tipo_parametro) {
                    if($unidad_tipo_parametro == $unidad_tipo)
                        $formatos_legitimos[$i.'_'.$formato->getId()]=$formato->getNombre();
                }
            } else if($parametros['emisores']['unidades']['especificas']!='false') {
                foreach ($parametros['emisores']['unidades']['especificas'] as $unidad_especifica_parametro) {
                    if($unidad_especifica_parametro == $unidad_id)
                        $formatos_legitimos[$i.'_'.$formato->getId()]=$formato->getNombre();
                }
            }
    }
    }

    $formatos_unidad = Doctrine::getTable('Correspondencia_CorrelativosFormatos')->filtralPorUnidad($unidades);
    $formatos_unidad_count += count($formatos_unidad);
    $i++;
} ?>

<?php if($sf_user->getAttribute('pae_funcionario_unidad_id')) : ?>
<li class="sf_admin_action_regresar_modulo">
    <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>configuracion/index?opcion=correlativo">Regresar</a>
</li>
<?php endif; ?>
<?php //if(count($formatos_legitimos) > $formatos_unidad_count)
        echo $helper->linkToNew(array(  'params' =>   array(  ),  'class_suffix' => 'new',  'label' => 'New',));
      //else {
?>
<li class="sf_admin_action_pordefecto">
  <?php
//    echo link_to(__('Generación automática', array(), 'messages'), 'grupos/correlativosPorDefecto', array('confirm' => 'Se borran los permisos creados de todas las unidades listadas. Continuar?', 'class' => 'tooltip', 'title' => 'Crea correletivos faltantes de forma recursiva'))
  ?>
    
    <!--<a class="tooltip" href="#" onClick="mostrarConf(); return false;" title="Crea correletivos faltantes de forma recursiva">Generación automática</a>-->

    <div id="correlativos_conf" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 150px; min-height:50px; display: none">
        <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:50px; padding: 5px; box-shadow: #777 0.3em 0.3em 0.8em;">
            <div style="top: -15px; left: -15px; position: absolute;">
                    <x onclick="javascrtrip: mostrarConf(); return false;" style="cursor: pointer"><?php echo image_tag('icon/icon_close.png') ?></x>
            </div>
            <div id="content_correlativos_conf" style='padding: 5px'>
                
            </div>
        </div>
    </div>
</li>

<!--<li class="sf_admin_action_new">
    <a href="#" onclick="return alert('Ya se han definido todos los correlativos para esta unidad.'); false;">Nuevo</a>
</li>-->

<div id="content_add" style="display: none">
    <b>Parametros de Generaci&oacute;n Autom&aacute;tica</b><br/><hr/>
    <font style="font-size: 10px; color: #777">Nomenclador</font><br/>
    <select id='nomenclador_input'>
        <option value="Siglas-Letra-Año-Secuencia">Siglas-Letra-Año-Secuencia</option>
        <option value="Siglas-Año-Secuencia">Siglas-Año-Secuencia</option>
    </select><br/><br/>
    <font style="font-size: 10px; color: #777">Formatos</font><br/>
    ¿Utilizar un separador adicinal para letras?<br/>
    <input type='radio' onClick="changeSep(this.value)" style='vertical-align: middle' name='separador' checked="checked" value='('/>&nbsp;( )&nbsp;
    <input type='radio' onClick="changeSep(this.value)" style='vertical-align: middle' name='separador' value='['/>&nbsp;[ ]&nbsp;
    <input type='radio' onClick="changeSep(this.value)" style='vertical-align: middle' name='separador' value='{'/>&nbsp;{ }&nbsp;
    <input type='radio' onClick="changeSep(this.value)" style='vertical-align: middle' name='separador' value=''/>&nbsp;No Usar
    <?php $formatos = Doctrine::getTable('Correspondencia_TipoFormato')->findByStatusAndPrivadoAndTipo('A','N','C');
    $cadena='<table>';
    $checkados= array('oficio', 'redireccion', 'informe', 'amonestacion', 'memorandum');
    $siglas= array('oficio'=>'OF','redireccion'=>'RE','puntoCuenta'=>'PC','puntoInformacion'=>'PI','revisionPuntoInformacion'=>'RPI','revisionPuntoCuenta'=>'RPC','informe'=>'IN','amonestacion'=>'AM','memorandum'=>'ME');
    $admitidos= array('OF'=>'oficio','RE'=>'redireccion','PC'=>'puntoCuenta','PI'=>'puntoInformacion','RPI'=>'revisionPuntoInformacion','RPC'=>'revisionPuntoCuenta','IN'=>'informe','AM'=>'amonestacion','ME'=>'memorandum');
    foreach($formatos as $value) {
        if(in_array($value->getClasse(), $admitidos)) {
            $cadena.= '<tr>';
            $cadena.= '<td><input class="formatos_input" style="vertical-align: middle" type="checkbox" id="formato_'. $value->getId() .'" value="'. $value->getId() .'"  '. ((in_array($value->getClasse(), $checkados))? 'checked="checked"' : '') .'/></td>';
            $cadena.= '<td>'.$value->getNombre().'</td>';

            $cadena.= '<td><input class="siglas_input" id="siglas_'. $value->getId() .'" type="text" size="4" value="'. $siglas[$value->getClasse()] .'" /></td>';
            $cadena.= '<td><input class="sec_input" id="sec_'. $value->getId() .'" type="text" size="1" value="1" /></td>';
            $cadena.= '</tr>';
        }
    }
    $cadena.= '</table>';
    echo $cadena; ?>
    <div style="text-align: right; width: auto; background-color: #B7B7B7">
        <input name="generar" type="button" value="Generar" onClick="generar()" />
    </div>
    <font style="font-size: 10px; color: #777; font-weight: bold">Nota:</font>&nbsp;<font style="font-size: 10px; color: #777">De los formatos seleccionados, solo se generarán <br/>los legítimos y permitidos para la(s) unidad(es)</font><br/>
</div>

<style>
    .caja {
        padding: 1em 3em;
        margin: 1em 25%;
    }
</style>

<script>
    function generar() {
        var nomenclador= $('#nomenclador_input').value();
        var formatos= '';
        $('.formatos_input').each(function() {
            if($(this).is(':checked')) {
                var cad= $(this).val()+"|"+$('#siglas_'+$(this).val()).val()+"|"+$('#sec_'+$(this).val()).val();
                if(formatos.indexOf(cad) === -1) {
                    formatos+= cad;
                    formatos+= '#';
                }
            }
        });
        formatos+= 'ending';
        formatos= formatos.replace('#ending', '');
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>correlativos/correlativosPorDefecto',
            type:'POST',
            dataType:'html',
            data: '&formatos='+formatos+'&nomenclador='+nomenclador,
            success:function(data, textStatus){
                alert('regreso');
            }});
    }
    
    function changeSep(value) {
        $('.siglas_input').each(function() {
            $(this).val($(this).val().replace('(',''));
            $(this).val($(this).val().replace(')',''));
            $(this).val($(this).val().replace('[',''));
            $(this).val($(this).val().replace(']',''));
            $(this).val($(this).val().replace('{',''));
            $(this).val($(this).val().replace('}',''));
        
            if(value === '(') {
                $(this).val('('+ $(this).val() +')');
            }else {
                if(value === '[') {
                    $(this).val('['+ $(this).val() +']');
                }else {
                    if(value === '{') {
                        $(this).val('{'+ $(this).val() +'}');
                    }
                }
            }
        });
    }
    
    function mostrarConf(){
        tab = $('#correlativos_conf');
        if(!tab.is(':visible')){
            tab.show();
            tab.attr("position", "absolute");
            $('#content_correlativos_conf').html($('#content_add').html());
        }else{
            tab.hide();
        }
    }
</script>





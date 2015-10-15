<?php use_helper('jQuery'); ?>
<script>
    function solo_num(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 13) {
            EnviarInfo('persona_cedula');
        }
        if ((tecla >= 35 && tecla <= 37) || tecla == 8 || tecla == 9 || tecla == 46 || tecla == 39) {
            return true;
        }
        if ((tecla >= 95 && tecla <= 105) || (tecla >= 48 && tecla <= 57)) {
            return true;
        } else {
            return false;
        }
    }
    
    function buscar_cedula(cedula) {
        $('#busqueda_cedula_'+cedula).html('<?php echo image_tag('icon/cargando.gif', array('size'=>'20x20')); ?> Buscando en SAIME...');

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/visitanteForm',
            data: {cedula: cedula},
            success:function(data, textStatus){
                $('#busqueda_cedula_'+cedula).html(data);
            }
        })
    }
    
    function filtrar_pisos(){        
         $("#unidad_recibe option").hide();  
         $("#unidad_recibe option[class=piso_" + $('#unidad_pisos_id').val() + "]").show();  
         
         $("#unidad_recibe option[value='']").attr("selected", "selected");
         $("#funcionario_recibe option[value='']").attr("selected", "selected");
         $("#funcionario_recibe option[class=funcionario_id_class]").hide();  
    };
</script>

<script type="text/javascript">
    function LimpiarCombo(combo) {
        while (combo.length > 0) {
            combo.remove(combo.length - 1);
        }
    }
    function LlenarCombo(json, combo, texto, module) {
        combo.options[0] = new Option('<- Seleccione ' + module + '->', '');
        for (var i = 0; i < json.length; i++) {
            //verificamos el contenido del combo y del text 
            //para q quede seleccionado el combo
            if (json[i].a_descripcion.toUpperCase() === texto.value.toUpperCase())
                combo.options[combo.length] = new Option(json[i].a_descripcion, json[i].a_id, 'selected="selected"')
            else
                combo.options[combo.length] = new Option(json[i].a_descripcion, json[i].a_id)
        }
        texto.value = '';
    }
    function EnviarInfo(texto, combo, module) {
        combo = document.getElementById(combo);
        texto = document.getElementById(texto);
        LimpiarCombo(combo);
        if (texto.value != '') {
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url') ?>ingresa/ajaxAddItem'+module,
                data: {valor: texto.value},
                beforeSend: function() {
                    combo.options[0] = new Option('Cargando...', '');
                    $("#add_" + module).hide();
                    $("img#busy_" + module).show();
                },
                complete: function() {
                    $("img#busy_" + module).hide();
                },
                success: function(json) {
                    LlenarCombo(json, combo, texto, module);
                },
                error: function(Obj, err) {
                    alert('No se pudo incluir el registro!');
                }
            })
        }
    }
</script>

<style>
    a.agregar {
        background: transparent url(/images/icon/new.png) no-repeat scroll left center;
        padding: 2px 4px 1px 20px;
        font-family: "Trebuchet MS", helvetica, sans-serif;
        font-style: normal;
        font-size: 14px;
        cursor: pointer;
    }
    
</style>
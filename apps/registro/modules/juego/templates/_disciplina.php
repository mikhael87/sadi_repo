<script>
    $(document).ready(function (){
        var ter= '<?php echo $form->isNew() ?>';
        if(ter=== '') {
            actualizar_variantes();
        }
        });
    
    function actualizar_variantes() {
        var disc= $('#disciplina_select').val();
        var vari= $('#old_disciplina_variante_id').val();
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>juego/actualizarVariante',
            type:'POST',
            dataType:'html',
            data: 'disc='+disc+'&vari='+vari,
            success:function(data, textStatus){
                $('#variante_div').html(data);
                actualizar_equipos();
            }});
    }
    
    function actualizar_equipos() {
        var disc= $('#disciplina_select').val();
        var disc_v= $('#variante_select').val();
        var grupo= $('#grupo_select').val();
    
        if(disc !== '' && disc_v !== '') {
            var ter= '<?php echo $form->isNew() ?>';
            if(ter=== '') {
                ter= '<?php echo $form['id']->getValue() ?>';
            }else {
                ter= '';
            }

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>juego/match',
                type:'POST',
                dataType:'html',
                data: 'disc='+disc+'&disc_v='+disc_v+'&mod='+ter+'&grupo='+grupo,
                success:function(data, textStatus){
                    $('#juego_div').html(data);
                }});
        }
    }
</script>

<?php $disciplinas= Doctrine::getTable('Operaciones_Disciplina')->findByStatus('A');
$grupos= Doctrine::getTable('Operaciones_Grupo')->disponibles();

$disci_old= ''; $grupo_old= '';
if(!$form->isNew()) {
    $disci= Doctrine::getTable('Operaciones_DisciplinaVariante')->find($form['disciplina_variante_id']->getValue());
    $disci_old= $disci->getDisciplinaId();
    
    if($form['grupo_id']->getValue() != '') {
        $grupo_old= $form['grupo_id'];
    }
}
?>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_disciplina_variante">
    <div>
        <label for="disciplina">Disciplina</label>
        <select id="disciplina_select" name="disciplina" onchange="javascript: actualizar_variantes(); return false;">
            <option value="0"><- Seleccione ->
            <?php
            foreach($disciplinas as $value) {
                echo '<option value="'.$value->getId().'" '.(($disci_old == $value->getId())? "selected":"").'>'.$value->getNombre().'</opcion>';
            } ?>
        </select>
    </div><br/>
    <input type="hidden" id="old_disciplina_variante_id" value="<?php echo ((!$form->isNew())? $form['disciplina_variante_id']->getValue():"") ?>"/>
    <div id="variante_div">
        <label for="variante">Variante</label>
        <select id="variante_select" name="operaciones_juego[disciplina_variante_id]"></select>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_grupo">
    <div>
        <label for="grupo">Grupo</label>
        <select id="grupo_select" name="operaciones_juego[grupo_id]" onchange="javascript: actualizar_equipos(); return false;">
            <option value=''>LIBRE</option>
            <?php
            foreach($grupos as $value) {
                echo '<option value="'.$value->getId().'" '.(($disci_old == $value->getId())? "selected":"").'>'.$value->getNombre().'</opcion>';
            } ?>
        </select>
    </div>
</div>


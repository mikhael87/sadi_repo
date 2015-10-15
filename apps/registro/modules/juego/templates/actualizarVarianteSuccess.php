<label for="variante">Variante</label>
<select id="variante_select" name="operaciones_juego[disciplina_variante_id]" id="operaciones_juego_disciplina_variante_id" onChange="javascrit: actualizar_equipos(); return false;">
<?php
foreach($variantes as $value) {
    echo '<option value="'. $value->getDvi() .'" ';
    if(count($variantes) == 1) {
        echo 'selected';
    }else {
        if($old_variante != '') {
            if($old_variante== $value->getDvi()) {
                echo 'selected';
            }
        }
    }
    echo '>'. $value->getNombre() .'</option>';
}
?>
</select>

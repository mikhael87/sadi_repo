<div id="div_pos_<?php echo $pos ?>">
    <x class="pos" id="pos_<?php echo $pos ?>"><font style="font-size: 22px; font-weight: bold; vertical-align: middle"><?php echo $pos ?>°</font>&nbsp;&nbsp;</x>
    <select name="tipo[equipos][<?php echo $pos ?>][id]" data-placeholder="Seleccione un equipo..." class="team-select chosen-select" style="width:350px;" tabindex="2">
        <option value=""></option>
        <?php
        foreach($empresas as $value) {
            echo '<option value="'.$value->getRegistro().'">'.$value->getNombre().'</option>';
        }
        ?>
    </select>
    <a id="<?php echo $pos ?>" href="#" onclick="javascript: delete_team(this); return false;"><img src="/images/icon/delete.png" style=" vertical-align: middle"/></a>
</div>

<script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, no se encontro!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
    
    $(document).ready(function (){
        $('.team-select').change(function() {
            checkId();
        });
    });
</script>

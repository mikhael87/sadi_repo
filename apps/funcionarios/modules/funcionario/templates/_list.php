<?php use_helper('jQuery'); ?>
<script>
    function conmutar_user(id,tipo_user){
        user = document.getElementById('user_'+tipo_user+'_'+id);
        div = document.getElementById('tab_user_'+tipo_user+'_'+id);
        
        if(div.style.display == 'none'){
            $('#tab_user_'+tipo_user+'_'+id).show();
            div.style.position= "absolute";
            user.style.fontWeight= "bold";
        } else {
            $('#tab_user_'+tipo_user+'_'+id).hide();
            user.style.fontWeight= "normal";
        }
    };
    
    function checkuserSiglas(id_usr) {
        var ext1= $('#ext1_'+id_usr).val();
        var ext2= $('#ext2_'+id_usr).val();
        var re= /^[0-9-a-z]*$/;
        
        if(ext1 != '' && ext2 != ''){
            if(re.test(ext1) && re.test(ext2)){
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/checkUser',
                    type:'POST',
                    dataType:'html',
                    data: 'id_usr='+id_usr+'&ext1='+ext1+'&ext2='+ext2+'&tipo_user=siglas',
                    success:function(data, textStatus){
                        $('#renew_siglas_'+id_usr).html(data);
                    }});
            }else{
                alert('Utilize solo letras y números (minuscula)');
            }
        } 
    }; 
    
    function checkuserLdap(id_usr) {
        var ext_ldap= $('#ext_ldap_'+id_usr).val();
        var re= /^[\.-_a-z0-9-]*$/;
        
        if(ext_ldap != ''){
            if(re.test(ext_ldap)){
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/checkUser',
                    type:'POST',
                    dataType:'html',
                    data: 'id_usr='+id_usr+'&ext_ldap='+ext_ldap+'&tipo_user=ldap',
                    success:function(data, textStatus){
                        $('#renew_ldap_'+id_usr).html(data);
                    }});
            }else{
                alert('Utilize solo letras, números, puntos, guiones y arroba');
            }
        } 
    }; 
    
    function saveuser(id_usr,nombre_usr,tipo_user) {
        if(confirm('Se reiniciará la contraseña...¿Esta seguro?')) {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/saveUser',
                type:'POST',
                dataType:'html',
                data: 'id_usr='+id_usr+'&nombre='+nombre_usr+'&tipo_user='+tipo_user,
                success:function(data, textStatus){
                    $('#usuario_'+tipo_user+'_'+id_usr).html(data);
                }});
        }
    };
    
    function cerrar_edit_user(id_usr) {
        $('#tab_user_siglas_'+id_usr).hide();
    }
</script>
<style>
    .helpfont {
        font-size: 10px;
        color: #333;
    }
    .icon_check {
        vertical-align: middle
    }
</style>

<div class="sf_admin_list">
  <?php if (!$pager->getNbResults()): ?>
    <p><?php echo __('No result', array(), 'sf_admin') ?></p>
  <?php else: ?>
    <table cellspacing="0">
      <thead>
        <tr>
          <?php include_partial('funcionario/list_th_tabular', array('sort' => $sort)) ?>
          <th id="sf_admin_list_th_actions"><?php echo __('Actions', array(), 'sf_admin') ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="11">
            <?php if ($pager->haveToPaginate()): ?>
              <?php include_partial('funcionario/pagination', array('pager' => $pager)) ?>
            <?php endif; ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
            <?php if ($pager->haveToPaginate()): ?>
              <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
            <?php endif; ?>
          </th>
        </tr>
      </tfoot>
      <tbody>
        <?php
        // ########### LEVANTAR LOS PARAMETROS DE AUTENTICACION ############
        $sf_autenticacion = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/autenticacion.yml");
        ?>  

        <?php foreach ($pager->getResults() as $i => $funcionarios_funcionario): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
          <tr class="sf_admin_row <?php echo $odd ?>">
            <?php include_partial('funcionario/list_td_tabular', array('funcionarios_funcionario' => $funcionarios_funcionario, 'sf_autenticacion' => $sf_autenticacion)) ?>
            <?php include_partial('funcionario/list_td_actions', array('funcionarios_funcionario' => $funcionarios_funcionario, 'helper' => $helper)) ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<script type="text/javascript">
/* <![CDATA[ */
function checkAll()
{
  var boxes = document.getElementsByTagName('input'); for(var index = 0; index < boxes.length; index++) { box = boxes[index]; if (box.type == 'checkbox' && box.className == 'sf_admin_batch_checkbox') box.checked = document.getElementById('sf_admin_list_batch_checkbox').checked } return true;
}
/* ]]> */
</script>

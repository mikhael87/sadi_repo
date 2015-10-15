<script>
    function conmutar_pago(id){
        if($('#tab_pago_'+id).is(':hidden')){
            $('#tab_pago_'+id).show();
        } else {
            $('#tab_pago_'+id).hide();
        }
    };
    
    function savePago(id) {
        var banco= $('#banco_'+id).val();
        var referencia= $('#referencia_'+id).val();
        var tipo_pago= $('#tipo_pago_'+id).val();
        var monto= $('#monto_'+id).val();
        var RegExPattern= /^\d+(\,\d{1,2})?$/;
        
        if(tipo_pago != '' && monto != '') {
            if(monto.match(RegExPattern)) {
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>empresa/savePago',
                    type:'POST',
                    dataType:'html',
                    data: 'regid='+id+'&banco='+banco+'&referencia='+referencia+'&tipo_pago='+tipo_pago+'&monto='+monto,
                    success:function(data, textStatus){
                        document.location = "<?php echo sfConfig::get('sf_app_registro_url').'empresa/index'; ?>";
                    }
                });
            }else {
                alert('Por favor, solo utilice coma (,) y dos (2) decimales como máximo');
            }
        }else {
            alert('Por favor, indique monto');
        }
    }
</script>

<div class="sf_admin_list">
  <?php if (!$pager->getNbResults()): ?>
    <p><?php echo __('No result', array(), 'sf_admin') ?></p>
  <?php else: ?>
    <table cellspacing="0">
      <thead>
        <tr>
          <?php include_partial('empresa/list_th_tabular', array('sort' => $sort)) ?>
          <th id="sf_admin_list_th_actions"><?php echo __('Actions', array(), 'sf_admin') ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="5">
            <?php if ($pager->haveToPaginate()): ?>
              <?php include_partial('empresa/pagination', array('pager' => $pager)) ?>
            <?php endif; ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
            <?php if ($pager->haveToPaginate()): ?>
              <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
            <?php endif; ?>
          </th>
        </tr>
      </tfoot>
      <tbody>
        <?php foreach ($pager->getResults() as $i => $empresas_empresa): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
          <tr class="sf_admin_row <?php echo $odd ?>">
            <?php include_partial('empresa/list_td_tabular', array('empresas_empresa' => $empresas_empresa)) ?>
            <?php include_partial('empresa/list_td_actions', array('empresas_empresa' => $empresas_empresa, 'helper' => $helper)) ?>
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

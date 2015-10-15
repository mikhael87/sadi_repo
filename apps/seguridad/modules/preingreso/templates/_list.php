<div class="sf_admin_list">
  <?php if (!$pager->getNbResults()): ?>
    <p><?php echo __('No result', array(), 'sf_admin') ?></p>
  <?php else: ?>
    <table cellspacing="0">
      <thead>
        <tr>
          <th id="sf_admin_list_batch_actions"><input id="sf_admin_list_batch_checkbox" type="checkbox" onclick="checkAll();" /></th>
          <?php include_partial('preingreso/list_th_tabular', array('sort' => $sort)) ?>
          <th id="sf_admin_list_th_actions"><?php echo __('Actions', array(), 'sf_admin') ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="5">
            <?php if ($pager->haveToPaginate()): ?>
              <?php include_partial('preingreso/pagination', array('pager' => $pager)) ?>
            <?php endif; ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
            <?php if ($pager->haveToPaginate()): ?>
              <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
            <?php endif; ?>
          </th>
        </tr>
      </tfoot>
      <tbody>
        <?php foreach ($pager->getResults() as $i => $seguridad_preingreso): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
          <?php 
          
            $session_cargos = sfContext::getInstance()->getUser()->getAttribute('session_cargos');
            foreach ($session_cargos as $session_cargo) {
                $unidad_ids[] = $session_cargo['unidad_id'];
            }
            
            if(!in_array($seguridad_preingreso->getUnidadId(),$unidad_ids)) {
              $color='yellow';
            } else {
              $color = '';
            }
          ?>
          <tr class="sf_admin_row <?php echo $odd ?>" style="background-color: <?php echo $color?>;">
            <?php include_partial('preingreso/list_td_batch_actions', array('seguridad_preingreso' => $seguridad_preingreso, 'helper' => $helper)) ?>
            <?php include_partial('preingreso/list_td_tabular', array('seguridad_preingreso' => $seguridad_preingreso)) ?>
            <?php include_partial('preingreso/list_td_actions', array('seguridad_preingreso' => $seguridad_preingreso, 'helper' => $helper)) ?>
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

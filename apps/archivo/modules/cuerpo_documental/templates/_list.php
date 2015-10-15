<script>
    function fn_orden_tipo(){
        $(".up,.down").click(function(){
            var row = $(this).parents("tr:first");
            if ($(this).is(".up")) {
                row.insertBefore(row.prev("tr:has(td)"));
            } else {
                row.insertAfter(row.next("tr:has(td)"));
            }
            
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>cuerpo_documental/ordenar',
                type:'POST',
                dataType:'html',
                data: $('.cuerpos').serialize(),
                success:function(data, textStatus){
                }})
        });
    };
    
    $(document).ready(function(){
        fn_orden_tipo();
    });
</script>

<div class="sf_admin_list">
  <?php if (!$pager->getNbResults()): ?>
    <p><?php echo __('No result', array(), 'sf_admin') ?></p>
  <?php else: ?>
    <table cellspacing="0">
      <thead>
        <tr>
          <?php include_partial('cuerpo_documental/list_th_tabular', array('sort' => $sort)) ?>
          <th id="sf_admin_list_th_actions"><?php echo __('Actions', array(), 'sf_admin') ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="3">
            <?php if ($pager->haveToPaginate()): ?>
              <?php include_partial('cuerpo_documental/pagination', array('pager' => $pager)) ?>
            <?php endif; ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
            <?php if ($pager->haveToPaginate()): ?>
              <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
            <?php endif; ?>
          </th>
        </tr>
      </tfoot>
      <tbody>
        <?php foreach ($pager->getResults() as $i => $archivo_cuerpo_documental): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
          <tr class="sf_admin_row <?php echo $odd ?>">
            <?php include_partial('cuerpo_documental/list_td_tabular', array('archivo_cuerpo_documental' => $archivo_cuerpo_documental)) ?>
            <?php include_partial('cuerpo_documental/list_td_actions', array('archivo_cuerpo_documental' => $archivo_cuerpo_documental, 'helper' => $helper)) ?>
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

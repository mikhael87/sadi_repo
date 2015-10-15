<?php $count_row = 2; 
$idr = 0;
$counter = 0;
?>
<div class="sf_admin_list">
  <?php if (!$pager->getNbResults()){ ?>
    <p><?php echo __('No result', array(), 'sf_admin') ?></p>
  <?php } else{ ?>
    <table cellspacing="0">
      <thead>
        <tr>
          <?php include_partial('mensajes/list_th_tabular', array('sort' => $sort)) ?>
          <th id="sf_admin_list_th_actions"><?php echo __('Actions', array(), 'sf_admin') ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="3">
            <?php if ($pager->haveToPaginate()): ?>
              <?php include_partial('mensajes/pagination', array('pager' => $pager)) ?>
            <?php endif; ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
            <?php if ($pager->haveToPaginate()): ?>
              <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
            <?php endif; ?>
          </th>
        </tr>
      </tfoot>
      <tbody>
        <?php foreach ($pager->getResults() as $i => $public_mensajes){ 
            $odd = fmod(++$i, 2) ? 'odd' : 'even'; 
            if($counter == 0){ 
                if($public_mensajes->getIdEliminado() != $sf_user->getAttribute('funcionario_id') || $public_mensajes->getIdEliminado() == '')
               {?>
                    <tr class="sf_admin_row <?php echo $odd ?>" style="word-wrap: break-word;">
                      <?php include_partial('mensajes/list_td_tabular', array('public_mensajes' => $public_mensajes)) ?>
                      <?php include_partial('mensajes/list_td_actions', array('public_mensajes' => $public_mensajes, 'helper' => $helper)) ?>
                    </tr>
                <?php    
                $idr = $public_mensajes->getFuncionarioRecibeId(); 
                $counter = 1;
                } }
           else {
            if($public_mensajes->getIdEliminado() != $sf_user->getAttribute('funcionario_id') || $public_mensajes->getIdEliminado() == '')
               {
                 if($public_mensajes->getFuncionarioRecibeId() != $idr)
                    { ?>
                        <tr class="sf_admin_row <?php echo $odd ?>" style="word-wrap: break-word;">
                          <?php include_partial('mensajes/list_td_tabular', array('public_mensajes' => $public_mensajes)) ?>
                          <?php include_partial('mensajes/list_td_actions', array('public_mensajes' => $public_mensajes, 'helper' => $helper)) ?>
                        </tr>
        <?php       
                    $idr = $public_mensajes->getFuncionarioRecibeId(); 
                    } 
                else
                    {
                    $count_row--;
                    }
                
            } 
            else 
                {
                $count_row--;
                }
        $count_row++; 
        } } ?>
      </tbody>
    </table>
  <?php }; ?>
</div>

<script type="text/javascript">
/* <![CDATA[ */
function checkAll()
{
  var boxes = document.getElementsByTagName('input'); for(var index = 0; index < boxes.length; index++) { box = boxes[index]; if (box.type == 'checkbox' && box.className == 'sf_admin_batch_checkbox') box.checked = document.getElementById('sf_admin_list_batch_checkbox').checked } return true;
}
/* ]]> */
add = "<td id='mensajes' style='background: #fff;' rowspan='<?php echo $count_row; ?>'></td>";
</script>

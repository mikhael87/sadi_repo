<td>
  <ul class="sf_admin_td_actions">
    <?php if($acceso_accion_delegada->getStatus()=='A'){ ?>
    <li class="sf_admin_action_deshabilitar">
      <?php echo link_to(__('Deshabilitar', array(), 'messages'), 'delegar/deshabilitar?id='.$acceso_accion_delegada->getId(), 'confirm=\'¿Estas seguro de deshabilitar la firma?\'') ?>
    </li>
    <?php } ?>
  </ul>
   
    <br/>
    <div style="position: relative; width: 100px;">
        <div style="position: relative; font-size: 8px; left: 0px;">
            Fecha de creación
        </div>
        <div style="position: relative; font-size: 13px; left: 0px;">
            <?php echo date('d-m-Y h:i:s A', strtotime($acceso_accion_delegada->getCreatedAt())); ?>
        </div>

        <div style="position: relative; font-size: 8px; left: 0px;">
            <br/>PC de donde se autorizo
        </div>
        <div style="position: relative; font-size: 13px; left: 0px;">
            <?php echo $acceso_accion_delegada->getIpUpdate(); ?>
            <br/>
        </div>
    </div>

</td>

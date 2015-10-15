<td class="sf_admin_text sf_admin_list_td_codigo_unidad">
  <?php echo $organigrama_unidad->getCodigoUnidad() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_list_identificacion">
  <?php echo get_partial('unidad/list_identificacion', array('type' => 'list', 'organigrama_unidad' => $organigrama_unidad, 'unidades_orden' => $unidades_orden)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_list_direccion">
  <?php echo get_partial('unidad/list_detalles', array('type' => 'list', 'organigrama_unidad' => $organigrama_unidad)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_list_direccion">
  <?php echo get_partial('unidad/list_direccion', array('type' => 'list', 'organigrama_unidad' => $organigrama_unidad)) ?>
</td>
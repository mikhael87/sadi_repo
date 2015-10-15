<td>
  <ul class="sf_admin_td_actions">
    <li class="sf_admin_action_solicitar_fisico">
        <a href="#" onclick="javascript:solicitar_expedientes(<?php echo $archivo_expediente->getId(); ?>); return false;" style="text-decoration: none;" class="tooltip" >[!]Solicitar Físico[/!]Se creará una correspondencia</a>
    </li>
  </ul>
  <br/>
    <div class="" style="position: relative;">
        <font class="f10n">Archivado por:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo $archivo_expediente->getUserUpdate(); ?><br/></font>
        <font class="f10n">Fecha:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($archivo_expediente->getCreatedAt())); ?><br/></font>
        <font class="f10n">Hora:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($archivo_expediente->getCreatedAt())); ?></font>
    </div>
</td>
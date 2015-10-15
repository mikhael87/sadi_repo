<td>
  <ul class="sf_admin_td_actions">
    <li class="sf_admin_action_salida">
         <?php 
            if($seguridad_ingreso->getFEgreso()){
                echo '';
            }else{
                echo '<a  class="egreso" id="egreso_'.$seguridad_ingreso->getId().'" href="#" onClick="registrar_egreso('.$seguridad_ingreso->getId().'); return false;">Egreso de Visitante</a>';
            }
          ?>
        
    </li>
  </ul>
  <br/>
    <div class="f10n">Registrado por:</div> 
    <div><?php  echo $seguridad_ingreso->getRegistrador(); ?></div><br/>
    <?php if($seguridad_ingreso->getDespachador()) { ?>
        <div class="f10n">Despachado por:</div> 
        <div><?php  echo $seguridad_ingreso->getDespachador(); ?></div>
    <?php } ?>
</td>

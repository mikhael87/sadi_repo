
<?php 
    $cantresidencias = Doctrine::getTable('Funcionarios_Residencia')->findByFuncionarioIdAndStatus(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'),'A');
    if ($cantresidencias[0]!=''){
    foreach($cantresidencias as $residencia): 

        $estados    = Doctrine::getTable('Public_Estado')->findById($residencia->getEstadoId());
        $municipios = Doctrine::getTable('Public_Municipio')->findById($residencia->getMunicipioId());
        $parroquias = Doctrine::getTable('Public_Parroquia')->findById($residencia->getParroquiaId());
        $id = $residencia->getId();
?>
 <div align="right"  style="position: relative; right: 5px; top: 5px;">
    <a  style="right: 0px;" title="Editar"  href="#" onclick="javascript:abrir_notificacion_derecha('infoResidencial?residencia_accion=editar&res_id=<?php echo $id; ?>'); return false;">
        <?php echo image_tag('icon/edit.png'); ?>
    </a>
    &nbsp;&nbsp;&nbsp;
    <a style="right: 0px;" title="Eliminar" href="<?php echo url_for('ficha/eliminarResidencia?res_id='.$id) ?>" onclick="javascript:if(confirm('Esta seguro de eliminar?')){return true}else{return false};">
        <?php echo image_tag('icon/delete.png'); ?>
    </a>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label style="width: 10em;" for="">Dirección:</label>
        <div class="content">
             &nbsp;
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">

        <div class="">
        <?php echo $residencia->getDirAvCalleEsq(); ?>&nbsp;<br> 
         Urb.  <?php echo $residencia->getDirUrbanizacion(); ?>&nbsp; , Edif/casa:     <?php echo $residencia->getDirEdfCasa(); ?>&nbsp;<br> 
         Piso:        <?php echo $residencia->getDirPiso(); ?>&nbsp;, Apto/Nombre: <?php echo $residencia->getDirAptNombre(); ?>&nbsp;<br> 
         Ciudad:  <?php echo $residencia->getDirCiudad(); ?>&nbsp; <br> 
         Estado: <?php echo $estados[0]; ?>&nbsp;/ municipio <?php echo $municipios[0];  ?>&nbsp; / parroquia 
             <?php echo $parroquias[0]; ?>&nbsp; <br>
         Pto de referencia: <?php echo $residencia->getDirPuntoReferencia(); ?>&nbsp;
         <br>
         Telefonos: <?php echo $residencia->getTelfUno(); ?>, <?php echo $residencia->getTelfDos(); ?>&nbsp;
        </div>  
</div>

<?php endforeach; ?>
<?php } else { ?>
    <div class="sf_admin_form_row sf_admin_text" style="min-height: 70px;">
        <div class="f16n gris_medio" style="text-align: justify;">
            Tu información residencial es importante, ...............
        </div>
    </div>
<?php } ?>

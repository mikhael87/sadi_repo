<?php use_helper('jQuery'); ?>
<?php if ($sf_user->hasFlash('error')){ ?>
  <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
<?php } else { ?>
<div class="sf_admin_form_row sf_admin_text">

    <div>
        <label for="">Tipologia Documental</label>
        <div class="content">
            <select name="archivo_documento[tipologia_documental_id]" onchange="<?php
                    echo jq_remote_function(array('update' => 'div_valores',
                    'url' => 'documento/listarValores',
                    'with'     => "'t_id='+this.value")); 
                    ?>">
                <option value=""></option>
                <?php 
                    $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($sf_user->getAttribute('serie_documental_id'),'null');

                    foreach ($tipologias as $tipologia) { 
                ?>                
                        <option value="<?php echo $tipologia->getId(); ?>" <?php if(isset($form['tipologia_documental_id'])){ if($tipologia->getId() == $form['tipologia_documental_id']->getValue()) echo "selected"; } ?>>
                            <?php echo $tipologia->getNombre(); ?>
                        </option>
                <?php } ?>
                <optgroup>
                <?php 
                    $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($sf_user->getAttribute('serie_documental_id'),'permitidos',$sf_user->getAttribute('expediente_id'));

                    $cuerpo = null;
                    foreach ($tipologias as $tipologia) { 
                        if($tipologia->getCuerpo()!=$cuerpo) {
                            echo '</optgroup><optgroup label="'.$tipologia->getCuerpo().'">';
                            $cuerpo = $tipologia->getCuerpo();
                        }
                ?>
                
                        <option value="<?php echo $tipologia->getId(); ?>" <?php if(isset($form['tipologia_documental_id'])){ if($tipologia->getId() == $form['tipologia_documental_id']->getValue()) echo "selected"; } ?>>
                            <?php echo $tipologia->getNombre(); ?>
                        </option>
                <?php } ?>
                </optgroup>
            </select>
        </div>
    </div>

    <div class="help">Seleccione la tipologia documental.</div>
</div>

<div id="div_valores">
    
    
<?php 
    if(isset($form['id'])){
        if(!$form->isNew()) {
            $valores_etiquetas = Doctrine::getTable('Archivo_DocumentoEtiqueta')->valoresEtiquetas($form['id']->getValue());

            foreach ($valores_etiquetas as $valores) { ?>
                <div class="sf_admin_form_row sf_admin_text">
                    <div>
                        <label for=""><?php echo $valores->getEtiqueta(); ?></label>
                        <div class="content">
                            <input name="valores[<?php echo $valores->getEtiquetaId(); ?>]" type="text" value="<?php echo $valores->getValor(); ?>"/>
                        </div>
                    </div>
                </div>
    <?php } } }?>
    
    
</div>
<?php } ?>

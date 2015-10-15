<?php foreach ($clasificadores as $valores) { ?>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for=""><?php echo $valores->getNombre(); ?></label>
        <div class="content">
            <?php if($valores->getTipoDato()=='texto') { ?>
            
                <?php if($correspondencia_solicitud==0) { ?>
                    <input name="valores[<?php echo $valores->getId(); ?>]" type="text"/>
                <?php } else { ?>
                    <input name="correspondencia[formato][solicitud_expediente_clasificador][<?php echo $valores->getId(); ?>]" type="text" value="<?php echo (isset($descriptores_salvados[$valores->getId()])) ? $descriptores_salvados[$valores->getId()] : ''; ?>"/>
                <?php } ?>   
                    
            <?php } else if($valores->getTipoDato()=='numero') { ?>
                
                <?php if($correspondencia_solicitud==0) { ?>
                    <input name="valores[<?php echo $valores->getId(); ?>]" type="text"/>
                <?php } else { ?>
                    <input name="correspondencia[formato][solicitud_expediente_clasificador][<?php echo $valores->getId(); ?>]" type="text"  value="<?php echo (isset($descriptores_salvados[$valores->getId()])) ? $descriptores_salvados[$valores->getId()] : ''; ?>"/>
                <?php } ?>  
                    
            <?php } else if($valores->getTipoDato()=='listado') { ?>
                    
                <?php if($correspondencia_solicitud==0) { ?>
                    <select name="valores[<?php echo $valores->getId(); ?>]">
                <?php } else { ?>
                    <select name="correspondencia[formato][solicitud_expediente_clasificador][<?php echo $valores->getId(); ?>]">
                <?php } ?>  
                
                    <option value=""></option>
                    <?php 
                        $opciones = explode(';',$valores->getParametros());

                        for($i=0;$i<count($opciones);$i++)
                            echo '<option ' . ($descriptores_salvados[$valores->getId()] && $descriptores_salvados[$valores->getId()] == $opciones[$i] ? 'selected' : '') . ' value="'.$opciones[$i].'">'.$opciones[$i].'</option>';
                    ?>
                </select>
            <?php } else if($valores->getTipoDato()=='fecha') { 
                list($desde,$hasta) = explode('-',$valores->getParametros());
                $years = range($desde, $hasta);
                $w = new sfWidgetFormJQueryDate(array(
                'culture' => 'es',
                'date_widget' => new sfWidgetFormI18nDate(array(
                                'format' => '%day%-%month%-%year%',
                                'culture'=>'es',
                                'empty_values' => array('day'=>'<- Día ->',
                                'month'=>'<- Mes ->',
                                'year'=>'<- Año ->'),
                                'years' => array_combine($years, $years)))
                ));
                
                if($correspondencia_solicitud==0) {
                    echo $w->render('valores['.$valores->getId().'#f]');
                } else {
                    echo $w->render('correspondencia[formato][solicitud_expediente_clasificador]['.$valores->getId().'#f]');
                }
            } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if(count($cuerpos)>0){ ?>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Cuerpos o Secciones</label>
        <div class="content">
            <?php foreach ($cuerpos as $cuerpo) { ?>
            <input type="checkbox" name="cuerpos[]" value="<?php echo $cuerpo->getId(); ?>" checked="checked"/> <?php echo $cuerpo->getNombre(); ?><br/>
            <?php } ?>
        </div>
    </div>
    <div class="help">Seleccione los cuerpos o secciones de la serie documental que almacenara en este expediente.</div>
</div>
<?php } ?>
<?php foreach ($etiquetas as $valores) { ?>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for=""><?php echo $valores->getNombre(); ?></label>
        <div class="content">
            <?php if($valores->getTipoDato()=='texto') { ?>
                <input name="valores[<?php echo $valores->getId(); ?>]" type="text"/>
            <?php } else if($valores->getTipoDato()=='numero') { ?>
                <input name="valores[<?php echo $valores->getId(); ?>]" type="text"/>
            <?php } else if($valores->getTipoDato()=='listado') { ?>
                <select name="valores[<?php echo $valores->getId(); ?>]">
                    <option value=""></option>
                    <?php 
                        $opciones = explode(';',$valores->getParametros());

                        for($i=0;$i<count($opciones);$i++)
                            echo '<option value="'.$opciones[$i].'">'.$opciones[$i].'</option>';
                    ?>
                </select>
            <?php } else if($valores->getTipoDato()=='fecha') { 
                list($desde,$hasta) = explode('-',$valores->getParametros());
                $years = range($desde, $hasta);
                $w = new sfWidgetFormJQueryDate(array(
                'image' => '/images/icon/calendar.png',
                'culture' => 'es',
                'config' => '{changeYear: true, yearRange: \'c-100:c+100\'}',
                'date_widget' => new sfWidgetFormI18nDate(array(
                                'format' => '%day%-%month%-%year%',
                                'culture'=>'es',
                                'empty_values' => array('day'=>'<- Día ->',
                                'month'=>'<- Mes ->',
                                'year'=>'<- Año ->'),
                                'years' => array_combine($years, $years)))
                ));
                
                echo $w->render('valores['.$valores->getId().'#f]');
            } ?>
        </div>
    </div>
</div>
<?php } ?>
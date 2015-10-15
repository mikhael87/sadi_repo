<br/>
<table>
<?php foreach ($etiquetas as $valores) { ?>
    <tr>
        <td><label for=""><?php echo $valores->getNombre(); ?></label></td>
        <td>
            <?php if($valores->getTipoDato()=='texto') { ?>
                <input name="valores_documento[<?php echo $valores->getId(); ?>]" type="text"/>
            <?php } else if($valores->getTipoDato()=='numero') { ?>
                <input name="valores_documento[<?php echo $valores->getId(); ?>]" type="text"/>
            <?php } else if($valores->getTipoDato()=='listado') { ?>
                <select name="valores_documento[<?php echo $valores->getId(); ?>]">
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
                'culture' => 'es',
                'date_widget' => new sfWidgetFormI18nDate(array(
                                'format' => '%day%-%month%-%year%',
                                'culture'=>'es',
                                'empty_values' => array('day'=>'<- Día ->',
                                'month'=>'<- Mes ->',
                                'year'=>'<- Año ->'),
                                'years' => array_combine($years, $years)))
                ));
                
                echo $w->render('valores_documento['.$valores->getId().'#f]');
            } ?>
        </td>
    </tr>
<?php } ?>
</table>
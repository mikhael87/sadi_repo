<?php 
    if ($corporal!=''){
        $id = $corporal->getFuncionarioId();
?>
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">
                <b>Colores:</b><br>
                &nbsp;&nbsp;&nbsp;Ojos<br/>
                &nbsp;&nbsp;&nbsp;Cabello<br/>
                &nbsp;&nbsp;&nbsp;Piel<br/>
            </label>
            <div class="content"><br/>
                <?php echo image_tag('icon/ficha/ojos_'.$corporal->getColorOjos()).' '.$corporal->getColorOjos(); ?><br/>
                <?php echo image_tag('icon/ficha/cabello_'.$corporal->getColorCabello()).' '.$corporal->getColorCabello(); ?><br/>
                <?php echo image_tag('icon/ficha/piel_'.$corporal->getColorPiel()).' '.$corporal->getColorPiel(); ?><br/>
            </div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">
                <b>Medidas:</b><br>
                &nbsp;&nbsp;&nbsp;Peso<br/>
                &nbsp;&nbsp;&nbsp;Altura<br/>
            </label>
            <div class="content"><br/>
                <?php echo $corporal->getPeso(); ?> (Kg)<br/>
                <?php echo $corporal->getAltura(); ?> (Cm)<br/>
            </div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">
                <b>Tallas:</b><br>
                &nbsp;&nbsp;&nbsp;Gorra<br/>
                &nbsp;&nbsp;&nbsp;Camisa<br/>
                &nbsp;&nbsp;&nbsp;Pantalon<br/>
                &nbsp;&nbsp;&nbsp;Calzado<br/>
            </label>
            <div class="content"><br/>
                <?php echo $corporal->getTallaGorra(); ?><br/>
                <?php echo $corporal->getTallaCamisa(); ?><br/>
                <?php echo $corporal->getTallaPantalon(); ?><br/>
                <?php echo $corporal->getTallaCalzado(); ?><br/>
            </div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Tipo de sangre</label>
            <div class="content">
                <?php echo $corporal->getTipoSangre(); ?>&nbsp;
            </div>
        </div>
    </div>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Formula de lentes</label>
            <div class="content">
                <?php echo $corporal->getLentesFormula(); ?>&nbsp;
            </div>
        </div>
    </div>

<?php } else { ?>
    <div class="sf_admin_form_row sf_admin_text" style="min-height: 70px;">
        <div class="f16n gris_medio" style="text-align: justify;">
            Sabias que tu información corporal tendrá diferentes usos, 
            como por ejemplo tus tallas de camisa, pantalón y zapatos pueden 
            usarse para la adquisición de uniformes o prendas de vestir para eventos, 
            de igual manera tu tipo de sangre para en caso de emergencia 
            tener información para equipos médicos. 
        </div>
    </div>
<?php } ?>

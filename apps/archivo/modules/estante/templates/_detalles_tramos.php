    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_alto_tramos">
        <div style="">
            <label for="archivo_estante_alto_tramos">Tamaño de los tramos o gavetas</label>
            <div class="content" style="position: relative; width: 800px; height: 120px;">
                <div style="position: absolute; width: 200px; left: 80px;">
                    <div class="content">
                        <label for="archivo_estante_alto_tramos">Alto</label>
                        <select id="archivo_estante_alto_tramos" name="archivo_estante[alto_tramos]">
                            <option value=''></option>
                            <?php for($i=30;$i<201;$i++) { 
                                $select = ""; if($i==$form['alto_tramos']->getValue()) $select = "selected";
                                echo "<option value='".$i."' ".$select.">".$i."</option>"; 
                            } ?>                
                        </select>
                    </div>
                    <div style="position: absolute; top: 0px; left: 40px;"><?php echo image_tag('other/estante_alto.jpg'); ?></div>
                    <div class="help">centimetros</div>
                </div>

                <div style="position: absolute; width: 200px; left: 250px;">
                    <div class="content">
                        <label for="archivo_estante_ancho_tramos">Ancho</label>
                        <select id="archivo_estante_ancho_tramos" name="archivo_estante[ancho_tramos]">
                            <option value=''></option>
                            <?php for($i=30;$i<201;$i++) { 
                                $select = ""; if($i==$form['ancho_tramos']->getValue()) $select = "selected";
                                echo "<option value='".$i."' ".$select.">".$i."</option>"; 
                            } ?>                  
                        </select>
                    </div>
                    <div style="position: absolute; top: 0px; left: 40px;"><?php echo image_tag('other/estante_ancho.jpg'); ?></div>
                    <div class="help">centimetros</div>
                </div>

                <div style="position: absolute; width: 200px; left: 420px;">
                    <div class="content">
                        <label for="archivo_estante_largo_tramos">Largo</label>
                        <select id="archivo_estante_largo_tramos" name="archivo_estante[largo_tramos]">
                            <option value=''></option>
                            <?php for($i=30;$i<201;$i++) { 
                                $select = ""; if($i==$form['largo_tramos']->getValue()) $select = "selected";
                                echo "<option value='".$i."' ".$select.">".$i."</option>"; 
                            } ?>
                        </select>
                    </div>
                    <div style="position: absolute; top: 0px; left: 40px;"><?php echo image_tag('other/estante_largo.jpg'); ?></div>
                    <div class="help">centimetros</div>
                </div>
            </div>
            <div class="help">La información del tamaño de los tramos o gavetas sera utilizada para calcular la capacidad de cajas y carpetas que soporta cada uno.</div>
        </div>

    </div>

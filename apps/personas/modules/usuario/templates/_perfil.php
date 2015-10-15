<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_perfil">
    <div>
        <label for="personas_persona_ci">Perfil SADI</label>
        <div class="content" style="position: relative;">
            <?php $perfiles= Doctrine::getTable('Acceso_Perfil')->findByStatus('A');
            $perfil_edit = 0; $perfil_id_edit= 0; if(!$form->isNew()) { $perfil_edit = $form['id']->getValue(); }
            
            if(!$form->isNew()) {
                $perfil_ar= Doctrine::getTable('Acceso_Perfil')->perfilesActivosPerPersona($perfil_edit);
                foreach($perfil_ar as $val) {
                    $perfil_id_edit= $val->getId();
                }
            } ?>
            
            
            <select id="personas_persona_perfil" name="personas_persona_perfil">
                <?php
                foreach($perfiles as $perfil) {
                    echo '<option value="'. $perfil->getId() .'" '. ((!$form->isNew())? (($perfil_id_edit== $perfil->getId())? 'selected':'') : '' ) .'>'. $perfil->getNombre() .'</option>';
                }
                ?>
            </select>
        </div>

        <div class="help">Perfil de acceso para este usuario</div>
    </div>
</div>

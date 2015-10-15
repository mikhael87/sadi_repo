<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_mensajes_email">
    <div>
        <label for="public_mensajes_opciones">Opciones</label>
        <div class="content">
            <input type="checkbox" name="mensajes_email" id="mensajes_email"> Enviar correo electrónico &nbsp;&nbsp;&nbsp;
            <?php
            $actual = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
            if (count($actual) > 0) {
                $actual = $actual[0];
                $cargo_id = $actual->getIdCargo();
                $activo= false;
                
                $sf_sms = sfYaml::load(sfConfig::get("sf_root_dir") . "/config/siglas/sms.yml");
                if ($sf_sms['activo'] == true && $sf_sms['aplicaciones']['mensajes']['activo'] == true)
                    $activo= true;

                if (is_array($sf_sms['aplicaciones']['mensajes']['autorizados']['otros'])){
                    if (in_array($cargo_id, $sf_sms['aplicaciones']['mensajes']['autorizados']['otros']) || $cargo_id == $sf_sms['aplicaciones']['mensajes']['autorizados']['unico']) {
                        if ($activo == true){
                            ?><input type="checkbox" name="mensajes_sms" id="mensajes_sms"> Enviar SMS<?php
                        }else{
                            echo image_tag('icon/error', array('class'=> 'tooltip', 'title'=> '[!]Modulo Inactivo[/!]Contacte con el administrador del SIGLAS para mas información'));
                        }
                    }
                }else{
                    if ($cargo_id == $sf_sms['aplicaciones']['mensajes']['autorizados']['unico']) {
                        if ($activo == true){
                            ?><input type="checkbox" name="mensajes_sms" id="mensajes_sms"> Enviar SMS<?php
                        }else{
                            echo image_tag('icon/error', array('class'=> 'tooltip', 'title'=> '[!]Modulo Inactivo[/!]Contacte con el administrador del SIGLAS para mas información'));
                        } 
                    }    
                }
            }
            ?>
        </div>
        <div class="help">Seleccione las opciones que desea para este mensaje.</div>
    </div>
</div>
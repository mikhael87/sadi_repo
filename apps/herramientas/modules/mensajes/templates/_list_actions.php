<?php echo $helper->linkToNew(array(  'params' =>   array(  ),  'class_suffix' => 'new',  'label' => 'New',)) ?>
<li class="sf_admin_action_grupo">
  <?php //echo link_to(__('Grupos', array(), 'messages'), 'mensajes/grupos', array()) ?>
</li>
<li class="sf_admin_action_externo">
    <?php
    $activo= false;
    $activo_general= false;
    $mis_modems= Sms::modems_per_user();
    
    if (empty($mis_modems)) {
        $mis_modems = array(0 => '');
    }

    $sf_sms = sfYaml::load(sfConfig::get("sf_root_dir") . "/config/siglas/sms.yml");

    if ($sf_sms['activo'] == true && $sf_sms['aplicaciones']['mensajes_externos']['activo'] == true)
        $activo = true;
    if ($sf_sms['activo'] == true)
        $activo_general = true;

    if($activo_general) {
        if($activo) {
            if($mis_modems[0]!= '') {
                if (count($mis_modems) > 0 || $mis_modems[0] == 'all') {
                    echo link_to(__('Mensajes Externos', array(), 'messages'), 'mensajes/externo', array());
                } else {
                    echo image_tag('icon/error', array('class' => 'tooltip', 'title' => '[!]Sin Modems Asignados[/!]Contacte con el administrador del SIGLAS para mas información', 'style' => 'vertical-align: middle'));
                }
            }
        }else {
            echo image_tag('icon/error', array('class'=> 'tooltip', 'title'=> '[!]Modulo Inactivo[/!]Contacte con el administrador del SIGLAS para mas información', 'style' => 'vertical-align: middle'));
        }    
    }else {
        echo image_tag('icon/error', array('class'=> 'tooltip', 'title'=> '[!]Modulo Inactivo[/!]Contacte con el administrador del SIGLAS para mas información', 'style' => 'vertical-align: middle'));
    }?>
</li>

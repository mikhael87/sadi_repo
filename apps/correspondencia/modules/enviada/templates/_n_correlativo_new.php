<?php
$correlativo_funcionario = Doctrine::getTable('Correspondencia_FuncionarioCorrelativo')->findByFuncionarioId(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
$session_funcionario = sfContext::getInstance()->getUser()->getAttribute('session_funcionario');
$secuencia_replace=1;
$options = '';
if ($correlativo_funcionario[0]['id'])
{
    $listo=0;
    //$secuencia_replace = $correlativo_unidad->getSecuencia();
    $secuencia_replace = $correlativo_funcionario[0]['secuencia'];
    //$secuencia = $correlativo_funcionario[0]['secuencia'];
    while($listo == 0)
    {
        $correspondencia_find = Doctrine::getTable('Correspondencia_Correspondencia')->findByNCorrespondenciaEmisor($session_funcionario['cedula'].'-'.$secuencia_replace);

        if($correspondencia_find[0]['id'])
            $secuencia_replace++;
        else
            $listo = 1;
    }
}
else
    $secuencia = 1;

$nomenclatura = $session_funcionario['cedula'].'-'.$secuencia_replace;
$options .= '<option value="P.!#!.'.$nomenclatura.'">'.$nomenclatura.'  :  Personal</option>';

//-------------------------------------------------------------

$correlativo_unidad = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));

foreach ($correlativo_unidad as $correlativo_unidad_x) {
    $correlativos_unidad = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findByUnidadIdAndStatusAndTipo($correlativo_unidad_x->getAutorizadaUnidadId(),'A','E');
    $unidad = Doctrine::getTable('Organigrama_Unidad')->find($correlativo_unidad_x->getAutorizadaUnidadId());

    foreach ($correlativos_unidad as $correlativo_unidad) {
        $nomenclatura = $correlativo_unidad->getNomenclador();

        $nomenclatura = str_replace("Siglas", $unidad->getSiglas(), $nomenclatura);
        $nomenclatura = str_replace("Letra", $correlativo_unidad->getLetra(), $nomenclatura);
        $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
        $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
        $nomenclatura = str_replace("Día", date('d'), $nomenclatura);
        $nomenclatura_reinicio = str_replace("Secuencia", "1", $nomenclatura);
        $nomenclatura = str_replace("Secuencia", $correlativo_unidad->getSecuencia(), $nomenclatura);

        if($correlativo_unidad->getUltimoCorrelativo()!='')
        {
            $verificar = Doctrine::getTable('Correspondencia_Correspondencia')->findByNCorrespondenciaEmisor($nomenclatura_reinicio);
            
            if(!$verificar[0]['id'])
               $nomenclatura = $nomenclatura_reinicio;


            // DESDE AQUI !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $listo=0;
            $i=0;
            $secuencia_replace = $correlativo_unidad->getSecuencia();
            while($listo == 0)
            {
                $correspondencia_find = Doctrine::getTable('Correspondencia_Correspondencia')->findByNCorrespondenciaEmisor($nomenclatura);

                if($correspondencia_find[0]['id'])
                {
                    $i++;
                    
                    $nomenclatura = $correlativo_unidad->getNomenclador();

                    $nomenclatura = str_replace("Siglas", $unidad->getSiglas(), $nomenclatura);
                    $nomenclatura = str_replace("Letra", $correlativo_unidad->getLetra(), $nomenclatura);
                    $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
                    $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
                    $nomenclatura = str_replace("Día", date('d'), $nomenclatura);
                    $nomenclatura = str_replace("Secuencia", $secuencia_replace++, $nomenclatura);
                }
                else
                {
                    $listo = 1;
                    
                    if($i>0)
                    {
                        $correlativos_unidad_edit = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneById($correlativo_unidad->getId());
                        $correlativos_unidad_edit->setUltimoCorrelativo('RESTABLECIDO');
                        $correlativos_unidad_edit->setSecuencia($secuencia_replace-1);
                        $correlativos_unidad_edit->save();
                    }
                }
            }
            // HASTA AQUI !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }

        $options .= '<option value="'.$correlativo_unidad->getId().'.!#!.'.$nomenclatura.'">'.$nomenclatura.'  :  '.$correlativo_unidad->getDescripcion().'</option>';
    }
}
?>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_receptor_persona_cargo">
    <div>
        <label for="correspondencia_correspondencia_receptor_persona_cargo">Nº Envio</label>
        <div class="content">
            <select name="correspondencia_correspondencia[n_correspondencia_emisor]" id="correspondencia_correspondencia_n_correspondencia_emisor">
                <?php echo $options; ?>
            </select>

        </div>

        <div class="help">Seleccione el correlativo que utilizara para esta correspondencia</div>
    </div>
</div>
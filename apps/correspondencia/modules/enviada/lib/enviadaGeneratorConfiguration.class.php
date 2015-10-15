<?php

/**
 * enviada module configuration.
 *
 * @package    siglas-(institucion)
 * @subpackage enviada
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class enviadaGeneratorConfiguration extends BaseEnviadaGeneratorConfiguration
{
    public function getFilterDefaults()
    {
        $variables_entorno = sfContext::getInstance()->getUser()->getAttribute('sf_variables_entorno');
        return array('status' => $variables_entorno['correspondencia']['bandeja_enviada_defecto']);
    }

}

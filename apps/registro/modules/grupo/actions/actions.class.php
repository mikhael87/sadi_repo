<?php

require_once dirname(__FILE__).'/../lib/grupoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/grupoGeneratorHelper.class.php';

/**
 * grupo actions.
 *
 * @package    siglas
 * @subpackage grupo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class grupoActions extends autoGrupoActions
{
    public function executeVolver(sfWebRequest $request)
    {
        $this->redirect('disciplina/index?id='.$this->getUser()->getAttribute('pae_registro_id'));
    }
}

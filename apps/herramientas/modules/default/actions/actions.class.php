<?php
 /***/
/**
 * default actions.
 *
 * @package    sac
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class defaultActions extends sfActions
{
 public function executeDisabled(sfWebRequest $request)
  {}

  public function executeError404(sfWebRequest $request)
  {}

  public function executeLogin(sfWebRequest $request)
  {}

  public function executeSecure(sfWebRequest $request)
  {}
}

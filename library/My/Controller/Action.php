<?php
require_once 'Zend/Controller/Action.php';

class My_Controller_Action extends Zend_Controller_Action
{
    protected $_userName;

    public function getUserName()
    {
        return $this->_userName;
    }


  public function init()
  {
    /*require_once('Zend/Auth.php');
    $auth = Zend_Auth::getInstance();
    
    if ($auth->hasIdentity())
    {
      $user = $auth->getIdentity();
      $this->_userName = $user->first_name;
    }
    else
    {
      $this->_redirect('auth/login');
    }*/

  }

}
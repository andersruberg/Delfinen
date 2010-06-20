<?php

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';

class AuthController extends Zend_Controller_Action
{
  public function indexAction()
  {
    $this->_redirect('auth/login');
  }
  
  public function loginAction()
  {
    $this->view->message = $this->_getParam('message','');
    $this->view->form = $this->getLoginForm();
    #$this->_helper->layout->disableLayout();
    $this->view->title = "Logga in";
    $this->view->headTitle($this->view->title, 'PREPEND');
  }
  
  public function logoutAction()
  {
    Zend_Auth::getInstance()->clearIdentity();
    $this->view->form = $this->getLoginForm();
    $this->view->message = 'Utloggningen lyckades';
    #$this->_helper->layout->disableLayout();
    $this->render('login');
   }
   
  public function getLoginForm()
  {
    $form = new Zend_Form();
    $form->setAction('process')->setMethod('post');
    $username = $form->createElement('text', 'username', array('label' => 'Anv�ndarnamn'));
    $username->addValidator('alnum')
             ->addValidator('regex', false, array('/^[a-z]+/'))
             ->addValidator('stringLength', false, array(4, 20))
             ->setRequired(true)
             ->addFilter('StringToLower');
    $password = $form->createElement('password', 'password',array('label' => 'L�senord'));
    $password->addValidator('StringLength', false, array(4))->setRequired(true);
    $form->addElement($username)
         ->addElement($password)
         ->addElement('submit', 'login', array('label' => 'Login'));
    return $form;
  }
  
  public function processAction()
  {
    $form = $this->getLoginForm();
    if (!$form->isValid($_POST))
    {
      $this->view->form = $form;
      return $this->render('login');
    }
    

    require_once 'Zend/Auth/Adapter/DbTable.php';
    $users = new Model_DbTable_Users();
    
    /*$auth_adapter = new Zend_Auth_Adapter_DbTable($users,'users', 'username', 'password');
    $auth_adapter -> setIdentity($this->_request->getPost('username'))
                  -> setCredential($this->_request->getPost('password'));
    
    $auth = Zend_Auth::getInstance();*/
    
    $result = $users->authenticate($this->_request->getPost('username'), $this->_request->getPost('password'));
    
    if (!$result->isValid())
    {
      $this->view->form = $form;
      $this->view->message = 'Inloggningen misslyckades';
      return $this->render('login');
    }

     
    $this->_redirect('/');
    
  }
}


?>

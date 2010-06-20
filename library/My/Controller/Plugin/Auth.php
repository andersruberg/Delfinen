<?php
class My_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    private $_acl = null;
    private $_auth = null;

    public function __construct(Zend_Acl $acl, Zend_Auth $auth)
    {
        $this->_acl = $acl;
        $this->_auth = $auth;
        
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $resource = $request->getControllerName();
        $action = $request->getActionName();

        
        
        $role = $this->_auth->getIdentity()->role;
        var_dump($role);
        var_dump($resource);
        var_dump($action);
        var_dump($this->_acl->isAllowed($role, $resource));
            /*if(!$this->_acl->isAllowed($role, $resource, $action)) {
                $request->setControllerName('auth')
                        ->setActionName('login');
            } */
        $request->setControllerName($resource)
                        ->setActionName($action);
    }
}
<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    private $_acl = null;
    private $_auth = null;

    protected function _initAutoload()
	{
            $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH));

            setlocale(LC_TIME, "se_SE");

            /*$this->_acl = new My_Acl();
            $this->_auth = Zend_Auth::getInstance();
            if (!$this->_auth->hasIdentity()) {
                $this->_auth->getIdentity()->role = 'guest';
            }

            $fc = Zend_Controller_Front::getInstance();
            $fc->registerPlugin(new My_Controller_Plugin_Auth($this->_acl, $this->_auth));*/

            return $moduleLoader;
	}
        
	protected function _initViewHelpers()
	{
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$view->headTitle()->setSeparator(' - ');
		$view->headTitle('Dykklubben Delfinen');
	}
	protected function _initNavigation()
	{
	    $this->bootstrap('layout');
	    $layout = $this->getResource('layout');
	    $view = $layout->getView();
	    $config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml', 'nav');
	    $navigation = new Zend_Navigation($config);
	    $view->navigation($navigation);
	}
}
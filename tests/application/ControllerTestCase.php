<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/**
 * Description of ControllerTestCase
 *
 * @author Anders
 */
class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {

    protected $application;

    protected function setUp()
    {
        $this->bootstrap=array($this, 'appBootstrap');
        //Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_NonPersistent());
        parent::setUp();
    }

    protected function tearDown()
    {
        //Zend_Auth::getInstance()->clearIdentity();
    }

    protected function appBootstrap()
    {
        $this->application = new Zend_Application(APPLICATION_ENV . APPLICATION_PATH . '/configs/application.ini');
    }
}


<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of GData
 *
 * @author Anders
 */
abstract class Model_GData {

    protected $_client;
    protected $_service;

    protected function __construct($authServiceName) {
        Zend_Loader::loadClass('Zend_Gdata');
        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
        Zend_Loader::loadClass('Zend_Http_Client');
        Zend_Loader::loadClass('Zend_Registry');

        //TODO: Prepere for CAPTCHA response
        //se http://www.ngoprekweb.com/2006/11/04/clientlogin-authentication-for-zend-gdata/
        
        try {
            $client = Zend_Registry::get($authServiceName);
            $this->_client = $client;
            
        }
        catch (Zend_Exception $e) {
            
            $this->_client = Zend_Gdata_ClientLogin::getHttpClient(
                    'dykklubben.delfinen@gmail.com',
                    'dykameddelfinen',
                    $authServiceName);
        }
    }
}


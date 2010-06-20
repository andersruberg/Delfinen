<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GDocs
 *
 * @author Anders
 */
class Model_GDocs extends Model_GData {

    public function __construct() {

        parent::__construct(Zend_Gdata_Docs::AUTH_SERVICE_NAME);
        Zend_Loader::loadClass('Zend_Gdata_Docs');

        $this->_service = new Zend_Gdata_Docs($this->_client);
    }

    public function getItems() {

        try {
            return $this->_service->getDocumentListFeed();
        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getResponse();
        }
    }

}


<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DocsController
 *
 * @author Anders
 */
class DocsController extends My_Controller_Action {

    public function listAction()
    {
        $docs = new Model_GDocs();

        $this->view->docs = $docs->getItems();
    }
}

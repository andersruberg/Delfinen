<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhotoController
 *
 * @author Anders
 */
class PhotoController extends My_Controller_Action {

    protected $_photos;

    public function init()
    {
        
    }

    public function preDispatch() {
        
        $this->_photos = new Model_GPhoto();
    }


    public function thumbnailsAction() {
        
        $this->_helper->viewRenderer->setResponseSegment('thumbnails');
        
        
        
        $this->view->photos = $this->_photos->getRandomPhotosCached(4);
        
    }

    public function indexAction() {

        $this->_helper->actionStack('list', 'calendar');
        //$this->_helper->actionStack('thumbnails', 'photo');
        $this->_helper->actionStack('list', 'blog');

        $this->_helper->layout()->setLayout('alternative');

        $id = $this->_getParam('albumid');
        if ($id != null) {
            $this->view->viewMode = 'album';
            $this->view->albumId = $id;
        }
        else {
            $this->view->viewMode = 'albums';
            $this->view->albumId = null;
        }


        #$this->view->albums = $this->_photos->getAll();
    }
    
}


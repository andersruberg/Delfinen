<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of BlogController
 *
 * @author Anders
 */
class BlogController extends My_Controller_Action {

    protected $_blog;

    public function preDispatch() {
        $this->_blog = new Model_GBlogger();
    }

    public function listAction() {

        $this->_helper->viewRenderer->setResponseSegment('news');
        $this->view->posts = $this->_blog->getLatestEntries($labels='nyhet', $maxResults=10);
    }

    public function indexAction() {
        $this->_helper->actionStack('list', 'calendar');
        $this->_helper->actionStack('thumbnails', 'photo');
        $this->_helper->actionStack('list', 'blog');

        $this->view->posts = $this->_blog->getLatestEntries();
    }

}


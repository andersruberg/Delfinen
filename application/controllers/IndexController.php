<?php
  
  class IndexController extends My_Controller_Action
  {
      public function init()
      {
          $this->_helper->actionStack('list', 'calendar');
    $this->_helper->actionStack('thumbnails', 'photo');
    $this->_helper->actionStack('list', 'blog');
          
      }
  

  public function indexAction()
  {
    

    $this->view->title = "Välkommen till DK Delfinen";
    $this->view->headTitle($this->view->title, 'PREPEND');

  }
  
  public function addAction()
  {
      $this->view->title = "Lägg till album";
      $this->view->headTitle($this->view->title, 'PREPEND');
  }
  
  public function editAction()
  {
  // action body
  }
  
  public function deleteAction()
  {
  // action body
  }
  
  
  }
  







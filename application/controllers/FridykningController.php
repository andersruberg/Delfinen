<?php

class FridykningController extends My_Controller_Action
{
	public function indexAction()
	{
		$this->view->title = "Fridykning";
		$this->view->headTitle($this->view->title, 'PREPEND');
	}
}
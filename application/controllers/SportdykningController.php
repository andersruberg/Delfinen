<?php

class SportdykningController extends My_Controller_Action
{
	public function indexAction()
	{
		$this->view->title = "Sportdykning";
		$this->view->headTitle($this->view->title, 'PREPEND');
	}
}
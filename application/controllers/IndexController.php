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
	
	public function contactAction()
	{
		$form = new Form_Contact();		
		$this->view->form = $form;
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($form->isValid($formData))
			{
				$mail = new Zend_Mail();
				$mail->setBodyText($formData['message']);
				$mail->setFrom($formData['email'], $formData['name']);
				$mail->addTo('marcus@behrensgroup.se', 'DK Delfinen');
				$mail->setSubject('Meddelande från dkdelfinen.se');
				$mail->send();

				$this->_redirect('/index/contact');
			}
		}
	}
	
	public function styrelseAction()
	{
		
	}
	
	public function medlemAction()
	{
		
	}
	
	public function loginAction()
	{
		
	}
}
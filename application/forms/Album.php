<?php

class Form_Album extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setName('album');

		$id = new Zend_Form_Element_Hidden('id);
		$artist = new Zend_Form_Element_Text('artist');
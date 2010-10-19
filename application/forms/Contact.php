<?php
class Form_Contact extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setName('contact_form');
		
		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Namn')
					->setRequired(true)
					->setAttrib('size', 30)
					->addValidator('NotEmpty');
		
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('E-postadress')
					->setRequired(true)
					->setAttrib('size', 30)
					->addValidator('NotEmpty');
					
		$phone = new Zend_Form_Element_Text('phone');
		$phone->setLabel('Telefonnummer')
					->setRequired(true)
					->setAttrib('size', 30);
			
		$message = new Zend_Form_Element_Textarea('message');
        $message->addFilter('StringTrim')
        			->setLabel('Meddelande')
        			->setRequired(true)
        			->setAttrib('rows',7)
        			->setAttrib('cols',80)
        			->addValidator('NotEmpty');
		
		
		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Skicka');
		
		$this->addElements(array($name, $email, $phone, $message, $submit));
	} 
}
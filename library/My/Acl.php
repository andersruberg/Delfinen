<?php

class My_Acl extends Zend_Acl
{
	public function __construct()
	{

		$this->add(new Zend_Acl_Resource(My_Resources::ADMIN_SECTION));
		$this->add(new Zend_Acl_Resource(My_Resources::PUBLIC_PAGE));
		$this->add(new Zend_Acl_Resource(My_Resources::MEMBER_PAGE));
		$this->add(new Zend_Acl_Resource(My_Resources::STYRELSE_PAGE));

		$this->addRole(new Zend_Acl_Role(My_Roles::GUEST));
		$this->addRole(new Zend_Acl_Role(My_Roles::MEMBER, My_Roles::GUEST));
		$this->addRole(new Zend_Acl_Role(My_Roles::ADMIN, My_Roles::MEMBER));

                // assign privileges
                $this->allow(My_Roles::GUEST, My_Resources::PUBLIC_PAGE);
                $this->allow(My_Roles::MEMBER, My_Resources::MEMBER_PAGE);
                $this->allow(My_Roles::ADMIN, My_Resources::MEMBER_PAGE, 'add');
                
	}

}
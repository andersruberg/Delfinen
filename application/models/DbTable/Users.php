<?php

require_once('Zend/Auth/Adapter/DbTable.php');
require_once('Zend/Db/Table/Abstract.php');

class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	
        public function getUser($id)
	{
		
	}
	
	public function addUser( $openId, $firstName, $lastName, $email, $role, $status)
	{

            $data = array(
                'openId' => $openId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'role' => $role,
                'status' => $status
                );

         

         $this->insert($data);
		
	}

	public function updateUser($id, $user, $password, $real_name, $email)
	{
		
	}

	public function deleteUser($id)
	{
		$this->delete('id = ' . (int)$id);
	}
}
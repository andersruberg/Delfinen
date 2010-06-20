<?php

class Model_DbTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	
	public function authenticate($username, $password)
	{
    $db_adapter = $this->getDefaultAdapter();
    $auth_adapter = new Zend_Auth_Adapter_DbTable($db_adapter,'users', 'username', 'password_hash');
    $auth_adapter -> setIdentity($username)
                  -> setCredential($password);
    
    $auth = Zend_Auth::getInstance();
    
    $result = $auth->authenticate($auth_adapter);

    $user = $auth_adapter->getResultRowObject(null, 'password_hash');
    var_dump($auth->getIdentity());
    $authStorage = $auth->getStorage();
    $authStorage->write($user);
    
    return $result;
  }
  
  public function getUser($id)
	{
		
	}
	
	public function addUser($user, $password, $real_name, $email)
	{
		
	}

	public function updateUser($id, $user, $password, $real_name, $email)
	{
		
	}

	public function deleteUser($id)
	{
		$this->delete('id = ' . (int)$id);
	}
}
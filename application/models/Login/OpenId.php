<?php 

require_once('Zend/Db.php');
require_once('Zend/Auth.php');
require_once('Zend/Session.php');
require_once('Zend/Auth/Adapter/DbTable.php');

require_once "Auth/OpenID/google_discovery.php";
require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/FileStore.php";
require_once "Auth/OpenID/PAPE.php";
require_once "Auth/OpenID/AX.php";

require_once "ZendDatabaseStore.php";


class Model_Login_OpenId {
    function __construct($identifier=null) {
        $this->openid_identifier = $identifier;

        $this->db = Zend_Db::factory('Pdo_Mysql', array(
                'host'     => 'db-103967.mysql.binero.se',
                'username' => '103967_cm18023',
                'password' => 'gV173iL2',
                'dbname'   => '103967-db'
        ));

            $this->authAdapter = new Zend_Auth_Adapter_DbTable($this->db,
                'users',
                'openId',
                'email');

    }

    function authenticate($openId,$email) {
       $this->authAdapter->setIdentity($openId)
                ->setCredential($email);

        $this->authInstance = Zend_Auth::getInstance();
        
        $result = $this->authInstance->authenticate($this->authAdapter);
        
        if ($result->getCode() == Zend_Auth_Result::SUCCESS) {
            $resultData = $this->authAdapter->getResultRowObject();
            $this->authInstance->getStorage()->write($resultData);
            return true;
        }

        return false;
    }

    function createUser() {
        $openId = htmlentities($this->response->getDisplayIdentifier());
        $email = $this->registrationData['email'];
	$firstName = $this->registrationData['firstname'];
	$lastName = $this->registrationData['lastname'];
        $namePerson = $this->registrationData['namePerson'];
        
        $this->db->insert('users', array(
            'openId' => $openId,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'role' => 'user',
            'status' => 'active'));

        return $this->db->lastInsertId();

    }

    function authorizationRequest() {
        // required for YADIS discovery
        Zend_Session::start();

        $store = $this->_createDataStore();
        $consumer = new Auth_OpenID_Consumer($store);
        new GApps_OpenID_Discovery($consumer);
        error_log("Identifier is "+$this->openid_identifier);
        $this->auth_request = $consumer->begin($this->openid_identifier);
        
        return $this->auth_request;
    }


    function redirectProvider($host,$url) {
        $urlRedirect = $this->auth_request->redirectURL($host,$url);
        header('Location: ' . $urlRedirect);
    }


    function complete($url) {
        // required for YADIS
        Zend_Session::start();

        $store = $this->_useDataStore();

        // Create OpenID consumer
        $consumer = new Auth_OpenID_Consumer($store);
        new GApps_OpenID_Discovery($consumer);
        
        // Create an authentication request to the OpenID provider
        $this->response = $consumer->complete($url);
        if ($this->response->status == Auth_OpenID_SUCCESS) {
            $ax = new Auth_OpenID_AX_FetchResponse();
            $this->registration = $ax->fromSuccessResponse($this->response);
            $this->normalizeRegistrationData();
            return $this->response;
        }

        if ($this->response->status == Auth_OpenID_FAIL) {
            error_log("Completion failure ".$this->reponse->status);
        }
        else
        if ($this->response->status == Auth_OpenID_SETUP_NEEDED) {
            error_log("Setup error");
        }
        return false;
    }


//--------------------------------------------------------------------------------------------------	

    function setupRegistrationQuery() {
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email',1,1, 'email');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson',1,1, 'nameperson');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first',1,1, 'firstname');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last',1,1, 'lastname');

        // Create AX fetch request
        $ax = new Auth_OpenID_AX_FetchRequest;

        // Add attributes to AX fetch request
        foreach($attribute as $attr) {
            $ax->add($attr);
        }

        // Add AX fetch request to authentication request
        $this->auth_request->addExtension($ax);
    }

    function normalizeRegistrationData() {
        $this->registrationData['email'] = $this->registration->data['http://axschema.org/contact/email'][0];
        $this->registrationData['namePerson'] = $this->registration->data['http://axschema.org/namePerson'][0];
        $this->registrationData['lastname'] = $this->registration->data['http://axschema.org/namePerson/last'][0];
        $this->registrationData['firstname'] = $this->registration->data['http://axschema.org/namePerson/first'][0];
    }

    // private functions
    function _createDataStore() {
        $store = new Model_Login_ZendDatabaseStore($this->db);
        $store->createTables();
        return $store;
        //return new Auth_OpenID_FileStore('/tmp/oid_store');
    }

    function _useDataStore() {
        $store = new Model_Login_ZendDatabaseStore($this->db);
        return $store;
        //return new Auth_OpenID_FileStore('/tmp/oid_store');
    }
}
?>
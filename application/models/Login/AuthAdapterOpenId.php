<?php

require_once('Zend/Auth/Adapter/Interface.php');

class Model_Login_AuthAdapterOpenId implements Zend_Auth_Adapter_Interface {

    /**
     * The identity value being authenticated
     *
     * @var string
     */
    private $_id = null;

    /**
     * The URL to redirect response from server to
     *
     * @var string
     */
    private $_returnTo = null;

    /**
     * The HTTP URL to identify consumer on server
     *
     * @var string
     */
    private $_root = null;
    /**
     * Extension object or array of extensions objects
     *
     * @var string
     */
    private $_extensions = null;

    private $_check_immediate;


    public function __construct($id = null,
            $returnTo = null,
            $root = null,
            $extensions = null) {

        $this->_id         = $id;
        $this->_returnTo   = $returnTo;
        $this->_root       = $root;
        $this->_extensions = $extensions;
        $this->_check_immediate = false;


    }

    /**
     * Authenticates the given OpenId identity.
     * Defined by Zend_Auth_Adapter_Interface.
     *
     * @throws Zend_Auth_Adapter_Exception If answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        echo "in authenticate";
        $id = $this->_id;

        if (!empty($id)) {
            $login = new Model_Login_OpenId($id);

            /* login() is never returns on success */
            if ($this->_check_immediate) { {
                    return new Zend_Auth_Result(
                            Zend_Auth_Result::FAILURE,
                            $id,
                            array("Authentication failed"));
                }
            } else {
                echo "Before authorizationRequest";
                if (!$login->authorizationRequest()) {
                    return new Zend_Auth_Result(
                            Zend_Auth_Result::FAILURE,
                            $id,
                            array("Authentication failed"));
                }

                $login->setupRegistrationQuery();
                $login->redirectProvider($this->_root,$this->_returnTo);
            }
        } else {

            $login = new Model_Login_OpenId();

            if ($login->complete($this->_returnTo)) {

                $openid = $login->response->getDisplayIdentifier();
                $esc_openid =  htmlentities($openid);

                return new Zend_Auth_Result(
                        Zend_Auth_Result::SUCCESS,
                        array("id" => $esc_openid,
                                "email" => $login->registrationData['email'],
                                "name" => $login->registrationData['firstName'] + $login->registrationData['lastName']),
                        array("Authentication successful"));
            } else {
                return new Zend_Auth_Result(
                        Zend_Auth_Result::FAILURE,
                        array("id" => $esc_openid,
                                "email" => $login->registrationData['email'],
                                "name" => $login->registrationData['firstName'] + $login->registrationData['lastName']),
                        array("Authentication failed"));
            }
        }
    }


}
?>

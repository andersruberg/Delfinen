<?php

require_once 'Zend/Controller/Action.php';

class AuthController extends Zend_Controller_Action {


    public function indexAction() {

        $oid_identifier = 'dkdelfinen.se';

        $login = new Model_Login_OpenId($oid_identifier);

        $request = $login->authorizationRequest();

        if (!$request) {
            die("Authorization failed");
        }

        $REALM = 'http://test.dkdelfinen.se';
        $RETURN_TO = 'http://test.dkdelfinen.se/auth/return';

        $login->setupRegistrationQuery();
        $login->redirectProvider($REALM,$RETURN_TO);

    }



    public function returnAction() {

        $login = new Model_Login_OpenId();
        $RETURN_TO = 'http://test.dkdelfinen.se/auth/return';

        $success = $login->complete($RETURN_TO);

        if ($success) {

            $openid = $login->response->getDisplayIdentifier();

            $esc_identity =  htmlentities($openid);
            
            if ($login->authenticate($esc_identity,$login->registrationData['email'])) {
		$this->view->message = "Välkommen tillbaka";
                $this->view->fullName = $login->registrationData['firstname'] . " " . $login->registrationData['lastname'];
                $this->view->email = $login->registrationData['email'];
            }
            else {
                $id = $login->createUser();
                if ($login->authenticate($esc_identity,$login->registrationData['email']))
                 {
                    $this->view->message = "Välkommen (id " . $id .")";
                    $this->view->fullName = $login->registrationData['firstname'] . " " . $login->registrationData['lastname'];
                    $this->view->email = $login->registrationData['email'];
                 }
                else
                    die("Not authenticated");
            }
        }

    }
}

?>

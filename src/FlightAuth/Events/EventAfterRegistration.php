<?php

namespace FlightAuth\Events;

use Flight;
use FlightAuth;

class EventAfterRegistration implements EventInterface
{

    /***
     * Notify function called at the end of successfully changing email.
     *
     * @param $data array
     * @return void
     */
    public function notify($data)
    {
        $email = $_POST['email'];
        $verification_link = $data['verification_link'];

        // assign/elevate user's privilege
        $auth = Flight::get('auth')->getAuth();
        $auth->admin()->addRoleForUserByEmail($email, \Delight\Auth\Role::CONSUMER);

        // send post-registration (aka 'welcome') email
        $auth = Flight::get('auth');
        $mailer = Flight::get('mailer');
        $mailer->setupEmail();
        $mailer->addAddress($email);
        $mailer->setSubject('Please verify your email...');

        // content starts
        ob_start();
        $auth->render('email/email-verify-email', ['verification_link' => $verification_link]);
        $body = ob_get_clean();
        // content ends

        // send content to user in email
        $mailer->setBody($body);
        $response = $mailer->sendEmail();

    }


}

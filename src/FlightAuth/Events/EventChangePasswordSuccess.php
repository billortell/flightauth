<?php

namespace FlightAuth\Events;

use Flight;
use FlightAuth\Auth;

class EventChangePasswordSuccess implements EventInterface
{

    /***
     * Notify function called at the end of successfully changing email.
     *
     * @param $data array
     * @return void
     */
    public function notify($data)
    {
        $email = $data['email'];

        // other methods you may want to use...
        // $auth = $this->getAuth();
        // $user_id = $auth->getUserId();

        // send user change email address confirmation email
        // user needs to click on verification link to confirm the new email
        $auth = Flight::get('auth');
        $mailer = Flight::get('mailer');
        $mailer->setupEmail();
        $mailer->addAddress($email);
        $mailer->setSubject('Your password was reset');

        // content starts
        ob_start();
        $auth->render('email/email-reset-password-success');
        $body = ob_get_clean();
        // content ends

        // send content to user in email
        $mailer->setBody($body);
        $response = $mailer->sendEmail();

    }


}

<?php

namespace FlightAuth\Events;

use Flight;

class EventVerificationEmailResend implements EventInterface
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

        // send user change email address confirmation email
        // user needs to click on verification link to confirm the new email
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

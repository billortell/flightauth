<?php

namespace FlightAuth\Events;

use Flight;

class EventForgotPasswordConfirmation implements EventInterface
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
        $reset_link = $data['reset_link'];

        // send user change email address confirmation email
        // user needs to click on verification link to confirm the new email
        $auth = Flight::get('auth');
        $mailer = Flight::get('mailer');
        $mailer->setupEmail();
        $mailer->addAddress($email);
        $mailer->setSubject('Password reset request confirmation...');


        // content starts
        ob_start();
        $auth->render('email/email-reset-password', ['verification_link' => $reset_link]);
        $body = ob_get_clean();
        // content ends

        // send content to user in email
        $mailer->setBody($body);
        $response = $mailer->sendEmail();

    }


}

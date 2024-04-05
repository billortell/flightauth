<?php
namespace FlightAuth\Helpers;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends PHPMailer {

    public function __construct()
    {
        $this->setup();
    }

    protected function setup()
    {
        // 0 - Disable Debugging, 2 - Responses received from the server
        $this->SMTPDebug = $_ENV['MAILER_SMTP_DEBUG'];

        // Set mailer to use SMTP
        if ( $_ENV['MAILER_SMTP_ISSMTP'] ) {
            $this->isSMTP();
        }

        // Enable SMTP authentication
        $this->SMTPAuth = $_ENV['MAILER_SMTP_AUTH'];

        //PHPMailer::ENCRYPTION_STARTTLS; Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->SMTPSecure = $_ENV['MAILER_SMTP_SECURE'];

        // 587; // TCP port to connect to
        $this->Port = $_ENV['MAILER_SMTP_PORT'];

        // Set email format to HTML
        $this->isHTML($_ENV['MAILER_SMTP_ISHTML']);

        // user specific details
        $this->Host = $_ENV['MAILER_HOST']; // Specify main and backup SMTP servers

        // SMTP username
        $this->Username = $_ENV['MAILER_USERNAME'];

        // SMTP password
        $this->Password = $_ENV['MAILER_PASSWORD'];

        // SMTP from setup
        $this->setFrom($_ENV['MAILER_FROM_EMAIL'], $_ENV['MAILER_FROM_NAME']);
    }

    public function setupEmail()
    {
        $this->clearAddresses();
        $this->setBody(false);
        $this->setSubject(false);
        return $this;
    }

    public function getBody(){
        return $this->Body;
    }

    public function setBody($body){
        $this->Body = $body;
        return $this;
    }

    public function getSubject(){
        return $this->Subject;
    }

    public function setSubject($subject){
        $this->Subject = $subject;
        return $this;
    }

    public function sendEmail(){
        $response = $this->send();
    }

}



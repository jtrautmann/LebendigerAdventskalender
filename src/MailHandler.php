<?php

class MailHandler {

    /** @var string */
    private $sender;

    public function __construct(string $sender) {
        $this->setSender($sender);
    }

    public function setSender(string $sender) {
        $this->sender = $sender;
    }

    public function sendMail(string $recipient, string $subject, string $text) {
        $encoded_subject = '=?utf-8?B?'.base64_encode($subject).'?=';
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/plain; charset=utf-8";
        $headers[] = "From: $this->sender";
        $headers[] = "Reply-To: $this->sender";
        $headers[] = "X-Mailer: PHP/".phpversion();
        mail($recipient, $encoded_subject, $text, implode("\r\n",$headers));
    }

}
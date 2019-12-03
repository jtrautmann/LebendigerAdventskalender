<?php

class Controller {

    private static $controller;

    private $data_handler;
    private $post_handler;
    private $input_handler;
    private $mail_handler;
    
    public static function getController() : Controller {
        if (!isset(self::$controller)) {
            self::$controller = new Controller();
        }
        return self::$controller;
    }

    private function __construct() {
        // instantiate attributes
        $this->data_handler = new DataHandler();
        $this->post_handler = new PostHandler();
        $this->input_handler = new InputHandler();
        // TODO: make mail address of sender configurable
        $this->mail_handler = new MailHandler('bl-prweb@sfc-karlsruhe.de');
    }

    public function getShowState() {
        if (!$this->isActiveCalendar()) {
            return ShowState::INACTIVE;
        }
        if (!$this->input_handler->doorNumberSet()) {
            return ShowState::CALENDAR;
        }
        $nr = $this->getDoorNumberInput();
        $door_end_time = mktime(23,59,59,12,$nr,date('Y'));
        $now = time();
        if ($now > $door_end_time) {
            return ShowState::PAST_DOOR;
        }
        if (!$this->hasHost($nr)) {
            return ShowState::RESERVATION;
        }
        return ShowState::DOOR;
    }
    
    public function activate() {
        $this->data_handler->initializeDatabase();
    }

    public function deactivate() {
        $this->deactivateCalendar();
        $this->data_handler->deleteDatabase();
    }

    public function activateCalendar() {
        if ($this->isActiveCalendar())
            return true;

        $result = $this->post_handler->createPost();
        if (is_wp_error($result))
            return $result;

        $this->data_handler->setActiveCalendar($result);
        return true;
    }

    public function deactivateCalendar() {
        if (!$this->isActiveCalendar())
            return true;

        $post_id = $this->data_handler->getPostID();
        if (!post_id)
            return false;
        if (!$this->post_handler->deletePost($post_id))
            return false;

        $this->data_handler->setInactiveCalendar();
        return true;
    }

    public function isActiveCalendar() {
        return $this->data_handler->isActiveCalendar();
    }

    public function addHost($day, $data) {
        if (!$this->data_handler->addHost($day, $data)) {
            return false;
        }

        // send confirmation mail to host
        $subject = 'Dein SfC-Adventskalender-Türchen wurde eingetragen';
        // TODO: add link to page to change the data and see the participants
        $text = "Dein Adventskalender-Türchen am $day. Dezember";
        $text .= " wurde erfolgreich eingetragen!";
        $this->mail_handler->sendMail($data['email'], $subject, $text);

        return true;
    }

    public function hasHost($day) {
        return $this->data_handler->hasHost($day);
    }

    public function getHostInformation($day, $var) {
        return $this->data_handler->getHostInformation($day, $var);
    }

    public function addParticipant($day, $data) {
        if (!$this->data_handler->addParticipant($day, $data)) {
            return false;
        }
        
        // send confirmation mail to participant
        $subject = 'Erfolgreiche Anmeldung zum SfC-Adventskalender-Türchen';
        $text = "Du hast dich erfolgreich für das Adventskalender-Türchen";
        $text .= " von heute, den $day. Dezember angemeldet! Viel Spaß bei diesem Türchen!";
        $this->mail_handler->sendMail($data['email'], $subject, $text);

        // send mail to host
        $subject = 'Anmeldung zu deinem SfC-Adventskalender-Türchen';
        $text = "Zu deinem Adventskalender-Türchen von heute, den $day.";
        $text .= " Dezember hat sich jemand angemeldet:\n";
        // TODO: do generically
        $text .= "Name: ".$data['name']."\n";
        $text .= "E-Mail: ".$data['email'];
        $this->mail_handler->sendMail($this->getHostInformation($day, 'email'), $subject, $text);
        
        return true;
    }

    public function getParticipantsNumber($day) {
        return $this->data_handler->getParticipantsNumber($day);
    }

    public function getParticipantInformation($day, $index, $var) {
        return $this->data_handler->getParticipantInformation($day, $index, $var);
    }

    public function getDoorNumberInput() {
        return $this->input_handler->getDoorNumber();
    }

}

?>
<?php

class Controller {

    private static $controller;

    /** @var DataHandler */
    private $data_handler;
    /** @var PostHandler */
    private $post_handler;
    /** @var InputHandler */
    private $input_handler;
    /** @var MailHandler */
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
        $this->input_handler = new InputHandler($this->data_handler);
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
        // TODO: create img and img_tmp directories
        $this->data_handler->initializeDatabase();
    }

    public function deactivate() {
        // TODO: uncomment when upgrading per SFTP or other is possible
        // $this->deactivateCalendar();
        // $this->data_handler->deleteDatabase();
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
        $subject = "Dein SfC-Adventskalender-Türchen wurde eingetragen";
        // TODO: add link to page to change the data and see the participants
        $text = "Dein Adventskalender-Türchen am $day. Dezember";
        $text .= " wurde erfolgreich eingetragen!\n";
        $text .= $this->getHostInformationOutput($day);
        $this->mail_handler->sendMail($data['email'], $subject, $text);

        return true;
    }

    public function updateHost($day, $data) {
        $email = $this->getHostInformation($day, 'email');

        if (!$this->data_handler->updateHost($day, $data)) {
            return false;
        }

        if ($data['email'] && $email != $data['email']) {
            // send mail to old email
            $subject = "Dein SfC-Adventskalender-Türchen wurde bearbeitet";
            $text = "Dein Adventskalender-Türchen am $day. Dezember";
            $text .= " wurde bearbeitet: Deine Mail-Adresse wurde auf ";
            $text .= $data['email']." gesetzt.";
            $this->mail_handler->sendMail($email, $subject, $text);
            
            $email = $data['email'];
        }

        // send mail to host
        $subject = "Dein SfC-Adventskalender-Türchen wurde bearbeitet";
        $text = "Dein Adventskalender-Türchen am $day. Dezember";
        $text .= " wurde bearbeitet!\n";
        $text .= $this->getHostInformationOutput($day);
        $this->mail_handler->sendMail($email, $subject, $text);

        return true;
    }

    public function deleteHost($day) {
        if (!$this->hasHost($day)) {
            return false;
        }

        $email = $this->getHostInformation($day, 'email');

        if (!$this->data_handler->deleteHost($day)) {
            return false;
        }

        // send mail to host
        $subject = "Dein SfC-Adventskalender-Türchen wurde gelöscht";
        $text = "Dein Adventskalender-Türchen am $day. Dezember";
        $text .= " wurde gelöscht!";
        $this->mail_handler->sendMail($email, $subject, $text);

        return true;
    }

    public function hasHost($day) {
        return $this->data_handler->hasHost($day);
    }

    /**
     * Returns information about a host.
     * 
     * @param int $day the day of december this year
     * @param string $var the database variable
     * 
     * @return mixed the value of the variable of the respective host
     */
    public function getHostInformation($day, $var) {
        return $this->data_handler->getHostInformation($day, $var);
    }

    public function getHostVariables() {
        return $this->data_handler->getHostVariables();
    }

    public function getHostOutput($key) {
        return $this->data_handler->getHostOutput($key);
    }

    public function addParticipant($day, $data) {
        if (!$this->data_handler->addParticipant($day, $data)) {
            return false;
        }
        
        // send confirmation mail to participant
        $subject = 'Erfolgreiche Anmeldung zum SfC-Adventskalender-Türchen';
        $text = "Du hast dich erfolgreich für das Adventskalender-Türchen";
        $text .= " von heute, dem $day. Dezember, angemeldet! Viel Spaß bei diesem Türchen!";
        $this->mail_handler->sendMail($data['email'], $subject, $text);

        // send mail to host
        $name = $data['name'];
        $subject = 'Anmeldung zu deinem SfC-Adventskalender-Türchen';
        $text = "Zu deinem Adventskalender-Türchen von heute, dem $day.";
        $text .= " Dezember, hat sich jemand angemeldet:\n";
        $text .= $this->getParticipantInformationOutput($day,$name);
        $text .= "\n";
        $text .= $this->getParticipantsInformationOutput($day);
        $this->mail_handler->sendMail($this->getHostInformation($day, 'email'), $subject, $text);
        
        return true;
    }

    public function updateParticipant($day, $name, $data) {
        if (!$this->hasParticipant($day, $name)) {
            return false;
        }

        $email = $this->getParticipantInformation($day, $name, 'email');

        if (!$this->data_handler->updateParticipant($day, $name, $data)) {
            return false;
        }

        if ($data['email'] && $email != $data['email']) {
            // send mail to old participant email
            $subject = "Deine Anmeldung zum Adventskalender-Türchen wurde bearbeitet";
            $text = "Deine Anmeldung für das heutige Adventskalender-Türchen";
            $text .= " wurde bearbeitet: Deine Mail-Adresse wurde auf ";
            $text .= $data['email']." gesetzt.";
            $this->mail_handler->sendMail($email, $subject, $text);
            
            $email = $data['email'];
        }

        $old_name = $name;
        if ($data['name']) {
            $name = $data['name'];
        }

        // send mail to participant
        $subject = "Deine Anmeldung zum Adventskalender-Türchen wurde bearbeitet";
        $text = "Deine Anmeldung für das Adventskalender-Türchen von heute,";
        $text .= " dem $day. Dezember, wurde bearbeitet:\n";
        $text .= $this->getParticipantInformationOutput($day,$name);
        $this->mail_handler->sendMail($email, $subject, $text);

        // send mail to host
        $subject = "Bearbeitete Anmeldung zu deinem Adventskalender-Türchen";
        $text = "Die Anmeldung von $old_name zu deinem Adventskalender-Türchen von heute,";
        $text .= " dem $day. Dezember, wurde bearbeitet:\n";
        $text .= $this->getParticipantInformationOutput($day,$name);
        $text .= "\n";
        $text .= $this->getParticipantsInformationOutput($day);
        $this->mail_handler->sendMail($email, $subject, $text);

        return true;
    }

    public function deleteParticipant($day, $name) {
        if (!$this->hasParticipant($day, $name)) {
            return false;
        }

        $mail = $this->getParticipantInformation($day, $name, 'email');

        if (!$this->data_handler->deleteParticipant($day, $name)) {
            return false;
        }

        // send mail to participant
        $subject = 'Abmeldung vom SfC-Adventskalender-Türchen';
        $text = "Deine Anmeldung für das Adventskalender-Türchen";
        $text .= " von heute, dem $day. Dezember, wurde gelöscht!";
        $this->mail_handler->sendMail($mail, $subject, $text);

        // send mail to host
        $mail = $this->getHostInformation($day, 'email');
        $subject = 'Abmeldung von deinem SfC-Adventskalender-Türchen';
        $text = "Die Anmeldung von $name für das Adventskalender-Türchen";
        $text .= " von heute, dem $day. Dezember, wurde gelöscht!\n";
        $text .= $this->getParticipantsInformationOutput($day);
        $this->mail_handler->sendMail($mail, $subject, $text);

        return true;
    }

    public function hasParticipant($day, $name) {
        return $this->data_handler->hasParticipant($day, $name);
    }

    public function getParticipantsNumber($day) {
        return $this->data_handler->getParticipantsNumber($day);
    }

    /**
     * Returns information about a participant.
     * 
     * @param int $day the day of december this year
     * @param int|string $participant the index or the name of the participant
     * @param string $var the database variable
     * 
     * @return mixed the value of the variable of the respective participant
     */
    public function getParticipantInformation($day, $participant, $var) {
        return $this->data_handler->getParticipantInformation($day, $participant, $var);
    }

    public function getParticipantVariables() : array {
        return $this->data_handler->getParticipantVariables();
    }

    public function getParticipantOutput($key) {
        return $this->data_handler->getParticipantOutput($key);
    }

    public function getDoorNumberInput() {
        return $this->input_handler->getDoorNumber();
    }

    public function getReservationMandatoryInput() {
        return $this->data_handler->getReservationMandatoryInput();
    }

    public function getRegistrationMandatoryInput() {
        return $this->data_handler->getRegistrationMandatoryInput();
    }

    public function getReservationInput() : InputData {
        return $this->input_handler->getReservationInput();
    }

    public function getRegistrationInput() : InputData {
        return $this->input_handler->getRegistrationInput();
    }

    private function getHostInformationOutput($day) {
        $text = "Hier alle Daten im Überblick:\n\n";
        foreach ($this->getHostVariables() as $var) {
            $text .= $this->getHostOutput($var).": ";
            $val = $this->getHostInformation($day,$var);
            $text .= $this->getValueOutput($val);
            $text .= "\n";
        }
        return $text;
    }

    private function getParticipantInformationOutput($day, $participant) {
        $text = "";
        
        foreach($this->getParticipantVariables() as $var) {
            $text.= $this->getParticipantOutput($var).": ";
            $val = $this->getParticipantInformation($day,$participant,$var);
            $text .= $this->getValueOutput($val);
            $text .= "\n";
        }
        
        return $text;
    }

    private function getParticipantsInformationOutput($day) {
        $text = "Hier alle bisherigen Anmeldungen im Überblick:\n\n";
        for ($i = 0; $i < $this->getParticipantsNumber($day); $i++) {
            $text .= $this->getParticipantInformationOutput($day,$i);
            $text .= "\n";
        }
        return $text;
    }

    private function getValueOutput($val) {
        if (!isset($val)) {
            return "keine Angabe";
        }
        if (is_bool($val)) {
            return $val ? "ja" : "nein";
        }
        return $val;
    }

}

?>
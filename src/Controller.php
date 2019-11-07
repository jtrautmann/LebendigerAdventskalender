<?php

class Controller {

    private static $controller;

    private $data_handler;
    private $post_manager;
    private $input_handler;
    
    public static function getController() : Controller {
        if (!isset(self::$controller)) {
            self::$controller = new Controller();
        }
        return self::$controller;
    }

    private function __construct() {
        // instantiate attributes
        $this->data_handler = new DataHandler();
        $this->post_manager = new PostManager();
        $this->input_handler = new InputHandler();
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

        $result = $this->post_manager->createPost();
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
        if (!$this->post_manager->deletePost($post_id))
            return false;

        $this->data_handler->setInactiveCalendar();
        return true;
    }

    public function isActiveCalendar() {
        return $this->data_handler->isActiveCalendar();
    }

    public function addHost($day, $data) {
        return $this->data_handler->addHost($day, $data);
    }

    public function hasHost($day) {
        return $this->data_handler->hasHost($day);
    }

    public function getHostInformation($day, $var) {
        return $this->data_handler->getHostInformation($day, $var);
    }

    public function addParticipant($day, $data) {
        return $this->data_handler->addParticipant($day, $data);
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
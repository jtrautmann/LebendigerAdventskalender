<?php

class Controller {

    private $data_handler;
    private $post_manager;

    public function __construct() {
        // instantiate attributes
        $this->data_handler = new DataHandler();
        $this->post_manager = new PostManager();
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

}

?>
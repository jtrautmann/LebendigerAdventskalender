<?php

class Controller {

    private $data_handler;

    public function __construct() {
        // instantiate data handler
        $this->data_handler = new DataHandler();
    }

    public function activate() {
        $this->data_handler->initializeDatabase();
    }

    public function deactivate() {
        $this->data_handler->deleteDatabase();
    }

    public function activateCalendar() {
        // TODO
        $this->data_handler->setActiveCalendar();
    }

    public function deactivateCalendar() {
        // TODO
        $this->data_handler->setInactiveCalendar();
    }

    public function isActiveCalendar() {
        return $this->data_handler->isActiveCalendar();
    }

}

?>
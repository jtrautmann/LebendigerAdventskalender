<?php

class InputHandler {

    private $nr;

    public function __construct() {
        // set input variables
        $this->nr = filter_input(INPUT_GET,'nr',FILTER_SANITIZE_NUMBER_INT);
        if ($this->nr < 1 || $this->nr > 24) {
            $nr = NULL;
        }
    }

    public function doorNumberSet() {
        return isset($this->nr);
    }

    public function getDoorNumber() {
        return $this->nr;
    }

}
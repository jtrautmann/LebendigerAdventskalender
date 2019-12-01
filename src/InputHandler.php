<?php

class InputHandler {

    private $nr;

    function __construct() {
        // set input variables
        $this->nr = filter_input(INPUT_GET,'nr',FILTER_SANITIZE_NUMBER_INT);
        if ($this->nr < 1 || $this->nr > 24) {
            $nr = NULL;
        }
    }

    function doorNumberSet() {
        return isset($this->nr);
    }

    function getDoorNumber() {
        return $this->nr;
    }

}
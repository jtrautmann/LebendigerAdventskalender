<?php

class InputData {

    private $input_received;
    private $data;
    private $error;

    public function __construct($data, $error = NULL, $input_received=true) {
        $this->input_received = $input_received;
        $this->data = $data;
        $this->error = $error;
    }

    public function inputReceived() {
        return $this->input_received;
    }

    public function getData() {
        return $this->data;
    }

    public function hasError() {
        return isset($this->error) && $this->error->has_errors();
    }

    public function getError() {
        return $this->error;
    }

}
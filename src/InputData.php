<?php

class InputData {

    /** @var bool */
    private $input_received;
    /** @var array */
    private $data;
    /** @var WP_Error */
    private $error;

    public function __construct(array $data, WP_Error $error = NULL, bool $input_received = true) {
        $this->input_received = $input_received;
        $this->data = $data;
        $this->error = $error;
    }

    public function inputReceived() : bool {
        return $this->input_received;
    }

    public function getData() : array {
        return $this->data;
    }

    public function hasError() : bool {
        return isset($this->error) && $this->error->has_errors();
    }

    public function getError() : WP_Error {
        return $this->error;
    }

}
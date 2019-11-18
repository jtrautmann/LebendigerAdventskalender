<?php

class InputHandler {

    private $nr;

    // TODO: get from DataHandler
    const RESERVATION_MANDATORY = [
        'name',
        'title',
        'address',
        'time',
        'email'
    ];
    const RESERVATION_FILTER = [
        'la_title' => FILTER_SANITIZE_STRING,
        'la_description' => FILTER_SANITIZE_STRING,
        'la_address' => FILTER_SANITIZE_STRING,
        'la_time' => FILTER_SANITIZE_STRING,
        'la_max_participants' => FILTER_SANITIZE_NUMBER_INT,
        'la_name' => FILTER_SANITIZE_STRING,
        'la_email' => FILTER_SANITIZE_EMAIL,
        'la_phonenumber' => FILTER_SANITIZE_STRING,
        'la_image' => FILTER_SANITIZE_STRING
    ];

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

    public function getReservationInput() : InputData {
        $error = new WP_Error();

        if ($_SERVER['REQUEST_METHOD']!="POST") {
            $data = [
                'title' => '',
                'description' => '',
                'address' => '',
                'time' => '',
                'max_participants' => '',
                'registration' => true,
                'name' => '',
                'email' => '',
                'phonenumber' => ''
            ];

            return new InputData($data, $error, false);
        }

        $inputs = filter_input_array(INPUT_POST,self::RESERVATION_FILTER);
    
        $inputs['la_registration'] = isset($_POST['la_registration']) ? true : false;
        
        if ($inputs['la_max_participants']) {
            if(strpos($inputs['la_max_participants'],'-')) {
                $expl = explode('-',$inputs['la_max_participants']);
                $inputs['la_max_participants'] = end($expl);
            }
            $inputs['la_max_participants'] = intval($inputs['la_max_participants']);
        }
        
        $valid_email = filter_input(INPUT_POST, 'la_email', FILTER_VALIDATE_EMAIL);
        if (!$valid_email) {
            $error->add(InputErrorType::INVALID_EMAIL, 'Invalid email');
        }

        $data = [];
		foreach ($inputs as $key => $value) {
			// delete the "la_" prefix of the key
			$new_key = substr($key, 3);
			$data[$new_key] = $value;
        }
        
        $missing = [];
        foreach (self::RESERVATION_MANDATORY as $value) {
            if (!$data[$value]) {
                $missing[] = $value;
            }
        }
        if (sizeof($missing) > 0) {
            $error->add(InputErrorType::MANDATORY_MISSING, 'Mandatory input missing', $missing);
        }

        return new InputData($data, $error);
    }

}
<?php

class InputHandler {

    // use DataHandler
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
    
    // use DataHandler
    const REGISTRATION_FILTER = [
        'la_name' => FILTER_SANITIZE_STRING,
		'la_email' => FILTER_SANITIZE_EMAIL
    ];
    
    /** @var int */
    private $nr;
    /** @var DataHandler */
    private $data_handler;

    public function __construct(DataHandler $data_handler) {
        // set input variables
        $this->nr = filter_input(INPUT_GET,'nr',FILTER_SANITIZE_NUMBER_INT);
        if ($this->nr < 1 || $this->nr > 24) {
            $nr = NULL;
        }
        $this->data_handler = $data_handler;
    }

    public function doorNumberSet() : bool {
        return isset($this->nr);
    }

    /**
     * Returns the input door number or null if it is not set.
     * 
     * @return int|null the input door number or null if it is not set
     */
    public function getDoorNumber() {
        return $this->nr;
    }

    public function getReservationInput() : InputData {
        $error = new WP_Error();

        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            // TODO: generate with DataHandler
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
        
        $this->checkMail('la_email', $error);
        $data = $this->getCleanData($inputs);
        $this->checkMissing($this->data_handler->getHostMandatoryInput(), $data, $error);

        return new InputData($data, $error);
    }

    public function getRegistrationInput() : InputData {
        $error = new WP_Error();

        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            // TODO: generate with DataHandler
            $data = [
                'name' => '',
                'email' => ''
            ];

            return new InputData($data, $error, false);
        }

        $inputs = filter_input_array(INPUT_POST,self::REGISTRATION_FILTER);
        
        $this->checkMail('la_email', $error);
        $data = $this->getCleanData($inputs);
        $this->checkMissing($this->data_handler->getParticipantMandatoryInput(), $data, $error);

        return new InputData($data, $error);
    }

    private function checkMail($email_input, &$error) {
        $valid_email = filter_input(INPUT_POST,$email_input,FILTER_VALIDATE_EMAIL);
        if (!$valid_email) {
            $error->add(InputErrorType::INVALID_EMAIL, 'Invalid email');
        }
    }

    private function getCleanData($inputs) {
        $data = [];
		foreach ($inputs as $key => $value) {
			// delete the "la_" prefix of the key
			$new_key = substr($key, 3);
			$data[$new_key] = $value;
        }
        return $data;
    }

    private function checkMissing($mandatory, $data, &$error) {
        $missing = [];
        foreach ($mandatory as $value) {
            if (!$data[$value]) {
                $missing[] = $value;
            }
        }
        if (sizeof($missing) > 0) {
            $error->add(InputErrorType::MANDATORY_MISSING, 'Mandatory input missing', $missing);
        }
    }

}
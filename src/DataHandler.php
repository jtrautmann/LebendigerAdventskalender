<?php

class DataHandler {

    // value types
    const VALUE_TYPE_TINY_TEXT = 1;
    const VALUE_TYPE_TEXT      = 2;
    const VALUE_TYPE_EMAIL     = 3;
    const VALUE_TYPE_BOOLEAN   = 4;
    const VALUE_TYPE_INT       = 6;
    const VALUE_TYPE_TINY_INT  = 7;

    const PROJECT_NAME = "lebendigeradventskalender";

    // option names
    const DB_VERSION_OPTION       = self::PROJECT_NAME . "_db_version";
    const CALENDAR_ACTIVE_OPTION  = self::PROJECT_NAME . "_calendar_active";
    const HOST_VARS_OPTION        = self::PROJECT_NAME . "_host_vars";
    const PARTICIPANT_VARS_OPTION = self::PROJECT_NAME . "_participant_vars";
    const POST_ID_OPTION          = self::PROJECT_NAME . "_post_id";

    // TODO: unite all information about variables here
    const DEFAULT_HOST_VARS = [
        'name'             => self::VALUE_TYPE_TINY_TEXT,
        'title'            => self::VALUE_TYPE_TINY_TEXT,
        'description'      => self::VALUE_TYPE_TEXT,
        'address'          => self::VALUE_TYPE_TINY_TEXT,
        'time'             => self::VALUE_TYPE_TINY_TEXT,
        'registration'     => self::VALUE_TYPE_BOOLEAN,
        'max_participants' => self::VALUE_TYPE_TINY_INT,
        'email'            => self::VALUE_TYPE_EMAIL,
        'phonenumber'      => self::VALUE_TYPE_TINY_TEXT,
        'image'            => self::VALUE_TYPE_TINY_TEXT
    ];

    // TODO: unite all information about variables here
    const DEFAULT_PARTICIPANT_VARS = [
        'name'  => self::VALUE_TYPE_TINY_TEXT,
        'email' => self::VALUE_TYPE_EMAIL
    ];

    const HOST_MANDATORY = [
        'name',
        'title',
        'address',
        'time',
        'email'
    ];

    const PARTICIPANT_MANDATORY = [
        'name',
        'email'
    ];

    const HOST_OUTPUT = [
        'name' => 'Gastgeber',
        'title' => 'Aktion',
        'description' => 'Beschreibung',
        'address' => 'Adresse',
        'time' => 'Uhrzeit',
        'registration' => 'Anmeldung erforderlich oder zumindest erwünscht',
        'max_participants' => 'max. Teilnehmer',
        'email' => 'E-Mail',
        'phonenumber' => 'Telefonnummer',
        'image' => 'Flyer / Bild zur Einstimmung'
    ];

    const PARTICIPANT_OUTPUT = [
        'name' => 'Name',
        'email' => 'E-Mail'
    ];

    private $year;

    private $hosts_table_name;
    private $participants_table_name;

    public function __construct() {
        global $wpdb;

        $this->year = date("Y");

        $db_prefix = $wpdb->prefix . self::PROJECT_NAME . "_";
        
        // set names of the tables
        $this->hosts_table_name = $db_prefix . "hosts";
        $this->participants_table_name = $db_prefix . "participants";

        $this->updateDatabase();
    }

    public function initializeDatabase() {
        // check whether database was already initialized
        // TODO: remove when upgrading per SFTP or other is possible
        $db_version = get_option(self::DB_VERSION_OPTION);
        if ($db_version) {
            return;
        }

        global $wpdb;

        // ---- create tables ----
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');    // needed for dbDelta function
        
        // create hosts table
        // TODO: use DEFAULT_HOST_VARS
        $sql = "CREATE TABLE $this->hosts_table_name (
                    day tinyint(1) unsigned NOT NULL,
                    year smallint(4) unsigned NOT NULL, 
                    name tinytext NOT NULL,
                    title tinytext NOT NULL,
                    description text,
                    address tinytext NOT NULL,
                    time tinytext NOT NULL,
                    registration bool DEFAULT '1' NOT NULL,
                    max_participants tinyint(1) unsigned,
                    email tinytext NOT NULL,
                    phonenumber tinytext,
                    image tinytext,
                    PRIMARY KEY  (day,year)
                ) $charset_collate;";
          
        dbDelta($sql);

        // create participants table
        // TODO: use DEFAULT_PARTICIPANT_VARS
        $sql = "CREATE TABLE $this->participants_table_name (
                    day tinyint(1) unsigned NOT NULL,
                    year smallint(4) unsigned NOT NULL,
                    name tinytext NOT NULL,
                    email tinytext NOT NULL,
                    PRIMARY KEY  (day,year,name)
                ) $charset_collate;";
        
        dbDelta($sql);

        // ---- add options ----
        // add version number for updates
        add_option(self::DB_VERSION_OPTION, "1.2");

        // add information whether the calendar is active
        add_option(self::CALENDAR_ACTIVE_OPTION, false);

        // add information about the variables
        add_option(self::HOST_VARS_OPTION, self::DEFAULT_HOST_VARS);
        add_option(self::PARTICIPANT_VARS_OPTION, self::DEFAULT_PARTICIPANT_VARS);
    }

    private function updateDatabase() {
        $db_version = get_option(self::DB_VERSION_OPTION);
        if ($db_version) {
            // update from 1.0 to 1.1
            if ($db_version == "1.0") {
                add_option(self::HOST_VARS_OPTION, self::DEFAULT_HOST_VARS);
                $db_version = "1.1";
                update_option(self::DB_VERSION_OPTION, $db_version);
            }
            // update from 1.1 to 1.2
            if ($db_version == "1.1") {
                add_option(self::PARTICIPANT_VARS_OPTION, self::DEFAULT_PARTICIPANT_VARS);
                $db_version = "1.2";
                update_option(self::DB_VERSION_OPTION, $db_version);
            }
        }
    }

    public function deleteDatabase() {
        global $wpdb;

        // delete tables
        $sql = "DROP TABLE IF EXISTS $this->hosts_table_name;";
        $wpdb->query($sql);

        $sql = "DROP TABLE IF EXISTS $this->participants_table_name;";
        $wpdb->query($sql);

        // delete options
        delete_option(self::DB_VERSION_OPTION);
        delete_option(self::CALENDAR_ACTIVE_OPTION);
    }

    public function setActiveCalendar($post_id) {
        add_option(self::POST_ID_OPTION, $post_id);
        update_option(self::CALENDAR_ACTIVE_OPTION, true);
    }

    public function setInactiveCalendar() {
        delete_option(self::POST_ID_OPTION);
        update_option(self::CALENDAR_ACTIVE_OPTION, false);
    }

    public function getPostID() {
        return get_option(self::POST_ID_OPTION);
    }

    public function isActiveCalendar() {
        return get_option(self::CALENDAR_ACTIVE_OPTION);
    }

    public function addHost($day, $data) {
        global $wpdb;
        $data['day'] = $day;
        $data['year'] = $this->year;
        $result = $wpdb->insert($this->hosts_table_name, $data);
        return $result !== false;
    }

    public function updateHost($day, $data) {
        global $wpdb;
        $where = [
            'day' => $day,
            'year' => $this->year
        ];
        $result = $wpdb->update($this->hosts_table_name, $data, $where);
        return $result !== false;
    }

    public function deleteHost($day) {
        global $wpdb;
        $where = [
            'day' => $day,
            'year' => $this->year
        ];
        return $wpdb->delete($this->hosts_table_name, $where);
    }

    public function hasHost($day) {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM $this->hosts_table_name WHERE day = %d AND year = $this->year;";
        return $wpdb->get_var($wpdb->prepare($sql, $day));
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
        global $wpdb;
        $sql = "SELECT $var FROM $this->hosts_table_name WHERE day = %d AND year = $this->year;";
        $val = $wpdb->get_var($wpdb->prepare($sql,$day));
        // check for bool
        $type = $this->getHostVariableType($var);
        if ($type == self::VALUE_TYPE_BOOLEAN) {
            $val = (bool) $val;
        }
        return $val;
    }

    public function getHostMandatoryInput() {
        return self::HOST_MANDATORY;
    }

    public function getHostVariables() {
        $vars = [];
        foreach (get_option(self::HOST_VARS_OPTION) as $key => $value) {
            $vars[] = $key;
        }
        return $vars;
    }

    public function getHostVariableType($var) {
        return get_option(self::HOST_VARS_OPTION)[$var];
    }

    public function getHostOutput(string $key) {
        return self::HOST_OUTPUT[$key];
    }

    public function addParticipant($day, $data) {
        global $wpdb;
        $data['day'] = $day;
        $data['year'] = $this->year;
        $result = $wpdb->insert($this->participants_table_name, $data);
        return $result !== false;
    }

    public function updateParticipant($day, $name, $data) {
        global $wpdb;
        $where = [
            'day' => $day,
            'year' => $this->year,
            'name' => $name
        ];
        $result = $wpdb->update($this->participants_table_name, $data, $where);
        return $result !== false;
    }

    public function deleteParticipant($day, $name) {
        global $wpdb;
        $where = [
            'day' => $day,
            'year' => $this->year,
            'name' => $name
        ];
        return $wpdb->delete($this->participants_table_name, $where);
    }

    public function hasParticipant($day, $name) {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM $this->participants_table_name WHERE day = %d AND year = $this->year";
        $sql.=  " AND name = %s;";
        return $wpdb->get_var($wpdb->prepare($sql,$day,$name));
    }

    public function getParticipantsNumber($day) {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM $this->participants_table_name WHERE day = %d AND year = $this->year;";
        $result = $wpdb->get_var($wpdb->prepare($sql,$day));
        return $result;
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
        global $wpdb;

        $sql = "SELECT $var FROM $this->participants_table_name WHERE day = %d AND year = $this->year";
        
        if (is_int($participant)) {
            // index
            return $wpdb->get_var($wpdb->prepare($sql.";",$day), 0, $participant);
        }

        // name
        $sql .= " AND name = %s;";
        return $wpdb->get_var($wpdb->prepare($sql,$day,$participant));
    }

    public function getParticipantMandatoryInput() {
        return self::PARTICIPANT_MANDATORY;
    }

    public function getParticipantVariables() : array {
        $vars = [];
        foreach (get_option(self::PARTICIPANT_VARS_OPTION) as $key => $value) {
            $vars[] = $key;
        }
        return $vars;
    }

    public function getParticipantVariableType($var) {
        return get_option(self::PARTICIPANT_VARS_OPTION)[$var];
    }

    public function getParticipantOutput(string $key) {
        return self::PARTICIPANT_OUTPUT[$key];
    }

}
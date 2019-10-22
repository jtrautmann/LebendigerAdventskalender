<?php

class DataHandler {

    private $PROJECT_NAME = "lebendigeradventskalender";

    private $year;

    private $db_version_option;
    private $calendar_active_option;
    private $post_id_option;

    private $hosts_table_name;
    private $participants_table_name;

    public function __construct() {
        global $wpdb;

        $this->year = date("Y");

        $db_prefix = $wpdb->prefix . $this->PROJECT_NAME . "_";
        
        // set names of the tables
        $this->hosts_table_name = $db_prefix . "hosts";
        $this->participants_table_name = $db_prefix . "participants";

        // set option names
        $this->db_version_option = $this->PROJECT_NAME . "_db_version";
        $this->calendar_active_option = $this->PROJECT_NAME . "_calendar_active";
        $this->post_id_option = $this->PROJECT_NAME . "_post_id";
    }

    public function initializeDatabase() {
        global $wpdb;

        // ---- create tables ----
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');    // needed for dbDelta function
        
        // create hosts table
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
        $sql = "CREATE TABLE $this->participants_table_name (
                    day tinyint(1) unsigned NOT NULL,
                    year smallint(4) unsigned NOT NULL,
                    name varchar(40) NOT NULL,
                    email tinytext NOT NULL,
                    PRIMARY KEY  (day,year,name)
                ) $charset_collate;";
        
        dbDelta($sql);

        // ---- add options ----
        // add version number for updates
        add_option($this->db_version_option, "1.0");

        // add information whether the calendar is active
        add_option($this->calendar_active_option, false);
    }

    public function deleteDatabase() {
        global $wpdb;

        // delete tables
        $sql = "DROP TABLE IF EXISTS $this->hosts_table_name;";
        $wpdb->query($sql);

        $sql = "DROP TABLE IF EXISTS $this->participants_table_name;";
        $wpdb->query($sql);

        // delete options
        delete_option($this->db_version_option);
        delete_option($this->calendar_active_option);
    }

    public function setActiveCalendar($post_id) {
        add_option($this->post_id_option, $post_id);
        update_option($this->calendar_active_option, true);
    }

    public function setInactiveCalendar() {
        delete_option($this->post_id_option);
        update_option($this->calendar_active_option, false);
    }

    public function getPostID() {
        return get_option($this->post_id_option);
    }

    public function isActiveCalendar() {
        return get_option($this->calendar_active_option);
    }

    public function addHost($day, $data) {
        global $wpdb;
        $data['day'] = $day;
        $data['year'] = $this->year;
        $result = $wpdb->insert($this->hosts_table_name, $data);
        return $result !== false;
    }

    public function hasHost($day) {
        global $wpdb;
        $sql = "SELECT COUNT(1) FROM $this->hosts_table_name WHERE day = $day AND year = $this->year;";
        return $wpdb->get_var($sql);
    }

    public function getHostInformation($day, $var) {
        global $wpdb;
        $sql = "SELECT $var FROM $this->hosts_table_name WHERE day = $day AND year = $this->year;";
        return $wpdb->get_var($sql);
    }

    public function addParticipant($day, $data) {
        global $wpdb;
        $data['day'] = $day;
        $data['year'] = $this->year;
        $wpdb->insert($this->participants_table_name, $data);
        return $wpdb->insert_id !== false;
    }

    public function getParticipantsNumber($day) {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM $this->participants_table_name WHERE day = $day AND year = $this->year;";
        return $wpdb->get_var($sql);
    }

    public function getParticipantInformation($day, $index, $var) {
        global $wpdb;
        $sql = "SELECT $var FROM $this->participants_table_name WHERE day = $day AND year = $this->year;";
        return $wpdb->get_var($sql, 0, $index);
    }

}

?>
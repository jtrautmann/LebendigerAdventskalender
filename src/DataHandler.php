<?php

class DataHandler {

    private $project_name = "lebendigeradventskalender";
    private $hosts_table_name;
    private $participants_table_name;

    public function __construct() {
        global $wpdb;

        $db_prefix = $wpdb->prefix . $this->project_name . "_";
        
        // set names of the tables
        $this->hosts_table_name = $db_prefix . "hosts";
        $this->participants_table_name = $db_prefix . "participants";
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
                    description text NOT NULL,
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

        // add version number for updates
        add_option($this->project_name . "_db_version", "1.0" );
    }

    public function deleteDatabase() {
        global $wpdb;

        // delete tables
        $sql = "DROP TABLE IF EXISTS $this->hosts_table_name;";
        $wpdb->query($sql);

        $sql = "DROP TABLE IF EXISTS $this->participants_table_name;";
        $wpdb->query($sql);

        // delete version number
        delete_option($this->project_name . "_db_version");
    }

}

?>
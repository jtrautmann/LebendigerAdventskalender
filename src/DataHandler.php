<?php

class DataHandler {

    public function initializeDatabase() {
        global $wpdb;

        // set names of the new tables
        $hosts_table_name = $wpdb->prefix . "lebendigeradventskalender_hosts";
        $participants_table_name = $wpdb->prefix . "lebendigeradventskalender_participants";

        // ---- create tables ----
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');    // needed for dbDelta function
        
        // create hosts tables
        $sql = "CREATE TABLE $hosts_table_name (
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

        // create participants tables
        $sql = "CREATE TABLE $participants_table_name (
                    day tinyint(1) unsigned NOT NULL,
                    year smallint(4) unsigned NOT NULL,
                    name varchar(40) NOT NULL,
                    email tinytext NOT NULL,
                    PRIMARY KEY  (day,year,name)
                ) $charset_collate;";
        
        dbDelta($sql);
    }

    public function deleteDatabase() {
        // TODO
    }

}

?>
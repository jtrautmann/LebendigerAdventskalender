<?php

/*
 Plugin Name: Lebendiger Adventskalender
 Plugin URI: https://github.com/sebaur/LebendigerAdventskalender
 Description: This plugin provides an advent calendar for SfC Karlsruhe.
 Author: Jeremias Trautmann
 Version: 1.0
 Author URI: https://github.com/jtrautmann
*/

$lebendiger_adventskalender = new LebendigerAdventskalender();

class LebendigerAdventskalender {

    private $controller;

    public function __construct() {
        // add administrator tool to administrator menu
        add_action('admin_menu', array($this, 'addToMenu'));

        // add autoload function
        spl_autoload_register(array($this, 'autoload'));

        // add functions collection
        include(plugin_dir_url(__FILE__)."/src/functions.php");

        // register plugin activation and deactivation hook 
        register_activation_hook( __FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // register plugin styles
        wp_register_style('lebendiger_adventskalender_calendar', plugin_dir_url(__FILE__).'/assets/calendar.css');
        wp_register_style('lebendiger_adventskalender_door', plugin_dir_url(__FILE__).'/assets/door.css');
        wp_register_style('lebendiger_adventskalender_reservation', plugin_dir_url(__FILE__).'/assets/reservation.css');

        // register plugin scipts
        wp_register_script('lebendiger_adventskalender_door', plugin_dir_url(__FILE__).'/assets/door.js');

        // instantiate controller
        $this->controller = new Controller();
    }

    public function activate() {
        $this->controller->activate();
    }

    public function deactivate() {
        $this->controller->deactivate();
    }

    public function addToMenu() {
        add_menu_page('Lebendiger Adventskalender', 'Lebendiger Adventskalender', 'edit_posts', 'lebendiger_adventskalender', array($this, 'printAdminPage'));
    }

    public function printAdminPage() {
        include(plugin_dir_url(__FILE__).'/admin.php');
    }

    public function autoload($class) {
        $path = plugin_dir_url(__FILE__).'/src/'.$class.'.php';
        if (file_exists($path)) {
            require_once($path);
        }
        else {
            return false;
        }
    }
}

?>
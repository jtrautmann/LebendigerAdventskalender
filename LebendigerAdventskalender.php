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

    public function __construct() {
        // add administrator tool to administrator menu
        add_action('admin_menu', array($this, 'addToMenu'));

        spl_autoload_register(array($this, 'autoload'));

        // register plugin activation and deactivation hook 
        register_activation_hook( __FILE__, array($this, 'activate' ));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function activate()
    {
        // TODO
    }

    public function deactivate()
    {
        // TODO
    }

    public function addToMenu()
    {
        add_menu_page('Lebendiger Adventskalender', 'Lebendiger Adventskalender', 'edit_posts', 'lebendiger_adventskalender', array($this, 'printAdminPage'));
    }

    public function printAdminPage()
    {
        include(dirname(__FILE__).'/admin.php');
    }

    public function autoload($class)
    {
        $dir = "src";

        if (file_exists(dirname(__FILE__)."/".$dir."/".$class.".php"))
        {
            require_once(dirname(__FILE__)."/".$dir."/".$class.".php");
        }
        else
        {
            return false;
        }
    }
}

?>
<?php

// ---- constants ----
$WEEK_DAYS = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');

// ---- initialize variables ----
$controller = new Controller();
$nr = filter_input(INPUT_GET,'nr',FILTER_SANITIZE_NUMBER_INT);
if($nr<1 || $nr>24) {
	$nr = NULL;
}

// ---- choose output ----
if (!$nr) {
	// show calendar
	include(plugin_dir_path(__FILE__).'calendar.php');
	return;
}

// TODO: door in the past

if (!$controller->hasHost($nr)) {
	// show reservation
	include(plugin_dir_path(__FILE__).'reservation.php');
	return;
}

// show door
include(plugin_dir_path(__FILE__).'door.php');
<?php
// ---- constants ----
const WEEK_DAYS = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');

// ---- initialize variables ----
$controller = Controller::getController();
$nr = $controller->getDoorNumberInput();

// ---- choose output ----
switch ($controller->getShowState()) {
	case ShowState::CALENDAR:
		// show calendar
		include(plugin_dir_path(__FILE__).'calendar.php');
		break;
	case ShowState::PAST_DOOR:
		// TODO: door in the past
		break;
	case ShowState::RESERVATION:
		// show reservation
		include(plugin_dir_path(__FILE__).'reservation.php');
		break;
	case ShowState::DOOR:
		// show door
		include(plugin_dir_path(__FILE__).'door.php');
		break;
}
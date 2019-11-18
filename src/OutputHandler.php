<?php

class OutputHandler {

    // TODO: write into file
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

    public function __construct() {
        // TODO read file
    }

    public function getHostOutput($key) {
        return self::HOST_OUTPUT[$key];
    }

}
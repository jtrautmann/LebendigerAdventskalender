<?php
// ---- constants ----
// commands
const ACTIVATION_COMMAND_STRING = "activation_command";
const ACTIVATE = 1;
const DEACTIVATE = 2;

const DELETION_COMMAND_STRING = "deletion_command";
const DELETION_CONFIRMATION_COMMAND_STRING = "deletion_confirmation";
const EDITING_COMMAND_STRING = "editing_command";
const EDITING_CONFIRMATION_COMMAND_STRING = "editing_confirmation";

// ---- initialize variables ----
$controller = Controller::getController();

// ---- output ----
wp_enqueue_style('lebendiger_adventskalender_admin');
?>

<h1>Lebendiger Adventskalender Administration</h1>

<?php
// check for POST variables
$activation_command = filter_input(INPUT_POST,ACTIVATION_COMMAND_STRING,FILTER_SANITIZE_NUMBER_INT);
if ($activation_command) {
	switch($activation_command) {
        case ACTIVATE:
            if (!$controller->isActiveCalendar()) {
                $result = $controller->activateCalendar();
                if (is_wp_error($result)) {
                    echo '<div class="notice notice-error is-dismissible"><p>Der Lebendige Adventskalender konnte nicht aktiviert werden. Fehler: '.$result->get_error_message().'</p></div>';
                }
                else {
                    echo '<div class="notice notice-success is-dismissible"><p>Der Lebendige Adventskalender wurde aktiviert.</p></div>';
                }
            }
            else {
                echo '<div class="notice notice-error is-dismissible"><p>Der Lebendige Adventskalender ist bereits aktiv.</p></div>';
            }
            break;
        case DEACTIVATE:
            if ($controller->isActiveCalendar()) {
                if ($controller->deactivateCalendar()) {
                    echo '<div class="notice notice-success is-dismissible"><p>Der Lebendige Adventskalender wurde deaktiviert.</p></div>';
                }
                else {
                    echo '<div class="notice notice-error is-dismissible"><p>Der Lebendige Adventskalender konnte nicht deaktiviert werden.</p></div>';
                }
            }
            else {
                echo '<div class="notice notice-error is-dismissible"><p>Der Lebendige Adventskalender ist bereits inaktiv.</p></div>';
            }
            break;
    }
}

$deletion_command = filter_input(INPUT_POST,DELETION_COMMAND_STRING, FILTER_SANITIZE_NUMBER_INT);
if ($deletion_command) {
    echo '<div class="notice notice-warning">';
    echo '  <p>';
    echo '      Möchtest du wirklich die Reservierung für den '.$deletion_command.'. Dezember löschen?';
    echo '      <form action="'.get_current_url().'" method="post">';
    echo '          <button class="button" type="submit" name="'.DELETION_CONFIRMATION_COMMAND_STRING.'" value="'.$deletion_command.'">';
    echo '              Löschen';
    echo '          </button>';
    echo '          <button class="button" type="submit" name="'.DELETION_CONFIRMATION_COMMAND_STRING.'" value="0">';
    echo '              Abbrechen';
    echo '          </button>';
    echo '      </form>';
    echo '  </p>';
    echo '</div>';
}

$deletion_confirmation_command = filter_input(INPUT_POST,DELETION_CONFIRMATION_COMMAND_STRING, FILTER_SANITIZE_NUMBER_INT);
if ($deletion_confirmation_command) {
    // delete reservation
    if ($controller->deleteHost($deletion_confirmation_command)) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '  <p>Die Reservierung für den '.$deletion_confirmation_command.'. Dezember wurde erfolgreich gelöscht.</p>';
        echo '</div>';
    }
    else {
        echo '<div class="notice notice-error is-dismissible">';
        echo '  <p>Die Reservierung für den '.$deletion_confirmation_command.'. Dezember konnte nicht gelöscht werden.</p>';
        echo '</div>';
    }
}

$editing_command = filter_input(INPUT_POST,EDITING_COMMAND_STRING, FILTER_SANITIZE_NUMBER_INT);

$editing_confirmation_command = filter_input(INPUT_POST,EDITING_CONFIRMATION_COMMAND_STRING, FILTER_SANITIZE_NUMBER_INT);
$error_invalid_email = false;
$error_mandatory_fields = [];
$input_data = [];
if ($editing_confirmation_command) {
    $input = $controller->getReservationInput();
    if ($input->hasError()) {
        $input_data = $input->getData();
        $editing_command = $editing_confirmation_command;
        $error = $input->getError();
        $errors = $error->get_error_codes();
        echo '<div class="notice notice-error is-dismissible">';
        echo '  <p>Die Reservierung für den '.$editing_confirmation_command.'. Dezember konnte nicht bearbeitet werden.</p>';
        if (in_array(InputErrorType::INVALID_EMAIL,$errors)) {
            $error_invalid_email = true;
            echo '  <p>Ungültige E-Mail-Adresse.</p>';
        }
        if (in_array(InputErrorType::MANDATORY_MISSING,$errors)) {
            foreach ($error->get_error_data(InputErrorType::MANDATORY_MISSING) as $value) {
                $error_mandatory_fields[] = $value;
                echo '  <p>Fehlende Pflichtfelder.</p>';
            }
        }
        echo '</div>';
    }
    else {
        // TODO: edit reservation
        $data = $input->getData();
        if ($controller->updateHost($editing_confirmation_command, $data)) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '  <p>Die Reservierung für den '.$deletion_confirmation_command.'. Dezember wurde erfolgreich bearbeitet.</p>';
            echo '</div>';
		}
		else {
            echo '<div class="notice notice-error is-dismissible">';
            echo '  <p>Die Reservierung für den '.$deletion_confirmation_command.'. Dezember konnte nicht bearbeitet werden.</p>';
            echo '</div>';
		}
    }
}
?>

<form action="<?php echo get_current_url() ?>" method="post">
    <h2><label>Status: <?php echo ($controller->isActiveCalendar() ? "aktiv" : "inaktiv") ?></label></h2>
    <div>
        <button class="button button-primary" type="submit" name="<?php echo ACTIVATION_COMMAND_STRING?>" value="<?php echo $controller->isActiveCalendar() ? DEACTIVATE : ACTIVATE ?>">
            <?php echo $controller->isActiveCalendar() ? 'Stoppen' : 'Starten' ?>
        </button>
    </div>
</form>

<h2>Reservierungen</h2>
<?php // TODO: create with Controller ?>
<div class="la-admin-table">
    <table>
        <tr>
            <th>Tag</th>
            <th>Name</th>
            <th>Aktion</th>
            <th>Beschreibung</th>
            <th>Adresse</th>
            <th>Uhrzeit</th>
            <th>Anmeldung</th>
            <th>max. Teilnehmer</th>
            <th>E-Mail</th>
            <th>Telefonnummer</th>
            <th></th>
            <th></th>
        </tr>
        <?php
        for ($i = 1; $i < 25; $i++) {
            if ($controller->hasHost($i)) {
                $name             = $controller->getHostInformation($i,'name');
                $title            = $controller->getHostInformation($i,'title');
                $description      = $controller->getHostInformation($i,'description');
                $address          = $controller->getHostInformation($i,'address');
                $time             = $controller->getHostInformation($i,'time');
                $registration     = $controller->getHostInformation($i,'registration');
                $max_participants = $controller->getHostInformation($i,'max_participants');
                $email            = $controller->getHostInformation($i,'email');
                $phonenumber      = $controller->getHostInformation($i,'phonenumber');

                echo'  <tr>';
                echo '      <td>'.$i.'</td>';
                if ($editing_command == $i) {
                    $name             = isset($input_data['name']) ? $input_data['name'] : $name;
                    $title            = isset($input_data['title']) ? $input_data['title'] : $title;
                    $description      = isset($input_data['description']) ? $input_data['description'] : $description;
                    $address          = isset($input_data['address']) ? $input_data['address'] : $address;
                    $time             = isset($input_data['time']) ? $input_data['time'] : $time;
                    $registration     = isset($input_data['registration']) ? $input_data['registration'] : $registration;
                    $max_participants = isset($input_data['max_participants']) ? $input_data['max_participants'] : $max_participants;
                    $email            = isset($input_data['email']) ? $input_data['email'] : $email;
                    $phonenumber      = isset($input_data['phonenumber']) ? $input_data['phonenumber'] : $phonenumber;
                    
                    echo '      <form action="'.get_current_url().'" method="post">';
                    echo '      <td '.(in_array('name',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_name" type="text" value="'.$name.'"/>';
                    echo '      </td>';
                    echo '      <td '.(in_array('title',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_title" type="text" value="'.$title.'"/>';
                    echo '      </td>';
                    echo '      <td '.(in_array('description',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_description" type="text" value="'.$description.'"/>';
                    echo '      </td>';
                    echo '      <td '.(in_array('address',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_address" type="text" value="'.$address.'"/>';
                    echo '      </td>';
                    echo '      <td '.(in_array('time',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_time" type="text" value="'.$time.'"/>';
                    echo '      </td>';
                    echo '      <td '.(in_array('registration',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_registration" type="checkbox" onclick="javascript:document.getElementById(\'la_max_participants\').disabled = !document.getElementById(\'la_max_participants\').disabled;"'.($registration ? ' checked' : '').'/>';
                    echo '      </td>';
                    echo '      <td '.(in_array('max_participants',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input id="la_max_participants" name="la_max_participants" type="text" value="'.$max_participants.'"'.(!$registration ? ' disabled' : '').'/>';
                    echo '      </td>';
                    echo '      <td'.($error_invalid_email | in_array('email',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_email" type="text" value="'.$email.'"/>';
                    echo '      </td>';
                    echo '      <td '.(in_array('phonenumber',$error_mandatory_fields) ? ' class="la-error"' : '').'>';
                    echo '          <input name="la_phonenumber" type="text" value="'.$phonenumber.'"/>';
                    echo '      </td>';
                    echo '      <td>';
                    echo '          <button type="submit" title="Senden" name="'.EDITING_CONFIRMATION_COMMAND_STRING.'" value="'.$i.'">';
                    echo '              <span class="dashicons dashicons-yes-alt"/>';
                    echo '          </button>';
                    echo '      </td>';
                    echo '      </form>';
                    echo '      <td>';
                    echo '          <form action="'.get_current_url().'" method="post">';
                    echo '              <button type="submit" title="Abbrechen">';
                    echo '                  <span class="dashicons dashicons-dismiss"/>';
                    echo '              </button>';
                    echo '          </form>';
                    echo '      </td>';
                }
                else {
                    echo '      <td>'.$name.'</td>';
                    echo '      <td>'.$title.'</td>';
                    echo '      <td>'.$description.'</td>';
                    echo '      <td>'.$address.'</td>';
                    echo '      <td>'.$time.'</td>';
                    echo '      <td>'.($registration ? 'ja' : 'nein').'</td>';
                    echo '      <td>'.$max_participants.'</td>';
                    echo '      <td><a href="mailto:'.$email.'">'.$email.'</a></td>';
                    echo '      <td>'.$phonenumber.'</td>';
                    
                    if (!$editing_command) {
                        echo '      <td>';
                        echo '          <form action="'.get_current_url().'" method="post">';
                        echo '              <button type="submit" title="Bearbeiten" name="'.EDITING_COMMAND_STRING.'" value="'.$i.'">';
                        echo '                  <span class="dashicons dashicons-edit"/>';
                        echo '              </button>';
                        echo '          </form>';
                        echo '      </td>';
                        echo '      <td>';
                        echo '          <form action="'.get_current_url().'" method="post">';
                        echo '              <button type="submit" title="Löschen" name="'.DELETION_COMMAND_STRING.'" value="'.$i.'">';
                        echo '                  <span class="dashicons dashicons-trash"/>';
                        echo '              </button>';
                        echo '          </form>';
                        echo '      </td>';
                    }
                    else {
                        echo '      <td></td>';
                        echo '      <td></td>';
                    }
                }
                echo '  </tr>';
            }
        }
        ?>
    </table>
</div>

<h2>Anmeldungen für heute</h2>

<?php
$now = microtime(true);
$start = mktime(0,0,0,12,1,date('Y'));
$end = mktime(23,59,59,12,24,date('Y'));
if ($now < $start || $now > $end) {
    echo 'Es ist noch nicht Adventszeit.';
    return;
}

$day = date('j');
if (!$controller->hasHost($day)) {
    echo 'Keine Reservierung für heute vorhanden.';
    return;
}

if (!$controller->getHostInformation($day, 'registration')) {
    echo 'Für das heutige Türchen sind Anmeldungen nicht aktiviert.';
    return;
}

$participants = $controller->getParticipantsNumber($day);
if ($participants == 0) {
    echo 'Für das heutige Türchen hat sich bisher noch keiner angemeldet.';
    return;
}
?>

<?php // TODO: create with Controller ?>
<div class="la-admin-table">
    <table>
        <tr>
            <th>Name</th>
            <th>E-Mail</th>
        </tr>
        <?php
        for ($i = 0; $i < $participants; $i++) {
            $email = $controller->getParticipantInformation($day,$i,'email');
            $name = $controller->getParticipantInformation($day,$i,'name');
            echo '  <tr>';
            echo '      <td>'.$name.'</td>';
            echo '      <td><a href="mailto:'.$email.'">'.$email.'</a></td>';
            echo '  </tr>';
        }
        ?>
    </table>
</div>
<?php
// ---- constants ----
// commands
$COMMAND_STRING = "command";
$ACTIVATE = 1;
$DEACTIVATE = 2;

// ---- initialize variables ----
$controller = new Controller();

// ---- output ----
?>

<h1>Lebendiger Adventskalender Administration</h1>

<?php
// check for POST variables
$command = filter_input(INPUT_POST,$COMMAND_STRING,FILTER_SANITIZE_NUMBER_INT);
if ($command) {
	switch($command) {
        case $ACTIVATE:
            if (!$controller->isActiveCalendar()) {
                $result = $controller->activateCalendar();
                if (is_wp_error($result)) {
                    echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender konnte nicht aktiviert werden. Fehler: '.$result->get_error_message().'</p></div>';
                }
                else {
                    echo '<div class="notice notice-success"><p>Der Lebendige Adventskalender wurde aktiviert.</p></div>';
                }
            }
            else {
                echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender ist bereits aktiv.</p></div>';
            }
            break;
        case $DEACTIVATE:
            if ($controller->isActiveCalendar()) {
                if ($controller->deactivateCalendar()) {
                    echo '<div class="notice notice-success"><p>Der Lebendige Adventskalender wurde deaktiviert.</p></div>';
                }
                else {
                    echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender konnte nicht deaktiviert werden.</p></div>';
                }
            }
            else {
                echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender ist bereits inaktiv.</p></div>';
            }
            break;
    }
}
?>

<form action="<? echo get_current_url() ?>" method="post">
  <div><label>Status: <? echo $controller->isActiveCalendar() ? "aktiv" : "inaktiv" ?></label></div>
  <div><button class="button button-primary" type="submit" name="<? echo $COMMAND_STRING?>" value="<? echo $controller->isActiveCalendar() ? $DEACTIVATE.'">Stoppen' : $ACTIVATE.'">Starten' ?></button></div>
</form>
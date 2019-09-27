<?php
// ---- constants ----
// current page
$LINK = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// commands
$COMMAND = "command";
$ACTIVATE = 1;
$DEACTIVATE = 2;

// initialize variables
$controller = new Controller();
?>

<h1>Lebendiger Adventskalender Administration</h1>

<?php
// check for POST variables
if (isset($_POST[$COMMAND])) {
	switch($_POST[$COMMAND]) {
        case $ACTIVATE:
            if (!$controller->isActiveCalendar()) {
                $controller->activateCalendar();
                echo '<div class="notice notice-success"><p>Der Lebendige Adventskalender wurde aktiviert.</p></div>';
            }
            else {
                echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender ist bereits aktiv.</p></div>';
            }
            break;
        case $DEACTIVATE:
            if ($controller->isActiveCalendar()) {
                $controller->deactivateCalendar();
                echo '<div class="notice notice-success"><p>Der Lebendige Adventskalender wurde deaktiviert.</p></div>';
            }
            else {
                echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender ist bereits inaktiv.</p></div>';
            }
            break;
    }
}
?>

<form action=<? echo $LINK ?> method="post">
  <div><label>Status: <? echo $controller->isActiveCalendar() ? "aktiv" : "inaktiv" ?></label></div>
  <div><button class="button button-primary" type="submit" name="<? echo $COMMAND?>" value="<? echo $controller->isActiveCalendar() ? $DEACTIVATE.'">Stoppen' : $ACTIVATE.'">Starten' ?></button></div>
</form>
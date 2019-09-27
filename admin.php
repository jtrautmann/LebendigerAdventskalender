<?php
// commands
$COMMAND = "command"
$ACTIVATE = 1;
$DEACTIVATE = 2;

// initialize variables
$controller = new Controller();
?>

<h1>Lebendiger Adventskalender Administration</h1>

<?php
// check for POST variables
if (isset($_POST[$COMMAND]))
{
	switch($_POST[$COMMAND]) {
        case $ACTIVATE:
            if (!$controller->isActiveCalendar()) {
                $controller->activateCalendar();
                echo '<div class="notice notice-success"><p>Der Lebendige Adventskalender wurde aktiviert.</p></div>"';
            }
            else {
                echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender ist bereits aktiv.</p></div>"';
            }
            break;
        case $DEACTIVATE:
            if ($controller->isActiveCalendar()) {
                $controller->deactivateCalendar();
                echo '<div class="notice notice-success"><p>Der Lebendige Adventskalender wurde deaktiviert.</p></div>"';
            }
            else {
                echo '<div class="notice notice-error"><p>Der Lebendige Adventskalender ist bereits inaktiv.</p></div>"';
            }
            break;
    }
}
?>

<form action=<?__FILE__?> method="post">
  <label>Status: <?$controller->isActiveCalendar() ? echo "aktiv" : echo "inaktiv"?></label>
  <button type="submit" name="<?$COMMAND?>" value="<?$controller->isActiveCalendar() ? $DEACTIVATE.">Stoppen" : $ACTIVATE.">Starten" ?></button>
</form>
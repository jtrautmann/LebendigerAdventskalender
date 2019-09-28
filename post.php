<? wp_enqueue_style('lebendiger_adventskalender'); ?>

<div id="imgMap">
<div id="year"><?php date_default_timezone_set('Europe/Berlin'); echo date("Y"); ?></div>
<?php
for ($i = 1; $i < 25; $i++) {
	echo '<div id="t'.$i.'"><a href="'.plugin_dir_url(__FILE__).'door.php?nr='.$i.'"></a></div>';
	echo "\n";
}
?>
</div>

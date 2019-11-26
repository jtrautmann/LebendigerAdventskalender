<?php
// ---- load style ----
wp_enqueue_style('lebendiger_adventskalender_calendar');

// ---- output ----
?>

<div id="imgMap">
    <div id="year"><?php echo date("Y") ?></div>

<?php
for ($i = 1; $i < 25; $i++) {
    $link = add_param(get_current_url(), 'nr', $i);
    $randshift = rand(1,5);
	$linkShifted = shift($link, $randshift);
    echo '	<div id="t'.$i.'"><a href="javascript:linkTo_UnCryptLink(\''.$linkShifted.'\','.$randshift.')"></a></div>';
}
?>

</div><!-- imgMap -->

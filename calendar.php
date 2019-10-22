<?php
// ---- load style ----
wp_enqueue_style('lebendiger_adventskalender_calendar');

// ---- output ----
?>

<div id="imgMap">
    <div id="year"><?php echo date("Y") ?></div>

<?php
for ($i = 1; $i < 25; $i++)
    echo '	<div id="t'.$i.'"><a href="'.add_param(get_current_url(), 'nr', $i).'"></a></div>';
?>

</div><!-- imgMap -->

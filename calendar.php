<?
// ---- load style ----
wp_enqueue_style('lebendiger_adventskalender_calendar');

// ---- output ----
?>

<div id="imgMap">
    <div id="year"><? echo date("Y") ?></div>

<?
for ($i = 1; $i < 25; $i++)
    echo '	<div id="t'.$i.'"><a href="'.get_current_url().'?nr='.$i.'"></a></div>';
?>

</div><!-- imgMap -->
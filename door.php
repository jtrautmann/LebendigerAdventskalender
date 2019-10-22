<?php

// ---- functions ----
function echo_participant_formular($nr, $input=false, $name=NULL, $email=NULL, $valid_email=false) {
	// TODO: create with DataHandler
	echo '<div class="formular">
<h3>Es sind noch Plätze frei!</h3>
<form class="pure-form" action="'.add_param(get_current_url(), 'nr', $nr).'" method="post">
<fieldset>
<label for="name">Name</label>
<input id="name" name="name" type="text" required';
	if ($input) {
		if(!$name)
			echo ' class="f"';
		else
			echo ' value="'.$name.'"';
	}
	echo '>
<label for="email">E-Mail (optional)</label>
<input id="email" name="email" type="email"';
	if ($input) {
		if(!$email)
			echo '" class="f"';
		else {
			echo ' value="'.$email.'"';
			if (!$valid_email)
				echo '" class="f"';
		}
	}
	echo '>
<button type="submit" class="pure-button pure-button-primary">teilnehmen</button>
</fieldset>
</form>
</div>';
}

// ---- load style and script ----
wp_enqueue_style('lebendiger_adventskalender_door');
wp_enqueue_script('lebendiger_adventskalender_door');

// ---- output ----
$diffMillisec = round((mktime(0,0,0,12,$nr,date('Y'))-microtime(true))*1000);
$trigger_countdown = function() use ($diffMillisec) {
	echo "<script>countdown($diffMillisec)</script>";
};
if ($diffMillisec > 0) {
	add_action('wp_footer', $trigger_countdown,1000);
}
?>

<div class="main">
	<div class="top">
		<div id="year"><?php echo date("Y") ?></div>
		<a href="<?php echo remove_param(get_current_url(), 'nr') ?>" title="Zurück zur Türchenübersicht"></a>
		<img src="<?php echo plugin_dir_url(__FILE__) ?>pics/heading/<?php echo $nr ?>.jpg" alt="Türchen <?php echo $nr ?>"/>
	</div>

<?php
if ($diffMillisec > 0) {
	echo '<div class="misc"><b>Türchen Nr. '.$nr.' ist schon reserviert. Für die Anmeldung bist zu früh!</b><br/>Versuch\'s in <span id="timer">'.$diffMillisec.' ms</span> nochmal...</div>';
}
else {
	
	// output host information
	echo '<div class="info">';
	$w = date('w', mktime(0,0,0,12,$nr,date('Y')));
	echo '<h3>'.$WEEK_DAYS[$w].', '.$nr.'.</h3>';
	echo '<div class="i">';
	echo '<h2>'.$controller->getHostInformation($nr, 'title').'</h2>';
	$description = $controller->getHostInformation($nr, 'description');
	if ($description) {
		$description =
			str_replace("\n",'<br/>',
						preg_replace('@(?<![.*">])\b(?:(?:https?|ftp|file)://|[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i', 
										'<a href="\0" target="_blank">\0</a>',
						$description));
		echo '<p style="font-size: 11pt; margin-bottom: 20px;">'.$description.'</p>';
	}
	echo '<table>';
	$address = $controller->getHostInformation($nr, 'address');
	if (strpos($address,'Karlsruhe'))
		$mapslink = 'http://maps.google.com/?q='.$address;
	else
		$mapslink = 'http://maps.google.com/?q='.$address.' Karlsruhe';
	echo '<tr><td class="l">Wo:</td><td><a target="_blank" href="'.$mapslink.'">'.$address.'</a></td></tr>';
	echo '<tr><td class="l">Ab wann:</td><td>'.$controller->getHostInformation($nr, 'time').'</td></tr>';
	echo '<tr><td class="l s">Gastgeber:</td><td class="s">'.$controller->getHostInformation($nr, 'name').'</td></tr>';
	$phone = $controller->getHostInformation($nr, 'phonenumber');
	if($phone)
		echo '<tr><td class="l">Telefonnr.:</td><td>'.$phone.'</td></tr>';
	$emailShifted = rand_shift($controller->getHostInformation($nr, 'email'));
	echo '<tr><td class="l">E-Mail:</td><td><a href="javascript:linkTo_UnCryptMailto(\''.$emailShifted.'\','.$randshift.')"><script type="text/javascript">document.write(UnCryptMailto(\''.$emailShifted.'\','.$randshift.'));</script></a></td></tr>';
	echo '</table>';
	echo '<br/></div></div>';
	$image = $controller->getHostInformation($nr, 'image');
	if($image)
		echo '<div class="bild"><a target="_blank" href="img/'.$image.'" data-lightbox="bild"><img src="img/'.$image.'"/></a></div>';
	$registration = $controller->getHostInformation($nr, 'registration');
	if($registration) {
		$max_participants = $controller->getHostInformation($nr, 'max_participants');
		$num_participants = $controller->getParticipantsNumber($nr);
		if(!$max_participants || $num_participants < $max_participants) {
			// registration still possible
			if($_SERVER['REQUEST_METHOD']=="POST") {
				// TODO: create with DataHandler
				$args = array(
					'name' => FILTER_SANITIZE_STRING,
					'email' => FILTER_SANITIZE_EMAIL
				);
				$inputs = filter_input_array(INPUT_POST, $args);

				$valid_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
				if ($inputs['name'] && $inputs['email'] && $valid_email) {
					// registration
					// TODO: send email to participant and host
					if ($controller->addParticipant($nr, $inputs))
						echo '<div class="formular" style="color: #0075e2;"><h3>Anmeldung erfolgreich</h3>Viel Spaß bei diesem Türchen!</div>';
					else
						echo '<div class="formular f"><h3>Anmeldung konnte nicht erfolgreich abgeschlossen werden</h3>Versuche es bitte erneut</div>';
				}
				else
					echo_participant_formular($nr, true, $name, $email, $valid_email);
			}
			else
				echo_participant_formular($nr);
		}
		else
			echo '<div class="formular"><h3>Keine Plätze mehr frei</h3>Leider wurde die maximale Teilnehmerzahl schon erreicht!<br/>Du kannst den / die Gastgeber persönlich fragen, ob du trotzdem dazkommen darfst.</div>';
	}
	else
		echo '<div class="formular"><h3>Keine Anmeldung erforderlich</h3>An dieser Aktion kannst du ganz spontan ohne Anmeldung teilnehmen!</div>';
}
?>

</div><!-- main -->
<script src="lightbox/lightbox-plus-jquery.min.js"></script>
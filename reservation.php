<?php
// ---- load style ----
wp_enqueue_style('lebendiger_adventskalender_reservation');

// ---- initialize variables ----
// TODO: create with DataHandler
$inputs = [
	'la_title' => '',
	'la_description' => '',
	'la_address' => '',
	'la_time' => '',
	'la_max_participants' => '',
	'la_registration' => true,
	'la_name' => '',
	'la_email' => '',
	'la_phonenumber' => ''
];

$mandatory = [
	'la_name' => '*',
	'la_title' => '*',
	'la_address' => '*',
	'la_time' => '*',
	'la_email' => '*'
];

$mandatory_field_empty = false;
$emailerror = '';
$readonly = '';
$w = date('w', mktime(0,0,0,12,$nr,date('Y')));


// ---- output ----
$title = '<div class="info">Türchen Nr. '.$nr.' ist noch frei. Reserviere hier das Türchen für deine Aktion!</div>';
$buttons = '<button type="submit" class="pure-button pure-button-primary">absenden</button>';
$image_upload = '<div id="fine-uploader"></div><input id="la_image" name="la_image" type="hidden" value=""/>';

if ($_SERVER['REQUEST_METHOD']=="POST") {
	$args = [
	    'la_title' => FILTER_SANITIZE_STRING,
	    'la_description' => FILTER_SANITIZE_STRING,
	    'la_address' => FILTER_SANITIZE_STRING,
	    'la_time' => FILTER_SANITIZE_STRING,
	    'la_max_participants' => FILTER_SANITIZE_NUMBER_INT,
	    'la_name' => FILTER_SANITIZE_STRING,
	    'la_email' => FILTER_SANITIZE_EMAIL,
	    'la_phonenumber' => FILTER_SANITIZE_STRING,
	    'la_image' => FILTER_SANITIZE_STRING
	];
	$inputs = filter_input_array(INPUT_POST, $args);

	$inputs['la_registration'] = isset($_POST['la_registration']) ? true : false;
	
	if ($inputs['la_max_participants']) {
		if(strpos($inputs['la_max_participants'],'-')) {
			$expl = explode('-',$inputs['la_max_participants']);
			$inputs['la_max_participants'] = end($expl);
		}
		$inputs['la_max_participants'] = intval($inputs['la_max_participants']);
	}
	
	$valid_email = filter_input(INPUT_POST, 'la_email', FILTER_VALIDATE_EMAIL);
	if ($inputs['la_email'] && !$valid_email)
		$emailerror = '<div class="error" style="margin-left: 10px; font-size: 11pt; text-align: center;">keine gültige E-Mail-Adresse!</div>';

	if ($inputs['la_image'])
		$image_upload = '<img id="image_preview" src="img_tmp/'.$inputs['la_image'].'" alt="Bild" style="max-width: 350px; max-height: 350px;"/> <div id="fine-uploader"></div><input id="la_image" name="la_image" type="hidden" value="'.$inputs['la_image'].'"/>';

	foreach ($mandatory as $key => $value) {
		if (!$inputs[$key]) {
			$mandatory[$key] = '<span class="error l">*</span>';
			$mandatory_field_empty = true;
		}
	}
	if (!$mandatory_field_empty && !$emailerror && !isset($_POST['correct'])) {
		$readonly = ' readonly';
		$buttons = '<div class="b" style="margin-bottom: 10px;">Sind die Eingaben korrekt übernommen worden?</div><button type="submit" class="pure-button pure-button-primary" name="correct">korrigieren</button><button type="submit" class="pure-button pure-button-primary" style="margin-left: 10px;" name="confirm">alles korrekt, absenden</button>';
		if($inputs['la_image'])
			$image_upload = '<img id="image_preview" src="img_tmp/'.$inputs['la_image'].'" alt="Bild" style="max-width: 350px; max-height: 350px;"/><input id="la_image" name="la_image" type="hidden" value="'.$inputs['la_image'].'"/>';
		else
			$image_upload = '<input id="la_image" name="la_image" type="hidden" value=""/>';
	}
	if (isset($_POST['confirm'])) {
		$buttons = '';
		if ($inputs['la_image'])
			copy('img_tmp/'.$inputs['la_image'],'img/'.$inputs['la_image']);
		else
			$image_upload = '<input id="la_image" name="la_image" type="hidden" value=""/>';
		$data = [];
		foreach ($inputs as $key => $value) {
			// delete the "la_" prefix of the key
			$new_key = substr($key, 3);
			$data[$new_key] = $value;
		}
		if ($controller->addHost($nr, $data)) {
			$title = '<div class="b">Dein Adventskalender-Türchen wurde hinzugefügt! Cool, dass du mitmachst!</div>';
		}
		else
			$title = '<div class="error">Dein Adventskalender-Türchen konnte leider nicht hinzugefügt werden! Versuche es bitte erneut!</div>';
		
		// send confirmation mail to host
		// TODO: make mail address of sender configurable
		$sender = 'bl-prweb@sfc-karlsruhe.de';
		$subject = 'Dein SfC-Adventskalender-Türchen wurde eingetragen';
		$encoded_subject = '=?utf-8?B?'.base64_encode($subject).'?=';
		// TODO: add link to page to change the data and see the participants
		$text = "Dein Adventskalender-Türchen am $WEEK_DAYS[$w], den $nr";
		$text .= ". Dezember wurde erfolgreich eingetragen!";
		$headers = [];
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=utf-8";
		$headers[] = "From: $sender";
		$headers[] = "Reply-To: $sender";
		$headers[] = "X-Mailer: PHP/".phpversion();
		
		mail($inputs['la_email'], $encoded_subject, $text, implode("\r\n",$headers));
	}
}

// fill mandatory array for non-mandatory inputs
foreach ($inputs as $key => $value) {
	if (!isset($mandatory[$key])) {
		$mandatory[$key] = '';
	}
}
?>

<!-- START CODE FOR IMAGE UPLOAD -->
<link href="fineuploader/fineuploader.css" rel="stylesheet">
<script src="fineuploader/fineuploader.js"></script>
<script type="text/template" id="qq-template">
	<div class="qq-uploader-selector qq-uploader">
		<ul class="qq-upload-list-selector qq-upload-list">
			<li>
				<div style="text-align: center;">
				<span class="qq-upload-file-selector qq-upload-file"></span><br/>
				<img style="margin: 5px 0;" class="qq-thumbnail-selector" qq-max-size="330" qq-server-scale style=""><br/>
				<div class="qq-progress-bar-container-selector">
					<div class="qq-progress-bar-selector qq-progress-bar"></div>
				</div>
				<span class="qq-upload-spinner-selector qq-upload-spinner"></span>
				<span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
				<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
				<span class="qq-upload-size-selector qq-upload-size"></span>
				<a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Abbrechen</a>
				<a class="qq-upload-retry-selector qq-upload-retry" href="#">Wiederholen</a>
				<a class="qq-upload-delete-selector qq-upload-delete" href="#">Löschen</a>
				<span class="qq-upload-status-text-selector qq-upload-status-text"></span>
				</div>
			</li>
		</ul>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span>Hochzuladende Datei hier ablegen</span>
		</div>
		<div class="qq-upload-button-selector pure-button" style="font-size: 10pt;">
			<div>Datei wählen</div>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
			<span>Verarbeite abgelegte Datei..</span>
			<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
	</div>
</script>
<script src="https://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js" type="text/javascript"></script>
<script>
	function createUploader() {
		var uploader = new qq.FineUploader({
			element: $('fine-uploader'),
			request: {
				endpoint: 'fineuploader/endpoint.php'
			},
			multiple: false,
			validation: {
				acceptFiles: 'image/*',
				allowedExtensions: new Array('jpeg', 'jpg', 'png', 'gif', 'tif', 'tiff'),
				sizeLimit: 5000000,
			},
			callbacks:{
				onSubmit: function() { $('image_preview').remove(); },
				onComplete: function(id, fileName, responseJSON) {
					if (responseJSON.success) {
						$('la_image').value = responseJSON.uploadName;
					}
				}
			}
		});
	 }
	 window.onload = createUploader;
</script>
<!-- END CODE FOR IMAGE UPLOAD -->


<h3><?php echo $WEEK_DAYS[$w] ?>, den <?php echo $nr ?>. Dezember</h3>
<?php echo $title ?>
<form class="pure-form pure-form-aligned" action="<?php echo add_param(get_current_url(), 'nr', $nr) ?>" method="post">
<fieldset>
	<div class="pure-control-group">
		<label for="la_name">Gastgeber<?php echo $mandatory['la_name']; ?></label>
		<input id="la_name" name="la_name" type="text" value="<?php echo $inputs['la_name']; ?>" placeholder="Dein Name / eure Namen"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="title">Aktion<?php echo $mandatory['la_title']; ?></label>
		<input id="la_title" name="la_title" type="text" value="<?php echo $inputs['la_title']; ?>" placeholder="Kurzer Titel des Adventskalender-Türchens"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="la_description">Beschreibung<?php echo $mandatory['la_description']; ?></label>
		<textarea rows="8" id="la_description" name="la_description" placeholder="Etwas ausführlichere Beschreibung der Aktion"<?php echo $readonly; ?>><?php echo $inputs['la_description']; ?></textarea>
	</div>
	<div class="pure-control-group">
		<label for="la_address">Adresse<?php echo $mandatory['la_address']; ?></label>
		<input id="la_address" name="la_address" type="text" value="<?php echo $inputs['la_address']; ?>" placeholder="Wo macht das Türchen auf?"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="la_time">Uhrzeit<?php echo $mandatory['la_time']; ?></label>
		<input id="la_time" name="la_time" type="text" value="<?php echo $inputs['la_time']; ?>" placeholder="Ab wann?"<?php echo $readonly; ?>/>
	</div>
	<div style="margin: 10px 0 0 165px; font-size: 11pt;">
	<label for="la_registration" class="pure-checkbox">
		<input id="la_registration" name="la_registration" type="checkbox" onclick="javascript:$('la_max_participants').disabled = !$('la_max_participants').disabled;"<?php if($inputs['la_registration']) echo ' checked'; ?>> Anmeldung erforderlich oder zumindest erwünscht
	</label>
	</div>
	<div class="pure-control-group">
		<label for="la_max_participants">max. Teilnehmer<?php echo $mandatory['la_max_participants']; ?></label>
		<input id="la_max_participants" name="la_max_participants" type="text" value="<?php echo $inputs['la_max_participants']; ?>" placeholder="Wie viele Leute dürfen kommen?"<?php echo $readonly; if(!$inputs['la_registration']) echo ' disabled'; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="la_email">E-Mail<?php echo $mandatory['la_email']; ?></label>
		<input id="la_email" name="la_email" type="text" value="<?php echo $inputs['la_email']; ?>" placeholder="Deine E-Mail-Adresse"<?php echo $readonly; ?>/><?php echo $emailerror; ?>
	</div>
	<div class="pure-control-group">
		<label for="la_phonenumber">Telefonnummer</label>
		<input id="la_phonenumber" name="la_phonenumber" type="text" value="<?php echo $inputs['la_phonenumber']; ?>" placeholder="Deine Telefonnummer (erscheint auf der Website!)"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label style="margin-top: -5px;">Flyer / Bild<br/>zur Einstimmung</label>
		<div style="display: inline-block;"><?php echo $image_upload ?></div>
	</div>
	<div class="pure-control-group"><label <?php if ($mandatory_field_empty) echo 'class="error"'?>><span class="l">*</span> Pflichtfeld(er)</label></div>
	<div class="pure-controls">
		<?php echo $buttons; ?>
	</div>
</fieldset>
</form>
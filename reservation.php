<?php
// ---- load style ----
wp_enqueue_style('lebendiger_adventskalender_reservation');

// ---- initialize variables ----
$input = $controller->getReservationInput();
$input_data = $input->getData();

$mandatory_fields = $controller->getReservationMandatoryInput();
$mandatory = [];
foreach ($input_data as $key => $value) {
	if (in_array($mandatory_fields,$key)) {
		$mandatory[$key] = '*';
	}
	else {
		$mandatory[$key] = '';
	}
}

$mandatory_field_empty = false;
$emailerror = '';
$readonly = '';
$w = date('w', mktime(0,0,0,12,$nr,date('Y')));


// ---- output ----
$title = '<div class="info">Türchen Nr. '.$nr.' ist noch frei. Reserviere hier das Türchen für deine Aktion!</div>';
$buttons = '<button type="submit" class="pure-button pure-button-primary">absenden</button>';
$image_upload = '<div id="fine-uploader"></div><input id="la_image" name="la_image" type="hidden" value=""/>';

if ($input->inputReceived()) {
	if ($input->hasError()) {
		$error = $input->getError();
		if (in_array(InputErrorType::INVALID_EMAIL,$error->get_error_codes())) {
			$emailerror = '<div class="error" style="margin-left: 10px; font-size: 11pt; text-align: center;">keine gültige E-Mail-Adresse!</div>';
		}
		if (in_array(InputErrorType::MANDATORY_MISSING,$error->get_error_codes())) {
			$mandatory_field_empty = true;
			foreach ($error->get_error_data() as $value) {
				$mandatory[$value] = '<span class="error l">*</span>';
			}
		}
	}

	if ($input_data['image'])
		$image_upload = '<img id="image_preview" src="img_tmp/'.$input_data['image'].'" alt="Bild" style="max-width: 350px; max-height: 350px;"/> <div id="fine-uploader"></div><input id="la_image" name="la_image" type="hidden" value="'.$input_data['image'].'"/>';
	
	if (!$mandatory_field_empty && !$emailerror && !isset($_POST['correct'])) {
		$readonly = ' readonly';
		$buttons = '<div class="b" style="margin-bottom: 10px;">Sind die Eingaben korrekt übernommen worden?</div><button type="submit" class="pure-button pure-button-primary" name="correct">korrigieren</button><button type="submit" class="pure-button pure-button-primary" style="margin-left: 10px;" name="confirm">alles korrekt, absenden</button>';
		if($input_data['image'])
			$image_upload = '<img id="image_preview" src="img_tmp/'.$input_data['image'].'" alt="Bild" style="max-width: 350px; max-height: 350px;"/><input id="la_image" name="la_image" type="hidden" value="'.$input_data['image'].'"/>';
		else
			$image_upload = '<input id="la_image" name="la_image" type="hidden" value=""/>';
	}
	
	if (isset($_POST['confirm'])) {
		$buttons = '';
		if ($input_data['image'])
			copy('img_tmp/'.$input_data['image'],'img/'.$input_data['image']);
		else
			$image_upload = '<input id="la_image" name="la_image" type="hidden" value=""/>';
		
		if ($controller->addHost($nr, $input_data)) {
			$title = '<div class="b">Dein Adventskalender-Türchen wurde hinzugefügt! Cool, dass du mitmachst!</div>';
		}
		else {
			$title = '<div class="error">Dein Adventskalender-Türchen konnte leider nicht hinzugefügt werden! Versuche es bitte erneut!</div>';
		}
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


<h3><?php echo WEEK_DAYS[$w] ?>, den <?php echo $nr ?>. Dezember</h3>
<?php echo $title ?>
<form class="pure-form pure-form-aligned" action="<?php echo add_param(get_current_url(), 'nr', $nr) ?>" method="post">
<fieldset>
	<div class="pure-control-group">
		<label for="la_name">Gastgeber<?php echo $mandatory['name']; ?></label>
		<input name="la_name" type="text" value="<?php echo $input_data['name']; ?>" placeholder="Dein Name / eure Namen"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="title">Aktion<?php echo $mandatory['title']; ?></label>
		<input name="la_title" type="text" value="<?php echo $input_data['title']; ?>" placeholder="Kurzer Titel des Adventskalender-Türchens"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="la_description">Beschreibung<?php echo $mandatory['description']; ?></label>
		<textarea rows="8" name="la_description" placeholder="Etwas ausführlichere Beschreibung der Aktion"<?php echo $readonly; ?>><?php echo $input_data['description']; ?></textarea>
	</div>
	<div class="pure-control-group">
		<label for="la_address">Adresse<?php echo $mandatory['address']; ?></label>
		<input name="la_address" type="text" value="<?php echo $input_data['address']; ?>" placeholder="Wo macht das Türchen auf?"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="la_time">Uhrzeit<?php echo $mandatory['time']; ?></label>
		<input name="la_time" type="text" value="<?php echo $input_data['time']; ?>" placeholder="Ab wann?"<?php echo $readonly; ?>/>
	</div>
	<div style="margin: 10px 0 0 165px; font-size: 11pt;">
	<label for="la_registration" class="pure-checkbox">
		<input name="la_registration" type="checkbox" onclick="javascript:$('la_max_participants').disabled = !$('la_max_participants').disabled;"<?php if($input_data['registration']) echo ' checked'; ?>> Anmeldung erforderlich oder zumindest erwünscht
	</label>
	</div>
	<div class="pure-control-group">
		<label for="la_max_participants">max. Teilnehmer<?php echo $mandatory['max_participants']; ?></label>
		<input id="la_max_participants" name="la_max_participants" type="text" value="<?php echo $input_data['max_participants']; ?>" placeholder="Wie viele Leute dürfen kommen?"<?php echo $readonly; if(!$input_data['registration']) echo ' disabled'; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="la_email">E-Mail<?php echo $mandatory['email']; ?></label>
		<input name="la_email" type="text" value="<?php echo $input_data['email']; ?>" placeholder="Deine E-Mail-Adresse"<?php echo $readonly; ?>/><?php echo $emailerror; ?>
	</div>
	<div class="pure-control-group">
		<label for="la_phonenumber">Telefonnummer</label>
		<input name="la_phonenumber" type="text" value="<?php echo $input_data['phonenumber']; ?>" placeholder="Deine Telefonnummer (erscheint auf der Website!)"<?php echo $readonly; ?>/>
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
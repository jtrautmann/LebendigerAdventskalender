<?
// ---- load style ----
wp_enqueue_style('lebendiger_adventskalender_reservation');

// ---- initialize variables ----
$inputs = [
	'title' => '',
	'description' => '',
	'address' => '',
	'time' => '',
	'max_participants' => '',
	'registration' => true,
	'name' => '',
	'email' => '',
	'phonenumber' => ''
];

$mandatory = [
	'name' => '*',
	'title' => '*',
	'address' => '*',
	'time' => '*',
	'email' => '*'
];

$mandatory_field_empty = false;
$emailerror = '';
$readonly = '';
$w = date('w', mktime(0,0,0,12,$nr,date('Y')));


// ---- output ----
$title = '<div class="info">Türchen Nr. '.$nr.' ist noch frei. Reserviere hier das Türchen für deine Aktion!</div>';
$buttons = '<button type="submit" class="pure-button pure-button-primary">absenden</button>';
$image_upload = '<div id="fine-uploader"></div><input id="image" name="image" type="hidden" value=""/>';

if ($_SERVER['REQUEST_METHOD']=="POST") {
	$args = [
	    'title' => FILTER_SANITIZE_STRING,
	    'description' => FILTER_SANITIZE_STRING,
	    'address' => FILTER_SANITIZE_STRING,
	    'time' => FILTER_SANITIZE_STRING,
	    'registration' => FILTER_VALIDATE_BOOLEAN,
	    'max_participants' => FILTER_SANITIZE_NUMBER_INT,
	    'name' => FILTER_SANITIZE_STRING,
	    'email' => FILTER_SANITIZE_EMAIL,
	    'phonenumber' => FILTER_SANITIZE_STRING,
	    'image' => FILTER_SANITIZE_STRING
	];
	$inputs = filter_input_array(INPUT_POST, $args);
	
	if ($inputs['max_participants']) {
		if(strpos($inputs['max_participants'],'-')) {
			$expl = explode('-',$inputs['max_participants']);
			$inputs['max_participants'] = end($expl);
		}
		$inputs['max_participants'] = intval($inputs['max_participants']);
	}
	
	$valid_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	if ($inputs['email'] && !$valid_email)
		$emailerror = '<div class="error" style="margin-left: 10px; font-size: 11pt; text-align: center;">keine gültige E-Mail-Adresse!</div>';

	if ($inputs['image'])
		$image_upload = '<img id="image_preview" src="img_tmp/'.$inputs['image'].'" alt="Bild" style="max-width: 350px; max-height: 350px;"/> <div id="fine-uploader"></div><input id="image" name="image" type="hidden" value="'.$inputs['image'].'"/>';

	foreach ($mandatory as $key => $value) {
		if (!$inputs[$key]) {
			$mandatory[$key] = '<span class="error l">*</span>';
			$mandatory_field_empty = true;
		}
	}
	if (!$mandatory_field_empty && !$emailerror && !isset($_POST['correct'])) {
		$readonly = ' readonly';
		$buttons = '<div class="b" style="margin-bottom: 10px;">Sind die Eingaben korrekt übernommen worden?</div><button type="submit" class="pure-button pure-button-primary" name="correct">korrigieren</button><button type="submit" class="pure-button pure-button-primary" style="margin-left: 10px;" name="confirm">alles korrekt, absenden</button>';
		if($inputs['image'])
			$image_upload = '<img id="image_preview" src="img_tmp/'.$inputs['image'].'" alt="Bild" style="max-width: 350px; max-height: 350px;"/><input id="image" name="image" type="hidden" value="'.$inputs['image'].'"/>';
		else
			$image_upload = '<input id="image" name="image" type="hidden" value=""/>';
	}
	if (isset($_POST['confirm'])) {
		$buttons = '';
		if ($inputs['image'])
			copy('img_tmp/'.$inputs['image'],'img/'.$inputs['image']);
		else
			$image_upload = '<input id="image" name="image" type="hidden" value=""/>';
		if ($controller->addHost($nr, $inputs)) {
			$title = '<div class="b">Dein Adventskalender-Türchen wurde hinzugefügt! Cool, dass du mitmachst!</div>';
		}
		else
			$title = '<div class="error">Dein Adventskalender-Türchen konnte leider nicht hinzugefügt werden! Versuche es bitte erneut!</div>';
		// TODO: make mail of sender configurable
		// TODO: add link to page to change the data and see the participants
		mail(
			$inputs['email'],
			'Dein SfC-Adventskalender-Türchen wurde eingetragen',
			'Dein Adventskalender-Türchen am '.$WEEK_DAYS[$w].', den '.$nr.'. wurde erfolgreich eingetragen!',
			'Content-type: text/plain; charset=utf-8'."\r\n".'From: bl-prweb@sfc-karlsruhe.de'."\r\n".'Reply-To: bl-prweb@sfc-karlsruhe.de'."\r\n".'X-Mailer: PHP/'.phpversion()
		);
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
						$('image').value = responseJSON.uploadName;
					}
				}
			}
		});
	 }
	 window.onload = createUploader;
</script>
<!-- END CODE FOR IMAGE UPLOAD -->


<h3><? echo $WEEK_DAYS[$w] ?>, <?echo $nr ?></h3>
<? echo $title ?>
<form class="pure-form pure-form-aligned" action="<? echo get_current_url() ?>/?nr=<? echo $nr ?>" method="post">
<fieldset>
	<div class="pure-control-group">
		<label for="name">Gastgeber<?php echo $mandatory['name']; ?></label>
		<input id="name" name="name" type="text" value="<?php echo $inputs['name']; ?>" placeholder="Dein Name / eure Namen"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="title">Aktion<?php echo $mandatory['title']; ?></label>
		<input id="title" name="title" type="text" value="<?php echo $inputs['title']; ?>" placeholder="Kurzer Titel des Adventskalender-Türchens"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="description">Beschreibung<?php echo $mandatory['description']; ?></label>
		<textarea rows="8" id="description" name="description" placeholder="Etwas ausführlichere Beschreibung der Aktion"<?php echo $readonly; ?>><?php echo $inputs['description']; ?></textarea>
	</div>
	<div class="pure-control-group">
		<label for="address">Adresse<?php echo $mandatory['address']; ?></label>
		<input id="address" name="address" type="text" value="<?php echo $inputs['address']; ?>" placeholder="Wo macht das Türchen auf?"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="time">Uhrzeit<?php echo $mandatory['time']; ?></label>
		<input id="time" name="time" type="text" value="<?php echo $inputs['time']; ?>" placeholder="Ab wann?"<?php echo $readonly; ?>/>
	</div>
	<div style="margin: 10px 0 0 165px; font-size: 11pt;">
	<label for="registration" class="pure-checkbox">
		<input id="registration" name="registration" type="checkbox" onclick="javascript:$('max_participants').disabled = !$('max_participants').disabled;"<?php if($inputs['registration']) echo ' checked'; ?>> Anmeldung erforderlich oder zumindest erwünscht
	</label>
	</div>
	<div class="pure-control-group">
		<label for="max_participants">max. Teilnehmer<?php echo $mandatory['max_participants']; ?></label>
		<input id="max_participants" name="max_participants" type="text" value="<?php echo $inputs['max_participants']; ?>" placeholder="Wie viele Leute dürfen kommen?"<?php echo $readonly; if(!$inputs['registration']) echo ' disabled'; ?>/>
	</div>
	<div class="pure-control-group">
		<label for="email">E-Mail<?php echo $mandatory['email']; ?></label>
		<input id="email" name="email" type="text" value="<?php echo $inputs['email']; ?>" placeholder="Deine E-Mail-Adresse"<?php echo $readonly; ?>/><?php echo $emailerror; ?>
	</div>
	<div class="pure-control-group">
		<label for="phonenumber">Telefonnummer</label>
		<input id="phonenumber" name="phonenumber" type="text" value="<?php echo $inputs['phonenumber']; ?>" placeholder="Deine Telefonnummer (erscheint auf der Website!)"<?php echo $readonly; ?>/>
	</div>
	<div class="pure-control-group">
		<label style="margin-top: -5px;">Flyer / Bild<br/>zur Einstimmung</label>
		<div style="display: inline-block;"><?php echo $image_upload ?></div>
	</div>
	<div class="pure-control-group"><label <? if ($mandatory_field_empty) echo 'class="error"'?>><span class="l">*</span> Pflichtfeld(er)</label></div>
	<div class="pure-controls">
		<?php echo $buttons; ?>
	</div>
</fieldset>
</form>
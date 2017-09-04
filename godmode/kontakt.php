<?php
	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (kontakt.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
		exit;
	};

	#Hent fra db
	$sql = "SELECT * FROM contact WHERE id=1";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$result = mysqli_fetch_array($query);

	#Skifter panelet, alt an på status
	$panelHead = "Vidersend beskeder til";
	$panel = "default";
	if(isset($_SESSION['mailError'])){
		$panel = "danger";
		$panelHead = "Der opstod en fejl!";
		unset($_SESSION['mailError']);
	};
	if(isset($_SESSION['mailUpdated'])){
		$panel = "success";
		$panelHead = "Mailen blev opdateret";
		unset($_SESSION['mailUpdated']);
	};
	if(isset($_SESSION['guestWarning'])){
		$panelHead = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};


	#Hvor mange gange mail funktionen er blevet anvendt
	echo '<div class="alert alert-info" role="alert">';
		echo'<b>Goodies!</b> Mail funktionener blevet anvendt '.$result['contacted'].' gange';
	echo'</div>';

	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading"><b>Vidersend beskeder til</b></div>';
		echo'<div class="panel-body">';

			echo'<form method="post" action="godmode/php/updateContact.php">';
			echo'<div class="input-group">';
				echo'<span class="input-group-btn">';
					echo'<input type="submit" name="okMail" value="Opdater" class="btn btn-primary"/>';
				echo'</span>';
				echo'<input type="" id="mail" name="mail" value="'.$result['mail_to'].'" class="form-control" required/>';
				echo'</div>';
			echo'</form>';

		echo'</div>';
	echo'</div>';



	#Skifter panelet, alt an på status
	$panelHead = "Sidens indhold";
	$panel = "default";
	if(isset($_SESSION['contactError'])){
		$panel = "danger";
		$panelHead = "Der opstod en fejl!";
		unset($_SESSION['contactError']);
	};
	if(isset($_SESSION['contactUpdated'])){
		$panel = "success";
		$panelHead = "Kontakt siden blev opdateret";
		unset($_SESSION['contactUpdated']);
	};
	if(isset($_SESSION['guestWarning'])){
		$panelHead = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};



	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading"><b>'.$panelHead.'</b></div>';
		echo'<div class="panel-body">';

		echo'<form method="post" action="godmode/php/updateContact.php">';

			echo'<textarea name="txt" class="form-control" id="summernote" style="margin-bottom:1%;" required>'.$result['txt'].'</textarea>';
			echo'<input type="submit" name="okUpdate" class="btn btn-block btn-primary" style="margin-top:.5%;" value="Opdater"/>';
		echo'</form>';
		echo'</div>';
	echo'</div>';
?>

<script>
	//wysiwyg
	$(document).ready(function() {
	    $('#summernote').summernote({
	        height:500,
	        toolbar: [
	            ['insert',['picture','link','video']],
	            ['font style',['fontsize','color','bold','italic','underline','strikethrough']],
	            ['para',['ol','ul','paragraph']],
	            ['misc',['undo','codeview']]
	        ]
	    });
	})
</script>
<?php

	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/updateContact.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../../");
		exit;
	};


	#Ikke foretag noget hvis det er gæsteprofilen
	if($_SESSION['sloaLogged'] == 2){
		$_SESSION['guestWarning'] = true;
		header("location:".$_SESSION['last']);
		exit;
	};


	#Ideen kører på at opdatere id#1, så med mindre id#1 findes fungere intet. Her sørger jeg lige for, at id#1 er oprettet.
	$sql = "SELECT id FROM contact WHERE id=1";
	$query  = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	if(mysqli_num_rows($query) != true){
		$sql = "INSERT INTO main (txt) VALUES ('<h3>Kontakt udgiver bag sloa.dk</h3>')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
	};



	#Hvis man opdaterer teksten på sitet
	if(isset($_POST['okUpdate'])){

		#Klargør inputs
		$txt = mysqli_real_escape_string($conn,$_POST['txt']);
		$sloaLogged = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

		#Forsøg at opdater databasen og opret en log undervejs
		$sql = "UPDATE contact SET txt='".$txt."' WHERE id=1";
		if(mysqli_query($conn,$sql)){
			$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Sidens inhold blev opdateret','".$time."','kontakt','".$sloaLogged."')";
			$_SESSION['contactUpdated'] = TRUE;
		}else{
			$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Sidens inhold blev ikke opdateret','".$time."','kontakt','".$sloaLogged."',1)";
			$_SESSION['contactError'] = TRUE;
		};
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
	};


	#Hvis man opdaterer mailen
	if(isset($_POST['okMail'])){

		#Klargør input
		$mail = mysqli_real_escape_string($conn,$_POST['mail']);

		#Opdater db og opret log
		$sql = "UPDATE contact SET mail_to='".$mail."' WHERE id=1";
		if(mysqli_query($conn,$sql)){
			$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Kontakt mailen blev skiftet','".$time."','kontakt','".$sloaLogged."')";
			$_SESSION['mailUpdated'] = TRUE;
		}else{
			$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Kontakt mailen blev ikke skiftet','".$time."','kontakt','".$sloaLogged."',1)";
			$_SESSION['mailError'] = TRUE;
		};
	};

	header("location:".$_SESSION['last']);

?>
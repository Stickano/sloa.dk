<?php

	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/updateIndex.php)','".$time."',1,'sikkerhed')";
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


	if(isset($_POST['okUpdate'])){

		#Ideen kører på at opdatere id#1, så med mindre id#1 findes fungere intet. Her sørger jeg lige for, at id#1 er oprettet.
		$sql = "SELECT id FROM main WHERE id=1";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
		if(mysqli_num_rows($query) != true){
			$sql = "INSERT INTO main (txt) VALUES ('<h3>Velkommen til sloa.dk</h3>')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
		};

		#Klargør input
		$txt = mysqli_real_escape_string($conn,$_POST['txt']);
		$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

		#Dobbelt tjek at der er indhold
		if(empty($txt)){
			$_SESSION['mainError'] = "Der mangler indhold til din forside";
			$sql = "INSERT INTO events (ip,event,time,rel,danger,uid) VALUES ('".$client."','Forsiden blev ikke opdateret: Ingen indhold','".$time."','forsiden',1,'".$uid."')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$error = true;
		};

		#Hvis kriterierne er mødt
		if(!isset($error)){

			#Forsøg at opdater forsiden og opret en log undervejs
			$sql = "UPDATE main SET txt='".$txt."' WHERE id=1";
			if(mysqli_query($conn,$sql)){
				$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Forsiden blev opdateret','".$time."','forsiden','".$uid."')";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$_SESSION['mainSuccess'] = true;
			}else{
				$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Forsiden blev ikke opdateret: Ukendt fejl','".$time."','forsiden','".$uid."',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$_SESSION['mainError'] = "Der opstod en fejl!";
			};
		};
	};

	header("location:".$_SESSION['last']);

?>
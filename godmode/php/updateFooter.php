<?php

	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/updateMeta.php)','".$time."',1,'sikkerhed')";
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



	#Kontakt oplysninger
	if(isset($_POST['okCon'])){

		#Klargør inputs
		$city = mysqli_real_escape_string($conn,$_POST['city']);
		$name = mysqli_real_escape_string($conn,$_POST['name']);
		$mail = mysqli_real_escape_string($conn,$_POST['mail']);
		$phone = mysqli_real_escape_string($conn,$_POST['phone']);
		$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

		$danger = 0;

		#Tjek at der ikke mangler data
		if(empty($city) || empty($name) || empty($mail) || empty($phone)){
			$error = 1;
			$_SESSION['conError'] = "Alle felter skal udfyldes";
			$event = "Data mangler til kontakt oplysninger";
			$danger = 1;
		};

		if(!isset($error)){

			$sql = "UPDATE footer SET name='".$name."', mail='".$mail."', phone='".$phone."', adress='".$city."' WHERE id=1";
			if(mysqli_query($conn,$sql)){
				$event = "Kontakt oplysninger opdateret";
				$_SESSION['conSuccess'] = TRUE;
			}else{
				$event = "Kontakt oplysningerne blev ikke opdateret (ukendt fejl)";
				$_SESSION['conError'] = "Ukendt fejl! Kontakt oplysningerne blev ikke opdateret";
				$danger = 1;
			};
		};

		$sql = "INSERT INTO events (time,event,danger,rel,uid,ip) VALUES ('".$time."','".$event."','".$danger."','footer','".$uid."','".$client."')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		header("location:".$_SESSION['last']);

	};


	# SOCIAL MEDIA #######


?>
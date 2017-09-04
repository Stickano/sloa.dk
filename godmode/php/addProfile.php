<?php

	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/addProfile.php)','".$time."',1,'sikkerhed')";
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


	#######################################
	# Mangler stadig en del arbejde
	#######################################



	#Manuel oprettelse
	if(isset($_POST['okMan'])){

		#Klargør lidt blandet
		$error = 0;
		$upass = mysqli_real_escape_string($conn,md5(sha1($_POST['upass'])));
		$uname = mysqli_real_escape_string($conn,$_POST['uname']);
		$mail = mysqli_real_escape_string($conn,$_POST['mail']);
		$web = mysqli_real_escape_string($conn,$_POST['web']);
		$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

		#Bekræft der de nødvendige felter er udfyldte
		if(empty($_POST['mail']) || empty($_POST['uname']) || empty($_POST['upass'])){
			$error = 1;
			$_SESSION['adminError'] = "Udfyld E-mail, brugernavn og en adgangskode";
		};

		#Tjek at brugernavn og email ikke er anvendt
		$sql = "SELECT id FROM users WHERE uname='".$uname."'";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$num = mysqli_num_rows($query);
		if($num == true){
			$error = 1;
			$_SESSION['adminError'] = "Brugernavn allerede i brug";
		};

		$sql = "SELECT id FROM users WHERE mail='".$mail."'";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$num = mysqli_num_rows($query);
		if($num == true){
			$error = 1;
			$_SESSION['adminError'] = "E-mail allerede i brug";
		};

		#Smid tilbage hvis alle kriterierne ikke er mødt - opret lige en log undervejs
		if($error != 0){
			$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Administrator profil blev ikke oprettet: ".$_SESSION['adminError']."','".$time."','administratorer','".$uid."',1)";
			if($_SESSION['sloaLogged'] != 2){
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			};
			header("location:".$_SESSION['last']);
			exit;
		};

		#Forsøg at opret profil, og skab en log undervejs
		if($error == 0){
			$sql = "INSERT INTO users (uname,upass,mail,web,created) VALUES ('".$uname."','".$upass."','".$mail."','".$web."','".$time."')";
			if(mysqli_query($conn,$sql)){
				$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Administrator profil oprettet: ".$mail."','".$time."','administratorer','".$uid."')";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$_SESSION['adminSuccess'] = true;

			}else{
				$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Administrator profil blev ikke oprettet: (ukendt fejl) ".$mail."','".$time."','administratorer','".$uid."',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$_SESSION['adminError'] = "Der opstod en fejl!";
			};
			header("location:".$_SESSION['last']);
		};
	};

?>
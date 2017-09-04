<?php

	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/delContent.php)','".$time."',1,'sikkerhed')";
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


	#Klargør inputs (lidt en farlig tilgang jeg har lavet her, men den er dynamisk)
	$rel = mysqli_real_escape_string($conn,$_GET['rel']);
	$id = mysqli_real_escape_string($conn,$_GET['id']);
	$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

	#Bekræft indhold
	$sql = "SELECT id FROM ".$rel." WHERE id='".$id."'";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$num = mysqli_num_rows($query);

	if($num == TRUE){

		#Retur funktion til services
		if($rel == "services"){
			$sql = "SELECT category FROM services WHERE id='".$id."'";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$result = mysqli_fetch_array($query);
			$catID = $result['category'];
			$cat = true;
		};

		#slet indhold
		$sql = "DELETE FROM ".$rel." WHERE id='".$id."'";

		#Til visning af den rette log besked (service eller kategori)
		$eventFirst = ucfirst($rel);
		$eventPara = NULL;
		#Hvis det er en service kategori man sletter, slet også dens indhold
		if($rel == "service_category"){
			$sqlServ = "DELETE FROM services WHERE category='".$id."'";
			mysqli_query($conn,$sqlServ)or die(mysqli_error($conn));
			$rel = "services";
			$eventFirst = "Kategori";
			$eventPara = "og dens indhold ";
		};

		#opret log, alt an' på udfald
		if(mysqli_query($conn,$sql)){
			$event = $eventFirst." #".$id." ".$eventPara."blev succesfuldt slettet";
			$sql = "INSERT INTO events (ip,event,time,uid,rel) VALUES ('".$client."','".$event."','".$time."','".$uid."','".$rel."')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$_SESSION['createSuccess'] = "Indholdet blev slettet";
		}else{
			$event = $eventFirst." #".$id." ".$eventPara."kunne blev ikke slettet";
			$sql = "INSERT INTO events (ip,event,time,uid,rel,danger) VALUES ('".$client."','".$event."','".$time."','".$uid."','".$rel."',1)";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$err = TRUE;
			$_SESSION['createError'] = "Indholdet kunne ikke slettes ".mysqli_error($conn);
		};

		#Slet keywords til blog artikler
		if($rel == "blog"){
			$sql = "DELETE FROM keywords WHERE blog=1 AND mid='".$id."'";
			if(mysqli_query($conn,$sql)){
				$event = "Keywords blev slettet";
				$sql = "INSERT INTO events (ip,event,time,uid,rel) VALUES ('".$client."','".$event."','".$time."','".$uid."','".$rel."')";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			}else{
				$event = "Keywords kunne ikke slettes ".mysqli_error($conn);
				$sql = "INSERT INTO events (ip,event,time,uid,rel,danger) VALUES ('".$client."','".$event."','".$time."','".$uid."','".$rel."',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$err = TRUE;
			};
		};

		#Hvis der var en tilhørende medie mappe til indholdet, send til dokumentet der håndterer medie sletning
		if($rel == "blog" || $rel == "portfolio" && !isset($err)){
			$sql = "SELECT id FROM media WHERE ".$rel."=1 AND mid='".$id."'";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$num = mysqli_num_rows($query);
			if($num == TRUE){
				$result = mysqli_fetch_array($query);
				header("location:delMedia.php?con&id=".$result['id']);
				exit;
			};
		};
	};

	if(isset($cat)){
		header("location:../../godmode.php?services&kategori=".$catID);
	}elseif($rel == "blog"){
		header("location:../../godmode.php?fl&blog");
	}else{
		header("location:".$_SESSION['last']);
	};




?>
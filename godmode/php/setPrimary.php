<?php

	#Sikkerhed bl.a.
	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/setPrimary.php)','".$time."',1,'sikkerhed')";
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


	#Definer lidt typisk og hent medie information
	$id = mysqli_real_escape_string($conn,$_GET['id']);
	$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

	$sql = "SELECT * FROM media WHERE id='".$id."'";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$result = mysqli_fetch_array($query);

	#Hvis det er et portfolio projekt man ændrer på
	if($result['portfolio'] == 1){
		$sql = "UPDATE media SET preview=0 WHERE portfolio=1 AND mid='".$result['mid']."'";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$sql = "UPDATE media SET preview=1 WHERE portfolio=1 AND id='".$id."'";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$sql ="INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Preview ændret i projekt#".$result['mid']."','".$time."','portfolio','".$uid."')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
	};

	#Hvis det er forsiden man ændrer på
	if($result['front'] == 1){
		$sql = "UPDATE media SET preview=0 WHERE front=1";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$sql = "UPDATE media SET preview=1 WHERE front=1 AND id='".$id."'";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$sql ="INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Billede karrusel: Primær opdateret','".$time."','forsiden','".$uid."')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
	};

	header("location:".$_SESSION['last']);